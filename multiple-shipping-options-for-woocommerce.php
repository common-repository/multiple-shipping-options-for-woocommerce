<?php
/**
 * Plugin Name: Multiple Shipping Options for WooCommerce
 * Plugin URI: https://minilogics.com/multiple-shipping-options-for-woocommerce/
 * Description: Providing shipping rates on cart/checkout & WooCommerce orders, printable labels, packaging options, & multi-currency support. Free trial!
 * Version: 1.1.0
 * Author: Mini Logics
 * Author URI: https://minilogics.com/
 * Text Domain: mini-logics
 * WC requires at least: 8.6.0
 * WC tested up to: 8.6.1
 * License: GPL version 3 - https://www.minilogics.com/
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once 'vendor/autoload.php';

define('MSO_MAIN_DIR', __DIR__);
define('MSO_HITTING_URL', 'https://ws.minilogics.com/');
define('MSO_MAIN_FILE', __FILE__);
define('MSO_PLUGIN_URL', plugins_url());
define('MSO_DIR_FILE', plugin_dir_url(MSO_MAIN_FILE));

if (empty(\MsoPrerequisites\MsoPrerequisites::mso_check_prerequisites('Multiple Shipping Options for WooCommerce', '5.6', '5.7', '5.0'))) {
    require_once 'mso-install.php';
}