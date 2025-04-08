<?php
defined('MOODLE_INTERNAL') || die();

class block_quranplayer extends block_base {

    public function init() {
        $this->title = get_string('quranplayer', 'block_quranplayer');
    }

    public function get_content() {
        global $PAGE, $OUTPUT, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (!has_capability('block/quranplayer:view', $this->context)) {
            $this->content->text = $OUTPUT->notification(
                get_string('nopermissions', 'block_quranplayer'),
                'error'
            );
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
            'instanceid' => $this->context->instanceid
        ];

        // Add AMD module
        $PAGE->requires->js_call_amd('block_quranplayer/quranplayer', 'init', [
            'instanceid' => $this->context->instanceid,
            'sesskey' => sesskey()
        ]);

        // Load CSS
        $PAGE->requires->css('/blocks/quranplayer/styles.css');

        $this->content->text = $OUTPUT->render_from_template('block_quranplayer/quran_player', $data);
        return $this->content;
    }

    private function get_surah_list() {
        $surahs = [
            "الفاتحة", "البقرة", "آل عمران", "النساء", "المائدة", "الأنعام", "الأعراف", 
            // ... rest of surahs ...
            "الإخلاص", "الفلق", "الناس"
        ];

        $result = [];
        foreach ($surahs as $index => $name) {
            $result[] = ['number' => $index + 1, 'name' => $name];
        }
        return $result;
    }

    public function has_config() {
        return true;
    }
}