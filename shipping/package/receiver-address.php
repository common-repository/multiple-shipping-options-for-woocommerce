<?php

/**
 * Receiver address.
 */

namespace WasaioReceiverAddress;

/**
 * Get address from cart|checkout page.
 * Class WasaioReceiverAddress
 * @package WasaioReceiverAddress
 */
if (!class_exists('WasaioReceiverAddress')) {

    class WasaioReceiverAddress
    {

        static public $woocommerce_version;

        /**
         * Receiver address
         * @return array
         */
        static public function get_address()
        {
            self::wasaio_get_woo_version_number();

            $county = strlen(WC()->customer->get_shipping_country()) > 0 ? WC()->customer->get_shipping_country() : self::get_country();

            // Use the WC_Countries class to get the full country name
            $wc_countries = new \WC_Countries();
            $country_name = $wc_countries->countries[$county];
            $parts = explode('(', $country_name);
            $trimmed_country_name = trim($parts[0]);

            return [
                'postcode' => strlen(WC()->customer->get_shipping_postcode()) > 0 ? WC()->customer->get_shipping_postcode() : self::get_postcode(),
                'state' => strlen(WC()->customer->get_shipping_state()) > 0 ? WC()->customer->get_shipping_state() : self::get_state(),
                'country' => $county,
                'country_name' => $trimmed_country_name,
                'city' => strlen(WC()->customer->get_shipping_city()) > 0 ? WC()->customer->get_shipping_city() : self::get_city(),
                'address1' => strlen(WC()->customer->get_shipping_address_1()) > 0 ? WC()->customer->get_shipping_address_1() : self::get_address1()
            ];
        }

        /**
         * Declared woo version publically
         */
        static public function wasaio_get_woo_version_number()
        {
            if (!function_exists('get_plugins')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $plugin_folder = get_plugins('/' . 'woocommerce');
            $plugin_file = 'woocommerce.php';
            (isset($plugin_folder[$plugin_file]['Version'])) ?
                self::$woocommerce_version = $plugin_folder[$plugin_file]['Version'] : '';
        }

        /**
         * Get Postcode
         * @return string
         */
        static public function get_postcode()
        {
            $postcode = "";
            switch (self::$woocommerce_version) {
                case (self::$woocommerce_version <= '2.7'):
                    $postcode = WC()->customer->get_postcode();
                    break;
                case (self::$woocommerce_version >= '3.0'):
                    $postcode = WC()->customer->get_billing_postcode();
                    break;
                default:
                    $postcode = WC()->customer->get_shipping_postcode();
                    break;
            }

            return $postcode;
        }

        /**
         * Get state
         * @return string
         */
        static public function get_state()
        {
            $state = "";
            switch (self::$woocommerce_version) {
                case (self::$woocommerce_version <= '2.7'):
                    $state = WC()->customer->get_state();
                    break;
                case (self::$woocommerce_version >= '3.0'):
                    $state = WC()->customer->get_billing_state();
                    break;
                default:
                    $state = WC()->customer->get_shipping_state();
                    break;
            }
            return $state;
        }

        /**
         * Get city
         * @return string
         */
        static public function get_city()
        {
            $city = "";
            switch (self::$woocommerce_version) {
                case (self::$woocommerce_version <= '2.7'):
                    $city = WC()->customer->get_city();
                    break;
                case (self::$woocommerce_version >= '3.0'):
                    $city = WC()->customer->get_billing_city();
                    break;
                default:
                    $city = WC()->customer->get_shipping_city();
                    break;
            }
            return $city;
        }

        /**
         * Get country
         * @return string
         */
        static public function get_country()
        {
            $country = "";
            switch (self::$woocommerce_version) {
                case (self::$woocommerce_version <= '2.7'):
                    $country = WC()->customer->get_country();
                    break;
                case (self::$woocommerce_version >= '3.0'):
                    $country = WC()->customer->get_billing_country();
                    break;
                default:
                    $country = WC()->customer->get_shipping_country();
                    break;
            }
            return $country;
        }

        /**
         * Get address
         * @return string
         */
        static public function get_address1()
        {
            $address = "";
            switch (self::$woocommerce_version) {
                case (self::$woocommerce_version <= '2.7'):
                    $address = WC()->customer->get_address();
                    break;
                case (self::$woocommerce_version >= '3.0'):
                    $address = WC()->customer->get_billing_address_1();
                    break;
                default:
                    $address = WC()->customer->get_address();
                    break;
            }
            return $address;
        }

    }

}