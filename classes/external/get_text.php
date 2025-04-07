<?php
namespace block_quranplayer\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

class get_text extends external_api {
    public static function execute_parameters() {
        return new external_function_parameters([
            'surah' => new external_value(PARAM_INT, 'Surah number', VALUE_REQUIRED)
        ]);
    }

    public static function execute($surah) {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), ['surah' => $surah]);

        if ($params['surah'] < 1 || $params['surah'] > 114) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
        }

        $quranfile = __DIR__ . '/../../quran.txt';
        if (!file_exists($quranfile)) {
            return [
                'success' => false,
                'text' => get_string('noqurantext', 'block_quranplayer')
            ];
        }

        $qurantext = file_get_contents($quranfile);
        $lines = explode("\n", $qurantext);
        $selectedtext = '';

        foreach ($lines as $line) {
            list($linesurah, $lineverse, $text) = explode('|', $line, 3);
            if ($linesurah == $params['surah']) {
                $selectedtext .= "$lineverse. $text\n";
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