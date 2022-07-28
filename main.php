<?php
/*
Plugin Name: Membership Data
Plugin URI: https://www.adralberta.com/
Description: Displays a table of the memberships summary
Authors: HarinSBal
Version: 2.0
*/

require_once(dirname(__FILE__)."/exportSubmenuPage.php");

add_action('admin_menu', "add_admin_menu_item_membership_summary_plugin_hsb");

function add_admin_menu_item_membership_summary_plugin_hsb(){
    add_menu_page( 'WooMemberships', //page title
    'Membership Data', //menu title
    'manage_options', //capability
    'membership_summary_plugin_hsb', //parent slug
    'export_membership_data_submenu_page_hsb', //callback
    'dashicons-portfolio' //icon
    );   
    add_submenu_page( 'membership_summary_plugin_hsb', 
    'Export membership data',
    'Export Membership Data', 
    'manage_options', 
    'export_csv_submenuslug_hsb', 
    'export_membership_data_submenu_page_hsb'
    );
}

add_action('admin_enqueue_scripts', 'enqueue_style_table_page_hsb');
function enqueue_style_table_page_hsb($hook){
    if('toplevel_page_membership_summary_plugin_hsb' === $hook){
        wp_enqueue_style('table_styles_hsb', plugins_url("/css/tablehsb.css", __FILE__));
    }
}

add_action('admin_enqueue_scripts', 'enqueue_script_export_submenu_hsb');
function enqueue_script_export_submenu_hsb($hook){
    if('membership-data_page_export_csv_submenuslug_hsb' === $hook){
        wp_enqueue_script('quarterButtons_jsfile_hsb', plugins_url("js/quarterButtonshsb.js", __FILE__));
        wp_enqueue_style('export_styles_hsb', plugins_url("/css/exporthsb.css", __FILE__));
    }
}
 
function create_and_display_memberships_summary_table_hsb(){
    //if memberships are changed fix their names and ids here and below:
    //from memberpress->memberships tab: 
    $membership_ids_adria = array(0=>1914, 1=>3966, 2=>1915, 3=>1916, 4=>1917);
    $membership_names_adria = array(0=>'Associate', 1=>'Directory', 2=>'Full', 3=>'LINK', 4=>'Organizational');
    //woocommerce products ids for memberships. 
    //new
    $wc_product_ids_new_adria = '3138, 2574, 4220, 3151, 8511, 8517, 3632';
    //WooCommerce product ids mapped to the memberpress membership ids:
    $case_statement_new_adria = 'WHEN 4220 THEN 1917 WHEN 8511 THEN 3966 WHEN 8517 THEN 3966 WHEN 3632 THEN 3966 WHEN 3151 THEN 1916 WHEN 3138 THEN 1914 WHEN 2574 THEN 1915';
    //renewal
    $wc_product_ids_renewal_adria = '3148, 3143, 3050';
    //WooCommerce product ids mapped to the memberpress membership ids:
    $case_statement_renewal_adria = 'WHEN 3148 THEN 1916 WHEN 3143 THEN 1914 WHEN 3050 THEN 1915';

    display_membership_summary_table_hsb($membership_ids_adria,$membership_names_adria,$wc_product_ids_new_adria,$case_statement_new_adria,$wc_product_ids_renewal_adria,$case_statement_renewal_adria);
}

function export_membership_data_submenu_page_hsb() {
    //if memberships are changed fix their names and ids here, above and in the exportSbmenuPage.php file. In that file it is hardcoded to check for organizational memberships:
    $memberships_adria = array(1914=>'Associate', 3966=>'Directory', 1915=>'Full', 1916=>'LINK', 1917=>'Organizational');
    //wc product ids for each membership. New first and then the renewal.
    $memberships_wc_adria = array('Full'=>2574, 'LINK'=>3151, 'Organizational'=>4220, 'Associate'=>3138);

    render_export_submenu_page_html_hsb($memberships_adria, $memberships_wc_adria);
}

add_action('admin_init','membership_data_download_csv_hsb');
    
           
?>