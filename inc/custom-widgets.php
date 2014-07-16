<?php
add_action('init', 'custom_widgets_custom_init');
function custom_widgets_custom_init() 
{
  $labels = array(
    'name' => __('Custom Widgets', 'post type general name'),
    'singular_name' => __('Widget', 'post type singular name'),
    'add_new' => __('Add New', 'Widget'),
    'add_new_item' => __('Add New Widget'),
    'edit_item' => __('Edit Widget'),
    'new_item' => __('New Widget'),
    'view_item' => __('View Widget'),
    'search_items' => __('Search Widget'),
    'not_found' =>  __('No Widget found'),
    'not_found_in_trash' => __('No Widget found in Trash'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    /*'rewrite' => true,*/
	'rewrite' => array('slug' => 'custom-widget', 'with_front' => FALSE),
    'capability_type' => 'post',
    'hierarchical' => true,
    'menu_position' => null,
    'supports' => array('title','editor','thumbnail',)
  ); 
  register_post_type('custom-widget', $args);
  
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}


//add filter to insure the text Slide, or Slide, is displayed when user updates a Slide 
add_filter('post_updated_messages', 'custom_widgets_updated_messages');
function custom_widgets_updated_messages( $messages ) {

  $messages['Slide'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Widget updated. <a href="%s">View Widget</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Widget updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Widget restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Widget published. <a href="%s">View Widget</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Widget saved.'),
    8 => sprintf( __('Widget submitted. <a target="_blank" href="%s">Preview Widget</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Widget scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Widget</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Widget draft updated. <a target="_blank" href="%s">Preview Widget</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

//display contextual help for Slide
add_action( 'contextual_help', 'add_custom_widgets_help_text', 10, 3 );

function add_custom_widgets_help_text($contextual_help, $screen_id, $screen) { 
  //$contextual_help = . var_dump($screen); // use this to help determine $screen->id
  if ('Widget' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing a Widget:') . '</p>' .
      '<ul>' .
      '<li>' . __('Specify the correct genre such as Mystery, or Historic.') . '</li>' .
      '<li>' . __('Specify the correct writer of the Widget.  Remember that the Author module refers to you, the author of this Widget review.') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule the Widget review to be published in the future:') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.') . '</li>' .
      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:') . '</strong></p>' .
      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>') . '</p>' .
      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>' ;
  } elseif ( 'edit-Widget' == $screen->id ) {
    $contextual_help = 
      '<p>' . __('This is the help screen displaying the table of Widget blah blah blah.') . '</p>' ;
  }
  return $contextual_help;
}

class HomeBlockWidget extends WP_Widget {
    function HomeBlockWidget() {
        parent::WP_Widget(false, $name = 'Home Widget');
    }

    function widget($args, $instance) {
		global $wpdb;

		extract( $args );

		$wid = $instance['wid'];
		if ($wid) {
			$wpost = get_post($wid);

			$title = apply_filters('widget_title', $wpost->post_title);
			$button_link = get_post_meta($wpost->ID, 'button_link', true);
			$button_text = get_post_meta($wpost->ID, 'button_text', true);
			$wpost_image = get_post_thumbnail_id($wpost->ID);

			echo $before_widget;
?>
			<?php if ($wpost_image) { ?><img src="<?php echo get_thumb($wpost_image, 100, 100); ?>" class="alignright" alt="<?php echo $title; ?>"><?php } ?>
			<div class="holder">
				<?php echo $before_title . $title . $after_title; ?>
				<?php echo wpautop($wpost->post_content); ?>
				<a href="<?php echo $button_link; ?>" class="btn-green"><?php echo $button_text; ?></a>
			</div>
<?php
			echo $after_widget;
		}
    }

    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['wid'] = $new_instance['wid'];
        return $instance;
    }

    function form($instance) {
        $wid = $instance['wid'];
		$cw_posts = get_posts('post_type=custom-widget&posts_per_page=-1&orderby=title&order=asc');
		if (!$cw_posts) { $cw_posts = array(); }
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('wid'); ?>"><?php _e('Widget:'); ?></label> 
          <select class="widefat" id="<?php echo $this->get_field_id('wid'); ?>" name="<?php echo $this->get_field_name('wid'); ?>">
			<option value="">-- Select Widget --</option>
			<?php foreach($cw_posts as $cw_post) { $s = ''; if ($wid == $cw_post->ID) { $s = ' SELECTED'; } ?>
			<option value="<?php echo $cw_post->ID; ?>"<?php echo $s; ?>><?php echo $cw_post->post_title; ?></option>
			<?php } ?>
		  </select>
        </p>
        <?php 
    }

}

class RightSidebarWidget extends WP_Widget {
    function RightSidebarWidget() {
        parent::WP_Widget(false, $name = 'Right Sidebar Widget');
    }

    function widget($args, $instance) {
		global $wpdb;

		extract( $args );

		$wid = $instance['wid'];
		if ($wid) {
			$wpost = get_post($wid);

			$title = apply_filters('widget_title', $wpost->post_title);
			$button_link = get_post_meta($wpost->ID, 'button_link', true);
			$button_text = get_post_meta($wpost->ID, 'button_text', true);
			$wpost_image = get_post_thumbnail_id($wpost->ID);

			echo $before_widget;
?>
			<?php if ($wpost_image) { ?><img src="<?php echo get_thumb($wpost_image, 100, 100); ?>" class="alignright" alt="<?php echo $title; ?>"><?php } ?>
			<?php echo $before_title . $title . $after_title; ?>
			<?php echo wpautop($wpost->post_content); ?>
			<a href="<?php echo $button_link; ?>" class="btn-green"><?php echo $button_text; ?></a>
<?php
			echo $after_widget;
		}
    }

    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['wid'] = $new_instance['wid'];
        return $instance;
    }

    function form($instance) {
        $wid = $instance['wid'];
		$cw_posts = get_posts('post_type=custom-widget&posts_per_page=-1&orderby=title&order=asc');
		if (!$cw_posts) { $cw_posts = array(); }
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('wid'); ?>"><?php _e('Widget:'); ?></label> 
          <select class="widefat" id="<?php echo $this->get_field_id('wid'); ?>" name="<?php echo $this->get_field_name('wid'); ?>">
			<option value="">-- Select Widget --</option>
			<?php foreach($cw_posts as $cw_post) { $s = ''; if ($wid == $cw_post->ID) { $s = ' SELECTED'; } ?>
			<option value="<?php echo $cw_post->ID; ?>"<?php echo $s; ?>><?php echo $cw_post->post_title; ?></option>
			<?php } ?>
		  </select>
        </p>
        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("HomeBlockWidget");'));
add_action('widgets_init', create_function('', 'return register_widget("RightSidebarWidget");'));
?>