<?php

namespace MsoPackaging;

class MsoPackaging
{
    /**
     * Display and manage the settings for Pallet and Packaging solutions.
     */
    static public function mso_settings()
    {
        // Pallet solution
        $mso_pallet_option = get_option('mso_edppf');
        $mso_pallet_flag = '';
        $mso_pallet_check = ($mso_pallet_option == 'yes') ? ' checked="checked"' : '';

        // Packaging solution
        $mso_box_option = get_option('mso_edpf');
        $mso_box_flag = '';
        $mso_box_check = ($mso_box_option == 'yes') ? ' checked="checked"' : '';

//        $status_description = '';
//        $mso_pallet_plan = $mso_box_plan = 'mso_disabled';

//        if (!MSO_DONT_AUTH) {
//            if (MSO_PLAN_STATUS != 'success' || empty(MSO_SUBSCRIPTIONS)) {
//                $mso_box_check = $mso_pallet_check = '';
//                $status_description = '<span class="notice notice-error mso_err_status_description">' . MSO_PAID_PLAN_FEATURE . '</span>';
//            }
//
//            if (!empty(MSO_SUBSCRIPTIONS)) {
//                $carriers = [];
//                foreach (MSO_SUBSCRIPTIONS as $key => $subscription) {
//                    $carrier_type = isset($subscription['type']) ? sanitize_text_field($subscription['type']) : '';
//                    switch ($carrier_type) {
//                        case 's':
//                            $mso_box_plan = '';
//                            break;
//                        case 'f':
//                            $mso_pallet_plan = '';
//                            break;
//                    }
//                    $carriers[] = isset($subscription['carrier']) ? sanitize_text_field($subscription['carrier']) : '';
//                }
//                echo !empty($carriers) ? "<div class='notice notice-success'><p><strong>Success!</strong> " . sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers)) . "</p></div>" : '';
//            }
//        } elseif (MSO_DONT_AUTH) {
//            $mso_pallet_plan = $mso_box_plan = '';
//        }

        ?>

        <div class="mso_pallets_boxes_page">

            <?php echo mso_get_carriers_plan_status(); ?>

            <!-- Pallet solution -->
            <h2 class="mso_carrier_title">Pallets</h2>
            <div class="mso_pallet_solution">
                <div class="mso_packaging_template">
                    <h3><?php echo MSO_PALLET_DESC; ?></h3>
                    <!--                    --><?php //if ($mso_pallet_plan != 'mso_disabled') {
                    ?>
                    <div class="mso_packaging_error"></div>
                    <div class="mso_switch">
                        <label>Enable / Disable</label>
                        <input data-main="mso_pallet_solution"
                               class="mso_pp_solution" <?php echo $mso_pallet_check ?>
                               type="checkbox" id="mso_edppf">
                    </div>
                    <!--                    --><?php //}
                    ?>
                    <!--                    --><?php //echo $status_description;
                    ?>
                    <div class="mso_packaging_post_meta <?php echo $mso_pallet_flag; ?>">
                        <?php echo self::mso_pallet_post_meta($mso_pallet_flag); ?>
                    </div>
                </div>

                <!--                --><?php //if ($mso_pallet_plan != 'mso_disabled') {
                ?>
                <div class="mso_packaging_submit_btn">
                    <input type="submit" data-main="mso_pallet_solution"
                           class="mso_add_packaging button-primary <?php echo $mso_pallet_flag; ?>" name="submit"
                           value="+">
                    <input type="submit" data-plan="<?php echo $mso_pallet_flag; ?>" data-main="mso_pallet_solution"
                           class="mso_save_packaging button <?php echo $mso_pallet_flag; ?>" name="submit"
                           value="Submit">
                </div>
                <!--                --><?php //}
                ?>
            </div>

            <!-- Packaging solution -->
            <h2 class="mso_carrier_title">Boxes</h2>
            <div class="mso_packaging_solution">
                <div class="mso_packaging_template">
                    <h3><?php echo MSO_BOXES_DESC; ?></h3>
                    <!--                    --><?php //if ($mso_box_plan != 'mso_disabled') {
                    ?>
                    <div class="mso_packaging_error"></div>
                    <div class="mso_switch">
                        <label>Enable / Disable</label>
                        <input data-main="mso_packaging_solution"
                               class="mso_pp_solution" <?php echo $mso_box_check ?>
                               type="checkbox" id="mso_edpf">
                    </div>
                    <!--                    --><?php //}
                    ?>
                    <!--                    --><?php //echo $status_description;
                    ?>
                    <div class="mso_packaging_post_meta">
                        <?php echo self::mso_packaging_post_meta($mso_box_flag); ?>
                    </div>
                </div>

                <!--                --><?php //if ($mso_box_plan != 'mso_disabled') {
                ?>
                <div class="mso_packaging_submit_btn">
                    <input type="submit" data-main="mso_packaging_solution"
                           class="mso_add_packaging button-primary <?php echo $mso_box_flag; ?>"
                           name="submit" value="+">
                    <input type="submit" data-plan="<?php echo $mso_box_flag; ?>" data-main="mso_packaging_solution"
                           class="mso_save_packaging button <?php echo $mso_box_flag; ?>" name="submit"
                           value="Submit">
                </div>
                <!--                --><?php //}
                ?>
            </div>
        </div>

        <div class="mso_packaging_delete_warning_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <div class="mso_package_delete_action">
                    <div class="mso_delete_message">
                        <p>Are you sure you want to delete the box?</p>
                    </div>
                    <div class="mso_warning_buttons">
                        <input type="submit" class="button" onclick="mso_packaging_delete_warning_overly_hide(event)"
                               name="submit" value="Cancel">
                        <input type="submit" class="mso_delete_packaging_done button-primary" name="submit" value="Yes,
                            delete it">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Generate HTML markup for pallet settings.
     *
     * @param string $is_disabled Class for disabled elements.
     */
    static public function mso_pallet_post_meta($is_disabled = '')
    {
        $settings = [
            'box_name' => [
                'title' => 'Pallet Name',
                'placeholder' => 'Pallet Name',
                'class' => '',
                'position' => 10
            ],
            'inner_length' => [
                'title' => 'Length (in)',
                'placeholder' => 'Length (in)',
                'class' => '',
                'position' => 20
            ],
            'inner_width' => [
                'title' => 'Width (in)',
                'placeholder' => 'Width (in)',
                'class' => '',
                'position' => 30
            ],
            'inner_height' => [
                'title' => 'Max Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 40
            ],
            'pallet_height' => [
                'title' => 'Pallet Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 40
            ],
            'box_weight' => [
                'title' => 'Pallet Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => 'mso_box_weight',
                'position' => 80
            ],
            'max_weight' => [
                'title' => 'Max Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => '',
                'position' => 90
            ]
        ];
        ?>
        <table border="1px solid">
            <tr class="row mso_packaging_th_row">
                <!-- Pallet available column -->
                <th>Allowed</th>
                <?php
                // Loop through each pallet setting and generate table headers.
                foreach ($settings as $package_type => $package) {
                    $title = '';
                    extract($package);
                    echo '<th>' . $title . '</th>';
                }
                ?>
                <th>Delete</th>
            </tr>

            <?php
            // Query existing pallet posts.
            $args = [
                'post_type' => 'mso_pallet',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order' => 'ASC'
            ];
            $posts_array = get_posts($args);
            (empty($posts_array)) ? $posts_array = [1] : '';
            ob_start();

            // Loop through each pallet post.
            foreach ($posts_array as $post) {
                $mso_packaging_id = 'new';
                $mso_pa_extension = '';
                if (isset($post->ID)) {
                    $mso_packaging_id = $post->ID;
                    $get_post_meta = get_post_meta($post->ID, 'mso_pallet', true);
                    // Get pallet availability and set extension for checkbox.
                    $mso_pa = (isset($get_post_meta['mso_pallet_available'])) ? $get_post_meta['mso_pallet_available'] : 'off';
                    $mso_pa_extension = ($mso_pa == 'on') ? ' checked="checked"' : '';
                }

                // Start generating the table row.
                ?>
                <tr class="row mso_packaging_td_row">
                    <input type="hidden" class="mso_packaging_id" name="mso_packaging_id"
                           value="<?php echo esc_attr($mso_packaging_id); ?>">

                    <!-- Pallet available column -->
                    <td>
                        <input type="checkbox" title="Allowed" class="<?php echo $is_disabled; ?>"
                               name="mso_pallet_available" <?php echo $mso_pa_extension; ?>>
                    </td>
                    <?php
                    // Loop through each setting and generate input elements.
                    foreach ($settings as $package_type => $package) {
                        $title = $placeholder = $position = $class = $col = '';
                        extract($package);
                        $value = (isset($get_post_meta[$package_type])) ? $get_post_meta[$package_type] : '';
                        $alphanumeric = ($package_type != 'box_name') ? 'data-numeric="1"' : '';
                        echo '<td><input type="text" name="' . esc_attr($package_type) . '" class="' . $is_disabled . '" title="' . esc_attr($title) . '" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr($value) . '" ' . $alphanumeric . ' ></td>';
                    }
                    ?>

                    <!-- Delete column -->
                    <td class="mso_delete_packaging">
                    <span class="dashicons dashicons-trash <?php echo $is_disabled; ?>"
                          onclick="mso_delete_packaging(this,event, 'pallet')"></span>
                    </td>
                </tr>

            <?php } ?>
        </table>
        <?php
    }

    /**
     * Generate HTML markup for packaging settings.
     *
     * @param string $is_disabled Class for disabled elements.
     */
    static public function mso_packaging_post_meta($is_disabled = '')
    {
        $settings = [
            'box_type' => [
                'title' => 'Box Type',
                'placeholder' => 'Box Type',
                'class' => '',
                'position' => 11
            ],
            'box_name' => [
                'title' => 'Box Name',
                'placeholder' => 'Box Name',
                'class' => '',
                'position' => 10
            ],
            'inner_length' => [
                'title' => 'Inner Length (in)',
                'placeholder' => 'Length (in)',
                'class' => '',
                'position' => 20
            ],
            'inner_width' => [
                'title' => 'Inner Width (in)',
                'placeholder' => 'Width (in)',
                'class' => '',
                'position' => 30
            ],
            'inner_height' => [
                'title' => 'Inner Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 40
            ],
            'outer_length' => [
                'title' => 'Outer Length (in)',
                'placeholder' => 'Length (in)',
                'class' => '',
                'position' => 50
            ],
            'outer_width' => [
                'title' => 'Outer Width (in)',
                'placeholder' => 'Width (in)',
                'class' => '',
                'position' => 60
            ],
            'outer_height' => [
                'title' => 'Outer Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 70
            ],
            'box_weight' => [
                'title' => 'Box Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => 'mso_box_weight',
                'position' => 80
            ],
            'max_weight' => [
                'title' => 'Max Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => '',
                'position' => 90
            ]
        ];
        ?>
        <table border="1px solid;">
            <tr class="row mso_packaging_th_row">
                <!-- Box available column -->
                <th>Allowed</th>
                <?php
                // Loop through each package setting and generate table headers.
                foreach ($settings as $package_type => $package) {
                    $title = '';
                    extract($package);
                    echo '<th>' . $title . '</th>';
                }
                ?>
                <th>Delete</th>
            </tr>
            </form>

            <?php
            // Query existing packaging posts.
            $args = [
                'post_type' => 'mso_packaging',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order' => 'ASC'
            ];
            $posts_array = get_posts($args);
            (empty($posts_array)) ? $posts_array = [1] : '';
            ob_start();

            // Loop through each packaging post.
            foreach ($posts_array as $post) {
                $mso_packaging_id = 'new';
                $mso_ba_extension = '';
                if (isset($post->ID)) {
                    $mso_packaging_id = $post->ID;
                    $get_post_meta = get_post_meta($post->ID, 'mso_packaging', true);
                    // Get box availability and set extension for checkbox.
                    $mso_ba = (isset($get_post_meta['mso_box_available'])) ? $get_post_meta['mso_box_available'] : 'off';
                    $mso_ba_extension = ($mso_ba == 'on') ? ' checked="checked"' : '';
                }

                // Start generating the table row.
                ?>
                <tr class="row mso_packaging_td_row">
                    <input type="hidden" class="mso_packaging_id" name="mso_packaging_id"
                           value="<?php echo esc_attr($mso_packaging_id); ?>">

                    <!-- Box available column -->
                    <td>
                        <input type="checkbox" title="Allowed" class="<?php echo $is_disabled; ?>"
                               name="mso_box_available" <?php echo $mso_ba_extension; ?>>
                    </td>
                    <?php
                    // Loop through each setting and generate input/select elements.
                    foreach ($settings as $package_type => $package) {
                        $title = $placeholder = $position = $class = $col = '';
                        extract($package);
                        $value = (isset($get_post_meta[$package_type])) ? $get_post_meta[$package_type] : '';
                        $alphanumeric = ($package_type != 'box_name') ? 'data-numeric="1"' : '';
                        if ($package_type == 'box_type') {
                            echo '<td><select name="' . esc_attr($package_type) . '" class="' . $is_disabled . '" title="' . esc_attr($title) . '">' . self::carriers_boxes($value) . '</select></td>';
                        } else {
                            echo '<td><input type="text" name="' . esc_attr($package_type) . '" class="' . $is_disabled . '" title="' . esc_attr($title) . '" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr($value) . '" ' . $alphanumeric . ' ></td>';
                        }
                    }
                    ?>

                    <!-- Delete column -->
                    <td class="mso_delete_packaging">
                        <span class="dashicons dashicons-trash <?php echo $is_disabled; ?>"
                              onclick="mso_delete_packaging(this,event, 'box')"></span>
                    </td>
                </tr>

                <?php
            }
            ?>
        </table>
        <?php
    }

    /**
     * Generate HTML options for carrier boxes selection based on provided value.
     *
     * @param string $value The current selected value.
     * @return string HTML options for carrier boxes.
     */
    static public function carriers_boxes($value)
    {
        $options = [
            'default' => 'Default',
            'EXPRESS' => 'Priority Mail Express Box',
            'VARIABLE' => 'Priority Mail Box',
            'LG FLAT RATE BOX' => 'Priority Mail Large Flat Rate Box',
            'MD FLAT RATE BOX' => 'Priority Mail Medium Flat Rate Box',
            'SM FLAT RATE BOX' => 'Priority Mail Small Flat Rate Box',
            'PADDED FLAT RATE ENVELOPE' => 'Priority Mail Padded Flat Rate Envelope'
        ];

        $carriers_boxes = '';

//        if (MSO_DONT_AUTH || (MSO_PLAN_STATUS == 'success' && (!empty(MSO_SUBSCRIPTIONS) && isset(MSO_SUBSCRIPTIONS[MSO_USPS_GET])))) {
        foreach ($options as $option_value => $option_label) {
            $selected = ($value == $option_value) ? 'selected="selected"' : '';
            $carriers_boxes .= '<option ' . $selected . ' value="' . $option_value . '">' . $option_label . '</option>';
        }
//        }

        return $carriers_boxes;
    }

}