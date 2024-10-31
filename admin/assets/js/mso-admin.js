var msoobj = '';

jQuery(document).ready(function () {

    // Print label dialog
    jQuery.fn.extend({
        mso_print: function () {
            var frameName = 'printIframe';
            var doc = window.frames[frameName];
            if (!doc) {
                jQuery('<iframe>').hide().attr('name', frameName).appendTo(document.body);
                doc = window.frames[frameName];
            }
            doc.document.body.innerHTML = this.html();
            doc.window.print();
            return this;
        }
    });

    // When plan is not enabled
    // jQuery('textarea.mso_disabled').attr('readonly', 'readonly');
    // jQuery('input[type="number"].mso_disabled').attr('readonly', 'readonly');
    // jQuery('input[type="text"].mso_disabled').attr('readonly', 'readonly');
    jQuery('textarea.mso_disabled,input[type="number"].mso_disabled,input[type="text"].mso_disabled').attr('readonly', 'readonly');
    jQuery('select.mso_disabled').prop("disabled", true);
    jQuery('input[type="checkbox"].mso_disabled').prop('checked', false);
    jQuery('input[type="checkbox"].mso_disabled,input[type="radio"].mso_disabled,textarea.mso_disabled,input[type="number"].mso_disabled,input[type="text"].mso_disabled,select.mso_disabled').on('click', function (ev) {
        ev.preventDefault();
        jQuery('.mso_c_wrapper').remove();
        jQuery(this).after('<span class="mso_c_wrapper"><span class="mso_tooltip" style="opacity: 1;">' + mso_script.mso_paid_plan_feature + '</span></span>');
    });

    jQuery(document).on('click', function (e) {
        if (jQuery(e.target).closest(".mso_disabled").length === 0) {
            jQuery('.mso_c_wrapper').remove();
        }
    });

    // Error handling
    // jQuery(".mso_disabled").on({
    //     mouseenter: function () {
    //         jQuery(this).after('<span class="mso_c_wrapper"><span class="mso_tooltip" style="opacity: 1;">' + mso_script.mso_paid_plan_feature + '</span></span>');
    //     },
    //     mouseleave: function () {
    //         jQuery('.mso_c_wrapper').remove();
    //     }
    // });

    // if (jQuery('.mso_order_page_disabled').length) {
    //     mso_display_li_option('msoorc');
    //     jQuery('.mso_order_id').closest('.inside').addClass('mso_disabled');
    // }

    jQuery('.mso_order_main_tab li').on('click', function () {
        jQuery(this).closest('form.mso_flex_template').find('div.mso_order_tab').css("display", "none");
        jQuery(this).closest('.mso_order_main_tab').find('li').removeClass('mso_order_tab_active');
        jQuery(this).addClass('mso_order_tab_active');
        var data_tab = jQuery(this).attr('data-tab');
        jQuery(this).closest('form.mso_flex_template').find('div.' + data_tab).css("display", "block");
        console.log(data_tab);
    });

    // Unset for shipment button
    jQuery("input[name='mso_residential'],input[name='mso_liftgate'],span.mso_item_quantity input[type='number']").on('change', function () {
        jQuery('button.mso_order_ship').remove();
    });

    // Order sortable
    if (jQuery(".mso_items_sortable").length) {
        jQuery(".mso_items_sortable").sortable({
            connectWith: ".mso_items_sortable"
        });
    }

    // jQuery('select.mso_order_shipment_origin').next('.description').after('&nbsp&nbsp&nbsp<input onclick="mso_order_shipment_enable_disable(this,event)" type="checkbox" checked="checked">&nbsp<span style="font-weight: 500; font-size: 13px; padding-top: 2px">Enable / Disable</span>');

    // At a time one option would be selected.
    jQuery('.mso_cheapest_single_rate').on('change', function () {
        jQuery('.mso_cheapest_single_rate').not(this).prop('checked', false);
    });

    if (jQuery(".mso_items_sortable").length) {
        jQuery(".mso_items_sortable").disableSelection();
    }

    // When click  Enable / Disable on packaging page.
    jQuery('.mso_pp_solution').on('click', function (event) {
        var mso_main = jQuery(this).attr('data-main');
        var parent_div = 'div.' + mso_main + ' .mso_packaging_submit_btn, div.' + mso_main + ' .mso_packaging_post_meta';
        var mso_cid = jQuery(this).attr('id');
        var mso_current_action = 'no';
        if (jQuery(this).is(':checked')) {
            mso_current_action = 'yes';
        }
        // jQuery(parent_div).removeClass('mso_disabled');
        // jQuery(parent_div).find('input').removeClass('mso_disabled');
        // jQuery(parent_div).find('span').removeClass('mso_disabled');
        // } else {
        //     jQuery(parent_div).addClass('mso_disabled');
        //     jQuery(parent_div).find('input').addClass('mso_disabled');
        //     jQuery(parent_div).find('span').addClass('mso_disabled');
        // }

        let params = {
            'action': 'mso_edpa',
            'mso_current_action': mso_current_action,
            'mso_cid': mso_cid,
            'loader_id': '#mso_edp',
        };

        mso_ajax_request(params, mso_edp);
    });

    // Set ship to address to shipping options section
    jQuery('.mso_ship_to_address_selection td input').on('click', function () {
        let mso_ship_to_address = jQuery(this).closest('td').find('span.description').text();
        jQuery('tr.mso_order_destination td p.description').text(mso_ship_to_address);
    });

    jQuery('.mso_optional').attr('data-optional', 1);

    // jQuery('input.mso_disabled').click(function (event) {
    //     event.stopPropagation();
    // });

    // Select All Domestic FedEx
    mso_select_all_domestic_fedex();

    // Select All International FedEx
    mso_select_all_international_fedex();

    // Select All Domestic UPS
    mso_select_all_domestic_ups();

    // Select All International UPS
    mso_select_all_international_ups();

    jQuery('.mso_pk').closest('tr').addClass("mso_pk");
    jQuery('.mso_license_api_status').closest('tr').addClass("mso_license_api_status");
    jQuery('.mso_services_sa').closest('tr').addClass("mso_services_sa");
    jQuery('.mso_carrier_settings_on_off').closest('tr').addClass("mso_carrier_settings_on_off_tr");
    jQuery('.mso_carrier_plan_status').closest('tr').addClass("mso_carrier_plan_status");
    jQuery('.mso_carrier_partition').closest('tr').addClass("mso_carrier_partition");
    jQuery('.mso_carrier_partition_64').closest('tr').addClass("mso_carrier_partition_64");
    jQuery('.mso_api_credentials_status').closest('tr').addClass("mso_api_credentials_status_tr");
    jQuery('.mso_carrier_id').closest('tr').addClass("mso_carrier_id");
    jQuery('.mso_child_carrier').closest('tr').addClass("mso_child_carrier");
    jQuery('.mso_asteric').closest('tr').addClass("mso_asteric");
    jQuery('.mso_origin_description').closest('tr').addClass("mso_origin_description");
    jQuery('.mso_shipping_settings_heading').closest('tr').addClass("mso_shipping_settings_heading");
    jQuery('.mso_no_shipping_option_error_message').closest('tr').addClass("mso_no_shipping_option_error_message");
    jQuery('.mso_no_shipping_option_custom_rate').closest('tr').addClass("mso_no_shipping_option_custom_rate");
    jQuery('.mso_connection').closest('table').prev('h2').addClass("mso_carrier_title");

    jQuery('.mso_api_credentials_status').closest('tr').find('th label').addClass('button-primary mso_api_credentials_status_btn');

    if (jQuery('.mso_carrier_id').length) {
        jQuery('body').after('<div class="mso_save_settings"><div class="mso_rounded_btn">Save</div></div>');
        jQuery('.mso_save_settings').on('click', function () {
            jQuery('button.woocommerce-save-button').click();
        });
    }

    mso_no_shipping_cost_options(jQuery('input[name="mso_no_shipping_cost_options"]:checked').val());
    jQuery("input[name='mso_no_shipping_cost_options']").on('change', function () {
        var mso_no_shipping_cost_option = jQuery(this).val();
        mso_no_shipping_cost_options(mso_no_shipping_cost_option);
    });

    // Slide show/hide on settings page
    jQuery('.mso_carrier_title').next().slideToggle();
    jQuery('.mso_carrier_title').on('click', function () {
        jQuery(this).next().slideToggle();
    });

    // Slide show/hide on order page
    jQuery('.mso_order_shipment').next().slideToggle();
    jQuery('.mso_order_shipment').on('click', function () {
        jQuery(this).next().slideToggle();
    });

    // Save Changes button next to test license button
    // jQuery('tr.mso_license_api_status th label').after('<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save MSO Key</button>');

    // Delete shipment
    jQuery('.mso_cancel_shipment').on('click', function () {
        msoobj = this;
        var mso_ship_num = jQuery(this).closest('.mso_flex_template').prev('.mso_order_shipment').data('mso_ship_num');
        var carrier = jQuery(this).attr('data-carrier');
        var post_data = jQuery(this).attr('data-post_data');
        var mso_order_id = jQuery('.mso_order_id').attr('value');
        jQuery('.mso_order_shipment_delete_warning_overly').css({'opacity': 1, 'display': 'block'});
        event.preventDefault();
        jQuery('.mso_delete_shipment_done').on('click', function () {
            let params = {
                'action': 'mso_cancel_shipment_hook',
                'mso_order_id': mso_order_id,
                'mso_carrier': carrier,
                'mso_post_data': post_data,
                'mso_ship_num': mso_ship_num,
                'loader_id': '#mso_order'
            };

            mso_ajax_request(params, mso_cancel_shipment_done);
        });
    });

    // Print all shipment
    jQuery('.mso_print_all_shipment').on('click', function () {
        let labels = jQuery(this).attr('data-post_data');
        console.log(labels.split(","));
        printJS({
            // printable: get_object_string(labels),
            printable: labels.split(","),
            type: 'image',
            // header: 'MiniLogics',
            showModal: true,
            modalMessage: "Label Loading...",
            documentTitle: 'LABEL',
            imageStyle: 'width: 85%;display: block;page-break-after: always;margin: auto;'
        });
    });

    jQuery('.mso_create_shipment_block').on('click', function () {
        var mso_order_id = jQuery('.mso_order_id').attr('value');

        let params = {
            'action': 'mso_new_shipment',
            'mso_order_id': mso_order_id,
            'loader_id': '#mso_order',
        };

        mso_ajax_request(params, mso_new_shipment);
    });

    jQuery('select.mso_product_setting,select[name=mso_order_shipment_origin]').next('span.description').on('click', function (event) {
        // if (jQuery("#mso_err_product_page_description").length == 0) {
        event.preventDefault();
        jQuery('.mso_popup_overly_error').html('');

        let params = {
            'action': 'mso_get_location_data',
        };
        mso_ajax_request(params, mso_get_location_data);

        jQuery('.mso_popup_location_overly').css({'opacity': 1, 'display': 'block'});
        // }
    });

    // hide popup on product page for locations
    jQuery('.mso_popup_location_overly .mso_popup_overly_template .close').on('click', function () {
        jQuery('.mso_popup_location_overly').css({'opacity': 0, 'display': 'none'});
    });

    // Product settings
    jQuery('.mso_enable_product_setting').on('click', function () {
        mso_enable_product_setting();
    });

    // Save location on product settings
    jQuery('.mso_save_location').on('click', function () {
        let mso_location_data_invalid = true;
        let mso_location_data = {};
        let mso_location_count = 0;
        jQuery('.mso_popup_overly_error').html('');
        // jQuery('.mso_popup_location_row').each(function (ind, template) {
        jQuery('.mso_popup_location_overly').last().find('tr.mso_popup_location_row').each(function (ind, template) {
            if (mso_validate_input(template) == false) {
                mso_location_data_invalid = false;
            }

            mso_location_data[mso_location_count] = jQuery(template).find('input').serialize();
            mso_location_count++;
        });

        !mso_location_data_invalid ? jQuery('.mso_popup_overly_error').append('<div class="error"><p>Form data should not be empty and should be valid.</p></div>') : '';

        if (mso_location_data_invalid) {

            jQuery('.mso_save_location, .mso_add_location').attr("disabled", true);

            let params = {
                'action': 'mso_save_location',
                'mso_post_data': mso_location_data,
                'loader_id': '.mso_popup_location_overly',
            };

            mso_ajax_request(params, mso_save_location);
        }

    });

    // Save packaging on product settings
    jQuery('.mso_save_packaging').on('click', function (event) {
        event.preventDefault();
        var mso_maindiv = jQuery(this).attr('data-main');
        var mso_mainplan = jQuery(this).attr('data-plan');
        let mso_packaging_data_invalid = true;
        let mso_packaging_data = {};
        let mso_packaging_count = 0;
        jQuery('div.' + mso_maindiv + ' .mso_packaging_error').html('');
        jQuery('div.' + mso_maindiv + ' .mso_packaging_td_row').each(function (ind, template) {
            if (mso_validate_input(template) == false) {
                mso_packaging_data_invalid = false;
            }

            mso_packaging_data[mso_packaging_count] = jQuery(template).find('input').serialize();
            mso_packaging_count++;
        });

        !mso_packaging_data_invalid ? jQuery('div.' + mso_maindiv + ' .mso_packaging_error').append('<div class="error"><p>Form data should not be empty and should be valid.</p></div>') : '';

        if (mso_packaging_data_invalid) {

            let params = {
                'action': 'mso_save_packaging',
                'mso_post_data': mso_packaging_data,
                'mso_main_div': mso_maindiv,
                'mso_mainplan': mso_mainplan,
                'loader_id': '.mso_packaging_template',
            };

            mso_ajax_request(params, mso_save_packaging);
        }

    });

    jQuery('.mso_add_packaging').on('click', function (event) {
        event.preventDefault();
        var mso_maindiv = jQuery(this).attr('data-main');
        var mso_linked_div = 'div.' + mso_maindiv + ' tr.mso_packaging_td_row';
        console.log(mso_linked_div);
        var new_pakcaging_row = jQuery(mso_linked_div).last().html();
        jQuery(mso_linked_div).last().after('<tr class="row mso_packaging_td_row">' + new_pakcaging_row + '</tr>');
        jQuery(mso_linked_div).last().find('input[type=text]').val('');
        jQuery(mso_linked_div).last().find('input[type=checkbox]').prop('checked', true);
        jQuery(mso_linked_div).last().find('.mso_packaging_id').val('new');
        jQuery(mso_linked_div).last().find('input').removeClass('mso_input_red_border');
    });

    mso_enable_product_setting();

    jQuery('.mso_popup_overly_template .mso_add_location').on('click', function () {
        var mso_popup_location_row_tr = jQuery('tr.mso_popup_location_row').last();
        jQuery(mso_popup_location_row_tr).after('<tr class="row mso_popup_location_row">' + jQuery(mso_popup_location_row_tr).html() + '</tr>');
        jQuery('tr.mso_popup_location_row').last().find('input[type=text]').val('');
        jQuery('tr.mso_popup_location_row').last().find('.mso_location_id').val('new');
        jQuery('tr.mso_popup_location_row').last().find('input').removeClass('mso_input_red_border');

        // let mso_location_input = [
        //     'address', 'city', 'state', 'zip', 'country', 'action'
        // ];
        //
        // let mso_input_template = '<input type="hidden" class="mso_location_id" name="mso_location_id" value="new">';
        // jQuery.each(mso_location_input, function (index, location) {
        //     if (location == 'action') {
        //         mso_input_template += '<div class="mso_delete_location col-md-1">';
        //         mso_input_template += '<span class="dashicons dashicons-trash" onclick="mso_delete_location(this,event)"></span>';
        //         mso_input_template += '</div>';
        //     } else {
        //         let col_length = location == 'address' ? 'col-md-3' : 'col-md-2';
        //         mso_input_template += '<div class="mso_append_input ' + col_length + '">';
        //
        //         // Uppercase first letter
        //         var mso_text_ph = location;
        //         mso_text_ph = mso_text_ph.toLowerCase().replace(/\b[a-z]/g, function (letter) {
        //             return letter.toUpperCase();
        //         });
        //         mso_input_template += '<input type="text" name="mso_' + location + '" class="form-control" placeholder="' + mso_text_ph + '">';
        //         mso_input_template += '<span class="error"></span>';
        //         mso_input_template += '</div>';
        //     }
        // });
        //
        // if (mso_input_template.length > 0) {
        //     jQuery('.mso_popup_location_overly tr.mso_popup_location_row').last().after('<div class="bootstrap-iso form-wrp"><tr class="row mso_popup_location_row">' + mso_input_template + '</tr></div>');
        // }
    });

    jQuery(".mso_api_credentials_status").closest("tr").find("th label").on('click', function (event) {
        event.preventDefault();
        if (mso_validate_input(jQuery(this).closest('table')) == false) {
            jQuery('tr.mso_api_credentials_status_tr p.description').html("");
            return false;
        }

        let params = {
            'action': 'mso_test_connection',
            'mso_api_test_mode': jQuery("#mso_api_test_mode").is(':checked') ? 'yes' : 'no',
            'mso_carrier_id': jQuery(this).closest('table').find('tr.mso_carrier_id td input').val(),
            'mso_post_data': jQuery(this).closest('table').find('input').serializeArray(),
            'loader_id': '#mso_spq_settings-description'
        };

        msoobj = this;
        // remove previous message.
        if (jQuery(msoobj).closest('tr').find('td .description').length) {
            jQuery(msoobj).closest('tr').find('td .description').remove();
        }
        mso_ajax_request(params, mso_test_connection_response);
    });
});

// Options to Consider When No Shipping Rates Are Available
if (typeof mso_no_shipping_cost_options != 'function') {
    function mso_no_shipping_cost_options(n) {
        jQuery('tr.mso_no_shipping_option_error_message, tr.mso_no_shipping_option_custom_rate').hide();
        switch (n) {
            case 'custom_rate':
                jQuery('tr.mso_no_shipping_option_custom_rate').show();
                break;
            default:
                jQuery('tr.mso_no_shipping_option_error_message').show();
                break;
        }
    }
}

// Get object or array from string
function get_object_string(url) {
    let query_string = url.substring(url.lastIndexOf("?") + 1);
    return query_string.split('&').map(function (sParam) {
        let param = sParam.split('=');
        return {
            name: param[0],
            value: decodeURIComponent(param[1])
        };
    });
}

// Select All Domestic FedEx
if (typeof mso_select_all_domestic_fedex != 'function') {
    function mso_select_all_domestic_fedex() {
        // jQuery('.mso_fedex_spq_domestic_services_sa').closest('tr').addClass("mso_fedex_spq_domestic_services_sa");
        jQuery('#mso_fedex_spq_domestic_services_sa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (this.checked) {
                    jQuery('input.mso_fedex_dsa').each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery('input.mso_fedex_dsa').each(function () {
                        this.checked = false;
                    });
                }
            }
        });

        jQuery('input.mso_fedex_dsa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (jQuery('input.mso_fedex_dsa:checked').length == jQuery('input.mso_fedex_dsa').length) {
                    jQuery('#mso_fedex_spq_domestic_services_sa').prop("checked", true);
                } else {
                    jQuery('#mso_fedex_spq_domestic_services_sa').prop("checked", false);
                }
            }
        });
    }
}

// Select All International FedEx
if (typeof mso_select_all_international_fedex != 'function') {
    function mso_select_all_international_fedex() {
        // jQuery('.mso_fedex_spq_international_services_sa').closest('tr').addClass("mso_fedex_spq_international_services_sa");
        jQuery('#mso_fedex_spq_international_services_sa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (this.checked) {
                    jQuery('input.mso_fedex_isa').each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery('input.mso_fedex_isa').each(function () {
                        this.checked = false;
                    });
                }
            }
        });

        jQuery('input.mso_fedex_isa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (jQuery('input.mso_fedex_isa:checked').length == jQuery('input.mso_fedex_isa').length) {
                    jQuery('#mso_fedex_spq_international_services_sa').prop("checked", true);
                } else {
                    jQuery('#mso_fedex_spq_international_services_sa').prop("checked", false);
                }
            }
        });
    }
}

// Select All Domestic UPS
if (typeof mso_select_all_domestic_ups != 'function') {
    function mso_select_all_domestic_ups() {
        // jQuery('.mso_ups_spq_domestic_services_sa').closest('tr').addClass("mso_ups_spq_domestic_services_sa");
        jQuery('#mso_ups_spq_domestic_services_sa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (this.checked) {
                    jQuery('input.mso_ups_dsa').each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery('input.mso_ups_dsa').each(function () {
                        this.checked = false;
                    });
                }
            }
        });

        jQuery('input.mso_ups_dsa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (jQuery('input.mso_ups_dsa:checked').length == jQuery('input.mso_ups_dsa').length) {
                    jQuery('#mso_ups_spq_domestic_services_sa').prop("checked", true);
                } else {
                    jQuery('#mso_ups_spq_domestic_services_sa').prop("checked", false);
                }
            }
        });
    }
}

// Select All International UPS
if (typeof mso_select_all_international_ups != 'function') {
    function mso_select_all_international_ups() {
        // jQuery('.mso_ups_spq_international_services_sa').closest('tr').addClass("mso_ups_spq_international_services_sa");
        jQuery('#mso_ups_spq_international_services_sa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (this.checked) {
                    jQuery('input.mso_ups_isa').each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery('input.mso_ups_isa').each(function () {
                        this.checked = false;
                    });
                }
            }
        });

        jQuery('input.mso_ups_isa').click(function (event) {
            if (!jQuery(this).hasClass('mso_disabled')) {
                if (jQuery('input.mso_ups_isa:checked').length == jQuery('input.mso_ups_isa').length) {
                    jQuery('#mso_ups_spq_international_services_sa').prop("checked", true);
                } else {
                    jQuery('#mso_ups_spq_international_services_sa').prop("checked", false);
                }
            }
        });
    }
}

// Cancel Shipment
if (typeof mso_cancel_shipment_done != 'function') {
    function mso_cancel_shipment_done(params, response) {
        console.log(response);
        var pr = JSON.parse(response);
        var message = 'There is a system error occured';
        if (typeof pr.success != 'undefined' && typeof pr.message != 'undefined') {
            jQuery(msoobj).closest('form.mso_flex_template').find('.mso_ship_label_content').remove();
            jQuery(msoobj).closest('form.mso_flex_template').find('table.mso_api_response_table').remove();
            jQuery(msoobj).closest('form.mso_flex_template').find('.mso_cancel_shipment').remove();
            message = pr.message;
        } else if (typeof pr.message != 'undefined') {
            message = pr.message;
        }

        jQuery('.mso_deleting_shipment_message').remove();
        jQuery('.mso_delete_shipment_done').closest('div.mso_input').after('<div style="width: 96.5%; margin: 15px; padding: 8px;" class="col-md-12 updated woocommerce-message mso_deleting_shipment_message">' + message + '</div>');
        // location.reload(true);
    }
}

// Order label print
if (typeof mso_order_label_click_to_print != 'function') {
    function mso_order_label_click_to_print() {
        // jQuery('.mso_order_shipment_file_to_show_overly').find('.mso_file_to_upload').mso_print();
        let image_path = jQuery('.mso_file_to_upload img').attr('src');
        printJS({
            printable: [image_path],
            type: 'image',
            // header: 'MiniLogics',
            showModal: true,
            modalMessage: "Label Loading...",
            documentTitle: 'LABEL',
            imageStyle: 'width: 85%;display: block;margin: auto;'
        });
    }
}

// Order label menu
if (typeof mso_order_label_click_to_download != 'function') {
    function mso_order_label_click_to_download(obj) {
        var fw = jQuery(obj).attr('fileway');
        var fl = document.createElement('a');
        fl.href = fw;
        fl.download = fw.split('/').pop();
        fl.click();
        fl.remove();
        return false;
    }
}

// Order label menu
if (typeof mso_file_to_click != 'function') {
    function mso_file_to_click(obj, file_type) {
        // TODO
        // jQuery('.mso_order_shipment_file_to_show_overly .mso_file_to_upload').removeClass('mso_order_label_rotated_90');
        var mso_file_script = jQuery(obj).attr('src');
        jQuery('.msoolctd').attr('fileway', mso_file_script);
        jQuery('.msoolctp').show();
        var mso_file_upload = '';
        if (file_type == 1) {
            mso_file_upload = '<img src="' + mso_file_script + '">';
            // jQuery('.mso_order_shipment_file_to_show_overly .mso_file_to_upload').addClass('mso_order_label_rotated_90');
        } else if (file_type == 2 || file_type == 3) {
            jQuery('.msoolctp').hide();
            var mso_pdf_src = jQuery(obj).attr('mso_pdf_src');
            jQuery('.msoolctd').attr('fileway', mso_pdf_src);
            mso_file_upload = '<iframe src="' + mso_pdf_src + '" height="600px" width="100%"/>';
            if (file_type == 3) {
                // jQuery('.mso_order_shipment_file_to_show_overly .mso_file_to_upload').addClass('mso_order_label_rotated_270');
            }
        }

        jQuery('.mso_order_shipment_file_to_show_overly .mso_file_to_upload').html(mso_file_upload);
        jQuery('.mso_order_shipment_file_to_show_overly').css({'opacity': 1, 'display': 'block'});
    }
}

// Product settings
if (typeof mso_enable_product_setting != 'function') {
    function mso_enable_product_setting() {
        if (jQuery('.mso_enable_product_setting').is(':checked')) {
            jQuery('.mso_product_setting').closest('p').show();
        } else {
            jQuery('.mso_product_setting').closest('p').hide();
        }
    }
}

// Location delete
if (typeof mso_location_delete_warning_overly_hide != 'function') {
    function mso_location_delete_warning_overly_hide() {
        jQuery('.mso_location_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        jQuery('.mso_popup_location_overly').css({'opacity': 1, 'display': 'block'});
    }
}

// Packaging delete
if (typeof mso_packaging_delete_warning_overly_hide != 'function') {
    function mso_packaging_delete_warning_overly_hide(e) {
        e.preventDefault();
        jQuery('.mso_packaging_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        jQuery('.mso_packaging_delete_warning_overly').hide();
    }
}

// Order asset delete cancel
if (typeof mso_order_asset_delete_warning_overly_hide != 'function') {
    function mso_order_asset_delete_warning_overly_hide() {
        jQuery('.mso_order_item_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        jQuery('.mso_order_shipment_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        // File to show, hide popup
        jQuery('.mso_order_shipment_file_to_show_overly').css({'opacity': 0, 'display': 'none'});
        // Remove the message
        jQuery('.mso_deleting_shipment_message').remove();
    }
}

// Logs overly hide
if (typeof mso_logs_overly_hide != 'function') {
    function mso_logs_overly_hide() {
        jQuery('.mso_logs_overly').css({'opacity': 0, 'display': 'none'});
    }
}

// Order item delete
if (typeof mso_order_item_remove != 'function') {
    function mso_order_item_remove(obj, event) {
        jQuery('.mso_order_item_delete_warning_overly').css({'opacity': 1, 'display': 'block'});
        event.preventDefault();
        jQuery('.mso_delete_order_item_done').on('click', function () {
            jQuery(obj).closest('li').remove();
            jQuery('.mso_order_item_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        });

    }
}

// Shipment enable disable
if (typeof mso_order_shipment_enable_disable != 'function') {
    function mso_order_shipment_enable_disable(obj, event) {
        if (jQuery(obj).is(':checked')) {
            jQuery(obj).closest('.mso_order_ship_action').next('.mso_order_shipment').removeClass('mso_disabled');
        } else {
            if (jQuery(obj).closest('.mso_order_ship_action').next('.mso_order_shipment').next('.mso_flex_template').is(':visible')) {
                jQuery(obj).closest('.mso_order_ship_action').next('.mso_order_shipment').click();
            }
            jQuery(obj).closest('.mso_order_ship_action').next('.mso_order_shipment').addClass('mso_disabled');
        }
    }
}

// Order item delete
if (typeof mso_order_shipment_remove != 'function') {
    function mso_order_shipment_remove(obj, event) {
        jQuery('.mso_order_shipment_delete_warning_overly').css({'opacity': 1, 'display': 'block'});
        event.preventDefault();
        jQuery('.mso_delete_shipment_done').on('click', function () {
            var remove_template = jQuery(obj).closest('.mso_order_shipment');
            jQuery(remove_template).prev('table').remove();
            jQuery(remove_template).next('.mso_flex_template').remove();
            jQuery(remove_template).remove();
            jQuery('.mso_order_shipment_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        });
    }
}

// Order get quote
if (typeof mso_order_recreate_shipment_allow != 'function') {
    function mso_order_recreate_shipment_allow(order_id) {
        let params = {
            'action': 'mso_order_recreate_shipment_allowed',
            'mso_order_id': order_id,
        };
        mso_ajax_request(params, mso_order_recreate_shipment_allowed);
    }
}

// Order get quote
if (typeof mso_order_get_quote != 'function') {
    function mso_order_get_quote() {
        var mso_shipment = {};
        jQuery(".mso_order_shipment").each(function (index, obj) {
            var mso_shipment_number = jQuery(obj).data('mso_ship_num');
            var mso_get_items = {};
            var mso_get_accessorials = {};

            var mso_enable_disable = jQuery(obj).prev().find('input[type="checkbox"]').is(':checked') ? 'enabled' : 'disabled';
            console.log(mso_enable_disable);
            // TODO
            var mso_origin = jQuery(obj).next().find('.mso_order_shipment_origin').val();
            var mso_items_list = jQuery(obj).next().find('ul.mso_items_sortable li');
            var mso_order_accessorial = jQuery(obj).next().find('.mso_order_accessorial td input[type=checkbox]');
            jQuery(mso_items_list).each(function (ind, item) {
                mso_get_items[jQuery(item).data('product_id')] = jQuery(item).find('.mso_item_quantity input[type=number]').val();
            });

            jQuery(mso_order_accessorial).each(function (ind, accessorial) {
                var accessorial_type = jQuery(accessorial).attr('id');
                var accessorial_prop = jQuery(accessorial).is(':checked') ? 'yes' : 'no';
                mso_get_accessorials[accessorial_type] = accessorial_prop;
            });

            mso_shipment[mso_shipment_number] = {};
            mso_shipment[mso_shipment_number]['origin'] = mso_origin;
            mso_shipment[mso_shipment_number]['items'] = mso_get_items;
            mso_shipment[mso_shipment_number]['accessorials'] = mso_get_accessorials;
            mso_shipment[mso_shipment_number]['enable_disable'] = mso_enable_disable;
        });

        console.log(mso_shipment);
        var mso_ship_to_address = jQuery('input[name=mso_ship_to_address]:checked').val();
        var mso_order_id = jQuery('.mso_order_id').attr('value');

        let params = {
            'action': 'mso_shipment_order',
            'mso_shipments': mso_shipment,
            'mso_ship_to_address': mso_ship_to_address,
            'mso_order_id': mso_order_id,
            'loader_id': '#mso_order',
        };
        mso_ajax_request(params, mso_shipment_order);
    }
}

// Order ship
if (typeof mso_order_ship != 'function') {
    function mso_order_ship() {
        var mso_shipment = {};
        var mso_shipment_number = 1;
        jQuery(".mso_order_shipment").each(function (index, obj) {
            var mso_get_items = {};
            var mso_get_accessorials = {};

            var mso_enable_disable = jQuery(obj).prev().find('input[type="checkbox"]').is(':checked') ? 'enabled' : 'disabled';
            var mso_origin = jQuery(obj).next().find('.mso_order_shipment_origin').val();
            var mso_items_list = jQuery(obj).next().find('ul.mso_items_sortable li');
            var mso_order_accessorial = jQuery(obj).next().find('.mso_order_accessorial td input[type=checkbox]');
            var mso_order_service = jQuery(obj).next().find('input[name=mso_order_rate]:checked').val();
            var mso_order_rate = jQuery(obj).next().find('input[name=mso_order_rate]:checked').attr('data-rate');
            console.log(mso_order_rate);
            jQuery(mso_items_list).each(function (ind, item) {
                mso_get_items[jQuery(item).data('product_id')] = jQuery(item).find('.mso_item_quantity input[type=number]').val();
            });

            var mso_order_service_error = '';
            if (!(typeof (mso_order_service) != "undefined" && mso_order_service.length > 0)) {
                mso_order_service_error = jQuery(obj).next().find('.mso_rate_error_message').text();
            }

            jQuery(mso_order_accessorial).each(function (ind, accessorial) {
                var accessorial_type = jQuery(accessorial).attr('id');
                var accessorial_prop = jQuery(accessorial).is(':checked') ? 'yes' : 'no';
                mso_get_accessorials[accessorial_type] = accessorial_prop;
            });

            mso_shipment[mso_shipment_number] = {};
            mso_shipment[mso_shipment_number]['service'] = mso_order_service;
            mso_shipment[mso_shipment_number]['selected_rate'] = mso_order_rate;
            mso_shipment[mso_shipment_number]['service_error'] = mso_order_service_error;
            mso_shipment[mso_shipment_number]['origin'] = mso_origin;
            mso_shipment[mso_shipment_number]['items'] = mso_get_items;
            mso_shipment[mso_shipment_number]['enable_disable'] = mso_enable_disable;
            mso_shipment[mso_shipment_number]['accessorials'] = mso_get_accessorials;
            mso_shipment_number++;
        });

        console.log(mso_shipment);
        var mso_ship_to_address = jQuery('input[name=mso_ship_to_address]:checked').val();
        var mso_order_id = jQuery('.mso_order_id').attr('value');

        let params = {
            'action': 'mso_shipment_order_placed',
            'mso_shipments': mso_shipment,
            'mso_ship_to_address': mso_ship_to_address,
            'mso_order_id': mso_order_id,
            'loader_id': '#mso_order',
        };
        mso_ajax_request(params, mso_shipment_order_placed);
    }
}

// Order placed
if (typeof mso_shipment_order_placed != 'function') {
    function mso_shipment_order_placed(params, response) {
        console.log(response);
        history.go(0);
        location.reload(true);
    }
}

// If object is empty
if (typeof mso_is_empty != 'function') {
    function mso_is_empty(obj) {
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop))
                return false;
        }
        return true;
    }
}

// New Shipment
if (typeof mso_new_shipment != 'function') {
    function mso_new_shipment(params, response) {
        location.reload();
    }
}

// Recreate shipment allowed
if (typeof mso_order_recreate_shipment_allowed != 'function') {
    function mso_order_recreate_shipment_allowed(params, response) {
        location.reload();
    }
}

// Showing rates on the order page
if (typeof mso_shipment_order != 'function') {
    function mso_shipment_order(params, response) {
        jQuery('.mso_rate_error_message').length ? jQuery('.mso_rate_error_message').remove() : '';
        jQuery('div.mso_order_rates').html('');
        jQuery('div.mso_order_package').html('');
        var parsed_response = JSON.parse(response);
        var shipment_rates = parsed_response.order_rates;
        var order_packages = parsed_response.order_packages;
        var rates_returning = parsed_response.rates_returning;
        if (mso_is_empty(shipment_rates)) {
            jQuery('div.mso_order_rates').last().append('<span style="color: red;"><b>Error! </b> Please try again later.</span>');
        } else {
            jQuery(".mso_order_shipment").each(function (index, obj) {
                var mso_shipment_number = jQuery(obj).data('mso_ship_num');
                jQuery(obj).next().find('div.mso_order_rates').append('<p class="mso_calculate_shipping_heading">Shipping</p>');
                jQuery.each(shipment_rates, function (mso_ship_num, rate) {
                    if (mso_shipment_number == mso_ship_num) {
                        if (rate.length > 0) {
                            jQuery(obj).next().find('div.mso_order_rates').append(rate);
                            if (typeof order_packages[mso_ship_num] != 'undefined') {
                                // jQuery(obj).next().find('div.mso_order_rates').prepend(order_packages[mso_ship_num]);
                                jQuery(obj).next().find('div.mso_order_package').html(order_packages[mso_ship_num]);
                            }
                        } else {
                            jQuery(obj).next().find('div.mso_order_rates').append('<span style="color: red;"><b>Error! </b> Please try again later.</span>');
                        }
                    }
                });
            });

            jQuery('button.mso_order_ship').remove();
            if (rates_returning == true) {
                mso_display_li_option('msoorc');
                jQuery('button.mso_order_get_quote').after('<button type="button" onclick="mso_order_ship()" class="button-primary mso_order_ship">Ship Now</button>');
            }
        }
    }
}

// Display tab on the order page
if (typeof mso_display_li_option != 'function') {
    function mso_display_li_option(type) {
        jQuery('div.mso_order_shipment').each(function (ind, obj) {
            if (!jQuery(obj).hasClass('mso_disabled') && jQuery(obj).next().css('display') == 'none') {
                jQuery(obj).trigger('click');
            }

            jQuery(obj).next().find('.mso_order_main_tab li.' + type).trigger('click');
        });
    }
}

// When click  Enable / Disable on packaging page.
if (typeof mso_edp != 'function') {
    function mso_edp(params, response) {
        console.log(response);
    }
}

// Location delete proceed
if (typeof mso_location_delete_proceed != 'function') {
    function mso_location_delete_proceed(params, response) {
        console.log(response);
        jQuery('.mso_location_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        jQuery('.mso_popup_location_overly').css({'opacity': 1, 'display': 'block'});
    }
}

// Location delete proceed
if (typeof mso_packaging_delete_proceed != 'function') {
    function mso_packaging_delete_proceed(params, response) {
        console.log(response);
        jQuery('.mso_packaging_delete_warning_overly').css({'opacity': 0, 'display': 'none'});
        jQuery('.mso_packaging_delete_warning_overly').hide();
    }
}

// Packaging delete
if (typeof mso_delete_packaging != 'function') {
    function mso_delete_packaging(obj, event) {
        if (!jQuery(obj).hasClass('mso_disabled')) {
            var mso_packaging_id = jQuery(obj).closest('tr.mso_packaging_td_row').find('.mso_packaging_id').val();
            console.log(mso_packaging_id);
            event.preventDefault();
            jQuery('.mso_packaging_delete_warning_overly').css({'opacity': 1, 'display': 'block'});
            jQuery('.mso_delete_packaging_done').on('click', function (e) {
                e.preventDefault();
                let params = {
                    'action': 'mso_delete_packaging',
                    'mso_packaging_id': mso_packaging_id,
                };
                mso_ajax_request(params, mso_packaging_delete_proceed);
                if (jQuery('.mso_delete_packaging').length > 1) {
                    jQuery(obj).closest('tr.mso_packaging_td_row').remove();
                } else {
                    jQuery(obj).closest('tr.mso_packaging_td_row').find('input[type=text]').val('');
                }
            });
        }
    }
}

// Location delete
if (typeof mso_delete_location != 'function') {
    function mso_delete_location(obj, event) {
        let mso_location_id = jQuery(obj).closest('.mso_popup_location_row').find('.mso_location_id').val();
        event.preventDefault();
        jQuery('.mso_popup_location_overly').css({'opacity': 0, 'display': 'none'});
        jQuery('.mso_location_delete_warning_overly').css({'opacity': 1, 'display': 'block'});

        jQuery('.mso_delete_location_done').on('click', function () {
            let params = {
                'action': 'mso_delete_location',
                'mso_location_id': mso_location_id,
            };
            mso_ajax_request(params, mso_location_delete_proceed);
            if (jQuery('.mso_popup_overly_template .mso_delete_location').length > 1) {
                jQuery(obj).closest('.mso_popup_location_row').remove();
            } else {
                jQuery(obj).closest('.mso_popup_location_row').find('input[type=text]').val('');
            }
        });
    }
}

// Connection settings response
if (typeof mso_test_connection_response != 'function') {
    function mso_test_connection_response(params, response) {
        console.log(response);
        var api_response = JSON.parse(response);
        var api_message = typeof api_response.message != 'undefined' ? api_response.message : 'Error';
        jQuery(msoobj).closest('tr').find('td input').after('<p class="description">' + api_message + '</p>');
    }
}

// Get location data
if (typeof mso_get_location_data != 'function') {
    function mso_get_location_data(params, response) {
        console.log(response);
        jQuery('.mso_popup_overly_template .mso-form-control').html(response);
    }
}

// Save location data
if (typeof mso_save_location != 'function') {
    function mso_save_location(params, response) {
        console.log(response);
        jQuery('.mso_popup_overly_template .mso-form-control').html(response);
        window.setTimeout(function () {
            location.reload()
        }, 1000)
    }
}

// Validate json
if (typeof mso_is_valid_json != 'function') {
    function mso_is_valid_json(json) {
        try {
            JSON.parse(json);
            return true;
        } catch (e) {
            return false;
        }
    }
}

// Save location data
if (typeof mso_save_packaging != 'function') {
    function mso_save_packaging(params, response) {
        var mso_main_div = params.mso_main_div;
        if (mso_is_valid_json(response)) {
            var mso_response = JSON.parse(response);
            var mso_message = typeof mso_response.message != 'undefined' ? mso_response.message : 'Error';
            jQuery('div.' + mso_main_div + ' .mso_packaging_error').append('<div class="error"><p>' + mso_message + '</p></div>');
        } else {
            jQuery('div.' + mso_main_div + ' .mso_packaging_error').append('<div class="updated"><p>Your settings have been saved.</p></div>');
            jQuery('div.' + mso_main_div + ' .mso_packaging_post_meta').html(response);
        }
    }
}

// Validation Form JS
if (typeof mso_validate_input != 'function') {
    function mso_validate_input(template) {
        let has_err = true;
        let has_skip = false;
        jQuery('.mso_error').remove();
        jQuery(template).find("input[type='text']").each(function () {
            if (jQuery(this).hasClass('mso_api_credentials_status') || has_skip) {
                has_skip = true;
                return has_err;
            }

            jQuery(this).removeClass('mso_input_red_border');
            jQuery(this).after('<div class="mso_error"></div>')
            let input = jQuery(this).val();
            let response = mso_validate_string(input);
            let error_text = jQuery(this).closest('tr').find('th label').text();
            let optional = jQuery(this).data('optional');
            let mso_error_element = jQuery(this).parent().find('.mso_error');

            // Alphanumeric string validation
            let alphanumeric = jQuery(this).data('alphanumeric');
            response = alphanumeric !== undefined && alphanumeric == 1 ? mso_string_alphanumeric(input) : response;

            // Numeric string validation, decimal allowed
            let mso_numeric = jQuery(this).data('numeric');
            response = mso_numeric !== undefined && mso_numeric == 1 ? mso_string_numeric(input) : response;

            optional = (optional === undefined) ? 0 : 1;
            error_text = (error_text != undefined) ? error_text : '';

            if ((optional == 0) && (response == false || response == 'empty')) {
                if (error_text.length > 0) {
                    error_text = (response == 'empty') ? error_text + ' is required.' : 'Invalid input.';
                    jQuery(mso_error_element).html(error_text);
                } else {
                    jQuery(this).addClass('mso_input_red_border');
                }
            }
            has_err = (response != true && optional == 0) ? false : has_err;
        });
        return has_err;
    }
}

// Validate Input String
if (typeof mso_validate_string != 'function') {
    function mso_validate_string(string) {
        if (string == '')
            return 'empty';
        else
            return true;

    }
}

// Validate alphanumeric input string
if (typeof mso_string_alphanumeric != 'function') {
    function mso_string_alphanumeric(string) {
        var alphanumeric = /^[0-9a-zA-Z\s]+$/;
        if (string.match(alphanumeric))
            return true;
        else
            return false;
    }
}

// Validate numeric input string
if (typeof mso_string_numeric != 'function') {
    function mso_string_numeric(string) {
        var mso_numeric = /^[0-9\s.]+$/;
        if (string.match(mso_numeric))
            return true;
        else
            return false;
    }
}

// Variable exist
if (typeof mso_is_var_exist != 'function') {
    function mso_is_var_exist(index, item) {
        return typeof item[index] != 'undefined' ? true : false;
    }
}

// Common ajax request
if (typeof mso_ajax_request != 'function') {
    function mso_ajax_request(params, call_back_function) {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: params,
            beforeSend: function () {
                if (jQuery(params.loader_id).length) {
                    jQuery('.mso_roller_overly').remove();
                    var loader_html = '<div class="mso_roller_overly"><div class="mso_roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
                    jQuery(params.loader_id).append(loader_html);
                }
            },
            success: function (response) {
                if (jQuery('.mso_roller_overly').length) {
                    jQuery('.mso_roller_overly').remove();
                }
                return call_back_function(params, response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
}