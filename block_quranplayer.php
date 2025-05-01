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
 * Quran Player block
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Autoloader should handle finding block_base and namespaced classes.
// If \block_quranplayer\output\content is not found, you might need:
// require_once($CFG->dirroot . '/blocks/quranplayer/classes/output/content.php');

/**
 * Quran Player block class
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quranplayer extends block_base {

    /**
     * Initializes the block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_quranplayer');
    }

    /**
     * Returns the block content
     *
     * @return \stdClass|null
     */
    public function get_content() {
        global $CFG; // Needed for ajaxurl

        if ($this->content !== null) {
            return $this->content;
        }

        // Prevent errors when block is first added or context is unavailable
        if (empty($this->instance) || !$this->page) {
             return null; // Or return an empty stdClass if preferred
        }

        $this->content = new \stdClass();
        $this->content->footer = ''; // Footer is often set here

        // --- Initial Data Setup ---
        // Define the default Surah and Ayah to load when the block appears.
        $initial_surah = 1; // Default to Surah Al-Fatiha
        $initial_ayah = 1;  // Default to the first Ayah

        // Get the list of Surahs for the dropdown.
        $surahs = $this->get_surah_list();
        if (empty($surahs)) {
             // Display an error if the Surah list cannot be loaded.
             // Make sure 'error:nosurahs' is defined in your block's language file.
             $this->content->text = html_writer::tag('div', get_string('error:nosurahs', 'block_quranplayer', 'Error: Could not load Surah list.'), ['class' => 'qp-error']);
             return $this->content;
        }

        // --- Rendering ---
        // Get the specific renderer for this block.
        $renderer = $this->page->get_renderer('block_quranplayer');
        // Prepare data for the template. This includes the list of Surahs
        // and the initial Surah/Ayah numbers for the JavaScript to use.
        // The actual verse text and audio URL will be fetched via AJAX.
        // We assume your \block_quranplayer\output\content class can handle this data.
        // If it's just a simple data container, its constructor might take an array/object.
        $verse_data = $this->get_verse_text($initial_surah, $initial_ayah);

        $templateData = [
            'surahs' => $surahs,
            'initial_surah' => $initial_surah,
            'initial_ayah' => $initial_ayah,
            'selected_surah' => $initial_surah,
            'verse_arabic' => $verse_data['arabic'],
            'verse_translations' => $verse_data['translations'],
            'verse_transliterations' => $verse_data['transliterations'],
            'verse_word_by_word' => $verse_data['word_by_word'],
            'audio_url' => '', // Add audio URL if available
            // Add any other static data needed by the template structure itself
        ];

        // Create the renderable object. Adjust this line based on how your
        // \block_quranplayer\output\content class constructor is defined.
        // If it expects specific arguments:
        // $contentRenderable = new \block_quranplayer\output\content($initial_surah, '', '', $surahs, $initial_ayah);
        // If it expects an array/object (more flexible):
        try {
            // Check if the class exists before trying to instantiate it
            if (class_exists('\block_quranplayer\output\content')) {
                $contentRenderable = new \block_quranplayer\output\content(
    $templateData['selected_surah'] ?? null, 
    $templateData['surah_text'] ?? '', 
    $templateData['audio_url'] ?? '', 
    $templateData['surahs'] ?? []
);
            } else {
                // Fallback or error if the class doesn't exist
                // For now, let's assume the renderer can handle a stdClass if the specific one is missing
                $contentRenderable = (object)$templateData;
                // You might want to log this situation
                debugging("Class \block_quranplayer\output\content not found. Using stdClass for template data.", DEBUG_DEVELOPER);
            }
        } catch (\Throwable $e) {
            // Catch potential errors during instantiation
            $this->content->text = html_writer::tag('div', 'Error creating content renderable: ' . $e->getMessage(), ['class' => 'qp-error']);
            // *** FIXED HERE ***
            debugging('Error creating content renderable: ' . $e->getMessage() . $e->getTraceAsString(), DEBUG_DEVELOPER);
            return $this->content;
        }


        // Render the block's HTML structure using the renderer and the data.
        // This assumes the renderer's 'render_content' method uses a template
        // (like 'block_quranplayer/content') which sets up the player UI elements.
        try {
             // Check if the renderer method exists
             if (method_exists($renderer, 'render_content')) {
                 $this->content->text = $renderer->render_content($contentRenderable);
             } else {
                 // Fallback: Try rendering a template directly if render_content is missing
                 // This assumes your renderer extends plugin_renderer_base
                 $this->content->text = $renderer->render_from_template('block_quranplayer/content', $contentRenderable);
                 debugging("Renderer method 'render_content' not found in " . get_class($renderer) . ". Using render_from_template as fallback.", DEBUG_DEVELOPER);
             }
        } catch (\Throwable $e) {
            // Catch potential errors during rendering
            $this->content->text = html_writer::tag('div', 'Error rendering block content: ' . $e->getMessage(), ['class' => 'qp-error']);
             // *** FIXED HERE ***
            debugging('Error rendering block content: ' . $e->getMessage() . $e->getTraceAsString(), DEBUG_DEVELOPER);
            return $this->content;
        }


        // --- JavaScript Initialization ---
        // Pass necessary data to the AMD JavaScript module.
        $ajaxurl = new \moodle_url('/blocks/quranplayer/ajax.php');
        $jsmodule = [
            'name' => 'block_quranplayer', // Should match 'define' in amd/src/quranplayer.js
            'function' => 'init',          // The function to call within the JS module
            'args' => [
                'blockid' => $this->instance->id,
                'initial_surah' => $initial_surah,
                'initial_ayah' => $initial_ayah,
                'ajaxurl' => $ajaxurl->out(false), // URL for JavaScript to make AJAX calls
                // Pass language strings needed by JavaScript (define these in lang/en/block_quranplayer.php)
                'loadingstring' => get_string('loading', 'block_quranplayer', 'Loading...'),
                'errorstring' => get_string('error:ajax', 'block_quranplayer', 'Error loading data.'),
            ]
        ];
        $this->page->requires->js_call_amd($jsmodule['name'], $jsmodule['function'], $jsmodule['args']);
        // Ensure you have 'amd/src/quranplayer.js' defined and it expects these arguments in its 'init' function.

        return $this->content;
    }

    /**
     * Fetch verse text for a specific Surah and Ayah
     *
     * @param int $surah Surah number
     * @param int $ayah Ayah number
     * @return string Verse text
     */
    /**
     * Fetches comprehensive Quran text for a specific verse
     *
     * @param int $surah Surah number
     * @param int $ayah Ayah number
     * @return array Verse details with multiple translations and metadata
     */
    private function get_verse_text($surah, $ayah) {
        // Comprehensive Quran text database
        // Note: This is a placeholder. In a real implementation, you'd load this from
        // a database, JSON file, or external API
        $quranText = [
            1 => [ // Al-Fatiha
                1 => [
                    'arabic' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
                    'translations' => [
                        'english' => 'In the name of Allah, the Most Gracious, the Most Merciful',
                        'urdu' => 'اللہ کے نام سے جو بڑا مہربان نہایت رحم والا ہے',
                        'turkish' => 'Rahman ve Rahim olan Allah\'ın adıyla'
                    ],
                    'transliterations' => [
                        'latin' => 'Bismillah ir-Rahman ir-Rahim',
                        'phonetic' => 'Bis-mil-laa-hir Rah-maa-nir Ra-heem'
                    ],
                    'word_by_word' => [
                        'بِسْمِ' => 'In the name of',
                        'اللَّهِ' => 'Allah',
                        'الرَّحْمَٰنِ' => 'The Most Gracious',
                        'الرَّحِيمِ' => 'The Most Merciful'
                    ]
                ],
                2 => [
                    'arabic' => 'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ',
                    'translations' => [
                        'english' => 'Praise be to Allah, the Lord of the worlds',
                        'urdu' => 'تمام تعریفیں اللہ کے لیے ہیں جو عالمین کا رب ہے',
                        'turkish' => 'Âlemlerin Rabbi Allah\'a hamdolsun'
                    ],
                    'transliterations' => [
                        'latin' => 'Al-hamdu lillahi rabbil \'alamin\'',
                        'phonetic' => 'Al-ham-du lil-laa-hi rab-bil \'aa-la-meen'
                    ],
                    'word_by_word' => [
                        'الْحَمْدُ' => 'Praise',
                        'لِلَّهِ' => 'to Allah',
                        'رَبِّ' => 'Lord of',
                        'الْعَالَمِينَ' => 'the worlds'
                    ]
                ]
            ]
            // Add more Surahs and verses as needed
        ];

        // Return verse details or default not found message
        return $quranText[$surah][$ayah] ?? [
            'arabic' => 'آية غير موجودة',
            'translations' => [
                'english' => 'Verse not found',
                'urdu' => 'آیت نہیں ملی',
                'turkish' => 'Ayet bulunamadı'
            ],
            'transliterations' => [
                'latin' => 'N/A',
                'phonetic' => 'N/A'
            ],
            'word_by_word' => []
        ];
    }

    /**
     * Returns the applicable formats for the block.
     *
     * @return array
     */
    public function applicable_formats() {
        return [
            'my' => true,
            'site' => true,
            'course' => true,
        ];
    }

    /**
     * Returns whether the block has instance-specific configuration.
     * Keep as false unless you add an edit_form.php for settings.
     *
     * @return bool True if the block has config
     */
    public function has_config() {
        return false;
    }

    /**
     * Returns whether multiple instances of the block are allowed on a page.
     *
     * @return bool True if multiple instances are allowed
     */
    public function instance_allow_multiple() {
        return false; // Usually false for this type of block
    }

    /**
     * Hide the header?
     *
     * @return boolean
     */
    public function hide_header() {
        return false; // Set to true if you don't want the block title bar
    }

    /**
     * Fetches the list of Surah names and numbers.
     * TODO: Replace placeholder logic with actual data fetching (API, DB, file).
     *
     * @return array Array of Surah data (e.g., [['number' => 1, 'name' => 'Al-Fatiha'], ...])
     */
    private function get_surah_list() {
        // Comprehensive Surah list with additional metadata
        $surahData = [
            1 => ['name' => 'Al-Fatiha', 'translation' => 'The Opening', 'type' => 'Meccan', 'total_verses' => 7],
            2 => ['name' => 'Al-Baqarah', 'translation' => 'The Cow', 'type' => 'Medinan', 'total_verses' => 286],
            // Add more Surahs as needed
            114 => ['name' => 'An-Nas', 'translation' => 'Mankind', 'type' => 'Meccan', 'total_verses' => 6]
        ];

        $surahs = [];
        for ($i = 1; $i <= 114; $i++) {
            // Try to get localized Surah name
            $surahname = get_string('surah_name_' . $i, 'block_quranplayer');

            // Use predefined data if available, otherwise use fallback
            $surahInfo = $surahData[$i] ?? [
                'name' => "Surah {$i}",
                'translation' => "Surah {$i} Translation",
                'type' => 'Unknown',
                'total_verses' => 0
            ];

            // Provide a fallback if the language string is missing
            if (strpos($surahname, '[[surah_name_') === 0) {
                $surahname = $surahInfo['name'];
            }
            // Ensure the structure matches what your template/JS expects.
            $surahs[] = [
                'number' => $i, 
                'name' => $surahname,
                'translation' => $surahInfo['translation'],
                'type' => $surahInfo['type'],
                'total_verses' => $surahInfo['total_verses']
            ];
        }

        // Sort Surahs by number
        usort($surahs, function($a, $b) {
            return $a['number'] - $b['number'];
        });

        return $surahs;
    }

    // --- Configuration Methods (if has_config() is true) ---
    // public function instance_config_save($data, $nolongerused = false) { ... }
    // public function instance_config_print() { ... }
    // Or implement edit_form.php

}