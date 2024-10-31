<?php

namespace MsoFedexFreight;

class MsoFedexFreight
{
    static public function mso_init()
    {
//        $status_description = $status_direction = '';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_FEDEX_FREIGHT_GET]))) {
//                $status_direction = 'mso_disabled';
////            $status_description = '<span class="mso_err_status_description"><b>Error!</b> ' . MSO_PLAN_DESC . '</span>';
//                $status_description = '<span class="notice notice-error mso_err_status_description"><b>Error!</b> ' . MSO_PAID_PLAN_FEATURE . '</span>';
//            } elseif (MSO_PLAN_STATUS == 'success' && (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_FEDEX_FREIGHT_GET]))) {
//                $current_carrier = MSO_SUBSCRIPTIONS[MSO_FEDEX_FREIGHT_GET];
//                $carrier = $current_carrier['carrier'];
//                $current_period_end = $current_carrier['current_period_end'];
////                $description = "Your $carrier plan will expire on $current_period_end";
//                $description = "Your $carrier plan would be renewed on " . date('F jS, Y', strtotime($current_period_end));
////            $status_description = '<span class="mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//                $status_description = '<span class="notice notice-success mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//            }
//        }

        return [
            'mso_fedex_lfq' => [
                'name' => __('WooCommerce Fedex Freight Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
            ],
            'mso_fedex_lfq_carrier_id' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'value' => 'mso_fedex_lfq',
                'class' => 'hidden mso_connection mso_optional mso_carrier_id',
            ],
            'mso_fedex_lfq_carrier_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => mso_plan_status(MSO_FEDEX_FREIGHT_GET),
                'id' => 'mso_fedex_lfq_carrier_plan_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional',
            ],
            'mso_fedex_lfq_carrier_enable' => [
                'name' => __('Enable / Disable', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'desc' => MSO_ENABLE_DESC,
                'id' => 'mso_fedex_lfq_carrier_enable',
                'class' => 'mso_carrier_settings_on_off'
            ],
            'mso_fedex_lfq_connection' => [
                'name' => __('API Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_connection mso_optional',
            ],
//            'mso_fedex_lfq_parent_key' => [
//                'name' => __('Parent Credential - Key', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'id' => 'mso_fedex_lfq_parent_key',
//                'class' => 'mso_child_carrier'
//            ],
//            'mso_fedex_lfq_parent_password' => [
//                'name' => __('Parent Credential - Password', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'id' => 'mso_fedex_lfq_parent_password',
//                'class' => 'mso_child_carrier'
//            ],
            'mso_fedex_lfq_user_key' => [
                'name' => __('Key', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Key',
                'id' => 'mso_fedex_lfq_user_key',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_user_password' => [
                'name' => __('Password', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Password',
                'id' => 'mso_fedex_lfq_user_password',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_billing_account_number' => [
                'name' => __('Billing Account Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Billing Account Number',
                'id' => 'mso_fedex_lfq_billing_account_number',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_meter_number' => [
                'name' => __('Meter Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Meter Number',
                'id' => 'mso_fedex_lfq_meter_number',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_account_number' => [
                'name' => __('Shipper Account Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Shipper Account Number',
                'id' => 'mso_fedex_lfq_account_number',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_third_party_account_number' => [
                'name' => __('Third Party Account Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Third Party Account Number',
                'id' => 'mso_fedex_lfq_third_party_account_number',
                'class' => 'mso_child_carrier mso_optional'
            ],
            // Billing Details
            'mso_fedex_lfq_billing_settings' => [
                'name' => __('Billing Details', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_fedex_lfq_billing_address' => [
                'name' => __('Address', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Address',
                'id' => 'mso_fedex_lfq_billing_address',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_billing_city' => [
                'name' => __('City', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'City',
                'id' => 'mso_fedex_lfq_billing_city',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_billing_state' => [
                'name' => __('State', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'State',
                'id' => 'mso_fedex_lfq_billing_state',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_billing_zip' => [
                'name' => __('Zip', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Zip',
                'id' => 'mso_fedex_lfq_billing_zip',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_billing_country' => [
                'name' => __('Country', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Country',
                'id' => 'mso_fedex_lfq_billing_country',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            // Physical Details
            'mso_fedex_lfq_physical_settings' => [
                'name' => __('Physical Details', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Key',
                'class' => 'hidden mso_optional',
            ],
            'mso_fedex_lfq_physical_address' => [
                'name' => __('Address', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Address',
                'id' => 'mso_fedex_lfq_physical_address',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_physical_city' => [
                'name' => __('City', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'City',
                'id' => 'mso_fedex_lfq_physical_city',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_physical_state' => [
                'name' => __('State', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'State',
                'id' => 'mso_fedex_lfq_physical_state',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_physical_zip' => [
                'name' => __('Zip', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Zip',
                'id' => 'mso_fedex_lfq_physical_zip',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_physical_country' => [
                'name' => __('Country', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Country',
                'id' => 'mso_fedex_lfq_physical_country',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_fedex_lfq_credentials_status' => [
                'name' => __('Test Fedex Freight Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
//                'id' => 'mso_fedex_lfq_credentials_status',
                'id' => '',
                'desc' => mso_cfas(get_option('mso_fedex_lfq_credentials_status')),
                'class' => 'hidden mso_carrier_end mso_child_carrier mso_api_credentials_status'
            ],
//            'mso_fedex_lfq_carrier_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_fedex_lfq_carrier_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional',
//            ],
            'mso_fedex_lfq_accessorials' => [
                'name' => __('Accessorials', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_fedex_lfq_rad' => [
                'name' => __('Residential delivery', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_lfq_rad',
                'class' => 'mso_child_carrier'
            ],
            'mso_fedex_lfq_liftgate' => [
                'name' => __('Liftgate delivery', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_fedex_lfq_liftgate',
                'class' => 'mso_child_carrier'
            ],
            'mso_fedex_lfq_additional_details' => [
                'name' => __('Additional Details', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_fedex_lfq_markup' => [
                'name' => __('Markup', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_fedex_lfq_markup',
                'placeholder' => 'Markup in USD ($)',
//                'desc' => 'Please enter the markup in the format of 1.00 or as a percentage (e.g. 5.0%).',
                'desc' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to the "Fedex Freight" shipping services. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_child_carrier'
            ],
            'mso_fedex_lfq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_fedex_lfq_end',
            ],
        ];
    }
}