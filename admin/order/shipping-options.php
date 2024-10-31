<?php

namespace MsoOrderShippingOptions;

use MsoDhlShipment\MsoDhlShipment;
use MsoFedexLfqShipment\MsoFedexLfqShipment;
use MsoFedexShipment\MsoFedexShipment;
use MsoUpsLfqShipment\MsoUpsLfqShipment;
use MsoUpsShipment\MsoUpsShipment;
use MsoUspsShipment\MsoUspsShipment;

class MsoOrderShippingOptions
{

    public function __construct()
    {
        add_action('woocommerce_order_actions', [$this, 'mso_order'], 10, 1);

        // Order get quotes
        add_action('wp_ajax_mso_shipment_order', [$this, 'mso_shipment_order_get_quotes']);

        // Order new shipment
        add_action('wp_ajax_mso_new_shipment', [$this, 'mso_shipment_order_new_shipment']);

        // Recreate shipment
        add_action('wp_ajax_mso_order_recreate_shipment_allowed', [$this, 'mso_order_recreate_shipment_allowed']);

        // Order create shipment
        add_action('wp_ajax_mso_shipment_order_placed', [$this, 'mso_shipment_order_ship']);

        // Order cancel shipment
        add_action('wp_ajax_mso_cancel_shipment_hook', [$this, 'mso_cancel_shipment_hook']);
    }

    /**
     * Order list.
     */
    public function mso_order_shipments_list($mso_shipments, $location_options, $order_id, $mso_order_items, $mso_ship_num, $ship_to_address_label)
    {
//        echo '<pre>';
//        print_r($location_options);
//        echo '</pre>'; die;
        foreach ($mso_shipments as $key => $mso_shipment) {
            $shipment_num = $key;
            $accessorials = (isset($mso_shipment['accessorials'])) ? $mso_shipment['accessorials'] : [];
            $ship_from = [];
            if (isset($mso_shipment['response']['ship_from'])) {
                $ship_from = $mso_shipment['response']['ship_from'];
            } elseif (isset($mso_shipment['ship_from'])) {
                $ship_from = $mso_shipment['ship_from'];
            }
            $origin_id = (isset($ship_from['id'])) ? $ship_from['id'] : 'store_address';

            $origin_address_tooltip = '<span class="mso_orig_address_wrapper mso_wrapper"><span class="mso_tooltip">' . MSO_ORIG_ADDRESS_TOOLTIP . '</span></span>';
            $origin_template = [
                'mso_order_shipment_origin' => [
                    'name' => 'From',
                    'type' => 'select',
                    'default' => $origin_id,
                    'desc' => 'Edit Origins',
                    'id' => 'mso_order_shipment_origin',
                    'options' => $location_options,
                    'tr_class' => 'mso_tr_order_shipment_origin',
                    'tooltip' => $origin_address_tooltip
                ]
            ];

            $accesso_tooltip = '<span class="mso_accesso_wrapper mso_wrapper"><span class="mso_tooltip">' . MSO_ACCESSO_ADDRESS_TOOLTIP . '</span></span>';
            $accessorials_heading_template = [
                'mso_accessorials' => [
                    'name' => 'Accessorials',
                    'type' => 'title',
                    'tr_class' => 'mso_accessorials_heading',
                    'tooltip' => $accesso_tooltip
                ]
            ];

            $accessorial_options = [
                'mso_residential' => [
                    'name' => 'Residential',
                    'type' => 'checkbox',
                    'default' => (isset($accessorials['residential_delivery'])) ? $accessorials['residential_delivery'] : 'no',
                    'id' => 'mso_residential',
                    'tr_class' => 'mso_order_child mso_order_accessorial',
                ],
                'mso_liftgate' => [
                    'name' => 'Liftgate',
                    'type' => 'checkbox',
                    'default' => (isset($accessorials['liftgate_delivery'])) ? $accessorials['liftgate_delivery'] : 'no',
                    'id' => 'mso_liftgate',
                    'tr_class' => 'mso_order_child mso_order_accessorial',
                ],
            ];

            $items_tooltip = '<span class="mso_add_items_wrapper mso_wrapper"><span class="mso_tooltip">' . MSO_ITEMS_TOOLTIP . '</span></span>';
            $itmes_heading_template = [
                'mso_items' => [
                    'name' => 'Items',
                    'type' => 'title',
                    'tr_class' => 'mso_itmes_heading',
                    'tooltip' => $items_tooltip
                ]
            ];

            $ship_options_heading_template = [
                'mso_ship' => [
                    'name' => 'Shipping Options',
                    'type' => 'title',
                    'tr_class' => 'mso_ship_options_heading',
                ]
            ];

            $dest_address_tooltip = '<span class="mso_dest_address_wrapper mso_wrapper"><span class="mso_tooltip">' . MSO_DEST_ADDRESS_TOOLTIP . '</span></span>';
            $destination_address = [
                'mso_destination' => [
                    'name' => 'To',
                    'type' => 'title',
                    'tr_class' => 'mso_order_destination',
                    'desc' => $ship_to_address_label,
                    'tooltip' => $dest_address_tooltip
                ]
            ];

//            echo apply_filters('mso_form_template', $origin_template);
            echo '<div class="mso_order_ship_action">';
//            echo '<input onclick="mso_order_shipment_enable_disable(this,event)" type="checkbox" checked="checked">&nbsp<span style="font-weight: 500; font-size: 13px; padding-top: 2px">Enable / Disable</span>';
            echo '<span style="font-weight: 500; font-size: 13px; padding-top: 2px">Enable / Disable</span> &nbsp <input onclick="mso_order_shipment_enable_disable(this,event)" type="checkbox" checked="checked"> &nbsp Enable the shipment creation process.';
            echo '</div>';

            echo '<div data-mso_ship_num="' . esc_attr($mso_ship_num) . '" class="mso_order_shipment"><span class="mso_shipment_num_text">Shipment</span><span title="Shipment Number" class="mso_order_shipment_number">' . esc_attr($mso_ship_num) . '</span> <span onclick="mso_order_shipment_remove(this,event)" class="ui-icon mso_shipment_remove ui-icon-closethick"></div>';

            echo '</form>';
            echo '<form class="mso_flex_template" method="post">';


            // Order shipment options
            echo '<div class="mso_order_shipment_options">';
            // *** Left block ***
            echo '<div class="mso_order_lb">';
            // From
            echo '<div class="mso_order_from">';
            echo apply_filters('mso_form_template', $origin_template);
            echo '</div>';
            // To
            echo '<div class="mso_order_to">';
            echo apply_filters('mso_form_template', $destination_address);
            echo '</div>';
            // Items
            $mso_items = [];
            if (isset($mso_shipment['response']['items'])) {
                $mso_items = $mso_shipment['response']['items'];
            } elseif (isset($mso_shipment['items'])) {
                $mso_items = $mso_shipment['items'];
            }
            echo '<div class="mso_order_items">';
            echo apply_filters('mso_form_template', $itmes_heading_template);
            echo '<ul class="mso_items_sortable">';
            $mso_items = !empty($mso_items) && is_array($mso_items) ? $mso_items : [];
            foreach ($mso_items as $mso_item_id => $mso_item) {
                $product_id = $variation_id = 0;
                $quantity = $title = '';
                extract($mso_item);
                $product_id = $variation_id > 0 ? $variation_id : $product_id;
                $item_data = wc_get_product($product_id);
                $title = wp_strip_all_tags($item_data->get_formatted_name());
                if (isset($mso_order_items[$product_id])) {
                    unset($mso_order_items[$product_id]);
                }
                echo '<li data-product_id="' . esc_attr($product_id) . '" class="ui-state-default"><span class="ui-icon mso_item_arrow ui-icon-arrowthick-2-n-s"></span><span onclick="mso_order_item_remove(this,event)" class="ui-icon mso_item_remove ui-icon-closethick"></span><span class="mso_item_quantity"><input type="number" value="' . esc_attr($quantity) . '"></span>' . esc_html($title) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            echo '</div>';

            // *** Right block ***
            echo '<div class="mso_order_rb">';
            // Accessorials
            echo '<div class="mso_order_accessorials">';
            echo apply_filters('mso_form_template', $accessorials_heading_template);
            echo apply_filters('mso_form_template', $accessorial_options);
            echo '</div>';

            echo apply_filters('mso_form_template', $ship_options_heading_template);
            ?>
            <div class="mso_order_main_tab">
                <ol>
                    <li data-tab="mso_order_shipments_labels">
                        <div>Label</div>
                    </li>
                    <li data-tab="mso_order_api_response">
                        <div>API Response</div>
                    </li>
                    <li data-tab="mso_order_package">
                        <div>Packaging</div>
                    </li>
                    <li class="msoorc" data-tab="mso_order_rates">
                        <div>Rates</div>
                    </li>
                </ol>
            </div>
            <?php
            $mso_api_response = [];
            $mso_carrier_name = '';
            echo '<div class="mso_order_shipments_labels mso_order_tab">'; // Start label showing div
            $created_ship_error = isset($mso_shipment['created_ship_error']) && strlen($mso_shipment['created_ship_error']) > 0 ? '<span class="mso_rate_error_message"><b>Error! </b> ' . $mso_shipment['created_ship_error'] . '</span>' : '';
            // TODO
            echo '<p class="mso_calculate_shipping_heading">Label</p>' . $created_ship_error;
//            echo '<p class="mso_calculate_shipping_heading">Label</p>';
            if (isset($mso_shipment['rate']) && !empty($mso_shipment['rate'])) {
                $pdf_icon = MSO_DIR_FILE . '/images/pdf.png';
                $carrier = $ups_spq_code = $fedex_spq_service = $packaging_type = $fedex_lfq_service = '';
                extract($mso_shipment['rate']);
                $mso_carrier_name = $carrier;
                switch ($carrier) {
                    case 'fedex':

                        $mso_fedex_shipment = MsoFedexShipment::mso_fedex_shipment($order_id, $shipment_num, $mso_shipment);
                        $mso_api_response = [];
                        extract($mso_fedex_shipment);

//                        echo '<div class="mso_ship_label_content">';
//                        $package_shipments = isset($mso_shipment['fedex_ship']) ? $this->mso_parsing_build_query($mso_shipment['fedex_ship']) : [];
//                        $fedex_labels = $fedex_sd = [];
//                        $fedex_sl = '';
//                        foreach ($package_shipments as $package_number => $response) {
//                            // Error handling
//                            if (isset($response['HighestSeverity'], $response['Notifications'], $response['Notifications']['Message']) && ($response['HighestSeverity'] == 'FAILURE' || $response['HighestSeverity'] == 'ERROR')) {
//                                echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['Notifications']['Message'] . '</span></br>';
//                            }
//
//                            $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-fedex-spq-' . 'package-' . $package_number;
//                            $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';
//
//                            if (isset($response['HighestSeverity']) && $response['HighestSeverity'] != 'FAILURE' && $response['HighestSeverity'] != 'ERROR') {
//
//                                if (isset($response['CompletedShipmentDetail'], $response['CompletedShipmentDetail']['CompletedPackageDetails'], $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'])) {
//                                    $fedex_sd[] = [
//                                        'TrackingIdType' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingIdType'],
//                                        'TrackingNumber' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber']
//                                    ];
//                                }
//
//                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'])) {
//                                    $base64_string_png = $response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'];
//                                    $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';
//                                    file_exists($png) ? unlink($png) : '';
//                                    file_put_contents($png, base64_decode($base64_string_png));
//                                }
//
//                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'])) {
//                                    $base64_string_png = $response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'];
//                                    $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';
//                                    file_exists($png) ? unlink($png) : '';
//                                    file_put_contents($png, base64_decode($base64_string_png));
//                                }
//
//                                if (file_exists($png)) {
//                                    $fedex_labels[] = $png_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.png';
//                                    $fedex_sl .= '<img src="' . esc_url($png_to_show) . '" class="mso_real_label_image" onclick="mso_file_to_click(this,1)" alt="Label Missing" alt="Missing Label"/>';
//                                }
//                            }
//                        }
//
//                        echo $fedex_sl;
//                        echo '</div>';
//                        if (!empty($fedex_sd)) {
//                            // TODO
////                            echo '<span data-carrier="fedex" data-post_data="' . http_build_query($fedex_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
//                            echo '<span data-carrier="fedex" data-post_data="' . implode(',', $fedex_labels) . '" class="form-control button-primary mso_print_all_shipment">Print All Shipment</span>';
//                        }
//                        // API response
//                        isset($package_shipments) ? $mso_api_response = $package_shipments : '';
                        break;

                    case 'usps':

                        $mso_usps_shipment = MsoUspsShipment::mso_usps_shipment($order_id, $shipment_num, $mso_shipment);
                        $mso_api_response = [];
                        extract($mso_usps_shipment);

//                        $response = isset($mso_shipment['usps_ship']) ? $this->mso_parsing_build_query($mso_shipment['usps_ship']) : [];
//                        $usps_sd = $usps_labels = [];
//                        $usps_sl = '';
//                        // Error handling
//                        if (isset($response['error'], $response['message'])) {
//                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['message'] . '</span></br>';
//                        }

                        break;

                    case 'ups':

                        $mso_ups_shipment = MsoUpsShipment::mso_ups_shipment($order_id, $shipment_num, $mso_shipment);
                        $mso_api_response = [];
                        extract($mso_ups_shipment);


//                        $response = isset($mso_shipment['ups_ship']) ? $this->mso_parsing_build_query($mso_shipment['ups_ship']) : [];
//                        $ups_sd = $ups_labels = [];
//                        $ups_sl = '';
//                        // Error handling
//                        if (isset($response['confirm'], $response['confirm']['Response'], $response['confirm']['Response']['Error'], $response['confirm']['Response']['Error']['ErrorDescription'], $response['confirm']['Response']['Error']['ErrorSeverity']) && (strtolower($response['confirm']['Response']['Error']['ErrorSeverity']) != 'warning')) {
//                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['confirm']['Response']['Error']['ErrorDescription'] . '</span></br>';
//                        } else if (isset($response['accept'], $response['accept']['Response'], $response['accept']['Response']['Error'], $response['accept']['Response']['Error']['ErrorDescription'])) {
//                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['accept']['Response']['Error']['ErrorDescription'] . '</span></br>';
//                        }
//
//                        echo '<div class="mso_ship_label_content">';
//                        if (isset($response['accept'], $response['accept']['ShipmentResults'], $response['accept']['ShipmentResults']['PackageResults'])) {
//                            $package_results = $response['accept']['ShipmentResults']['PackageResults'];
//                            (isset($package_results['LabelImage'])) ? $package_results = [$package_results] : '';
//                            foreach ($package_results as $package => $label_detail) {
//                                if (isset($label_detail['TrackingNumber'])) {
//                                    $ups_sd[] = [
//                                        'TrackingNumber' => $label_detail['TrackingNumber']
//                                    ];
//                                }
//
//                                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-ups-spq-' . $package;
//                                $png = MSO_MAIN_DIR . '/label/' . $label_id . '.png';
//                                $base64_string_png = isset($label_detail['LabelImage'], $label_detail['LabelImage']['GraphicImage']) ? $label_detail['LabelImage']['GraphicImage'] : '';
//                                if (strlen($base64_string_png) > 0) {
//                                    file_exists($png) ? unlink($png) : '';
//                                    file_put_contents($png, base64_decode($base64_string_png));
//                                }
//
//                                if (file_exists($png)) {
//                                    $ups_labels[] = $png_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.png';
//                                    $ups_sl .= '<img class="mso_real_label_image" onclick="mso_file_to_click(this,1)" src="' . esc_url($png_to_show) . '" alt="Label Missing" alt="Missing Label"/>';
//                                }
//                            }
//                        }
//
//                        echo $ups_sl;
//                        echo '</div>';
//
//                        if (!empty($ups_sd)) {
//                            // TODO
////                            echo '<span data-carrier="ups" data-post_data="' . http_build_query($ups_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
//                            echo '<span data-carrier="ups" data-post_data="' . implode(',', $ups_labels) . '" class="form-control button-primary mso_print_all_shipment">Print All Shipment</span>';
//                        }
//
//                        // API response
//                        (isset($response['confirm'])) ? $mso_api_response['confirm'] = $response['confirm'] : '';
//                        (isset($response['accept'])) ? $mso_api_response['accept'] = $response['accept'] : '';

                        break;
                    case 'ups_lfq':

                        $mso_ups_lfq_shipment = MsoUpsLfqShipment::mso_ups_lfq_shipment($order_id, $shipment_num, $mso_shipment);
                        $mso_api_response = [];
                        extract($mso_ups_lfq_shipment);

//                        $response = isset($mso_shipment['ups_lfq_ship']) ? $this->mso_parsing_build_query($mso_shipment['ups_lfq_ship']) : [];
//                        // Error handling
//                        if (isset($response['detail'], $response['detail']['Errors'], $response['detail']['Errors']['ErrorDetail'], $response['detail']['Errors']['ErrorDetail']['PrimaryErrorCode'], $response['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'])) {
//                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'] . '</span></br>';
//                        }
//
//                        echo '<div class="mso_ship_label_content">';
//                        $ups_lfq_sd = [];
//                        $ups_lfq_sl = '';
//                        if (isset($response['ShipmentResults'], $response['ShipmentResults'])) {
//                            $package_results = $response['ShipmentResults'];
//                            (isset($package_results['Documents'])) ? $package_results = [$package_results] : '';
//                            foreach ($package_results as $label_key => $package) {
//                                if (isset($package['ShipmentNumber'], $package['BOLID'])) {
//                                    $ups_lfq_sd[] = [
//                                        'ShipmentNumber' => $package['ShipmentNumber'],
//                                        'BOLID' => $package['BOLID']
//                                    ];
//                                }
//
//                                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-ups-lfq-' . $label_key;
//                                $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
//                                $base64_string_pdf = isset($package['Documents'], $package['Documents']['Image'], $package['Documents']['Image']['GraphicImage']) ? $package['Documents']['Image']['GraphicImage'] : '';
//                                if (strlen($base64_string_pdf) > 0) {
//                                    $ifp = fopen($pdf, 'wb');
//                                    fwrite($ifp, base64_decode($base64_string_pdf));
//                                    fclose($ifp);
//                                }
//
//                                if (file_exists($pdf)) {
//                                    $pdf_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.pdf';
//                                    $ups_lfq_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url($pdf_icon) . '" alt="Label Missing" alt="Missing Label"/>';
//                                }
//                            }
//                        }
//
//                        echo $ups_lfq_sl;
//                        echo '</div>';
//
//                        if (!empty($ups_lfq_sd)) {
//                            // TODO
////                            echo '<span data-carrier="ups_lfq" data-post_data="' . http_build_query($ups_lfq_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
//                        }
//
//                        // API response
//                        (isset($response['ShipmentResults'])) ? $mso_api_response = $response['ShipmentResults'] : '';

                        break;
                    case 'fedex_lfq':

                        $mso_fedex_lfq_shipment = MsoFedexLfqShipment::mso_fedex_lfq_shipment($order_id, $shipment_num, $mso_shipment);
                        $mso_api_response = [];
                        extract($mso_fedex_lfq_shipment);

//                        echo '<div class="mso_ship_label_content">';
//                        $package_shipments = isset($mso_shipment['fedex_lfq_ship']) ? $this->mso_parsing_build_query($mso_shipment['fedex_lfq_ship']) : [];
//                        $fedex_lfq_sd = [];
//                        $fedex_lfq_sl = '';
//                        foreach ($package_shipments as $package_number => $response) {
//                            // Error handling
//                            if (isset($response['HighestSeverity'], $response['Notifications'], $response['Notifications']['Message']) && ($response['HighestSeverity'] == 'FAILURE' || $response['HighestSeverity'] == 'ERROR')) {
//                                echo '<span class="mso_rate_error_message"><b>Error! </b>' . $response['Notifications']['Message'] . '</span></br>';
//                            }
//
//                            $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-fedex-lfq';
//                            $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
//
//                            if (isset($response['HighestSeverity']) && $response['HighestSeverity'] != 'FAILURE' && $response['HighestSeverity'] != 'ERROR') {
//
//                                if (isset($response['CompletedShipmentDetail'], $response['CompletedShipmentDetail']['CompletedPackageDetails'], $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'])) {
//                                    $fedex_lfq_sd[] = [
//                                        'TrackingIdType' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingIdType'],
//                                        'TrackingNumber' => $response['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber']
//                                    ];
//                                }
//
//                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'])) {
//                                    $base64_string_pdf = $response['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'];
//                                    $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
//                                    $data = $base64_string_pdf;
//                                    file_put_contents($pdf, $data);
//                                }
//
//                                if (isset($response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'])) {
//                                    $base64_string_pdf = $response['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'];
//                                    $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
//                                    $data = $base64_string_pdf;
//                                    file_put_contents($pdf, $data);
//                                }
//
//                                if (file_exists($pdf)) {
//                                    $pdf_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.pdf';
//                                    $fedex_lfq_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url($pdf_icon) . '" alt="Label Missing" alt="Missing Label"/>';
//                                }
//                            }
//                        }
//
//                        echo $fedex_lfq_sl;
//                        echo '</div>';
//                        if (!empty($fedex_lfq_sd)) {
//                            // TODO
////                            echo '<span data-carrier="fedex_lfq" data-post_data="' . http_build_query($fedex_lfq_sd) . '" class="mso_cancel_shipment">Cancel Shipment</span>';
//                        }
//                        // API response
//                        isset($package_shipments) ? $mso_api_response = $package_shipments : '';
                        break;
                    case 'dhl':

                        $mso_dhl_shipment = MsoDhlShipment::mso_dhl_shipment($order_id, $shipment_num, $mso_shipment);
                        $mso_api_response = [];
                        extract($mso_dhl_shipment);

//                        echo '<div class="mso_ship_label_content">';
//                        $dhl_ship = isset($mso_shipment['dhl_ship']) ? $this->mso_parsing_build_query($mso_shipment['dhl_ship']) : [];
//
//                        if (isset($dhl_ship['detail'])) {
//                            $detail = $dhl_ship['detail'];
//                            $additionalDetails = '';
//                            if (isset($dhl_ship['additionalDetails']) && !empty($dhl_ship['additionalDetails'])) {
//                                foreach ($dhl_ship['additionalDetails'] as $key => $message) {
//                                    $additionalDetails .= strlen($additionalDetails) > 0 ? ", " . $message : $message;
//                                }
//                            }
//
//                            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $detail . "[Additional Details: $additionalDetails ]" . '</span></br>';
//                        }
//
//                        $dhl_sl = '';
//                        if (isset($dhl_ship['packages'])) {
//                            foreach ($dhl_ship['packages'] as $package_number => $package) {
//
//                                $referenceNumber = $trackingNumber = $trackingUrl = '';
//                                extract($package);
//
//                                $base64_string_pdf = isset($dhl_ship['documents'], $dhl_ship['documents'][$package_number], $dhl_ship['documents'][$package_number]['content']) ? $dhl_ship['documents'][$package_number]['content'] : '';
//
//                                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-dhl';
//                                $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
//
//                                if (strlen($base64_string_pdf) > 0) {
//                                    $pdf = MSO_MAIN_DIR . '/label/' . $label_id . '.pdf';
//                                    $data = $base64_string_pdf;
//                                    file_put_contents($pdf, base64_decode($data));
//                                }
//
//                                if (file_exists($pdf)) {
//                                    $pdf_to_show = MSO_DIR_FILE . '/label/' . $label_id . '.pdf';
//                                    $dhl_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url($pdf_icon) . '" alt="Label Missing" alt="Missing Label"/>';
//                                }
//                            }
//                        }
//
//                        echo $dhl_sl;
//                        echo '</div>';
//                        // API response
//                        isset($dhl_ship) ? $mso_api_response = $dhl_ship : '';
                        break;
                }
            } else if (isset($mso_shipment['service_error']) && strlen($mso_shipment['service_error']) > 0) {
                echo '<span class="mso_rate_error_message">' . $mso_shipment['service_error'] . '</span>';
            }
            echo '</div>'; // Close label showing div

            // API response
            $carriers_name = [
                'dhl_rate' => 'DHL Package Shipping',
                'ups_rate' => 'UPS Small Package Shipping',
                'usps_rate' => 'USPS Small Package Shipping',
                'fedex_rate' => 'Fedex Small Package Shipping',
                'ups_lfq_rate' => 'UPS LTL Freight Shipping',
                'fedex_lfq_rate' => 'Fedex LTL Freight Shipping',
            ];
            echo '<div class="mso_order_api_response mso_order_tab">';
            echo '<p class="mso_calculate_shipping_heading">API Response</p>';
            // Created shipment response
            if (isset($mso_api_response) && !empty($mso_api_response)) {
                if (strlen($mso_carrier_name) > 0) {
                    $mso_carrier_name .= '_rate';
                    if (isset($carriers_name[$mso_carrier_name])) {
                        echo "<h2 class='mso_carrier_name'>$carriers_name[$mso_carrier_name] <span title='Created Shipment' class='mso_rcn'>S</span> </h2>";
                        $this->mso_show_shipment_api_data($mso_api_response);
                    }
                }

            } else {
                // Calculated rates respone
                if (isset($mso_shipment['response']) && !empty($mso_shipment['response'])) {
                    foreach ($mso_shipment['response'] as $rate_key => $rate_data) {
                        if (isset($carriers_name[$rate_key]) && !empty($rate_data)) {
                            echo "<h2 class='mso_carrier_name'>$carriers_name[$rate_key] <span title='Calculated Rate' class='mso_rcn'>R</span> </h2>";
                            $this->mso_show_shipment_api_data($rate_data);
                        }
                    }
                }
            }
            echo '</div>';

            // Shipment Packages
            $cost = (isset($mso_shipment['cost'])) ? $mso_shipment['cost'] : '';
//            $append_subscription_status = !MSO_DONT_AUTH ? $this->mso_subscription_status : '';
            echo '<div class="mso_order_package mso_order_tab"><p class="mso_calculate_shipping_heading">Packaging</p>';
//            if ($this->subscription_boolean || MSO_DONT_AUTH) {
            if (isset($mso_shipment['response'], $mso_shipment['response']['packed_items'])) {
                $shipment_packages = $this->mso_order_package($mso_shipment);
                echo $shipment_packages;
            } else if (isset($mso_shipment['packed_items']) && $cost > 0) {
                $shipment_packages = $this->mso_order_package(['response' => $mso_shipment]);
                echo $shipment_packages;
            }
//            }
            echo '</div>';


            // Shipping rates
            $label = (isset($mso_shipment['label'])) ? $mso_shipment['label'] : '';
            $currency_symbol = MSO_CURRENCY_SYMBOL;
            $rate_checkout_order = ($cost > 0) ? "$label: $currency_symbol$cost" : "Shipping rates will be show here";
            $rate_checkout_order = '<span class="mso_shipping_rates_block">' . $rate_checkout_order . '</span>';
            isset($mso_shipment['error'], $mso_shipment['message']) ? $rate_checkout_order = '<span class="mso_rate_error_message"><b>Error! </b>' . $mso_shipment['message'] . '</span>' : '';
            echo '<div class="mso_order_rates mso_order_tab"><p class="mso_calculate_shipping_heading">Rates</p>' . $rate_checkout_order . '</div>';
            echo '</div>';
            // Main shipments loop closed here
            echo '</div>';
            echo '<div style="clear: both"></div>';

//            echo '<hr class="mso_hr">';
            echo '</form>';

            $mso_ship_num++;
        }

        if (isset($mso_order_items) && !empty($mso_order_items)) {
            $mso_shipments = [];
            $mso_shipments[] = [
                'response' => [
                    'items' => $mso_order_items,
                    'ship_from' => [],
                ],
                'accessorials' => [],
            ];
            $this->mso_order_shipments_list($mso_shipments, $location_options, $order_id, $mso_order_items, $mso_ship_num, $ship_to_address_label);
        }
    }

    // Shipment API data
    public function mso_show_shipment_api_data($shipment_results, $return = false)
    {
        ob_start();
        echo '<table class="mso_api_response_table" border="1px solid">';
        foreach ($shipment_results as $key => $detail) {
            echo '<tr>';
            echo '<td>' . $key . '</td>';
            if (is_array($detail)) {
                echo '<td>';
                echo '<details>';
                echo '<summary>Expand me</summary>';
                echo '<pre>';
                print_r($detail);
                echo '</pre>';
                echo '</details>';
                echo '</td>';
            } else {
                echo '<td>' . mb_strimwidth($detail, 0, 35, "...") . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';

        if ($return) {
            return ob_get_clean();
        }
    }

    // Packages
    public function mso_order_package($mso_shipment)
    {
        $shipment_packages = '';
//        $all_packaging = isset($mso_shipment['response'], $mso_shipment['individualPackage'], $mso_shipment['response']['usps_rate']) && !empty($mso_shipment['individualPackage']) && !empty($mso_shipment['response']['usps_rate']) ? $mso_shipment['response']['usps_rate'] : $mso_shipment['response']['packed_items'];
        $default_package_flag = false;
        $all_packaging = $usps_packaging = [];
        if (isset($mso_shipment['response'])) {
            $default_packaging = isset($mso_shipment['response']['packed_items']) ? $mso_shipment['response']['packed_items'] : [];
            $usps_packaging = isset($mso_shipment['response']['usps_rate']) && !empty($mso_shipment['response']['usps_rate']) ? $mso_shipment['response']['usps_rate'] : [];
            $all_packaging = array_merge($default_packaging, $usps_packaging);
        }

        foreach ($all_packaging as $package_no => $package_detail) {

            // USPS conditions
            $append_usps = '';
            if (isset($package_detail['service'], $package_detail['container']) && strlen($package_detail['container']) > 0) {
                $service = $package_detail['service'];
                $container = $package_detail['container'];

                if ($default_package_flag) {
                    $default_package_flag = false;
                    $shipment_packages .= '<hr class="mso_hr">';
                }

                $shipment_packages .= '<h3>USPS Service: ' . $service . '</h3>';

                $append_usps .= '<tr><td>Service</td><td>' . $service . '</td>';
                $append_usps .= '<tr><td>Container</td><td>' . $container . '</td>';
            } else {
                if (!empty($usps_packaging) && !$default_package_flag) {
                    $default_package_flag = true;
                    $shipment_packages .= '<h3>Default Packaging</h3>';
                }
            }

            $package_detail = isset($package_detail['individualPackage']) && !empty($package_detail['individualPackage']) ? $package_detail['individualPackage'] : $package_detail;
            if (!is_array($package_detail) || empty($package_detail)) {
                continue;
            }

            $type = $title = $width = $length = $height = $weight = $quantity = $package = '';
            extract($package_detail);
            $front_name = $package . 'Box';
            $front_width = '100px';
            $bin_to_show = MSO_DIR_FILE . '/images/bin.png';
            if ($type == 'pallet') {
                $front_width = '100x';
                $front_name = $package . 'Pallet';
                $bin_to_show = MSO_DIR_FILE . '/images/pallet.png';
            }

            $dimensions = "$length <b>X</b> $width <b>X</b> $height";
//            $package_detail = "$front_name Name: $title Quantity: $quantity Dimensions: $dimensions Weight: $weight";
//            $package_detail = "<table style='border: 1px solid'><tr><th>$front_name Name</th><th>Dimensions</th><th>Quantity</th><th>Weight</th></tr><tr><td>$title</td><td>$dimensions</td><td>$quantity</td><td>$weight</td></tr></table>";

            ob_start();
            ?>
            <table border="1px solid" class="mso_order_packages_tip">
                <?php echo $append_usps; ?>
                <tr>
                    <td><?php echo $front_name; ?></td>
                    <td><?php echo $title; ?></td>
                </tr>
                <tr>
                    <td>Dimensions <br> L X W X H (In)</td>
                    <td><?php echo $dimensions; ?></td>
                </tr>
                <tr>
                    <td>Quantity</td>
                    <td><?php echo $quantity; ?></td>
                </tr>
                <tr>
                    <td>Weight (LBS)</td>
                    <td><?php echo $weight; ?></td>
                </tr>
            </table>
            <?php
            $package_detail = ob_get_clean();
//            $shipment_packages .= '<img src="' . esc_url($bin_to_show) . '" height="70px" width="' . $front_width . '">' . "<span class='mso_order_package_content'>$dimensions</span><span class='woocommerce-help-tip' data-tip='.$package_detail.'></span>";
            $shipment_packages .= '<img src="' . esc_url($bin_to_show) . '" height="70px" width="' . $front_width . '">' . "<span class='mso_order_package_content'>$dimensions</span>";
            // Custom tooltip
            // https://codepen.io/rudeayelo/pen/DWNyxg
            $shipment_packages .= '<span class="mso_wrapper"><span class="mso_tooltip">' . $package_detail . '</span></span>';
        }

        return $shipment_packages;
    }
}