var NoticeModel = function(
    container,
    block,
    close_url,
    see_later_url,
    close,
    see_later,
    offset,
    message,
    scroll_left,
    scroll_right
) {
    this.container = container;
    this.block = block;
    this.close_url = close_url;
    this.see_later_url = see_later_url;
    this.close_button = close;
    this.see_later_button = see_later;
    this.offset = offset;
    this.message = message;
    this.scroll_left = scroll_left;
    this.scroll_right = scroll_right;

    var that = this;
    this.close_button.click(function(){
        that.close();
    });
    this.see_later_button.click(function(){
        that.seeLater();
    });
    // scroll buttons
    if (offset > 0) {
        this.scroll_left.hover(function() {
            that.scrollLeft();
        }, function() {
            that.stopScroll();
        }).show();
        this.scroll_right.hover(function() {
            that.scrollRight();
        }, function() {
            that.stopScroll();
        }).show();
    }
};
NoticeModel.prototype = {
    close: function() {
        var that = this;
        this.block.animate({opacity: 0}, 400, function() {
            // report to backend
            $.ajax({
                type: 'POST',
                url: that.close_url,
                success: function() {
                    // remove this
                    that.block.remove();
                    delete that.container.notice;
                    // load new notice
                    that.container.load();
                }
            });
        });
    },
    seeLater: function() {
        var that = this;
        this.block.animate({opacity: 0}, 400, function() {
            // report to backend
            $.ajax({
                type: 'POST',
                url: that.see_later_url,
                success: function() {
                    // remove this
                    that.block.remove();
                    delete that.container.notice;
                }
            });
        });
    },
    scrollLeft: function() {
        this.message.stop().animate({
            'margin-left': 0
        }, 1500);
    },
    scrollRight: function() {
        this.message.stop().animate({
            'margin-left': -(this.offset)
        }, 1500);
    },
    stopScroll: function() {
        this.message.stop();
    }
};
