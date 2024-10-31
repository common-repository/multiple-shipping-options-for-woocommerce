<?php

/**
 * Product detail page.
 */

namespace MsoProductAjax;


use MsoProductDetail\MsoProductDetail;

/**
 * Location on product page.
 * Class MsoProductAjax
 * @package MsoProductAjax
 */
class MsoProductAjax
{

    /**
     * Hook for call.
     * MsoProductAjax constructor.
     */
    public function __construct()
    {
        // Save Location data
        add_action('wp_ajax_mso_save_location', [$this, 'mso_save_location']);

        // Get location
        add_action('wp_ajax_mso_get_location_data', [$this, 'mso_get_location_data']);

        // Delete location
        add_action('wp_ajax_mso_delete_location', [$this, 'mso_delete_location']);
    }

    // Delete location row.
    public function mso_delete_location()
    {
        $postId = (isset($_POST['mso_location_id'])) ? sanitize_text_field($_POST['mso_location_id']) : 0;
        wp_delete_post($postId, true);
        var_dump($postId);
    }

    // Get location data.
    public function mso_get_location_data()
    {
        $product_detail_obj = new MsoProductDetail();
        echo _e($product_detail_obj->mso_location_post_meta());
        exit;
    }

    // Save location data.
    public function mso_save_location()
    {
        $post_type = 'mso_location';
        $location_data = (isset($_POST['mso_post_data'])) ? $_POST['mso_post_data'] : [];
        foreach ($location_data as $key => $location) {
            $form_data = [];
            parse_str($location, $form_data);

            if (isset($form_data['mso_location_id'], $form_data['mso_zip'], $form_data['mso_city'], $form_data['mso_state'], $form_data['mso_country'])) {
                $mso_location_id = $form_data['mso_location_id'];
                unset($form_data['mso_location_id']);
                $mso_location_merge = implode("", $form_data);

                if ($mso_location_id != 'new' && $mso_location_id > 0) {
                    // update
                    $wp_post = array("ID" => $mso_location_id, "post_title" => $form_data['mso_zip'], "post_content" => $mso_location_merge);
                    wp_update_post($wp_post, true);
                    update_post_meta($mso_location_id, $post_type, $form_data);
                } else {
                    // insert
                    $wp_post = array("post_title" => $form_data['mso_zip'],
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

        $product_detail_obj = new MsoProductDetail();
        echo _e($product_detail_obj->mso_location_post_meta());
        exit;
    }
}
