<?php

namespace MsoFedexLfqCarriers;

class MsoFedexLfqCarriers
{
    // Fedex LFQ rates
    public function mso_fedex_rates($rates, $response, $accessorials)
    {
        $fedex_rates = [];
        if (isset($rates['HighestSeverity']) && $rates['HighestSeverity'] != 'FAILURE' && $rates['HighestSeverity'] != 'ERROR') {
            $rates = isset($rates['RateReplyDetails']) ? $rates['RateReplyDetails'] : [];
            if (isset($rates['ServiceType'])) {
                $rates = [$rates];
            }

            $accessorials = (isset($accessorials['fedex_lfq'])) ? $accessorials['fedex_lfq'] : [];
            foreach ($rates as $key => $rate) {
                $service_type = isset($rate['ServiceType']) ? $rate['ServiceType'] : '';
                $rated_shipment_details = isset($rate['RatedShipmentDetails']) ? $rate['RatedShipmentDetails'] : [];
                if (!empty($rated_shipment_details) && !isset($rated_shipment_details['ShipmentRateDetail']['TotalNetCharge']['Amount'])) {
                    $rated_shipment_details = reset($rated_shipment_details);
                }

                $cost = 0;
                if (isset($rated_shipment_details['ShipmentRateDetail']['TotalNetCharge']['Amount'])) {
                    $cost = $rated_shipment_details['ShipmentRateDetail']['TotalNetCharge']['Amount'];
                }

                if (isset($rate['DeliveryTimestamp'])) {
                    $delivery_date = $rate['DeliveryTimestamp'];
                } else if (isset($rate['TransitTime'])) {
                    $delivery_date = $rate['TransitTime'];
                }

                if (strlen($delivery_date)) {
                    $delivery_date = ' ( Estimated delivery date is ' . date('Y-m-d', strtotime($delivery_date)) . ' by ' . date('h:i A', strtotime($delivery_date)) . ')';
                }

                $carriers_name = [
                    'FEDEX_FREIGHT_ECONOMY' => 'Fedex Freight Economy',
                    'FEDEX_FREIGHT_PRIORITY' => 'Fedex Freight Priority',
                ];
                $label = (isset($carriers_name[$service_type])) ? $carriers_name[$service_type] : $service_type;
                if ($cost > 0) {
                    $fedex_rates[] = [
                        'id' => $service_type,
                        'label' => $label . $delivery_date,
                        'label' => $label,
                        'cost' => $cost,
                        'fedex_lfq_service' => $service_type,
                        'packaging_type' => 'YOUR_PACKAGING',
                        'carrier' => 'fedex_lfq',
                        'markup' => get_option('mso_fedex_lfq_markup'),
                        'accessorials' => $accessorials,
                        'response' => $response
                    ];
                }
            }
        } elseif (isset($rates['HighestSeverity'], $rates['Notifications'], $rates['Notifications']['Message']) && ($rates['HighestSeverity'] == 'FAILURE' || $rates['HighestSeverity'] == 'ERROR')) {
            $accessorials = (isset($accessorials['fedex_lfq'])) ? $accessorials['fedex_lfq'] : [];
            $fedex_rates = [
                'error' => true,
                'message' => $rates['Notifications']['Message'],
                'carrier' => 'fedex_lfq',
                'accessorials' => $accessorials,
                'response' => $response
            ];
        }

        return $fedex_rates;
    }
}