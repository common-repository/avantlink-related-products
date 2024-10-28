<?php
/*
Plugin Name: AvantLink Related Products
Plugin URI: http://wordpress.org/#
Description: Outputs a widget of related products from an AvantLink Product Search query based on a set of site-wide or post-specific keywords.
Version: 0.5.4
Author: Nathaniel Volk
Author URI: http://nathanielvolk.com/
License: GPL2
*/

/*  Copyright 2010 Nathaniel Volk (email: thanvolk@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Hook for adding admin menus
add_action('admin_menu', 'rp_add_pages');

// action function for above hook
function rp_add_pages() {
    // Add a new submenu under Settings:
    add_options_page(__('AvantLink Related Products','related-products'), __('AvantLink Related Products','related-products'), 'manage_options', 'related-products', 'rp_settings_page');
}

add_action('wp_head', 'rp_style');

function rp_style() {

echo '<link rel="stylesheet" href="'.get_bloginfo('url') .'/wp-content/plugins/avantlink-related-products/style.css" type="text/css">';

}

//constructs/controls settings page
include 'settings.php';

//constructs/controls page/post meta box
include 'meta_box.php';


function display_related_products() {

global $post;
$id = $post->ID;

$keyword = get_post_meta($id, '_rp_keyword', true);
$nkeyword = get_post_meta($id, '_rp_nkeyword', true);
$results = get_post_meta($id, '_rp_num_results', true);

//echo $search_term;

$al_id = get_option('al_id');
$d_results = get_option('num_results');
$d_keyword = get_option('keyword');
$d_nkeyword = get_option('nkeyword');
$d_title = get_option('title');
$sponsor_link = get_option('sponsor_link');
$all_posts = get_option('all_posts');

//echo "$id $keyword $num_results $al_id $d_num_results $d_keyword";

if($al_id != '') { $affiliate_id = $al_id; } else { $affiliate_id = 9037; }
if($keyword != '') { $search_term = $keyword; } else { $search_term = $d_keyword; }
if($nkeyword != '') { $nsearch_term = $nkeyword; } else { $nsearch_term = $d_nkeyword; }
if($results != '') { $num_results = $results; } else { $num_results = $d_results; }
if($d_title == '') { $title = "Related Products"; } else { $title = $d_title; }

if($all_posts != 1 && $keyword == '') { $disabled = true; }

$nsearch_terms = explode(",", $nsearch_term);

for($i = 0; $i < count($nsearch_terms); $i++) {

//if(strpos($nsearch_terms[$i]," ") == 0) { $nsearch_terms[$i] = substr($nsearch_terms[$i], 1); }

$nsearch_term = str_replace(" "," -",$nsearch_terms[$i]);

$query .= "-".$nsearch_term." ";

}

$search_terms = explode(",", $search_term);

for($i = 0; $i < count($search_terms); $i++) {

//if(strpos($search_terms[$i]," ") == 0) { $search_terms[$i] = substr($search_terms[$i], 1); }

$search_term = str_replace(" ","+",$search_terms[$i]);

if($i == 0) { $query .= $search_term; } else { $query .= " | ".$search_term; }

}

for($i = 0; $i < count($nsearch_terms); $i++) {

$nsearch_term = str_replace(" "," -",$nsearch_terms[$i]);

$query .= " -".$nsearch_term;

}

//echo $query."<br>".urlencode($query);

$strUrl = 'http://www.avantlink.com/api.php';
$strUrl .= "?affiliate_id=$affiliate_id";
$strUrl .= "&module=ProductSearch";
$strUrl .= "&output=" . urlencode('xml');
$strUrl .= "&website_id=11193";
$strUrl .= "&search_term=" . urlencode($query);
$strUrl .= "&search_advanced_syntax=1";
$strUrl .= "&search_results_options=" . urlencode('nofollow');
$strUrl .= "&custom_tracking_code=" . urlencode(wp_related_products);

if($disabled == false) {

// Make the actual API request
$xmlStr = file_get_contents($strUrl);

//parse xml into array
$xmlObj = simplexml_load_string($xmlStr);
$arrXml = objectsIntoArray($xmlObj);

}

if($arrXml['Table1'][0] == '') { $empty = true; }

//print_r($arrXml);

$output .= '<div id="related_products">';
$output .= '<div id="rp_title"><h3>'.$title.'</h3></div>';

if($num_results > 10) { $num_results = 10; }
if(!is_numeric($num_results)) { $num_results = 5; }

for($i = 0; $i < $num_results; $i++) {

$merchant_name = $arrXml['Table1'][$i]['Merchant_Name'];
$product_name = $arrXml['Table1'][$i]['Product_Name'];
$retail_price = $arrXml['Table1'][$i]['Retail_Price'];
$sale_price = $arrXml['Table1'][$i]['Sale_Price'];

$retail_price = substr($retail_price, 1);
$sale_price = substr($sale_price, 1);

if(!$empty) { $percent_off = round(100 * ($retail_price - $sale_price) / $retail_price); }
if($percent_off != 0) { $percent_off_styled = ' '.$percent_off.'% Off'; }

$thumbnail_image = $arrXml['Table1'][$i]['Large_Image'];

$pos = strpos($thumbnail_image, "?");
	
if($pos == true) { $pieces = explode('?', $thumbnail_image); $thumbnail_image = $pieces[0]; }

$buy_url =  $arrXml['Table1'][$i]['Buy_URL'].'_'.$author;

$output .= '<div class="rp_item">';

$output .= '<div class="rp_image"><a href="'.$buy_url.'" target="_blank"><img src='.$thumbnail_image.' /></a></div>';
$output .= '<div class="rp_name"><span><a href="'.$buy_url.'" target="_blank">'.$product_name.'</a></span></div>';
$output .= '<div class="prices"><a href="'.$buy_url.'" target="_blank"><span class="sale_price">$'.$sale_price.'</span><span class="percent_off">'.$percent_off_styled.'</span></a></div>';

$output .= '</div>';

unset($percent_off_styled);

}

$output .= '<div class="clear"></div>';

if($sponsor_link == 1) { $indent = ' style="text-indent: -99999px;height:1px;width:1px;padding:0px;" '; }

$output .= '<p id="link-share"'.$indent.'><a href="http://www.cleansnipe.com/?utm_source=wpRP&utm_medium=plugin&utm_campaign=wp_related">Powered by CleanSnipe</a></p>';

$output .= '</div>';

if($empty == true) { $output = '<p>No related products found</p>'; }
if($disabled == true) { $output = ''; }

echo $output;

}

function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
    $arrData = array();
    
    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }
    
    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}

?>