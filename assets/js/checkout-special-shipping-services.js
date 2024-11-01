/**
 * Created by Clif.Molina on 10/15/2014.
 */

/**
 * Created by Clif.Molina on 1/23/2015.
 */
/*jslint browser: true*/
/*global $, jQuery, ajax_reset_shipping, ajax_itemized_rates, console, swal*/

(function ($, root, undefined) {
    'use strict';
    $(function () {


        function itemizedRates() {

            jQuery.ajax({
                url: url2,
                type: 'POST',
                dataType: 'html',
                data: {
                    action: 'itemized_rates',
                    nonce: nonce2
                },
                success: function (data) {
                    jQuery('tr.wooocommerce-shipping-rlc-charge').remove();
                    var charges = jQuery.parseJSON(data),
                        output = '';
                    jQuery.each(charges, function(label, charge) {
                        output += '<tr class="wooocommerce-shipping-rlc-charge"><td style="font-weight: normal">'+label+'</td><td style="font-weight: normal"><span class="amount">'+charge+'</span></td></tr>';
                    });
                    if ( output.length ) {
                        jQuery('table.woocommerce-checkout-review-order-table').find('tr.cart-subtotal').after(output);
                    }

                },
                error: function (jqXHR, textStatus, errorThrow) {
                    console.log(jqXHR);
                    console.log("Details: " + textStatus + "\nError:" + errorThrow);
                    console.log(jqXHR + "\n" + errorThrow);
                }
            });
        }

        var destinationAccessorials = jQuery('ul#rqr-destination-accessorials'),
            url = ajax_reset_shipping.ajax_url,
            nonce = ajax_reset_shipping.ajax_nonce,
            url2 = ajax_itemized_rates.ajax_url2,
            nonce2 = ajax_itemized_rates.ajax_nonce2;

        if ( destinationAccessorials.length ) {
            destinationAccessorials.tooltip();

            jQuery('#save-dest-accessorials').click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                jQuery.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        action: 'reset_shipping',
                        nonce: nonce
                    },
                    success: function () {
                        jQuery('body').trigger('update_checkout');


                    },
                    error: function (jqXHR, textStatus, errorThrow) {
                        console.log(jqXHR);
                        console.log("Details: " + textStatus + "\nError:" + errorThrow);
                        console.log(jqXHR + "\n" + errorThrow);
                    },
                    complete: function() {
                        setTimeout(itemizedRates, 4000);
                    }
                });
            });

        }


    });
}(jQuery, this));
