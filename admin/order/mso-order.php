<?php

namespace MsoOrder;

use MsoProductDetail\MsoProductDetail;
use MsoPackage\MsoPackage;
use WasaioCurl\WasaioCurl;

/**
 * Order show on admin side.
 * Class MsoOrder
 */
class MsoOrder
{
    public $mso_shipment_meta_k = '';
    public $mso_shipment_meta_v = [];
    public $mso_subscription_status = '';
    public $subscription_boolean = false;

    public function __construct()
    {
        add_action('woocommerce_order_actions', [$this, 'mso_order'], 10, 1);

        // Order get quotes
        add_action('wp_ajax_mso_shipment_order', [$this, 'mso_shipment_order_get_quotes']);

        // Order new shipment
        add_action('wp_ajax_mso_new_shipment', [$this, 'mso_shipment_order_new_shipment']);

        // Recreate shipment
        add_action('wp_ajax_mso_order_recreate_shipment_allowed', [$this, 'mso_order_recreate_shipment_allowed']);

        // Order create shipment
        add_action('wp_ajax_mso_shipment_order_placed', [$this, 'mso_shipment_order_ship']);

        // Order cancel shipment
        add_action('wp_ajax_mso_cancel_shipment_hook', [$this, 'mso_cancel_shipment_hook']);
    }

    // Cancel shipment
    public function mso_cancel_shipment_hook()
    {
        $post_data = [];
        $order_id = (isset($_POST['mso_order_id'])) ? sanitize_text_field($_POST['mso_order_id']) : '';
        $mso_ship_num = (isset($_POST['mso_ship_num'])) ? sanitize_text_field($_POST['mso_ship_num']) : '';
        $carrier = (isset($_POST['mso_carrier'])) ? sanitize_text_field($_POST['mso_carrier']) : [];
        $tracking_ids = (isset($_POST['mso_post_data'])) ? $this->mso_parsing_build_query($_POST['mso_post_data']) : [];

        $accessorials = MsoPackage::msofw_accessorials();
        $accessor = isset($accessorials[$carrier]) ? $accessorials[$carrier] : [];
        $func = 'mso_' . $carrier . '_request';
        $credentials = MsoPackage::$func([], $accessor);

        if (!empty($credentials)) {
            $post_data['credentials'] = isset($credentials['carriers']) ? reset($credentials['carriers']) : [];
            $post_data['carrier'] = $carrier;
            $post_data['tracking_ids'] = $tracking_ids;
            $post_data['domain'] = MSO_SERVER_NAME;
            $post_data['mso_key'] = MSO_SERVER_KEY;
            $post_data['api_test_mode'] = get_option('mso_api_test_mode');
            $post_data['mso_type'] = 'cancel';
            $url = MSO_HITTING_URL . 'index.php';
            $wasaio_http_request = WasaioCurl::wasaio_http_request($url, $post_data);

            $cancel_packages = json_decode($wasaio_http_request, true);
            if (!empty($cancel_packages)) {
                $mso_shipment_order_ship = [];
                $mso_shipment_order_ship_main = get_post_meta($order_id, 'mso_shipment_order_ship', true);
                if (isset($mso_shipment_order_ship_main) && strlen($mso_shipment_order_ship_main) > 0) {
                    $mso_shipment_order_ship = json_decode($mso_shipment_order_ship_main, true);
                }

                $remove = 0;
                $not_remove = 0;
                $proceed_to_remove = false;
                $error_messages = '';
                foreach ($cancel_packages as $key => $cancel_package) {
                    if (isset($cancel_package['success'])) {
                        $proceed_to_remove = true;
                        $remove++;
                    } elseif (isset($cancel_package['error'], $cancel_package['message'])) {
                        $error_message = '(' . $key + 1 . ') ' . $cancel_package['message'];
                        $error_messages .= strlen($error_messages) > 0 ? "\r\n $error_message" : $error_message;
                        $not_remove++;
                    }
                }

                $per_ind = $carrier . '_ship';
//                if ($proceed_to_remove && isset($mso_shipment_order_ship['shipments'], $mso_shipment_order_ship['shipments'][$mso_ship_num], $mso_shipment_order_ship['shipments'][$mso_ship_num][$per_ind])) {
//                    unset($mso_shipment_order_ship['shipments'][$mso_ship_num][$per_ind]);
//                    update_post_meta($order_id, 'mso_shipment_order_ship', trim(json_encode($mso_shipment_order_ship)));
//                }

                if ($proceed_to_remove && isset($mso_shipment_order_ship, $mso_shipment_order_ship[$mso_ship_num], $mso_shipment_order_ship[$mso_ship_num][$per_ind])) {
                    unset($mso_shipment_order_ship[$mso_ship_num][$per_ind]);
                    update_post_meta($order_id, 'mso_shipment_order_ship', trim(json_encode($mso_shipment_order_ship)));
                }

                $message = 'Please try again later';
                $action = 'error';
                if ($remove > 0 && $not_remove > 0) {
                    $action = 'note';
                    $message .= $remove . ' packages has been deleted but ' . $not_remove . ' packages not deleted please deal with them manually on the carrier portal';
                } elseif ($remove > 0) {
                    $action = 'success';
                    $message = 'Shipment deleted including ' . $remove . ' package';
                } elseif ($not_remove > 0) {
                    $action = 'error';
                    $message = strlen($error_messages) > 0 ? $error_messages : $message;
                }

                echo json_encode([
                    $action => true,
                    'message' => ucfirst($action) . '! ' . $message
                ]);
                die;
            }
        }
    }

    // Recreate shipment
    public function mso_order_recreate_shipment_allowed()
    {
        $order_id = (isset($_POST['mso_order_id'])) ? sanitize_text_field($_POST['mso_order_id']) : [];
        $mso_shipment_order_ship = get_post_meta($order_id, 'mso_shipment_order_ship', true);
        update_post_meta($order_id, 'mso_shipment_order_ship_backup', trim($mso_shipment_order_ship));
        delete_post_meta($order_id, 'mso_shipment_order_ship');
        exit;
    }

    /**
     * Order ship
     */
    public function mso_shipment_order_ship()
    {
        $shipments = $shipment_rates = [];
        $mso_post_data = (isset($_POST['mso_shipments'])) ? $_POST['mso_shipments'] : [];
        $ship_to = (isset($_POST['mso_ship_to_address'])) ? sanitize_text_field($_POST['mso_ship_to_address']) : [];
        $order_id = (isset($_POST['mso_order_id'])) ? sanitize_text_field($_POST['mso_order_id']) : 0;
        $carriers_rate = WC()->session->get('mso_cr_store');
        $carriers_rate = (isset($carriers_rate) && strlen($carriers_rate) > 0) ? json_decode($carriers_rate, true) : [];
        $order = wc_get_order($order_id);

        $mso_mswrflfq = get_option('mso_mswrflfq');
        $mso_min_weight = isset($mso_mswrflfq) && strlen($mso_mswrflfq) > 0 && is_numeric($mso_mswrflfq) ? $mso_mswrflfq : 150;

        $type = '';
        switch ($ship_to) {
            case 'mso_billing_address':
                // Billing address
                $ship_to = $order->get_address('billing');
                $type = 'billing';
                break;
            default:
                // Shipping address
                $ship_to = $order->get_address('shipping');
                $type = 'shipping';
                break;
        }

        $ship_to['type'] = $type;

        // Shipment disabled
        $mso_last_access = $this->mso_shipments_getting_data($order_id);

        // Locations
        $mso_product_detail = new MsoProductDetail();
        $locations = $mso_product_detail->mso_locations();
        $origin_id_list = [];

        foreach ($mso_post_data as $mso_ship_num => $shipment) {
            // Shipment disabled
            $non_ship_trigger = false;
            $enable_disable = isset($shipment['enable_disable']) ? $shipment['enable_disable'] : '';
//            if ($enable_disable == 'disabled') {
//                if (isset($mso_last_access['shipments'], $mso_last_access['shipments'][$mso_ship_num])) {
//                    $non_shipments[$mso_ship_num] = $mso_last_access['shipments'][$mso_ship_num];
//                    continue;
//                } elseif (isset($mso_last_access[$mso_ship_num])) {
//                    $non_shipments[$mso_ship_num] = $mso_last_access[$mso_ship_num];
//                    continue;
//                } else {
//                    $non_ship_trigger = true;
//                }
//            }

            if ($enable_disable == 'disabled' || !isset($shipment['selected_rate'])) {
                if (isset($mso_last_access[$mso_ship_num])) {
                    $non_shipments[$mso_ship_num] = $mso_last_access[$mso_ship_num];
                    continue;
                } else {
                    $non_ship_trigger = true;
                }
            }

            $mso_ship_num = sanitize_text_field($mso_ship_num);
            if (isset($shipment['origin'], $locations[$shipment['origin']])) {
                $shipment_origin = sanitize_text_field($shipment['origin']);
                $origin_id = $shipment_origin;
                $location = $locations[$shipment_origin];
                $mso_address = $mso_zip = $mso_city = $mso_state = $mso_country = '';
                extract($location);
                $origin = [
                    'id' => $origin_id,
                    'address' => $mso_address,
                    'address_1' => $mso_address,
                    'city' => $mso_city,
                    'postcode' => $mso_zip,
                    'state' => $mso_state,
                    'country' => $mso_country
                ];
            } else {
                $origin = MsoPackage::mso_shop_base_address();
            }

            $mso_zip = isset($origin['postcode']) ? $origin['postcode'] : '';
            $origin_id = isset($origin['id']) ? $origin['id'] : 0;
            $origin_id_list[$mso_zip] = $origin_id;

            $shipments[$mso_ship_num]['ship_from'] = $origin;

            if (isset($shipment['accessorials'])) {
                $accessorials = [
                    'mso_residential' => 'residential_delivery',
                    'mso_liftgate' => 'liftgate_delivery'
                ];

                foreach ($shipment['accessorials'] as $accessorial_key => $accessorial_value) {
                    (isset($accessorials[$accessorial_key])) ? $shipments[$mso_ship_num]['accessorials'][$accessorials[$accessorial_key]] = sanitize_text_field($accessorial_value) : '';
                }
            }

            $shipment_action = $ship_label = '';
            $ship_cost = 0;
            $packed_items = $rate = [];
            if (isset($shipment['selected_rate'])) {

                $selected_rate = [];
                parse_str($shipment['selected_rate'], $selected_rate);

                if (isset($selected_rate['carrier'])) {
                    $packed_items = (isset($selected_rate['response'], $selected_rate['response']['packed_items'])) ? $selected_rate['response']['packed_items'] : [];
                    $carrier = $ups_spq_code = $fedex_spq_service = $packaging_type = $fedex_lfq_service = '';
                    extract($selected_rate);
                    $ship_label = isset($selected_rate['label']) ? $selected_rate['label'] : '';
                    $ship_cost = isset($selected_rate['cost']) ? $selected_rate['cost'] : '';
                    switch ($selected_rate['carrier']) {
                        case 'fedex':
                            $shipment_action = 'spq';
                            $rate = [
                                'carrier' => $carrier,
                                'fedex_spq_service' => $fedex_spq_service,
                                'packaging_type' => $packaging_type
                            ];
                            break;
                        case 'ups':
                            $shipment_action = 'spq';
                            $rate = [
                                'carrier' => $carrier,
                                'ups_spq_code' => $ups_spq_code
                            ];
                            break;
                        case 'fedex_lfq':
                            $shipment_action = 'lfq';
                            $rate = [
                                'carrier' => $carrier,
                                'fedex_lfq_service' => $fedex_lfq_service
                            ];
                            break;
                        case 'ups_lfq':
                            $shipment_action = 'lfq';
                            $rate = [
                                'carrier' => $carrier,
                                'ups_lfq_code' => '308'
                            ];
                            break;
                    }
                }
            }

            if (isset($shipment['items'])) {
                foreach ($shipment['items'] as $product_id => $product_quantity) {
                    $product_id = sanitize_text_field($product_id);
                    $product_quantity = sanitize_text_field($product_quantity);
                    $item_data = wc_get_product($product_id);
                    $product_name = $item_data->get_title();
                    $product_price = $item_data->get_price();
                    // Product details
                    $product = wc_get_product($product_id);
                    $weight = wc_get_weight($product->get_weight(), 'lbs');
                    $height = wc_get_dimension($product->get_height(), 'in');
                    $width = wc_get_dimension($product->get_width(), 'in');
                    $length = wc_get_dimension($product->get_length(), 'in');

                    (!isset($shipments[$mso_ship_num]['ship_weight'])) ? $shipments[$mso_ship_num]['ship_weight'] = 0 : '';
                    $shipments[$mso_ship_num]['ship_weight'] += $weight;
                    $shipments[$mso_ship_num]['items'][$product_id] = [
                        'product_id' => $product_id,
                        'freight_class' => 60,
                        'quantity' => $product_quantity,
                        'title' => $product_name,
                        'weight' => $weight,
                        'height' => $height,
                        'width' => $width,
                        'length' => $length,
                        'price' => $product_price
                    ];

                    if ($shipments[$mso_ship_num]['ship_weight'] > $mso_min_weight) {
                        $shipments[$mso_ship_num]['action'] = strlen($shipment_action) > 0 ? $shipment_action : 'lfq';
                    }
                }
            }

            if (!empty($packed_items)) {
                $shipments[$mso_ship_num]['packed_items'] = $packed_items;
                (isset($shipments[$mso_ship_num]['ship_weight'])) ? $shipments[$mso_ship_num]['ship_weight'] = 0 : '';
                foreach ($packed_items as $bin_key => $bin) {
                    $weight = (isset($bin['weight'])) ? $bin['weight'] : 0;
                    (!isset($shipments[$mso_ship_num]['ship_weight'])) ? $shipments[$mso_ship_num]['ship_weight'] = 0 : '';
                    $shipments[$mso_ship_num]['ship_weight'] += $weight;

                    if ($shipments[$mso_ship_num]['ship_weight'] > $mso_min_weight) {
                        $shipments[$mso_ship_num]['action'] = strlen($shipment_action) > 0 ? $shipment_action : 'lfq';
                    }
                }
            }

            $shipments[$mso_ship_num]['rate'] = $rate;
            $shipments[$mso_ship_num]['label'] = $ship_label;
            $shipments[$mso_ship_num]['cost'] = $ship_cost;
            if (isset($shipment['service_error'])) {
                $shipments[$mso_ship_num]['service_error'] = $shipment['service_error'];
            }

            // Shipment disabled
            if ($non_ship_trigger) {
                $non_shipments[$mso_ship_num] = $shipments[$mso_ship_num];
                unset($shipments[$mso_ship_num]);
            }
        }

//        echo '<pre>';
//        print_r($shipments);
//        echo '</pre>'; die;
        $response = MsoPackage::mso_request_settings($shipments, $ship_to, 'ship');
//        echo $response;
        $getting_rates = json_decode($response, true);

        // TODO

        // Shipment disabled
//        if (!empty($non_shipments)) {
//            foreach ($non_shipments as $non_shipment_key => $non_shipment) {
//                $getting_rates['shipments'][$non_shipment_key] = $non_shipment;
//            }
//
//            $recreate_shipments = [];
//            foreach ($mso_post_data as $mso_post_data_k => $mso_post_data_v) {
//                isset($getting_rates['shipments'][$mso_post_data_k]) ? $recreate_shipments[$mso_post_data_k] = $getting_rates['shipments'][$mso_post_data_k] : '';
//            }
//
//            $getting_rates['shipments'] = $recreate_shipments;
//        }

        // Shipment disabled
        if (!empty($non_shipments)) {
            foreach ($non_shipments as $non_shipment_key => $non_shipment) {
                $getting_rates[$non_shipment_key] = $non_shipment;
            }

            $recreate_shipments = [];
            foreach ($mso_post_data as $mso_post_data_k => $mso_post_data_v) {
                isset($getting_rates[$mso_post_data_k]) ? $recreate_shipments[$mso_post_data_k] = $getting_rates[$mso_post_data_k] : '';
            }

            $getting_rates = $recreate_shipments;
        }

        update_post_meta($order_id, 'mso_shipment_order_ship', json_encode($getting_rates));

        echo $this->mso_order_shipping();
        die;
    }

    /**
     * Order get quotes
     */
    public function mso_shipment_order_new_shipment()
    {
        // Shipment disabled
        $order_id = (isset($_POST['mso_order_id'])) ? sanitize_text_field($_POST['mso_order_id']) : [];
        $this->mso_shipments_getting_data($order_id);
        if (empty($this->mso_shipment_meta_v)) {
            $order = wc_get_order($order_id);
            $shipping_details = $order->get_items('shipping');
            foreach ($shipping_details as $item_id => $shipping_item_obj) {
                $get_formatted_meta_data = $shipping_item_obj->get_formatted_meta_data();
                foreach ($get_formatted_meta_data as $key => $meta_data) {
                    switch ($meta_data->key) {
                        case 'mso_widget_detail':
                            $this->mso_shipment_meta_k = 'mso_shipment_order_arranged_by_customer';
                            $this->mso_shipment_meta_v = json_decode($meta_data->value, true);
                            break;
                    }
                }
            }
        }

        if (strlen($this->mso_shipment_meta_k) > 0 && !empty($this->mso_shipment_meta_v)) {
            $shipments = is_array($this->mso_shipment_meta_v) ? $this->mso_shipment_meta_v : [];
//            $shipments = isset($shipments['shipments']) ? $shipments['shipments'] : $shipments;
            if (!empty($shipments)) {
                $next_shipment = count($shipments) + 1;
                $shipments[$next_shipment] = [
                    'response' => [
                        'items' => [],
                        'ship_from' => []
                    ],
                    'accessorials' => []
                ];
//                $this->mso_shipment_meta_v['shipments'] = $shipments;
                $this->mso_shipment_meta_v = $shipments;
                $this->mso_shipment_meta_v = $this->mso_shipment_meta_k == 'mso_shipment_order_ship' || $this->mso_shipment_meta_k == 'mso_shipment_order_ship_backup' ? http_build_query($this->mso_shipment_meta_v) : json_encode($this->mso_shipment_meta_v);
                update_post_meta($order_id, $this->mso_shipment_meta_k, $this->mso_shipment_meta_v);
            }
        }
    }

    /**
     * Label parcing build query
     */
    public function mso_parsing_build_query($build_query)
    {
        $parsed_arr = [];
        parse_str(trim($build_query), $parsed_arr);
        return $parsed_arr;
    }

    /**
     * Order get meta data
     */
    public function mso_shipments_getting_data($order_id)
    {
        $mso_last_access = [];

        $this->mso_shipment_meta_k = '';

        $mso_shipment_order_ship_main = get_post_meta($order_id, 'mso_shipment_order_ship', true);
        if (isset($mso_shipment_order_ship_main) && strlen($mso_shipment_order_ship_main) > 0) {
            $mso_order_ship_main = json_decode($mso_shipment_order_ship_main, true);
            $this->mso_shipment_meta_k = 'mso_shipment_order_ship';
            $this->mso_shipment_meta_v = $mso_order_ship_main;
//            $mso_last_access = (isset($mso_order_ship_main['shipments'])) ? $mso_order_ship_main['shipments'] : [];
            $mso_last_access = $mso_order_ship_main;
        }

        $mso_shipment_order_ship_backup = get_post_meta($order_id, 'mso_shipment_order_ship_backup', true);
        if (empty($mso_last_access) && isset($mso_shipment_order_ship_backup) && strlen($mso_shipment_order_ship_backup) > 0) {
            $mso_order_ship_backup = json_decode($mso_shipment_order_ship_backup, true);
            $this->mso_shipment_meta_k = 'mso_shipment_order_ship_backup';
            $this->mso_shipment_meta_v = $mso_order_ship_backup;
//            $mso_last_access = (isset($mso_order_ship_backup['shipments'])) ? $mso_order_ship_backup['shipments'] : [];
            $mso_last_access = $mso_order_ship_backup;
        }

        $arranged_customer = get_post_meta($order_id, 'mso_shipment_order_arranged_by_customer', true);
        if (empty($mso_last_access) && isset($arranged_customer) && strlen($arranged_customer) > 0) {
            $arranged_customer = json_decode($arranged_customer, true);
            $this->mso_shipment_meta_k = 'mso_shipment_order_arranged_by_customer';
            $this->mso_shipment_meta_v = $arranged_customer;
//            $mso_last_access = (isset($arranged_customer['shipments'])) ? $arranged_customer['shipments'] : [];
            $mso_last_access = $arranged_customer;
        }

        return $mso_last_access;
    }

    /**
     * Order get quotes
     */
    public function mso_shipment_order_get_quotes()
    {
        // Subscription status
        $this->mso_subscriptions_status();
        $shipments = $shipment_rates = $unshipment_rates = $mso_order_package = [];
        $mso_post_data = (isset($_POST['mso_shipments'])) ? $_POST['mso_shipments'] : [];
        $ship_to = (isset($_POST['mso_ship_to_address'])) ? sanitize_text_field($_POST['mso_ship_to_address']) : [];
        $order_id = (isset($_POST['mso_order_id'])) ? sanitize_text_field($_POST['mso_order_id']) : [];
        $order = wc_get_order($order_id);

        $mso_mswrflfq = get_option('mso_mswrflfq');
        $mso_min_weight = isset($mso_mswrflfq) && strlen($mso_mswrflfq) > 0 && is_numeric($mso_mswrflfq) ? $mso_mswrflfq : 150;

        $type = '';
        switch ($ship_to) {
            case 'mso_billing_address':
                // Billing address
                $ship_to = $order->get_address('billing');
                $type = 'billing';
                break;
            default:
                // Shipping address
                $ship_to = $order->get_address('shipping');
                $type = 'shipping';
                break;
        }

        $ship_to['type'] = $type;

        // Shipment disabled
        $mso_last_access = $this->mso_shipments_getting_data($order_id);

        // Locations
        $mso_product_detail = new MsoProductDetail();
        $locations = $mso_product_detail->mso_locations();
        $non_shipments = $mso_ship_numbers = $origin_id_list = [];
        foreach ($mso_post_data as $mso_ship_num => $shipment) {
            // shipment disabled
            $non_ship_trigger = false;
            $enable_disable = isset($shipment['enable_disable']) ? $shipment['enable_disable'] : '';
            if ($enable_disable == 'disabled') {
//                if (isset($mso_last_access['shipments'], $mso_last_access['shipments'][$mso_ship_num])) {
//                    $non_shipments[$mso_ship_num] = $mso_last_access['shipments'][$mso_ship_num];
//                    continue;
//                }
                if (isset($mso_last_access[$mso_ship_num])) {
                    $non_shipments[$mso_ship_num] = $mso_last_access[$mso_ship_num];
                    continue;
                } else {
                    $non_ship_trigger = true;
                }
            }

            $mso_ship_num = sanitize_text_field($mso_ship_num);
            if (isset($shipment['origin'], $locations[$shipment['origin']])) {
                $shipment_origin = sanitize_text_field($shipment['origin']);
                $origin_id = $shipment_origin;
                $location = $locations[$shipment_origin];
                $mso_zip = $mso_city = $mso_state = $mso_country = '';
                extract($location);
                $origin = [
                    'id' => $origin_id,
                    'city' => $mso_city,
                    'postcode' => $mso_zip,
                    'state' => $mso_state,
                    'country' => $mso_country
                ];
            } else {
                $origin = MsoPackage::mso_shop_base_address();
            }

            $mso_zip = isset($origin['postcode']) ? $origin['postcode'] : '';
            $origin_id = isset($origin['id']) ? $origin['id'] : 0;
            $origin_id_list[$mso_zip] = $origin_id;
            $mso_ship_numbers[$mso_ship_num] = [
                'mso_zip' => $mso_zip,
                'origin_id' => $origin_id
            ];

            $shipments[$mso_ship_num]['ship_from'] = $origin;

            if (isset($shipment['accessorials'])) {
                $accessorials = [
                    'mso_residential' => 'residential_delivery',
                    'mso_liftgate' => 'liftgate_delivery'
                ];

                foreach ($shipment['accessorials'] as $accessorial_key => $accessorial_value) {
                    (isset($accessorials[$accessorial_key])) ? $shipments[$mso_ship_num]['accessorials'][$accessorials[$accessorial_key]] = sanitize_text_field($accessorial_value) : '';
                }

            }

            if (isset($shipment['items'])) {
                foreach ($shipment['items'] as $product_id => $product_quantity) {
                    $product_id = sanitize_text_field($product_id);
                    $product_quantity = sanitize_text_field($product_quantity);
                    $item_data = wc_get_product($product_id);
                    $product_name = wp_strip_all_tags($item_data->get_formatted_name());
                    $product_price = $item_data->get_price();
                    // Product id
                    // Product details
                    $product = wc_get_product($product_id);
                    $weight = wc_get_weight($product->get_weight(), 'lbs');
                    $height = wc_get_dimension($product->get_height(), 'in');
                    $width = wc_get_dimension($product->get_width(), 'in');
                    $length = wc_get_dimension($product->get_length(), 'in');

                    (!isset($shipments[$mso_ship_num]['ship_weight'])) ? $shipments[$mso_ship_num]['ship_weight'] = 0 : '';
                    $shipments[$mso_ship_num]['ship_weight'] += $weight;
                    $shipments[$mso_ship_num]['items'][$product_id] = [
                        'product_id' => $product_id,
                        'freight_class' => 60,
                        'quantity' => $product_quantity,
                        'title' => $product_name,
                        'weight' => $weight,
                        'height' => $height,
                        'width' => $width,
                        'length' => $length,
                        'price' => $product_price
                    ];

                    if ($shipments[$mso_ship_num]['ship_weight'] > $mso_min_weight) {
                        $shipments[$mso_ship_num]['action'] = 'lfq';
                    }
                }
            }

            // Shipment disabled
            if ($non_ship_trigger) {
                $non_shipments[$mso_ship_num] = $shipments[$mso_ship_num];
                unset($shipments[$mso_ship_num]);
            }
        }

        $carriers_rate = $mso_rates = [];
        $actual_response = $rates = MsoPackage::mso_request_settings($shipments, $ship_to, 'rate_order_page');

//        // Shipment disabled
//        if (!empty($non_shipments)) {
//            $getting_rates = strlen($rates) > 0 ? json_decode($rates, true) : [];
//            foreach ($non_shipments as $non_shipment_key => $non_shipment) {
//                $getting_rates['shipments'][$non_shipment_key] = $non_shipment;
//            }
//
//            $recreate_shipments = [];
//            foreach ($mso_post_data as $mso_post_data_k => $mso_post_data_v) {
//                isset($getting_rates['shipments'][$mso_post_data_k]) ? $recreate_shipments[$mso_post_data_k] = $getting_rates['shipments'][$mso_post_data_k] : '';
//            }
//            $getting_rates['shipments'] = $recreate_shipments;
//            $rates = json_encode(['shipments' => $getting_rates['shipments']]);
//        }
//
//        update_post_meta($order_id, 'mso_shipment_order_arranged_by_customer', $rates);

        $mso_rates = json_decode($actual_response, true);
//        $rates_output = json_decode($actual_response, true);
//        $accessorials = (isset($rates_output['accessorials'])) ? $rates_output['accessorials'] : [];
//        $shipments = (isset($rates_output['shipments'])) ? $rates_output['shipments'] : [];

        $mso_ship_rates = [];
        $rates_returning = false;
//        if (!empty($shipments) && is_array($shipments)) {
        if (!empty($mso_rates) && is_array($mso_rates)) {
//            $mso_rates = MsoPackage::mso_shipment_rates($shipments, $accessorials, $ship_to, true);
//            if (isset($mso_rates['shipments'])) {
//                unset($mso_rates['shipments']);
//            }

            $mso_package_obj = new MsoPackage();

            foreach ($mso_rates as $mso_ship_num => $mso_carrier_rates) {
                if (isset($mso_carrier_rates['spq']) || isset($mso_carrier_rates['lfq'])) {
                    $mso_carrier_rates = reset($mso_carrier_rates);
                }

                foreach ($mso_carrier_rates as $carrier_name => $mso_carrier_rate) {
                    $rates_count = 0;
                    if (isset($mso_carrier_rate['error'], $mso_carrier_rate['message'])) {
                        !isset($mso_ship_rates[$mso_ship_num]) ? $mso_ship_rates[$mso_ship_num] = $mso_carrier_rate : '';
                        $unshipment_rates[$mso_ship_num] .= '<span class="mso_rate_error_message"><b>Error! </b>' . $mso_carrier_rate['message'] . '</span></br>';
                    }

                    $mso_carrier_rate = $mso_package_obj->mso_sort_asec($mso_carrier_rate, 'cost');;
                    foreach ($mso_carrier_rate as $key => $rate) {
                        $mso_ship_rates[$mso_ship_num] = $rate;

                        if (isset($rate['response'], $rate['response']['packed_items'])) {
                            $shipment_packages = $this->mso_order_package($rate);
                            $mso_order_package[$mso_ship_num] = '<p class="mso_calculate_shipping_heading">Packaging (' . $this->mso_subscription_status . ') </p>&nbsp;' . $shipment_packages;
                        }

                        $cost = (isset($rate['cost'])) ? $rate['cost'] : 0;
                        $is_paid = (isset($rate['is_paid'])) ? $rate['is_paid'] : '';
                        $disabled = !MSO_DONT_AUTH && !$is_paid ? 'disabled' : '';
                        if ($cost > 0) {
                            $rates_returning = true;
                            $label = (isset($rate['label'])) ? $rate['label'] : 'Shipping';
                            $currency_symbol = MSO_CURRENCY_SYMBOL;
                            $mso_quote = $label . ': ' . $currency_symbol . $cost;
                            !isset($shipment_rates[$mso_ship_num]) ? $shipment_rates[$mso_ship_num] = '' : '';
                            $service_id = $this->mso_random();
                            $packed_items = isset($rate['response'], $rate['response']['packed_items']) ? $rate['response']['packed_items'] : [];
                            if (isset($rate['response'])) {
                                unset($rate['response']);
                            }
                            !empty($packed_items) ? $rate['response']['packed_items'] = $packed_items : '';
                            $carriers_rate[$service_id] = $rate;
                            $is_checked = $rates_count === 0 && $disabled != 'disabled' ? 'checked="checked"' : '';

                            // Error handling
                            $mso_paid_plan_feature = '';
                            if ($disabled == 'disabled') {
                                $disabled = 'disabled="disabled"';
                                $mso_paid_plan_feature = '<span class="mso_order_wrapper mso_wrapper"><span class="mso_tooltip">' . MSO_PAID_PLAN_FEATURE . '</span></span>';
                            }
                            $shipment_rates[$mso_ship_num] .= '<input ' . $disabled . ' data-rate="' . http_build_query($rate) . '" type="radio" id="" name="mso_order_rate" value="' . esc_attr($service_id) . '" ' . esc_attr($is_checked) . '><label for="mso_order_rate">' . esc_html($mso_quote) . '</label>' . $mso_paid_plan_feature . '<br>';
                        }

                        $rates_count++;
                    }
                }
            }

            // Shipment return the error handling
            if (!empty($unshipment_rates)) {
                foreach ($unshipment_rates as $unshipment_num => $unshipment_rate) {
                    if (!isset($shipment_rates[$unshipment_num])) {
                        $shipment_rates[$unshipment_num] = $unshipment_rate;
                    }
                }
            }
        }

        // Shipment disabled
        !empty($mso_ship_rates) ? $rates = json_encode($mso_ship_rates) : '';
        if (!empty($non_shipments)) {
            $getting_rates = strlen($rates) > 0 ? json_decode($rates, true) : [];
            foreach ($non_shipments as $non_shipment_key => $non_shipment) {
                $getting_rates[$non_shipment_key] = $non_shipment;
            }

            $recreate_shipments = [];
            foreach ($mso_post_data as $mso_post_data_k => $mso_post_data_v) {
                isset($getting_rates[$mso_post_data_k]) ? $recreate_shipments[$mso_post_data_k] = $getting_rates[$mso_post_data_k] : '';
            }

            $rates = json_encode($recreate_shipments);
        }

        update_post_meta($order_id, 'mso_shipment_order_arranged_by_customer', $rates);

        WC()->session->set('mso_cr_store', json_encode($carriers_rate));
        echo json_encode([
            'order_rates' => $shipment_rates,
            'order_packages' => $mso_order_package,
            'rates_returning' => $rates_returning
        ]);
        exit;
    }

    /**
     * Get random integers
     */
    function mso_random()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Subscription status
     */
    public function mso_subscriptions_status()
    {
        if (MSO_DONT_AUTH) {
            return;
        }

        $subscription_boolean = false;
        $status_description = $mso_subscription_status = '';
        if (MSO_PLAN_STATUS != 'success' || empty(MSO_SUBSCRIPTIONS)) {
            $mso_subscription_status = MSO_PAID_PLAN_FEATURE_DIALOG;
            $status_description = '<span class="mso_err_status_description">' . MSO_PAID_PLAN_FEATURE . '</span>';
        }

        if (!empty(MSO_SUBSCRIPTIONS)) {
            $subscription_boolean = true;
            $carriers = [];
            foreach (MSO_SUBSCRIPTIONS as $key => $subscription) {
                $carriers[] = isset($subscription['carrier']) ? $subscription['carrier'] : '';
            }
//            $mso_subscription_status = "Please note that the following feature is only available for your paid carriers, such as " . mso_implode_carriers($carriers) . ", will be effective in controlling order shipments and allowing for packages.";
            $mso_subscription_status = sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers));
//            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>it is only possible to monitor the shipment of orders through your paid carriers such as " . mso_implode_carriers($carriers) . ".</p></div>" : '';
            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>" . sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers)) . "</p></div>" : '';
        }

        $this->mso_subscription_status = $mso_subscription_status;
        $this->subscription_boolean = $subscription_boolean;

        return $status_description;
    }

    /**
     * Setting Order For Woocommerce
     */
    public function mso_order($actions)
    {
        $status_description = $this->mso_subscriptions_status();
//        add_meta_box('mso_order', __('Order Details ' . $status_description . ' <span class="form-control button-primary mso_create_shipment_block">New Shipment</span>', 'woocommerce'), [$this, 'mso_order_shipping'], 'shop_order', 'normal', 'core');
        add_meta_box('mso_order', __($status_description . ' <span title="Through this you can add shipment for dealing with more items" class="form-control button-primary mso_create_shipment_block">Add shipment</span>', 'woocommerce'), [$this, 'mso_order_shipping'], 'shop_order', 'normal', 'core');
        return $actions;
    }

    /**
     * Get items shipping
     */
    public function mso_get_items_shipping($order, $meta_keys)
    {
        $get_items = [];
        $shipping_details = $order->get_items('shipping');
        foreach ($shipping_details as $item_id => $shipping_item) {
            $get_formatted_meta_data = $shipping_item->get_formatted_meta_data();
            foreach ($get_formatted_meta_data as $key => $meta_data) {
                if (in_array($meta_data->key, $meta_keys)) {
                    $get_items[$meta_data->key] = json_decode($meta_data->value, true);
                }
            }
        }

        return $get_items;
    }


    /**
     * Shipping order
     */
    public function mso_order_shipping()
    {
        $pass_order_id = 0;
        if (isset($_POST['mso_order_id']) && $_POST['mso_order_id'] > 0) {
            $pass_order_id = $_POST['mso_order_id'];
        }

        $mso_widget_detail = $mso_shipments = $mso_items = [];
        $order_id = $pass_order_id > 0 ? $pass_order_id : get_the_ID();
        $order = wc_get_order($order_id);

//        $shipping_details = $order->get_items('shipping');
//        foreach ($shipping_details as $item_id => $shipping_item_obj) {
//            $get_formatted_meta_data = $shipping_item_obj->get_formatted_meta_data();
//            foreach ($get_formatted_meta_data as $key => $meta_data) {
//                switch ($meta_data->key) {
//                    case 'mso_widget_detail':
//                        $mso_widget_detail = json_decode($meta_data->value, true);
//                        break;
//                }
//            }
//        }

        $mso_get_items_shipping = $this->mso_get_items_shipping($order, ['mso_widget_detail']);
        $mso_widget_detail = [];
        extract($mso_get_items_shipping);

        // Locations
        $mso_product_detail = new MsoProductDetail();
        $locations = $mso_product_detail->mso_locations();

        // Shipping address
        $shipping_address_label = '';
        $shipping_address = $order->get_address('shipping');
        $address_1 = $city = $state = $postcode = $country = '';
        extract($shipping_address);
        $shipping_address_label = "$address_1, $city, $state, $postcode, $country";

        // Billing address
        $billing_address_label = '';
        $billing_address = $order->get_address('billing');
        $address_1 = $city = $state = $postcode = $country = '';
        extract($billing_address);
        $billing_address_label = "$address_1, $city, $state, $postcode, $country";

        echo '<h2 class="mso_order_id" value="' . esc_attr($order_id) . '">' . esc_html("Order #" . $order_id) . '</h2>';

        // TODO
        $mso_order_ship = [];
        $ship_to_address = [];
        $mso_shipment_order_ship_main = trim(get_post_meta($order_id, 'mso_shipment_order_ship', true));
        if (isset($mso_shipment_order_ship_main) && strlen($mso_shipment_order_ship_main) > 0) {
            $mso_order_ship = json_decode($mso_shipment_order_ship_main, true);
//            $mso_order_ship = (isset($mso_order_ship_main['shipments'])) ? $mso_order_ship_main['shipments'] : [];
//            $ship_to_address = (isset($mso_order_ship_main['ship_to'])) ? $mso_order_ship_main['ship_to'] : [];
        }

        $mso_shipment_order_ship_backup = trim(get_post_meta($order_id, 'mso_shipment_order_ship_backup', true));
        if (empty($mso_order_ship) && isset($mso_shipment_order_ship_backup) && strlen($mso_shipment_order_ship_backup) > 0) {
            $mso_order_ship = json_decode($mso_shipment_order_ship_backup, true);
//            $ship_to_address = (isset($mso_order_ship_backup['ship_to'])) ? $mso_order_ship_backup['ship_to'] : [];
//            $mso_order_ship = (isset($mso_order_ship_backup['shipments'])) ? $mso_order_ship_backup['shipments'] : [];
        }

        $arranged_customer = get_post_meta($order_id, 'mso_shipment_order_arranged_by_customer', true);
        if (empty($mso_order_ship) && isset($arranged_customer) && strlen($arranged_customer) > 0) {
            $mso_order_ship = json_decode($arranged_customer, true);
//            $ship_to_address = (isset($arranged_customer['ship_to'])) ? $arranged_customer['ship_to'] : [];
//            $mso_order_ship = (isset($arranged_customer['shipments'])) ? $arranged_customer['shipments'] : [];
        }

//        echo '<pre>';
//        print_r($mso_order_ship);
//        echo '</pre>'; die;

        $ship_to_type = '';
        if (!empty($ship_to_address)) {
            $type = $address_1 = $city = $state = $postcode = $country = '';
            extract($ship_to_address);
            $ship_to_address_label = "$address_1, $city, $state, $postcode, $country";
            $ship_to_type = $type;
        } else {
            $ship_to_address_label = $shipping_address_label;
        }

        $ship_to_template = [
            'mso_billing_address' => [
                'name' => 'Billing Address',
                'type' => 'radio',
                'default' => $ship_to_type == 'billing' ? 'yes' : 'no',
                'id' => 'mso_ship_to_address',
                'value' => 'mso_billing_address',
                'desc' => $billing_address_label,
                'tr_class' => 'mso_order_child mso_ship_to_address_selection'
            ],
            'mso_shipping_address' => [
                'name' => 'Shipping Address',
                'type' => 'radio',
                'default' => $ship_to_type == 'shipping' ? 'yes' : ($ship_to_type != 'billing' && $ship_to_type != 'shipping' ? 'yes' : 'no'),
                'id' => 'mso_ship_to_address',
                'value' => 'mso_shipping_address',
                'desc' => $shipping_address_label,
                'tr_class' => 'mso_order_child mso_ship_to_address_selection'
            ],
        ];

        echo apply_filters('mso_form_template', $ship_to_template);

        // Order Items detail
        $items = $order->get_items();
        foreach ($items as $item_id => $item_data) {
            $id = $item_data['product_id'];
            $variation_id = $item_data['variation_id'];
            $op_name = $item_data['name'];
            $op_price = $item_data['price'];
            $op_quantity = $item_data['quantity'];
            // Product id
            $product_id = $variation_id > 0 ? $variation_id : $id;
            // Product details
            $product = wc_get_product($product_id);
            $weight = wc_get_weight($product->get_weight(), 'lbs');
            $height = wc_get_dimension($product->get_height(), 'in');
            $width = wc_get_dimension($product->get_width(), 'in');
            $length = wc_get_dimension($product->get_length(), 'in');

            $mso_order_items[$product_id] = [
                'product_id' => $id,
                'variation_id' => $variation_id,
                'quantity' => $op_quantity,
                'title' => $op_name,
                'weight' => $weight,
                'height' => $height,
                'width' => $width,
                'length' => $length,
                'price' => $op_price
            ];
        }

        if (!empty($mso_order_ship)) {
            $mso_shipments = $mso_order_ship;
        } elseif (!empty($mso_widget_detail)) {
            $mso_shipments = $mso_widget_detail;
        } else {

            $mso_shipments[] = [
                'response' => [
                    'items' => $mso_order_items,
                    'ship_from' => [],
                ],
                'accessorials' => [],
            ];
        }

        $location_options = [];
        foreach ($locations as $location_id => $location) {
            $mso_city = $mso_state = $mso_zip = $mso_country = '';
            extract($location);
            $mso_origin = "$mso_city, $mso_state, $mso_zip, $mso_country";
            $location_options[$location_id] = $mso_origin;
        }

        $mso_ship_num = 1;
        $this->mso_order_shipments_list($mso_shipments, $location_options, $order_id, $mso_order_items, $mso_ship_num, $ship_to_address_label);
        $mso_shipment_order_ship_last_one = get_post_meta($order_id, 'mso_shipment_order_ship', true);

        ?>
        <table class="form-table"></table>
        <hr class="mso_hr">
        <?php if (!strlen($mso_shipment_order_ship_last_one) > 0) { ?>
        <div class="bootstrap-iso form-wrp">
            <div class="row mso_rate_ship_btn">
                <?php
                if (MSO_PLAN_STATUS == 'error') {
                    echo strlen(MSO_KEY_STATUS) > 0 ? MSO_KEY_STATUS : MSO_KEY_ERROR;
                } elseif (!strlen(MSO_PLAN_STATUS) > 0 && !strlen(MSO_KEY_STATUS) > 0) {
                    echo MSO_KEY_ERROR;
                } else {
                    ?>
                    <button type="button" onclick="mso_order_get_quote()" class="button-primary mso_order_get_quote">
                        Calculate Shipping
                    </button>
                <?php } ?>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="bootstrap-iso form-wrp">
            <div class="row mso_rate_ship_btn" style="color: red;">
                <p style="text-align: center; margin-top: 15px;"><b>Note! </b> A shipment has been created. click <a
                            type="button" onclick="mso_order_recreate_shipment_allow(<?php echo $order_id; ?>)"
                            class="mso_order_recreate_shipment_allow">here</a> to recreate the shipment.
                </p>
            </div>
        </div>
        <?php
    }
        ?>
        <div class="mso_order_shipment_file_to_show_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <div class="mso_label_popup_action">
                    <a onclick="mso_order_asset_delete_warning_overly_hide()">Cancel</a>
                    <a class="msoolctd" onclick="mso_order_label_click_to_download(this)">Download</a>
                    <a class="msoolctp" onclick="mso_order_label_click_to_print(this)">Print</a>
                </div>
                <div class="bootstrap-iso form-wrp">
                    <div class="row">
                        <div class="mso_file_to_upload col-md-12">
                            <!--                            <p>Uploading...</p>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mso_order_shipment_delete_warning_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <a onclick="mso_order_asset_delete_warning_overly_hide()" class="close">×</a>
                <div class="bootstrap-iso form-wrp">
                    <div class="row">
                        <div class="mso_input col-md-12 mso_deleting_shipment">
                            <p>Are you sure you want to delete the shipment?</p>
                        </div>
                        <div class="mso_input col-md-6">
                            <a onclick="mso_order_asset_delete_warning_overly_hide()"
                               class="form-control mso_order_link mso_button">Cancel</a>
                        </div>
                        <div class="mso_input col-md-6">
                            <a class="form-control mso_order_link mso_delete_shipment_done button-primary mso_button">Ok</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mso_order_item_delete_warning_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <a onclick="mso_order_asset_delete_warning_overly_hide()" class="close">×</a>
                <div class="bootstrap-iso form-wrp">
                    <div class="row">
                        <div class="mso_input col-md-12">
                            <p>Are you sure you want to delete the item?</p>
                        </div>
                        <div class="mso_input col-md-6">
                            <a onclick="mso_order_asset_delete_warning_overly_hide()"
                               class="form-control mso_order_link mso_button">Cancel</a>
                        </div>
                        <div class="mso_input col-md-6">
                            <a class="form-control mso_order_link mso_delete_order_item_done button-primary mso_button">Ok</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Order list.
     */
    public function mso_order_shipments_list($mso_shipments, $location_options, $order_id, $mso_order_items, $mso_ship_num, $ship_to_address_label)
    {
        foreach ($mso_shipments as $key => $mso_shipment) {
            $shipment_num = $key;
            $accessorials = (isset($mso_shipment['accessorials'])) ? $mso_shipment['accessorials'] : [];
            $ship_from = [];
            if (isset($mso_shipment['response']['ship_from'])) {
                $ship_from = $mso_shipment['response']['ship_from'];
            } elseif (isset($mso_shipment['ship_from'])) {
                $ship_from = $mso_shipment['ship_from'];
            }
            $origin_id = (isset($ship_from['id'])) ? $ship_from['id'] : 'store_address';
            $origin_template = [
                'mso_order_shipment_origin' => [
                    'name' => 'From',
                    'type' => 'select',
                    'default' => $origin_id,
                    'desc' => 'Edit Origins',
                    'id' => 'mso_order_shipment_origin',
                    'options' => $location_options,
                    'tr_class' => 'mso_tr_order_shipment_origin',
                ]
            ];

            $accessorials_heading_template = [
                'mso_accessorials' => [
                    'name' => 'Accessorials',
                    'type' => 'title',
                    'tr_class' => 'mso_accessorials_heading',
                ]
            ];

            $accessorial_options = [
                'mso_residential' => [
                    'name' => 'Residential',
                    'type' => 'checkbox',
                    'default' => (isset($accessorials['residential_delivery'])) ? $accessorials['residential_delivery'] : 'no',
                    'id' => 'mso_residential',
                    'tr_class' => 'mso_order_child mso_order_accessorial',
                ],
                'mso_liftgate' => [
                    'name' => 'Liftgate',
                    'type' => 'checkbox',
                    'default' => (isset($accessorials['liftgate_delivery'])) ? $accessorials['liftgate_delivery'] : 'no',
                    'id' => 'mso_liftgate',
                    'tr_class' => 'mso_order_child mso_order_accessorial',
                ],
            ];

            $itmes_heading_template = [
                'mso_items' => [
                    'name' => 'Items',
                    'type' => 'title',
                    'tr_class' => 'mso_itmes_heading',
                ]
            ];

            $ship_options_heading_template = [
                'mso_ship' => [
                    'name' => 'Shipping Options',
                    'type' => 'title',
                    'tr_class' => 'mso_ship_options_heading',
                ]
            ];

            $destination_address = [
                'mso_destination' => [
                    'name' => 'To',
                    'type' => 'title',
                    'tr_class' => 'mso_order_destination',
                    'desc' => $ship_to_address_label
                ]
            ];

//            echo apply_filters('mso_form_template', $origin_template);
            echo '<div class="mso_order_ship_action">';
            echo '<input onclick="mso_order_shipment_enable_disable(this,event)" type="checkbox" checked="checked">&nbsp<span style="font-weight: 500; font-size: 13px; padding-top: 2px">Enable / Disable</span>';
            echo '</div>';

            echo '<div data-mso_ship_num="' . esc_attr($mso_ship_num) . '" class="mso_order_shipment"><span class="mso_shipment_num_text">Shipment</span><span title="Shipment Number" class="mso_order_shipment_number">' . esc_attr($mso_ship_num) . '</span> <span onclick="mso_order_shipment_remove(this,event)" class="ui-icon mso_shipment_remove ui-icon-closethick"></div>';

            echo '</form>';
            echo '<form class="mso_flex_template" method="post">';


            // Order shipment options
            echo '<div class="mso_order_shipment_options">';
            // *** Left block ***
            echo '<div class="mso_order_lb">';
            // From
            echo '<div class="mso_order_from">';
            echo apply_filters('mso_form_template', $origin_template);
            echo '</div>';
            // To
            echo '<div class="mso_order_to">';
            echo apply_filters('mso_form_template', $destination_address);
            echo '</div>';
            // Items
            $mso_items = [];
            if (isset($mso_shipment['response']['items'])) {
                $mso_items = $mso_shipment['response']['items'];
            } elseif (isset($mso_shipment['items'])) {
                $mso_items = $mso_shipment['items'];
            }
            echo '<div class="mso_order_items">';
            echo apply_filters('mso_form_template', $itmes_heading_template);
            echo '<ul class="mso_items_sortable">';
            $mso_items = !empty($mso_items) && is_array($mso_items) ? $mso_items : [];
            foreach ($mso_items as $mso_item_id => $mso_item) {
                $product_id = $variation_id = 0;
                $quantity = $title = '';
                extract($mso_item);
                $product_id = $variation_id > 0 ? $variation_id : $product_id;
                $item_data = wc_get_product($product_id);
                $title = wp_strip_all_tags($item_data->get_formatted_name());
                if (isset($mso_order_items[$product_id])) {
                    unset($mso_order_items[$product_id]);
                }
                echo '<li data-product_id="' . esc_attr($product_id) . '" class="ui-state-default"><span class="ui-icon mso_item_arrow ui-icon-arrowthick-2-n-s"></span><span onclick="mso_order_item_remove(this,event)" class="ui-icon mso_item_remove ui-icon-closethick"></span><span class="mso_item_quantity"><input type="number" value="' . esc_attr($quantity) . '"></span>' . esc_html($title) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            echo '</div>';

            // *** Right block ***
            echo '<div class="mso_order_rb">';
            // Accessorials
            echo '<div class="mso_order_accessorials">';
            echo apply_filters('mso_form_template', $accessorials_heading_template);
            echo apply_filters('mso_form_template', $accessorial_options);
            echo '</div>';


//            echo '<div class="mso_accessorials_items">';
//            echo '<div class="mso_order_accessorials">';
//            echo apply_filters('mso_form_template', $accessorial_template);
//            echo '</div>';
//            $mso_items = [];
//            if (isset($mso_shipment['response']['items'])) {
//                $mso_items = $mso_shipment['response']['items'];
//            } elseif (isset($mso_shipment['items'])) {
//                $mso_items = $mso_shipment['items'];
//            }
//            echo '<div class="mso_order_items">';
//            echo apply_filters('mso_form_template', $itmes_heading_template);
//            echo '<ul class="mso_items_sortable">';
//            $mso_items = !empty($mso_items) && is_array($mso_items) ? $mso_items : [];
//            foreach ($mso_items as $mso_item_id => $mso_item) {
//                $product_id = $variation_id = 0;
//                $quantity = $title = '';
//                extract($mso_item);
//                $product_id = $variation_id > 0 ? $variation_id : $product_id;
//                $item_data = wc_get_product($product_id);
//                $title = wp_strip_all_tags($item_data->get_formatted_name());
//                if (isset($mso_order_items[$product_id])) {
//                    unset($mso_order_items[$product_id]);
//                }
//                echo '<li data-product_id="' . esc_attr($product_id) . '" class="ui-state-default"><span class="ui-icon mso_item_arrow ui-icon-arrowthick-2-n-s"></span><span onclick="mso_order_item_remove(this,event)" class="ui-icon mso_item_remove ui-icon-closethick"></span><span class="mso_item_quantity"><input type="number" value="' . esc_attr($quantity) . '"></span>' . esc_html($title) . '</li>';
//            }
//            echo '</ul>';
//            echo '</div>';
//            echo '</div>';
//            echo '<div style="clear: both"></div>';
            echo apply_filters('mso_form_template', $ship_options_heading_template);
            ?>
            <div class="mso_order_main_tab">
                <ol>
                    <li data-tab="mso_order_shipments_labels">
                        <div>Label</div>
                    </li>
                    <li data-tab="mso_order_api_response">
                        <div>API Response</div>
                    </li>
                    <li data-tab="mso_order_package">
                        <div>Packaging</div>
                    </li>
                    <li class="msoorc" data-tab="mso_order_rates">
                        <div>Rates</div>
                    </li>
                </ol>
            </div>
            <?php
            $mso_api_response = [];
            $mso_carrier_name = '';
            echo '<div class="mso_order_shipments_labels mso_order_tab">'; // Start label showing div
            $created_ship_error = isset($mso_shipment['created_ship_error']) && strlen($mso_shipment['created_ship_error']) > 0 ? '<span class="mso_rate_error_message"><b>Error! </b> ' . $mso_shipment['created_ship_error'] . '</span>' : '';
            // TODO
            echo '<p class="mso_calculate_shipping_heading">Label</p>' . $created_ship_error;
//            echo '<p class="mso_calculate_shipping_heading">Label</p>';
            if (isset($mso_shipment['rate']) && !empty($mso_shipment['rate'])) {
                $pdf_icon = MSO_DIR_FILE . '/images/pdf.png';
                $carrier = $ups_spq_code = $fedex_spq_service = $packaging_type = $fedex_lfq_service = '';
                extract($mso_shipment['rate']);
                $mso_carrier_name = $carrier;
                switch ($carrier) {
                    case 'fedex':
                        echo '<div class="mso_ship_label_content">';
                        $package_shipments = isset($mso_shipment['fedex_ship']) ? $this->mso_parsing_build_query($mso_shipment['fedex_ship']) : [];
                        $fedex_labels = $fedex_sd = [];
                        $fedex_sl = '';
                        foreach ($package_shipments as $package_number => $response) {
                            // Error handling
                            if (isset($response['HighestSeverity'], $response['Notifications'], $response['Notifications']['Message']) && ($response['HighestSeverity'] == 'FAILURE' || $response['HighestSeverity'] == 'ERROR')) {
                                echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['Notifications']['Message'] . '</span></br>';
                            }

                            $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-fedex-spq-' . 'package-' . $package_number;
                            $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';

                            if (isset($response['HighestSeverity']) && $response['HighestSeverity'] != 'FAILURE' && $response['HighestSeverity'] != 'ERROR') {

                                if (isset($response['CompletedShipmentDetail'], $response['CompletedShipmentDetail']['CompletedPackageDetails'], $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'])) {
                                    $fedex_sd[] = [
                                        'TrackingIdType' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingIdType'],
                                        'TrackingNumber' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber']
                                    ];
                                }

                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'])) {
                                    $base64_string_png = $response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'];
                                    $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';
                                    file_exists($png) ? unlink($png) : '';
                                    file_put_contents($png, base64_decode($base64_string_png));
                                }

                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'])) {
                                    $base64_string_png = $response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'];
                                    $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';
                                    file_exists($png) ? unlink($png) : '';
                                    file_put_contents($png, base64_decode($base64_string_png));
                                }

                                if (file_exists($png)) {
                                    $fedex_labels[] = $png_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.png';
                                    $fedex_sl .= '<img src="' . esc_url($png_to_show) . '" class="mso_real_label_image" onclick="mso_file_to_click(this,1)" alt="Label Missing" alt="Missing Label"/>';
                                }
                            }
                        }

                        echo $fedex_sl;
                        echo '</div>';
                        if (!empty($fedex_sd)) {
                            // TODO
//                            echo '<span data-carrier="fedex" data-post_data="' . http_build_query($fedex_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
                            echo '<span data-carrier="fedex" data-post_data="' . implode(',', $fedex_labels) . '" class="form-control button-primary mso_print_all_shipment">Print All Shipment</span>';
                        }
                        // API response
                        isset($package_shipments) ? $mso_api_response = $package_shipments : '';
                        break;
                    case 'ups':
                        $response = isset($mso_shipment['ups_ship']) ? $this->mso_parsing_build_query($mso_shipment['ups_ship']) : [];
                        $ups_sd = $ups_labels = [];
                        $ups_sl = '';
                        // Error handling
                        if (isset($response['confirm'], $response['confirm']['Response'], $response['confirm']['Response']['Error'], $response['confirm']['Response']['Error']['ErrorDescription'], $response['confirm']['Response']['Error']['ErrorSeverity']) && (strtolower($response['confirm']['Response']['Error']['ErrorSeverity']) != 'warning')) {
                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['confirm']['Response']['Error']['ErrorDescription'] . '</span></br>';
                        } else if (isset($response['accept'], $response['accept']['Response'], $response['accept']['Response']['Error'], $response['accept']['Response']['Error']['ErrorDescription'])) {
                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['accept']['Response']['Error']['ErrorDescription'] . '</span></br>';
                        }

                        echo '<div class="mso_ship_label_content">';
                        if (isset($response['accept'], $response['accept']['ShipmentResults'], $response['accept']['ShipmentResults']['PackageResults'])) {
                            $package_results = $response['accept']['ShipmentResults']['PackageResults'];
                            (isset($package_results['LabelImage'])) ? $package_results = [$package_results] : '';
                            foreach ($package_results as $package => $label_detail) {
                                if (isset($label_detail['TrackingNumber'])) {
                                    $ups_sd[] = [
                                        'TrackingNumber' => $label_detail['TrackingNumber']
                                    ];
                                }

                                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-ups-spq-' . $package;
                                $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';
                                $base64_string_png = isset($label_detail['LabelImage'], $label_detail['LabelImage']['GraphicImage']) ? $label_detail['LabelImage']['GraphicImage'] : '';
                                if (strlen($base64_string_png) > 0) {
                                    file_exists($png) ? unlink($png) : '';
                                    file_put_contents($png, base64_decode($base64_string_png));
                                }

                                if (file_exists($png)) {
                                    $ups_labels[] = $png_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.png';
                                    $ups_sl .= '<img class="mso_real_label_image" onclick="mso_file_to_click(this,1)" src="' . esc_url($png_to_show) . '" alt="Label Missing" alt="Missing Label"/>';
                                }
                            }
                        }

                        echo $ups_sl;
                        echo '</div>';

                        if (!empty($ups_sd)) {
                            // TODO
//                            echo '<span data-carrier="ups" data-post_data="' . http_build_query($ups_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
                            echo '<span data-carrier="ups" data-post_data="' . implode(',', $ups_labels) . '" class="form-control button-primary mso_print_all_shipment">Print All Shipment</span>';
                        }

                        // API response
                        (isset($response['confirm'])) ? $mso_api_response['confirm'] = $response['confirm'] : '';
                        (isset($response['accept'])) ? $mso_api_response['accept'] = $response['accept'] : '';

                        break;
                    case 'ups_lfq':
                        $response = isset($mso_shipment['ups_lfq_ship']) ? $this->mso_parsing_build_query($mso_shipment['ups_lfq_ship']) : [];
                        // Error handling
                        if (isset($response['detail'], $response['detail']['Errors'], $response['detail']['Errors']['ErrorDetail'], $response['detail']['Errors']['ErrorDetail']['PrimaryErrorCode'], $response['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'])) {
                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'] . '</span></br>';
                        }

                        echo '<div class="mso_ship_label_content">';
                        $ups_lfq_sd = [];
                        $ups_lfq_sl = '';
                        if (isset($response['ShipmentResults'], $response['ShipmentResults'])) {
                            $package_results = $response['ShipmentResults'];
                            (isset($package_results['Documents'])) ? $package_results = [$package_results] : '';
                            foreach ($package_results as $label_key => $package) {
                                if (isset($package['ShipmentNumber'], $package['BOLID'])) {
                                    $ups_lfq_sd[] = [
                                        'ShipmentNumber' => $package['ShipmentNumber'],
                                        'BOLID' => $package['BOLID']
                                    ];
                                }

                                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-ups-lfq-' . $label_key;
                                $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
                                $base64_string_pdf = isset($package['Documents'], $package['Documents']['Image'], $package['Documents']['Image']['GraphicImage']) ? $package['Documents']['Image']['GraphicImage'] : '';
                                if (strlen($base64_string_pdf) > 0) {
                                    $ifp = fopen($pdf, 'wb');
                                    fwrite($ifp, base64_decode($base64_string_pdf));
                                    fclose($ifp);
                                }

                                if (file_exists($pdf)) {
                                    $pdf_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.pdf';
                                    $ups_lfq_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url($pdf_icon) . '" alt="Label Missing" alt="Missing Label"/>';
                                }
                            }
                        }

                        echo $ups_lfq_sl;
                        echo '</div>';

                        if (!empty($ups_lfq_sd)) {
                            // TODO
//                            echo '<span data-carrier="ups_lfq" data-post_data="' . http_build_query($ups_lfq_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
                        }

                        // API response
                        (isset($response['ShipmentResults'])) ? $mso_api_response = $response['ShipmentResults'] : '';

                        break;
                    case 'fedex_lfq':
                        echo '<div class="mso_ship_label_content">';
                        $package_shipments = isset($mso_shipment['fedex_lfq_ship']) ? $this->mso_parsing_build_query($mso_shipment['fedex_lfq_ship']) : [];
                        $fedex_lfq_sd = [];
                        $fedex_lfq_sl = '';
                        foreach ($package_shipments as $package_number => $response) {
                            // Error handling
                            if (isset($response['HighestSeverity'], $response['Notifications'], $response['Notifications']['Message']) && ($response['HighestSeverity'] == 'FAILURE' || $response['HighestSeverity'] == 'ERROR')) {
                                echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['Notifications']['Message'] . '</span></br>';
                            }

                            $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-fedex-lfq';
                            $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';

                            if (isset($response['HighestSeverity']) && $response['HighestSeverity'] != 'FAILURE' && $response['HighestSeverity'] != 'ERROR') {

                                if (isset($response['CompletedShipmentDetail'], $response['CompletedShipmentDetail']['CompletedPackageDetails'], $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'])) {
                                    $fedex_lfq_sd[] = [
                                        'TrackingIdType' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingIdType'],
                                        'TrackingNumber' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber']
                                    ];
                                }

                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'])) {
                                    $base64_string_pdf = $response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'];
                                    $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
                                    $data = $base64_string_pdf;
                                    file_put_contents($pdf, $data);
                                }

                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'])) {
                                    $base64_string_pdf = $response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'];
                                    $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
                                    $data = $base64_string_pdf;
                                    file_put_contents($pdf, $data);
                                }

                                if (file_exists($pdf)) {
                                    $pdf_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.pdf';
                                    $fedex_lfq_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url($pdf_icon) . '" alt="Label Missing" alt="Missing Label"/>';
                                }
                            }
                        }

                        echo $fedex_lfq_sl;
                        echo '</div>';
                        if (!empty($fedex_lfq_sd)) {
                            // TODO
//                            echo '<span data-carrier="fedex_lfq" data-post_data="' . http_build_query($fedex_lfq_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
                        }
                        // API response
                        isset($package_shipments) ? $mso_api_response = $package_shipments : '';
                        break;
                }
            } else if (isset($mso_shipment['service_error']) && strlen($mso_shipment['service_error']) > 0) {
                echo '<span class="mso_rate_error_message">' . $mso_shipment['service_error'] . '</span>';
            }
            echo '</div>'; // Close label showing div

            // API response
            $carriers_name = [
                'ups_rate' => 'UPS Small Package Shipping',
                'fedex_rate' => 'Fedex Small Package Shipping',
                'ups_lfq_rate' => 'UPS LTL Freight Shipping',
                'fedex_lfq_rate' => 'Fedex LTL Freight Shipping',
            ];
            echo '<div class="mso_order_api_response mso_order_tab">';
            echo '<p class="mso_calculate_shipping_heading">API Response</p>';
            // Created shipment response
            if (isset($mso_api_response) && !empty($mso_api_response)) {
                if (strlen($mso_carrier_name) > 0) {
                    $mso_carrier_name .= '_rate';
                    if (isset($carriers_name[$mso_carrier_name])) {
                        echo "<h2 class='mso_carrier_name'>$carriers_name[$mso_carrier_name] <span title='Created Shipment' class='mso_rcn'>S</span> </h2>";
                        $this->mso_show_shipment_api_data($mso_api_response);
                    }
                }

            } else {
                // Calculated rates respone
                if (isset($mso_shipment['response']) && !empty($mso_shipment['response'])) {
                    foreach ($mso_shipment['response'] as $rate_key => $rate_data) {
                        if (isset($carriers_name[$rate_key])) {
                            echo "<h2 class='mso_carrier_name'>$carriers_name[$rate_key] <span title='Calculated Rate' class='mso_rcn'>R</span> </h2>";
                            $this->mso_show_shipment_api_data($rate_data);
                        }
                    }
                }
            }
            echo '</div>';

            // Shipment Packages
            $cost = (isset($mso_shipment['cost'])) ? $mso_shipment['cost'] : '';
            $append_subscription_status = !MSO_DONT_AUTH ? $this->mso_subscription_status : '';
            echo '<div class="mso_order_package mso_order_tab"><p class="mso_calculate_shipping_heading">Packaging</br> ' . $append_subscription_status . '</p>';
            if ($this->subscription_boolean || MSO_DONT_AUTH) {
                if (isset($mso_shipment['response'], $mso_shipment['response']['packed_items'])) {
                    $shipment_packages = $this->mso_order_package($mso_shipment);
                    echo $shipment_packages;
                } else if (isset($mso_shipment['packed_items']) && $cost > 0) {
                    $shipment_packages = $this->mso_order_package(['response' => $mso_shipment]);
                    echo $shipment_packages;
                }
            }
            echo '</div>';


            // Shipping rates
            $label = (isset($mso_shipment['label'])) ? $mso_shipment['label'] : '';
            $currency_symbol = MSO_CURRENCY_SYMBOL;
            $rate_checkout_order = ($cost > 0) ? "$label: $currency_symbol$cost" : "Shipping rates will be show here";
            $rate_checkout_order = '<span class="mso_shipping_rates_block">' . $rate_checkout_order . '</span>';
            isset($mso_shipment['error'], $mso_shipment['message']) ? $rate_checkout_order = '<span class="mso_rate_error_message"><b>Error! </b>' . $mso_shipment['message'] . '</span>' : '';
            echo '<div class="mso_order_rates mso_order_tab"><p class="mso_calculate_shipping_heading">Rates</p>' . $rate_checkout_order . '</div>';
            echo '</div>';
            // Main shipments loop closed here
            echo '</div>';
            echo '<div style="clear: both"></div>';

//            echo '<hr class="mso_hr">';
            echo '</form>';

            $mso_ship_num++;
        }

        if (isset($mso_order_items) && !empty($mso_order_items)) {
            $mso_shipments = [];
            $mso_shipments[] = [
                'response' => [
                    'items' => $mso_order_items,
                    'ship_from' => [],
                ],
                'accessorials' => [],
            ];
            $this->mso_order_shipments_list($mso_shipments, $location_options, $order_id, $mso_order_items, $mso_ship_num, $ship_to_address_label);
        }
    }

    // Shipment API data
    public function mso_show_shipment_api_data($shipment_results)
    {
        echo '<table class="mso_api_response_table" border="1px solid">';
        foreach ($shipment_results as $key => $detail) {
            echo '<tr>';
            echo '<td>' . $key . '</td>';
            if (is_array($detail)) {
                echo '<td>';
                echo '<details>';
                echo '<summary>Expand me</summary>';
                echo '<pre>';
                print_r($detail);
                echo '</pre>';
                echo '</details>';
                echo '</td>';
            } else {
                echo '<td>' . mb_strimwidth($detail, 0, 35, "...") . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    // Packages
    public function mso_order_package($mso_shipment)
    {
        $shipment_packages = '';
        foreach ($mso_shipment['response']['packed_items'] as $package_no => $package_detail) {
            $type = $title = $width = $length = $height = $weight = $quantity = $package = '';
            extract($package_detail);
            $front_name = $package . 'Box';
            $front_width = '100px';
            $bin_to_show = MSO_DIR_FILE . '/images/bin.png';
            if ($type == 'pallet') {
                $front_width = '100x';
                $front_name = $package . 'Pallet';
                $bin_to_show = MSO_DIR_FILE . '/images/pallet.png';
            }

            $dimensions = "$length <b>X</b> $width <b>X</b> $height";
//            $package_detail = "$front_name Name: $title Quantity: $quantity Dimensions: $dimensions Weight: $weight";
//            $package_detail = "<table style='border: 1px solid'><tr><th>$front_name Name</th><th>Dimensions</th><th>Quantity</th><th>Weight</th></tr><tr><td>$title</td><td>$dimensions</td><td>$quantity</td><td>$weight</td></tr></table>";
            ob_start();
            ?>
            <table border="1px solid" class="mso_order_packages_tip">
                <tr>
                    <td><?php echo $front_name; ?></td>
                    <td><?php echo $title; ?></td>
                </tr>
                <tr>
                    <td>Dimensions</td>
                    <td><?php echo $dimensions; ?></td>
                </tr>
                <tr>
                    <td>Quantity</td>
                    <td><?php echo $quantity; ?></td>
                </tr>
                <tr>
                    <td>Weight</td>
                    <td><?php echo $weight; ?></td>
                </tr>
            </table>
            <?php
            $package_detail = ob_get_clean();
//            $shipment_packages .= '<img src="' . esc_url($bin_to_show) . '" height="70px" width="' . $front_width . '">' . "<span class='mso_order_package_content'>$dimensions</span><span class='woocommerce-help-tip' data-tip='.$package_detail.'></span>";
            $shipment_packages .= '<img src="' . esc_url($bin_to_show) . '" height="70px" width="' . $front_width . '">' . "<span class='mso_order_package_content'>$dimensions</span>";
            // Custom tooltip
            // https://codepen.io/rudeayelo/pen/DWNyxg
            $shipment_packages .= '<span class="mso_wrapper"><span class="mso_tooltip">' . $package_detail . '</span></span>';
        }

        return $shipment_packages;
    }
}

