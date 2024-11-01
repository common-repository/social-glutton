<?php
/*
Plugin Name: Social Glutton Widget
Plugin URI: http://wordpress.org/extend/plugins/social-glutton/
Description: Display social widgets for Facebook, Twitter, Google +, StumbleUpon, and Pinterest
Author: Steven Jaeger
Version: 2.0
Author URI: http://stevenjaeger.com/
*/

add_action('wp_enqueue_scripts', 'add_social_glutton_css');

function add_social_glutton_css() {
	$social_glutton_myStyleUrl = plugins_url('style.css', __FILE__); // Respects SSL, Style.css is relative to the current file
	$social_glutton_myStyleFile = WP_PLUGIN_DIR . '/social-glutton/style.css';	
	$social_glutton_nailThumb = plugins_url('socialglutton.js', __FILE__);

	if ( file_exists($social_glutton_myStyleFile) ) {
		wp_register_style('socialgluttoncss', $social_glutton_myStyleUrl);
		wp_enqueue_style( 'socialgluttoncss');		
		wp_deregister_script( 'jquery' );
    	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
		wp_enqueue_script( 'jquery' );		
		wp_register_script( 'socialgluttonjs', $social_glutton_nailThumb);    	
		wp_enqueue_script( 'socialgluttonjs' );
	}
}

class SocialGluttonWidget extends WP_Widget
{
    /**
     * Widget settings.
     */
    protected $widget = array(
            // Default title for the widget in the sidebar.
            'pintitle' => 'Recent pins',
			'stumbletitle' => 'Recent Stumbles',

            // Default widget settings.
            'pinusername' => 'pinterest',
            'pinrows' => 3,
            'pincols' => 3,
            'pluginwidth' => 300,
            'buttonposition' => 'top',

            // The widget description used in the admin area.
            'description' => 'Widgets for most major social outlets',

            // RSS cache lifetime in seconds.
            'cache_lifetime' => 900,

            // Pinterest base url.
            'pinterest_url' => 'http://pinterest.com'
    );
    var $protocol;
	function SocialGluttonWidget()
	{
		$widget_ops = array('classname' => 'socialg_widget', 'description' => 'Widgets for Facebook, Twitter, Google+, Pinterest, and StumblUpon' );
		$this->WP_Widget('SocialGluttonWidget', 'Social Glutton', $widget_ops);
        $this->protocol = $this->is_secure() ? 'https://' : 'http://';
	}
	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'buttonposition' => '', 'pluginwidth' => '', 'facebookurl' => '', 'twittername' => '', 'googleid' => '', 'googleapi' => '', 'pintitle' => '', 'pinusername' => '', 'pinrows' => '', 'stumbletitle' => '', 'stumbleuser' => '' ) );
		$buttonposition = $instance['buttonposition'];
		$pluginwidth = $instance['pluginwidth'];
		$facebookurl = $instance['facebookurl'];
		$twittername = $instance['twittername'];
		$googleid = $instance['googleid'];
		$googleapi = $instance['googleapi'];
		$pintitle = $instance['pintitle'];
		$pinusername = $instance['pinusername'];
		$pinrows = $instance['pinrows'];
		$stumbletitle = $instance['stumbletitle'];
		$stumbleuser = $instance['stumbleuser'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('buttonposition'); ?>">Buttons position: </label>
			<select id="<?php echo $this->get_field_id('buttonposition'); ?>" name="<?php echo $this->get_field_name('buttonposition'); ?>">
				<option value="top">Top</option>
				<option value="left" <?php if($buttonposition == left){ ?> selected="selected" <?php } ?> >Left</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('pluginwidth'); ?>">Widget width: </label>
			<input id="<?php echo $this->get_field_id('pluginwidth'); ?>" name="<?php echo $this->get_field_name('pluginwidth'); ?>" type="text" value="<?php echo esc_attr($pluginwidth); ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('facebookurl'); ?>">Facepile URL (include http://): </label>
			<input class="widefat" id="<?php echo $this->get_field_id('facebookurl'); ?>" name="<?php echo $this->get_field_name('facebookurl'); ?>" type="text" value="<?php echo esc_attr($facebookurl); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('twittername'); ?>">Twitter Username: </label>
			<input class="widefat" id="<?php echo $this->get_field_id('twittername'); ?>" name="<?php echo $this->get_field_name('twittername'); ?>" type="text" value="<?php echo esc_attr($twittername); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('googleid'); ?>">Google+ Profile ID: </label>
			<input class="widefat" id="<?php echo $this->get_field_id('googleid'); ?>" name="<?php echo $this->get_field_name('googleid'); ?>" type="text" value="<?php echo esc_attr($googleid); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('googleapi'); ?>">Google+ API Key: </label>
			<input class="widefat" id="<?php echo $this->get_field_id('googleapi'); ?>" name="<?php echo $this->get_field_name('googleapi'); ?>" type="text" value="<?php echo esc_attr($googleapi); ?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('pintitle'); ?>"><?php _e('Pinterest Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('pintitle'); ?>" name="<?php echo $this->get_field_name('pintitle'); ?>" type="text" value="<?php echo esc_attr($pintitle); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('pinusername'); ?>"><?php _e('Pinterest Username:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('pinusername'); ?>" name="<?php echo $this->get_field_name('pinusername'); ?>" type="text" value="<?php echo esc_attr($pinusername); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('pinrows'); ?>"><?php _e('Nr. of pins tall:'); ?></label>
            <input id="<?php echo $this->get_field_id('pinrows'); ?>" name="<?php echo $this->get_field_name('pinrows'); ?>" type="text" value="<?php echo esc_attr($pinrows); ?>" size="3" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('stumbletitle'); ?>"><?php _e('StumbleUpon Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('stumbletitle'); ?>" name="<?php echo $this->get_field_name('stumbletitle'); ?>" type="text" value="<?php echo esc_attr($stumbletitle); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('stumbleuser'); ?>"><?php _e('StumbleUpon Username:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('stumbleuser'); ?>" name="<?php echo $this->get_field_name('stumbleuser'); ?>" type="text" value="<?php echo esc_attr($stumbleuser); ?>" />
        </p>
		<?php
	}
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
        $instance['buttonposition'] = strip_tags($new_instance['buttonposition']);
        $instance['pluginwidth'] = strip_tags($new_instance['pluginwidth']);
        $instance['facebookurl'] = strip_tags($new_instance['facebookurl']);
        $instance['twittername'] = strip_tags($new_instance['twittername']);
        $instance['googleid'] = strip_tags($new_instance['googleid']);
        $instance['googleapi'] = strip_tags($new_instance['googleapi']);
        $instance['pintitle'] = strip_tags($new_instance['pintitle']);
        $instance['pinusername'] = strip_tags($new_instance['pinusername']);
        $instance['pinrows'] = strip_tags($new_instance['pinrows']);
        $instance['stumbletitle'] = strip_tags($new_instance['stumbletitle']);
        $instance['stumbleuser'] = strip_tags($new_instance['stumbleuser']);
		// $instance = $new_instance;
		return $instance;
	}
	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$buttonposition = $instance['buttonposition'];
		$facebookurl = $instance['facebookurl'];
		$twittername = $instance['twittername'];
		$googleid = $instance['googleid'];
		$googleapi = $instance['googleapi'];
		$pinusernamecheck = $instance['pinusername'];
		$stumbleuser = $instance['stumbleuser'];
		$containerwidth = $instance['pluginwidth'];
		if($buttonposition == 'top'){
			$pluginwidth = $containerwidth - 30;
		}else{
			$pluginwidth = $containerwidth - 83;
		}
		?>
		<div id="socialg-shell" style="width:<?php echo $containerwidth ?>px">
			<ul class="socialg-pos-<?php echo $buttonposition; ?>">
				<?php if ($facebookurl != '' ){ ?><li><a href="#tab-1"><?php echo "<img src=" .plugins_url( 'social-glutton/images/icon_facebook.png' , dirname(__FILE__) ). " > "; ?></a></li><?php } ?>
				<?php if ($twittername != '' ){ ?><li><a href="#tab-2"><?php echo "<img src=" .plugins_url( 'social-glutton/images/icon_twitter.png' , dirname(__FILE__) ). " > "; ?></a></li><?php } ?>
				<?php if ($googleid != '' && $googleapi != ''){ ?><li><a href="#tab-3"><?php echo "<img src=" .plugins_url( 'social-glutton/images/icon_google.png' , dirname(__FILE__) ). " > "; ?></a></li><?php } ?>
				<?php if ($pinusernamecheck != '' ){ ?><li><a href="#tab-4"><?php echo "<img src=" .plugins_url( 'social-glutton/images/icon_pinterest.png' , dirname(__FILE__) ). " > "; ?></a></li><?php } ?>
				<?php if ($stumbleuser != '' ){ ?><li><a href="#tab-5"><?php echo "<img src=" .plugins_url( 'social-glutton/images/icon_stumbleupon.png' , dirname(__FILE__) ). " > "; ?></a></li><?php } ?>
			</ul>
			<?php if ($facebookurl != '' ){ ?><div id="tab-1" style="width:<?php echo $pluginwidth; ?>px">
				<!-- Facebook -->
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
					fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				</script>
				<div class="fb-like-box" data-href="<?php echo $facebookurl; ?>" data-width="<?php echo $pluginwidth; ?>" data-height="332" data-show-faces="true" data-stream="false" data-header="false" data-colorscheme="dark"></div>
				<!-- end Facebook -->
			</div> <?php } ?>
			<?php if ($twittername != '' ){ ?><div id="tab-2" style="width:<?php echo $pluginwidth; ?>px">
				<!-- Twitter -->
				<script src="http://widgets.twimg.com/j/2/widget.js"></script>
				<script>
					new TWTR.Widget({
						version: 2, type: 'profile', rpp: 4, interval: 30000, width: <?php echo $pluginwidth; ?>, height: 300,
							theme: {
								shell: {
									background: '#333333', color: '#ffffff'
								},
								tweets: {
									background: '#ffffff', color: '#7a7a7a', links: '#00afef'
								}
							},
							features: {
								scrollbar: false, loop: false, live: false, behavior: 'all'
							}
						}).render().setUser('<?php echo $twittername; ?>').start();
				</script>
				<!-- end twitter -->
			</div><?php } ?>
			<?php if ($googleid != '' && $googleapi != ''){ ?><div id="tab-3" style="width:<?php echo $pluginwidth; ?>px">
				<!-- Google -->
				<div id="gpluswidget" data-id="<?php echo $googleid; ?>" data-key="<?php echo $googleapi; ?>" data-posts="3" data-lang="yes" data-width="<?php echo $pluginwidth; ?>" data-bkg="transparent" data-padding="10" data-border="f5f5f5" data-radius="0" data-txt="0c0c0c" data-link="36c" data-favicon="yes" data-header="yes" data-footer="yes" data-page="no"></div>
				<script type="text/javascript" src="http://gplusapi.googlecode.com/files/widget0.js"></script>
				<!-- end google -->
			</div> <?php } ?>
			<?php if ($pinusernamecheck != '' ){ ?><div id="tab-4" style="width:<?php echo $pluginwidth; ?>px">
				<!-- Pinterest -->
				<?php
				$pintitle = apply_filters('widget_title', $instance['pintitle']);
				echo($before_title . $pintitle . $after_title);
				?>
				<div id="pinterest-pinboard-widget-container">
					<div class="pinboard">
					<?php
						// Get the RSS.
						$pinusername = $instance['pinusername'];
						$pinrows = $instance['pinrows'];
						// $pincols = $instance['pincols'];
						$pincols = floor($pluginwidth / 65);
						$nr_pins = $pinrows * $pincols;
						$pins = $this->get_pins($pinusername, $nr_pins);
						if (is_null($pins)) {
							echo("Unable to load Pinterest pins for '$username'\n");
						} else {
							// Render the pinboard.
							$count = 0;
							$totalpins = 1;
							foreach ($pins as $pin) {
								if ($count == 0) {
									echo("<div class=\"pinrow\">");
								}
								$pintitle = $pin['pintitle'];
								$url = $pin['url'];
								$image = $pin['image'];
								echo("<a href=\"$url\"><img src=\"$image\" alt=\"$pintitle\" title=\"$pintitle\" /></a>");
								$count++;
								$totalpins++;
								if ($count >= $pincols || $totalpins > sizeof($pins)) {
									echo("</div>");
									$count = 0;
								}
							}
						}
					?>
					</div>
					<div class="pin_link">
						<a class="pin_logo" href="<?php echo($this->protocol) ?>pinterest.com/<?php echo($pinusername) ?>/"><img src="<?php echo($this->protocol) ?>passets-cdn.pinterest.com/images/small-p-button.png" width="16" height="16" alt="Follow Me on Pinterest" /></a>
						<span class="pin_text"><a href="http://pinterest.com/<?php echo($pinusername) ?>/">More Pins</a></span>
					</div>
				</div>
			</div>
				<!-- end pinterest --> <?php } ?>
			<?php if ($stumbleuser != ''){ ?>
				<div id="tab-5" style="width:<?php echo $pluginwidth; ?>px">
				<!-- stumble upon --><?php
					function wpef_fetch_feed($url, $time) {
						require_once (ABSPATH . WPINC . '/class-feed.php');
						$feed = new SimplePie();
						$feed->set_feed_url($url);
						$feed->set_cache_class('WP_Feed_Cache');
						$feed->set_file_class('WP_SimplePie_File');
						$feed->set_cache_duration(apply_filters('wp_feed_cache_transient_lifetime', $time, $url));
						do_action_ref_array( 'wp_feed_options', array( &$feed, $url ) );
						$feed->init();
						$feed->handle_content_type();

						if ( $feed->error() )
							return new WP_Error('simplepie-error', $feed->error());
							return $feed;
						} ?>
						<?php
							$stumbletitle = apply_filters('widget_title', $instance['stumbletitle']);
							echo($before_title . $stumbletitle . $after_title);
						?>
						<?php	$rss = wpef_fetch_feed('http://rss.stumbleupon.com/user/' . $stumbleuser . '/favorites', 900);
							// If there were no errors get the amount of items to display.
							if (!is_wp_error( $rss ) ) :
								$maxitems = 2;
								if($pluginwidth > 249 && $pluginwidth < 400){ $maxitems = 4;};
								if($pluginwidth > 399){ $maxitems = 6;};
								$rss_items = $rss->get_items(0, $maxitems); 
							endif;
						?>
						<ul<?php if($pluginwidth > 249 && $pluginwidth < 400){ ?> class="stumblewide"<?php } elseif($pluginwidth > 399){ ?> class="stumbleextrawide"<?php } ?>>
						<?php
							// Display error if no results were returned or if there was an error.
							// @since 1.1.0: Display Custom Error
							if ($maxitems == 0) : echo htmlspecialchars_decode($custom_error);
					   		else :
							// Loop through results and echo each result in the form of a hyperlink.
								$stumblecounter = 1;
								if($pluginwidth < 400){
								foreach ( $rss_items as $item ) : ?>
									<li<?php if($stumblecounter % 2 == 0){?> class="stumbleright"<?php } ?>>
										<?php $stumblecounter++ ?>
					
										<a class="stumbletitle" href='<?php echo $item->get_permalink(); ?>' title='<?php echo 'Posted '.$item->get_date('j F Y'); ?>'>
										<?php echo $item->get_title();?></a>
										<div class="stumblecontent"><?php echo $item->get_description();?></div>
									</li>
								<?php endforeach;
								}else{
								foreach ( $rss_items as $item ) : ?>
									<li<?php if($stumblecounter == 1){ ?> class="stumbleclear"<?php } elseif ($stumblecounter == 3){ $stumblecounter = 0; ?> class="stumbleright"<?php } ?>>
										<?php $stumblecounter++ ?>
					
										<a class="stumbletitle" href='<?php echo $item->get_permalink(); ?>' title='<?php echo 'Posted '.$item->get_date('j F Y'); ?>'>
										<?php echo $item->get_title();?></a>
										<div class="stumblecontent"><?php echo $item->get_description();?></div>
									</li>
								<?php endforeach;
								}
							endif;?>
						</ul>
				<!-- end stumble upon -->
			</div> <?php } ?>
		</div>
		<?php 
			echo $after_widget;
	}
	/**
	* PINTEREST - Retrieve RSS feed for username, and parse the data needed from it.
	* Returns null on error, otherwise a hash of pins.
	*/
	function get_pins($pinusername, $nrpins) {
		// Set caching.
		add_filter('wp_feed_cache_transient_lifetime', create_function('$a', 'return '. $this->widget['cache_lifetime'] .';'));

		// Get the RSS feed.
		$url = $this->widget['pinterest_url'] .'/'. $pinusername .'/feed.rss';
		$rss = fetch_feed($url);
		if (is_wp_error($rss)) {
			return null;
		}

		$maxitems = $rss->get_item_quantity($nrpins);
		$rss_items = $rss->get_items(0, $maxitems);

		$pins;
		if (is_null($rss_items)) {
			$pins = null;
		} else {
			// Pattern to replace for the images.
			$search = array('_b.jpg');
			$replace = array('_t.jpg');
			// Add http replace is running secure.
			if ($this->is_secure) {
				array_push($search, 'http://');
				array_push($replace, $this->protocol);
			}
			$pins = array();
			foreach ($rss_items as $item) {
				$pintitle = $item->get_title();
				$description = $item->get_description();
				$url = $item->get_permalink();
				if (preg_match_all('/<img src="([^"]*)".*>/i', $description, $matches)) {
					$image = str_replace($search, $replace, $matches[1][0]);
				}
				array_push($pins, array(
					pintitle => $pintitle,
					image => $image,
					url => $url
				));
			}
		}
		return $pins;
	}
	/**
	* Check if the server is running SSL.
	*/
	function is_secure() {
		return !empty($_SERVER['HTTPS'])
			&& $_SERVER['HTTPS'] !== 'off'
			|| $_SERVER['SERVER_PORT'] == 443;
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("SocialGluttonWidget");') );?>