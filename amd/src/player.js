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
 * Quran Player module
 *
 * @module     block_quranplayer/player
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    /**
     * Quran Player class
     *
     * @class
     * @param {Object} config Configuration object
     * @param {HTMLElement} config.element The container element
     * @param {string} config.wwwroot Moodle's wwwroot URL
     */
    var Player = function(config) {
        this.element = config.element;
        this.wwwroot = config.wwwroot;
        this.currentSurah = null;
        this.currentAyah = 1;
        this.isPlaying = false;
        this.audio = new Audio();
        this.audioFiles = [];
        this.currentAudioIndex = 0;
        this.isLoading = false;

        this.init();
    };

    /**
     * Initialize the player
     */
    Player.prototype.init = function() {
        this.surahSelect = this.element.querySelector('#surah-select');
        this.playButton = this.element.querySelector('#play-button');
        this.pauseButton = this.element.querySelector('#pause-button');
        this.stopButton = this.element.querySelector('#stop-button');
        this.arabicText = this.element.querySelector('#arabic-text');
        this.quranHeader = this.element.querySelector('.quran-header h3');
        this.arabicName = this.element.querySelector('.arabic-name');

        // Initialize audio element
        this.audio = new Audio();
        this.audio.addEventListener('ended', this.handleAudioEnded.bind(this));
        this.audio.addEventListener('error', this.handleAudioError.bind(this));
        this.audio.addEventListener('canplaythrough', this.handleAudioCanPlay.bind(this));
        this.audio.addEventListener('loadstart', this.handleAudioLoadStart.bind(this));
        this.audio.addEventListener('loadeddata', this.handleAudioLoadedData.bind(this));

        this.bindEvents();
        
        // Set initial surah if one is selected
        if (this.surahSelect.value) {
            this.currentSurah = this.surahSelect.value;
            this.loadSurah();
        }
    };

    /**
     * Bind event handlers
     */
    Player.prototype.bindEvents = function() {
        // Use jQuery for more reliable event handling
        $(this.surahSelect).on('change', this.handleSurahChange.bind(this));
        $(this.playButton).on('click', this.handlePlay.bind(this));
        $(this.pauseButton).on('click', this.handlePause.bind(this));
        $(this.stopButton).on('click', this.handleStop.bind(this));
    };

    /**
     * Handle surah selection change
     */
    Player.prototype.handleSurahChange = function(event) {
        console.log('Surah change event triggered');
        console.log('Selected value:', this.surahSelect.value);
        
        // Prevent handling if we're already loading
        if (this.isLoading) {
            console.log('Already loading a surah, ignoring change event');
            return;
        }
        
        this.currentSurah = this.surahSelect.value;
        console.log('Current surah set to:', this.currentSurah);
        
        if (this.currentSurah) {
            this.currentAyah = 1;
            this.loadSurah();
        }
    };

    /**
     * Handle play button click
     */
    Player.prototype.handlePlay = function() {
        console.log('Play button clicked');
        
        if (!this.currentSurah) {
            console.log('No surah selected');
            Notification.alert('Please select a surah first');
            return;
        }

        if (this.audioFiles.length === 0) {
            console.error('No audio files available');
            Notification.alert('No audio available for this surah');
            return;
        }

        console.log('Attempting to play audio from:', this.audioFiles[this.currentAudioIndex]);
        this.isPlaying = true;
        this.playButton.style.display = 'none';
        this.pauseButton.style.display = 'inline-block';
        
        // Play the audio with error handling
        this.audio.play().then(function() {
            console.log('Audio started playing successfully');
        }).catch(function(error) {
            console.error('Error playing audio:', error);
            Notification.alert('Error playing audio: ' + error.message);
            this.handleStop();
        }.bind(this));
    };

    /**
     * Handle pause button click
     */
    Player.prototype.handlePause = function() {
        this.isPlaying = false;
        this.playButton.style.display = 'inline-block';
        this.pauseButton.style.display = 'none';
        this.audio.pause();
    };

    /**
     * Handle stop button click
     */
    Player.prototype.handleStop = function() {
        this.isPlaying = false;
        this.playButton.style.display = 'inline-block';
        this.pauseButton.style.display = 'none';
        this.audio.pause();
        this.audio.currentTime = 0;
        this.currentAudioIndex = 0;
    };

    /**
     * Handle audio ended event
     */
    Player.prototype.handleAudioEnded = function() {
        // Move to the next audio file if available
        if (this.currentAudioIndex < this.audioFiles.length - 1) {
            this.currentAudioIndex++;
            this.audio.src = this.audioFiles[this.currentAudioIndex];
            this.audio.play().catch(function(error) {
                console.error('Error playing next audio:', error);
                this.handleStop();
            }.bind(this));
        } else {
            // Reset to the beginning if we've reached the end
            this.currentAudioIndex = 0;
            this.handleStop();
        }
    };

    /**
     * Handle audio can play through event
     */
    Player.prototype.handleAudioCanPlay = function() {
        console.log('Audio can play through');
    };
    
    /**
     * Handle audio load start event
     */
    Player.prototype.handleAudioLoadStart = function() {
        console.log('Audio load started');
    };
    
    /**
     * Handle audio loaded data event
     */
    Player.prototype.handleAudioLoadedData = function() {
        console.log('Audio data loaded');
    };

    /**
     * Handle audio error event
     */
    Player.prototype.handleAudioError = function(e) {
        console.error('Audio error:', e);
        console.error('Audio error code:', this.audio.error.code);
        console.error('Audio error message:', this.audio.error.message);
        
        // Try to load a fallback audio URL
        if (this.currentSurah) {
            this.loadFallbackAudio(this.currentSurah);
        } else {
            Notification.alert('Error playing audio');
            this.handleStop();
        }
    };

    /**
     * Load fallback audio URL
     */
    Player.prototype.loadFallbackAudio = function(surah) {
        console.log('Loading fallback audio for surah:', surah);
        
        // Try an alternative audio source
        var fallbackUrl = "https://cdn.islamic.network/quran/audio/128/ar.alafasy/" + surah + ".mp3";
        console.log('Trying fallback URL:', fallbackUrl);
        
        this.audioFiles = [fallbackUrl];
        this.currentAudioIndex = 0;
        this.audio.src = fallbackUrl;
        
        // Try to play the audio
        if (this.isPlaying) {
            this.audio.play().then(function() {
                console.log('Fallback audio started playing successfully');
            }).catch(function(error) {
                console.error('Error playing fallback audio:', error);
                Notification.alert('Error playing audio: ' + error.message);
                this.handleStop();
            }.bind(this));
        }
    };

    /**
     * Load surah content and audio
     */
    Player.prototype.loadSurah = function() {
        console.log('Loading surah:', this.currentSurah);
        
        // Set loading flag
        this.isLoading = true;
        
        var promises = Ajax.call([{
            methodname: 'block_quranplayer_get_quran_text',
            args: {
                surah: this.currentSurah
            }
        }]);

        promises[0].then(function(response) {
            console.log('Received response:', response);
            
            if (response.error) {
                console.error('Error in response:', response.error);
                Notification.alert(response.error);
                this.handleStop();
                this.isLoading = false;
                return;
            }

            // Update the header with surah name
            this.quranHeader.textContent = 'Surah ' + this.currentSurah + ': ' + response.surah_name;
            this.arabicName.textContent = response.surah_arabic_name;
            
            // Update the content
            this.arabicText.innerHTML = response.arabic_text;
            
            // Update the dropdown selection to match the current surah
            this.updateDropdownSelection(this.currentSurah);
            
            // Update audio source
            if (response.audio_url) {
                console.log('Setting audio URL to:', response.audio_url);
                
                // For now, we're just using a single audio URL
                // In the future, we could fetch all audio files for the surah
                this.audioFiles = [response.audio_url];
                this.currentAudioIndex = 0;
                this.audio.src = response.audio_url;
                
                // If we were playing before loading a new surah, continue playing
                if (this.isPlaying) {
                    console.log('Resuming playback after loading new surah');
                    this.audio.play().then(function() {
                        console.log('Audio resumed successfully');
                    }).catch(function(error) {
                        console.error('Error resuming audio:', error);
                        Notification.alert('Error playing audio: ' + error.message);
                        this.handleStop();
                    }.bind(this));
                }
            } else {
                console.error('No audio URL provided in response');
                Notification.alert('No audio available for this surah');
                this.handleStop();
            }
            
            // Reset loading flag
            this.isLoading = false;
        }.bind(this)).catch(function(error) {
            console.error('Error loading surah:', error);
            Notification.alert('Error loading surah');
            this.handleStop();
            this.isLoading = false;
        }.bind(this));
    };
    
    /**
     * Update the dropdown selection to match the current surah
     * 
     * @param {number} surahNumber The surah number to select
     */
    Player.prototype.updateDropdownSelection = function(surahNumber) {
        console.log('Updating dropdown selection to surah:', surahNumber);
        
        // Use jQuery for more reliable selection
        $(this.surahSelect).val(surahNumber);
        
        // Also update the selectedIndex directly for compatibility
        var options = this.surahSelect.options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value == surahNumber) {
                this.surahSelect.selectedIndex = i;
                console.log('Set selectedIndex to:', i);
                break;
            }
        }
        
        // Trigger a change event to ensure any listeners are notified
        $(this.surahSelect).trigger('change');
    };

    return Player;
}); 