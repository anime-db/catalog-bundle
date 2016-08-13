/**
 * Confirm delete
 */
var ConfirmDeleteModel = function(link) {
    this.massage = link.data('massage') || trans('Are you sure want to delete this item(s)?');
    this.link = link;
    var that = this;
    link.click(function() {
        return that.remove();
    });
};
ConfirmDeleteModel.prototype = {
    remove: function() {
        return confirm(this.massage);
    }
};
