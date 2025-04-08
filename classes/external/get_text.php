<?php
namespace block_quranplayer\external;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use moodle_exception;

class get_text extends external_api {
    public static function execute_parameters() {
        return new external_function_parameters([
            'surah' => new external_value(PARAM_INT, 'Surah number', VALUE_REQUIRED),
            'sesskey' => new external_value(PARAM_TEXT, 'Session key', VALUE_REQUIRED)
        ]);
    }

    public static function execute($surah, $sesskey) {
        global $CFG;

        // Validate parameters
        $params = self::validate_parameters(self::execute_parameters(), [
            'surah' => $surah,
            'sesskey' => $sesskey
        ]);

        // Validate session
        if (!confirm_sesskey($params['sesskey'])) {
            throw new moodle_exception('invalidsesskey');
        }

        // Validate surah number
        if ($params['surah'] < 1 || $params['surah'] > 114) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
        }

        // Get Quran file
        $quranfile = $CFG->dirroot . '/blocks/quranplayer/quran.txt';
        if (!file_exists($quranfile)) {
            throw new moodle_exception('noqurantext', 'block_quranplayer');
        }

        $qurantext = file_get_contents($quranfile);
        if ($qurantext === false) {
            throw new moodle_exception('noqurantext', 'block_quranplayer');
        }

        // Parse Quran text
        $lines = explode("\n", $qurantext);
        $selectedtext = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode('|', $line, 3);
            if (count($parts) === 3 && $parts[0] == $params['surah']) {
                $selectedtext .= '<div class="ayah"><span class="ayah-number">' . 
                               $parts[1] . '.</span> ' . $parts[2] . '</div>';
            }
        }

        if (empty($selectedtext)) {
            throw new moodle_exception('noqurantext', 'block_quranplayer');
        }

        return [
            'success' => true,
            'text' => $selectedtext
        ];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'text' => new external_value(PARAM_RAW, 'Quran text content')
        ]);
    }
}