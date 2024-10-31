<?php

namespace MsoUpsLfqShipment;

class MsoUpsLfqShipment
{
    /**
     * Display UPS LFQ shipment error message.
     *
     * @param array $ups_lfq_ship The UPS LFQ shipment details.
     */
    static public function mso_ups_lfq_shipment_error_message($ups_lfq_ship)
    {
        // Check if required keys are set in the UPS LFQ shipment details
        if (
            isset(
                $ups_lfq_ship['detail'],
                $ups_lfq_ship['detail']['Errors'],
                $ups_lfq_ship['detail']['Errors']['ErrorDetail'],
                $ups_lfq_ship['detail']['Errors']['ErrorDetail']['PrimaryErrorCode'],
                $ups_lfq_ship['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description']
            )
        ) {
            // Display an error message
            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $ups_lfq_ship['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'] . '</span></br>';
        }
    }

    /**
     * Process and display UPS LFQ shipments and store labels in subfolders.
     *
     * @param int $order_id The WooCommerce order ID.
     * @param int $shipment_num The shipment number.
     * @param array $mso_shipment The MSO shipment details.
     */
    static public function mso_ups_lfq_shipment($order_id, $shipment_num, $mso_shipment)
    {
        // Retrieve UPS LFQ shipment details
        $ups_lfq_ship = isset($mso_shipment['ups_lfq_ship']) ? mso_parsing_build_query($mso_shipment['ups_lfq_ship']) : [];

        // Display error messages, if any
        self::mso_ups_lfq_shipment_error_message($ups_lfq_ship);

        $ups_lfq_sd = [];
        $ups_lfq_sl = '';

        // Check if UPS LFQ shipment results are set
        if (isset($ups_lfq_ship['ShipmentResults'], $ups_lfq_ship['ShipmentResults'])) {
            $package_results = $ups_lfq_ship['ShipmentResults'];
            (isset($package_results['Documents'])) ? $package_results = [$package_results] : '';

            // Loop through each package result
            foreach ($package_results as $label_key => $package) {
                // Check if required keys are set in the package
                if (isset($package['ShipmentNumber'], $package['BOLID'])) {
                    $ups_lfq_sd[] = [
                        'ShipmentNumber' => $package['ShipmentNumber'],
                        'BOLID' => $package['BOLID']
                    ];
                }

                // Generate label ID based on order ID, shipment number, and label key
                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-ups-lfq-' . $label_key;

                // Get the current date in the format: year-month-day
                $current_date = date('Y-m-d');

                // Create a subfolder path based on the current date
                $subfolder_path = MSO_MAIN_DIR . '/label/' . $current_date . '/';

                // Check if the subfolder exists, if not, create it
                if (!file_exists($subfolder_path)) {
                    mkdir($subfolder_path, 0755, true); // 0755 is the default permission
                }

                // Define the PDF file path
                $pdf = $subfolder_path . $label_id . '.pdf';

                // Check if base64 PDF content is set
                $base64_string_pdf = isset($package['Documents'], $package['Documents']['Image'], $package['Documents']['Image']['GraphicImage']) ? $package['Documents']['Image']['GraphicImage'] : '';

                // If base64 PDF content is present, save it to the PDF file
                if (strlen($base64_string_pdf) > 0) {
                    $ifp = fopen($pdf, 'wb');
                    fwrite($ifp, base64_decode($base64_string_pdf));
                    fclose($ifp);
                }

                // Check if the PDF file exists, and if so, display an image with a link to view the PDF
                if (file_exists($pdf)) {
                    $pdf_to_show = MSO_DIR_FILE . '/label/' . $current_date . '/' . $label_id . '.pdf';
                    $ups_lfq_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url(MSO_PDF_ICON) . '" alt="Label Missing" alt="Missing Label"/>';
                }
            }
        }

        // Display the UPS labels
        echo '<div class="mso_ship_label_content">' . $ups_lfq_sl . '</div>';

        // Return an array containing the API response
        return [
            'mso_api_response' => $ups_lfq_ship
        ];
    }

}