<?php

namespace MsoDhlShipment;

class MsoDhlShipment
{

    /**
     * Display DHL shipment error message.
     *
     * @param array $dhl_ship The DHL shipment data.
     */
    static public function mso_dhl_shipment_error_message($dhl_ship)
    {
        // Check if 'detail' key is set in the DHL shipment data
        if (isset($dhl_ship['detail'])) {
            // Get the error detail
            $detail = $dhl_ship['detail'];
            $additionalDetails = '';

            // Check if 'additionalDetails' is not empty
            if (!empty($dhl_ship['additionalDetails'])) {
                // Concatenate additional details with commas
                $additionalDetails = implode(', ', $dhl_ship['additionalDetails']);
            }

            // Add additional details to the error message if present
            $detail .= !empty($additionalDetails) ? " [Additional Details: $additionalDetails]" : '';

            // Display the error message
            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $detail . '</span><br>';
        }
    }

    /**
     * Display DHL shipment information and labels.
     *
     * @param int $order_id The WooCommerce order ID.
     * @param int $shipment_num The shipment number.
     * @param array $mso_shipment The MSO shipment data.
     *
     * @return array An array containing the API response.
     */
    static public function mso_dhl_shipment($order_id, $shipment_num, $mso_shipment)
    {
        // Parse DHL shipment data from MSO API response
        $dhl_ship = isset($mso_shipment['dhl_ship']) ? mso_parsing_build_query($mso_shipment['dhl_ship']) : [];

        self::mso_dhl_shipment_error_message($dhl_ship);

        $dhl_sl = '';
        // Check if 'packages' key is set in DHL shipment data
        if (isset($dhl_ship['packages'])) {
            foreach ($dhl_ship['packages'] as $package_number => $package) {
                // Get the base64 string for the PDF label
                $base64_string_pdf = isset($dhl_ship['documents'], $dhl_ship['documents'][$package_number], $dhl_ship['documents'][$package_number]['content']) ? $dhl_ship['documents'][$package_number]['content'] : '';
                if (!strlen($base64_string_pdf) > 0) {
                    continue;
                }

                // Generate a unique label ID based on order ID and shipment number
                $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-dhl';

                // Get the current date in the format: year-month-date
                $current_date = date('Y-m-d');

                // Create a subfolder path based on the current date
                $subfolder_path = MSO_MAIN_DIR . '/label/' . $current_date . '/';

                // Check if the subfolder exists, if not, create it
                if (!file_exists($subfolder_path)) {
                    mkdir($subfolder_path, 0755, true); // 0755 is the default permission
                }

                // Define the PDF file path within the subfolder
                $pdf = $subfolder_path . $label_id . '.pdf';

                // Save the PDF file if the base64 string is not empty
                if (strlen($base64_string_pdf) > 0) {
                    $data = $base64_string_pdf;
                    file_put_contents($pdf, base64_decode($data));
                }

                // Check if the PDF file exists and generate its display path
                if (file_exists($pdf)) {
                    $pdf_to_show = MSO_DIR_FILE . '/label/' . $current_date . '/' . $label_id . '.pdf';
                    $dhl_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url(MSO_PDF_ICON) . '" alt="Label Missing" alt="Missing Label"/>';
                }
            }
        }

        // Display the DHL labels
        echo '<div class="mso_ship_label_content">' . $dhl_sl . '</div>';

        // Return an array containing the API response
        return [
            'mso_api_response' => $dhl_ship
        ];
    }
}