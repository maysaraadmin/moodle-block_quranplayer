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
 * Quran Player block.
 *
 * @package    block_quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_quranplayer extends block_base {

    public function init() {
        $this->title = get_string('quranplayer', 'block_quranplayer');
    }

    public function get_content() {
        global $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        if (!has_capability('block/quranplayer:view', $this->context)) {
            return null;
        }

        $this->content = new stdClass();
        $this->content->text = $this->render_audio_player();
        $this->content->footer = '';

        return $this->content;
    }

    private function render_audio_player() {
        global $CFG, $USER;

        $quranchapters = [
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

        $options = '';
        for ($surahnumber = 1; $surahnumber <= 114; $surahnumber++) {
            $surahname = $quranchapters[$surahnumber - 1];
            $options .= "<option value='$surahnumber'>$surahnumber. $surahname</option>";
        }

        $html = <<<HTML
<div>
    <label for="quranplayer-select">{$this->title}</label>
    <select id="quranplayer-select">
        $options
    </select>
    <div id="quran-text">
        <h3>{$this->title}</h3>
        <pre id="quran-content">{$this->get_loading_message()}</pre>
    </div>
    <audio id="quranplayer" controls>
        <source id="quranplayer-source" src="" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <div id="audio-error" style="color: red; display: none;">Failed to load audio.</div>
</div>
<script>
    const select = document.getElementById('quranplayer-select');
    const audio = document.getElementById('quranplayer');
    const source = document.getElementById('quranplayer-source');
    const quranContent = document.getElementById('quran-content');
    const audioError = document.getElementById('audio-error');

    select.addEventListener('change', function() {
        const selectedSurah = this.value;
        const audioUrl = 'https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/' + String(selectedSurah).padStart(3, '0') + '.mp3';

        // Check if the audio file exists before setting the source.
        fetch(audioUrl, { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    source.src = audioUrl;
                    audio.load();
                    audioError.style.display = 'none';
                } else {
                    audioError.style.display = 'block';
                    audioError.textContent = 'Audio file not found for this chapter.';
                }
            })
            .catch(() => {
                audioError.style.display = 'block';
                audioError.textContent = 'Failed to check audio file.';
            });

        quranContent.textContent = '{$this->get_loading_message()}';

        fetch('{$CFG->wwwroot}/blocks/quranplayer/get_quran_text.php?file=' + selectedSurah + '&sesskey=' + '{$USER->sesskey}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(text => {
                quranContent.textContent = text;
            })
            .catch(error => {
                quranContent.textContent = '{$this->get_error_message()}';
                console.error('Error fetching Quran text:', error);
            });
    });

    select.dispatchEvent(new Event('change'));
</script>
HTML;

        return $html;
    }

    private function get_loading_message() {
        return get_string('loading', 'block_quranplayer');
    }

    private function get_error_message() {
        return get_string('errorloading', 'block_quranplayer');
    }
}