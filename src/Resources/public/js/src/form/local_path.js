// model field
var FormLocalPathModelField = function(path, button, controller) {
    this.path = path;
    this.button = button;
    this.popup = null;

    var that = this;
    this.button.click(function() {
        if (that.popup) {
            that.change();
        } else {
            controller.getPopup(that, function(popup) {
                that.popup = popup;
                that.change();
            })
        }
    });
    this.path.change(function() {
        that.correctPath();
    }).change();
};
FormLocalPathModelField.prototype = {
    change: function() {
        this.correctPath();
        this.popup.change(this.path.val());
        this.popup.show();
    },
    // correct the end symbol of path
    correctPath: function() {
        var value = this.path.val();
        if (value.length && !(/[\\\/]$/.test(value))) {
            if (value[0] == '/') {
                this.path.val(value += '/');
            } else {
                this.path.val(value += '\\');
            }
        }
        // if the root folder is set then the path must always start with him
        var root = this.path.attr('data-root');
        if (root) {
            if (!value.length || value.indexOf(root) !== 0) {
                this.path.val(root);
            }
        }
    }
};

// model folder
var FormLocalPathModelFolder = function(folder, path) {
    this.path = path;
    this.popup = null;

    var that = this;
    this.folder = folder.click(function() {
        that.select();
        return false;
    });
};
FormLocalPathModelFolder.prototype = {
    select: function() {
        this.popup.change(this.folder.attr('href'));
    },
    setPopup: function(popup) {
        this.popup = popup;
    }
};

// model pop-up
var FormLocalPathModelPopup = function(popup, path, letter, button, folders, prototype, field) {
    this.popup = popup;
    this.path = path;
    this.letter = letter;
    this.button = button;
    this.field = field;
    this.form = popup.body.find('form');
    this.folders = folders;
    this.folder_prototype = prototype;
    this.folder_models = [];

    var that = this;
    this.popup.hide();
    // apply chenges
    this.button.click(function() {
        that.apply();
        return false;
    });
    if (this.letter) {
        this.letter.change(function() {
            that.change(that.letter.val()+':\\');
        });
    }
};
FormLocalPathModelPopup.prototype = {
    show: function() {
        // unbund old hendlers and bind new
        var that = this;
        this.form.unbind('submit').bind('submit', function() {
            that.change();
            return false;
        });
        this.path.unbind('change keyup').bind('change keyup', function() {
            that.change();
            return false;
        });
        // show popup
        this.popup.show();
    },
    change: function(value) {
        if (typeof(value) !== 'undefined') {
            this.path.val(value);
        }
        // return if not full path
        if (this.path.val()) {
            // if the root folder is set then the path must always start with him
            var root = this.field.path.attr('data-root');
            if (root) {
                if (this.path.val().indexOf(root) !== 0) {
                    this.path.val(root);
                }
            }
            if (!(/[\\\/]$/.test(this.path.val()))) {
                return false;
            }
        }

        // start updating
        this.popup.body.addClass('updating');

        var that = this;
        // send form as ajax
        this.form.ajaxSubmit({
            dataType: 'json',
            data: {'root': that.field.path.attr('data-root')},
            success: function(data) {
                that.path.val(data.path);
                // remove old folders
                that.clearFoldersList();

                // create folders
                for (var i in data.folders) {
                    // prototype of new item
                    var new_item = that.folder_prototype
                        .replace('__name__', data.folders[i].name)
                        .replace('__link__', data.folders[i].path);
                    that.addFolder(new FormLocalPathModelFolder($(new_item), that.path));
                }
            },
            error: function(data, error, message) {
                alert(message);
            },
            complete: function() {
                that.popup.body.removeClass('updating');
            }
        });
    },
    clearFoldersList: function() {
        this.folder_models = [];
        this.folders.text('');
    },
    addFolder: function(folder) {
        folder.setPopup(this);
        this.folder_models.push(folder);
        this.folders.append(folder.folder);
    },
    apply: function() {
        this.field.path.val(this.path.val());
        this.popup.hide();
    }
};
// Form local path controller
var FormLocalPathController = function(path) {
    // create field model
    new FormLocalPathModelField(
        path.find('input'),
        path.find('.change-path'),
        this
    );
};
FormLocalPathController.prototype = {
    getPopup: function(field, init) {
        init = init || function() {};
        // on load popup
        var init_popup = function (popup) {
            var folders = popup.body.find('.folders');
            // create model
            popup = new FormLocalPathModelPopup(
                popup,
                popup.body.find('#local_path_popup_path'),
                popup.body.find('#local_path_popup_letter'),
                popup.body.find('.change-path'),
                folders,
                folders.data('prototype'),
                field
            );
            init(popup);
        };

        // create popup
        if (popup = PopupContainer.get('local-path')) {
            init_popup(popup);
        } else {
            PopupContainer.load('local-path', {
                url: field.path.closest('.f-local-path').data('popup'),
                success: init_popup
            });
        }
    }
};
