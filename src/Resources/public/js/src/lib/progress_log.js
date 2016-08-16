var ProgressLog = function(log, container) {
    this.offset = 0;
    this.log = log;
    this.container = container || log;
    this.from = log.data('from');
    this.message = log.data('message');
    this.redirect = log.data('redirect');

    this.update();
};
ProgressLog.prototype = {
    update: function() {
        var that = this;
        $.ajax({
            url: this.from,
            data: {offset: this.offset},
            dataType: 'json',
            success: function(data) {
                that.log.text(that.log.text()+data.content);
                that.offset += data.content.length;
                // scroll progress log to bottom
                if  (that.log.height() > that.container.height()) {
                    that.container.animate({scrollTop: that.container[0].scrollHeight}, 'slow');
                }

                if (data.end) {
                    that.complete();
                } else {
                    setTimeout(function() {
                        that.update();
                    }, 400);
                }
            }
        });
    },
    complete: function() {
        if (this.message) {
            alert(this.message);
        }
        if (this.redirect) {
            window.location.replace(this.redirect);
        }
    }
};