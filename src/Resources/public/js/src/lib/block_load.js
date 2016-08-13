/**
 * Notifies all observers of change
 */
var BlockLoadHandler = function() {
    this.observers = [];
};
BlockLoadHandler.prototype = {
    registr: function(observer) {
        if (typeof(observer.update) == 'function') {
            this.observers.push(observer);
        } else if (typeof(observer) == 'function') {
            this.observers.push({update:observer});
        }
    },
    unregistr: function(observer) {
        for (var i in this.observers) {
            if (this.observers[i] === observer) {
                this.observers.splice(i, 1);
            }
        }
    },
    notify: function(block) {
        for (var i in this.observers) {
            this.observers[i].update(block);
        }
    }
};
