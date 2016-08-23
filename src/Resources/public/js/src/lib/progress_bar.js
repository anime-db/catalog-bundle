var ProgressBar = function(bar, label) {
    this.bar = bar;
    this.label = label;
    this.expire = null;
    this.from = bar.data('from');
    this.message = bar.data('message');
    this.redirect = bar.data('redirect');
    this.timeout = 2 * 60; // 2 minutes

    var that = this;
    // init jQuery UI progressbar
    this.bar.progressbar({
        value: false,
        change: function() {
            that.label.text(that.bar.progressbar('value')+'%');
        },
        complete: function() {
            that.complete();
        }
    });

    that.update();
};
ProgressBar.prototype = {
    update: function() {
        var that = this;
        $.ajax({
            url: this.from,
            dataType: 'json',
            error: function () {
                if (!that.expire) {
                    that.setExpire();
                } else if (that.expire > new Date()) {
                    that.retry();
                } else {
                    console.log('Loading progress bar data is exceeded.');
                }
            },
            success: function(data) {
                that.bar.progressbar('value', data.status);
                that.setExpire();

                if (data.status == 100) {
                    that.complete();
                } else {
                    that.retry();
                }
            }
        });
    },
    complete: function() {
        this.label.text(this.message);
        this.expire = null;

        if (this.redirect) {
            // give the user the ability to see the completion message before redirecting
            var that = this;
            setTimeout(function() {
                window.location.replace(that.redirect);
            }, 500);
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
