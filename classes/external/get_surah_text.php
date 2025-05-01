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
 * External function to get surah text
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quranplayer\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

/**
 * External function to get surah text
 */
class get_surah_text extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'surah' => new external_value(PARAM_INT, 'Surah number'),
        ]);
    }

    /**
     * Get surah text
     *
     * @param int $surah Surah number
     * @return array
     */
    public static function execute($surah) {
        global $CFG;

        $params = self::validate_parameters(self::execute_parameters(), [
            'surah' => $surah,
        ]);

        $surah = $params['surah'];

        // Get the surah text from the API
        $apiurl = "https://api.quran.com/api/v4/quran/verses/uthmani?chapter_number={$surah}";
        $response = file_get_contents($apiurl);
        $data = json_decode($response, true);

        if (!$data || !isset($data['verses'])) {
            return [
                'success' => false,
                'message' => 'Failed to fetch surah text',
            ];
        }

        $verses = [];
        foreach ($data['verses'] as $verse) {
            $verses[] = [
                'number' => $verse['verse_number'],
                'text' => $verse['text_uthmani'],
            ];
        }

        return [
            'success' => true,
            'verses' => $verses,
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the request was successful'),
            'verses' => new external_value(PARAM_RAW, 'The verses of the surah'),
        ]);
    }
} 