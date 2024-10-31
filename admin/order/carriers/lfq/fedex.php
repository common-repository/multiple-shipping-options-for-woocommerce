<?php

namespace MsoFedexLfqShipment;

class MsoFedexLfqShipment
{
    /**
     * Display error messages for FedEx LFQ shipments.
     *
     * @param array $fedex_shipment The FedEx LFQ shipment details.
     */
    static public function mso_fedex_lfq_shipment_error_message($fedex_shipment)
    {
        // Check if required keys are set in the FedEx LFQ shipment details
        if (
            isset($fedex_shipment['HighestSeverity'], $fedex_shipment['Notifications'], $fedex_shipment['Notifications']['Message'])
            && ($fedex_shipment['HighestSeverity'] == 'FAILURE' || $fedex_shipment['HighestSeverity'] == 'ERROR')
        ) {
            // Display error message
            echo '<span class="mso_rate_error_message"><b>Error! </b>' . $fedex_shipment['Notifications']['Message'] . '</span></br>';
        }
    }


    /**
     * Display FedEx Freight Labels and handle errors.
     *
     * @param int $order_id Order ID.
     * @param int $shipment_num Shipment number.
     * @param array $mso_shipment MSO shipment data.
     */
    static public function mso_fedex_lfq_shipment($order_id, $shipment_num, $mso_shipment)
    {
        $fedex_shipments = isset($mso_shipment['fedex_lfq_ship']) ? mso_parsing_build_query($mso_shipment['fedex_lfq_ship']) : [];
        $fedex_lfq_sd = [];
        $fedex_lfq_sl = '';

        foreach ($fedex_shipments as $package_number => $fedex_shipment) {

            // Display error messages, if any
            self::mso_fedex_lfq_shipment_error_message($fedex_shipment);

            // Label ID based on order and shipment details
            $label_id = 'order-' . $order_id . '-shipment-' . $shipment_num . '-fedex-lfq';

            // Get label folder path based on the current date
            $date_folder_path = MSO_MAIN_DIR . '/label/' . date('Y-m-d') . '/';

            // Check if the folder exists, if not, create it
            if (!file_exists($date_folder_path)) {
                mkdir($date_folder_path, 0755, true);
            }

            // PDF file path
            $pdf = $date_folder_path . $label_id . '.pdf';

            if (isset($fedex_shipment['HighestSeverity']) && $fedex_shipment['HighestSeverity'] != 'FAILURE' && $fedex_shipment['HighestSeverity'] != 'ERROR') {

                // Track and store shipment details
                if (isset($fedex_shipment['CompletedShipmentDetail'], $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails'], $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds'])) {
                    $fedex_lfq_sd[] = [
                        'TrackingIdType' => $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingIdType'],
                        'TrackingNumber' => $fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['TrackingIds']['TrackingNumber']
                    ];
                }

                // Process CodReturnDetail label
                if (isset($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'])) {
                    self::process_fedex_label($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['CodReturnDetail']['Label']['Parts']['Image'], $pdf);
                }

                // Process Label label
                if (isset($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'])) {
                    self::process_fedex_label($fedex_shipment['CompletedShipmentDetail']['CompletedPackageDetails']['Label']['Parts']['Image'], $pdf);
                }

                if (file_exists($pdf)) {
                    // Display label image
                    $pdf_to_show = MSO_DIR_FILE . '/label/' . date('Y-m-d') . '/' . $label_id . '.pdf';
                    $fedex_lfq_sl .= '<img mso_pdf_src="' . esc_url($pdf_to_show) . '" class="mso_real_pdf_image" onclick="mso_file_to_click(this,2)" src="' . esc_url(MSO_PDF_ICON) . '" alt="Label Missing" alt="Missing Label"/>';
                }
            }
        }

        // Display the Fedex labels
        echo '<div class="mso_ship_label_content">' . $fedex_lfq_sl . '</div>';

        // Return an array containing the API response
        return [
            'mso_api_response' => $fedex_shipments
        ];
    }

    /**
     * Process and save a FedEx label from a base64-encoded image string to a file.
     *
     * @param string $base64_image The base64-encoded image string.
     * @param string $file_path The file path to save the image.
     */
    static public function process_fedex_label($base64_image, $file_path)
    {
        // Check if the base64 image string is not empty
        if (strlen($base64_image) > 0) {
            // Decode base64 image string
            $image_data = base64_decode($base64_image);

            // Save the image to the specified file path
            file_put_contents($file_path, $image_data);
        }
    }
}