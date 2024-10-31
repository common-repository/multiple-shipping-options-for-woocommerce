<?php

/**
 * Product detail page.
 */

namespace MsoProductDetail;


use MsoPackage\MsoPackage;

/**
 * Add and show simple and variable products.
 * Class MsoProductDetail
 * @package MsoProductDetail
 */
class MsoProductDetail
{

    /**
     * Hook for call.
     * MsoProductDetail constructor.
     */
    public function __construct()
    {
        // Add simple product fields
        add_action('woocommerce_product_options_shipping', [$this, 'mso_show_product_fields'], 999, 3);
        add_action('woocommerce_process_product_meta', [$this, 'mso_save_product_fields'], 10, 1);

        // Add variable product fields.
        add_action('woocommerce_product_after_variable_attributes', [$this, 'mso_show_product_fields'], 999, 3);
        add_action('woocommerce_save_product_variation', [$this, 'mso_save_product_fields'], 10, 1);

        add_action('admin_footer', [$this, 'mso_popup_overly']);
    }

    // Get location array.
    public function mso_locations()
    {
        $shop_base_address = MsoPackage::mso_shop_base_address();
        $args = [
            'post_type' => 'mso_location',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'order' => 'ASC'
        ];
        $posts_array = get_posts($args);
        (empty($posts_array)) ? $posts_array = [1] : '';
        $mso_location = [];
        foreach ($posts_array as $post) {
            if (isset($post->ID)) {
                $mso_location_id = $post->ID;
                $mso_location[$mso_location_id] = get_post_meta($post->ID, 'mso_location', true);
            }
        }

        $mso_location['store_address'] = $shop_base_address;
        return $mso_location;
    }

    // Get location data.
    public function mso_location_post_meta()
    {
        ?>
            <table border="1px solid">
        <tr class="row mso_popup_location_row">
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Country</th>
        </tr>
        <!--        </tr>-->
        <?php
        $args = [
            'post_type' => 'mso_location',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'order' => 'ASC'
        ];
        $posts_array = get_posts($args);
        (empty($posts_array)) ? $posts_array = [1] : '';
        ob_start();
        $post_count = 0;
        foreach ($posts_array as $post) {
            $mso_location_id = 'new';
            $mso_address = $mso_zip = $mso_city = $mso_state = $mso_country = '';
            $get_post_meta = [];
            if (isset($post->ID)) {
                $mso_location_id = $post->ID;
                $get_post_meta = get_post_meta($post->ID, 'mso_location', true);
            }
            extract($get_post_meta);
            ?>
                <tr class="row mso_popup_location_row">
                    <input type="hidden" class="mso_location_id" name="mso_location_id"
                           value="<?php echo esc_attr($mso_location_id) ?>">
                    <td class="mso_input">
                        <input type="text" name="mso_address" class="form-control" data-alphanumeric="1" title="Address"
                               placeholder="Address" value="<?php echo esc_attr($mso_address) ?>">
                    </td>
                    <td class="mso_input">
                        <input type="text" name="mso_city" class="form-control" data-alphanumeric="1" title="City"
                               placeholder="City" value="<?php echo esc_attr($mso_city) ?>">
                    </td>
                    <td class="mso_input">
                        <input type="text" name="mso_state" class="form-control" data-alphanumeric="1" title="State"
                               placeholder="State" value="<?php echo esc_attr($mso_state) ?>">
                    </td>
                    <td class="mso_input">
                        <input type="text" name="mso_zip" class="form-control" data-alphanumeric="1" title="Zip"
                               placeholder="Zip" value="<?php echo esc_attr($mso_zip) ?>">
                    </td>
                    <td class="mso_input">
                        <input type="text" name="mso_country" class="form-control" data-alphanumeric="1"
                               title="Country" placeholder="Country" value="<?php echo esc_attr($mso_country) ?>">
                    </td>
                    <td class="mso_delete_location">
                        <span class="dashicons dashicons-trash" onclick="mso_delete_location(this,event)"></span>
                    </td>
                </tr>
            <?php
            $post_count++;
        }

        echo '</table>';
        return ob_get_clean();
    }

    // Load popup show to product page.
    public function mso_popup_overly()
    {
        if (wp_doing_ajax()) {
            return;
        }
        ?>
        <div class="mso_location_delete_warning_overly" style="display: none">
            <div class="mso_popup_overly_template">
<!--                <a onclick="mso_location_delete_warning_overly_hide()" class="close">×</a>-->
                <div class="mso_location_delete_action">
                            <h3>Are you sure you want to delete the location?</h3>
                            <input type="submit" class="button" onclick="mso_location_delete_warning_overly_hide()" name="submit" value="Cancel">
                            <input type="submit" class="mso_delete_location_done button-primary" name="submit" value="Ok">
                </div>
            </div>
        </div>
        <div class="mso_popup_location_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <a class="close">×</a>
                <h2>Origins</h2>
                <div class="mso_popup_overly_error"></div>
                <div class="mso_locations_list">
                    <?php echo $this->mso_location_post_meta(); ?>
                </div>
                <div class="mso_location_submit_btn">
                    <input type="submit" class="mso_add_location button-primary" name="submit"value="+">
                    <input type="submit" class="mso_save_location button"name="submit" value="Submit">
                </div>
<!--                <div class="bootstrap-iso form-wrp">-->
<!--                    <div class="row">-->
<!--                        <div class="mso_input col-md-6">-->
<!--                            <input type="submit" class="form-control mso_add_location mso_button" name="submit"-->
<!--                                   value="+">-->
<!--                        </div>-->
<!--                        <div class="mso_input col-md-6">-->
<!--                            <input type="submit" class="form-control mso_save_location button-primary mso_button"-->
<!--                                   name="submit" value="Submit">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
        <?php
    }

    /**
     * Show product fields in variation and simple product.
     * @param array $loop
     * @param array $variation_data
     * @param array $variation
     */
    public function mso_show_product_fields($loop, $variation_data = [], $variation = [])
    {
        $postId = (isset($variation->ID)) ? $variation->ID : get_the_ID();
        echo '<hr><h2 class="mso_carrier_title mso_shipping_product_data">>> Multiple Shipping Options for WooCommerce</h2>';
        $this->mso_custom_product_fields($postId);
    }

    /**
     * Save the simple product fields.
     * @param int $postId
     */
    public function mso_save_product_fields($postId)
    {
        if (isset($postId) && $postId > 0) {
            $mso_product_fields = $this->mso_product_fields_arr();

            foreach ($mso_product_fields as $key => $custom_field) {
                $custom_field = (isset($custom_field['id'])) ? $custom_field['id'] : '';
                $mso_updated_product = (isset($_POST[$custom_field][$postId])) ? $_POST[$custom_field][$postId] : '';
                if ($custom_field == 'mso_product_locations') {
                    $mso_updated_product = (isset($_POST[$custom_field], $_POST[$custom_field][$postId])) ? $_POST[$custom_field][$postId] : '';
                }
                update_post_meta($postId, $custom_field, $mso_updated_product);
            }
        }
    }

    /**
     * Product Fields Array
     * @return array
     */
    public function mso_product_fields_arr()
    {
        $mso_locations = $this->mso_locations();
        $set_locations = [];
        foreach ($mso_locations as $location_id => $location) {
            $mso_city = $mso_state = $mso_zip = $mso_country = '';
            extract($location);
            $set_locations[$location_id] = "$mso_city, $mso_state, $mso_zip, $mso_country";
        }

//        $status_direction = '';
//        $status_description = 'Edit Origins';
//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || empty(MSO_SUBSCRIPTIONS)) {
//                $status_direction = 'mso_disabled';
//                $mso_plan_desc = '<a href="https://minilogics.com">Upgrade to access premium features by visiting our website, Mini Logics, and creating a subscription.</a>';
//                $status_description = '<span id="mso_err_product_page_description">' . $mso_plan_desc . '</span>';
//            }
//        }

        $mso_product_fields = [
//            [
//                'type' => 'checkbox',
//                'id' => 'mso_enable_product_setting',
//                'class' => 'mso_enable_product_setting',
//                'label' => 'Multiple Shipping Options for WooCommerce',
//            ],
            [
                'type' => 'dropdown',
                'id' => 'mso_product_locations',
                'class' => 'mso_product_setting',
                'name' => 'mso_product_locations',
                'label' => 'Origins',
                'options' => $set_locations,
                'description' => 'Edit Origins'
            ]
        ];

        // We can use hook for add new product field from other plugin add-on
        $mso_product_fields = apply_filters('mso_add_product', $mso_product_fields);

        return $mso_product_fields;
    }

    /**
     * Dynamic checkbox field show on product detail page
     * @param array $field
     */
    public function woocommerce_wp_select_multiple($field)
    {
        global $thepostid, $post;

        $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
        $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
        $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
        $field['value'] = isset($field['value']) ? $field['value'] : array();

        echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['name']) . '" class="' . esc_attr($field['class']) . '" multiple="multiple">';

        foreach ($field['options'] as $key => $value) {
            echo '<option value="' . esc_attr($key) . '" ' . (in_array($key, $field['value']) ? 'selected="selected"' : '') . '>' . esc_html($value) . '</option>';
        }

        echo '</select> ';

        if (!empty($field['description'])) {
            if (isset($field['desc_tip']) && false !== $field['desc_tip']) {
                echo '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />';
            } else {
                echo '<span class="description">' . wp_kses_post($field['description']) . '</span>';
            }
        }
        echo '</p>';
    }

    /**
     * Show Product Fields
     * @param int $postId
     */
    public function mso_custom_product_fields($postId)
    {
        $mso_product_fields = $this->mso_product_fields_arr();

        foreach ($mso_product_fields as $key => $custom_field) {
            $mso_field_type = (isset($custom_field['type'])) ? $custom_field['type'] : '';
            $mso_action_function_name = 'mso_product_' . $mso_field_type;

            if (method_exists(__CLASS__, $mso_action_function_name)) {
                $this->$mso_action_function_name($custom_field, $postId);
            }
        }
    }

    /**
     * Dynamic checkbox field show on product detail page
     * @param array $custom_field
     * @param int $postId
     */
    public function mso_product_checkbox($custom_field, $postId)
    {
        $custom_checkbox_field = [
            'id' => $custom_field['id'] . '[' . $postId . ']',
            'value' => get_post_meta($postId, $custom_field['id'], true),
            'label' => $custom_field['label'],
            'class' => $custom_field['class'],
        ];

        if (isset($custom_field['description'])) {
            $custom_checkbox_field['description'] = $custom_field['description'];
        }

        woocommerce_wp_checkbox($custom_checkbox_field);
    }

    /**
     * Dynamic dropdown field show on product detail page
     * @param array $custom_field
     * @param int $postId
     */
    public function mso_product_dropdown($custom_field, $postId)
    {
        $custom_dropdown_field = [
            'id' => $custom_field['id'] . '[' . $postId . ']',
            'label' => $custom_field['label'],
            'class' => $custom_field['class'],
            'value' => get_post_meta($postId, $custom_field['id'], true),
            'options' => $custom_field['options']
        ];

        if (isset($custom_field['description'])) {
            $custom_dropdown_field['description'] = $custom_field['description'];
        }

        woocommerce_wp_select($custom_dropdown_field);
    }

    /**
     * Dynamic multi select dropdown field show on product detail page
     * @param array $custom_field
     * @param int $postId
     */
    public function mso_product_multi_select_dropdown($custom_field, $postId)
    {
        $custom_dropdown_field = [
            'id' => $custom_field['id'],
            'label' => $custom_field['label'],
            'class' => $custom_field['class'],
            'value' => get_post_meta($postId, $custom_field['id'], true),
            'options' => $custom_field['options'],
            'name' => $custom_field['name'],
            'description' => $custom_field['description'],
        ];

        $this->woocommerce_wp_select_multiple($custom_dropdown_field);
    }

    /**
     * Dynamic input field show on product detail page
     * @param array $custom_field
     * @param int $postId
     */
    public function mso_product_input_field($custom_field, $postId)
    {
        $custom_input_field = [
            'id' => $custom_field['id'] . '[' . $postId . ']',
            'label' => $custom_field['label'],
            'class' => $custom_field['class'],
            'placeholder' => $custom_field['label'],
            'value' => get_post_meta($postId, $custom_field['id'], true)
        ];

        if (isset($custom_field['description'])) {
            $custom_input_field['desc_tip'] = true;
            $custom_input_field['description'] = $custom_field['description'];
        }

        woocommerce_wp_text_input($custom_input_field);
    }
}
