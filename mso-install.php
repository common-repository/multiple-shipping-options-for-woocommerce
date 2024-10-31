<?php

use MsoCsv\MsoCsv;

/**
 * install hook
 */
function mso_install()
{
    apply_filters('mso_activation_hook', false);
}

register_activation_hook(MSO_MAIN_FILE, 'mso_install');

/**
 * uninstall hook
 */
function mso_uninstall()
{
    apply_filters('company_name_deactivation_hook', false);
}

register_deactivation_hook(MSO_MAIN_FILE, 'mso_uninstall');


/**
 * init
 */
function mso_init()
{
    if (class_exists('SitePress')) {
        global $sitepress;

        // Get the current language
        $current_language = $sitepress->get_current_language();

        // Get the currency for the current language
        $currency = $sitepress->get_currency($current_language);

        if (!$currency) {
            $currency = get_woocommerce_currency();
        }

    } else {
        $currency = get_option('woocommerce_currency');
    }

    $timezone = get_option('timezone_string');
    if (empty($timezone)) {
        $timezone = date_default_timezone_get();
    }

    define('MSO_TIME_ZONE', $timezone);
    define('MSO_CURRENCY_CODE', $currency);
    define('MSO_CURRENCY_SYMBOL', get_woocommerce_currency_symbol($currency));
}

add_filter('init', 'mso_init');

/**
 * Custom error message
 * @param $message
 * @return string
 */
function mso_default_cart_error_message($message)
{
    $cart_error_message = apply_filters('mso_default_cart_error_message', '');
    strlen($cart_error_message) > 0 ? $message = $cart_error_message : '';
    return $message;
}

add_filter('woocommerce_cart_no_shipping_available_html', 'mso_default_cart_error_message', 9999999999, 1);

/**
 * Form template
 */
function mso_form_template($form_fields)
{
    $template = '<table class="form-table mso_table">';
    $template .= '<tbody>';
    foreach ($form_fields as $key => $form_field) {
        $name = $type = $default = $desc = $id = $class = $options = $tr_class = $value = $tooltip = '';
        extract($form_field);

        // Label for
        $template .= '<tr valign="top" class="' . $tr_class . '">';
        $template .= '<th scope="row" class="titledesc">';
        $template .= '<label for="' . $name . '">' . $name . '</label>';
        $template .= $tooltip;
        $template .= '</th>';

        // Form type
        switch ($type) {

            case 'title':
                $template .= '<td class="forminp forminp-text">';
                $template .= '<p class="description">' . $desc . '</p>';
                $template .= '</td>';
                break;

            case 'select':
                $template .= '<td class="forminp forminp-select">';
                $template .= '<select name="' . $id . '" class="' . $id . '">';
                foreach ($options as $option_id => $option) {
                    $selected = $option_id == $default ? 'selected="selected"' : '';
                    $template .= '<option value="' . $option_id . '" ' . $selected . '>' . $option . '</option>';
                }
                $template .= '</select>';
                $template .= $id == 'mso_order_shipment_origin' ? '<span class="description">' . $desc . '</span>' : '<p class="description">' . $desc . '</p>';
                $template .= '</td>';
                break;

            case 'checkbox':
                $checked = $default == 'yes' ? 'checked="checked"' : '';
                $template .= '<td class="forminp forminp-checkbox">';
                $template .= '<input name="' . $id . '" id="' . $id . '" type="checkbox" class="' . $class . '" ' . $checked . '>';
                $template .= '<span class="description">' . $desc . '</span>';
                $template .= '</td>';
                break;

            case 'radio':
                $checked = $default == 'yes' ? 'checked="checked"' : '';
                $template .= '<td class="forminp forminp-checkbox">';
                $template .= '<input value = "' . $value . '" name="' . $id . '" id="' . $id . '" type="radio" class="' . $class . '" ' . $checked . '>';
                $template .= '<span class="description">' . $desc . '</span>';
                $template .= '</td>';
                break;

            case 'shipping_order_radio':
                $template .= '<td class="forminp forminp-radio">';
                foreach ($options as $option_id => $option) {
                    $template .= '<input type="radio" name="' . $id . '" id="' . $option_id . '">';
                    $template .= '<label for="' . $option_id . '">' . $option['label'] . ':  ' . get_woocommerce_currency_symbol() . $option['cost'] . '</label><br>';
                }
                $template .= '</td>';
                break;

            case 'text':
                $template .= '<td class="forminp forminp-text">';
                $template .= '<input name="' . $id . '" id="' . $id . '" type="text" class="' . $class . '">';
                $template .= '<p class="description">' . $desc . '</p>';
                $template .= '</td>';
                break;
        }

        $template .= '</tr>';
    }
    $template .= '</tbody>';
    $template .= '</table>';

    return $template;
}

add_filter('mso_form_template', 'mso_form_template', 10, 1);

/**
 * Load tab file
 * @param $settings
 * @return array
 */
function mso_settings_pages($settings)
{
    $settings[] = include('admin/tab/tab.php');
    return $settings;
}

add_filter('woocommerce_get_settings_pages', 'mso_settings_pages');

/**
 * Show action links on plugins page
 * @param $actions
 * @param $plugin_file
 * @return array
 */
function mso_action_links($actions, $plugin_file)
{
    static $plugin;
    if (!isset($plugin)) {
        $plugin = plugin_basename(MSO_MAIN_FILE);
    }

    if ($plugin == $plugin_file) {
        $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=mso">' . __('Settings', 'General') . '</a>');
        $site_link = array('support' => '<a href="https://minilogics.com" target="_blank">Support</a>');
        $actions = array_merge($settings, $actions);
        $actions = array_merge($site_link, $actions);
    }

    return $actions;
}

add_filter('plugin_action_links', 'mso_action_links', 10, 2);

/**
 * Mso admin load admin side files of css and js hook
 */
function mso_admin_enqueue_scripts()
{
    // css
    wp_register_style('mso_admin_style', MSO_DIR_FILE . '/admin/assets/css/admin.css', false, '1.0.2');
    wp_enqueue_style('mso_admin_style');

    // default bootstrap css library
//    wp_register_style('mso_bootstrap_iso', MSO_DIR_FILE . '/admin/assets/css/mso-bootstrap.css', false, '1.0.0');
//    wp_enqueue_style('mso_bootstrap_iso');

    // Print label css library
    wp_register_style('mso_print_style', MSO_DIR_FILE . '/admin/assets/css/print.css', false, '1.0.2');
    wp_enqueue_style('mso_print_style');

    // JTV css library
    wp_register_style('mso_jtv_style', MSO_DIR_FILE . '/admin/assets/css/jtv.css', false, '1.0.2');
    wp_enqueue_style('mso_jtv_style');

    // js
    wp_enqueue_script('mso_admin_script', MSO_DIR_FILE . '/admin/assets/js/admin.js', [], '1.0.2');
    wp_localize_script('mso_admin_script', 'mso_script', [
        'mso_paid_plan_feature' => MSO_PAID_PLAN_FEATURE_DIALOG,
//        'mso_url' => MSO_PLUGIN_URL,
//        'mso_fedex_sqp' => get_option('mso_fedex_sqp'),
//        'mso_ups_sqp' => get_option('mso_ups_sqp'),
//        'mso_fedex_lfq' => get_option('mso_fedex_lfq'),
//        'mso_ups_lfq' => get_option('mso_ups_lfq')
    ]);

    // Print label js library
    wp_enqueue_script('mso_print_script', MSO_DIR_FILE . '/admin/assets/js/print.js', ['jquery'], '1.0.2');
    wp_localize_script('mso_print_script', 'mso_print_script', []);

    // JTV js library
    wp_enqueue_script('mso_jtv_script', MSO_DIR_FILE . '/admin/assets/js/jtv.js', ['jquery'], '1.0.2');
    wp_localize_script('mso_jtv_script', 'mso_jtv_script', []);
}

add_action('admin_enqueue_scripts', 'mso_admin_enqueue_scripts');


/**
 * Mso frontend load side files of css and js hook
 */
function mso_frontend_enqueue_scripts()
{
    // js
    wp_enqueue_script('mso_frontend_script', MSO_DIR_FILE . '/shipping/checkout/assets/js/frontend.js', ['jquery'], '1.0.1');
    wp_localize_script('mso_frontend_script', 'mso_script', []);
}

add_action('wp_enqueue_scripts', 'mso_frontend_enqueue_scripts');

/**
 * Mso method in woo method list
 * @param $methods
 * @return string
 */
function mso_add_shipping_method($methods)
{
    $methods['mso'] = 'MsoShipping';
    return $methods;
}

add_filter('woocommerce_shipping_methods', 'mso_add_shipping_method', 10, 1);

/**
 * Get Host
 * @param type $url
 * @return type
 */
function mso_get_host($url)
{
    $parse_url = parse_url(trim($url));
    if (isset($parse_url['host'])) {
        $host = $parse_url['host'];
    } else {
        $path = explode('/', $parse_url['path']);
        $host = $path[0];
    }
    return trim($host);
}

/**
 * Add shipping zone, shipping method
 */
function mso_aszsm()
{
    $shipping_method_mso = false;
    $shop_country = WC()->countries->get_base_country();
    if (class_exists('WC_Shipping_Zones')) {
        $shipping_zones = new WC_Shipping_Zones();
        $get_zones = $shipping_zones::get_zones();
        foreach ($get_zones as $key => $get_zone) {
            $zone_id = isset($get_zone['id']) ? $get_zone['id'] : 0;
            $zone_locations = isset($get_zone['zone_locations']) ? $get_zone['zone_locations'] : [];
            $shipping_methods = isset($get_zone['shipping_methods']) ? $get_zone['shipping_methods'] : [];
            foreach ($shipping_methods as $key => $shipping_method) {
                $shipping_method_id = isset($shipping_method->id) ? $shipping_method->id : '';
                $shipping_method_id == 'mso' ? $shipping_method_mso = true : '';
            }

            if (!$shipping_method_mso) {
                foreach ($zone_locations as $key => $zone_location) {
                    $type = isset($zone_location->type) ? $zone_location->type : '';
                    $code = isset($zone_location->code) ? $zone_location->code : '';
                    if ($type == 'country' && strtolower($shop_country) == strtolower($code)) {
                        $shipping_method_mso = true;
                        $zone = $shipping_zones::get_zone($zone_id);
                        $zone->add_shipping_method('mso');
                        $zone->save();
                        continue;
                    }
                }
            }
        }
    }

    if (!$shipping_method_mso && class_exists('WC_Shipping_Zone')) {
        $shipping_zone = new WC_Shipping_Zone();
        $shipping_zone->set_zone_name($shop_country);
        $shipping_zone->set_locations([[
            'code' => $shop_country,
            'type' => 'country'
        ]]);
        $shipping_zone->add_shipping_method('mso');
        $shipping_zone->save();
    }
}

add_filter('mso_activation_hook', 'mso_aszsm');

/**
 * Create shipments table if not exists.
 */
function mso_create_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'mso_shipments';

    // Check if the table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

        $charset_collate = $wpdb->get_charset_collate();

        // SQL to create the table
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            order_id int(11) DEFAULT NULL,
            status varchar(20) DEFAULT NULL,
            `key` varchar(50) DEFAULT NULL,
            response longtext DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}


add_filter('mso_activation_hook', 'mso_create_table');

/**
 * Get the last shipment for a specific order ID
 */
function mso_get_shipment_by_order_id($order_id)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'mso_shipments';

    // Check if the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        // Table doesn't exist, create it
        mso_create_table();
    }

    // Perform the SELECT query
    $response = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT response FROM $table_name WHERE order_id = %d ORDER BY id DESC LIMIT 1",
            $order_id
        )
    );

    return $response;
}

/**
 * Insert shipment for a specific order ID
 */
function mso_insert_shipment($order_id, $status, $key, $response)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'mso_shipments';

    // Check if the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        // Table doesn't exist, create it
        mso_create_table();
    }

    // Insert the record
    $result = $wpdb->insert(
        $table_name,
        array(
            'order_id' => $order_id,
            'status' => $status,
            'key' => $key,
            'response' => $response,
        )
    );

    return $result !== false;
}

/**
 * Parsed build query
 */
if (!function_exists('mso_parsing_build_query')) {
    function mso_parsing_build_query($build_query)
    {
        $parsed_arr = [];
        parse_str(trim($build_query), $parsed_arr);
        return $parsed_arr;
    }
}

// Receive request for update plan
add_action('rest_api_init', function () {
    register_rest_route('mso', '/v1', [
        'methods' => 'GET',
        'callback' => 'msoup',
        'permission_callback' => '__return_true'
    ]);

    // Update plan
    function msoup($request)
    {
        if (isset($request['key'], $request['domain'])) {
            $post_data = [
                'mso_key' => $request['key'],
                'domain' => $request['domain'],
                'mso_type' => 'key'
            ];
            $url = MSO_HITTING_URL . 'key.php';
            $wasaio_curl = new \WasaioCurl\WasaioCurl();
            $mso_api_results = json_decode($wasaio_curl::wasaio_http_request($url, $post_data), true);
            if (isset($mso_api_results['severity'], $mso_api_results['message'])) {
                $severity = $mso_api_results['severity'];
                $style_color = $show_status = '';
                $subscriptions = [];
                switch ($severity) {
                    case 'error':
                        $action = false;
                        $style_color = 'red';
                        $show_status = 'Error';
                        break;
                    case 'success':
                        $action = true;
                        $style_color = 'green';
                        $show_status = 'Success';
                        $subscriptions = $mso_api_results['subscriptions'];
                        break;
                }

                $message = '<span style="color: ' . $style_color . ';"><b>' . $show_status . '! </b> ' . $mso_api_results['message'] . '</span>';
                update_option('mso_key_status', $message);
                update_option('mso_key_direction', $severity);
                update_option('mso_key_subscriptions', json_encode($subscriptions));
            }
        }
    }
});

/**
 * Check API connection status
 */
function mso_cfas($status)
{
    return isset($status) && is_string($status) && strlen($status) > 0 ? $status : '<span style="color: black;">To see the updated status, please click on the "Test Connection" button.</span>';
}

/**
 * Implode carrier
 */
function mso_implode_carriers($carriers)
{
    $carrier_str = '';
    foreach ($carriers as $key => $carrier) {
        $carrier_name = "<span class='mso_implode_carrier'>$carrier</span>";
        $carrier_str .= strlen($carrier_str) > 0 ? ", $carrier_name" : $carrier_name;
    }
    return $carrier_str;
}

/**
 * Store shop address
 */
function mso_store_shop_address()
{
    $mso_store_origin_address = mso_store_origin_address();
    if (!empty($mso_store_origin_address)) {
        return $mso_store_origin_address;
    }
    $mso_state = $mso_country = '';
    $country_state = explode(':', get_option('woocommerce_default_country'));
    $country_state_count = count($country_state);
    switch ($country_state_count) {
        case 1:
            $mso_state = isset($country_state[0]) ? $country_state[0] : '';
            break;
        case 2:
            $mso_country = isset($country_state[0]) ? $country_state[0] : '';
            $mso_state = isset($country_state[1]) ? $country_state[1] : '';
            break;
    }

    return [
        'mso_city' => get_option('woocommerce_store_city'),
        'mso_state' => $mso_state,
        'mso_zip' => get_option('woocommerce_store_postcode'),
        'mso_country' => $mso_country,
        'address_1' => get_option('woocommerce_store_address'),
        'address_2' => get_option('woocommerce_store_address_2'),
    ];
}

/**
 * Origin address
 */
function mso_store_origin_address()
{
    $origin_address =
        [
            'address_1' => 'mso_origin_address_1',
            'address_2' => 'mso_origin_address_2',
            'mso_city' => 'mso_origin_city',
            'mso_state' => 'mso_origin_state',
            'mso_zip' => 'mso_origin_zipcode',
            'mso_country' => 'mso_origin_country'
        ];

    foreach ($origin_address as $key => $option) {
        $value = get_option($option);
        if (strlen($value) > 0 || $key == 'address_2') {
            $origin_address[$key] = $value;
        } else {
            return [];
        }
    }

    return $origin_address;
}

/**
 * Label Specifications
 */
function mso_label_specifications($origin)
{
    if (!empty($origin) && is_array($origin)) {
        $origin['company_name'] = get_option('mso_company_name');
        $origin['attention_name'] = get_option('mso_attention_name');
        $origin['phone_number'] = get_option('mso_phone_number');
        $origin['tax_identification_number'] = get_option('mso_tax_identify_number');
    }

    return $origin;
}

/**
 * Force carriers
 */
function mso_force_carriers($mso_carrier_get)
{
    return (MSO_PLAN_STATUS !== 'success' || empty(MSO_SUBSCRIPTIONS) || !isset(MSO_SUBSCRIPTIONS[$mso_carrier_get])) && !MSO_DONT_AUTH;
}

/**
 * Validate numeric
 */
function mso_vn($numeric)
{
    $is_numeric = 0;
    if (is_numeric($numeric) && $numeric > 0) {
        $is_numeric = $numeric;
    }

    return $is_numeric;
}

/**
 * Get Domain Name
 */
function mso_get_server_name()
{
    global $wp;
    $wp_request = (isset($wp->request)) ? $wp->request : '';
    $url = home_url($wp_request);
    return mso_get_host($url);
}

/**
 * Plan status notification
 */
function mso_plan_status($mso_carrier_get)
{
    $plan_status = '';
    if (!MSO_DONT_AUTH) {
        $plan_status = 'Your currently plan subscription is inactive. ' . MSO_SUBS_CLICK_HERE . ' to start the trial plan.';
        if (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[$mso_carrier_get])) {
            $plan_detail = MSO_SUBSCRIPTIONS[$mso_carrier_get];
            $carrier = $status = $plan_active = $current_period_end = '';
            $attributes = [];
            extract($plan_detail);
            $activated = false;

            if ($plan_active == 'yes') {
                if (!empty($attributes)) {
                    $types = [
                        't' => '',
                        'p1' => 'Shipping Rates on the Cart/Checkout Page',
                        'p2' => 'Shipping Rates on the Admin Order Page',
                        'p3' => 'Shipping Label including Generate Label and Print Label',
                    ];

                    foreach ($attributes as $type => $attribute_detail) {
                        if (isset($types[$type])) {
                            $attr_plan_active = isset($attribute_detail['plan_active']) ? $attribute_detail['plan_active'] : '';
                            $attr_status = isset($attribute_detail['status']) ? $attribute_detail['status'] : '';
                            if ($attr_plan_active == 'yes') {
                                $attr_current_period_end = isset($attribute_detail['current_period_end']) ? $attribute_detail['current_period_end'] : '';

                                $label = $types[$type];
//                                $attributes_plan_status .= ' - ';
                                $attributes_plan_links = '';
                                switch ($attr_status) {
                                    case 'active':
                                        $activated = true;
//                                        $attributes_plan_status .= $types['p1'] . " trial plan will expire on $attr_current_period_end.<br>";
//                                        $attributes_plan_status .= " - " . $types['p2'] . " trial plan will expire on $attr_current_period_end.<br>";
//                                        $attributes_plan_status .= " - " . $types['p3'] . " trial plan will expire on $attr_current_period_end.";

                                        $attributes_plan_links .= $types['p1'] . " trial plan will expire on $attr_current_period_end.<br> - ";
                                        $attributes_plan_links .= $types['p2'] . " trial plan will expire on $attr_current_period_end.<br> - ";
                                        $attributes_plan_links .= $types['p3'] . " trial plan will expire on $attr_current_period_end.";

                                        break;
                                        $activated = true;
                                    case 'unsubscription':
                                        $attributes_plan_links .= "$label plan will be active until $attr_current_period_end";
                                        break;
                                    case 'success':
                                        $activated = true;
                                        $attributes_plan_links .= "$label plan will be renewed on $attr_current_period_end";
                                        break;
                                }

//                                $attributes_plan_status .= '<br>';
                                $attributes_plan_status .= strlen($attributes_plan_links) > 0 ? ' - ' . $attributes_plan_links . '<br>' : '';
                            }
                        }
                    }
                }

                if ($activated) {
                    $plan_status = "Your $carrier subscription details: <br>$attributes_plan_status";
                } else {
                    $plan_status = "Your $carrier plan subscription is inactive. " . MSO_SUBS_CLICK_HERE . " to refresh the plan <br>";
                }

//                switch ($status) {
//                    case 'active':
//                        $plan_status = "Your $carrier trial plan will expire on $current_period_end.";
//                        break;
//                    case 'unsubscription':
//                        $plan_status = "Your $carrier plan will be active until $current_period_end";
//                        break;
//                    case 'success':
//                        $plan_status = "Your $carrier plan will be renewed on $current_period_end";
//                        break;
//                    default:
//                        $plan_status = "Your $carrier plan subscription is inactive. " . MSO_SUBS_CLICK_HERE . " to refresh the plan";
//                        break;
//                }
            } else {
                $plan_status = "Your $carrier plan subscription is inactive. " . MSO_SUBS_CLICK_HERE . " to refresh the plan <br>";
            }
        }

        $plan_status = '<span class="notice notice-warning mso_plan_status_warning">' . $plan_status . ' - <a class="mso_get_updated_plan_status">Get Updated Plan Status</a> </span>';
    }

    return $plan_status;
}

/**
 * Subscription plan status
 */
function mso_get_carriers_plan_status($plan_type = '')
{
    if (MSO_DONT_AUTH) {
        return;
    }

    $plan_status = MSO_INACTIVE_PLAN_MESSAGE;
    if (!empty(MSO_SUBSCRIPTIONS)) {
        $carriers = [];
        foreach (MSO_SUBSCRIPTIONS as $key => $subscription) {
//            if ($p3) {
////                $subscription = isset($subscription['attributes'], $subscription['attributes']['p3']) ? $subscription['attributes']['p3'] : [];
//                $subscription = isset($subscription['attributes']['p3']) ? $subscription['attributes']['p3'] : (isset($subscription['attributes']['t']) ? $subscription['attributes']['t'] : []);
//            }

            if (strlen($plan_type) > 0) {
                $subscription = isset($subscription['attributes'][$plan_type]) ?
                    $subscription['attributes'][$plan_type] :
                    (isset($subscription['attributes']['t']) ? $subscription['attributes']['t'] : []);
            }


            $carrier = $plan_active = '';
            extract($subscription);
            if ($plan_active == 'yes') {
                $carriers[] = $carrier;
            }
        }
//            $mso_subscription_status = "Please note that the following feature is only available for your paid carriers, such as " . mso_implode_carriers($carriers) . ", will be effective in controlling order shipments and allowing for packages.";
//        $plan_status = sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers));

        if (!empty($carriers)) {
            $plan_status = sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers));

            $types = [
                'p2' => 'Shipping Rates on the Admin Order Page',
                'p3' => 'Shipping Label including Generate Label and Print Label',
            ];
            if (strlen($plan_type) > 0 && isset($types[$plan_type])) {
                $plan_status = "<span class='mso_order_plan_status'>{$types[$plan_type]}</span>" . $plan_status;
            }
        }

//            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>it is only possible to monitor the shipment of orders through your paid carriers such as " . mso_implode_carriers($carriers) . ".</p></div>" : '';
//            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>" . sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers)) . "</p></div>" : '';
    }

    return '<span class="notice notice-warning mso_plan_status_warning">' . $plan_status . '</span>';
}

// Define server name
define('MSO_SERVER_NAME', mso_get_server_name());
define('MSO_SERVER_KEY', trim(get_option('mso_paid_key')));

// Don't auth for specific stores
$dont_auth_action = false;
$dont_auth_store = ['baitplastics.com','baitplastics.co.uk'];
if (in_array(MSO_SERVER_NAME, $dont_auth_store)) {
    $dont_auth_action = true;
}

define('MSO_DONT_AUTH', $dont_auth_action);

// Set pdf icon  
define('MSO_PDF_ICON', MSO_DIR_FILE . '/images/pdf.png');

// Define plan status
define('MSO_SUBS_LINK', '<a target="_blank" href="https://minilogics.com/subscription">Mini Logics</a>');
define('MSO_SUBS_CLICK_HERE', '<a target="_blank" href="https://minilogics.com/subscription">Click here</a>');
//define('MSO_PAID_PLAN_REQUIRE_SINGLE_CARRIER', 'The following features are paid; you need to purchase at least one carrier subscription from ' . MSO_SUBS_LINK . '.');
//define('MSO_PAID_PLAN_FEATURE', 'The following features are paid; you need to purchase a desired carrier subscription from ' . MSO_SUBS_LINK . ' in order to use their services.');
define('MSO_PAID_PLAN_FEATURE_DIALOG', 'This particular feature requires a paid subscription. To utilize this service, please purchase the desired carrier subscription from Mini Logics.');
//define('MSO_PAID_PLAN_MESSAGE', 'Your current subscription is active for %s; ' . MSO_SUBS_CLICK_HERE . ' to manage your subscriptions.');

$body = [
    'action' => 'mso_test_connection',
    'mso_carrier_id' => 'mso_paid_key',
    'mso_post_data' => [
        [
            'name' => 'mso_paid_key',
            'value' => MSO_SERVER_KEY
        ]
    ],
    'loader_id' => 'body'
];

// Define messages
$encoded_body = htmlspecialchars(json_encode($body), ENT_QUOTES, 'UTF-8');
$paid_plan_message = sprintf(
    'Your current subscription is active for %s; %s to manage your subscriptions. - <a style="cursor: pointer;" onclick="mso_get_updated_plan_status(%s)">Get Updated Plan Status</a>',
    '%s', // Placeholder for subscription duration
    MSO_SUBS_CLICK_HERE,
    $encoded_body
);
$inactive_plan_message = sprintf(
    'Your current plan subscription is inactive. %s to start the trial plan. - <a style="cursor: pointer;" onclick="mso_get_updated_plan_status(%s)">Get Updated Plan Status</a>',
    MSO_SUBS_CLICK_HERE,
    $encoded_body
);

// Define constants
define('MSO_PAID_PLAN_MESSAGE', $paid_plan_message);
define('MSO_INACTIVE_PLAN_MESSAGE', $inactive_plan_message);

//define('MSO_KEY_ERROR', '<span style="color: red;"><b>Error! </b>Please make sure to synchronize the plugin with ' . MSO_SUBS_LINK . ' by entering the correct MSO key for authorizations at the top of the plugin settings page.</span>');
define('MSO_PALLET_DESC', 'Pallets are used to identify a pallet solution before obtaining shipping rate estimates and are part of the pallet process available for all LTL Freight Shipping carriers.');
define('MSO_BOXES_DESC', 'Boxes are used to identify a packaging solution before obtaining shipping rate estimates and are part of the packaging process available for all Small Package Shipping carriers.');
define('MSO_ADD_SHIPMENT_DESC', 'Add Shipment button to generate a new shipment for additional products in the same order from a distinct origin. To add products, employ the default WooCommerce method. Modify the order status to Pending Payment, update the order page, and then utilize the "Add Item(s)" button.');
define('MSO_ITEMS_TOOLTIP', 'Through the items section, you can conveniently drag and drop items between multiple shipments. To include additional products in the same order from different origins, use the default WooCommerce method. Change the order status to "Pending Payment," update the order page, and then utilize the "Add Item(s)" button.');
define('MSO_DEST_ADDRESS_TOOLTIP', 'The indicated address is the shipment destination. Additionally, you can modify it to align with both the billing and shipping addresses located at the top of the same order page. Simply edit the address and update the order accordingly.');
define('MSO_ORIG_ADDRESS_TOOLTIP', "The indicated address is the shipment origin. Furthermore, you can add a new origin by using the 'Edit Origins' link, and then easily select it from the dropdown menu.");
define('MSO_ACCESSO_ADDRESS_TOOLTIP', "<h4>Residential:</h4> Applies to Small Package Shipping for UPS, FedEx, and LTL Freight Shipping. <h4>Liftgate:</h4> Specifically applicable for LTL Freight Shipping.");
define('MSO_ENABLE_DESC', 'Enable This Shipping Service');
define('MSO_AUTHORIZATION_KEY_DESC', "Obtain your authorization key from the subscription page on <a href='https://minilogics.com/subscription'>Mini Logics</a>. If you haven't registered yet, please use the <a href='https://minilogics.com/wp-login.php?action=register'>registration link</a>");

//define('MSO_PLAN_DESC', 'Upgrade to access premium features by visiting our website, <a href="https://minilogics.com">Mini Logics</a>, and creating a subscription.');
//define('MSO_BELOW_PLAN_DESC', 'To access the premium features below, please upgrade by visiting our website, <a href="https://minilogics.com">Mini Logics</a>, and creating a subscription.');
//define('MSO_ONE_PLAN_DESC', 'To access all the features below, you are required to have a minimum of one paid subscription by visiting our website, <a href="https://minilogics.com">Mini Logics</a>, and creating an account.');
define('MSO_PLAN_STATUS', get_option('mso_key_direction'));
define('MSO_KEY_STATUS', get_option('mso_key_status'));
$mks = get_option('mso_key_subscriptions');
define('MSO_SUBSCRIPTIONS', isset($mks) && strlen($mks) > 0 && $mks != NULL ? json_decode($mks, true) : []);

// Carrier Id's
define('MSO_UPS_GET', '1');
define('MSO_FEDEX_GET', '2');
define('MSO_UPS_FREIGHT_GET', '3');
define('MSO_FEDEX_FREIGHT_GET', '4');
define('MSO_USPS_GET', '5');
define('MSO_DHL_GET', '6');

// Woocommerce shipping init
new MsoShippingInit();

// Packaging
new \MsoPackagingAjax\MsoPackagingAjax();

// Order page
new MsoOrder\MsoOrder();

// Carrier settings ajax
new \MsoSettingsAjax\MsoSettingsAjax();

// Shipping settings
new \ShippingSettings\ShippingSettings();

// Ups Carrier list
//new \MsoUpsCarriers\MsoUpsCarriers();
//
//// Fedex Carrier list
//new \MsoFedexCarriers\MsoFedexCarriers();

// Product detail page
$mso_product_obj = new \MsoProductDetail\MsoProductDetail();

// CSV import/export
require_once __DIR__ . '/admin/csv/csv.php';
new MsoCsv($mso_product_obj->mso_locations());