<?php
/*
Plugin Name: Voice Talent.
Description: Voice Talent Grid for OHVO Website.
Version: 1.0
Author: OHVOPlugins
Text Domain: voice-for-ohvo
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


add_action( 'wp_enqueue_scripts', 'ohvo_voice_talent_grid_enqueue_scripts' );

/**
 * Audio Gallery Player enqueue scripts
 */
if (!function_exists('ohvo_voice_talent_grid_enqueue_scripts')) {
	function ohvo_voice_talent_grid_enqueue_scripts( $hook ) {
		
    wp_enqueue_style( 'ohvo-voice-talent-grid-css', plugins_url( '/voice-for-ohvo.css', __FILE__ ), array(), '', 'all' );
    wp_enqueue_style( 'ohvo-green-audio-player-css', plugins_url( '/assets/dist/css/green-audio-player.css', __FILE__ ), array(), '', 'all' );
    
    wp_enqueue_script( 'green-audio-player', plugins_url( '/assets/dist/js/green-audio-player.js', __FILE__ ) );

    wp_register_script('ohvo-voice-talent-grid-js', plugins_url( '/voice-for-ohvo.js', __FILE__ ), array('green-audio-player', 'jquery'), '', true);

    wp_enqueue_script( 'ohvo-voice-talent-grid-js' );

	}
}


function ohvo_voice_talent_grid($attr, $content) { 

    $attr = shortcode_atts(array(
        'accent' => array(),
        'platform' => array(),

    ), $attr);

    $accent = 'all';
    if ($attr['accent']) {
      $accent = $attr['accent'];
    }

    $platform = 'all';
    if ($attr['platform']) {
      $platform = $attr['platform'];
    }
    ob_start(); 
    ?>
    <div class="voice-talent-container">
        <div class="filter" voice-accent="<?php echo $accent; ?>" voice-platform="<?php echo $platform; ?>">
            <a href="javascript:void(0)" class="filter-btn active" data-value="male">Male</a>
            <a href="javascript:void(0)" class="filter-btn" data-value="female">Female</a>
        </div>
        <div class="voice-talent-grid">
          <div id="ohvo-ajax-voice-talents" class="talent-grid"></div>
        </div>
        <div class="loading">
          <div class="spinner">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
          </div>
        </div>
        <div class="load-more">
          <a href="javascript:void(0)" class="load-more-button">Load More</a>
        </div>
    </div>
    <?php return $content . ob_get_clean();
}
add_shortcode( 'voice_talents', 'ohvo_voice_talent_grid' );


function more_voice_talent_ajax() {
    header("Content-Type: text/html");

    $page_number = $_REQUEST['pageNumber'];
    $gender = $_REQUEST['gender'];
    $accent = $_REQUEST['accent'];
    $platform = $_REQUEST['platform'];

    $voice_talents = file_get_contents('https://api2.livingformusicgroup.com/api/v1/media-files/?accent=' . $accent . '&platform=' . $platform . '&gender=' . $gender . '&per_page=8&page=' . $page_number);

    $voice_talents = json_decode($voice_talents);
    
    $out = '';
    foreach ($voice_talents->data as $talent) {
      $out .= ' <div class="talent-item">
                  <div class="talent-image">
                    <img src="'.$talent->voice->image.'" />
                  </div>
                  <div class="talent-info">
                    <h5 class="talent-name">'.$talent->voice->name.'</h5>
                    <p class="talent-description">'.$talent->language.'</p>
                  </div>
                  <div class="voice-source-' . $page_number . '">
                    <audio>
                      <source src="'.$talent->file_location->file.'" type="'.$talent->file_location->type.'" />
                    </audio>
                  </div>
                </div>';
    }

    die($out);
}

add_action( 'wp_ajax_nopriv_more_voice_talent_ajax', 'more_voice_talent_ajax' );
add_action( 'wp_ajax_more_voice_talent_ajax', 'more_voice_talent_ajax' );
?>