<?php
/**
 * @package	Wordpress Tumblr AJAX Widget
 * @author	Sagie Maoz
 * @version	1.0
 */
/*
Plugin Name: Wordpress Tumblr AJAX Widget
Plugin URI: http://wordpress.org/extend/plugins/wp-tumblr/
Description: Simple plugin that launches an AJAX call to a selected Tumblr blog and displays the last couple of posts.
Author: Sagie Maoz
Version: 1.0
Author URI: http://n0nick.net/
*/

define( 'WP_TUMBLR_DIR', dirname( plugin_basename( __FILE__ ) ) );
define( 'WP_TUMBLR_PATH', ABSPATH . 'wp-content/plugins/' . WP_TUMBLR_DIR );
define( 'WP_TUMBLR_WEB_PATH', get_option( 'siteurl' ) . '/wp-content/plugins/' . WP_TUMBLR_DIR);


// widget code
function web_tumblr($args) {

    extract($args);
    $options = get_option('web_tumblr');
    $arg = GetWidgetArg($options);
    
    if (!is_single() && !is_home())
    	return;

    $title			= empty($options['title'])  		? __('Tumblr Feed', 'wp-tumblr') : $options['title'];
    $tumblr_domain		= empty($options['domain']) 		? null	: $options['domain'];
    $posts_count		= empty($options['count'])  		? 5	: $options['count'];
    $audio_color		= empty($options['audio_color'])	? false	: $options['audio_color'];
    
    if (empty($options['domain']))
    	return;
    else
    	$tumblr_domain = $options['domain'];
    
    $html_title = "<a href=\"http://$tumblr_domain/\" rel=\"external\">$title</a>";

    ?>
    <?php echo $before_widget; ?>
        <?php echo $before_title . $html_title . $after_title; ?>
        <div id="tumblr_container" class="loading"></div>
        <a href="http://<?php echo $tumblr_domain ?>/" class="tumblr_more"><?php _e( 'More...', 'wp-tumblr' ) ?></a>
	<?php if ($audio_color): ?>
	<script type="text/javascript">/*<![CDATA[*/wp_tumblr_audio_color = '<?php echo $audio_color ?>';/*]]>*/</script>
	<?php endif; ?>
	<script type="text/javascript" src="<?php echo WP_TUMBLR_WEB_PATH ?>/wp-tumblr.js"></script>
	<script type="text/javascript" src="http://<?php echo $tumblr_domain ?>/api/read/json?num=<?php echo $posts_count ?>&amp;callback=tumblr_ready"></script>
        
    <?php echo $after_widget; ?>
    <?php
    $post = $original_post;
}

// widget controller
function web_tumblr_control() {
    
	$options = $newoptions = get_option('web_tumblr');
	if ( $_POST['tumblr-submit'] ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['tumblr-title'] ) );
		$newoptions['domain'] = strip_tags( stripslashes( $_POST['tumblr-domain'] ) );
		$newoptions['count'] = strip_tags( stripslashes( $_POST['tumblr-count'] ) );
		$newoptions['audio_color'] = strip_tags( stripslashes( $_POST['tumblr-audio-color'] ) );
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option( 'web_tumblr', $options );
	}
	$title		 = attribute_escape( $options['title'] );
	$domain		 = attribute_escape( $options['domain'] );
	$count		 = attribute_escape( $options['count'] );
	$audio_color = attribute_escape( $options['audio_color'] );
?>
	<p><label for="tumblr-title"><?php _e( 'Title:', 'wp-tumblr' ) ?> <input class="widefat" id="tumblr-title" name="tumblr-title" type="text" value="<?php echo $title; ?>" /></label></p>
	<p><label for="tumblr-domain"><?php _e( 'Tumblelog Domain:', 'wp-tumblr' ) ?> <input class="widefat" id="tumblr-domain" name="tumblr-domain" type="text" value="<?php echo $domain; ?>" /></label></p>
	<p><label for="tumblr-count"><?php _e( 'Posts count:', 'wp-tumblr' ) ?> <input class="widefat" id="tumblr-count" name="tumblr-count" type="text" value="<?php echo $count; ?>" /></label></p>
	<p><label fpr="tumblr-audio-color"><?php _e( 'Audio Player color:', 'wp-tumblr' ) ?> <input class="widefat" id="tumblr-audio-color" name="tumblr-audio-color" value="<?php echo $audio_color; ?>" /></label></p>
	<input type="hidden" id="tumblr-submit" name="tumblr-submit" value="1" />
<?php
}

// widget initializer
function web_tumblr_init()
{
	// load language file
	$locale = get_locale();
	if ( !empty($locale) )
		$langfile = WP_TUMBLR_PATH . "/$locale.mo";
	if ( file_exists( $langfile ) )
		load_textdomain( 'wp-tumblr', $langfile);

	// register sidebar & controller
	register_sidebar_widget( 'wp-tumblr', 'web_tumblr', 'wid_tumblr' );
	register_widget_control( 'wp-tumblr', 'web_tumblr_control', 300, 470);
}

add_action( 'widgets_init', 'web_tumblr_init' );

?>
