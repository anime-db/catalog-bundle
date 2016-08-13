/**
 * Popup
 */
var Popup = function(body) {
    var that = this;
    this.body = body;
    this.close = body.find('.bt-popup-close').click(function() {
        that.hide();
    });
    Cap.registr(this);
};
Popup.prototype = {
    show: function() {
        Cap.show(this.body);
    },
    hide: function() {
        Cap.hide(this.body);
    }
};

var PopupContainer = {
    popup_loader: null,
    xhr: null,
    list: [],
    container: null,
    getContainer: function() {
        if (PopupContainer.container === null) {
            PopupContainer.container = $('body');
        }
        return PopupContainer.container;
    },
    load: function(name, options) {
        options = $.extend({
            success: function() {},
            error: function(xhr, status) {
                if (status != 'abort' && confirm(trans('Failed to get the data. Want to try again?'))) {
                    $.ajax(options);
                }
            }
        }, options||{});

        if (typeof(PopupContainer.list[name]) != 'undefined') {
            options.success(PopupContainer.list[name]);
        } else {
            // init popup on success load popup content
            var success = options.success;
            options.success = function(data) {
                PopupContainer.list[name] = new Popup($(data));
                success(PopupContainer.list[name]);
                PopupContainer.getContainer().append(PopupContainer.list[name].body);
            };

            PopupContainer.sendRequest(options);
        }
    },
    get: function(name) {
        if (typeof(PopupContainer.list[name]) != 'undefined') {
            return PopupContainer.list[name];
        } else {
            return null;
        }
    },
    setPopupLoader: function(el) {
        PopupContainer.popup_loader = new Popup(el);
    },
    sendRequest: function(options) {
        if (PopupContainer.xhr === null) {
            Cap.registr({
                show:function(){},
                hide:function(){
                    PopupContainer.xhr.abort();
                }
            });
        } else {
            PopupContainer.xhr.abort();
        }
        PopupContainer.xhr = $.ajax(options);
    },
    /**
     * Lazy loading body of popup
     */
    lazyload: function(name, options) {
        options = $.extend({
            success: function() {},
            error: function(xhr, status) {
                if (status != 'abort' && confirm(trans('Failed to get the data. Want to try again?'))) {
                    $.ajax(options);
                } else {
                    PopupContainer.popup_loader.hide();
                }
            }
        }, options||{});

        if (typeof(PopupContainer.list[name]) != 'undefined') {
            options.success(PopupContainer.list[name]);
        } else {
            PopupContainer.popup_loader.show();

            // init popup on success load popup content
            var success = options.success;
            options.success = function(data) {
                var popup = new Popup(PopupContainer.popup_loader.body.clone().hide());
                popup.body.attr('id', name).find('.content').append(data);
                PopupContainer.getContainer().append(popup.body);

                PopupContainer.list[name] = popup;
                success(popup);

                // animate show popup
                var width = popup.body.width();
                var height = popup.body.height();
                PopupContainer.popup_loader.body.find();
                PopupContainer.popup_loader.body.addClass('resize').animate({
                    'width': width,
                    'height': height
                }, 400, function() {
                    popup.show();
                    // reset style
                    PopupContainer.popup_loader.body.removeClass('resize').removeAttr('style').hide();
                });
            };

            PopupContainer.sendRequest(options);
        }
    }
};
