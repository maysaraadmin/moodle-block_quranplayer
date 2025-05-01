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
 * Configuration form for the quranplayer block.
 *
 * @package    block_quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quranplayer\form;

defined('MOODLE_INTERNAL') || die();

use moodleform;

/**
 * Configuration form for the quranplayer block.
 *
 * @package    block_quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_form extends moodleform {
    
    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        
        // Add a header
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block_quranplayer'));
        
        // Add surah field
        $mform->addElement('text', 'surah', get_string('surah', 'block_quranplayer'));
        $mform->setType('surah', PARAM_INT);
        $mform->addRule('surah', get_string('surah_help', 'block_quranplayer'), 'required', null, 'client');
        $mform->addRule('surah', get_string('surah_help', 'block_quranplayer'), 'numeric', null, 'client');
        $mform->addRule('surah', get_string('surah_help', 'block_quranplayer'), 'rangelength', array(1, 114), 'client');
        
        // Add ayah field
        $mform->addElement('text', 'ayah', get_string('ayah', 'block_quranplayer'));
        $mform->setType('ayah', PARAM_INT);
        $mform->addRule('ayah', get_string('ayah_help', 'block_quranplayer'), 'required', null, 'client');
        $mform->addRule('ayah', get_string('ayah_help', 'block_quranplayer'), 'numeric', null, 'client');
        $mform->addRule('ayah', get_string('ayah_help', 'block_quranplayer'), 'nonzero', null, 'client');
        
        // Add show translation field
        $mform->addElement('selectyesno', 'showtranslation', get_string('showtranslation', 'block_quranplayer'));
        $mform->setDefault('showtranslation', 1);
        
        return $mform;
    }
} 