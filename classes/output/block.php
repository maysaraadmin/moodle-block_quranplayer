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
 * Quran Player block class
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quranplayer;

defined('MOODLE_INTERNAL') || die();

/**
 * Quran Player block class
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quranplayer extends \block_base {

    /**
     * Initializes the block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_quranplayer');
    }

    /**
     * Returns the block content
     *
     * @return \stdClass The block content
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new \stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $renderer = $this->page->get_renderer('block_quranplayer');
        $content = new \block_quranplayer\output\content('', '', '', []);
        $this->content->text = $renderer->render($content);

        return $this->content;
    }

    /**
     * Returns the applicable formats
     *
     * @return array The applicable formats
     */
    public function applicable_formats() {
        return [
            'my' => true,
            'site' => true,
            'course' => true,
        ];
    }
} 