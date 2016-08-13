/**
 * Toggle block visible
 */
var ToggleBlock = function(button) {
    var block = $(button.data('target'));
    button.click(function() {
        block.slideToggle(150);
        return false;
    });
};
