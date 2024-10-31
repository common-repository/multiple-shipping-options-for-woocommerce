<?php

namespace MsoUpsShipment;

class MsoUpsShipment
{
    static public function mso_ups_shipment_error_message($ups_ship)
    {
        if (isset($ups_ship['confirm'], $ups_ship['confirm']['Response'], $ups_ship['confirm']['Response']['Error'], $ups_ship['confirm']['Response']['Error']['ErrorDescription'], $ups_ship['confirm']['Response']['Error']['ErrorSeverity']) && (strtolower($ups_ship['confirm']['Response']['Error']['ErrorSeverity']) != 'warning')) {
            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $ups_ship['confirm']['Response']['Error']['ErrorDescription'] . '</span></br>';
        } else if (isset($ups_ship['accept'], $ups_ship['accept']['Response'], $ups_ship['accept']['Response']['Error'], $ups_ship['accept']['Response']['Error']['ErrorDescription'])) {
            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $ups_ship['accept']['Response']['Error']['ErrorDescription'] . '</span></br>';
        }
    }

    /**
     * Display UPS shipment information and labels.
     *
     * @param int $order_id The WooCommerce order ID.
     * @param int $shipment_num The shipment number.
     * @param array $mso_shipment The UPS shipment details.
     *
     * @return array An array containing the display paths of UPS labels.
     */
    static public function mso_ups_shipment($order_id, $shipment_num, $mso_shipment)
    {
        $ups_ship = isset($mso_shipment['ups_ship']) ? mso_parsing_build_query($mso_shipment['ups_ship']) : [];
        $ups_sd = $ups_labels = [];

        // Error handling
        self::mso_ups_shipment_error_message($ups_ship);

        $ups_sl = '';

        if (isset($ups_ship['accept'], $ups_ship['accept']['ShipmentResults'], $ups_ship['accept']['ShipmentResults']['PackageResults'])) {
            $package_results = $ups_ship['accept']['ShipmentResults']['PackageResults'];
            (isset($package_results['LabelImage'])) ? $package_results = [$package_results] : '';

            foreach ($package_results as $package => $label_detail) {
                if (isset($label_detail['TrackingNumber'])) {
                    $ups_sd[] = [
                        'TrackingNumber' => $label_detail['TrackingNumber']
                    ];
                }

                // Generate a unique label ID based on order ID, shipment number, and package
                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-ups-spq-' . $package;

                // Get the current date in the format: year-month-date
                $current_date = date('Y-m-d');

                // Create a subfolder path based on the current date
                $subfolder_path = MSO_MAIN_DIR . '/label/' . $current_date . '/';

                // Check if the subfolder exists, if not, create it
                if (!file_exists($subfolder_path)) {
                    mkdir($subfolder_path, 0755, true); // 0755 is the default permission
                }

                // Define the PNG file path within the subfolder
                $png = $subfolder_path . $label_id . '.png';

                // Get the base64 string for the PNG label image
                $base64_string_png = isset($label_detail['LabelImage'], $label_detail['LabelImage']['GraphicImage']) ? $label_detail['LabelImage']['GraphicImage'] : '';

                // Save the PNG label image if the base64 string is not empty
                if (strlen($base64_string_png) > 0) {
                    file_exists($png) ? unlink($png) : ''; // Remove existing file if it exists
                    file_put_contents($png, base64_decode($base64_string_png));
                }

                // Check if the PNG file exists and generate its display path
                if (file_exists($png)) {
                    $ups_labels[] = $png_to_show = MSO_DIR_FILE . '/label/' . $current_date . '/' . $label_id . '.png';
                    $ups_sl .= '<img class="mso_real_label_image" onclick="mso_file_to_click(this,1)" src="' . esc_url($png_to_show) . '" alt="Label Missing" alt="Missing Label"/>';
                }
            }
        }

        // Display the UPS labels
        echo '<div class="mso_ship_label_content">' . $ups_sl . '</div>';

        // Display the "Print All Shipment" button if UPS labels exist
        if (!empty($ups_sd)) {
            echo '<span data-carrier="ups" data-post_data="' . implode(',', $ups_labels) . '" class="form-control button-primary mso_print_all_shipment">Print All Shipment</span>';
        }

        // Initialize an array to store API responses
        $mso_api_response = [];

        // Check and assign UPS confirm response to the array if it exists
        if (isset($ups_ship['confirm'])) {
            $mso_api_response['confirm'] = $ups_ship['confirm'];
        }

        // Check and assign UPS accept response to the array if it exists
        if (isset($ups_ship['accept'])) {
            $mso_api_response['accept'] = $ups_ship['accept'];
        }

        // Return an array containing the API response
        return [
            'mso_api_response' => $mso_api_response
        ];
    }
}