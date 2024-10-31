<?php

namespace MsoUpsFreight;

class MsoUpsFreight
{
    static public function mso_init()
    {
//        $status_description = $status_direction = '';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_UPS_FREIGHT_GET]))) {
//                $status_direction = 'mso_disabled';
//                $status_description = '<span class="notice notice-error mso_err_status_description"><b>Error!</b> ' . MSO_PAID_PLAN_FEATURE . '</span>';
//            } elseif (MSO_PLAN_STATUS == 'success' && (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_UPS_FREIGHT_GET]))) {
//                $current_carrier = MSO_SUBSCRIPTIONS[MSO_UPS_FREIGHT_GET];
//                $carrier = $current_carrier['carrier'];
//                $current_period_end = $current_carrier['current_period_end'];
////                $description = "Your $carrier plan will expire on $current_period_end";
//                $description = "Your $carrier plan would be renewed on " . date('F jS, Y', strtotime($current_period_end));
//                $status_description = '<span class="notice notice-success mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//            }
//        }

        return [
            'mso_ups_lfq' => [
                'name' => __('WooCommerce UPS Freight Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
            ],
            'mso_ups_lfq_carrier_id' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'value' => 'mso_ups_lfq',
                'class' => 'hidden mso_connection mso_optional mso_carrier_id',
            ],
            'mso_ups_lfq_carrier_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => mso_plan_status(MSO_UPS_FREIGHT_GET),
                'id' => 'mso_ups_lfq_carrier_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional',
            ],
            'mso_ups_lfq_carrier_enable' => [
                'name' => __('Enable / Disable', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_lfq_carrier_enable',
                'desc' => MSO_ENABLE_DESC,
                'class' => 'mso_carrier_settings_on_off'
            ],
            'mso_ups_lfq_connection' => [
                'name' => __('API Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_connection mso_optional',
            ],
            'mso_ups_lfq_account_number' => [
                'name' => __('Account Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Account Number',
                'id' => 'mso_ups_lfq_account_number',
                'class' => 'mso_child_carrier mso_optional'
            ],
            'mso_ups_lfq_username' => [
                'name' => __('Username', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Username',
                'id' => 'mso_ups_lfq_username',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_ups_lfq_password' => [
                'name' => __('Password', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Password',
                'id' => 'mso_ups_lfq_password',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_ups_lfq_access_key' => [
                'name' => __('Access Key', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Access Key',
                'id' => 'mso_ups_lfq_access_key',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_ups_lfq_credentials_status' => [
                'name' => __('Test UPS Freight Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
//                'id' => 'mso_ups_lfq_credentials_status',
                'id' => '',
                'desc' => mso_cfas(get_option('mso_ups_lfq_credentials_status')),
                'class' => 'hidden mso_carrier_end mso_child_carrier mso_api_credentials_status'
            ],
//            'mso_ups_lfq_carrier_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_ups_lfq_carrier_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional',
//            ],
            'mso_ups_lfq_accessorials_heading' => [
                'name' => __('Accessorials', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_ups_lfq_rad' => [
                'name' => __('Residential delivery', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_lfq_rad',
                'class' => 'mso_child_carrier'
            ],
            'mso_ups_lfq_liftgate' => [
                'name' => __('Liftgate delivery', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_ups_lfq_liftgate',
                'class' => 'mso_child_carrier'
            ],
            'mso_ups_lfq_additional_details' => [
                'name' => __('Additional Details', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_ups_lfq_markup' => [
                'name' => __('Markup', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_ups_lfq_markup',
                'placeholder' => 'Markup in USD ($)',
//                'desc' => 'Please enter the markup in the format of 1.00 or as a percentage (e.g. 5.0%).',
                'desc' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "UPS Freight" shipping services. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_child_carrier'
            ],
            'mso_ups_lfq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_ups_lfq_end',
            ],
        ];

    }
}