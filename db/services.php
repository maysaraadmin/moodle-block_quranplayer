<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_quranplayer_get_text' => [
        'classname' => 'block_quranplayer\external\get_text',
        'methodname' => 'execute',
        'description' => 'Get Quran text for a specific surah',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'block/quranplayer:view',
    ],
];

$services = [
    'Quran Player Service' => [
        'functions' => ['block_quranplayer_get_text'],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];