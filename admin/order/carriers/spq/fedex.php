<?php

namespace MsoFedexShipment;

class MsoFedexShipment
{
    /**
     * Display FedEx shipment error message if present.
     *
     * @param array $fedex_shipment FedEx shipment details.
     *
     * @return void
     */
    static public function mso_fedex_shipment_error_message($fedex_shipment)
    {
        // Check if FedEx shipment has severity, notifications, and a message indicating an error
        if (isset($fedex_shipment['HighestSeverity'], $fedex_shipment['Notifications'], $fedex_shipment['Notifications']['Message']) &&
            ($fedex_shipment['HighestSeverity'] == 'FAILURE' || $fedex_shipment['HighestSeverity'] == 'ERROR')) {

            // Display FedEx error message
            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $fedex_shipment['Notifications']['Message'] . '</span></br>';
        }
    }

    /**
     * Display FedEx shipment labels and handle errors.
     *
     * @param int $order_id Order ID.
     * @param int $shipment_num Shipment number.
     * @param array $mso_shipment FedEx shipment details.
     *
     * @return array API response and FedEx labels.
     */
    static public function mso_fedex_shipment($order_id, $shipment_num, $mso_shipment)
    {
        $fedex_shipments = isset($mso_shipment['fedex_ship']) ? mso_parsing_build_query($mso_shipment['fedex_ship']) : [];
        $fedex_labels = $fedex_sd = [];
        $fedex_sl = '';

        // Process FedEx shipments
        foreach ($fedex_shipments as $package_number => $fedex_shipment) {

            // Handle FedEx shipment errors
            self::mso_fedex_shipment_error_message($fedex_shipment);

            // Generate label ID
            $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-fedex-spq-' . 'package-' . $package_number;

            // Get current date in the format: year-month-date
            $current_date = date('Y-m-d');

            // Create subfolder path based on the current date
            $subfolder_path = MSO_MAIN_DIR . '/label/' . $current_date . '/';

            // Check if the subfolder exists, if not, create it
            if (!file_exists($subfolder_path)) {
                mkdir($subfolder_path, 0755, true); // 0755 is the default permission
            }

            // Set the label path with the subfolder
            $png = $subfolder_path . $label_id . '.png';

            // Check if FedEx shipment has no errors
            if (isset($fedex_shipment['HighestSeverity']) && $fedex_shipment['HighestSeverity'] != 'FAILURE' && $fedex_shipment['HighestSeverity'] != 'ERROR') {

                // Collect tracking details if available
                if (isset($fedex_shipment['CompletedShipmentDetail'], $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails'], $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'])) {
                    $fedex_sd[] = [
                        'TrackingIdType' => $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingIdType'],
                        'TrackingNumber' => $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber']
                    ];
                }

                // Check if CodReturnDetail key exists before accessing it
                if (isset($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'])) {
                    // Process CodReturnDetail label
                    self::process_fedex_label($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'], $png);
                }

                // Check if Label key exists before accessing it
                if (isset($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'])) {
                    // Process Label label
                    self::process_fedex_label($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'], $png);
                }


                // Display FedEx label
                if (file_exists($png)) {
                    $fedex_labels[] = $png_to_show = MSO_DIR_FILE . '/label/' . $current_date . '/' . $label_id . '.png';
                    $fedex_sl .= '<img src="' . esc_url($png_to_show) . '" class="mso_real_label_image" onclick="mso_file_to_click(this,1)" alt="Label Missing" alt="Missing Label"/>';
                }
            }
        }

        // Display the FedEx labels
        echo '<div class="mso_ship_label_content">' . $fedex_sl . '</div>';

        // Display 'Print All Shipment' button if there are tracking details
        if (!empty($fedex_sd)) {
            echo '<span data-carrier="fedex" data-post_data="' . implode(',', $fedex_labels) . '" class="form-control button-primary mso_print_all_shipment">Print All Shipment</span>';
        }

        // Return an array containing the API response
        return [
            'mso_api_response' => $fedex_shipments
        ];
    }

    /**
     * Process and save FedEx label to specified file.
     *
     * @param string $base64_string_png Base64-encoded FedEx label image.
     * @param string $png File path for the label.
     *
     * @return void
     */
    private static function process_fedex_label($base64_string_png, $png)
    {
        if (strlen($base64_string_png) > 0) {
            file_exists($png) ? unlink($png) : '';
            file_put_contents($png, base64_decode($base64_string_png));
        }
    }
}