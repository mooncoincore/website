(function ($) {
    'use strict';
    $(document).ready(function () {

        $(".ccpw_calculator").each(function(){
        var type = $(this).data('type');
        var ID = $(this).attr('data-calc-id');
        var el = '[data-calc-id="'+ID+'"]';
        $(el).find('.crypto_select , .fiat_select').each(function (i, val) {
            $(this).select2();
        });

        var convert_numbers = function () {
            // var fiat_amount = $(this).parents().find("#fiat_amount").val();
            var crypto_amount = $(el).find("#crypto_amount").val();
            var cryptocurrency = $(el).find("#crypto_dd").val();
            var currency = $(el).find("#fiat_dd").val();
            var coin_name = $(el).find("#crypto_dd option:selected").text();
            var currency_name = $(el).find("#fiat_dd option:selected").text();
            var label = $(el).find("#fiat_dd option:selected").closest('optgroup').prop('label');
            if (crypto_amount == '') {
                crypto_amount = 1;
            }
            if (label == "Crypto Currencies") {
                //10 * (1 BTC Price in USD / 1 ETH Price in USD)
                var calculate_price = crypto_amount * (parseFloat(cryptocurrency) / parseFloat(currency));
            } else {
                var calculate_price = (parseFloat(cryptocurrency) * crypto_amount) * parseFloat(currency);
            }

            if (calculate_price >= 25) {
                var formated_price = numeral(calculate_price).format('0,0.00');
            } else if (calculate_price >= 0.50 && calculate_price < 25) {
                var formated_price = numeral(calculate_price).format('0,0.000');
            } else if (calculate_price >= 0.01 && calculate_price < 0.50) {
                var formated_price = numeral(calculate_price).format('0,0.0000');
            } else if (calculate_price >= 0.0001 && calculate_price < 0.01) {
                var formated_price = numeral(calculate_price).format('0,0.00000');
            } else {
                var formated_price = numeral(calculate_price).format('0,0.00000000');
            }
            $(el).find(".cmc_cal_rs").text(formated_price + ' ' + currency_name);
            $(el).find(".cmc_rs_lbl").text(crypto_amount + ' ' + coin_name);

        }

        $(document).on("change keyup", el, convert_numbers);
        
        // initialize on load
        convert_numbers();
    });
       
    })
})(jQuery)