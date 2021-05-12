var $ = require('jquery');

$(function() {
    var $paymentRequired = $('#payment-required input');

    var $paymentDetails = $('#payment-details');

    $paymentRequired.change(function() {
        togglePaymentDetails();
    });

    function togglePaymentDetails()
    {
        if ($paymentRequired.is(':checked')) {
            $paymentDetails.show();
        } else {
            $paymentDetails.hide();
        }
    }

    togglePaymentDetails();
});

module.exports = $;