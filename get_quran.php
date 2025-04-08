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
 * Quran text display for Quran Player block.
 *
 * @package    block_quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_login();

// Get parameters
$surah = optional_param('surah', 1, PARAM_INT);
$ayah = optional_param('ayah', 0, PARAM_INT); // 0 means all ayahs

// Security check
$context = context_system::instance();
require_capability('block/quranplayer:view', $context);

// Set up page
$PAGE->set_context($context);
$PAGE->set_url('/blocks/quranplayer/get_quran.php');
$PAGE->set_title(get_string('qurantext', 'block_quranplayer'));
$PAGE->set_heading(get_string('qurantext', 'block_quranplayer'));

// Get Quran text
$quranfile = $CFG->dirroot . '/blocks/quranplayer/quran.txt';
if (!file_exists($quranfile)) {
    throw new moodle_exception('noqurantext', 'block_quranplayer');
}

$qurantext = file_get_contents($quranfile);
$lines = explode("\n", $qurantext);

// Filter by surah (and optionally ayah)
$filteredlines = [];
foreach ($lines as $line) {
    if (empty(trim($line))) {
        continue;
    }
    
    $parts = explode('|', $line, 3);
    if (count($parts) === 3 && $parts[0] == $surah) {
        if ($ayah === 0 || $parts[1] == $ayah) {
            $filteredlines[] = $parts;
        }
    }
}

if (empty($filteredlines)) {
    throw new moodle_exception('noqurantext', 'block_quranplayer');
}

// Prepare output
echo $OUTPUT->header();

echo html_writer::start_div('quran-text-container');
echo html_writer::tag('h2', get_string('surah', 'block_quranplayer') . ' ' . $surah);

foreach ($filteredlines as $line) {
    echo html_writer::start_div('quran-ayah');
    echo html_writer::tag('span', $line[1] . '. ', ['class' => 'ayah-number']);
    echo html_writer::tag('span', $line[2], ['class' => 'ayah-text']);
    echo html_writer::end_div();
}

echo html_writer::end_div();

echo $OUTPUT->footer();