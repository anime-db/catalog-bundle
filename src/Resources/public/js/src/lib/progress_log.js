var ProgressLog = function(log, container) {
    this.offset = 0;
    this.log = log;
    this.expire = null;
    this.container = container || log;
    this.from = log.data('from');
    this.message = log.data('message');
    this.redirect = log.data('redirect');
    this.timeout = 2 * 60; // 2 minutes

    this.retry();
};
ProgressLog.prototype = {
    update: function() {
        var that = this;
        $.ajax({
            url: this.from,
            data: {offset: this.offset},
            dataType: 'json',
            error: function () {
                if (!that.expire) {
                    that.setExpire();
                }

                if (that.expire > new Date()) {
                    that.retry();
                } else {
                    console.log('Loading progress bar data is exceeded.');
                }
            },
            success: function(data) {
                that.log.text(that.log.text()+data.content);
                that.offset += data.content.length;
                // scroll progress log to bottom
                if  (that.log.height() > that.container.height()) {
                    that.container.animate({scrollTop: that.container[0].scrollHeight}, 'slow');
                }
                that.setExpire();

                if (data.end) {
                    that.complete();
                } else {
                    that.retry();
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
    },
    retry: function() {
        var that = this;
        setTimeout(function() {
            that.update();
        }, 400);
    },
    setExpire: function() {
        this.expire = new Date((new Date()).getTime() + (this.timeout * 1000));
    }
};
