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
 * External functions and service definitions for the Quran Player block
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_quranplayer_get_surah_text' => [
        'classname' => 'block_quranplayer\external',
        'methodname' => 'get_surah_text',
        'description' => 'Get the text of a surah',
        'type' => 'read',
        'ajax' => true,
    ],
];

$services = [
    'Quran Player' => [
        'functions' => [
            'block_quranplayer_get_surah_text',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
]; 