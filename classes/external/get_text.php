<?php
namespace block_quranplayer\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use moodle_exception;

class get_text extends external_api {
    public static function execute_parameters() {
        return new external_function_parameters([
            'surah' => new external_value(PARAM_INT, 'Surah number', VALUE_REQUIRED),
            'sesskey' => new external_value(PARAM_TEXT, 'Session key', VALUE_REQUIRED)
        ]);
    }

    public static function execute($surah, $sesskey) {
        global $CFG, $USER;

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

        // Get Quran file path
        $quranfile = $CFG->dirroot . '/blocks/quranplayer/quran.txt';
        if (!file_exists($quranfile)) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
        }

        // Read and parse Quran file
        $qurantext = file_get_contents($quranfile);
        if ($qurantext === false) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
        }

        $lines = explode("\n", $qurantext);
        $selectedtext = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode('|', $line, 3);
            if (count($parts) === 3 && $parts[0] == $params['surah']) {
                $selectedtext .= '<span class="ayah">' . $parts[1] . '. ' . $parts[2] . '</span><br>';
            }
        }

        return [
            'success' => !empty($selectedtext),
            'text' => $selectedtext ?: get_string('noqurantext', 'block_quranplayer')
        ];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'text' => new external_value(PARAM_RAW, 'Quran text content')
        ]);
    }
}