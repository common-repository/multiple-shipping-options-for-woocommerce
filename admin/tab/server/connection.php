<?php

namespace MsoConnection;

use WasaioCurl\WasaioCurl;

class MsoConnection
{
    public function __construct()
    {
        add_action('wp_ajax_mso_test_connection', array($this, 'mso_test_connection'));
    }

    /**
     * Retrieve API response from MSO server.
     *
     * @param array $mso_packages The packages data to be sent to the API.
     * @return array The API response.
     */
    static public function mso_api_response($mso_packages)
    {
        $url = MSO_HITTING_URL . 'index.php';
        $mso_response = json_decode(WasaioCurl::wasaio_http_request($url, $mso_packages), true);

        if (isset($mso_response['shipments']) && !empty($mso_response['shipments'])) {
            $mso_response = reset($mso_response['shipments']);
        }

        return $mso_response;
    }

    /**
     * Generate post fields from provided data using mapping.
     *
     * @param array $mso_post_data The input data array.
     * @param array $fields The mapping array for field names.
     * @return array The generated post fields.
     */
    static public function mso_post_data($mso_post_data, $fields)
    {
        $post_fields = [];

        foreach ($mso_post_data as $key => $field) {
            $name = $value = '';
            extract($field);

            if (isset($fields[$name])) {
                $post_fields[$fields[$name]] = trim($value);
            }
        }

        return $post_fields;
    }


    // API testing request
    public function mso_test_connection()
    {
        $mso_api_test_mode = isset($_POST['mso_api_test_mode']) ? sanitize_text_field($_POST['mso_api_test_mode']) : '';
        $mso_carrier_id = isset($_POST['mso_carrier_id']) ? sanitize_text_field($_POST['mso_carrier_id']) : '';
        $mso_post_data = isset($_POST['mso_post_data']) ? $_POST['mso_post_data'] : [];
        $results = [];

        $mso_packages = [
            'api_test_mode' => $mso_api_test_mode,
            'domain' => MSO_SERVER_NAME,
            'mso_key' => MSO_SERVER_KEY,
            'currency' => MSO_CURRENCY_CODE,
            'timezone' => MSO_TIME_ZONE,
            'mso_type' => 'rate_connection_page',
            'static_request' => 'yes'
        ];

        switch ($mso_carrier_id) {
            case 'mso_paid_key':
                $fields = [
                    'mso_paid_key' => 'mso_key'
                ];

                $post_fields = self::mso_post_data($mso_post_data, $fields);
                $post_fields['domain'] = MSO_SERVER_NAME;
                $post_fields['mso_type'] = 'key';

                $url = MSO_HITTING_URL . 'index.php';

                $mso_api_results = json_decode(WasaioCurl::wasaio_http_request($url, $post_fields), true);
//                echo '<pre>';
//                print_r($post_fields);
//                print_r($mso_api_results);
//                echo '</pre>'; die;

                if (isset($mso_api_results['severity'], $mso_api_results['message'])) {
                    $severity = $mso_api_results['severity'];
                    $style_color = $show_status = '';
                    $subscriptions = [];

                    switch ($severity) {
                        case 'error':
                            $action = false;
                            $style_color = 'red';
                            $show_status = 'Error';
                            break;
                        case 'success':
                            $action = true;
                            $style_color = 'green';
                            $show_status = 'Success';
                            $subscriptions = $mso_api_results['subscriptions'];
                            $mso_key = isset($post_fields['mso_key']) ? $post_fields['mso_key'] : '';
                            update_option('mso_paid_key', $mso_key);
                            break;
                    }

                    $message = '<span style="color: ' . $style_color . ';"><b>' . $show_status . '! </b> ' . $mso_api_results['message'] . '</span>';
                    update_option('mso_key_status', $message);
                    update_option('mso_key_direction', $severity);
                    update_option('mso_key_subscriptions', json_encode($subscriptions));

                    $results = [
                        'action' => $action,
                        'message' => $message
                    ];
                }
                break;
            case 'mso_ups_sqp':
                $fields = [
                    'mso_ups_spq_account_number' => 'shipper_number',
                    'mso_ups_spq_username' => 'user_id',
                    'mso_ups_spq_password' => 'password',
                    'mso_ups_spq_access_key' => 'access'
                ];

                $post_fields = self::mso_post_data($mso_post_data, $fields);
                $mso_packages['carriers']['ups_spq'] = $post_fields;
                $mso_response = self::mso_api_response($mso_packages);

                if (isset($mso_response['ups_rate']['Response']['ResponseStatusCode']) && $mso_response['ups_rate']['Response']['ResponseStatusCode'] == '1') {
                    $message = '<span style="color: green;"><b>Success! </b> Your request to connect to the UPS API has been processed successfully.</span>';
                    $action = true;
                } elseif (isset($mso_response['ups_rate']['Response']['Error']['ErrorDescription'])) {
                    $errorDescription = $mso_response['ups_rate']['Response']['Error']['ErrorDescription'];
                    $message = '<span style="color: red;"><b>Error! </b>' . $errorDescription . '</span>';
                    $action = false;
                } elseif (isset($mso_response['error'], $mso_response['message'])) {
                    $errorMessage = $mso_response['message'];
                    $message = '<span style="color: red;"><b>Error! </b> ' . $errorMessage . '</span>';
                    $action = false;
                } else {
                    $message = '<span style="color: red;"><b>Error! </b> Please try again later.</span>';
                    $action = false;
                }

                update_option('mso_ups_spq_credentials_status', $message);

                $results = [
                    'action' => $action,
                    'message' => $message
                ];
                break;
            case 'mso_usps_sqp':
                $fields = [
                    'mso_usps_spq_user_id' => 'user_id',
                    'mso_usps_spq_password' => 'password'
                ];

                $post_fields = self::mso_post_data($mso_post_data, $fields);
                $mso_packages['carriers']['usps_spq'] = $post_fields;
                $mso_response = self::mso_api_response($mso_packages);

                if (isset($mso_response['usps_rate'], $mso_response['usps_rate']['0'], $mso_response['usps_rate']['0']['classId'])) {
                    $message = '<span style="color: green;"><b>Success! </b> Your request to connect to the UPS API has been processed successfully.</span>';
                    $action = true;
                } elseif (isset($mso_response['usps_rate'], $mso_response['usps_rate']['error'], $mso_response['usps_rate']['message'])) {
                    $message = '<span style="color: red;"><b>Error! </b> ' . $mso_response['usps_rate']['message'] . '</span>';
                    $action = false;
                } elseif (isset($mso_response['error'], $mso_response['message'])) {
                    $message = '<span style="color: red;"><b>Error! </b> ' . $mso_response['message'] . '</span>';
                    $action = false;
                } else {
                    $message = '<span style="color: red;"><b>Error! </b> Please try again later.</span>';
                    $action = false;
                }

                update_option('mso_usps_spq_credentials_status', $message);
                $results = [
                    'action' => $action,
                    'message' => $message
                ];
                break;
            case 'mso_dhl_sqp':
                $fields = [
                    'mso_dhl_spq_username' => 'username',
                    'mso_dhl_spq_password' => 'password',
                    'mso_dhl_spq_account_number' => 'account_number'
                ];

                $post_fields = self::mso_post_data($mso_post_data, $fields);
                $mso_packages['carriers']['dhl_spq'] = $post_fields;
                $mso_response = self::mso_api_response($mso_packages);
//                echo '<pre>';
//                print_r($mso_response);
//                echo '</pre>'; die;

                if (isset($mso_response['dhl_rate'], $mso_response['dhl_rate']['products'], $mso_response['dhl_rate']['exchangeRates'])) {
                    $message = '<span style="color: green;"><b>Success! </b> Your request to connect to the UPS API has been processed successfully.</span>';
                    $action = true;
                } elseif (isset($mso_response['dhl_rate'], $mso_response['dhl_rate']['error'], $mso_response['dhl_rate']['message'])) {
                    $message = '<span style="color: red;"><b>Error! </b> ' . $mso_response['dhl_rate']['message'] . '</span>';
                    $action = false;
                } elseif (isset($mso_response['dhl_rate'], $mso_response['dhl_rate']['detail'])) {
                    $detail = $mso_response['dhl_rate']['detail'];
                    $additionalDetails = '';
                    if (isset($mso_response['dhl_rate']['additionalDetails']) && !empty($mso_response['dhl_rate']['additionalDetails'])) {
                        foreach ($mso_response['dhl_rate']['additionalDetails'] as $key => $message) {
                            $additionalDetails .= strlen($additionalDetails) > 0 ? ", " . $message : $message;
                        }
                    }
                    $message = '<span style="color: red;"><b>Error! </b> ' . $detail . "[Additional Details: $additionalDetails ]" . '</span>';
                    $action = false;
                } elseif (isset($mso_response['error'], $mso_response['message'])) {
                    $message = '<span style="color: red;"><b>Error! </b> ' . $mso_response['message'] . '</span>';
                    $action = false;
                } else {
                    $message = '<span style="color: red;"><b>Error! </b> Please try again later.</span>';
                    $action = false;
                }

                update_option('mso_dhl_spq_credentials_status', $message);
                $results = [
                    'action' => $action,
                    'message' => $message
                ];
                break;
            case 'mso_ups_lfq':
                $fields = [
                    'mso_ups_lfq_account_number' => 'shipper_number',
                    'mso_ups_lfq_username' => 'user_id',
                    'mso_ups_lfq_password' => 'password',
                    'mso_ups_lfq_access_key' => 'access'
                ];

                $mso_packages['shipments']['60603']['action'] = 'lfq';
                $post_fields = self::mso_post_data($mso_post_data, $fields);
                $mso_packages['carriers']['ups_lfq'] = $post_fields;
                $mso_response = self::mso_api_response($mso_packages);

                if (isset($mso_response['ups_lfq_rate']['Response']['ResponseStatus']['Code']) && $mso_response['ups_lfq_rate']['Response']['ResponseStatus']['Code'] == '1') {
                    $message = '<span style="color: green;"><b>Success! </b> Your request to connect to the UPS Freight API has been processed successfully.</span>';
                    $action = true;
                } elseif (isset($mso_response['ups_lfq_rate']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'])) {
                    $errorDescription = $mso_response['ups_lfq_rate']['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'];
                    $message = '<span style="color: red;"><b>Error! </b>' . $errorDescription . '</span>';
                    $action = false;
                } elseif (isset($mso_response['error'], $mso_response['message'])) {
                    $errorMessage = $mso_response['message'];
                    $message = '<span style="color: red;"><b>Error! </b> ' . $errorMessage . '</span>';
                    $action = false;
                } else {
                    $message = '<span style="color: red;"><b>Error! </b> Please try again later.</span>';
                    $action = false;
                }

                update_option('mso_ups_lfq_credentials_status', $message);

                $results = [
                    'action' => $action,
                    'message' => $message
                ];
                break;

            case 'mso_fedex_sqp':
                $fields = [
                    'mso_fedex_spq_user_key' => 'key',
                    'mso_fedex_spq_user_password' => 'password',
                    'mso_fedex_spq_account_number' => 'account_number',
                    'mso_fedex_spq_meter_number' => 'meter_number',
                ];

                $post_fields = self::mso_post_data($mso_post_data, $fields);
                $post_fields['simple'] = true;
                $mso_packages['carriers']['fedex_small'] = $post_fields;
                $mso_response = self::mso_api_response($mso_packages);

                $rates = isset($mso_response['fedex_rate']['domestic_rate']['rate']) ? $mso_response['fedex_rate']['domestic_rate']['rate'] : [];

                if (isset($rates['HighestSeverity']) && !in_array($rates['HighestSeverity'], ['FAILURE', 'ERROR'])) {
                    $message = '<span style="color: green;"><b>Success! </b> Your request to connect to the FedEx API has been processed successfully.</span>';
                    $action = true;
                } elseif (isset($rates['Notifications']['Message']) && in_array($rates['HighestSeverity'], ['FAILURE', 'ERROR'])) {
                    $errorMessage = $rates['Notifications']['Message'];
                    $message = '<span style="color: red;"><b>Error! </b>' . $errorMessage . '</span>';
                    $action = false;
                } elseif (isset($rates['faultstring'])) {
                    $errorString = $rates['faultstring'];
                    $message = '<span style="color: red;"><b>Error! </b>' . $errorString . '</span>';
                    $action = false;
                } elseif (isset($mso_response['error'], $mso_response['message'])) {
                    $errorMessage = $mso_response['message'];
                    $message = '<span style="color: red;"><b>Error! </b> ' . $errorMessage . '</span>';
                    $action = false;
                } else {
                    $message = '<span style="color: red;"><b>Error! </b> Please try again later.</span>';
                    $action = false;
                }

                update_option('mso_fedex_spq_credentials_status', $message);

                $results = [
                    'action' => $action,
                    'message' => $message
                ];
                break;

            case 'mso_fedex_lfq':
                $fields = [
                    'mso_fedex_lfq_user_key' => 'key',
                    'mso_fedex_lfq_user_password' => 'password',
                    'mso_fedex_lfq_account_number' => 'account_number',
                    'mso_fedex_lfq_meter_number' => 'meter_number',
                    'mso_fedex_lfq_billing_account_number' => 'billing_account_number',
                    'mso_fedex_lfq_third_party_account_number' => 'third_party_account_number',
                    // Billing details
                    'mso_fedex_lfq_billing_address' => 'address_1',
                    'mso_fedex_lfq_billing_city' => 'city',
                    'mso_fedex_lfq_billing_state' => 'state',
                    'mso_fedex_lfq_billing_zip' => 'postcode',
                    'mso_fedex_lfq_billing_country' => 'country',
                    // Physical details
                    'mso_fedex_lfq_physical_address' => 'physical_address_1',
                    'mso_fedex_lfq_physical_city' => 'physical_city',
                    'mso_fedex_lfq_physical_state' => 'physical_state',
                    'mso_fedex_lfq_physical_zip' => 'physical_postcode',
                    'mso_fedex_lfq_physical_country' => 'physical_country'
                ];

                $mso_packages['shipments']['60603']['action'] = 'lfq';
                $post_fields = self::mso_post_data($mso_post_data, $fields);

                extract($post_fields);
                $mso_packages['shipments']['60603']['ship_from'] = [
                    'address1' => $physical_address_1,
                    'address2' => '',
                    'city' => $physical_city,
                    'postcode' => $physical_postcode,
                    'state' => $physical_state,
                    'country' => $physical_country,
                    'mso_city' => $physical_city,
                    'mso_zip' => $physical_postcode,
                    'mso_state' => $physical_state,
                    'mso_country' => $physical_country
                ];

                $mso_packages['carriers']['fedex_lfq'] = $post_fields;
                $mso_response = self::mso_api_response($mso_packages);

                $rates = isset($mso_response['fedex_lfq_rate']) ? $mso_response['fedex_lfq_rate'] : [];

                if (isset($rates['HighestSeverity']) && !in_array($rates['HighestSeverity'], ['FAILURE', 'ERROR'])) {
                    $message = '<span style="color: green;"><b>Success! </b> Your request to connect to the FedEx Freight API has been processed successfully.</span>';
                    $action = true;
                } elseif (isset($rates['Notifications']['Message']) && in_array($rates['HighestSeverity'], ['FAILURE', 'ERROR'])) {
                    $errorMessage = $rates['Notifications']['Message'];
                    $message = '<span style="color: red;"><b>Error! </b>' . $errorMessage . '</span>';
                    $action = false;
                } elseif (isset($mso_response['error'], $mso_response['message'])) {
                    $errorMessage = $mso_response['message'];
                    $message = '<span style="color: red;"><b>Error! </b> ' . $errorMessage . '</span>';
                    $action = false;
                } else {
                    $message = '<span style="color: red;"><b>Error! </b> Please try again later.</span>';
                    $action = false;
                }

                update_option('mso_fedex_lfq_credentials_status', $message);

                $results = [
                    'action' => $action,
                    'message' => $message
                ];
                break;
        }

        echo json_encode($results);
        die;
    }
}