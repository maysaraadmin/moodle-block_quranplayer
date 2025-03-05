<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Fetches Quran text for a given chapter.
 *
 * @package    block_quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();

defined('MOODLE_INTERNAL') || die();

$file = optional_param('file', '', PARAM_INT);

if (empty($file) || $file < 1 || $file > 114) {
    echo get_string('noqurantext', 'block_quranplayer');
    exit;
}

$quranfile = __DIR__ . '/quran.txt';

if (!file_exists($quranfile)) {
    echo get_string('noqurantext', 'block_quranplayer');
    exit;
}

$qurantext = file_get_contents($quranfile);
$lines = explode("\n", $qurantext);

$selectedtext = '';
foreach ($lines as $line) {
    list($linesurah, $lineverse, $text) = explode('|', $line, 3);
    if ($linesurah == $file) {
        $selectedtext .= "$lineverse. $text\n";
    }
}

if (empty($selectedtext)) {
    echo get_string('noqurantext', 'block_quranplayer');
} else {
    echo $selectedtext;
}