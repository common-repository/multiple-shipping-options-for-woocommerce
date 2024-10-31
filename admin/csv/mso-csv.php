<?php

namespace MsoCsv;

use MsoProductDetail\MsoProductDetail;

class MsoCsv
{
    public $mso_locations = [];

    public function __construct($mso_locations)
    {
        // Getting Locations
        $this->mso_locations = $mso_locations;

        add_filter('woocommerce_product_export_product_column_mso_city', [$this, 'mso_city'], 10, 2);
        add_filter('woocommerce_product_export_product_column_mso_address', [$this, 'mso_address'], 10, 2);
        add_filter('woocommerce_product_export_product_column_mso_state', [$this, 'mso_state'], 10, 2);
        add_filter('woocommerce_product_export_product_column_mso_zip', [$this, 'mso_zip'], 10, 2);
        add_filter('woocommerce_product_export_product_column_mso_country', [$this, 'mso_country'], 10, 2);

        // Update new columns
        add_filter('woocommerce_product_export_product_column_mso_product_locations', [$this, 'mso_product_locations'], 10, 2);
        add_filter('woocommerce_product_export_product_column_mso_product_locations_variation', [$this, 'mso_product_locations'], 10, 2);

        // Add columns
        add_filter('woocommerce_product_export_column_names', [$this, 'mso_add_export_column'], 10, 2);
        add_filter('woocommerce_product_export_product_default_columns', [$this, 'mso_add_export_column'], 10, 2);

        // Import products
        add_filter('woocommerce_product_importer_parsed_data', [$this, 'mso_import_csv'], '99', '2');
    }

    // Import products
    function mso_import_csv($data, $parse_data)
    {
        $locations = [];
        foreach ($data['meta_data'] as $key => $meta_data) {
            $location_part = trim($meta_data['value']);
            switch ($meta_data['key']) {
                // Update new columns
                case 'mso_address':
                    $locations['mso_address'] = $location_part;
                    unset($data['meta_data'][$key]);
                    break;
                case 'mso_city':
                    $locations['mso_city'] = $location_part;
                    unset($data['meta_data'][$key]);
                    break;
                case 'mso_state':
                    $locations['mso_state'] = $location_part;
                    unset($data['meta_data'][$key]);
                    break;
                case 'mso_zip':
                    $locations['mso_zip'] = $location_part;
                    unset($data['meta_data'][$key]);
                    break;
                case 'mso_country':
                    $locations['mso_country'] = $location_part;
                    unset($data['meta_data'][$key]);
                    break;
            }
        }

        if (!empty($locations) && !empty($this->mso_locations)) {
            $post_type = 'mso_location';
            $mso_location_merge = implode("", $locations);
            $wp_post_id = post_exists($locations['mso_zip'], $mso_location_merge);
            if ($wp_post_id > 0) {
                // update with existing id
                $data['meta_data'][] = [
                    'key' => 'mso_product_locations',
                    'value' => $wp_post_id,
                ];
            } else {
                // insert
                $wp_post = array("post_title" => $locations['mso_zip'],
                    "post_content" => $mso_location_merge,
                    "post_excerpt" => 'custom_post',
                    "post_type" => $post_type,
                    "post_status" => 'publish',
                );

                $wp_post_id = wp_insert_post($wp_post, true);
                add_post_meta($wp_post_id, $post_type, $locations);
                $data['meta_data'][] = [
                    'key' => 'mso_product_locations',
                    'value' => $wp_post_id,
                ];
            }
        }

        return $data;
    }

    // Add columns
    public function mso_add_export_column($columns)
    {
        $columns['mso_address'] = 'Meta:mso_address';
        $columns['mso_city'] = 'Meta:mso_city';
        $columns['mso_state'] = 'Meta:mso_state';
        $columns['mso_zip'] = 'Meta:mso_zip';
        $columns['mso_country'] = 'Meta:mso_country';

        if (isset($columns['meta:mso_product_locations'])) {
            unset($columns['meta:mso_product_locations']);
        }

        return $columns;
    }

    // Get location ID
    public function mso_product_locations($value, $product)
    {
        return get_post_meta($product->get_id(), 'mso_product_locations', true);
    }

    // City
    public function mso_city($value, $product)
    {
        $location_part = '';
        $location_id = get_post_meta($product->get_id(), 'mso_product_locations', true);
        if ((is_numeric($location_id) || $location_id == 'store_address') && isset($this->mso_locations[$location_id]) && isset($this->mso_locations[$location_id]['mso_city'])) {
            $location_part = $this->mso_locations[$location_id]['mso_city'];
        }

        return $location_part;
    }

    // Address
    public function mso_address($value, $product)
    {
        $location_part = '';
        $location_id = get_post_meta($product->get_id(), 'mso_product_locations', true);
        if ((is_numeric($location_id) || $location_id == 'store_address') && isset($this->mso_locations[$location_id]) && isset($this->mso_locations[$location_id]['mso_address'])) {
            $location_part = $this->mso_locations[$location_id]['mso_address'];
        }

        return $location_part;
    }

    // State
    public function mso_state($value, $product)
    {
        $location_part = '';
        $location_id = get_post_meta($product->get_id(), 'mso_product_locations', true);
        if ((is_numeric($location_id) || $location_id == 'store_address') && isset($this->mso_locations[$location_id]) && isset($this->mso_locations[$location_id]['mso_state'])) {
            $location_part = $this->mso_locations[$location_id]['mso_state'];
        }

        return $location_part;
    }

    // zip
    public function mso_zip($value, $product)
    {
        $location_part = '';
        $location_id = get_post_meta($product->get_id(), 'mso_product_locations', true);
        if ((is_numeric($location_id) || $location_id == 'store_address') && isset($this->mso_locations[$location_id]) && isset($this->mso_locations[$location_id]['mso_zip'])) {
            $location_part = $this->mso_locations[$location_id]['mso_zip'];
        }

        return $location_part;
    }

    // Country
    public function mso_country($value, $product)
    {
        $location_part = '';
        $location_id = get_post_meta($product->get_id(), 'mso_product_locations', true);
        if ((is_numeric($location_id) || $location_id == 'store_address') && isset($this->mso_locations[$location_id]) && isset($this->mso_locations[$location_id]['mso_country'])) {
            $location_part = $this->mso_locations[$location_id]['mso_country'];
        }

        return $location_part;
    }
}