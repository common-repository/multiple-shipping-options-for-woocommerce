jQuery(function () {
    if (typeof mso_trigger == "undefined") {
        var mso_trigger = false;
        jQuery('#billing_address_1').addClass('billing_address1');
        jQuery('#billing_address_2').addClass('billing_address_2');
        jQuery('#shipping_address_1').addClass('shipping_address_1');
        jQuery('#shipping_address_2').addClass('shipping_address_2');
        jQuery('#shipping_city').addClass('shipping_city');
        jQuery('#billing_city').addClass('billing_city');
        jQuery('#billing_postcode').addClass('billing_postcode');
        jQuery('#shipping_postcode').addClass('shipping_postcode');

        jQuery('.billing_address1, .billing_address_2, .shipping_address_1, .shipping_address_2, .shipping_city, .billing_city, .billing_postcode, .shipping_postcode').on('keydown', function (event) {
            if (mso_trigger == false) {
                event.stopImmediatePropagation();
            }
            mso_trigger = false;
        });

        jQuery('.billing_address1, .billing_address_2, .shipping_address_1 , .shipping_address_2, .shipping_city, .billing_city, .billing_postcode, .shipping_postcode').on('change', function (event) {
            mso_trigger = true;
            jQuery('.billing_address1').trigger('keydown');
        });
    }
});