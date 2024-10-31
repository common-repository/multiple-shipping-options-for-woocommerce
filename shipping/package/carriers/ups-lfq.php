<?php

namespace MsoUpsLfqCarriers;

class MsoUpsLfqCarriers
{
    // UPS LFQ rates
    public function mso_ups_rates($rates, $response, $accessorials)
    {
        $ups_ltl_rates = [];
        if (isset($rates['TotalShipmentCharge']['MonetaryValue'])) {
            $accessorials = (isset($accessorials['ups_lfq'])) ? $accessorials['ups_lfq'] : [];
            $cost = $rates['TotalShipmentCharge']['MonetaryValue'];
            $ups_ltl_rates[] = [
                'id' => 'msofw_ups_lfq',
                'label' => 'UPS Freight',
                'cost' => $cost,
                'ups_lfq_code' => 308,
                'carrier' => 'ups_lfq',
                'markup' => get_option('mso_ups_lfq_markup'),
                'accessorials' => $accessorials,
                'response' => $response
            ];
        } elseif (isset($rates['detail'], $rates['detail']['Errors'], $rates['detail']['Errors']['ErrorDetail'], $rates['detail']['Errors']['ErrorDetail']['PrimaryErrorCode'], $rates['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'])) {
            $accessorials = (isset($accessorials['ups_lfq'])) ? $accessorials['ups_lfq'] : [];
            $ups_ltl_rates = [
                'error' => true,
                'message' => $rates['detail']['Errors']['ErrorDetail']['PrimaryErrorCode']['Description'],
                'carrier' => 'ups_lfq',
                'ups_lfq_code' => 308,
                'accessorials' => $accessorials,
                'response' => $response
            ];
        }

        return $ups_ltl_rates;
    }
}