define(['jquery', 'core/ajax', 'core/str'], function($, ajax, str) {
    return {
        init: function(params) {
            const instanceid = params.instanceid;
            const select = $('#quranplayer-select-' + instanceid);
            const audio = $('#quranplayer-' + instanceid)[0];
            const source = $('#quranplayer-source-' + instanceid)[0];
            const quranContent = $('#quran-content-' + instanceid);
            const audioError = $('#audio-error-' + instanceid);

            select.on('change', function() {
                const selectedSurah = this.value;
                if (!selectedSurah) return;

                // Set loading message
                str.get_string('loading', 'block_quranplayer').then(function(loadingMsg) {
                    quranContent.text(loadingMsg);
                });

                // Load audio
                const audioUrl = 'https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/' + 
                    String(selectedSurah).padStart(3, '0') + '.mp3';
                source.src = audioUrl;
                audio.load();

                // Load Quran text
                ajax.call([{
                    methodname: 'block_quranplayer_get_text',
                    args: { 
                        surah: selectedSurah,
                        sesskey: params.sesskey
                    }
                }])[0]
                .then(function(response) {
                    if (response.success) {
                        quranContent.text(response.text);
                        audioError.hide();
                    } else {
                        throw new Error(response.text);
                    }
                })
                .catch(function(error) {
                    str.get_string('errorloading', 'block_quranplayer')
                        .then(function(msg) {
                            quranContent.text(msg);
                            audioError.text(msg).show();
                        });
                });
            });

            // Trigger initial load
            if (select.val()) {
                select.trigger('change');
            }
        }
    };
});