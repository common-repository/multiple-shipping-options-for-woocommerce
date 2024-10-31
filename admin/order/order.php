<?php

namespace MsoOrder;

use MsoOrderShippingOptions\MsoOrderShippingOptions;
use MsoProductDetail\MsoProductDetail;
use MsoPackage\MsoPackage;
use WasaioCurl\WasaioCurl;

/**
 * Order show on admin side.
 * Class MsoOrder
 */
class MsoOrder extends MsoOrderShippingOptions
{
    public $mso_shipment_meta_k = '';
    public $mso_shipment_meta_v = [];
    public $mso_subscription_status = '';
    public $subscription_boolean = false;

    // Cancel shipment
    public function mso_cancel_shipment_hook()
    {
        $post_data = [];
        $order_id = (isset($_POST['mso_order_id'])) ? sanitize_text_field($_POST['mso_order_id']) : '';
        $mso_ship_num = (isset($_POST['mso_ship_num'])) ? sanitize_text_field($_POST['mso_ship_num']) : '';
        $carrier = (isset($_POST['mso_carrier'])) ? sanitize_text_field($_POST['mso_carrier']) : '';
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
        // Get shipment from db if exist  
        if (!strlen($mso_shipment_order_ship) > 0) {
            $response = mso_get_shipment_by_order_id($order_id);
            if ($response !== null) {
                $mso_shipment_order_ship = $response;
            }
        }

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
        $ship_to = (isset($_POST['mso_ship_to_address'])) ? sanitize_text_field($_POST['mso_ship_to_address']) : '';
        $order_id = (isset($_POST['mso_order_id'])) ? sanitize_text_field($_POST['mso_order_id']) : 0;
        $ship_action = (isset($_POST['ship_action'])) ? sanitize_text_field($_POST['ship_action']) : '';

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
        $ship_to_country = isset($ship_to['country']) ? $ship_to['country'] : '';

        // Shipment disabled
        $mso_last_access = $this->mso_shipments_getting_data($order_id);

        // Locations
        $mso_product_detail = new MsoProductDetail();
        $locations = $mso_product_detail->mso_locations();
        $origin_id_list = [];

        $ship_confirm_html = '<div class="ship_confirmation_dialog">';
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

            $ship_confirm_html .= "<h3>Shipment $mso_ship_num </h3>";
            $ship_confirm_html .= "<div class='ship_confirmation_child' data-ship_package='" . $mso_ship_num . "'>";

            if (isset($shipment['origin'], $locations[$shipment['origin']]) && $shipment['origin'] != 'store_address') {
                $shipment_origin = sanitize_text_field($shipment['origin']);
                $origin_id = $shipment_origin;
                $location = $locations[$shipment_origin];
                $address_1 = $mso_address = $mso_zip = $mso_city = $mso_state = $mso_country = '';
                extract($location);
                $address_1 = strlen($address_1) > 0 ? $address_1 : $mso_address;
                $origin = [
                    'id' => $origin_id,
                    'address' => $mso_address,
                    'address_1' => $address_1,
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

            $origin = mso_label_specifications($origin);
            $ship_from_country = isset($origin['country']) ? $origin['country'] : '';
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
                    $dhl_product_code = $carrier = $ups_spq_code = $usps_spq_code = $container = $fedex_spq_service = $packaging_type = $fedex_lfq_service = '';
                    extract($selected_rate);
                    $ship_label = isset($selected_rate['label']) ? $selected_rate['label'] : '';
                    $ship_cost = isset($selected_rate['cost']) ? $selected_rate['cost'] : '';

                    $shipment_settings = isset($shipment['shipment_settings']) ? $shipment['shipment_settings'] : [];
                    $ship_confirm_html .= '<div class="ship_confirmation_child_field">';
                    $international_ship = strlen($ship_from_country) > 0 && strlen($ship_to_country) > 0 && $ship_from_country != $ship_to_country ? true : false;

                    switch ($selected_rate['carrier']) {
                        case 'fedex':
                            $shipment_action = 'spq';
                            $rate = [
                                'order_id' => $order_id,
                                'carrier' => $carrier,
                                'fedex_spq_service' => $fedex_spq_service,
                                'packaging_type' => $packaging_type,
                                'shipment_settings' => $shipment_settings
                            ];
                            break;
                        case 'ups':
                            $shipment_action = 'spq';
                            $rate = [
                                'order_id' => $order_id,
                                'carrier' => $carrier,
                                'ups_spq_code' => $ups_spq_code,
                                'shipment_settings' => $shipment_settings
                            ];
                            break;
                        case 'usps':
                            $shipment_action = 'spq';
                            $rate = [
                                'order_id' => $order_id,
                                'carrier' => $carrier,
                                'container' => $container,
                                'usps_spq_code' => $usps_spq_code,
                                'shipment_settings' => $shipment_settings
                            ];
                            break;
                        case 'dhl':
                            $shipment_action = 'spq';
                            $rate = [
                                'order_id' => $order_id,
                                'carrier' => $carrier,
                                'dhl_product_code' => $dhl_product_code,
                                'shipment_settings' => $shipment_settings
                            ];

                            if ($international_ship) {
                                $ship_confirm_html .= "<h4>DHL Commodity Code</h4>";
                                $ship_confirm_html .= "<div class='description'>You can find this information on the government website (<a target='_blank' href='https://www.gov.uk/trade-tariff'>https://www.gov.uk/trade-tariff</a>) or by using the <a target='_blank' href='https://www.dhl.com/global-en/microsites/express/mygts.html'>Import Duty & Customs Duty Calculator on MyGTS - Global (dhl.com)</a> .</div>";
                                $ship_confirm_html .= "<input placeholder='DHL Commodity Code' class='shipment_settings' name='dhl_commodity_code' type='text'>";
                            }

                            break;
                        case 'fedex_lfq':
                            $shipment_action = 'lfq';
                            $rate = [
                                'order_id' => $order_id,
                                'carrier' => $carrier,
                                'fedex_lfq_service' => $fedex_lfq_service,
                                'shipment_settings' => $shipment_settings
                            ];
                            break;
                        case 'ups_lfq':
                            $shipment_action = 'lfq';
                            $rate = [
                                'order_id' => $order_id,
                                'carrier' => $carrier,
                                'ups_lfq_code' => '308',
                                'shipment_settings' => $shipment_settings
                            ];
                            break;
                    }

                    $ship_confirm_html .= '</div>';
                }
            }

            $ship_confirm_html .= "<h4>Origin Address</h4>";
            $address_1 = $city = $state = $postcode = $country = '';
            extract($origin);
            $ship_confirm_html .= "<p>$address_1, $city, $state, $postcode, $country</p>";
            $ship_confirm_html .= "<h4>Destination Address</h4>";
            $address_1 = $city = $state = $postcode = $country = '';
            extract($ship_to);
            $ship_confirm_html .= "<p>$address_1, $city, $state, $postcode, $country</p>";

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
                    $quantity = $length = $width = $height = $package = $type = $title = '';
                    $weight = 0;
                    extract($bin);

                    $front_name = $package . ucwords($type);

                    $bin_count = $bin_key + 1;
//                    $ship_confirm_html .= "<h4>Package $bin_count [Quantity X (Length), (Width), (Height)]</h4>";
//                    $ship_confirm_html .= "<p><b>$quantity X</b> $length (L), $width (W), $height (H)</p>";

                    $ship_confirm_html .= "<h4>Package $bin_count</h4>";
                    $ship_confirm_html .= "<table border='1px solid'>";
                    $ship_confirm_html .= "<tr><th rowspan='2'>$front_name</th><th rowspan='2'>Quantity</th><th colspan='3'>Dimensions (In)</th><th rowspan='2'>Weight(LBS)</th></tr>";
                    $ship_confirm_html .= "<tr><th>L</th><th>W</th><th>H</th></tr>";
                    $ship_confirm_html .= "<tr><td>$title</td><td>$quantity</td><td>$length</td><td>$width</td><td>$height</td><td>$weight</td></tr>";
                    $ship_confirm_html .= "</table>";

//                    $weight = (isset($bin['weight'])) ? $bin['weight'] : 0;
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

            $ship_confirm_html .= "</div>";
        }

        if ($ship_action == 'confirmation') {
            $ship_confirm_html .= '</div>';
            echo $ship_confirm_html;
            die;
        }

//        echo '<pre>';
//        print_r($shipments);
//        echo '</pre>';
//        die;
        $response = MsoPackage::mso_request_settings($shipments, $ship_to, 'ship');
//        echo $response; die;
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

        // Insert shipment for a specific order ID  
        mso_insert_shipment($order_id, 'open', 'mso_shipment_order_ship', json_encode($getting_rates));
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

        // Get shipment from db if exist  
        if (empty($mso_last_access)) {
            $response = mso_get_shipment_by_order_id($order_id);
            if ($response !== null) {
                $mso_order_ship = json_decode($response, true);
                $this->mso_shipment_meta_k = 'mso_shipment_order_ship';
                $this->mso_shipment_meta_v = $mso_order_ship;
                $mso_last_access = $mso_order_ship;
            }
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
//        $this->mso_subscriptions_status();
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
            if (isset($shipment['origin'], $locations[$shipment['origin']]) && $shipment['origin'] != 'store_address') {
                $shipment_origin = sanitize_text_field($shipment['origin']);
                $origin_id = $shipment_origin;
                $location = $locations[$shipment_origin];
                $mso_zip = $mso_city = $mso_state = $mso_country = $address_1 = $mso_address = '';
                extract($location);
                $address_1 = strlen($address_1) > 0 ? $address_1 : $mso_address;
                $origin = [
                    'id' => $origin_id,
                    'address_1' => $address_1,
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

            $origin = mso_label_specifications($origin);
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

//        echo '<pre>';
//        print_r($shipments);
//        echo '</pre>';
//        die;
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
        $error_message = isset($mso_rates['error'], $mso_rates['message']) ? $mso_rates['message'] : '';

//        $rates_output = json_decode($actual_response, true);
//        $accessorials = (isset($rates_output['accessorials'])) ? $rates_output['accessorials'] : [];
//        $shipments = (isset($rates_output['shipments'])) ? $rates_output['shipments'] : [];

        $carriers_name = [
            'dhl_rate' => 'DHL Package Shipping',
            'ups_rate' => 'UPS Small Package Shipping',
            'usps_rate' => 'USPS Small Package Shipping',
            'fedex_rate' => 'Fedex Small Package Shipping',
            'ups_lfq_rate' => 'UPS LTL Freight Shipping',
            'fedex_lfq_rate' => 'Fedex LTL Freight Shipping',
        ];

        $mso_ship_rates = $api_response = [];
        $rates_returning = false;
//        if (!empty($shipments) && is_array($shipments)) {
        if (!empty($mso_rates) && is_array($mso_rates)) {
//            $mso_rates = MsoPackage::mso_shipment_rates($shipments, $accessorials, $ship_to, true);
//            if (isset($mso_rates['shipments'])) {
//                unset($mso_rates['shipments']);
//            }

            $mso_package_obj = new MsoPackage();
            $already_checked = [];
            foreach ($mso_rates as $mso_ship_num => $mso_carrier_rates) {
                if (isset($mso_carrier_rates['spq']) || isset($mso_carrier_rates['lfq'])) {
                    $mso_carrier_rates = reset($mso_carrier_rates);
                }

                foreach ($mso_carrier_rates as $carrier_name => $mso_carrier_rate) {
                    if (isset($mso_carrier_rate['error'], $mso_carrier_rate['message'])) {
                        !isset($mso_ship_rates[$mso_ship_num]) ? $mso_ship_rates[$mso_ship_num] = $mso_carrier_rate : '';
                        $unshipment_rates[$mso_ship_num] .= '<span class="mso_rate_error_message"><b>Error! </b>' . $mso_carrier_rate['message'] . '</span></br>';
                    }

                    $mso_carrier_rate = $mso_package_obj->mso_sort_asec($mso_carrier_rate, 'cost');;
                    foreach ($mso_carrier_rate as $key => $rate) {
                        $mso_ship_rates[$mso_ship_num] = $rate;

                        // Calculated API response
                        if (isset($rate['response']) && !empty($rate['response']) && !isset($api_response[$mso_ship_num])) {
                            $api_response[$mso_ship_num] = '';
                            foreach ($rate['response'] as $rate_key => $rate_data) {
                                if (isset($carriers_name[$rate_key]) && !empty($rate_data)) {
                                    $api_response[$mso_ship_num] .= "<h2 class='mso_carrier_name'>" . htmlspecialchars($carriers_name[$rate_key], ENT_QUOTES) . " <span title='Calculated Rate' class='mso_rcn'>R</span> </h2>";
                                    $api_response[$mso_ship_num] .= $this->mso_show_shipment_api_data($rate_data, true);
                                }
                            }
                        }


                        if (isset($rate['response'], $rate['response']['packed_items'])) {
                            $shipment_packages = $this->mso_order_package($rate);
//                            $status = strlen($this->mso_subscription_status) > 0 ? ' (' . $this->mso_subscription_status . ')' : '';
                            $mso_order_package[$mso_ship_num] = '<p class="mso_calculate_shipping_heading">Packaging</p>&nbsp;' . $shipment_packages;
                        }

                        $cost = (isset($rate['cost'])) ? $rate['cost'] : 0;
                        $is_paid = (isset($rate['is_paid'])) ? $rate['is_paid'] : '';
//                        $disabled = !MSO_DONT_AUTH && !$is_paid ? 'disabled' : '';
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
                            $is_checked = '';
//                            $is_checked = $rates_count === 0 && $disabled != 'disabled' ? 'checked="checked"' : '';
                            if (!isset($already_checked[$mso_ship_num])) {
                                $is_checked = 'checked="checked"';
                                $already_checked[$mso_ship_num] = true;
                            }

                            // Error handling
//                            $mso_paid_plan_feature = '';
//                            if ($disabled == 'disabled') {
//                                $disabled = 'disabled="disabled"';
//                                $mso_paid_plan_feature = '<span class="mso_order_wrapper mso_wrapper"><span class="mso_tooltip">' . MSO_PAID_PLAN_FEATURE . '</span></span>';
//                            }
                            $shipment_rates[$mso_ship_num] .= '<input data-rate="' . http_build_query($rate) . '" type="radio" id="" name="mso_order_rate" value="' . esc_attr($service_id) . '" ' . esc_attr($is_checked) . '><label for="mso_order_rate">' . esc_html($mso_quote) . '</label>' . $mso_paid_plan_feature . '<br>';
                        }
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
            'rates_returning' => $rates_returning,
            'error_message' => $error_message,
            'api_response' => $api_response
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

//    /**
//     * Subscription status
//     */
//    public function mso_get_carriers_plan_status()
//    {
//        if (MSO_DONT_AUTH) {
//            return;
//        }
//
//        $plan_status = 'Your current plan subscription is inactive. ' . MSO_SUBS_CLICK_HERE . ' to start the trial plan.';
//        if (!empty(MSO_SUBSCRIPTIONS)) {
//            $carriers = [];
//            foreach (MSO_SUBSCRIPTIONS as $key => $subscription) {
//                $carrier = $plan_active = '';
//                extract($subscription);
//                if ($plan_active == 'yes') {
//                    $carriers[] = $carrier;
//                }
//            }
////            $mso_subscription_status = "Please note that the following feature is only available for your paid carriers, such as " . mso_implode_carriers($carriers) . ", will be effective in controlling order shipments and allowing for packages.";
//            $plan_status = sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers));
////            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>it is only possible to monitor the shipment of orders through your paid carriers such as " . mso_implode_carriers($carriers) . ".</p></div>" : '';
////            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>" . sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers)) . "</p></div>" : '';
//        }
//
//        return '<span class="notice notice-warning mso_plan_status_warning">' . $plan_status . '</span>';
//    }


//    public function mso_subscriptions_status()
//    {
//        if (MSO_DONT_AUTH) {
//            return;
//        }
//
//        $subscription_boolean = false;
//        $status_description = $mso_subscription_status = '';
//        if (MSO_PLAN_STATUS != 'success' || empty(MSO_SUBSCRIPTIONS)) {
//            $mso_subscription_status = MSO_PAID_PLAN_FEATURE_DIALOG;
//            $status_description = '<span class="mso_err_status_description">' . MSO_PAID_PLAN_FEATURE . '</span>';
//        }
//
//        if (!empty(MSO_SUBSCRIPTIONS)) {
//            $subscription_boolean = true;
//            $carriers = [];
//            foreach (MSO_SUBSCRIPTIONS as $key => $subscription) {
//                $carriers[] = isset($subscription['carrier']) ? $subscription['carrier'] : '';
//            }
////            $mso_subscription_status = "Please note that the following feature is only available for your paid carriers, such as " . mso_implode_carriers($carriers) . ", will be effective in controlling order shipments and allowing for packages.";
//            $mso_subscription_status = sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers));
////            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>it is only possible to monitor the shipment of orders through your paid carriers such as " . mso_implode_carriers($carriers) . ".</p></div>" : '';
//            $status_description = !empty($carriers) ? "<div class='mso_order_details_plan'><p>" . sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers)) . "</p></div>" : '';
//        }
//
//        $this->mso_subscription_status = $mso_subscription_status;
//        $this->subscription_boolean = $subscription_boolean;
//
//        return $status_description;
//    }

    /**
     * Setting Order For Woocommerce
     */
    public function mso_order($actions)
    {
//        $status_description = $this->mso_subscriptions_status();
//        add_meta_box('mso_order', __('Order Details ' . $status_description . ' <span class="form-control button-primary mso_create_shipment_block">New Shipment</span>', 'woocommerce'), [$this, 'mso_order_shipping'], 'shop_order', 'normal', 'core');
        add_meta_box('mso_order', __('<span>Multiple Shipping Options for WooCommerce</span>', 'woocommerce'), [$this, 'mso_order_shipping'], 'shop_order', 'normal', 'core');
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

        echo mso_get_carriers_plan_status('p2');
        echo mso_get_carriers_plan_status('p3');

        echo '<h3 class="mso_order_id" value="' . esc_attr($order_id) . '">' . esc_html("Order #" . $order_id) . '</h2>';

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

        // Get shipment from db if exist  
        if (empty($mso_order_ship)) {
            $response = mso_get_shipment_by_order_id($order_id);
            if ($response !== null) {
                $mso_order_ship = json_decode($response, true);
            }
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

        // Get shipment from db if exist  
        if (!strlen($mso_shipment_order_ship_last_one) > 0) {
            $mso_shipment_order_ship_backup_last_one = get_post_meta($order_id, 'mso_shipment_order_ship_backup', true);
            if (!strlen($mso_shipment_order_ship_backup_last_one) > 0) {
                $response = mso_get_shipment_by_order_id($order_id);
                if ($response !== null) {
                    $mso_shipment_order_ship_last_one = $response;
                }
            }
        }

        ?>
        <table class="form-table"></table>
        <!--        <hr class="mso_hr">-->
        <?php if (!strlen($mso_shipment_order_ship_last_one) > 0) { ?>
        <!--        <div class="bootstrap-iso form-wrp">-->
        <div class="row mso_shipment_actions">

            <div class="mso_add_new_shipment_button">
                <span class="form-control button-primary mso_create_shipment_block">+ Add new shipment</span>
                <span class="mso_add_shipment_wrapper mso_wrapper">
                <span class="mso_tooltip"><?php echo MSO_ADD_SHIPMENT_DESC; ?></span>
            </span>
            </div>

            <div class="mso_shipping_method_buttons">
                <button type="button" onclick="mso_order_get_quote()" class="button-primary mso_order_get_quote">
                    Calculate Shipping
                </button>
            </div>

            <?php
            //            if (MSO_PLAN_STATUS == 'error' && !MSO_DONT_AUTH) {
            //                echo strlen(MSO_KEY_STATUS) > 0 ? MSO_KEY_STATUS : MSO_KEY_ERROR;
            //            } elseif (!strlen(MSO_PLAN_STATUS) > 0 && !strlen(MSO_KEY_STATUS) > 0 && !MSO_DONT_AUTH) {
            //                echo MSO_KEY_ERROR;
            //            } else {
            //                ?>
            <!--                <button type="button" onclick="mso_order_get_quote()" class="button-primary mso_order_get_quote">-->
            <!--                    Calculate Shipping-->
            <!--                </button>-->
            <!--            --><?php //} ?>
            <!--            </div>-->
        </div>
        <?php
    } else {
        ?>
        <!--        <div class="bootstrap-iso form-wrp">-->
        <div class="row mso_shipment_actions" style="color: red;">
            <p style="text-align: center; margin-top: 15px;"><b>Note! </b> A shipment has been created. click <a
                        type="button" onclick="mso_order_recreate_shipment_allow(<?php echo $order_id; ?>)"
                        class="mso_order_recreate_shipment_allow">here</a> to recreate the shipment.
            </p>
        </div>
        <!--        </div>-->
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
        <div class="mso_order_shipment_proceed_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <div class="row">
                    <div class="mso_proceed_message">
                        <p>Are you sure you want to proceed with the shipment?</p>
                    </div>
                    <div class="mso_warning_buttons">
                        <a class="form-control mso_order_link mso_proceed_shipment_done button-primary mso_button">Yes,
                            proceed it</a>

                        <a onclick="mso_order_proceed_ship_overly_hide()"
                           class="form-control mso_button_cancel mso_order_link button-primary mso_button">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mso_order_shipment_delete_warning_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <!--                <a onclick="mso_order_asset_delete_warning_overly_hide()" class="close">×</a>-->
                <div class="row">
                    <div class="mso_delete_message">
                        <p>Are you sure you want to delete the shipment?</p>
                    </div>
                    <div class="mso_warning_buttons">
                        <a class="form-control mso_order_link mso_delete_shipment_done button-primary mso_button">Yes,
                            delete it</a>

                        <a onclick="mso_order_asset_delete_warning_overly_hide()"
                           class="form-control mso_button_cancel mso_order_link button-primary mso_button">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mso_order_item_delete_warning_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <!--                <a onclick="mso_order_asset_delete_warning_overly_hide()" class="close">×</a>-->
                <div class="row">
                    <div class="mso_delete_message">
                        <p>Are you sure you want to delete the item?</p>
                    </div>
                    <div class="mso_warning_buttons">
                        <a class="form-control mso_order_link mso_delete_order_item_done button-primary mso_button">Yes,
                            delete it</a>

                        <a onclick="mso_order_asset_delete_warning_overly_hide()"
                           class="form-control mso_button_cancel mso_order_link button-primary mso_button">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

