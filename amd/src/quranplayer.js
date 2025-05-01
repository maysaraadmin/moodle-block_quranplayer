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
 * JavaScript for the quranplayer block.
 *
 * @module     block_quranplayer/quranplayer
 * @copyright  2025 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    return {
        /**
         * Initialise the quranplayer block.
         *
         * @param {Object} config The configuration object.
         */
        init: function(config) {
            // Add event listeners for audio controls
            $('.block_quranplayer .quran-audio audio').on('play', function() {
                // Pause all other audio elements
                $('.block_quranplayer .quran-audio audio').not(this).each(function() {
                    this.pause();
                });
            });
            
            // Add event listeners for configuration form
            if (config.hasconfig) {
                $('#id_s_surah, #id_s_ayah').on('change', function() {
                    // Validate surah and ayah inputs
                    var surah = $('#id_s_surah').val();
                    var ayah = $('#id_s_ayah').val();
                    
                    if (surah < 1 || surah > 114) {
                        alert(M.util.get_string('surah_help', 'block_quranplayer'));
                        $('#id_s_surah').focus();
                        return false;
                    }
                    
                    if (ayah < 1) {
                        alert(M.util.get_string('ayah_help', 'block_quranplayer'));
                        $('#id_s_ayah').focus();
                        return false;
                    }
                });
            }
        }
    };
}); 