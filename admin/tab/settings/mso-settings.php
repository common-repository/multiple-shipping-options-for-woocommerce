<?php

namespace MsoSettings;

use  MsoSpq\MsoSpq;
use  MsoLfq\MsoLfq;

class MsoSettings
{
    static public function mso_settings()
    {
        $mso_description = [
            'mso_desc' => [
                'name' => __('Multiple Shipping Options for WooCommerce', 'woocommerce-settings-mso'),
                'type' => 'title',
                'desc' => 'The settings page offers a flexible and customizable solution for businesses to configure their shipping options, ensuring a seamless shipping experience for your store. It provides a range of configurable options that allow you to customize the shipping experience according to your specific needs.',
                'id' => 'mso_desc',
            ],
            'mso_desc_end' => [
                'type' => 'sectionend',
                'id' => 'mso_desc_end',
            ]
        ];

        $key_settings = [
            'mso_key' => [
                'name' => __('Authorization', 'woocommerce-settings-mso'),
                'type' => 'title',
                'id' => 'mso_key_settings',
            ],
            'mso_key_id' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'value' => 'mso_paid_key',
                'class' => 'hidden mso_connection mso_optional mso_carrier_id',
            ],
            'mso_paid_key' => [
                'name' => __('MSO Key', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_paid_key',
                'placeholder' => 'Key',
                'desc_tip' => 'To obtain the MSO key, please register or log in to Mini Logics. The MSO key will be generated automatically for you. Copy the key from Mini Logics and paste it in the field below to access both the paid and free features of the plugin.',
                'class' => 'mso_pk mso_child_carrier mso_connection'
            ],
            'mso_key_status' => [
                'name' => __('Test and Update Plan Status', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => '',
                'desc' => mso_cfas(get_option('mso_key_status')),
                'class' => 'hidden mso_carrier_end mso_child_carrier mso_api_credentials_status mso_license_api_status'
            ],
            'mso_key_end' => [
                'type' => 'sectionend',
                'id' => 'mso_key_end',
            ],
        ];

        if (MSO_DONT_AUTH) {
            $key_settings = [];
        }

        $status_description = $status_direction = '';
        if (!MSO_DONT_AUTH) {
            if (MSO_PLAN_STATUS != 'success' || empty(MSO_SUBSCRIPTIONS)) {
                $status_direction = 'mso_disabled';
                $status_description = '<span class="notice notice-error mso_err_status_description mso_err_quoting_method">' . MSO_PAID_PLAN_REQUIRE_SINGLE_CARRIER . '</span>';
            }
        }

        $common_settings = [
            'mso_cs' => [
                'name' => __('Quoting Methods', 'woocommerce-settings-mso'),
                'type' => 'title',
                'desc' => '',
                'id' => 'mso_cs_settings',
            ],
            'mso_api_test_mode' => [
                'name' => __('API testing mode', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_api_test_mode',
                'desc_tip' => 'Enable this checkbox to activate the testing environment for all shipping carriers. This allows you to test credentials and verify if your expected results are being obtained.',
                'class' => 'mso_child_carrier mso_connection'
            ],
            'mso_shipping_options_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => $status_description,
                'id' => 'mso_shipping_options_plan_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional'
            ],
            'mso_csrfac' => [
                'name' => __('Lowest shipping option across all carriers', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_csrfac',
                'class' => 'mso_child_carrier mso_cheapest_single_rate ' . $status_direction
            ],
            'mso_csrfec' => [
                'name' => __('Lowest shipping option from each carrier', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_csrfec',
                'class' => 'mso_child_carrier mso_cheapest_single_rate ' . $status_direction
            ],
            'mso_mswrflfq' => [
                'name' => __('Minimum shipment weight requirement for LTL Freight Shipping; Small Package Shipping will be returned otherwise', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => '150',
                'id' => 'mso_mswrflfq',
                'class' => 'mso_child_carrier ' . $status_direction
            ],
            'mso_free_shipping_cost_heading' => [
                'name' => __("Offer free shipping when an order's parcel shipment exceeds a certain threshold.", 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_free_shipping_cost_heading',
                'class' => 'hidden mso_shipping_settings_heading',
            ],
            'mso_free_shipping_option_weight_threshold' => [
                'name' => __('Weight limit', 'woocommerce-settings-mso'),
                'type' => 'number',
                'id' => 'mso_free_shipping_option_weight_threshold',
                'desc' => 'Please input the weight limit in pounds (lbs), for example: 10.00.',
                'desc_tip' => 'Offer free shipping for orders when the parcel shipment weight exceeds the weight limit.',
                'class' => 'mso_child_carrier ' . $status_direction
            ],
            'mso_free_shipping_option_cart_total' => [
                'name' => __('Cart total limit', 'woocommerce-settings-mso'),
                'type' => 'number',
                'id' => 'mso_free_shipping_option_cart_total',
                'desc' => 'Please input the total cart limit, for example: 10.00',
                'desc_tip' => 'Offer free shipping for orders when the cart total of the parcel shipment exceeds the specified limit.',
                'class' => 'mso_child_carrier ' . $status_direction
            ],
//            'mso_free_shipping_option_custom_rate_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'id' => 'mso_free_shipping_option_custom_rate_label',
//                'default', 'Free shipping',
//                'class' => 'mso_child_carrier mso_free_shipping_option_custom_rate_label ' . $status_direction,
//                'desc' => 'Please input the shipping method you would like to display.',
//                'desc_tip' => 'This controls the title that the user will see during the checkout process.'
//            ],
//            'mso_free_shipping_option_custom_rate_cost' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'number',
//                'id' => 'mso_free_shipping_option_custom_rate_cost',
//                'desc' => 'Please input the shipping cost, for example: 10.00.',
//                'desc_tip' => 'The plugin considers the currency symbol selected on the store.',
//                'default' => '0',
//                'class' => 'mso_child_carrier mso_free_shipping_option_custom_rate_cost ' . $status_direction
//            ],
            'mso_no_shipping_cost_heading' => [
                'name' => __('What to do when a product does not provide a shipping rate on the cart/checkout Page', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_no_shipping_cost_heading',
                'class' => 'hidden mso_shipping_settings_heading',
            ],
            'mso_no_shipping_cost_enable' => [
                'name' => __('If the products in an order are from multiple origins and one of them does not have a return rate, the total shipping cost should not be displayed on the cart page.', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_no_shipping_cost_enable',
                'desc_tip' => 'This feature is for multiple shipments on the cart/checkout page, involving multiple products shipped from different origins.',
                'class' => 'mso_child_carrier mso_no_shipping_cost_enable ' . $status_direction
            ],
            'mso_no_shipping_cost_options' => [
                'name' => __('Options to Consider When No Shipping Rates Are Available', 'woocommerce-settings-mso'),
                'type' => 'radio',
                'id' => 'mso_no_shipping_cost_options',
                'class' => 'mso_child_carrier ' . $status_direction,
                'default' => 'error_message',
                'options' => [
                    'error_message' => __('Displaying an Error Message', 'woocommerce-settings-mso'),
                    'custom_rate' => __('Setting a Custom Shipping Rate', 'woocommerce-settings-mso'),
                ]
            ],
            'mso_no_shipping_option_error_message' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'textarea',
                'id' => 'mso_no_shipping_option_error_message',
                'desc' => 'Max. 200 alphanumeric characters is allowed.',
                'default' => 'No shipping methods are available for the provided address. Please check the address.',
                'class' => 'mso_child_carrier mso_no_shipping_option_error_message ' . $status_direction
            ],
            'mso_no_shipping_option_custom_rate_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'select',
                'id' => 'mso_no_shipping_option_custom_rate_label',
                'class' => 'mso_child_carrier mso_no_shipping_option_custom_rate ' . $status_direction,
                'desc' => 'Kindly select the shipping method you would like to add.',
                'desc_tip' => 'This controls the title that the user will see during the checkout process.',
                'default' => 'Free Shipping',
                'options' => [
                    'Local pickup' => __('Local pickup', 'woocommerce-settings-mso'),
                    'Free shipping' => __('Free shipping', 'woocommerce-settings-mso'),
                    'Flat rate' => __('Flat rate', 'woocommerce-settings-mso'),
                ]
            ],
            'mso_no_shipping_option_custom_rate_cost' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'number',
                'id' => 'mso_no_shipping_option_custom_rate_cost',
                'desc' => 'Please input the shipping cost, for example: 10.00.',
//                'desc_tip' => 'The plugin considers the currency symbol selected on the store.',
                'default' => '0',
                'class' => 'mso_child_carrier mso_no_shipping_option_custom_rate ' . $status_direction
            ],
            'mso_cs_end' => [
                'type' => 'sectionend',
                'id' => 'mso_cs_end',
            ],
        ];

        // Getting store address
        $mso_store_shop_address = mso_store_shop_address();
        $mso_city = $mso_state = $mso_zip = $mso_country = $address_1 = $address_2 = '';
        extract($mso_store_shop_address);
        $redirect_url_general_page = admin_url() . 'admin.php?page=wc-settings&tab=general';
        $redirect_url_product_page = '<a href="' . admin_url() . 'edit.php?post_type=product' . '">page</a>';
        $store_address = "<a href='$redirect_url_general_page' class='mso_store_shop_address_str'>$address_1 $address_2, $mso_city, $mso_state $mso_zip, $mso_country</a>";
        $origin_settings = [
            'mso_origin' => [
                'name' => __('Origin', 'woocommerce-settings-mso'),
                'type' => 'title',
                'desc' => '',
                'id' => 'mso_origin_settings',
            ],
            'mso_origin_description' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => "Your default origin address is the store address at $store_address for all store products. If you want to change the origin address for individual products, go to the product $redirect_url_product_page where you can make individual selections.",
                'class' => 'hidden mso_connection mso_origin_description'
            ],
//            'mso_no_shipping_options_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_no_shipping_options_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional'
//            ],
            'mso_origin_end' => [
                'type' => 'sectionend',
                'id' => 'mso_origin_end',
            ],
        ];

        $spq_settings = [
            'mso_spq' => [
                'name' => __('Small Package Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
                'desc' => 'Multiple Shipping Options for WooCommerce utilizes the Small Package Shipping API to dynamically generate and display real-time shipping rates for small packages directly within the WooCommerce shopping cart, streamlining the checkout process for your customers.',
                'id' => 'mso_spq_settings',
            ],
            'mso_spq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_spq_end',
            ],
        ];
        $spq_apps = MsoSpq::mso_init();
        $lfq_settings = [
            'mso_lfq' => [
                'name' => __('LTL Freight Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
                'desc' => 'Multiple Shipping Options for WooCommerce leverages the LTL Freight Shipping API to dynamically generate and display real-time shipping rates for less-than-truckload (LTL) freight shipments directly within the WooCommerce shopping cart, simplifying the checkout process and providing your customers with accurate, up-to-date pricing information.',
                'id' => 'mso_lfq_settings',
            ],
            'mso_lfq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_lfq_end',
            ],
        ];
        $lfq_apps = MsoLfq::mso_init();
        $settings = array_merge($mso_description, $key_settings, $common_settings, $origin_settings, $spq_settings, $spq_apps, $lfq_settings, $lfq_apps);
        return $settings;
    }
}