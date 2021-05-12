var $ = require('jquery');

$(function() {
    var $hasIncome = $('#has-income input');

    var $income = $('#income');

    $hasIncome.change(function() {
        toggleIncome();
    });

    function toggleIncome()
    {
        if ($hasIncome.is(':checked')) {
            $income.show();
        } else {
            $income.hide();
        }
    }

    toggleIncome();
});

module.exports = $;