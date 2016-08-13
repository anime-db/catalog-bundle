var ProgressBar = function(bar, label) {
    this.bar = bar;
    this.label = label;
    this.from = bar.data('from');
    this.message = bar.data('message');
    this.redirect = bar.data('redirect');

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
            success: function(data) {
                that.bar.progressbar('value', data.status);

                if (data.status == 100) {
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
        this.label.text(this.message);

        if (this.redirect) {
            // give the user the ability to see the completion message before redirecting
            var that = this;
            setTimeout(function() {
                window.location.replace(that.redirect);
            }, 500);
        }
    }
};
