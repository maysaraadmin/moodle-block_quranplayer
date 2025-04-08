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

        // Get Quran file - ensure path is correct
        $quranfile = $CFG->dirroot . '/blocks/quranplayer/quran.txt';
        if (!file_exists($quranfile)) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
        }

        $qurantext = file($quranfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($qurantext === false) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
        }

        $selectedtext = '';
        foreach ($qurantext as $line) {
            $parts = explode('|', trim($line), 3);
            if (count($parts) === 3 && (int)$parts[0] === (int)$params['surah']) {
                $selectedtext .= '<div class="ayah"><span class="ayah-number">' . 
                               htmlspecialchars($parts[1], ENT_QUOTES, 'UTF-8') . '.</span> ' . 
                               htmlspecialchars($parts[2], ENT_QUOTES, 'UTF-8') . '</div>';
            }
        }

        if (empty($selectedtext)) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
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