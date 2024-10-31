<?php

namespace MsoPackaging;

class MsoPackaging
{
    static public function mso_settings()
    {
        // Pallet solution
        $mso_pallet_option = get_option('mso_edppf');
        $mso_pallet_flag = $mso_pallet_check = '';
        if ($mso_pallet_option == 'yes') {
            $mso_pallet_check = ' checked="checked"';
        }
//        else {
//            $mso_pallet_flag = 'mso_disabled';
//        }

        // Packaging solution
        $mso_box_option = get_option('mso_edpf');
        $mso_box_flag = $mso_box_check = '';
        if ($mso_box_option == 'yes') {
            $mso_box_check = ' checked="checked"';
        }
//        else {
//            $mso_box_flag = 'mso_disabled';
//        }

        $status_description = '';
        $mso_pallet_plan = $mso_box_plan = 'mso_disabled';
        if (!MSO_DONT_AUTH) {
            if (MSO_PLAN_STATUS != 'success' || empty(MSO_SUBSCRIPTIONS)) {
                $mso_box_check = $mso_pallet_check = '';
                $status_description = '<span class="notice notice-error mso_err_status_description">' . MSO_PAID_PLAN_FEATURE . '</span>';
            }

            if (!empty(MSO_SUBSCRIPTIONS)) {
//                $mso_pallet_flag = $mso_box_flag = 'mso_disabled';
                $carriers = [];
                foreach (MSO_SUBSCRIPTIONS as $key => $subscription) {
                    $carrier_type = isset($subscription['type']) ? sanitize_text_field($subscription['type']) : '';
                    switch ($carrier_type) {
                        case 's':
                            $mso_box_plan = '';
                            break;
                        case 'f':
                            $mso_pallet_plan = '';
                            break;
                    }
                    $carriers[] = isset($subscription['carrier']) ? sanitize_text_field($subscription['carrier']) : '';
                }
//            echo !empty($carriers) ? "<span class='mso_pb_subs'>Pallets & Boxes will work for " . implode(", ", $carriers) . "</span>" : '';
//                echo !empty($carriers) ? "<div class='notice notice-success'><p><strong>Success!</strong> Please note that the following features are only available for your paid carriers, such as " . mso_implode_carriers($carriers) . ". These carriers will accept shipments of relative pallets or boxes.</p></div>" : '';
                echo !empty($carriers) ? "<div class='notice notice-success'><p><strong>Success!</strong> " . sprintf(MSO_PAID_PLAN_MESSAGE, mso_implode_carriers($carriers)) . "</p></div>" : '';
            }
        }

//        define('MSO_EDPC', $mso_edpc);
//        define('MSO_EDPPC', $mso_pallet_flag);

        ?>
        <!-- Pallet solution -->
        <h2 class="mso_carrier_title">Pallets</h2>
        <div class="mso_pallet_solution">
            <div class="mso_packaging_template">
                <h3><?php echo MSO_PALLET_DESC; ?></h3>
                <?php if ($mso_pallet_plan != 'mso_disabled') { ?>
                    <!--                    <b>Enable / Disable</b> &nbsp;&nbsp;-->
                    <div class="mso_switch">
                        <label>Enable / Disable</label>
                        <input data-main="mso_pallet_solution" class="mso_pp_solution" <?php echo $mso_pallet_check ?>
                               type="checkbox" id="mso_edppf">
                        <!--                        <span title="Enable / Disable" class="mso_slider mso_round"></span>-->
                    </div>
                <?php } ?>
                <?php echo $status_description; ?>
                <!--                <hr class="mso_hr">-->
                <div class="mso_packaging_error"></div>
                <div class="mso_packaging_post_meta <?php echo $mso_pallet_flag; ?>">
                    <?php echo self::mso_pallet_post_meta($mso_pallet_flag); ?>
                </div>
            </div>

            <?php if ($mso_pallet_plan != 'mso_disabled') { ?>
                <div class="mso_packaging_submit_btn">
                    <input type="submit" data-main="mso_pallet_solution"
                           class="mso_add_packaging button-primary <?php echo $mso_pallet_flag; ?>" name="submit"
                           value="+">
                    <input type="submit" data-plan="<?php echo $mso_pallet_flag; ?>" data-main="mso_pallet_solution"
                           class="mso_save_packaging button <?php echo $mso_pallet_flag; ?>" name="submit"
                           value="Submit">
                </div>
            <?php } ?>
        </div>

        <!-- Packaging solution -->
        <h2 class="mso_carrier_title">Boxes</h2>
        <div class="mso_packaging_solution">
            <div class="mso_packaging_template">
                <h3><?php echo MSO_BOXES_DESC; ?></h3>
                <?php if ($mso_box_plan != 'mso_disabled') { ?>
                    <div class="mso_switch">
                        <label>Enable / Disable</label>
                        <input data-main="mso_packaging_solution" class="mso_pp_solution" <?php echo $mso_box_check ?>
                               type="checkbox" id="mso_edpf">
<!--                        <span title="Enable / Disable" class="mso_slider mso_round"></span>-->
                    </div>
                <?php } ?>
                <?php echo $status_description; ?>
                <!--                <hr class="mso_hr">-->
                <div class="mso_packaging_error"></div>
                <div class="mso_packaging_post_meta">
                    <?php echo self::mso_packaging_post_meta($mso_box_flag); ?>
                </div>
            </div>

            <?php if ($mso_box_plan != 'mso_disabled') { ?>
                <div class="mso_packaging_submit_btn">
                    <input type="submit" data-main="mso_packaging_solution"
                           class="mso_add_packaging button-primary <?php echo $mso_box_flag; ?>"
                           name="submit" value="+">
                    <input type="submit" data-plan="<?php echo $mso_box_flag; ?>" data-main="mso_packaging_solution"
                           class="mso_save_packaging button <?php echo $mso_box_flag; ?>" name="submit" value="Submit">
                </div>
            <?php } ?>
        </div>

        <div class="mso_packaging_delete_warning_overly" style="display: none">
            <div class="mso_popup_overly_template">
                <!--                <a onclick="mso_packaging_delete_warning_overly_hide(event)" class="close">×</a>-->
                <div class="mso_package_delete_action">
                    <h3>Are you sure you want to delete the box?</h3>
                    <input type="submit" class="button" onclick="mso_packaging_delete_warning_overly_hide(event)"
                           name="submit" value="Cancel">
                    <input type="submit" class="mso_delete_packaging_done button-primary" name="submit" value="Ok">
                </div>
            </div>
        </div>

        <?php

    }

    // Pallets
    static public function mso_pallet_post_meta($is_disabled = '')
    {
        $settings = [
            'box_name' => [
                'title' => 'Pallet Name',
                'placeholder' => 'Pallet Name',
                'class' => '',
                'position' => 10,
//                'col' => 4,
            ],
            'inner_length' => [
                'title' => 'Length (in)',
                'placeholder' => 'Length (in)',
                'class' => '',
                'position' => 20,
//                'col' => 1,
            ],
            'inner_width' => [
                'title' => 'Width (in)',
                'placeholder' => 'Width (in)',
                'class' => '',
                'position' => 30,
//                'col' => 1,
            ],
            'inner_height' => [
                'title' => 'Max Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 40,
                'col' => 1,
            ],
            'pallet_height' => [
                'title' => 'Pallet Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 40,
//                'col' => 1,
            ],
            'box_weight' => [
                'title' => 'Pallet Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => 'mso_box_weight',
                'position' => 80,
//                'col' => 1,
            ],
            'max_weight' => [
                'title' => 'Max Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => '',
                'position' => 90,
//                'col' => 1,
            ]
        ];
        ?>
        <table border="1px solid">
            <tr class="row mso_packaging_th_row">
                <!-- Pallet available -->
                <th>Allowed</th>
                <?php foreach ($settings as $package_type => $package) {
                    $title = '';
                    extract($package);
                    echo '<th>' . $title . '</div>';
                } ?>
                <th>Delete</th>
            </tr>

            <?php
            $args = [
                'post_type' => 'mso_pallet',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order' => 'ASC'
            ];
            $posts_array = get_posts($args);
            (empty($posts_array)) ? $posts_array = [1] : '';
            ob_start();
            $post_count = 0;

            foreach ($posts_array as $post) {
                $mso_packaging_id = 'new';
                if (isset($post->ID)) {
                    $mso_packaging_id = $post->ID;
                    $get_post_meta = get_post_meta($post->ID, 'mso_pallet', true);
                    // Pallet available
                    $mso_pa = (isset($get_post_meta['mso_pallet_available'])) ? $get_post_meta['mso_pallet_available'] : 'off';
                    $mso_pa_extension = '';
                    if ($mso_pa == 'on') {
                        $mso_pa_extension = ' checked="checked"';
                    }
                }

                ?>
                <tr class="row mso_packaging_td_row">
                    <input type="hidden" class="mso_packaging_id" name="mso_packaging_id"
                           value="<?php echo esc_attr($mso_packaging_id); ?>">

                    <!-- Pallet available -->
                    <td>
                        <input type="checkbox" title="Allowed" class="<?php echo $is_disabled; ?>"
                               name="mso_pallet_available" <?php echo $mso_pa_extension; ?>>
                    </td>
                    <?php foreach ($settings as $package_type => $package) {
                        $title = $placeholder = $position = $class = $col = '';
                        extract($package);
                        $value = (isset($get_post_meta[$package_type])) ? $get_post_meta[$package_type] : '';
                        $alphanumeric = $package_type != 'box_name' ? 'data-numeric="1"' : '';
                        echo '<td><input type="text" name="' . esc_attr($package_type) . '" class="' . $is_disabled . '" title="' . esc_attr($title) . '" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr($value) . '" ' . $alphanumeric . ' ></td>';
                    } ?>

                    <td class="mso_delete_packaging">
                    <span class="dashicons dashicons-trash <?php echo $is_disabled; ?>"
                          onclick="mso_delete_packaging(this,event)"></span>
                    </td>
                </tr>

                <?php $post_count++;
            } ?>
        </table>
        <?php
    }

    // Packaging
    static public function mso_packaging_post_meta($is_disabled = '')
    {
        $settings = [
            'box_name' => [
                'title' => 'Box Name',
                'placeholder' => 'Box Name',
                'class' => '',
                'position' => 10,
//                'col' => 2,
            ],
            'inner_length' => [
                'title' => 'Inner Length (in)',
                'placeholder' => 'Length (in)',
                'class' => '',
                'position' => 20,
//                'col' => 1,
            ],
            'inner_width' => [
                'title' => 'Inner Width (in)',
                'placeholder' => 'Width (in)',
                'class' => '',
                'position' => 30,
//                'col' => 1,
            ],
            'inner_height' => [
                'title' => 'Inner Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 40,
//                'col' => 1,
            ],
            'outer_length' => [
                'title' => 'Outer Length (in)',
                'placeholder' => 'Length (in)',
                'class' => '',
                'position' => 50,
//                'col' => 1,
            ],
            'outer_width' => [
                'title' => 'Outer Width (in)',
                'placeholder' => 'Width (in)',
                'class' => '',
                'position' => 60,
//                'col' => 1,
            ],
            'outer_height' => [
                'title' => 'Outer Height (in)',
                'placeholder' => 'Height (in)',
                'class' => '',
                'position' => 70,
//                'col' => 1,
            ],
            'box_weight' => [
                'title' => 'Box Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => 'mso_box_weight',
                'position' => 80,
//                'col' => 1,
            ],
            'max_weight' => [
                'title' => 'Max Weight (lbs)',
                'placeholder' => 'Weight (lbs)',
                'class' => '',
                'position' => 90,
//                'col' => 1,
            ]
        ];
        ?>
        <table border="1px solid;">
            <tr class="row mso_packaging_th_row">
                <!-- Box available -->
                <th>Allowed</th>
                <?php foreach ($settings as $package_type => $package) {
                    $title = '';
                    extract($package);
                    echo '<th>' . $title . '</th>';
                } ?>
                <th>Delete</th>
            </tr>
            </form>

            <?php
            $args = [
                'post_type' => 'mso_packaging',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order' => 'ASC'
            ];
            $posts_array = get_posts($args);
            (empty($posts_array)) ? $posts_array = [1] : '';
            ob_start();
            $post_count = 0;

            foreach ($posts_array as $post) {
                $mso_packaging_id = 'new';
                if (isset($post->ID)) {
                    $mso_packaging_id = $post->ID;
                    $get_post_meta = get_post_meta($post->ID, 'mso_packaging', true);
                    // Box available
                    $mso_ba = (isset($get_post_meta['mso_box_available'])) ? $get_post_meta['mso_box_available'] : 'off';
                    $mso_ba_extension = '';
                    if ($mso_ba == 'on') {
                        $mso_ba_extension = ' checked="checked"';
                    }
                }

                ?>
                <tr class="row mso_packaging_td_row">
                    <input type="hidden" class="mso_packaging_id" name="mso_packaging_id"
                           value="<?php echo esc_attr($mso_packaging_id); ?>">

                    <!-- Box available -->
                    <td>
                        <input type="checkbox" title="Allowed" class="<?php echo $is_disabled; ?>"
                               name="mso_box_available" <?php echo $mso_ba_extension; ?>>
                    </td>
                    <?php foreach ($settings as $package_type => $package) {
                        $title = $placeholder = $position = $class = $col = '';
                        extract($package);
                        $value = (isset($get_post_meta[$package_type])) ? $get_post_meta[$package_type] : '';
                        $alphanumeric = $package_type != 'box_name' ? 'data-numeric="1"' : '';
                        echo '<td><input type="text" name="' . esc_attr($package_type) . '" class="' . $is_disabled . '" title="' . esc_attr($title) . '" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr($value) . '" ' . $alphanumeric . ' ></td>';
                    } ?>

                    <td class="mso_delete_packaging">
                            <span class="dashicons dashicons-trash <?php echo $is_disabled; ?>"
                                  onclick="mso_delete_packaging(this,event)"></span>
                    </td>
                </tr>

                <?php $post_count++;
            } ?>
        </table>
        <?php
    }
}