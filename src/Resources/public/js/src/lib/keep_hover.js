/**
 * Keep hover a mouse on the element
 *
 * While maintaining stationary cursor over an element it adds
 * class and removes it after you drag the cursor
 *
 * Takes a timeout from the attribute data-expect or 1000 seconds
 */
var KeepHover = function(el) {
    var that = this;
    this.timer = null;
    this.expect = el.data('expect') ? el.data('expect') : 1000;
    this.el = el.hover(function() {
        that.startTimer();
    }, function() {
        that.stopTimer();
        that.removeKeep();
    }).mousemove(function() {
        that.stopTimer();
        that.startTimer();
    });
};
KeepHover.prototype = {
    startTimer: function() {
        var that = this;
        this.timer = setTimeout(function() {
            that.setKeep();
        }, this.expect);
    },
    stopTimer: function() {
        clearTimeout(this.timer);
    },
    setKeep: function() {
        this.el.addClass('keep-hover')
    },
    removeKeep: function() {
        this.el.removeClass('keep-hover')
    }
};
