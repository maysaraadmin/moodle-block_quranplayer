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

        // Prepare template data
        $data = [
            'title' => $this->title,
            'surahs' => $this->get_surah_list(),
            'loading' => get_string('loading', 'block_quranplayer'),
            'instanceid' => $this->context->instanceid
        ];

        // Add AMD module
        $PAGE->requires->js_call_amd('block_quranplayer/quranplayer', 'init', [
            'instanceid' => $this->context->instanceid,
            'sesskey' => sesskey()
        ]);

        $this->content->text = $OUTPUT->render_from_template('block_quranplayer/quran_player', $data);
        return $this->content;
    }

    private function get_surah_list() {
        $surahs = [
            "الفاتحة", "البقرة", "آل عمران", "النساء", "المائدة", "الأنعام", "الأعراف", "الأنفال", "التوبة", "يونس",
            "هود", "يوسف", "الرعد", "ابراهيم", "الحجر", "النحل", "الإسراء", "الكهف", "مريم", "طه",
            "الأنبياء", "الحج", "المؤمنون", "النور", "الفرقان", "الشعراء", "النمل", "القصص", "العنكبوت", "الروم",
            "لقمان", "السجدة", "الأحزاب", "سبإ", "فاطر", "يس", "الصافات", "ص", "الزمر", "غافر",
            "فصلت", "الشورى", "الزخرف", "الدخان", "الجاثية", "الأحقاف", "محمد", "الفتح", "الحجرات", "ق",
            "الذاريات", "الطور", "النجم", "القمر", "الرحمن", "الواقعة", "الحديد", "المجادلة", "الحشر", "الممتحنة",
            "الصف", "الجمعة", "المنافقون", "التغابن", "الطلاق", "التحريم", "الملك", "القلم", "الحاقة", "المعارج",
            "نوح", "الجن", "المزمل", "المدثر", "القيامة", "الانسان", "المرسلات", "النبإ", "النازعات", "عبس",
            "التكوير", "الإنفطار", "المطففين", "الإنشقاق", "البروج", "الطارق", "الأعلى", "الغاشية", "الفجر", "البلد",
            "الشمس", "الليل", "الضحى", "الشرح", "التين", "العلق", "القدر", "البينة", "الزلزلة", "العاديات",
            "القارعة", "التكاثر", "العصر", "الهمزة", "الفيل", "قريش", "الماعون", "الكوثر", "الكافرون", "النصر",
            "المسد", "الإخلاص", "الفلق", "الناس",
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