define(['jquery', 'core/ajax', 'core/str'], function($, ajax, str) {
    return {
        init: function(instanceid) {
            const select = $(`#quranplayer-select-${instanceid}`);
            const audio = $(`#quranplayer-${instanceid}`)[0];
            const source = $(`#quranplayer-source-${instanceid}`)[0];
            const quranContent = $(`#quran-content-${instanceid}`);
            const audioError = $(`#audio-error-${instanceid}`);

            select.on('change', async function() {
                const selectedSurah = this.value;
                if (!selectedSurah) return;

                const audioUrl = `https://download.quranicaudio.com/quran/mishaari_raashid_al_3afaasee/${selectedSurah.padStart(3, '0')}.mp3`;

                try {
                    // Check audio file availability
                    const response = await fetch(audioUrl, { method: 'HEAD' });
                    if (response.ok) {
                        source.src = audioUrl;
                        audio.load();
                        audioError.hide();
                    } else {
                        throw new Error('Audio file not found');
                    }
                } catch (error) {
                    const errorMsg = await str.get_string(
                        error.message === 'Audio file not found' ? 'audiofilenotfound' : 'audiofilecheckfailed', 
                        'block_quranplayer'
                    );
                    audioError.text(errorMsg).show();
                    return;
                }

                // Set loading message
                const loadingMsg = await str.get_string('loading', 'block_quranplayer');
                quranContent.text(loadingMsg);

                try {
                    // Fetch Quran text
                    const response = await ajax.call([{
                        methodname: 'block_quranplayer_get_text',
                        args: { surah: selectedSurah }
                    }]);

                    if (response[0].success) {
                        quranContent.text(response[0].text);
                    } else {
                        throw new Error('Failed to load text');
                    }
                } catch (error) {
                    const errorMsg = await str.get_string('errorloading', 'block_quranplayer');
                    quranContent.text(errorMsg);
                    console.error('Error fetching Quran text:', error);
                }
            });

            // Trigger initial load if a surah is selected
            if (select.val()) {
                select.trigger('change');
            }
        }
    };
});