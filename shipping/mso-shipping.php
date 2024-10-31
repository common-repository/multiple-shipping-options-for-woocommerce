<?php

//namespace MsoShipping;

if (!class_exists('MsoShippingInit')) {
    class MsoShippingInit
    {
        public function __construct()
        {
            add_action('woocommerce_shipping_init', 'mso_shipping_init');
        }
    }
}

if (!function_exists('mso_shipping_init')) {
    function mso_shipping_init()
    {
        class MsoShipping extends WC_Shipping_Method
        {
            public $mso_final_rates = [];
            public $cart_error_message = '';
            public $mso_package_obj;

            /**
             * Hook for call
             * MsoShipping constructor.
             * @param int $instance_id
             */
            public function __construct($instance_id = 0)
            {
                $this->id = 'mso';
                $this->instance_id = absint($instance_id);
                $this->method_title = __('Multiple Shipping Options for WooCommerce');
                $this->method_description = __('Shipping rates through Multiple Shipping Options for WooCommerce.');
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->enabled = "yes";
                $this->title = 'Multiple Shipping Options for WooCommerce';
                $this->init();
            }

            /**
             * Let's start init function
             */
            public function init()
            {
                $this->init_form_fields();
                $this->init_settings();
                add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
            }

            /**
             * Enable woocommerce shipping for mso
             */
            public function init_form_fields()
            {
                $this->instance_form_fields = [
                    'enabled' => [
                        'title' => __('Enable / Disable', 'mso'),
                        'type' => 'checkbox',
                        'label' => __('Enable This Shipping Service', 'mso'),
                        'default' => 'yes',
                        'id' => 'mso_enable_disable_shipping'
                    ]
                ];
            }

            /**
             * Custom error message
             * @param string $message
             * @return string
             */

            public function mso_default_cart_error_message($message)
            {
                return $this->cart_error_message;
            }

            /**
             * Calculate shipping rates woocommerce
             * @param array $package
             * @return array|void
             */
            public function calculate_shipping($package = [])
            {
//                $mso_rates = [];
//                $error_from_api_detected = false;
//                $this->mso_package_obj = new MsoPackage\MsoPackage();
                $rates = \MsoPackage\MsoPackage::mso_init($package);
//                $running_request = $this->mso_package_obj::$running_request;
                $rates_output = json_decode($rates, true);
                if (isset($_REQUEST['mso_errors'])) {
                    // Encoded
                    echo '<details>';
                    echo '<summary>MSO Encoded</summary>';
                    echo '<pre>';
                    print_r($rates);
                    echo '</pre>';
                    echo '</details>';

                    // Decoded
                    echo '<details>';
                    echo '<summary>MSO Decoded</summary>';
                    echo '<pre>';
                    print_r($rates_output);
                    echo '</pre>';
                    echo '</details>';
                }

                // Custom error message
                if (isset($rates_output['error'], $rates_output['message'])) {
                    $this->cart_error_message = $rates_output['message'];
                    add_filter('mso_default_cart_error_message', [$this, 'mso_default_cart_error_message'], 10, 1);
                }

                $this->mso_add_rates($rates_output);

//                $accessorials = (isset($rates_output['accessorials'])) ? $rates_output['accessorials'] : [];
//                $shipments = (isset($rates_output['shipments'])) ? $rates_output['shipments'] : [];
//                $ship_to = (isset($rates_output['ship_to'])) ? $rates_output['ship_to'] : [];
//
//                if (!empty($shipments) && is_array($shipments)) {
//                    $mso_rates = \MsoPackage\MsoPackage::mso_shipment_rates($shipments, $accessorials, $ship_to, false);
//                    $mso_rates_count = count($mso_rates);
//                    if (!empty($mso_rates) && $mso_rates_count > 1) {
//                        $mso_get_lfq_rates = $mso_get_spq_rates = [];
//                        $mso_get_lfq_rates_count = $mso_get_spq_rates_count = 0;
//                        foreach ($mso_rates as $zip => $mso_rate) {
//                            $mso_lfq_rates = (isset($mso_rate['lfq'])) ? $mso_rate['lfq'] : [];
//                            $mso_spq_rates = (isset($mso_rate['spq'])) ? $mso_rate['spq'] : [];
//                            if (!empty($mso_lfq_rates)) {
//                                $mso_get_empty_lfq_rates = [];
//                                $mso_get_lfq_rates_count++;
//                                foreach ($mso_lfq_rates as $lfq_carrier => $lfq_carrier_rates) {
//                                    if (isset($lfq_carrier_rates['error'], $lfq_carrier_rates['message'])) {
//                                        $error_from_api_detected = true;
//                                        $mso_get_empty_lfq_rates[$zip] = $lfq_carrier_rates;
//                                    } else {
//                                        $lfq_carrier_rates = $this->mso_package_obj->mso_sort_asec($lfq_carrier_rates, 'cost');
//                                        $lfq_rate = array_slice($lfq_carrier_rates, 0, 1);
//                                        $mso_get_lfq_rates[$zip][] = reset($lfq_rate);
//                                    }
//                                }
//
//                                foreach ($mso_get_empty_lfq_rates as $mgelr_key => $mgelr) {
//                                    !isset($mso_get_lfq_rates[$mgelr_key]) ? $mso_get_lfq_rates[$mgelr_key][] = $mgelr : '';
//                                }
//                            }
//
//                            if (!empty($mso_spq_rates)) {
//                                $mso_get_empty_spq_rates = [];
//                                $mso_get_spq_rates_count++;
//                                foreach ($mso_spq_rates as $spq_carrier => $spq_carrier_rates) {
//                                    if (isset($spq_carrier_rates['error'], $spq_carrier_rates['message'])) {
//                                        $error_from_api_detected = true;
//                                        $mso_get_empty_spq_rates[$zip] = $spq_carrier_rates;
//                                    } else {
//                                        $spq_carrier_rates = $this->mso_package_obj->mso_sort_asec($spq_carrier_rates, 'cost');
//                                        $spq_rate = array_slice($spq_carrier_rates, 0, 1);
//                                        $mso_get_spq_rates[$zip][] = reset($spq_rate);
//                                    }
//                                }
//
//                                foreach ($mso_get_empty_spq_rates as $mgesr_key => $mgesr) {
//                                    !isset($mso_get_spq_rates[$mgesr_key]) ? $mso_get_spq_rates[$mgesr_key][] = $mgesr : '';
//                                }
//                            }
//                        }
//
//                        if (!empty($mso_get_lfq_rates) && !empty($mso_get_spq_rates) && ($mso_get_lfq_rates_count > 0 && $mso_get_lfq_rates_count != $mso_rates_count) && ($mso_get_spq_rates_count > 0 && $mso_get_spq_rates_count != $mso_rates_count)) {
//                            $mso_lfq_spq_rates = array_merge($mso_get_lfq_rates, $mso_get_spq_rates);
//                            $this->mso_calculate_rates($mso_lfq_spq_rates, 'mso_lfq_spq_rate', 'Freight');
//                        } else {
//                            if (!empty($mso_get_lfq_rates) && $mso_get_lfq_rates_count == $mso_rates_count) {
//                                $this->mso_calculate_rates($mso_get_lfq_rates, 'mso_lfq_rate', 'Freight');
//                            }
//
//                            if (!empty($mso_get_spq_rates) && $mso_get_spq_rates_count == $mso_rates_count) {
//                                $this->mso_calculate_rates($mso_get_spq_rates, 'mso_spq_rate', 'Shipping');
//                            }
//                        }
//                    } else {
//                        foreach ($mso_rates as $mso_zip => $mso_carrier_rates) {
//                            if (isset($mso_carrier_rates['spq']) || isset($mso_carrier_rates['lfq'])) {
//                                $mso_carrier_rates = reset($mso_carrier_rates);
//                            }
//
//                            foreach ($mso_carrier_rates as $carrier_name => $mso_carrier_rate) {
//                                foreach ($mso_carrier_rate as $key => $rate) {
//                                    !is_array($rate) ? $rate = [] : '';
//                                    $mso_widget_detail[$mso_zip] = $rate;
//                                    $cost = (isset($rate['cost'])) ? $rate['cost'] : 0;
//                                    $markup = (isset($rate['markup'])) ? $rate['markup'] : 0;
//                                    $rate['cost'] = $this->mso_add_markup($cost, $markup);
//                                    $mso_meta_data = [
//                                        'mso_widget_detail' => json_encode($mso_widget_detail)
//                                    ];
//                                    $rate['meta_data'] = $mso_meta_data;
//                                    $this->mso_final_rates[] = $rate;
//                                }
//                            }
//                        }
//                    }
//                }
//
//                $mso_final_rates = $this->mso_package_obj->mso_sort_asec($this->mso_final_rates, 'cost');
//                $mso_csrfac = get_option('mso_csrfac');
//                if ($mso_csrfac == 'yes') {
//                    $mso_final_rates = $this->mso_package_obj->mso_cheapest_single_rate($mso_final_rates, 1);
//                }
//
//                $this->mso_add_rates($mso_final_rates);
//
//                if (!$error_from_api_detected && strlen($running_request) > 0 && strlen($running_response) > 0) {
//                    $previous_request = WC()->session->get('mso_previous_requests');
//                    $previous_request[$running_request] = $running_response;
//                    WC()->session->set('mso_previous_requests', $previous_request);
//                }
            }

            /**
             * Add rate
             * @param array type $rates
             * @return string type $carrier
             */
//            public function mso_calculate_rates($mso_rates, $id, $label)
//            {
//                $mso_widget_detail = [];
//                $mso_get_rates_cost = 0;
//                foreach ($mso_rates as $mso_zip => $mso_rate) {
//                    $mso_rate = $this->mso_package_obj->mso_sort_asec($mso_rate, 'cost');
//                    $rate = array_slice($mso_rate, 0, 1);
//                    if (!empty($rate) && is_array($rate)) {
//                        $rate = reset($rate);
//                        $mso_widget_detail[$mso_zip] = $rate;
//                        $cost = (isset($rate['cost'])) ? $rate['cost'] : 0;
//                        $markup = (isset($rate['markup'])) ? $rate['markup'] : 0;
//                        $cost = $this->mso_add_markup($cost, $markup);
//                        $mso_get_rates_cost += $cost;
//                    }
//                }
//
//                $this->mso_final_rates[] = [
//                    'id' => $id,
//                    'label' => $label,
//                    'cost' => $mso_get_rates_cost,
//                    'meta_data' => [
//                        'mso_widget_detail' => json_encode($mso_widget_detail)
//                    ]
//                ];
//            }

            /**
             *
             * @param string type $cost
             * @param string type $markup
             * @return float type
             */
//            function mso_add_markup($cost, $markup)
//            {
//                $markup = $cost > 0 ? $markup : 0;
//                $markup_fee = 0;
//                if ($markup != '' && $markup != 0) {
//                    if (strrchr($markup, "%")) {
//                        $percent = (float)$markup;
//                        $markup_fee = (float)$cost / 100 * $percent;
//                    } else {
//                        $markup_fee = (float)$markup;
//                    }
//                }
//
//                $markup_fee = $this->mso_round($markup_fee);
//
//                $cost = (float)$cost + $markup_fee;
//                return $cost;
//            }

            /**
             *
             * @param float type $val
             * @param int type $min
             * @param int type $max
             * @return float type
             */
//            function mso_round($val, $min = 2, $max = 4)
//            {
//                $result = round($val, $min);
//
//                if ($result == 0 && $min < $max) {
//                    return $this->mso_round($val, ++$min, $max);
//                } else {
//                    return $result;
//                }
//            }

            /**
             * Add rate
             * @param array type $rates
             * @return string type $carrier
             */
            public function mso_add_rates($rates)
            {
                foreach ($rates as $key => $rate) {
                    $this->add_rate($rate);
                }
            }
        }
    }
}