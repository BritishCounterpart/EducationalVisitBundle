var $ = require('jquery');

$(function() {
    var $sendPaymentEmail = $('#send-payment-email input');

    var $paymentEmail = $('#payment-email');

    $sendPaymentEmail.change(function() {
        toggleEmail();
    });

    function toggleEmail()
    {
        if ($sendPaymentEmail.is(':checked')) {
            $paymentEmail.show();
        } else {
            $paymentEmail.hide();
        }
    }

    toggleEmail();
});

module.exports = $;