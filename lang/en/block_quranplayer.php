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
 * Language strings for the Quran Player block.
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Quran Player';
$string['quranplayer:addinstance'] = 'Add a new Quran Player block';
$string['quranplayer:myaddinstance'] = 'Add a new Quran Player block to Dashboard';
$string['quranplayer:view'] = 'View the Quran Player block';

// Block strings
$string['selectsurah'] = 'Select Surah';
$string['play'] = 'Play';
$string['pause'] = 'Pause';
$string['stop'] = 'Stop';
$string['configureblock'] = 'Please configure the Quran block to display a verse.';
$string['surah_ayah'] = 'Surah {$a->surah}, Ayah {$a->ayah}';
$string['surah_title'] = 'Surah {$a->surah}: {$a->name}';
$string['errortext'] = 'Error loading Quran text';
$string['browser_not_support'] = 'Your browser does not support the audio element.';
$string['source_link'] = 'View on Quran.com';
$string['blocksettings'] = 'Block settings';
$string['blocktitle'] = 'Quran Verse';
$string['surah'] = 'Surah Number';
$string['ayah'] = 'Ayah Number';
$string['showtranslation'] = 'Show Translation';
$string['quranapierror'] = 'Error connecting to Quran API: {$a}';
$string['jsondecodeerror'] = 'Error decoding JSON response: {$a}';
$string['invalidapiresponse'] = 'Invalid API response structure';
$string['surah_help'] = 'Enter a surah number between 1 and 114';
$string['ayah_help'] = 'Enter an ayah number';
$string['invalidsurah'] = 'Invalid surah number: {$a}';
$string['errorgettingtext'] = 'Error getting Quran text';