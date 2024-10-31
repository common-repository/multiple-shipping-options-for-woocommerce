<?php

namespace MsoFedexCarriers;

use MsoFedex\MsoFedex;

class MsoFedexCarriers
{
    public function __construct()
    {
        add_filter('mso_fedex_domestic_carriers', [$this, 'mso_fedex_domestic_carriers'], 10, 1);
        add_filter('mso_fedex_international_carriers', [$this, 'mso_fedex_international_carriers'], 10, 1);
        add_filter('mso_fedex_one_rate_carriers', [$this, 'mso_fedex_one_rate_carriers'], 10, 1);
    }

    // Fedex rates
    public function mso_fedex_rates($rates, $response, $packaging_type, $accessorials, $is_shipment)
    {
        $fedex_rates = [];
        if (isset($rates['HighestSeverity']) && $rates['HighestSeverity'] != 'FAILURE' && $rates['HighestSeverity'] != 'ERROR') {

            switch ($is_shipment) {
                case 'international':
                    $carriers = apply_filters('mso_fedex_international_carriers', []);
                    break;
                default:
                    $carriers = apply_filters('mso_fedex_domestic_carriers', []);
                    break;
            }

            $rates = isset($rates['RateReplyDetails']) ? $rates['RateReplyDetails'] : [];
            if (isset($rates['ServiceType'])) {
                $rates = [$rates];
            }

            $accessorials = (isset($accessorials['fedex'])) ? $accessorials['fedex'] : [];
            foreach ($rates as $key => $rate) {
                $service_type = isset($rate['ServiceType']) ? $rate['ServiceType'] : '';
                $rated_shipment_details = isset($rate['RatedShipmentDetails']) ? $rate['RatedShipmentDetails'] : [];
                if (!empty($rated_shipment_details) && !isset($rated_shipment_details['ShipmentRateDetail']['TotalNetCharge']['Amount'])) {
                    $rated_shipment_details = reset($rated_shipment_details);
                }

                $cost = 0;
                if (isset($rated_shipment_details['ShipmentRateDetail']['TotalNetCharge']['Amount'])) {
//                    $cost = number_format($rated_shipment_details['ShipmentRateDetail']['TotalNetCharge']['Amount'], 2, ".", ",");
                    $cost = number_format($rated_shipment_details['ShipmentRateDetail']['TotalNetCharge']['Amount'], 2, ".", "");
                }

                $delivery_date = '';
                if (isset($rate['DeliveryTimestamp'])) {
                    $delivery_date = $rate['DeliveryTimestamp'];
                } else if (isset($rate['TransitTime'])) {
                    $delivery_date = $rate['TransitTime'];
                }

                if (strlen($delivery_date) > 0) {
                    $delivery_date = ' ( Estimated delivery date is ' . date('Y-m-d', strtotime($delivery_date)) . ' by ' . date('h:i A', strtotime($delivery_date)) . ')';
                }

                if (isset($carriers[$service_type]['label'], $carriers[$service_type]['markup'])) {
                    $label = $carriers[$service_type]['label'];
                    $fedex_rates[] = [
                        'id' => $service_type . 'domestic',
                        'label' => $label . $delivery_date,
                        'label' => $label,
                        'cost' => $cost,
                        'fedex_spq_service' => $service_type,
                        'packaging_type' => $packaging_type,
                        'carrier' => 'fedex',
                        'markup' => $carriers[$service_type]['markup'],
                        'accessorials' => $accessorials,
                        'response' => $response
                    ];
                }
            }
        } elseif (isset($rates['HighestSeverity'], $rates['Notifications'], $rates['Notifications']['Message']) && ($rates['HighestSeverity'] == 'FAILURE' || $rates['HighestSeverity'] == 'ERROR')) {
            $accessorials = (isset($accessorials['fedex'])) ? $accessorials['fedex'] : [];
            $fedex_rates = [
                'error' => true,
                'message' => $rates['Notifications']['Message'],
                'carrier' => 'fedex',
                'accessorials' => $accessorials,
                'response' => $response
            ];
        }

        return $fedex_rates;
    }

    // Get fedex domestic carriers
    public function mso_fedex_domestic_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
            'GROUND_HOME_DELIVERY' => 'home_delivery',
            'FEDEX_GROUND' => 'ground',
            'FEDEX_EXPRESS_SAVER' => 'express_saver',
            'FEDEX_2_DAY' => '2nd_day',
            'FEDEX_2_DAY_AM' => '2nd_day_am',
            'STANDARD_OVERNIGHT' => 'standard_overnight',
            'PRIORITY_OVERNIGHT' => 'priority_overnight',
            'FIRST_OVERNIGHT' => 'first_overnight',
//            'SMART_POST' => 'smart_post'
        ];

        $template = MsoFedex::mso_init();
        $carrier_common = 'mso_fedex_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes') {
                $carrier_enabled = true;
                $carriers[$key] = [
                    'label' => $label,
                    'markup' => get_option($carrier_common . $carrier . '_markup'),
                ];
            }
        }

        !$carrier_enabled ? $carriers = [] : '';
        return $carriers;
    }

    // Get fedex international carriers
    public function mso_fedex_international_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
            'FEDEX_GROUND' => 'international_ground',
            'INTERNATIONAL_ECONOMY' => 'international_economy',
            'international_economy_distribution' => 'international_economy_distribution',
            'international_economy_freight' => 'international_economy_freight',
            'international_first' => 'international_first',
            'INTERNATIONAL_PRIORITY' => 'international_priority',
            'international_priority_distribution' => 'international_priority_distribution',
            'international_priority_freight' => 'international_priority_freight',
            'international_distribution_freight' => 'international_distribution_freight'
        ];

        $template = MsoFedex::mso_init();
        $carrier_common = 'mso_fedex_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes') {
                $carrier_enabled = true;
                $carriers[$key] = [
                    'label' => $label,
                    'markup' => get_option($carrier_common . $carrier . '_markup'),
                ];
            }
        }

        !$carrier_enabled ? $carriers = [] : '';
        return $carriers;
    }

    // Get fedex one rate carriers
    public function mso_fedex_one_rate_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
            'FEDEX_EXPRESS_SAVER' => 'express_saver',
            'FEDEX_2_DAY' => '2nd_day',
            'FEDEX_2_DAY_AM' => '2nd_day_am',
            'STANDARD_OVERNIGHT' => 'standard_overnight',
            'PRIORITY_OVERNIGHT' => 'priority_overnight',
            'FIRST_OVERNIGHT' => 'first_overnight'
        ];

        $template = MsoFedex::mso_init();
        $carrier_common = 'mso_fedex_spq_one_rate_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes') {
                $carrier_enabled = true;
                $carriers[$key] = [
                    'label' => $label,
                    'markup' => get_option($carrier_common . $carrier . '_markup'),
                ];
            }
        }

        !$carrier_enabled ? $carriers = [] : '';
        return $carriers;
    }
}