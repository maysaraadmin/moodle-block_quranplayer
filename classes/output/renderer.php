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
 * Quran Player block renderer
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quranplayer\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * Quran Player block renderer class
 *
 * @package    block_quranplayer
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Renders the block content
     *
     * @param content $content The content to render
     * @return string The rendered HTML
     */
    public function render_content(content $content) {
        $data = $content->export_for_template($this);
        return $this->render_from_template('block_quranplayer/content', $data);
    }
    
    /**
     * Render the config form template.
     *
     * @param \stdClass $data Template data
     * @return string HTML output
     */
    public function render_config_form($data) {
        return $this->render_from_template('block_quranplayer/config_form', $data);
    }
} 