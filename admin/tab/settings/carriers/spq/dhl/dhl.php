<?php

namespace MsoDhl;

class MsoDhl
{
    static public function mso_init()
    {
//        $status_description = $status_direction = '';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_DHL_GET]))) {
//                $status_direction = 'mso_disabled';
////            $status_description = '<span class="mso_err_status_description"><b>Error!</b> ' . MSO_PLAN_DESC . '</span>';
//                $status_description = '<span class="notice notice-error mso_err_status_description"><b>Error!</b> ' . MSO_PAID_PLAN_FEATURE . '</span>';
//            } elseif (MSO_PLAN_STATUS == 'success' && (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_DHL_GET]))) {
//                $current_carrier = MSO_SUBSCRIPTIONS[MSO_DHL_GET];
//                $carrier = $current_carrier['carrier'];
//                $current_period_end = $current_carrier['current_period_end'];
////                $description = "Your $carrier plan will expire on $current_period_end";
//                $description = "Your $carrier plan would be renewed on " . date('F jS, Y', strtotime($current_period_end));
////            $status_description = '<span class="mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//                $status_description = '<span class="notice notice-success mso_succ_status_description"><b>Success!</b> ' . $description . '</span>';
//            }
//        }

        return [
            'mso_dhl_spq' => [
                'name' => __('WooCommerce DHL Express Shipping', 'woocommerce-settings-mso'),
                'type' => 'title',
                'class' => 'hidden',
            ],
            'mso_dhl_spq_carrier_id' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'value' => 'mso_dhl_sqp',
                'class' => 'hidden mso_connection mso_optional mso_carrier_id',
            ],
            'mso_dhl_spq_carrier_plan_status' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'desc' => mso_plan_status(MSO_DHL_GET),
                'id' => 'mso_dhl_spq_carrier_plan_status',
                'class' => 'hidden mso_carrier_plan_status mso_optional',
            ],
            'mso_dhl_spq_carrier_enable' => [
                'name' => __('Enable / Disable', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'desc' => MSO_ENABLE_DESC,
                'id' => 'mso_dhl_spq_carrier_enable',
                'class' => 'mso_carrier_settings_on_off'
            ],
            'mso_dhl_spq_connection' => [
                'name' => __('API Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_connection mso_optional',
            ],
//            'mso_dhl_spq_user_id' => [
//                'name' => __('User ID', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'User ID',
//                'id' => 'mso_dhl_spq_user_id',
//                'class' => 'mso_child_carrier mso_asteric'
//            ],
            'mso_dhl_spq_username' => [
                'name' => __('Username', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Username',
                'id' => 'mso_dhl_spq_username',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_dhl_spq_password' => [
                'name' => __('Password', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Password',
                'id' => 'mso_dhl_spq_password',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_dhl_spq_account_number' => [
                'name' => __('Account Number', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Account Number',
                'id' => 'mso_dhl_spq_account_number',
                'class' => 'mso_child_carrier mso_asteric'
            ],
            'mso_dhl_spq_credentials_status' => [
                'name' => __('Test DHL Connection', 'woocommerce-settings-mso'),
                'type' => 'text',
//                'id' => 'mso_dhl_spq_credentials_status',
                'id' => '',
                'desc' => mso_cfas(get_option('mso_dhl_spq_credentials_status')),
                'class' => 'hidden mso_carrier_end mso_child_carrier mso_api_credentials_status'
            ],
//            'mso_dhl_spq_carrier_plan_status' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'desc' => $status_description,
//                'id' => 'mso_dhl_spq_carrier_plan_status',
//                'class' => 'hidden mso_carrier_plan_status mso_optional',
//            ],
            'mso_dhl_spq_express_services' => [
                'name' => __('Express Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_dhl_spq_express_services',
                'class' => 'hidden mso_optional',
            ],
            // Select All
            'mso_dhl_spq_express_services_sa' => [
                'name' => __('Select All', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_services_sa',
                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
            ],
            'mso_dhl_spq_add_space_1' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_dhl_spq_add_space_1',
                'class' => 'hidden mso_carrier_partition_64 mso_optional'
            ],
            // Domestic
            'mso_dhl_spq_domestic_services_heading' => [
                'name' => __('Domestic', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_dhl_spq_domestic_services_heading',
                'class' => 'hidden mso_optional',
            ],
            // Domestic Express
            'mso_dhl_spq_domestic_express_action' => [
                'name' => __('Domestic Express', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_domestic_express_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_domestic_express_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Domestic Express',
                'id' => 'mso_dhl_spq_domestic_express_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Domestic Express" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_domestic_express_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_domestic_express_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Domestic Express" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Domestic Express 12:00
            'mso_dhl_spq_domestic_express_12_00_action' => [
                'name' => __('Domestic Express 12:00', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_domestic_express_12_00_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_domestic_express_12_00_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Domestic Express 12:00',
                'id' => 'mso_dhl_spq_domestic_express_12_00_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Domestic Express 12:00" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_domestic_express_12_00_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_domestic_express_12_00_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Domestic Express 12:00" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Domestic Express 9:00
            'mso_dhl_spq_domestic_express_9_00_action' => [
                'name' => __('Domestic Express 9:00', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_domestic_express_9_00_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_domestic_express_9_00_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Domestic Express 9:00',
                'id' => 'mso_dhl_spq_domestic_express_9_00_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Domestic Express 9:00" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_domestic_express_9_00_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_domestic_express_9_00_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Domestic Express 9:00" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Rest Of World (Non-Documents)
            'mso_dhl_spq_non_documents_heading' => [
                'name' => __('Rest Of World (Non-Documents)', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_dhl_spq_non_documents_heading',
                'class' => 'hidden mso_optional',
            ],
            // Express Worldwide
            'mso_dhl_spq_express_worldwide_action' => [
                'name' => __('Express Worldwide', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_worldwide_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_worldwide_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express Worldwide',
                'id' => 'mso_dhl_spq_express_worldwide_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express Worldwide" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_worldwide_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_worldwide_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express Worldwide" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Express 12:00
            'mso_dhl_spq_express_12_00_action' => [
                'name' => __('Express 12:00', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_12_00_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_12_00_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express 12:00',
                'id' => 'mso_dhl_spq_express_12_00_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express 12:00" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_12_00_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_12_00_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express 12:00" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Express 10:30
            'mso_dhl_spq_express_10_30_action' => [
                'name' => __('Express 10:30', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_10_30_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_10_30_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express 10:30',
                'id' => 'mso_dhl_spq_express_10_30_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express 10:30" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_10_30_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_10_30_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express 10:30" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Express 9:00
            'mso_dhl_spq_express_9_00_action' => [
                'name' => __('Express 9:00', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_9_00_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_9_00_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express 9:00',
                'id' => 'mso_dhl_spq_express_9_00_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express 9:00" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_9_00_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_9_00_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express 9:00" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Rest Of World (Documents)
            'mso_dhl_spq_documents_heading' => [
                'name' => __('Rest Of World (Documents)', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_dhl_spq_documents_heading',
                'class' => 'hidden mso_optional',
            ],
            // Express Worldwide (Documents)
            'mso_dhl_spq_express_worldwide_doc_action' => [
                'name' => __('Express Worldwide (Documents)', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_worldwide_doc_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_worldwide_doc_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express Worldwide (Documents)',
                'id' => 'mso_dhl_spq_express_worldwide_doc_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express Worldwide (Documents)" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_worldwide_doc_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_worldwide_doc_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express Worldwide (Documents)" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Express 12:00 (Documents)
            'mso_dhl_spq_express_12_00_doc_action' => [
                'name' => __('Express 12:00 (Documents)', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_12_00_doc_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_12_00_doc_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express 12:00 (Documents)',
                'id' => 'mso_dhl_spq_express_12_00_doc_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express 12:00 (Documents)" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_12_00_doc_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_12_00_doc_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express 12:00 (Documents)" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Express 10:30 (Documents)
            'mso_dhl_spq_express_10_30_doc_action' => [
                'name' => __('Express 10:30 (Documents)', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_10_30_doc_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_10_30_doc_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express 10:30 (Documents)',
                'id' => 'mso_dhl_spq_express_10_30_doc_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express 10:30 (Documents)" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_10_30_doc_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_10_30_doc_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express 10:30 (Documents)" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Express 9:00 (Documents)
            'mso_dhl_spq_express_9_00_doc_action' => [
                'name' => __('Express 9:00 (Documents)', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_express_9_00_doc_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_express_9_00_doc_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Express 9:00 (Documents)',
                'id' => 'mso_dhl_spq_express_9_00_doc_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Express 9:00 (Documents)" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_express_9_00_doc_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_express_9_00_doc_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Express 9:00 (Documents)" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Road Services
            'mso_dhl_spq_road_services_heading' => [
                'name' => __('Road Services', 'woocommerce-settings-mso'),
                'type' => 'text',
                'id' => 'mso_dhl_spq_road_services_heading',
                'class' => 'hidden mso_optional',
            ],
            // Economy Select International
            'mso_dhl_spq_economy_select_international_action' => [
                'name' => __('Economy Select International', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_economy_select_international_action',
                'class' => 'mso_dhl_dsa mso_carrier_partition'
            ],
            'mso_dhl_spq_economy_select_international_label' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Economy Select International',
                'id' => 'mso_dhl_spq_economy_select_international_label',
                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Economy Select International" on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            'mso_dhl_spq_economy_select_international_markup' => [
                'name' => __('', 'woocommerce-settings-mso'),
                'type' => 'text',
                'placeholder' => 'Markup in USD ($)',
                'id' => 'mso_dhl_spq_economy_select_international_markup',
                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Economy Select International" shipping price. This will be reflected on the cart and checkout pages.',
                'class' => 'mso_carrier_partition'
            ],
            // Library Mail
//            'mso_dhl_spq_library_mail_action' => [
//                'name' => __('Library Mail', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_library_mail_action',
//                'class' => 'mso_dhl_dsa mso_carrier_partition'
//            ],
//            'mso_dhl_spq_library_mail_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Library Mail',
//                'id' => 'mso_dhl_spq_library_mail_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Library Mail" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_library_mail_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_dhl_spq_library_mail_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Library Mail" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_international_services' => [
//                'name' => __('International Services', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'id' => 'mso_dhl_spq_international_services',
//                'class' => 'hidden mso_optional',
//            ],
//            // Select All
//            'mso_dhl_spq_international_services_sa' => [
//                'name' => __('Select All', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_international_services_sa',
//                'class' => 'mso_services_sa mso_carrier_partition mso_optional'
//            ],
//            'mso_dhl_spq_add_space_2' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'id' => 'mso_dhl_spq_add_space_2',
//                'class' => 'hidden mso_carrier_partition_64 mso_optional'
//            ],
//            // First-Class Package International Service
//            'mso_dhl_spq_first_class_package_international_service_action' => [
//                'name' => __('First-Class Package International Service', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_first_class_package_international_service_action',
//                'class' => 'mso_dhl_isa mso_carrier_partition'
//            ],
//            'mso_dhl_spq_first_class_package_international_service_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'First-Class Package International Service',
//                'id' => 'mso_dhl_spq_first_class_package_international_service_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "First-Class Package International Service" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_first_class_package_international_service_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_dhl_spq_first_class_package_international_service_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "First-Class Package International Service" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Priority Mail International
//            'mso_dhl_spq_priority_mail_international_action' => [
//                'name' => __('Priority Mail International', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_priority_mail_international_action',
//                'class' => 'mso_dhl_isa mso_carrier_partition'
//            ],
//            'mso_dhl_spq_priority_mail_international_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Priority Mail International',
//                'id' => 'mso_dhl_spq_priority_mail_international_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Priority Mail International" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_priority_mail_international_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_dhl_spq_priority_mail_international_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Priority Mail International" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Priority Mail Express International
//            'mso_dhl_spq_priority_mail_express_international_action' => [
//                'name' => __('Priority Mail Express International', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_priority_mail_express_international_action',
//                'class' => 'mso_dhl_isa mso_carrier_partition'
//            ],
//            'mso_dhl_spq_priority_mail_express_international_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Priority Mail Express International',
//                'id' => 'mso_dhl_spq_priority_mail_express_international_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Priority Mail Express International" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_priority_mail_express_international_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_dhl_spq_priority_mail_express_international_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Priority Mail Express International" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            // Global Express Guaranteed
//            'mso_dhl_spq_global_express_guaranteed_action' => [
//                'name' => __('Global Express Guaranteed', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_global_express_guaranteed_action',
//                'class' => 'mso_dhl_isa mso_carrier_partition'
//            ],
//            'mso_dhl_spq_global_express_guaranteed_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Global Express Guaranteed',
//                'id' => 'mso_dhl_spq_global_express_guaranteed_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "Global Express Guaranteed" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_global_express_guaranteed_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_dhl_spq_global_express_guaranteed_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "Global Express Guaranteed" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            // International Shipping for Flat Rate Boxes and Envelopes
//            'mso_dhl_spq_international_shipping_for_flat_rate_boxes_and_envelopes_action' => [
//                'name' => __('International Shipping for Flat Rate Boxes and Envelopes', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_international_shipping_for_flat_rate_boxes_and_envelopes_action',
//                'class' => 'mso_dhl_isa mso_carrier_partition'
//            ],
//            'mso_dhl_spq_international_shipping_for_flat_rate_boxes_and_envelopes_label' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'International Shipping for Flat Rate Boxes and Envelopes',
//                'id' => 'mso_dhl_spq_international_shipping_for_flat_rate_boxes_and_envelopes_label',
//                'desc_tip' => 'Please specify the custom name that will be displayed instead of "International Shipping for Flat Rate Boxes and Envelopes" on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_international_shipping_for_flat_rate_boxes_and_envelopes_markup' => [
//                'name' => __('', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'placeholder' => 'Markup in USD ($)',
//                'id' => 'mso_dhl_spq_international_shipping_for_flat_rate_boxes_and_envelopes_markup',
//                'desc_tip' => 'Please input the additional cost (e.g. 1.00) or percentage (e.g. 5.0%) in USD ($) to be added to "International Shipping for Flat Rate Boxes and Envelopes" shipping price. This will be reflected on the cart and checkout pages.',
//                'class' => 'mso_carrier_partition'
//            ],
//            'mso_dhl_spq_settings' => [
//                'name' => __('Accessorials', 'woocommerce-settings-mso'),
//                'type' => 'text',
//                'class' => 'hidden mso_optional',
//            ],
//            'mso_dhl_spq_rad' => [
//                'name' => __('Residential delivery', 'woocommerce-settings-mso'),
//                'type' => 'checkbox',
//                'id' => 'mso_dhl_spq_rad',
//                'class' => 'mso_child_carrier'
//            ],
            'mso_dhl_spq_shipping_options' => [
                'name' => __('Shipping Options', 'woocommerce-settings-mso'),
                'type' => 'text',
                'class' => 'hidden mso_optional',
            ],
            'mso_dhl_spq_delivery_estimate' => [
                'name' => __('Display estimated delivery date.', 'woocommerce-settings-mso'),
                'type' => 'checkbox',
                'id' => 'mso_dhl_spq_delivery_estimate',
                'class' => 'mso_child_carrier'
            ],
            'mso_dhl_spq_end' => [
                'type' => 'sectionend',
                'id' => 'mso_dhl_spq_end',
            ],
        ];
    }
}