<?php

/**
 * Plugin Name: Products Fetcher for WHMCS
 * Description: This plugin will fetch Data feeds from WHMCS.
 * Version: 1.0.0
 * Author: Devbunch
 * Author URI: https://www.devbunch.com/
 */

add_action('admin_menu', 'pf_whmcs_register_submenu_page');
add_action('admin_enqueue_scripts', 'pf_whmcs_enqueue_admin_style');
add_action('wp_enqueue_scripts', 'pf_whmcs_user_scripts');

function pf_whmcs_register_submenu_page()
{
    add_submenu_page('options-general.php', 'Products Fetcher for WHMCS', 'Products Fetcher for WHMCS', 'manage_options', 'wp-products-fetcher-whmcs', 'dashboard_menu_page');
}

function dashboard_menu_page()
{
    return include dirname(__FILE__) . '/admin/index.php';
}

function pf_whmcs_user_scripts()
{
    wp_enqueue_style('style_css', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('front_script_js', plugin_dir_url(__FILE__) . 'assets/js/front_script.js', array(), '1.0.0', true);
}

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function pf_whmcs_enqueue_admin_style()
{
    wp_enqueue_style('admin_style_css', plugin_dir_url(__FILE__) . 'assets/css/admin_style.css');
    wp_enqueue_script('admin_script_js', plugin_dir_url(__FILE__) . 'assets/js/admin_script.js', array(), '1.0.1', true);
    wp_localize_script('admin_script_js', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
// function that runs when shortcode whmcs_details is called
function pf_whmcs_get_whmcs_details($params)
{
    $base_url = get_option('whmcs_url', '');
    if ($base_url == '') {
        return 'Please Provide WHMCS URL before using Plugin. <br>';
    }
    if (array_key_exists('tld', $params) && array_key_exists('type', $params) && array_key_exists('register', $params)) {
        // it means price is for domain
        $end_point = '/feeds/domainprice.php?tld=' . $params['tld'] . '&type=' . $params['type'] . '&regperiod=' . $params['register'] . '&format=1';
        $end_point .= '&format=' . isset($params['format']) && $params['format'] ? $params['format'] : 1;
    } elseif (array_key_exists('pid', $params)) {
        $fetch = isset($params['fetch']) && $params['fetch'] != '' ? $params['fetch'] : 'price';
        $end_point = '/feeds/productsinfo.php?pid=' . $params['pid'] . '&get=' . $fetch . '&billingcycle=' . $params['bc'];
        // if price is being fetch then also specify price unit
        if ($fetch == 'price') {
            $end_point .= urldecode("&currency=" . $params['price_unit']);
        }
    } else {
        return 'Invalid values Provided. <br>';
    }
    $final_url = $base_url . $end_point;
    $result = wp_remote_post($final_url);
    $response = isset($result['body']) ? $result['body'] : 'No Response from External Server';
    $response = esc_html(pf_whmcs_get_string_between($response, "document.write('", "');"));
    echo htmlspecialchars_decode($response, ENT_QUOTES);
}

// function that runs when shortcode is called
function pf_whmcs_get_whmcs_price_table()
{
    $base_url = get_option('whmcs_url', '');
    if ($base_url == '') {
        return 'Please Provide WHMCS URL before using Plugin. <br>';
    }
    $final_url = $base_url . '/feeds/domainpricing.php';
    $result = wp_remote_post($final_url);
    $response = isset($result['body']) ? $result['body'] : 'No Response from External Server';
    $response = esc_html(pf_whmcs_get_string_between($response, "document.write('", "');"));
    echo '
    <style type="text/css">
        table.domainpricing {
            width: 600px;
            background-color: #ccc;
        }
        table.domainpricing th {
            padding: 3px;
            background-color: #efefef;
            font-weight: bold;
        }
        table.domainpricing td {
            padding: 3px;
            background-color: #fff;
            text-align: center;
        }
    </style>' . htmlspecialchars_decode($response, ENT_QUOTES);
}
// function that runs when shortcode is called
function pf_whmcs_whmcs_domain_checker()
{
    $base_url = get_option('whmcs_url', '');
    if ($base_url == '') {
        return 'Please Provide WHMCS URL before using Plugin. <br>';
    }
    $final_url = $base_url . '/feeds/domainchecker.php';
    $result = wp_remote_post($final_url);
    $response = isset($result['body']) ? $result['body'] : 'No Response from External Server';
    $response = esc_html(pf_whmcs_get_string_between($response, "document.write('", "');"));
    echo htmlspecialchars_decode($response, ENT_QUOTES);
}

function pf_whmcs_get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {
        return '';
    }
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

// register shortcode
add_shortcode('whmcs_details', 'pf_whmcs_get_whmcs_details');
add_shortcode('whmcs_domain_pricing', 'pf_whmcs_get_whmcs_price_table');
add_shortcode('whmcs_domain_checker', 'pf_whmcs_whmcs_domain_checker');
