<?php

/**
 * Product detail page.
 */

namespace MsoPackagingAjax;


use MsoPackaging\MsoPackaging;

/**
 * Location on product page.
 * Class MsoPackagingAjax
 * @package MsoPackagingAjax
 */
class MsoPackagingAjax
{

    /**
     * Hook for call.
     * MsoPackagingAjax constructor.
     */
    public function __construct()
    {
        // Save Location data
        add_action('wp_ajax_mso_save_packaging', [$this, 'mso_save_packaging']);

        // Get location
        add_action('wp_ajax_mso_get_location_data', [$this, 'mso_get_location_data']);

        // Delete location
        add_action('wp_ajax_mso_delete_packaging', [$this, 'mso_delete_packaging']);

        // When click  Enable / Disable on packaging page.
        add_action('wp_ajax_mso_edpa', [$this, 'mso_edpa']);
    }

    // When click  Enable / Disable on packaging page.
    public function mso_edpa()
    {
        $mso_current_action = (isset($_POST['mso_current_action'])) ? sanitize_text_field($_POST['mso_current_action']) : 'no';
        $mso_cid = (isset($_POST['mso_cid'])) ? sanitize_text_field($_POST['mso_cid']) : '';
        update_option($mso_cid, $mso_current_action);
    }

    // Delete location row.
    public function mso_delete_packaging()
    {
        $postId = (isset($_POST['mso_packaging_id'])) ? sanitize_text_field($_POST['mso_packaging_id']) : 0;
        wp_delete_post($postId, true);
        var_dump($postId);
    }

    // Get location data.
    public function mso_get_location_data()
    {
        $product_detail_obj = new MsoProductDetail();
        echo $product_detail_obj->mso_location_post_meta();
        exit;
    }

    // Save location data.
    public function mso_save_packaging()
    {
        $location_data = (isset($_POST['mso_post_data'])) ? $_POST['mso_post_data'] : [];
        $mso_main_div = (isset($_POST['mso_main_div'])) ? sanitize_text_field($_POST['mso_main_div']) : '';
        $mso_mainplan = (isset($_POST['mso_mainplan'])) ? sanitize_text_field($_POST['mso_mainplan']) : '';

        $type_content = 'box';
        $post_type = 'mso_packaging';
        if ($mso_main_div == 'mso_pallet_solution') {
            $post_type = 'mso_pallet';
            $type_content = 'pallet';
        }

        $duplicate_box_name = '';
        foreach ($location_data as $key => $mso_location) {
            $form_data_validation = [];
            parse_str($mso_location, $form_data_validation);
            if (isset($form_data_validation['mso_packaging_id'])) {
                $mso_packaging_id = $form_data_validation['mso_packaging_id'];
                $box_name = (isset($form_data_validation['box_name'])) ? sanitize_text_field($form_data_validation['box_name']) : '';
                $box_name_exists = post_exists($box_name);
                if ($box_name_exists > 0 && $mso_packaging_id != $box_name_exists) {
                    $duplicate_box_name .= (strlen($duplicate_box_name) > 0) ? ", " . $box_name : $box_name;
                }
            }
        }

        // When duplicate boxes are exists.
        if (strlen($duplicate_box_name) > 0) {
            echo json_encode([
                'action' => false,
                'message' => "Duplicate $type_content name " . $duplicate_box_name . " not allowed."
            ]);
            die;
        }

        foreach ($location_data as $key => $location) {
            $form_data = [];
            parse_str($location, $form_data);
            if (isset($form_data['mso_packaging_id'])) {
                $mso_packaging_id = $form_data['mso_packaging_id'];
                unset($form_data['mso_packaging_id']);
                $mso_location_merge = implode("", $form_data);
                $box_name = (isset($form_data['box_name'])) ? sanitize_text_field($form_data['box_name']) : '';

                if ($mso_packaging_id != 'new' && $mso_packaging_id > 0) {
                    // update
                    $wp_post = array("ID" => $mso_packaging_id, "post_title" => $box_name, "post_content" => $mso_location_merge);
                    wp_update_post($wp_post, true);
                    update_post_meta($mso_packaging_id, $post_type, $form_data);
                } else {
                    // insert
                    $wp_post = array("post_title" => $box_name,
                        "post_content" => $mso_location_merge,
                        "post_excerpt" => 'custom_post',
                        "post_type" => $post_type,
                        "post_status" => 'publish',
                    );

                    $wp_post_id = wp_insert_post($wp_post, true);
                    add_post_meta($wp_post_id, $post_type, $form_data);
                }
            }
        }

        $packaging_obj = new MsoPackaging();
        if ($mso_main_div == 'mso_pallet_solution') {
            echo $packaging_obj->mso_pallet_post_meta($mso_mainplan);
        } else {
            echo $packaging_obj->mso_packaging_post_meta($mso_mainplan);
        }
        exit;
    }
}
