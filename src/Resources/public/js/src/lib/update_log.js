/**
 * Update log block controller
 *
 * Control over the application upgrade and change tracking
 */
var UpdateLogBlock = function(block) {
    this.block = block;
    this.from = block.data('from');
    this.message = block.data('message');
    this.redirect = block.data('redirect') || '/';
    this.end_message = block.data('end-message');
    this.update();
};
UpdateLogBlock.prototype = {
    update: function() {
        var that = this;
        $.ajax({
            url: that.from,
            success: function(data) {
                if (that.block.text() != data) {
                    that.block.text(data).animate({scrollTop: that.block[0].scrollHeight}, 'slow');
                    if (data.indexOf(that.end_message) != -1) {
                        that.complete();
                        return;
                    }
                }
                setTimeout(function() {
                    that.update();
                }, 400);
            },
            error: function () {
                setTimeout(function() {
                    that.update();
                }, 400);
            }
        });
    },
    complete: function() {
        alert(this.message);
        window.location.replace(this.redirect);
    }
};
