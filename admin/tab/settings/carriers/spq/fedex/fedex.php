<?php

namespace MsoFedex;

class MsoFedex
{
    static public function mso_init()
    {
//        $status_description = $status_direction = '';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_FEDEX_GET]))) {
//                $status_direction = 'mso_disabled';
////            $status_description = '<span class="mso_err_status_description"><b>Error!</b> ' . MSO_PLAN_DESC . '</span>';
//                $status_description = '<span class="notice notice-error mso_err_status_description"><b>Error!</b> ' . MSO_PAID_PLAN_FEATURE . '</span>';
//            } elseif (MSO_PLAN_STATUS == 'success' && (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_FEDEX_GET]))) {
//                $current_carrier = MSO_SUBSCRIPTIONS[MSO_FEDEX_GET];
//                $carrier = $current_carrier['carrier'];
//                $current_period_end = $current_carrier['current_period_end'];
////                $description = "Your $carrier plan will expire on $current_period_end";
//                $description = "Your $carrier plan would be renewed on " . date('F jS, Y', strtotime($current_period_end));
////            $status_description = '<span class="mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//                $status_description = '<span class="notice notice-success mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//            }
//        }

        return [
            'mso_fedex_spq' => [
                'name' => __('WooCommerce Fedex Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
                'class' => 'hidden',
            ],
            'mso_fedex_spq_carrier_id' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'value' => 'mso_fedex_sqp',
                'class' => 'hidden mso_connection mso_optional mso_carrier_id',
            ],
            'mso_fedex_spq_carrier_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => mso_plan_status(MSO_FEDEX_GET),
                'id' => 'mso_fedex_spq_carrier_plan_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional',
            ],
            'mso_fedex_spq_carrier_enable' => [
                'name' => __('Enable / Disable', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'desc' => MSO_ENABLE_DESC,
                'id' => 'mso_fedex_spq_carrier_enable',
                'class' => 'mso_carrier_settings_on_off'
            ],
            'mso_fedex_spq_connection' => [
                'name' => __('API Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_connection mso_optional',
            ],
//            'mso_fedex_spq_parent_key' => [
//                'name' => __('Parent Credential - Key', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'id' => 'mso_fedex_spq_parent_key',
//                'class' => 'mso_child_carrier'
//            ],
//            'mso_fedex_spq_parent_password' => [
//                'name' => __('Parent Credential - Password', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'id' => 'mso_fedex_spq_parent_password',
//                'class' => 'mso_child_carrier'
//            ],
            'mso_fedex_spq_user_key' => [
                'name' => __('Key', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Key',
                'id' => 'mso_fedex_spq_user_key',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_spq_user_password' => [
                'name' => __('Password', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Password',
                'id' => 'mso_fedex_spq_user_password',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_spq_account_number' => [
                'name' => __('Account Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Account Number',
                'id' => 'mso_fedex_spq_account_number',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_spq_meter_number' => [
                'name' => __('Meter Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Meter Number',
                'id' => 'mso_fedex_spq_meter_number',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_spq_credentials_status' => [
                'name' => __('Test Fedex Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
//                'id' => 'mso_fedex_spq_credentials_status',
                'id' => '',
                'desc' => mso_cfas(get_option('mso_fedex_spq_credentials_status')),
                'class' => 'hidden mso_carrier_end mso_child_carrier mso_api_credentials_status'
            ],
//            'mso_fedex_spq_carrier_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_fedex_spq_carrier_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional',
//            ],
            'mso_fedex_spq_domestic_services' => [
                'name' => __('Domestic Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_fedex_spq_domestic_services',
                'class' => 'hidden mso_optional',
            ],
            // Select All
            'mso_fedex_spq_domestic_services_sa' => [
                'name' => __('Select All', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_domestic_services_sa',
                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
            ],
            'mso_fedex_spq_add_space_1' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_fedex_spq_add_space_1',
                'class' => 'hidden mso_carrier_partition_64 mso_optional'
            ],
            // Fedex Home Delivery
            'mso_fedex_spq_home_delivery_action' => [
                'name' => __('Fedex Home Delivery', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_home_delivery_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_home_delivery_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex Home Delivery',
                'id' => 'mso_fedex_spq_home_delivery_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex Home Delivery" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_home_delivery_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex Home Delivery" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_home_delivery_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex Ground
            'mso_fedex_spq_ground_action' => [
                'name' => __('Fedex Ground', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_ground_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_ground_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex Ground',
                'id' => 'mso_fedex_spq_ground_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex Ground" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_ground_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex Ground" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_ground_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex Express Saver
            'mso_fedex_spq_express_saver_action' => [
                'name' => __('Fedex Express Saver', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_express_saver_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_express_saver_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex Express Saver',
                'id' => 'mso_fedex_spq_express_saver_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex Express Saver" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_express_saver_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex Express Saver" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_express_saver_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex 2Day
            'mso_fedex_spq_2nd_day_action' => [
                'name' => __('Fedex 2Day', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_2nd_day_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_2nd_day_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex 2Day',
                'id' => 'mso_fedex_spq_2nd_day_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex 2Day" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_2nd_day_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex 2Day" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_2nd_day_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex 2Day AM
            'mso_fedex_spq_2nd_day_am_action' => [
                'name' => __('Fedex 2Day AM', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_2nd_day_am_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_2nd_day_am_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex 2Day AM',
                'id' => 'mso_fedex_spq_2nd_day_am_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex 2Day AM" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_2nd_day_am_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex 2Day AM" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_2nd_day_am_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex Standard Overnight
            'mso_fedex_spq_standard_overnight_action' => [
                'name' => __('Fedex Standard Overnight', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_standard_overnight_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_standard_overnight_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex Standard Overnight',
                'id' => 'mso_fedex_spq_standard_overnight_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex Standard Overnight" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_standard_overnight_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex Standard Overnight" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_standard_overnight_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex Priority Overnight
            'mso_fedex_spq_priority_overnight_action' => [
                'name' => __('Fedex Priority Overnight', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_priority_overnight_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_priority_overnight_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex Priority Overnight',
                'id' => 'mso_fedex_spq_priority_overnight_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex Priority Overnight" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_priority_overnight_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex Priority Overnight" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_priority_overnight_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex First Overnight
            'mso_fedex_spq_first_overnight_action' => [
                'name' => __('Fedex First Overnight', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_first_overnight_action',
                'class' => 'mso_fedex_dsa mso_carrier_partition'
            ],
            'mso_fedex_spq_first_overnight_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex First Overnight',
                'id' => 'mso_fedex_spq_first_overnight_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex First Overnight" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_first_overnight_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex First Overnight" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_first_overnight_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex SmartPost
//            'mso_fedex_spq_smart_post_action' => [
//                'name' => __('Fedex SmartPost', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_fedex_spq_smart_post_action',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_smart_post_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Fedex SmartPost',
//                'id' => 'mso_fedex_spq_smart_post_label',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_smart_post_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_fedex_spq_smart_post_markup',
//                'class' => 'mso_carrier_partition'
//            ],
            'mso_fedex_spq_international_services' => [
                'name' => __('International Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_fedex_spq_international_services',
                'class' => 'hidden mso_optional',
            ],
            // Select All
            'mso_fedex_spq_international_services_sa' => [
                'name' => __('Select All', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_services_sa',
                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
            ],
            'mso_fedex_spq_add_space_2' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_fedex_spq_add_space_2',
                'class' => 'hidden mso_carrier_partition_64 mso_optional'
            ],
            // Fedex International Ground
            'mso_fedex_spq_international_ground_action' => [
                'name' => __('Fedex International Ground', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_ground_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_ground_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Ground',
                'id' => 'mso_fedex_spq_international_ground_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Ground" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_ground_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Ground" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_ground_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International Economy
            'mso_fedex_spq_international_economy_action' => [
                'name' => __('Fedex International Economy', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_economy_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_economy_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Economy',
                'id' => 'mso_fedex_spq_international_economy_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Economy" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_economy_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Economy" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_economy_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International Economy Distribution
            'mso_fedex_spq_international_economy_distribution_action' => [
                'name' => __('Fedex International Economy Distribution', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_economy_distribution_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_economy_distribution_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Economy Distribution',
                'id' => 'mso_fedex_spq_international_economy_distribution_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Economy Distribution" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_economy_distribution_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Economy Distribution" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_economy_distribution_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International Economy Freight
            'mso_fedex_spq_international_economy_freight_action' => [
                'name' => __('Fedex International Economy Freight', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_economy_freight_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_economy_freight_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Economy Freight',
                'id' => 'mso_fedex_spq_international_economy_freight_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Economy Freight" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_economy_freight_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Economy Freight" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_economy_freight_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International First
            'mso_fedex_spq_international_first_action' => [
                'name' => __('FedEx International First', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_first_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_first_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'FedEx International First',
                'id' => 'mso_fedex_spq_international_first_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "FedEx International First" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_first_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "FedEx International First" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_first_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International Priority
            'mso_fedex_spq_international_priority_action' => [
                'name' => __('Fedex International Priority', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_priority_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_priority_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Priority',
                'id' => 'mso_fedex_spq_international_priority_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Priority" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_priority_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Priority" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_priority_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International Priority Distribution
            'mso_fedex_spq_international_priority_distribution_action' => [
                'name' => __('Fedex International Priority Distribution', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_priority_distribution_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_priority_distribution_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Priority Distribution',
                'id' => 'mso_fedex_spq_international_priority_distribution_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Priority Distribution" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_priority_distribution_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Priority Distribution" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_priority_distribution_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International Priority Freight
            'mso_fedex_spq_international_priority_freight_action' => [
                'name' => __('Fedex International Priority Freight', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_priority_freight_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_priority_freight_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Priority Freight',
                'id' => 'mso_fedex_spq_international_priority_freight_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Priority Freight" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_priority_freight_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Priority Freight" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_priority_freight_markup',
                'class' => 'mso_carrier_partition'
            ],
            // Fedex International Distribution Freight
            'mso_fedex_spq_international_distribution_freight_action' => [
                'name' => __('Fedex International Distribution Freight', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_international_distribution_freight_action',
                'class' => 'mso_fedex_isa mso_carrier_partition'
            ],
            'mso_fedex_spq_international_distribution_freight_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Fedex International Distribution Freight',
                'id' => 'mso_fedex_spq_international_distribution_freight_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Fedex International Distribution Freight" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_fedex_spq_international_distribution_freight_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Fedex International Distribution Freight" shipping price. This will be reflected on the cart and checkout pages.',
                'id' => 'mso_fedex_spq_international_distribution_freight_markup',
                'class' => 'mso_carrier_partition'
            ],
//            'mso_fedex_spq_one_rate_services' => [
//                'name' => __('One Rate Services', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'class' => 'hidden mso_optional',
//            ],
//            // Fedex Express Saver - One Rate
//            'mso_fedex_spq_one_rate_express_saver_action' => [
//                'name' => __('Fedex Express Saver - One Rate', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_fedex_spq_one_rate_express_saver_action',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_express_saver_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Fedex Express Saver - One Rate',
//                'id' => 'mso_fedex_spq_one_rate_express_saver_label',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_express_saver_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_fedex_spq_one_rate_express_saver_markup',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Fedex 2Day - One Rate
//            'mso_fedex_spq_one_rate_2nd_day_action' => [
//                'name' => __('Fedex Express Saver - One Rate', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_fedex_spq_one_rate_2nd_day_action',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_2nd_day_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Fedex Express Saver - One Rate',
//                'id' => 'mso_fedex_spq_one_rate_2nd_day_label',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_2nd_day_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_fedex_spq_one_rate_2nd_day_markup',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Fedex 2Day AM - One Rate
//            'mso_fedex_spq_one_rate_2nd_day_am_action' => [
//                'name' => __('Fedex 2Day AM - One Rate', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_fedex_spq_one_rate_2nd_day_am_action',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_2nd_day_am_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Fedex 2Day AM - One Rate',
//                'id' => 'mso_fedex_spq_one_rate_2nd_day_am_label',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_2nd_day_am_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_fedex_spq_one_rate_2nd_day_am_markup',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Fedex Standard Overnight - One Rate
//            'mso_fedex_spq_one_rate_standard_overnight_action' => [
//                'name' => __('Fedex Standard Overnight - One Rate', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_fedex_spq_one_rate_standard_overnight_action',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_standard_overnight_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Fedex Standard Overnight - One Rate',
//                'id' => 'mso_fedex_spq_one_rate_standard_overnight_label',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_standard_overnight_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_fedex_spq_one_rate_standard_overnight_markup',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Fedex Priority Overnight - One Rate
//            'mso_fedex_spq_one_rate_priority_overnight_action' => [
//                'name' => __('Fedex Priority Overnight - One Rate', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_fedex_spq_one_rate_priority_overnight_action',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_priority_overnight_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Fedex Priority Overnight - One Rate',
//                'id' => 'mso_fedex_spq_one_rate_priority_overnight_label',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_priority_overnight_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_fedex_spq_one_rate_priority_overnight_markup',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Fedex First Overnight - One Rate
//            'mso_fedex_spq_one_rate_first_overnight_action' => [
//                'name' => __('Fedex First Overnight - One Rate', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_fedex_spq_one_rate_first_overnight_action',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_first_overnight_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Fedex First Overnight - One Rate',
//                'id' => 'mso_fedex_spq_one_rate_first_overnight_label',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_fedex_spq_one_rate_first_overnight_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_fedex_spq_one_rate_first_overnight_markup',
//                'class' => 'mso_carrier_partition'
//            ],
            'mso_fedex_spq_settings' => [
                'name' => __('Accessorials', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_fedex_spq_rad' => [
                'name' => __('Residential delivery', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_rad',
                'class' => 'mso_child_carrier'
            ],
            'mso_fedex_spq_shipping_options' => [
                'name' => __('Shipping Options', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_fedex_spq_delivery_estimate' => [
                'name' => __('Display estimated delivery date.', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_spq_delivery_estimate',
                'class' => 'mso_child_carrier'
            ],
            'mso_fedex_spq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_fedex_spq_end',
            ],
        ];
    }
}