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
 * External function to get Quran text and audio
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quranplayer\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use \external_api;
use \external_function_parameters;
use \external_value;
use \external_single_structure;
use \external_multiple_structure;

/**
 * External function to get Quran text and audio
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_quran_text extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            array(
                'surah' => new external_value(PARAM_INT, 'Surah number', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get Quran text and audio
     *
     * @param int $surah Surah number
     * @return array
     * @throws \moodle_exception
     */
    public static function execute($surah) {
        global $CFG, $DB;

        // Validate parameters
        $params = self::validate_parameters(self::execute_parameters(), array('surah' => $surah));

        // Check capability
        $context = \context_system::instance();
        require_capability('block/quranplayer:view', $context);

        // Get surah info
        $surah_info = self::get_surah_info($params['surah']);
        if (!$surah_info) {
            throw new \moodle_exception('invalidsurah', 'block_quranplayer', '', $params['surah']);
        }

        // Get surah text
        $arabic_text = self::get_surah_text($params['surah']);
        if (!$arabic_text) {
            throw new \moodle_exception('errorgettingtext', 'block_quranplayer');
        }

        // Get audio URL
        $audio_url = self::get_surah_audio_url($params['surah']);
        if (!$audio_url) {
            // If we can't get the audio URL, we'll still return the text
            $audio_url = '';
        }

        return array(
            'surah' => $params['surah'],
            'surah_name' => $surah_info['name'],
            'surah_arabic_name' => $surah_info['arabic_name'],
            'arabic_text' => $arabic_text,
            'audio_url' => $audio_url,
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure(
            array(
                'surah' => new external_value(PARAM_INT, 'Surah number'),
                'surah_name' => new external_value(PARAM_TEXT, 'Surah name in English'),
                'surah_arabic_name' => new external_value(PARAM_TEXT, 'Surah name in Arabic'),
                'arabic_text' => new external_value(PARAM_RAW, 'Arabic text of the surah'),
                'audio_url' => new external_value(PARAM_URL, 'URL to the audio file'),
            )
        );
    }

    /**
     * Get surah information
     *
     * @param int $surah Surah number
     * @return array|false
     */
    private static function get_surah_info($surah) {
        global $DB;

        $surah_info = $DB->get_record('block_quranplayer_surahs', array('number' => $surah));
        if (!$surah_info) {
            return false;
        }

        return array(
            'name' => $surah_info->name,
            'arabic_name' => $surah_info->arabic_name,
        );
    }

    /**
     * Get surah text
     *
     * @param int $surah Surah number
     * @return string|false
     */
    private static function get_surah_text($surah) {
        global $CFG;

        $api_url = "https://api.quran.com/api/v4/quran/verses/uthmani?chapter_number=" . $surah;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            debugging("Error fetching Quran text: HTTP code $http_code");
            return false;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['verses'])) {
            debugging("Error parsing Quran text response");
            return false;
        }

        $text = '';
        foreach ($data['verses'] as $verse) {
            $text .= '<div class="verse" data-verse="' . $verse['verse_number'] . '">';
            $text .= '<span class="verse-number">' . $verse['verse_number'] . '</span>';
            $text .= '<span class="verse-text">' . $verse['text_uthmani'] . '</span>';
            $text .= '</div>';
        }

        return $text;
    }

    /**
     * Get surah audio URL
     *
     * @param int $surah Surah number
     * @return string|false
     */
    private static function get_surah_audio_url($surah) {
        global $CFG;

        // First, try to get the reciter information
        $reciter_id = 7; // Default to Mishary Rashid Alafasy (ID 7)
        
        // Format the surah number with leading zeros (e.g., 001, 002, etc.)
        $formatted_surah = str_pad($surah, 3, '0', STR_PAD_LEFT);
        
        // Construct the API URL for the specific reciter and surah
        $api_url = "https://api.quran.com/api/v4/chapter_recitations/" . $reciter_id . "/" . $formatted_surah;
        
        debugging("Attempting to fetch audio from: " . $api_url);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            debugging("Error fetching audio: HTTP code $http_code");
            return false;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['audio_file'])) {
            debugging("Error parsing audio response");
            return false;
        }

        // Return the audio file URL
        return $data['audio_file']['audio_url'];
    }
} 