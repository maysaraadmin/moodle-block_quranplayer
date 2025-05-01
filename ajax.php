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

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/quranplayer/block_quranplayer.php');

// Check if user is logged in
require_login();

// Get action parameter
$action = required_param('action', PARAM_TEXT);

// Initialize response array
$response = array();

switch ($action) {
    case 'get_quran':
        $surah = required_param('surah', PARAM_INT);
        $ayah = required_param('ayah', PARAM_INT);
        
        // Validate surah and ayah numbers
        if ($surah < 1 || $surah > 114) {
            $response['error'] = 'Invalid surah number';
            break;
        }
        
        // Get surah info
        $surah_info = get_surah_info($surah);
        if (!$surah_info) {
            $response['error'] = 'Could not get surah information';
            break;
        }
        
        // Validate ayah number
        if ($ayah < 1 || $ayah > $surah_info['ayah_count']) {
            $response['error'] = 'Invalid ayah number for this surah';
            break;
        }
        
        // Get ayah content
        $ayah_content = get_ayah_content($surah, $ayah);
        if (!$ayah_content) {
            $response['error'] = 'Could not get ayah content';
            break;
        }
        
        $response['text'] = $ayah_content['text'];
        $response['audio_url'] = $ayah_content['audio_url'];
        break;
        
    default:
        $response['error'] = 'Invalid action';
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);

/**
 * Get information about a specific surah
 * 
 * @param int $surah_number The surah number
 * @return array|false Surah information or false on failure
 */
function get_surah_info($surah_number) {
    // This is a placeholder - you would need to implement the actual logic
    // to fetch surah information from your data source
    $surah_info = array(
        'number' => $surah_number,
        'name' => 'Surah ' . $surah_number,
        'ayah_count' => 100 // This should be the actual count for each surah
    );
    return $surah_info;
}

/**
 * Get content for a specific ayah
 * 
 * @param int $surah_number The surah number
 * @param int $ayah_number The ayah number
 * @return array|false Ayah content or false on failure
 */
function get_ayah_content($surah_number, $ayah_number) {
    // This is a placeholder - you would need to implement the actual logic
    // to fetch ayah content from your data source
    $ayah_content = array(
        'text' => "This is the text of Surah $surah_number, Ayah $ayah_number",
        'audio_url' => "path/to/audio/file.mp3"
    );
    return $ayah_content;
} 