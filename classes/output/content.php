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
 * Quran Player block content class
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quranplayer\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;

/**
 * Quran Player block content class
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content implements renderable, templatable {

    /** @var int The selected surah */
    protected $selected_surah;

    /** @var string The surah text */
    protected $surah_text;

    /** @var string The audio URL */
    protected $audio_url;

    /** @var array The list of surahs */
    protected $surahs;

    /**
     * Constructor
     *
     * @param int $selected_surah The selected surah
     * @param string $surah_text The surah text
     * @param string $audio_url The audio URL
     * @param array $surahs The list of surahs
     */
    public function __construct($selected_surah, $surah_text, $audio_url, $surahs) {
        $this->selected_surah = $selected_surah;
        $this->surah_text = $surah_text;
        $this->audio_url = $audio_url;
        $this->surahs = $surahs;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new \stdClass();
        $data->selected_surah = $this->selected_surah;
        $data->surah_text = $this->surah_text;
        $data->audio_url = $this->audio_url;
        $data->surahs = $this->surahs;
        return $data;
    }
} 