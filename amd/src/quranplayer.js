define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    return {
        init: function(params) {
            const instanceid = params.instanceid;
            const select = $('#quranplayer-select-' + instanceid);
            const audio = $('#quranplayer-' + instanceid)[0];
            const source = $('#quranplayer-source-' + instanceid)[0];
            const quranContent = $('#quran-content-' + instanceid);
            const audioError = $('#audio-error-' + instanceid);

            // Clear content initially
            quranContent.empty();

            // Audio error handling
            const handleAudioError = function() {
                str.get_string('audioerror', 'block_quranplayer').then(function(msg) {
                    audioError.text(msg).show();
                }).catch(notification.exception);
            };
            audio.addEventListener('error', handleAudioError);

            select.on('change', async function() {
                const selectedSurah = $(this).val();
                if (!selectedSurah) {
                    quranContent.empty();
                    audioError.hide();
                    return;
                }

                try {
                    // Show loading state
                    const loadingMsg = await str.get_string('loading', 'block_quranplayer');
                    quranContent.html(`<div class="text-center"><i class="fa fa-spinner fa-spin"></i> ${loadingMsg}</div>`);
                    audioError.hide();

                    // Load audio
                    source.src = `https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/${String(selectedSurah).padStart(3, '0')}.mp3`;
                    audio.load();

                    // Load Quran text via AJAX - FIXED response handling
                    const responses = await ajax.call([{
                        methodname: 'block_quranplayer_get_text',
                        args: { 
                            surah: parseInt(selectedSurah),
                            sesskey: params.sesskey
                        }
                    }]);

                    const response = responses[0]; // Get first response

                    if (response && response.success) {
                        // FIX: Ensure text is properly inserted
                        quranContent.html(response.text);
                        try {
                            await audio.play();
                        } catch (e) {
                            console.log('Auto-play prevented:', e);
                        }
                    } else {
                        throw new Error(response?.text || 'Invalid response from server');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    try {
                        const [errorMsg, noTextMsg] = await str.get_strings([
                            {key: 'errorloading', component: 'block_quranplayer'},
                            {key: 'noqurantext', component: 'block_quranplayer'}
                        ]);
                        quranContent.html(`<div class="alert alert-danger">${error.message || errorMsg}</div>`);
                        audioError.text(noTextMsg).show();
                    } catch (e) {
                        notification.exception(e);
                        quranContent.html('<div class="alert alert-danger">Error loading content</div>');
                        audioError.text('Failed to load text').show();
                    }
                }
            });

            // Cleanup
            return function() {
                audio.removeEventListener('error', handleAudioError);
                select.off('change');
            };
        }
    };
});