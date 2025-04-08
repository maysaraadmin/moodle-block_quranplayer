define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    return {
        init: function(params) {
            const instanceid = params.instanceid;
            const select = $('#quranplayer-select-' + instanceid);
            const audio = $('#quranplayer-' + instanceid)[0];
            const source = $('#quranplayer-source-' + instanceid)[0];
            const quranContent = $('#quran-content-' + instanceid);
            const audioError = $('#audio-error-' + instanceid);

            // Improved error handling for audio
            audio.addEventListener('error', function() {
                str.get_string('audioerror', 'block_quranplayer').then(function(msg) {
                    audioError.text(msg).show();
                    notification.addNotification({
                        message: msg,
                        type: 'error'
                    });
                });
            });

            select.on('change', function() {
                const selectedSurah = this.value;
                if (!selectedSurah) {
                    quranContent.html('');
                    audioError.hide();
                    return;
                }

                // Set loading message
                str.get_string('loading', 'block_quranplayer').then(function(loadingMsg) {
                    quranContent.html('<div class="text-center">' + loadingMsg + '</div>');
                }).catch(function() {
                    quranContent.html('<div class="text-center">Loading...</div>');
                });

                // Load audio
                const audioUrl = 'https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/' + 
                    String(selectedSurah).padStart(3, '0') + '.mp3';
                source.src = audioUrl;
                audio.load();

                // Load Quran text with timeout
                const textPromise = ajax.call([{
                    methodname: 'block_quranplayer_get_text',
                    args: { 
                        surah: selectedSurah,
                        sesskey: params.sesskey
                    }
                }])[0];

                // Set timeout for the AJAX call (10 seconds)
                const timeoutPromise = new Promise((_, reject) => 
                    setTimeout(() => reject(new Error('Request timeout')), 10000)
                );

                Promise.race([textPromise, timeoutPromise])
                .then(function(response) {
                    if (response && response.success) {
                        quranContent.html(
                            '<div class="quran-text-content" dir="rtl">' + 
                            response.text.replace(/\n/g, '<br>') + 
                            '</div>'
                        );
                        audioError.hide();
                        // Try to play audio
                        audio.play().catch(e => {
                            console.log('Auto-play prevented:', e);
                        });
                    } else {
                        throw new Error(response ? response.text : 'Invalid response');
                    }
                })
                .catch(function(error) {
                    console.error('Error loading Quran text:', error);
                    str.get_strings([
                        {key: 'errorloading', component: 'block_quranplayer'},
                        {key: 'noqurantext', component: 'block_quranplayer'}
                    ]).then(function(strings) {
                        quranContent.html('<div class="text-danger">' + strings[0] + '</div>');
                        audioError.text(error.message || strings[1]).show();
                    }).catch(function() {
                        quranContent.html('<div class="text-danger">Error loading text</div>');
                        audioError.text('Error loading text').show();
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