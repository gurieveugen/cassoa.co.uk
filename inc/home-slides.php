<?php
add_action('init', 'home_slides_custom_init');
function home_slides_custom_init() 
{
  $labels = array(
    'name' => __('Home Slides', 'post type general name'),
    'singular_name' => __('Slide', 'post type singular name'),
    'add_new' => __('Add New', 'Slide'),
    'add_new_item' => __('Add New Slide'),
    'edit_item' => __('Edit Slide'),
    'new_item' => __('New Slide'),
    'view_item' => __('View Slide'),
    'search_items' => __('Search Slide'),
    'not_found' =>  __('No Slide found'),
    'not_found_in_trash' => __('No Slide found in Trash'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    /*'rewrite' => true,*/
	'rewrite' => array('slug' => 'home-slide', 'with_front' => FALSE),
    'capability_type' => 'post',
    'hierarchical' => true,
    'menu_position' => null,
    'supports' => array('title','editor','thumbnail','page-attributes')
  ); 
  register_post_type('home-slide', $args);
  
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}


//add filter to insure the text Slide, or Slide, is displayed when user updates a Slide 
add_filter('post_updated_messages', 'home_slides_updated_messages');
function home_slides_updated_messages( $messages ) {

  $messages['Slide'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Slide updated. <a href="%s">View Slide</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Slide updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Slide restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Slide published. <a href="%s">View Slide</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Slide saved.'),
    8 => sprintf( __('Slide submitted. <a target="_blank" href="%s">Preview Slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Slide scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Slide</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Slide draft updated. <a target="_blank" href="%s">Preview Slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

//display contextual help for Slide
add_action( 'contextual_help', 'add_home_slides_help_text', 10, 3 );

function add_home_slides_help_text($contextual_help, $screen_id, $screen) { 
  //$contextual_help = . var_dump($screen); // use this to help determine $screen->id
  if ('Slide' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing a Slide:') . '</p>' .
      '<ul>' .
      '<li>' . __('Specify the correct genre such as Mystery, or Historic.') . '</li>' .
      '<li>' . __('Specify the correct writer of the Slide.  Remember that the Author module refers to you, the author of this Slide review.') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule the Slide review to be published in the future:') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.') . '</li>' .
      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:') . '</strong></p>' .
      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>') . '</p>' .
      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>' ;
  } elseif ( 'edit-Slide' == $screen->id ) {
    $contextual_help = 
      '<p>' . __('This is the help screen displaying the table of Slide blah blah blah.') . '</p>' ;
  }
  return $contextual_help;
}
?>