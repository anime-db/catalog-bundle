// Model Field
var FormImageModelField = function(field, image, button, controller) {
    this.field = field;
    this.image = image;
    this.popup = null;
    var that = this;
    this.button = button.click(function() {
        if (that.popup) {
            that.change();
        } else {
            controller.getPopup(that, function(popup) {
                that.popup = popup;
                that.change();
            });
        }
    });
};
FormImageModelField.prototype = {
    change: function() {
        this.popup.show();
    },
    // update field data
    update: function(data) {
        this.field.val(data.path);
        this.image.attr('src', data.image);
    }
};
// Model Popup
var FormImageModelPopup = function(popup, remote, local, field) {
    this.remote = remote;
    this.local = local;
    this.popup = popup;
    this.field = field;
    this.form = popup.body.find('form');
    this.popup.hide();
};
FormImageModelPopup.prototype = {
    show: function() {
        // unbund old hendlers and bind new
        var that = this;
        this.form.unbind('submit').bind('submit', function() {
            that.upload();
            return false;
        });
        // show popup
        this.popup.show();
    },
    upload: function() {
        var that = this;
        // send form as ajax and call onUpload handler
        this.form.ajaxSubmit({
            dataType: 'json',
            success: function(data) {
                that.field.update(data);
                that.popup.hide();
                that.form.resetForm();
            },
            error: function(data, error, message) {
                // for normal error
                if (data.status == 404) {
                    data = JSON.parse(data.responseText);
                    if (typeof(data.error) !== 'undefined' && data.error) {
                        message = data.error;
                    }
                }
                alert(message);
            }
        });
    }
};
// Image controller
var FormImageController = function(image) {
    new FormImageModelField(
        image.find('input'),
        image.find('img'),
        image.find('.change-button'),
        this
    );
};
FormImageController.prototype = {
    getPopup: function(field, init) {
        init = init || function() {};
        // on load popup
        var init_popup = function (popup) {
            // create model
            popup = new FormImageModelPopup(
                popup,
                $('#image-popup-remote'),
                $('#image-popup-local'),
                field
            );
            init(popup);
        };

        // create popup
        if (popup = PopupContainer.get('image')) {
            init_popup(popup);
        } else {
            PopupContainer.load('image', {
                url: field.field.closest('.f-image').data('popup'),
                success: init_popup
            });
        }
    }
};
