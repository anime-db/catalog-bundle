var FormRefill = function(form, button, controller, handler, sources) {
    this.form = form;
    this.button = button;
    this.controller = controller;
    this.handler = handler;
    this.sources = sources;

    var that = this;
    this.button.click(function() {
        if (that.button.data('can-refill') == 1) {
            that.refill();
        } else {
            that.search();
        }
        return false;
    });
};
FormRefill.prototype = {
    refill: function() {
        var that = this;
        this.showPopup(
            'refill-form-' + this.controller.field.attr('id'),
            this.button.attr('href'),
            function (popup) {
                popup.body.find('form').submit(function() {
                    that.update(popup);
                    return false;
                });
            }
        );
    },
    search: function() {
        var that = this;
        this.showPopup(
            'refill-search',
            this.button.attr('href'),
            function (popup) {
                popup.body.find('a:not(.external)').each(function() {
                    new FormRefillSearchItem(that, popup, $(this));
                });
            }
        );
    },
    refillFromSearch: function(url) {
        var that = this;
        this.showPopup(
            'refill-form-' + this.controller.field.attr('id'),
            url,
            function (popup) {
                popup.body.find('form').submit(function() {
                    that.update(popup);
                    return false;
                });
            }
        );
    },
    showPopup: function(name, url, handler) {
        var group = name;
        name +=  '-plugin-' + this.button.data('plugin');
        handler = handler || function() {};
        var that = this;

        if (popup = PopupContainer.get(name)) {
            handler(popup);
            popup.show();
        } else {
            PopupContainer.lazyload(name, {
                url: url,
                method: 'POST', // request is too large for GET
                data: this.form.serialize(),
                success: function(popup) {
                    that.handler.notify(popup.body);
                    popup.body.addClass(group);
                    handler(popup);
                }
            });
        }
    },
    update: function(popup) {
        this.controller.update(popup);
        // add source link
        var source = popup.body.find('input[type=hidden]');
        if (source && (value = source.val())) {
            this.canRefill();
            this.sources.add().row.find('input').val(value);
        }
        popup.hide();
    },
    canRefill: function() {
        this.form.find('a[data-plugin='+this.button.data('plugin')+']').each(function() {
            var button = $(this);
            button.attr('href', button.data('link-refill')).data('can-refill', 1);
        });
    }
};
var FormRefillSimple = function(field) {
    this.field = field;
};
FormRefillSimple.prototype = {
    update: function(popup) {
        this.field.val(popup.body.find('#'+this.field.attr('id')).val());
    }
};
var FormRefillCollection = function(field, collection, container) {
    this.field = field;
    this.collection = collection; // FormCollection
    this.container = container; // FormCollectionContainer
};
FormRefillCollection.prototype = {
    update: function(popup) {
        // remove old rows
        while (this.collection.rows.length) {
            this.collection.rows[0].remove();
        }
        // add new rows
        var collection = this.container.get(this.field.attr('id'));
        for (var i = 0; i < collection.rows.length; i++) {
            this.collection.addRowObject(new FormCollectionRow(collection.rows[i].row.clone()));
        }
    }
};
var FormRefillMulti = function(field) {
    this.field = field;
};
FormRefillMulti.prototype = {
    update: function(popup) {
        var that = this;
        popup.body.find('input:checked').each(function() {
            that.field.find('#'+$(this).attr('id')).prop('checked', true);
        });
    }
};
var FormRefillSearchItem = function(form, popup, link) {
    var that = this;
    this.form = form;
    this.popup = popup;
    this.link = link.click(function() {
        that.refill();
        return false;
    });

};
FormRefillSearchItem.prototype = {
    refill: function() {
        this.popup.hide();
        var source = decodeURIComponent(this.link.attr('href')).replace(/^.*(?:\?|&)source=(.*)$/, '$1');
        if (source) {
            this.form.canRefill();
            this.form.sources.add().row.find('input').val(source);
            this.form.refill();
        } else {
            this.form.refillFromSearch(this.link.attr('href'));
        }
    }
};
