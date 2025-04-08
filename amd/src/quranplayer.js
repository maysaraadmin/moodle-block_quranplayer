define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    return {
        init: function(params) {
            const instanceid = params.instanceid;
            const select = $('#quranplayer-select-' + instanceid);
            const audio = $('#quranplayer-' + instanceid)[0];
            const source = $('#quranplayer-source-' + instanceid)[0];
            const quranContent = $('#quran-content-' + instanceid);
            const audioError = $('#audio-error-' + instanceid);

            // Set initial state
            quranContent.html('<div class="text-center text-muted">Select a surah to begin</div>');

            // Audio error handling
            audio.addEventListener('error', function() {
                str.get_string('audioerror', 'block_quranplayer').then(function(msg) {
                    audioError.html(msg).show();
                });
            });

            select.on('change', function() {
                const selectedSurah = $(this).val();
                if (!selectedSurah) {
                    quranContent.html('<div class="text-center text-muted">Select a surah to begin</div>');
                    audioError.hide();
                    return;
                }

                // Show loading message
                quranContent.html('<div class="text-center text-muted">Loading Quran text...</div>');

                // Load audio
                const audioUrl = 'https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/' + 
                    String(selectedSurah).padStart(3, '0') + '.mp3';
                source.src = audioUrl;
                audio.load();

                // Load Quran text via AJAX
                ajax.call([{
                    methodname: 'block_quranplayer_get_text',
                    args: { 
                        surah: selectedSurah,
                        sesskey: params.sesskey
                    }
                }])[0]
                .done(function(response) {
                    console.log('AJAX Response:', response); // Debug log
                    if (response && response.success) {
                        quranContent.html(response.text);
                        audioError.hide();
                        audio.play().catch(e => console.log('Auto-play prevented:', e));
                    } else {
                        throw new Error(response?.text || 'Invalid response');
                    }
                })
                .fail(function(error) {
                    console.error('AJAX Error:', error); // Debug log
                    str.get_string('errorloading', 'block_quranplayer')
                        .then(function(msg) {
                            quranContent.html('<div class="alert alert-danger">' + msg + '</div>');
                            audioError.html(error.message || msg).show();
                        })
                        .catch(function() {
                            quranContent.html('<div class="alert alert-danger">Error loading text</div>');
                            audioError.html('Error loading text').show();
                        });
                });
            });

            // Trigger initial load if a surah is selected
            if (select.val()) {
                select.trigger('change');
            }
        }
    };
});