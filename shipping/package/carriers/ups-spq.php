<?php

namespace MsoUpsCarriers;

use MsoUps\MsoUps;

class MsoUpsCarriers
{
    public function __construct()
    {
        add_filter('mso_ups_domestic_carriers', [$this, 'mso_ups_domestic_carriers'], 10, 1);
        add_filter('mso_ups_international_carriers', [$this, 'mso_ups_international_carriers'], 10, 1);
    }

    // Get ups rates
    public function mso_ups_rates($rates, $response, $accessorials, $is_shipment)
    {
        $ups_rates = [];
        if (isset($rates['RatedShipment']) && !empty($rates['RatedShipment'])) {
            switch ($is_shipment) {
                case 'international':
                    $carriers = apply_filters('mso_ups_international_carriers', []);
                    break;
                default:
                    $carriers = apply_filters('mso_ups_domestic_carriers', []);
                    break;
            }

            $accessorials = (isset($accessorials['ups'])) ? $accessorials['ups'] : [];
            $rates = $rates['RatedShipment'];
            foreach ($rates as $key => $rate) {
                $code = (isset($rate['Service']['Code'])) ? $rate['Service']['Code'] : '';

                if (isset($carriers[$code]['label'], $carriers[$code]['markup'])) {
                    $label = $carriers[$code]['label'];
                    $markup = $carriers[$code]['markup'];
                    if (isset($rate['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['MonetaryValue'])) {
                        $cost = $rate['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['MonetaryValue'];
                    } elseif (isset($rate['TotalCharges']['MonetaryValue'])) {
                        $cost = $rate['TotalCharges']['MonetaryValue'];
                    }

                    $ups_rates[] = [
                        'id' => 'msofw_' . $code,
                        'label' => $label,
                        'cost' => $cost,
                        'ups_spq_code' => $code,
                        'carrier' => 'ups',
                        'markup' => $markup,
                        'accessorials' => $accessorials,
                        'response' => $response
                    ];
                }
            }
        } else if (isset($rates['Response'], $rates['Response']['Error'], $rates['Response']['Error']['ErrorDescription'])) {
            $accessorials = (isset($accessorials['ups'])) ? $accessorials['ups'] : [];
            $ups_rates = [
                'error' => true,
                'message' => $rates['Response']['Error']['ErrorDescription'],
                'carrier' => 'ups',
                'accessorials' => $accessorials,
                'response' => $response
            ];
        }

        return $ups_rates;
    }

    // Get ups domestic carriers
    public function mso_ups_domestic_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
            '03' => 'ground',
            '02' => '2nd_day_air',
            '59' => '2nd_day_air_am',
            '13' => 'next_day_air_saver',
            '01' => 'next_day_air',
            '14' => 'next_day_air_early',
            '12' => '3_day_select'
        ];

        $template = MsoUps::mso_init();
        $carrier_common = 'mso_ups_spq_';
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

    // Get ups international carriers
    public function mso_ups_international_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
            '11' => 'standard',
            '08' => 'expedited',
            '65' => 'express_saver',
            '07' => 'express',
            '54' => 'express_plus'
        ];

        $template = MsoUps::mso_init();
        $carrier_common = 'mso_ups_spq_';
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