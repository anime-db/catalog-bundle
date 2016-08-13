/**
 * Cap for block site
 */
var Cap = {
    element: null,
    button: null,
    observers: [],
    html: null,
    setElement: function(element) {
        Cap.element = element;
        if (!Cap.button) {
            Cap.setButton(element);
        }
    },
    setButton: function(button) {
        if (Cap.button) {
            Cap.button.off('click.cap');
        }
        Cap.button = button.on('click.cap', function() {
            Cap.hide();
        });
    },
    getHtml: function() {
        if (Cap.html === null) {
            Cap.html = $('html');
        }
        return Cap.html;
    },
    // hide cup and observers
    hide: function(observer) {
        if (typeof(observer) !== 'undefined') {
            observer.hide();
        } else {
            for (var i in Cap.observers) {
                Cap.observers[i].hide();
            }
        }
        Cap.element.hide();
        Cap.getHtml().removeClass('scroll-lock');
    },
    // show cup and observers
    show: function(observer) {
        Cap.element.show();
        observer.show();
        Cap.getHtml().addClass('scroll-lock');
    },
    // need methods 'show' and 'hide'
    registr: function(observer) {
        Cap.observers.push($.extend({
            show: function() {},
            hide: function() {}
        }, observer));
    },
    unregistr: function(observer) {
        for (var i in Cap.observers) {
            if (Cap.observers[i] === observer) {
                Cap.observers.splice(i, 1);
            }
        }
    }
};
