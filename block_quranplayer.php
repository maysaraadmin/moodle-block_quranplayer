<?php
defined('MOODLE_INTERNAL') || die();

class block_quranplayer extends block_base {

    public function init() {
        $this->title = get_string('quranplayer', 'block_quranplayer');
    }

    public function get_content() {
        global $PAGE, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (!has_capability('block/quranplayer:view', $this->context)) {
            return $this->content;
        }

        // Verify quran.txt exists
        $quranfile = $CFG->dirroot . '/blocks/quranplayer/quran.txt';
        if (!file_exists($quranfile)) {
            $this->content->text = $OUTPUT->notification(
                get_string('noqurantext', 'block_quranplayer'),
                'error'
            );
            return $this->content;
        }

        // Prepare template data
        $data = [
            'title' => $this->title,
            'surahs' => $this->get_surah_list(),
            'loading' => get_string('loading', 'block_quranplayer'),
            'instanceid' => $this->context->instanceid
        ];

        // Add AMD module with error handling
        try {
            $PAGE->requires->js_call_amd('block_quranplayer/quranplayer', 'init', [
                'instanceid' => $this->context->instanceid,
                'sesskey' => sesskey()
            ]);
        } catch (Exception $e) {
            $this->content->text = $OUTPUT->notification(
                get_string('errorloading', 'block_quranplayer'),
                'error'
            );
            return $this->content;
        }

        // Load CSS
        $PAGE->requires->css('/blocks/quranplayer/styles.css');

        $this->content->text = $OUTPUT->render_from_template('block_quranplayer/quran_player', $data);
        return $this->content;
    }

    private function get_surah_list() {
        $surahs = [
            "الفاتحة", "البقرة", "آل عمران", "النساء", "المائدة", "الأنعام", "الأعراف", "الأنفال", "التوبة", "يونس",
            // ... rest of surahs ...
        ];

        return array_map(function($index, $name) {
            return ['number' => $index + 1, 'name' => $name];
        }, array_keys($surahs), $surahs);
    }

    public function has_config() {
        return true;
    }
}