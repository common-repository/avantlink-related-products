<?php
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'rp_add_custom_box');

/* Use the save_post action to do something with the data entered */
add_action('save_post', 'rp_save_postdata');

/* Adds a custom section to the "advanced" Post and Page edit screens */
function rp_add_custom_box() {

  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'rp_sectionid', __( 'AvantLink Related Products', 'rp_textdomain' ), 
                'rp_inner_custom_box', 'post', 'side' );
    add_meta_box( 'rp_sectionid', __( 'AvantLink Related Products', 'rp_textdomain' ), 
                'rp_inner_custom_box', 'page', 'side' );
   } else {
    add_action('dbx_post_advanced', 'rp_old_custom_box' );
    add_action('dbx_page_advanced', 'rp_old_custom_box' );
  }
}
   
/* Prints the inner fields for the custom post/page section */
function rp_inner_custom_box() {

  global $post;

  // Use nonce for verification

  echo '<input type="hidden" name="rp_noncename" id="rp_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  // The actual fields for data entry

  echo '<p><label for="rp_keyword">' . __("Keywords/Phrases (comma separated): ", 'rp_textdomain' ) . '</label></p><p>';

  $keyword = get_post_meta($post->ID, '_rp_keyword', true);

  echo '<input type="text" name="rp_keyword" value="'.$keyword.'" size="40" /></p>';

  echo '<p><label for="rp_nkeyword">' . __("Negative Keywords (comma separated): ", 'rp_textdomain' ) . '</label></p><p>';

  $nkeyword = get_post_meta($post->ID, '_rp_nkeyword', true);

  echo '<input type="text" name="rp_nkeyword" value="'.$nkeyword.'" size="40" /></p>';

  echo '<p><label for="rp_num_results">' . __("Number of Results: ", 'rp_textdomain' ) . '</label> ';

  $results = get_post_meta($post->ID, '_rp_num_results', true);

  echo '<input type="text" name="rp_num_results" value="'.$results.'" size="20" /></p>';
}

/* Prints the edit form for pre-WordPress 2.5 post/page */
function rp_old_custom_box() {

  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="rp_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'My Post Section Title', 'rp_textdomain' ) . "</h3></div>";   
   
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  // output editing form

  rp_inner_custom_box();

  // end wrapper

  echo "</div></div></fieldset></div>\n";
}

/* When the post is saved, saves our custom data */
function rp_save_postdata( $post_id ) {

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['rp_noncename'], plugin_basename(__FILE__) )) {
    return $post_id;
  }

  // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  // to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;

  
  // Check permissions
  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
      return $post_id;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
      return $post_id;
  }

  // OK, we're authenticated: we need to find and save the data

  $mydata = $_POST['rp_keyword'];
  update_post_meta($post_id, '_rp_keyword', $mydata);

  $mydata = $_POST['rp_nkeyword'];
  update_post_meta($post_id, '_rp_nkeyword', $mydata);

  $mydata = $_POST['rp_num_results'];
  update_post_meta($post_id, '_rp_num_results', $mydata);

  return true;
}
?>