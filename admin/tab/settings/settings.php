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
            'mso_main_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => mso_get_carriers_plan_status(),
                'id' => 'mso_main_plan_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional',
            ],
            'mso_desc_end' => [
                'type' => 'sectionend',
                'id' => 'mso_desc_end',
            ]
        ];

        $authorization_key = get_option('mso_paid_key');
        $key_settings = [
            'mso_key' => [
                'name' => __('Authentication', 'woocommerce-settings-mso'),
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
                'name' => __('Authorization Key', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_paid_key',
                'placeholder' => 'Authorization Key',
                'desc' => empty(trim($authorization_key)) ? MSO_AUTHORIZATION_KEY_DESC : '',
                'class' => 'mso_pk mso_child_carrier mso_connection'
            ],
            'mso_key_status' => [
                'name' => __('Test Authorization Key', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => '',
                'desc' => empty(trim($authorization_key)) ? mso_cfas('') : mso_cfas(get_option('mso_key_status')),
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

//        $status_description = $status_direction = '';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || empty(MSO_SUBSCRIPTIONS)) {
//                $status_direction = 'mso_disabled';
//                $status_description = '<span class="notice notice-error mso_err_status_description mso_err_quoting_method">' . MSO_PAID_PLAN_REQUIRE_SINGLE_CARRIER . '</span>';
//            }
//        }

        $common_settings = [
            'mso_cs' => [
                'name' => __('Shipping Methods ', 'woocommerce-settings-mso'),
                'type' => 'title',
                'desc' => '',
                'id' => 'mso_cs_settings',
            ],
            'mso_testing_hitting_url_heading' => [
                'name' => __("Testing Environment Activation for Shipping Carriers", 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_testing_hitting_url_heading',
                'class' => 'hidden mso_shipping_settings_heading',
            ],
            'mso_api_test_mode' => [
                'name' => __('Shipping in Test Mode', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_api_test_mode',
                'default' => 'yes',
                'desc' => 'Enable the testing environment for all shipping carriers.',
                'class' => 'mso_child_carrier mso_connection'
            ],
//            'mso_shipping_options_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_shipping_options_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional'
//            ],
            'mso_rating_methods_heading' => [
                'name' => __("Rating Methods", 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_rating_methods_heading',
                'class' => 'hidden mso_shipping_settings_heading',
            ],
            'mso_csrfac' => [
                'name' => __('Single shipping rate option', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_csrfac',
                'desc' => 'This option shows the most cost-effective shipping method available among all activated carriers. It selects the single cheapest shipping rate.',
                'class' => 'mso_child_carrier mso_cheapest_single_rate'
            ],
            'mso_csrfec' => [
                'name' => __('Multiple cheapest options', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_csrfec',
                'desc' => 'This feature displays the cheapest shipping rates from each activated carrier. It provides multiple affordable options, each from a different carrier.',
                'class' => 'mso_child_carrier mso_cheapest_single_rate'
            ],
            'mso_mswrflfq' => [
                'name' => __('Minimum shipment weight requirement for LTL Freight Shipping; Small Package Shipping will be returned otherwise', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => '150',
                'id' => 'mso_mswrflfq',
                'desc' => 'Please input the total weight of cart in pounds (lbs), for example: 150.00',
                'class' => 'mso_child_carrier'
            ],
            'mso_free_shipping_cost_heading' => [
                'name' => __("Suppress shipping rates and offer free shipping when an order's parcel shipment exceeds a certain threshold", 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_free_shipping_cost_heading',
                'class' => 'hidden mso_shipping_settings_heading',
            ],
            'mso_free_shipping_option_weight_threshold' => [
                'name' => __('Weight limit', 'woocommerce-settings-mso'),
                'type' => 'number',
                'id' => 'mso_free_shipping_option_weight_threshold',
                'desc' => 'Please input the weight limit in pounds (lbs), for example: 10.00',
                'desc_tip' => 'Offer free shipping for orders when the parcel shipment weight exceeds the weight limit.',
                'class' => 'mso_child_carrier'
            ],
            'mso_free_shipping_option_cart_total' => [
                'name' => __('Cart total limit', 'woocommerce-settings-mso'),
                'type' => 'number',
                'id' => 'mso_free_shipping_option_cart_total',
                'desc' => 'Please input the total cart limit, for example: 10.00',
                'desc_tip' => 'Offer free shipping for orders when the cart total of the parcel shipment exceeds the specified limit.',
                'class' => 'mso_child_carrier'
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
                'class' => 'mso_child_carrier mso_no_shipping_cost_enable'
            ],
            'mso_no_shipping_cost_options' => [
                'name' => __('Options to consider when no shipping rates are available', 'woocommerce-settings-mso'),
                'type' => 'radio',
                'id' => 'mso_no_shipping_cost_options',
                'class' => 'mso_child_carrier',
                'default' => 'error_message',
                'options' => [
                    'error_message' => __('Displaying an error message', 'woocommerce-settings-mso'),
                    'custom_rate' => __('Setting a custom shipping rate', 'woocommerce-settings-mso'),
                ]
            ],
            'mso_no_shipping_option_error_message' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'textarea',
                'id' => 'mso_no_shipping_option_error_message',
                'desc' => 'Max. 200 alphanumeric characters is allowed.',
                'default' => 'No shipping methods are available for the provided address. Please check the address.',
                'class' => 'mso_child_carrier mso_no_shipping_option_error_message'
            ],
            'mso_no_shipping_option_custom_rate_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'select',
                'id' => 'mso_no_shipping_option_custom_rate_label',
                'class' => 'mso_child_carrier mso_no_shipping_option_custom_rate',
                'desc' => 'Please select the shipping method you would like to add.',
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
                'desc' => 'Please input the shipping cost in USD ($), for example: 10.00',
//                'desc_tip' => 'The plugin considers the currency symbol selected on the store.',
                'default' => '0',
                'class' => 'mso_child_carrier mso_no_shipping_option_custom_rate'
            ],
            'mso_cs_end' => [
                'type' => 'sectionend',
                'id' => 'mso_cs_end',
            ],
        ];

        $label_specifications = [
            'mso_sl' => [
                'name' => __('Label Specifications ', 'woocommerce-settings-mso'),
                'type' => 'title',
                'desc' => '',
                'id' => 'mso_sl_settings'
            ],
            'mso_label_owner_info' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_label_owner_info',
                'desc' => "This information is specifically for creating labels and will be visible on them. It includes key details about the owner. Having these details on the label is important for compliance and clear shipping across various companies.",
                'class' => 'hidden mso_child_carrier mso_connection mso_hidden_table_th'
            ],
            'mso_company_name' => [
                'name' => __('Company Name', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_company_name',
                'class' => 'mso_child_carrier'
            ],
            'mso_attention_name' => [
                'name' => __('Contact Name', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_attention_name',
                'class' => 'mso_child_carrier'
            ],
            'mso_phone_number' => [
                'name' => __('Phone Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_phone_number',
                'class' => 'mso_child_carrier'
            ],
            'mso_tax_identify_number' => [
                'name' => __('Tax Identification Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_tax_identify_number',
                'class' => 'mso_child_carrier'
            ],
            'mso_sl_end' => [
                'type' => 'sectionend',
                'id' => 'mso_sl_end',
            ]
        ];

        // Origin
//        $mso_store_origin_address = mso_store_origin_address();
//        if (!empty($mso_store_origin_address)) {
//            $origin_address = $mso_store_origin_address;
//        } else {
//            $origin_address = mso_store_shop_address();
//        }

        $origin_address = mso_store_shop_address();

        $mso_city = $mso_state = $mso_zip = $mso_country = $address_1 = $address_2 = '';
        extract($origin_address);
//        $redirect_url_general_page = admin_url() . 'admin.php?page=wc-settings&tab=general';
        $redirect_url_product_page = '<a href="' . admin_url() . 'edit.php?post_type=product' . '">page</a>';
        $origin_complete_address = "<a class='mso_store_shop_address_str'>$address_1 $address_2, $mso_city, $mso_state $mso_zip, $mso_country</a>";

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
                'desc' => "Your default origin address for all rating methods and shipments is $origin_complete_address. If you want to change the origin address for individual products, go to the product $redirect_url_product_page where you can make individual selections. Access the 'Multiple Shipping Options for WooCommerce' tab and proceed to the 'Origins' section.",
                'class' => 'hidden mso_child_carrier mso_connection mso_hidden_table_th'
            ],
//            'mso_no_shipping_options_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_no_shipping_options_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional'
//            ],

            'mso_origin_address_1' => [
                'name' => __('Address', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => $address_1,
                'id' => 'mso_origin_address_1',
                'class' => 'mso_child_carrier'
            ],
            'mso_origin_address_2' => [
                'name' => __('Address 2', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => $address_2,
                'id' => 'mso_origin_address_2',
                'class' => 'mso_child_carrier'
            ],
            'mso_origin_city' => [
                'name' => __('City', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => $mso_city,
                'id' => 'mso_origin_city',
                'class' => 'mso_child_carrier'
            ],
            'mso_origin_state' => [
                'name' => __('State', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => $mso_state,
                'id' => 'mso_origin_state',
                'class' => 'mso_child_carrier'
            ],
            'mso_origin_zipcode' => [
                'name' => __('Zip Code', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => $mso_zip,
                'id' => 'mso_origin_zipcode',
                'class' => 'mso_child_carrier'
            ],
            'mso_origin_country' => [
                'name' => __('Country', 'woocommerce-settings-mso'),
                'type' => 'text',
                'default' => $mso_country,
                'id' => 'mso_origin_country',
                'class' => 'mso_child_carrier'
            ],
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
        $settings = array_merge($mso_description, $key_settings, $common_settings, $label_specifications, $origin_settings, $spq_settings, $spq_apps, $lfq_settings, $lfq_apps);
        return $settings;
    }
}