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
 * External API for the quranplayer block.
 *
 * @package    block_quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quranplayer\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

/**
 * External API for the quranplayer block.
 *
 * @package    block_quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quran_api extends external_api {
    
    /**
     * Get Quran verse text.
     *
     * @param int $surah Surah number
     * @param int $ayah Ayah number
     * @return array Verse data
     */
    public static function get_verse_text($surah, $ayah) {
        $params = self::validate_parameters(self::get_verse_text_parameters(), array(
            'surah' => $surah,
            'ayah' => $ayah
        ));
        
        $url = "https://api.quran.com/api/v4/verses/by_key/{$params['surah']}:{$params['ayah']}?translations=131";
        
        $curl = new \curl();
        $response = $curl->get($url);
        
        if ($curl->get_errno()) {
            throw new \moodle_exception('quranapierror', 'block_quranplayer', '', $curl->error);
        }
        
        $data = json_decode($response);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception('jsondecodeerror', 'block_quranplayer', '', json_last_error_msg());
        }
        
        if (!isset($data->verse) || !isset($data->verse->text_uthmani)) {
            throw new \moodle_exception('invalidapiresponse', 'block_quranplayer');
        }
        
        $result = array(
            'arabic_text' => $data->verse->text_uthmani,
            'translation' => isset($data->verse->translations[0]->text) ? $data->verse->translations[0]->text : '',
            'surah_name' => isset($data->verse->surah_name) ? $data->verse->surah_name : '',
            'surah_number' => $params['surah'],
            'ayah_number' => $params['ayah']
        );
        
        return $result;
    }
    
    /**
     * Get Quran verse audio URL.
     *
     * @param int $surah Surah number
     * @param int $ayah Ayah number
     * @return string Audio URL
     */
    public static function get_verse_audio($surah, $ayah) {
        $params = self::validate_parameters(self::get_verse_audio_parameters(), array(
            'surah' => $surah,
            'ayah' => $ayah
        ));
        
        $url = "https://api.quran.com/api/v4/verses/by_key/{$params['surah']}:{$params['ayah']}?audio=1";
        
        $curl = new \curl();
        $response = $curl->get($url);
        
        if ($curl->get_errno()) {
            throw new \moodle_exception('quranapierror', 'block_quranplayer', '', $curl->error);
        }
        
        $data = json_decode($response);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception('jsondecodeerror', 'block_quranplayer', '', json_last_error_msg());
        }
        
        if (!isset($data->verse) || !isset($data->verse->audio) || !isset($data->verse->audio->primary)) {
            throw new \moodle_exception('invalidapiresponse', 'block_quranplayer');
        }
        
        return $data->verse->audio->primary;
    }
    
    /**
     * Parameter validation for get_verse_text.
     *
     * @return external_function_parameters
     */
    public static function get_verse_text_parameters() {
        return new external_function_parameters(array(
            'surah' => new external_value(PARAM_INT, 'Surah number'),
            'ayah' => new external_value(PARAM_INT, 'Ayah number')
        ));
    }
    
    /**
     * Return value for get_verse_text.
     *
     * @return external_single_structure
     */
    public static function get_verse_text_returns() {
        return new external_single_structure(array(
            'arabic_text' => new external_value(PARAM_RAW, 'Arabic text'),
            'translation' => new external_value(PARAM_RAW, 'Translation'),
            'surah_name' => new external_value(PARAM_TEXT, 'Surah name'),
            'surah_number' => new external_value(PARAM_INT, 'Surah number'),
            'ayah_number' => new external_value(PARAM_INT, 'Ayah number')
        ));
    }
    
    /**
     * Parameter validation for get_verse_audio.
     *
     * @return external_function_parameters
     */
    public static function get_verse_audio_parameters() {
        return new external_function_parameters(array(
            'surah' => new external_value(PARAM_INT, 'Surah number'),
            'ayah' => new external_value(PARAM_INT, 'Ayah number')
        ));
    }
    
    /**
     * Return value for get_verse_audio.
     *
     * @return external_value
     */
    public static function get_verse_audio_returns() {
        return new external_value(PARAM_URL, 'Audio URL');
    }
} 