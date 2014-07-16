<?php
/*
 * @package WordPress
 * @subpackage Base_Theme
 */

define('TDU', get_bloginfo('template_url'));

require_once (dirname (__FILE__) . '/inc/ajax-actions.php');
require_once (dirname (__FILE__) . '/inc/home-slides.php');
require_once (dirname (__FILE__) . '/inc/custom-widgets.php');
require_once (dirname (__FILE__) . '/inc/theme-options.php');
require_once (dirname (__FILE__) . '/inc/members.php');
require_once (dirname (__FILE__) . '/inc/cyc.php');
require_once (dirname (__FILE__) . '/inc/users-csv-import.php');

$caravan_theme_options = get_option("caravan_theme_options");

add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-thumbnails' );
add_image_size( 'archive-thumbnail', 126, 92, true );
add_image_size( 'single-thumbnail', 205, 205, true );

register_sidebar(array(
	'id' => 'home-blocks',
	'name' => 'Home Blocks',
	'before_widget' => '<div class="block">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'id' => 'right-sidebar',
	'name' => 'Right Sidebar',
	'before_widget' => '<aside class="aside">',
	'after_widget' => '</aside>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_nav_menus( array(
	'primary_nav' => __( 'Primary Navigation', 'theme' ),
	'left_nav' => __( 'Left Navigation', 'theme' ),
	'footer_nav' => __( 'Footer Navigation', 'theme' )
) );

function change_menu_classes($css_classes){
	$css_classes = str_replace("current-menu-item", "current-menu-item active", $css_classes);
	$css_classes = str_replace("current-menu-parent", "current-menu-parent active", $css_classes);
	return $css_classes;
}
add_filter('nav_menu_css_class', 'change_menu_classes');

function show_posted_in() {
	$categories_list = get_the_category_list( __( ', ', 'theme' ) );
	$tag_list = get_the_tag_list( '', __( ', ', 'theme' ) );
	if ( '' != $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s by <a href="%6$s">%5$s</a>. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'theme' );
	} elseif ( '' != $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s by <a href="%6$s">%5$s</a>. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'theme' );
	} else {
		$utility_text = __( 'This entry was posted by <a href="%6$s">%5$s</a>. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'theme' );
	}
	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		esc_url( get_permalink() ),
		the_title_attribute( 'echo=0' ),
		get_the_author(),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) )
	);
}

function short_content($content,$sz = 500,$more = '...') {
	if (strlen($content)<$sz) return $content;
	$p = strpos($content, " ",$sz);
	if (!$p) return $content;
	$content = strip_tags($content);
	if (strlen($content)<$sz) return $content;
	$p = strpos($content, " ",$sz);
	if (!$p) return $content;
	return substr($content, 0, $p).$more;
}

function wp_nav( $nav_id ) {
	global $wp_query;
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="navigation" id="<?php echo $nav_id; ?>">
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'theme' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'theme' ) ); ?></div>
		</div><!-- #nav-above -->
	<?php endif;
}

function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {
	global $post;
	$link = '<a href="'.get_permalink($post->ID).'" class="more-link">read more »</a>';
	return '... '.$link;
}
add_filter('excerpt_more', 'new_excerpt_more');

function get_thumb($attach_id, $width, $height, $crop = false) {
	if (is_numeric($attach_id)) {
		$image_src = wp_get_attachment_image_src($attach_id, 'full');
		$file_path = get_attached_file($attach_id);
	} else {
		$imagesize = getimagesize($attach_id);
		$image_src[0] = $attach_id;
		$image_src[1] = $imagesize[0];
		$image_src[2] = $imagesize[1];
		$file_path = $_SERVER["DOCUMENT_ROOT"].str_replace(get_bloginfo('siteurl'), '', $attach_id);
		
	}
	
	$file_info = pathinfo($file_path);
	$extension = '.'. $file_info['extension'];

	// image path without extension
	$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

	$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

	// if file size is larger than the target size
	if ($image_src[1] > $width || $image_src[2] > $height) {
		// if resized version already exists
		if (file_exists($cropped_img_path)) {
			return str_replace(basename($image_src[0]), basename($cropped_img_path), $image_src[0]);
		}

		if (!$crop) {
			// calculate size proportionaly
			$proportional_size = wp_constrain_dimensions($image_src[1], $image_src[2], $width, $height);
			$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;			

			// if file already exists
			if (file_exists($resized_img_path)) {
				return str_replace(basename($image_src[0]), basename($resized_img_path), $image_src[0]);
			}
		}

		// resize image if no such resized file
		$new_img_path = image_resize($file_path, $width, $height, $crop);
		//echo 'new_img_path '.print_r($new_img_path);
		$new_img_size = getimagesize($new_img_path);
		return str_replace(basename($image_src[0]), basename($new_img_path), $image_src[0]);
	}

	// return without resizing
	return $image_src[0];
}

function get_postcodes_by_radius($postcode, $radius) {
	global $wpdb;
	$member_postcodes = array();
	if (strlen($postcode) && strlen($radius)) {
		$postcode_data = $wpdb->get_row(sprintf("SELECT * FROM %spostcodes WHERE postcode = '%s'", $wpdb->prefix, $postcode));
		if ($postcode_data) {
			$latitude = $postcode_data->latitude;
			$longitude = $postcode_data->longitude;

			$check_m = $wpdb->get_var(sprintf("SELECT user_id FROM %susermeta WHERE meta_key = 'postcode' AND REPLACE(meta_value, ' ', '') = '%s'", $wpdb->prefix, $postcode));
			if ($check_m) {
				$member_postcodes[] = $postcode;
			}

			$equator_lat_mile = 69.172;
			$max_lat = $latitude + ($radius / $equator_lat_mile);
			$min_lat = $latitude - ($max_lat - $latitude);
			$max_long = $longitude + $radius / (cos($min_lat * M_PI / 180) * $equator_lat_mile);
			$min_long = $longitude - ($max_long - $longitude);
			
			$pcodes = $wpdb->get_results(sprintf("SELECT p.postcode FROM %spostcodes p INNER JOIN %susermeta um ON REPLACE(um.meta_value, ' ', '') = p.postcode AND um.meta_key = 'postcode' WHERE (p.latitude BETWEEN %s AND %s) AND (p.longitude BETWEEN %s AND %s) ORDER BY p.postcode", $wpdb->prefix, $wpdb->prefix, $min_lat, $max_lat, $min_long, $max_long));
			if (count($pcodes)) {
				foreach($pcodes as $pcode) {
					$member_postcodes[] = $pcode->postcode;
				}
			}
		}
	}
	return $member_postcodes;
}

function get_search_members($params) {
	global $wpdb;
	$members = array();
	$member_postcodes = array();
	$members_exists = array();
	$s_postcode = $params['s_postcode'];
	$s_miles = $params['s_miles'];
	$s_sitename = $params['s_sitename'];
	$s_county = $params['s_county'];
	$limit = $params['per_page'];
	$miles_vals = $params['miles'];

	if (strlen($s_sitename)) {
		if (strlen($s_county)) {
			$smembers = $wpdb->get_results(sprintf("SELECT u.* FROM %susers u LEFT JOIN %susermeta um ON um.user_id = u.ID LEFT JOIN %susermeta um2 ON um2.user_id = u.ID LEFT JOIN %susermeta um3 ON um3.user_id = u.ID WHERE um.meta_key = 'wp_capabilities' AND (um.meta_value LIKE '%s' OR um.meta_value LIKE '%s') AND um2.meta_key = 'site_name' AND um2.meta_value LIKE '%s' AND (um3.meta_key = 'address3' OR um3.meta_key = 'address4') AND um3.meta_value LIKE '%s' GROUP BY u.ID ORDER BY um2.meta_value ASC LIMIT 0, %s", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, '%subscriber%', '%s2member_level%', '%'.$s_sitename.'%', '%'.$s_county.'%', $limit));
		} else {
			$smembers = $wpdb->get_results(sprintf("SELECT u.* FROM %susers u LEFT JOIN %susermeta um ON um.user_id = u.ID LEFT JOIN %susermeta um2 ON um2.user_id = u.ID WHERE um.meta_key = 'wp_capabilities' AND (um.meta_value LIKE '%s' OR um.meta_value LIKE '%s') AND um2.meta_key = 'site_name' AND um2.meta_value LIKE '%s' GROUP BY u.ID ORDER BY um2.meta_value ASC LIMIT 0, %s", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, '%subscriber%', '%s2member_level%', '%'.$s_sitename.'%', $limit));
		}
	} else if (strlen($s_county)) {
		$smembers = $wpdb->get_results(sprintf("SELECT u.* FROM %susers u LEFT JOIN %susermeta um ON um.user_id = u.ID LEFT JOIN %susermeta um2 ON um2.user_id = u.ID WHERE um.meta_key = 'wp_capabilities' AND (um.meta_value LIKE '%s' OR um.meta_value LIKE '%s') AND (um2.meta_key = 'address3' OR um2.meta_key = 'address4') AND um2.meta_value LIKE '%s' GROUP BY u.ID ORDER BY um2.meta_value ASC LIMIT 0, %s", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, '%subscriber%', '%s2member_level%', '%'.$s_county.'%', $limit));
	} else if (strlen($s_postcode)) {
		$s_postcode = str_replace(' ', '', $s_postcode);
		if ($s_miles > 0) { // by radius
			foreach ($miles_vals as $m) { // begin from small
				if ($m <= $s_miles) {
					$mpostcodes = get_postcodes_by_radius($s_postcode, $m);
					foreach($mpostcodes as $mpostcode) {
						if (!in_array($mpostcode, $member_postcodes)) {
							$member_postcodes[] = $mpostcode;
							$smembers = $wpdb->get_results(sprintf("SELECT u.* FROM %susers u LEFT JOIN %susermeta um ON um.user_id = u.ID LEFT JOIN %susermeta um2 ON um2.user_id = u.ID WHERE um.meta_key = 'wp_capabilities' AND (um.meta_value LIKE '%s' OR um.meta_value LIKE '%s') AND um2.meta_key = 'postcode' AND REPLACE(um2.meta_value, ' ', '') = '%s' GROUP BY u.ID ORDER BY um2.meta_value ASC LIMIT 0, %s", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, '%subscriber%', '%s2member_level%', $mpostcode, $limit));
							if ($smembers) {
								foreach($smembers as $smember) {
									if (!in_array($smember->ID, $members_exists)) {
										$members[] = $smember;
										$members_exists[] = $smember->ID;
									}
									if (count($members) >= $limit) {
										return $members;
									}
								}
							}
						}
					}
				}
			}
		} else {
			$smembers = $wpdb->get_results(sprintf("SELECT u.* FROM %susers u LEFT JOIN %susermeta um ON um.user_id = u.ID LEFT JOIN %susermeta um2 ON um2.user_id = u.ID WHERE um.meta_key = 'wp_capabilities' AND (um.meta_value LIKE '%s' OR um.meta_value LIKE '%s') AND um2.meta_key = 'postcode' AND REPLACE(um2.meta_value, ' ', '') = '%s' GROUP BY u.ID ORDER BY um2.meta_value ASC LIMIT 0, %s", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, '%subscriber%', '%s2member_level%', $s_postcode, $limit));
		}
	}
	if ($smembers) {
		foreach($smembers as $smember) {
			if (!in_array($smember->ID, $members_exists)) {
				$members[] = $smember;
				$members_exists[] = $smember->ID;
			}
		}
	}

	return $members;
}
