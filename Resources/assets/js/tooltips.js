var $ = require('jquery');

$().ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
});

module.exports = $;