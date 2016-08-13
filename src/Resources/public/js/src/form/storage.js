var FormStorage = function(storage, source, target) {
    this.storage = storage;
    this.source = source;
    this.target = target;

    var that = this;
    this.storage.change(function() {
        that.change();
    }).change();
};
FormStorage.prototype = {
    change: function() {
        if (this.storage.val()) {
            var that = this;
            $.ajax({
                url: this.source,
                data: {'id': this.storage.val()},
                success: function(data) {
                    if (data.required) {
                        that.require(data.path);
                    } else {
                        that.unrequire();
                    }
                }
            });
        } else {
            this.unrequire();
        }
    },
    unrequire: function() {
        this.target.removeAttr('required').removeAttr('data-root').val('').change();
    },
    require: function(path) {
        this.target.attr({'required': 'required', 'data-root': path}).change();
    }
};
