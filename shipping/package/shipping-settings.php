<?php

namespace ShippingSettings;

use MsoDhl\MsoDhl;
use MsoUps\MsoUps;
use MsoUsps\MsoUsps;
use MsoFedex\MsoFedex;

class ShippingSettings
{

    public function __construct()
    {
        // UPS
        add_filter('mso_ups_domestic_carriers', [$this, 'mso_ups_domestic_carriers'], 10, 1);
        add_filter('mso_ups_international_carriers', [$this, 'mso_ups_international_carriers'], 10, 1);
        // Fedex
        add_filter('mso_fedex_domestic_carriers', [$this, 'mso_fedex_domestic_carriers'], 10, 1);
        add_filter('mso_fedex_international_carriers', [$this, 'mso_fedex_international_carriers'], 10, 1);
        // USPS
        add_filter('mso_usps_domestic_carriers', [$this, 'mso_usps_domestic_carriers'], 10, 1);
        add_filter('mso_usps_international_carriers', [$this, 'mso_usps_international_carriers'], 10, 1);

        // DHL
        add_filter('mso_dhl_express_carriers', [$this, 'mso_dhl_express_carriers'], 10, 1);
    }

    // Get DHL Express carriers
    public function mso_dhl_express_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
            'H' => 'economy_select',
            'K' => 'express_9',
            'E' => 'express_9',
            'A' => 'express_10_30',
            'T' => 'express_12',
            'Y' => 'express_12',
            'P' => 'express_worldwide',
            'D' => 'express_worldwide_doc',
            'N' => 'express_domestic'
        ];

        $carriers = [
            // Domestic
            'N' => 'domestic_express',
            '1' => 'domestic_express_12_00',
            'I' => 'domestic_express_9_00',
            // Rest Of World (Non-Documents)
            'P' => 'express_worldwide',
            'Y' => 'express_12_00',
            'M' => 'express_10_30',
            'E' => 'express_9_00',
            // Rest Of World (Documents)
            'D' => 'express_worldwide_doc',
            'T' => 'express_12_00_doc',
            'L' => 'express_10_30_doc',
            'K' => 'express_9_00_doc',
            // Road Services
            'H' => 'economy_select_international'
        ];

//        $carriers_force = MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_DHL_GET])) ? true : false;
        $carriers_force = mso_force_carriers(MSO_DHL_GET);
//        MSO_DONT_AUTH ? $carriers_force = false : '';
        $template = MsoDhl::mso_init();
        $carrier_common = 'mso_dhl_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes' || $carriers_force) {
                $carrier_enabled = true;
                $carriers[$key] = [
                    'label' => $label,
                    'markup' => get_option($carrier_common . $carrier . '_markup'),
                ];
            }
        }

        !$carrier_enabled ? $carriers = [] : '';

//        echo '<pre>';
//        print_r($carriers);
//        echo '</pre>'; die;
        return $carriers;
    }

    // Get ups domestic carriers
    public function mso_usps_domestic_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
//            '25' => 'first_class_mail',
            '29' => 'retail_ground',
            '27' => 'priority_mail',
            '28' => 'priority_mail_flat_rate',
            '26' => 'priority_mail_express',
            '6' => 'media_mail',
//            '59' => 'first_class_package_service',
            '34' => 'ground_advantage',
            '7' => 'library_mail'
        ];

//        $carriers_force = MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_USPS_GET])) ? true : false;
        $carriers_force = mso_force_carriers(MSO_USPS_GET);
        $template = MsoUsps::mso_init();
        $carrier_common = 'mso_usps_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes' || $carriers_force) {
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
    public function mso_usps_international_carriers($carriers)
    {
        $carrier_enabled = false;
        $carriers = [
            '15' => 'first_class_package_international_service',
            '2' => 'priority_mail_international',
            '1' => 'priority_mail_express_international',
//            '4' => 'global_express_guaranteed',
//            '6' => 'international_shipping_for_flat_rate_boxes_and_envelopes'
        ];

//        $carriers_force = MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_USPS_GET])) ? true : false;
        $carriers_force = mso_force_carriers(MSO_USPS_GET);
        $template = MsoUsps::mso_init();
        $carrier_common = 'mso_usps_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes' || $carriers_force) {
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

//        $carriers_force = MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_UPS_GET])) ? true : false;
        $carriers_force = mso_force_carriers(MSO_UPS_GET);
        $template = MsoUps::mso_init();
        $carrier_common = 'mso_ups_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes' || $carriers_force) {
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

//        $carriers_force = MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_UPS_GET])) ? true : false;
        $carriers_force = mso_force_carriers(MSO_UPS_GET);
        $template = MsoUps::mso_init();
        $carrier_common = 'mso_ups_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes' || $carriers_force) {
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

//        $carriers_force = MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_FEDEX_GET])) ? true : false;
        $carriers_force = mso_force_carriers(MSO_FEDEX_GET);
        $template = MsoFedex::mso_init();
        $carrier_common = 'mso_fedex_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes' || $carriers_force) {
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

//        $carriers_force = MSO_PLAN_STATUS != 'success' || !(!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_FEDEX_GET])) ? true : false;
        $carriers_force = mso_force_carriers(MSO_FEDEX_GET);
        $template = MsoFedex::mso_init();
        $carrier_common = 'mso_fedex_spq_';
        foreach ($carriers as $key => $carrier) {
            $action = get_option($carrier_common . $carrier . '_action');
            $label_path = $carrier_common . $carrier . '_label';
            $label = get_option($label_path);
            $label = (isset($label) && strlen($label) > 0) ? $label : $template[$label_path]['placeholder'];
            if ($action == 'yes' || $carriers_force) {
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
            if ($action == 'yes' || !$this->carrier_ps) {
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