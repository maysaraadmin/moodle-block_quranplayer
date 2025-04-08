<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/quranplayer/classes/external/get_text.php');

require_login();

// Test with Surah 1
$surah = 1;
$sesskey = sesskey();

try {
    $result = block_quranplayer\external\get_text::execute($surah, $sesskey);
    echo "<h2>Test Results</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    // Verify quran.txt exists
    $quranfile = $CFG->dirroot . '/blocks/quranplayer/quran.txt';
    echo "<h3>File Check</h3>";
    echo "Quran file path: " . $quranfile . "<br>";
    echo "File exists: " . (file_exists($quranfile) ? "Yes" : "No") . "<br>";
    echo "Readable: " . (is_readable($quranfile) ? "Yes" : "No") . "<br>";
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<div style='color:red;'>" . $e->getMessage() . "</div>";
    echo "<pre>";
    debug_print_backtrace();
    echo "</pre>";
}