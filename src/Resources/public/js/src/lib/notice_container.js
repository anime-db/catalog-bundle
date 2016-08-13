/**
 * Notice container
 */
var NoticeContainerModel = function(container, from) {
    this.container = container;
    this.from = from;
    this.notice = null;
    this.load();
};
NoticeContainerModel.prototype = {
    load: function() {
        var that = this;
        this.notice = null;
        $.ajax({
            url: this.from,
            ifModified: true,
            complete: function(jqXHR, textStatus) {
                var data;
                if (
                    (textStatus == 'success' || textStatus == 'notmodified') &&
                    (data = $.parseJSON(jqXHR.responseText))
                ) {
                    that.show(data)
                }
            }
        });
    },
    show: function(data) {
        //data.notice;
        var block = $(data.content);
        var message = block.find('.b-message');
        this.container.append(block);
        this.notice = new NoticeModel(
            this,
            block,
            data.close,
            data.see_later,
            block.find('.bt-close'),
            block.find('.bt-see-later'),
            message.width() - block.find('.b-message-wrapper').width(),
            message,
            block.find('.bt-notice-scroll-left'),
            block.find('.bt-notice-scroll-right')
        );
    }
};
