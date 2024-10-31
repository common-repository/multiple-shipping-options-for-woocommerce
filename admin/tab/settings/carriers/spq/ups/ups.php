<?php

namespace MsoUps;

class MsoUps
{
    static public function mso_init()
    {
//        $status_description = $status_direction = '';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_UPS_GET]))) {
//                $status_direction = 'mso_disabled';
////            $status_description = '<span class="mso_err_status_description"><b>Error!</b> ' . MSO_PLAN_DESC . '</span>';
//                $status_description = '<span class="notice notice-error mso_err_status_description"><b>Error!</b> ' . MSO_PAID_PLAN_FEATURE . '</span>';
//            } elseif (MSO_PLAN_STATUS == 'success' && (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_UPS_GET]))) {
//                $current_carrier = MSO_SUBSCRIPTIONS[MSO_UPS_GET];
//                $carrier = $current_carrier['carrier'];
//                $current_period_end = $current_carrier['current_period_end'];
////                $description = "Your $carrier plan will expire on $current_period_end";
//                $description = "Your $carrier plan would be renewed on " . date('F jS, Y', strtotime($current_period_end));
////            $status_description = '<span class="mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//                $status_description = '<span class="notice notice-success mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//            }
//        }

        return [
            'mso_ups_spq' => [
                'name' => __('WooCommerce UPS Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
                'class' => 'hidden',
            ],
            'mso_ups_spq_carrier_id' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'value' => 'mso_ups_sqp',
                'class' => 'hidden mso_connection mso_optional mso_carrier_id',
            ],
            'mso_ups_spq_carrier_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => mso_plan_status(MSO_UPS_GET),
                'id' => 'mso_ups_spq_carrier_plan_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional',
            ],
            'mso_ups_spq_carrier_enable' => [
                'name' => __('Enable / Disable', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'desc' => MSO_ENABLE_DESC,
                'id' => 'mso_ups_spq_carrier_enable',
                'class' => 'mso_carrier_settings_on_off'
            ],
            'mso_ups_spq_connection' => [
                'name' => __('API Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_connection mso_optional',
            ],
            'mso_ups_spq_account_number' => [
                'name' => __('Account Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Account Number',
                'id' => 'mso_ups_spq_account_number',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_ups_spq_username' => [
                'name' => __('Username', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Username',
                'id' => 'mso_ups_spq_username',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_ups_spq_password' => [
                'name' => __('Password', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Password',
                'id' => 'mso_ups_spq_password',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_ups_spq_access_key' => [
                'name' => __('Access Key', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Access Key',
                'id' => 'mso_ups_spq_access_key',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_ups_spq_credentials_status' => [
                'name' => __('Test UPS Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
//                'id' => 'mso_ups_spq_credentials_status',
                'id' => '',
                'desc' => mso_cfas(get_option('mso_ups_spq_credentials_status')),
                'class' => 'hidden mso_carrier_end mso_child_carrier mso_api_credentials_status'
            ],
//            'mso_ups_spq_carrier_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_ups_spq_carrier_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional',
//            ],
            'mso_ups_spq_domestic_services' => [
                'name' => __('Domestic Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_ups_spq_domestic_services',
                'class' => 'hidden mso_optional',
            ],
            // Select All
            'mso_ups_spq_domestic_services_sa' => [
                'name' => __('Select All', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_domestic_services_sa',
                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
            ],
            'mso_ups_spq_add_space_1' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_ups_spq_add_space_1',
                'class' => 'hidden mso_carrier_partition_64 mso_optional'
            ],
            // UPS Ground
            'mso_ups_spq_ground_action' => [
                'name' => __('UPS Ground', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_ground_action',
                'class' => 'mso_ups_dsa mso_carrier_partition'
            ],
            'mso_ups_spq_ground_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Ground',
                'id' => 'mso_ups_spq_ground_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Ground" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_ground_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_ground_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Ground" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS 2nd Day Air
            'mso_ups_spq_2nd_day_air_action' => [
                'name' => __('UPS 2nd Day Air', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_2nd_day_air_action',
                'class' => 'mso_ups_dsa mso_carrier_partition'
            ],
            'mso_ups_spq_2nd_day_air_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS 2nd Day Air',
                'id' => 'mso_ups_spq_2nd_day_air_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS 2nd Day Air" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_2nd_day_air_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_2nd_day_air_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS 2nd Day Air" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS 2nd Day Air A.M
            'mso_ups_spq_2nd_day_air_am_action' => [
                'name' => __('UPS 2nd Day Air A.M', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_2nd_day_air_am_action',
                'class' => 'mso_ups_dsa mso_carrier_partition'
            ],
            'mso_ups_spq_2nd_day_air_am_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS 2nd Day Air A.M',
                'id' => 'mso_ups_spq_2nd_day_air_am_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS 2nd Day Air A.M" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_2nd_day_air_am_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_2nd_day_air_am_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS 2nd Day Air A.M" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS Next Day Air Saver
            'mso_ups_spq_next_day_air_saver_action' => [
                'name' => __('UPS Next Day Air Saver', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_next_day_air_saver_action',
                'class' => 'mso_ups_dsa mso_carrier_partition'
            ],
            'mso_ups_spq_next_day_air_saver_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Next Day Air Saver',
                'id' => 'mso_ups_spq_next_day_air_saver_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Next Day Air Saver" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_next_day_air_saver_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_next_day_air_saver_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Next Day Air Saver" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS Next Day Air
            'mso_ups_spq_next_day_air_action' => [
                'name' => __('UPS Next Day Air', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_next_day_air_action',
                'class' => 'mso_ups_dsa mso_carrier_partition'
            ],
            'mso_ups_spq_next_day_air_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Next Day Air',
                'id' => 'mso_ups_spq_next_day_air_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Next Day Air" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_next_day_air_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_next_day_air_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Next Day Air" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS Next Day Air Early
            'mso_ups_spq_next_day_air_early_action' => [
                'name' => __('UPS Next Day Air Early', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_next_day_air_early_action',
                'class' => 'mso_ups_dsa mso_carrier_partition'
            ],
            'mso_ups_spq_next_day_air_early_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Next Day Air Early',
                'id' => 'mso_ups_spq_next_day_air_early_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Next Day Air Early" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_next_day_air_early_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_next_day_air_early_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Next Day Air Early" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS 3 Day Select
            'mso_ups_spq_3_day_select_action' => [
                'name' => __('UPS 3 Day Select', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_3_day_select_action',
                'class' => 'mso_ups_dsa mso_carrier_partition'
            ],
            'mso_ups_spq_3_day_select_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS 3 Day Select',
                'id' => 'mso_ups_spq_3_day_select_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS 3 Day Select" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_3_day_select_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_3_day_select_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS 3 Day Select" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_international_services' => [
                'name' => __('International Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_ups_spq_international_services',
                'class' => 'hidden mso_optional',
            ],
            // Select All
            'mso_ups_spq_international_services_sa' => [
                'name' => __('Select All', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_international_services_sa',
                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
            ],
            'mso_ups_spq_add_space_2' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_ups_spq_add_space_2',
                'class' => 'hidden mso_carrier_partition_64 mso_optional'
            ],
            // UPS Standard
            'mso_ups_spq_standard_action' => [
                'name' => __('UPS Standard', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_standard_action',
                'class' => 'mso_ups_isa mso_carrier_partition'
            ],
            'mso_ups_spq_standard_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Standard',
                'id' => 'mso_ups_spq_standard_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Standard" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_standard_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_standard_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Standard" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS Expedited
            'mso_ups_spq_expedited_action' => [
                'name' => __('UPS Expedited', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_expedited_action',
                'class' => 'mso_ups_isa mso_carrier_partition'
            ],
            'mso_ups_spq_expedited_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Expedited',
                'id' => 'mso_ups_spq_expedited_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Expedited" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_expedited_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_expedited_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Expedited" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS Express Saver
            'mso_ups_spq_express_saver_action' => [
                'name' => __('UPS Express Saver', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_express_saver_action',
                'class' => 'mso_ups_isa mso_carrier_partition'
            ],
            'mso_ups_spq_express_saver_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Express Saver',
                'id' => 'mso_ups_spq_express_saver_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Express Saver" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_express_saver_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_express_saver_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Express Saver" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS Express
            'mso_ups_spq_express_action' => [
                'name' => __('UPS Express', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_express_action',
                'class' => 'mso_ups_isa mso_carrier_partition'
            ],
            'mso_ups_spq_express_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Express',
                'id' => 'mso_ups_spq_express_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Express" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_express_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_express_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Express" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // UPS Express Plus
            'mso_ups_spq_express_plus_action' => [
                'name' => __('UPS Express Plus', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_express_plus_action',
                'class' => 'mso_ups_isa mso_carrier_partition'
            ],
            'mso_ups_spq_express_plus_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'UPS Express Plus',
                'id' => 'mso_ups_spq_express_plus_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "UPS Express Plus" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_express_plus_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_ups_spq_express_plus_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Express Plus" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_ups_spq_settings' => [
                'name' => __('Accessorials', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_ups_spq_rad' => [
                'name' => __('Residential delivery', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_rad',
                'class' => 'mso_child_carrier'
            ],
            'mso_ups_spq_shipping_options' => [
                'name' => __('Shipping Options', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_ups_spq_delivery_estimate' => [
                'name' => __('Display estimated delivery date.', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_spq_delivery_estimate',
                'class' => 'mso_child_carrier'
            ],
            'mso_ups_spq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_ups_spq_end',
            ],
        ];
    }
}