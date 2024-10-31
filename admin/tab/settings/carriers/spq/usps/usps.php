<?php

namespace MsoUsps;

class MsoUsps
{
    static public function mso_init()
    {
//        $status_description = $status_direction = '';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_USPS_GET]))) {
//                $status_direction = 'mso_disabled';
////            $status_description = '<span class="mso_err_status_description"><b>Error!</b> ' . MSO_PLAN_DESC . '</span>';
//                $status_description = '<span class="notice notice-error mso_err_status_description"><b>Error!</b> ' . MSO_PAID_PLAN_FEATURE . '</span>';
//            } elseif (MSO_PLAN_STATUS == 'success' && (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_USPS_GET]))) {
//                $current_carrier = MSO_SUBSCRIPTIONS[MSO_USPS_GET];
//                $carrier = $current_carrier['carrier'];
//                $current_period_end = $current_carrier['current_period_end'];
////                $description = "Your $carrier plan will expire on $current_period_end";
//                $description = "Your $carrier plan would be renewed on " . date('F jS, Y', strtotime($current_period_end));
////            $status_description = '<span class="mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//                $status_description = '<span class="notice notice-success mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//            }
//        }

        return [
            'mso_usps_spq' => [
                'name' => __('WooCommerce USPS Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
                'class' => 'hidden',
            ],
            'mso_usps_spq_carrier_id' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'value' => 'mso_usps_sqp',
                'class' => 'hidden mso_connection mso_optional mso_carrier_id',
            ],
            'mso_usps_spq_carrier_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => mso_plan_status(MSO_USPS_GET),
                'id' => 'mso_usps_spq_carrier_plan_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional',
            ],
            'mso_usps_spq_carrier_enable' => [
                'name' => __('Enable / Disable', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'desc' => MSO_ENABLE_DESC,
                'id' => 'mso_usps_spq_carrier_enable',
                'class' => 'mso_carrier_settings_on_off'
            ],
            'mso_usps_spq_connection' => [
                'name' => __('API Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_connection mso_optional',
            ],
            'mso_usps_spq_user_id' => [
                'name' => __('User ID', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'User ID',
                'id' => 'mso_usps_spq_user_id',
                'class' => 'mso_child_carrier mso_asteric'
            ],
//            'mso_usps_spq_username' => [
//                'name' => __('Username', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Username',
//                'id' => 'mso_usps_spq_username',
//                'class' => 'mso_child_carrier mso_asteric'
//            ],
            'mso_usps_spq_password' => [
                'name' => __('Password', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Password',
                'id' => 'mso_usps_spq_password',
                'class' => 'mso_child_carrier mso_optional'
            ],
//            'mso_usps_spq_access_key' => [
//                'name' => __('Access Key', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Access Key',
//                'id' => 'mso_usps_spq_access_key',
//                'class' => 'mso_child_carrier mso_asteric'
//            ],
            'mso_usps_spq_credentials_status' => [
                'name' => __('Test USPS Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
//                'id' => 'mso_usps_spq_credentials_status',
                'id' => '',
                'desc' => mso_cfas(get_option('mso_usps_spq_credentials_status')),
                'class' => 'hidden mso_carrier_end mso_child_carrier mso_api_credentials_status'
            ],
//            'mso_usps_spq_carrier_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_usps_spq_carrier_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional',
//            ],
            'mso_usps_spq_domestic_services' => [
                'name' => __('Domestic Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_usps_spq_domestic_services',
                'class' => 'hidden mso_optional',
            ],
            // Select All
            'mso_usps_spq_domestic_services_sa' => [
                'name' => __('Select All', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_domestic_services_sa',
                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
            ],
            'mso_usps_spq_add_space_1' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_usps_spq_add_space_1',
                'class' => 'hidden mso_carrier_partition_64 mso_optional'
            ],
            // First-Class Mail
//            'mso_usps_spq_first_class_mail_action' => [
//                'name' => __('First-Class Mail', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_usps_spq_first_class_mail_action',
//                'class' => 'mso_usps_dsa mso_carrier_partition'
//            ],
//            'mso_usps_spq_first_class_mail_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'First-Class Mail',
//                'id' => 'mso_usps_spq_first_class_mail_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "First-Class Mail" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_usps_spq_first_class_mail_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_usps_spq_first_class_mail_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "First-Class Mail" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
            // Priority Mail
            'mso_usps_spq_priority_mail_action' => [
                'name' => __('Priority Mail', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_priority_mail_action',
                'class' => 'mso_usps_dsa mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Priority Mail',
                'id' => 'mso_usps_spq_priority_mail_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Priority Mail" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_priority_mail_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Priority Mail" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Priority Mail Express
            'mso_usps_spq_priority_mail_express_action' => [
                'name' => __('Priority Mail Express', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_priority_mail_express_action',
                'class' => 'mso_usps_dsa mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_express_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Priority Mail Express',
                'id' => 'mso_usps_spq_priority_mail_express_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Priority Mail Express" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_express_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_priority_mail_express_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Priority Mail Express" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Priority Mail Flat Rate
            'mso_usps_spq_priority_mail_flat_rate_action' => [
                'name' => __('Priority Mail Flat Rate', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_priority_mail_flat_rate_action',
                'class' => 'mso_usps_dsa mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_flat_rate_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Priority Mail Flat Rate',
                'id' => 'mso_usps_spq_priority_mail_flat_rate_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Priority Mail Flat Rate" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_flat_rate_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_priority_mail_flat_rate_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Priority Mail Flat Rate" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Media Mail
            'mso_usps_spq_media_mail_action' => [
                'name' => __('Media Mail', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_media_mail_action',
                'class' => 'mso_usps_dsa mso_carrier_partition'
            ],
            'mso_usps_spq_media_mail_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Media Mail',
                'id' => 'mso_usps_spq_media_mail_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Media Mail" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_media_mail_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_media_mail_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Media Mail" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Ground Advantage
            'mso_usps_spq_ground_advantage_action' => [
                'name' => __('Ground Advantage', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_ground_advantage_action',
                'class' => 'mso_usps_dsa mso_carrier_partition'
            ],
            'mso_usps_spq_ground_advantage_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Ground Advantage',
                'id' => 'mso_usps_spq_ground_advantage_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Ground Advantage" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_ground_advantage_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_ground_advantage_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Ground Advantage" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Retail Ground
            'mso_usps_spq_retail_ground_action' => [
                'name' => __('Retail Ground', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_retail_ground_action',
                'class' => 'mso_usps_dsa mso_carrier_partition'
            ],
            'mso_usps_spq_retail_ground_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Retail Ground',
                'id' => 'mso_usps_spq_retail_ground_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Retail Ground" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_retail_ground_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_retail_ground_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Retail Ground" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // First-Class Package Service
//            'mso_usps_spq_first_class_package_service_action' => [
//                'name' => __('First-Class Package Service', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_usps_spq_first_class_package_service_action',
//                'class' => 'mso_usps_dsa mso_carrier_partition'
//            ],
//            'mso_usps_spq_first_class_package_service_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'First-Class Package Service',
//                'id' => 'mso_usps_spq_first_class_package_service_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "First-Class Package Service" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_usps_spq_first_class_package_service_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_usps_spq_first_class_package_service_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "First-Class Package Service" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
            // Library Mail
            'mso_usps_spq_library_mail_action' => [
                'name' => __('Library Mail', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_library_mail_action',
                'class' => 'mso_usps_dsa mso_carrier_partition'
            ],
            'mso_usps_spq_library_mail_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Library Mail',
                'id' => 'mso_usps_spq_library_mail_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Library Mail" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_library_mail_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_library_mail_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Library Mail" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_international_services' => [
                'name' => __('International Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_usps_spq_international_services',
                'class' => 'hidden mso_optional',
            ],
            // Select All
            'mso_usps_spq_international_services_sa' => [
                'name' => __('Select All', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_international_services_sa',
                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
            ],
            'mso_usps_spq_add_space_2' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_usps_spq_add_space_2',
                'class' => 'hidden mso_carrier_partition_64 mso_optional'
            ],
            // First-Class Package International Service
            'mso_usps_spq_first_class_package_international_service_action' => [
                'name' => __('First-Class Package International Service', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_first_class_package_international_service_action',
                'class' => 'mso_usps_isa mso_carrier_partition'
            ],
            'mso_usps_spq_first_class_package_international_service_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'First-Class Package International Service',
                'id' => 'mso_usps_spq_first_class_package_international_service_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "First-Class Package International Service" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_first_class_package_international_service_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_first_class_package_international_service_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "First-Class Package International Service" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Priority Mail International
            'mso_usps_spq_priority_mail_international_action' => [
                'name' => __('Priority Mail International', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_priority_mail_international_action',
                'class' => 'mso_usps_isa mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_international_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Priority Mail International',
                'id' => 'mso_usps_spq_priority_mail_international_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Priority Mail International" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_international_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_priority_mail_international_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Priority Mail International" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Priority Mail Express International
            'mso_usps_spq_priority_mail_express_international_action' => [
                'name' => __('Priority Mail Express International', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_priority_mail_express_international_action',
                'class' => 'mso_usps_isa mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_express_international_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Priority Mail Express International',
                'id' => 'mso_usps_spq_priority_mail_express_international_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Priority Mail Express International" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_usps_spq_priority_mail_express_international_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_usps_spq_priority_mail_express_international_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Priority Mail Express International" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Global Express Guaranteed
//            'mso_usps_spq_global_express_guaranteed_action' => [
//                'name' => __('Global Express Guaranteed', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_usps_spq_global_express_guaranteed_action',
//                'class' => 'mso_usps_isa mso_carrier_partition'
//            ],
//            'mso_usps_spq_global_express_guaranteed_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Global Express Guaranteed',
//                'id' => 'mso_usps_spq_global_express_guaranteed_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Global Express Guaranteed" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_usps_spq_global_express_guaranteed_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_usps_spq_global_express_guaranteed_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Global Express Guaranteed" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
            // International Shipping for Flat Rate Boxes and Envelopes
//            'mso_usps_spq_international_shipping_for_flat_rate_boxes_and_envelopes_action' => [
//                'name' => __('International Shipping for Flat Rate Boxes and Envelopes', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_usps_spq_international_shipping_for_flat_rate_boxes_and_envelopes_action',
//                'class' => 'mso_usps_isa mso_carrier_partition'
//            ],
//            'mso_usps_spq_international_shipping_for_flat_rate_boxes_and_envelopes_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'International Shipping for Flat Rate Boxes and Envelopes',
//                'id' => 'mso_usps_spq_international_shipping_for_flat_rate_boxes_and_envelopes_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "International Shipping for Flat Rate Boxes and Envelopes" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_usps_spq_international_shipping_for_flat_rate_boxes_and_envelopes_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_usps_spq_international_shipping_for_flat_rate_boxes_and_envelopes_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "International Shipping for Flat Rate Boxes and Envelopes" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_usps_spq_settings' => [
//                'name' => __('Accessorials', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'class' => 'hidden mso_optional',
//            ],
//            'mso_usps_spq_rad' => [
//                'name' => __('Residential delivery', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_usps_spq_rad',
//                'class' => 'mso_child_carrier'
//            ],
            'mso_usps_spq_shipping_options' => [
                'name' => __('Shipping Options', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_usps_spq_delivery_estimate' => [
                'name' => __('Display estimated delivery date.', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_usps_spq_delivery_estimate',
                'class' => 'mso_child_carrier'
            ],
            'mso_usps_spq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_usps_spq_end',
            ],
        ];
    }
}