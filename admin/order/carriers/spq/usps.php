<?php

namespace MsoUspsShipment;

class MsoUspsShipment
{
    /**
     * USPS shipment error handling function.
     *
     * @param array $usps_ship The USPS shipment data.
     */
    static public function mso_usps_shipment_error_message($usps_ship)
    {
        if (isset($usps_ship['error'], $usps_ship['message'])) {
            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $usps_ship['message'] . '</span></br>';
        }
    }

    /**
     * Process USPS shipments and display labels.
     *
     * @param int $order_id The ID of the WooCommerce order.
     * @param int $shipment_num The shipment number.
     * @param array $mso_shipment The USPS shipment data.
     *
     * @return array  The USPS API response and labels array.
     */
    static public function mso_usps_shipment($order_id, $shipment_num, $mso_shipment)
    {
        // Parse USPS shipment data
        $usps_ship = isset($mso_shipment['usps_ship']) ? mso_parsing_build_query($mso_shipment['usps_ship']) : [];

        // Check for USPS shipment errors
        self::mso_usps_shipment_error_message($usps_ship);

        // Process USPS labels
        $usps_labels = [];
        $usps_sl = '';
        foreach ($usps_ship as $label_key => $label_data) {
            // Construct label ID
            $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-usps-' . $label_key;

            $date_subfolder = date('Y-m-d');
            $pdf_folder = MSO_MAIN_DIR . '/label/' . $date_subfolder;
            if (!file_exists($pdf_folder)) {
                mkdir($pdf_folder, 0777, true);
            }
            $pdf = $pdf_folder . '/' . $label_id . '.pdf';

            // Extract base64 PDF from label data
            $base64_string_pdf = isset($label_data['Label']) ? $label_data['Label'] : '';

            // Save the PDF file
            if (strlen($base64_string_pdf) > 0) {
                file_put_contents($pdf, base64_decode($base64_string_pdf));
            }

            // If the PDF file exists, add it to the labels array and generate HTML for display
            if (file_exists($pdf)) {
                $pdf_to_show = MSO_DIR_FILE . '/label/' . $date_subfolder . '/' . $label_id . '.pdf';
                $usps_labels[] = $pdf_to_show;
                $usps_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url(MSO_PDF_ICON) . '" alt="Label Missing" alt="Missing Label"/>';
            }
        }

        // Display the USPS labels
        echo '<div class="mso_ship_label_content">' . $usps_sl . '</div>';

        // Return an array containing the API response
        return [
            'mso_api_response' => $usps_ship
        ];
    }
}