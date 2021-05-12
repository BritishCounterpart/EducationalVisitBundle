var $ = require('jquery');

$(function() {
    var $hasExpenses = $('#has-expenses input');

    var $expenses = $('#expenses');

    $hasExpenses.change(function() {
        toggleExpenses();
    });

    function toggleExpenses()
    {
        if ($hasExpenses.is(':checked')) {
            $expenses.show();
        } else {
            $expenses.hide();
        }
    }

    toggleExpenses();
});

module.exports = $;