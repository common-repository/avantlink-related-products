<?php

// rp_settings_page() displays the page content for the Test settings submenu
function rp_settings_page() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 

    $al_id_opt_name = 'al_id';
    $al_id_data_field_name = 'al_id';

    $title_opt_name = 'title';
    $title_data_field_name = 'title';

    $num_results_opt_name = 'num_results';
    $num_results_data_field_name = 'num_results';

    $keyword_opt_name = 'keyword';
    $keyword_data_field_name = 'keyword';

    $nkeyword_opt_name = 'nkeyword';
    $nkeyword_data_field_name = 'nkeyword';

    $sponsor_link_opt_name = 'sponsor_link';
    $sponsor_link_data_field_name = 'sponsor_link';

    $all_posts_opt_name = 'all_posts';
    $all_posts_data_field_name = 'all_posts';

    $hidden_field_name = 'rp_submit_hidden';

    // Read in existing option value from database
    $al_id_opt_val = get_option( $al_id_opt_name );
    $title_opt_val = get_option( $title_opt_name );
    $num_results_opt_val = get_option( $num_results_opt_name );
    $keyword_opt_val = get_option( $keyword_opt_name );
    $nkeyword_opt_val = get_option( $nkeyword_opt_name );
    $sponsor_link_opt_val = get_option( $sponsor_link_opt_name );
    $all_posts_opt_val = get_option( $all_posts_opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $al_id_opt_val = $_POST[ $al_id_data_field_name ];
        $title_opt_val = $_POST[ $title_data_field_name ];
        $num_results_opt_val = $_POST[ $num_results_data_field_name ];
        $keyword_opt_val = $_POST[ $keyword_data_field_name ];
        $nkeyword_opt_val = $_POST[ $nkeyword_data_field_name ];
        $sponsor_link_opt_val = $_POST[ $sponsor_link_data_field_name ];
        $all_posts_opt_val = $_POST[ $all_posts_data_field_name ];

        // Save the posted value in the database
        update_option( $al_id_opt_name, $al_id_opt_val );
        update_option( $title_opt_name, $title_opt_val );
        update_option( $num_results_opt_name, $num_results_opt_val );
        update_option( $keyword_opt_name, $keyword_opt_val );
        update_option( $nkeyword_opt_name, $nkeyword_opt_val );
        update_option( $sponsor_link_opt_name, $sponsor_link_opt_val );
        update_option( $all_posts_opt_name, $all_posts_opt_val );

        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Settings Saved.', '' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'AvantLink Related Products Settings', '' ) . "</h2>";

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<table class="form-table"> 

<tr valign="top"><th scope="row" colspan="3"><label for="blogname"><em>To style this plugin, edit the style.css file in its root directory. Adjustments to the width of the plugin's "rp_item" and "rp_item img" classes may be necessary depending on its placement.</em></label></th>
</tr> 

<tr valign="top"> 
<th scope="row"><label for="blogname"><?php _e("AvantLink Affiliate ID:", 'al_id' ); ?></label></th> 
<td> <input type="text" name="<?php echo $al_id_data_field_name; ?>" value="<?php echo $al_id_opt_val; ?>" size="20"></td> 
</tr> 

<tr valign="top"> 
<th scope="row"><label for="blogname"><?php _e("Plugin Title Text:", 'title' ); ?></label></th> 
<td> <input type="text" name="<?php echo $title_data_field_name; ?>" value="<?php echo $title_opt_val; ?>" size="20"> <span class="description">Defaults to "Related Products" if blank.</span></td> 
</tr> 

<tr valign="top"> 
<th scope="row"><label for="blogname"><?php _e("Apply To All Posts:", 'all_posts' ); ?></label></th> 
<td><input type="checkbox" name="<?php echo $all_posts_data_field_name; ?>" value="1" <?php if($all_posts_opt_val == 1) { echo "checked"; } ?> /> <span class="description">Display related products in all posts using the default keywords defined below. By default related products will only be displayed on posts that have keywords defined in their "AvantLink Related Products" meta box.</span></td> 
</tr>

<tr valign="top"> 
<th scope="row"><label for="blogname"><?php _e("Default Keywords/Phrases (comma separated):", 'keyword' ); ?></label></th> 
<td><input type="text" name="<?php echo $keyword_data_field_name; ?>" value="<?php echo $keyword_opt_val; ?>" size="20"> <span class="description">Default keywords/phrases to query. Keywords defined within the "AvantLink Related Products" meta box of specific posts will override these.</span></td> 
</tr> 

<tr valign="top"> 
<th scope="row"><label for="blogname"><?php _e("Default Negative Keywords (comma separated):", 'keyword' ); ?></label></th> 
<td><input type="text" name="<?php echo $nkeyword_data_field_name; ?>" value="<?php echo $nkeyword_opt_val; ?>" size="20"> <span class="description">Default keywords to exclude from search results. Negative keywords defined within the "AvantLink Related Products" meta box of specific posts will override these.</span></td> 
</tr> 

<tr valign="top"> 
<th scope="row"><label for="blogname"><?php _e("Default Number of Products:", 'num_results' ); ?></label></th> 
<td><input type="text" name="<?php echo $num_results_data_field_name; ?>" value="<?php echo $num_results_opt_val; ?>" size="20"> <span class="description">The default number of products to display, 10 Maximum. Defaults to 5 if blank.</span></td> 
</tr> 
 
<tr valign="top"> 
<th scope="row"><label for="blogname"><?php _e("Don't Show Sponsor Link:", 'sponsor_link' ); ?></label></th> 
<td><input type="checkbox" name="<?php echo $sponsor_link_data_field_name; ?>" value="1" <?php if($sponsor_link_opt_val == 1) { echo "checked"; } ?> /></td> 
</tr> 

</table>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
 
}

?>