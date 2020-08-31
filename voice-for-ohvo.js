jQuery(document).ready(function (){ // use jQuery code inside this to avoid "$ is not defined" error
    
    let p = 1;
    let gender = 'male'
    let accent = jQuery('.voice-talent-container .filter').attr('voice-accent');
    let platform = jQuery('.voice-talent-container .filter').attr('voice-platform');

    jQuery(document).on('click', '.voice-talent-container .filter a.filter-btn', function(){

        p = 1;
        gender = jQuery(this).attr('data-value');

        jQuery('.filter .filter-btn').removeClass('active');
        jQuery(this).addClass('active');

        jQuery('.talent-grid').empty();
        
        loadVoiceTalents();
    });

    function loadVoiceTalents() {

        let q = '&pageNumber=' + p + '&gender=' + gender + '&accent=' + accent + '&platform=' + platform + '&action=more_voice_talent_ajax';
        jQuery('.voice-talent-container .loading').css('display', 'block');
        jQuery.ajax({
            type: "GET",
            dataType: 'html',
            url: ajax_posts.ajaxurl,
            data: q,
            success: function(data) {
                $data = jQuery(data);
                jQuery('.talent-grid').append($data);

                GreenAudioPlayer.init({
                    selector: '.voice-source-' + p, // inits Green Audio Player on each audio container that has class "player"
                    stopOthersOnPlay: true
                });
                jQuery('.voice-talent-container .loading').css('display', 'none');
                p++;
            },
            error: function() {
                console.error('error while loading posts');
            }
        })
    }

    $filterButtons = jQuery('.voice-talent-container .filter a.filter-btn');

    jQuery(document).on('click', '.voice-talent-container .load-more .load-more-button', function() {
        loadVoiceTalents();
    })
    jQuery($filterButtons[0]).click();
});