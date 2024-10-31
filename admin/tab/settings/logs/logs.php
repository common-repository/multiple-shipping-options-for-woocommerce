<?php

namespace MsoLogs;

use WasaioCurl\WasaioCurl;

class MsoLogs
{
    static public function mso_settings()
    {
        $post_data = [
            'mso_type' => 'logs',
            'domain' => MSO_SERVER_NAME,
            'mso_key' => MSO_SERVER_KEY
        ];

        $url = MSO_HITTING_URL . 'index.php';
        $logs_template_data = WasaioCurl::wasaio_http_request($url, $post_data);
        // Decode the JSON string
        $json_data = json_decode($logs_template_data, true);

        // Check if decoding was successful and if "error" index exists
        if (json_last_error() === JSON_ERROR_NONE && isset($json_data['error'], $json_data['message'])) {
            $logs_template_data = '<tr style="background-color: red; color: white;"><td colspan="6">' . $json_data['message'] . '</td></tr>';
        }

        $logs_template = '<table border="1px solid" class="form-table mso_logs_table">';
        $logs_template .= '<tbody>';

//        $logs_template .= '<tr>';
//        $logs_template .= '<th rowspan="2">The calculated shipping was displayed on the cart/checkout page.</th>';
//        $logs_template .= '<th colspan="5">WooCommerce Items</th>';
//        $logs_template .= '<th colspan="5">Pallets & Boxes</th>';
//        $logs_template .= '<th rowspan="2">Sender Address</th>';
//        $logs_template .= '<th rowspan="2">Receiver Address</th>';
//        $logs_template .= '<th rowspan="2">API Response</th>';
//        $logs_template .= '</tr>';
//        $logs_template .= '<tr>';
//        $logs_template .= '<th colspan="2">Items</th>';
//        $logs_template .= '<th colspan="2">Dimensions (L x W x H)</th>';
//        $logs_template .= '<th>Qty</th>';
//        $logs_template .= '<th colspan="2">Packing Information</th>';
//        $logs_template .= '<th colspan="2">Dimensions (L x W x H)</th>';
//        $logs_template .= '<th>Qty</th>';
//        $logs_template .= '</tr>';

        $logs_template .= '<tr>';
        $logs_template .= '<th>The calculated shipping was displayed on the cart/checkout page.</th>';
        $logs_template .= '<th>WooCommerce Items</th>';
        $logs_template .= '<th>Pallets & Boxes</th>';
        $logs_template .= '<th>Sender Address</th>';
        $logs_template .= '<th>Receiver Address</th>';
        $logs_template .= '<th>API Response</th>';
        $logs_template .= '</tr>';

        // Adding html template
        $logs_template .= $logs_template_data;

        $logs_template .= '</tbody>';
        $logs_template .= '</table>';

        $logs_template .= '<div class="mso_logs_overly" style="display: none">';
        $logs_template .= '<div class="mso_popup_overly_template">';
        $logs_template .= '<a onclick="mso_logs_overly_hide()" class="close">×</a>';
        $logs_template .= '<div id="mso_api_json_response">';
        $logs_template .= '</div>';
        $logs_template .= '</div>';
        $logs_template .= '</div>';

        echo $logs_template;
    }
}