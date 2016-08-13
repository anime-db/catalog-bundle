var CheckAllToggle = function(checker, list) {
    this.checker = checker;
    this.list = list;
    var that = this;
    this.checker.click(function(){
        that.change();
    });
    for (var i in this.list) {
        this.list[i].setToggle(this);
    }
};
CheckAllToggle.prototype = {
    change: function() {
        if (this.checker.is(':checked')) {
            this.all();
        } else {
            this.neither();
        }
    },
    all: function() {
        for (var i in this.list) {
            this.list[i].check();
        }
    },
    neither: function() {
        for (var i in this.list) {
            this.list[i].uncheck();
        }
    }
};
// Check all node
var CheckAllNode = function(checkbox) {
    this.checkbox = checkbox;
    this.toggle = null;
    var that = this;
    this.checkbox.click(function(){
        that.change();
    });
};
CheckAllNode.prototype = {
    change: function() {
        if (!this.checkbox.is(':checked') && this.toggle) {
            this.toggle.checker.prop('checked', false);
        }
    },
    check: function() {
        this.checkbox.prop('checked', true);
    },
    uncheck: function() {
        this.checkbox.prop('checked', false);
    },
    setToggle: function(toggle) {
        this.toggle = toggle;
    }
};
// Check all in table
var TableCheckAllController = function(checker) {
    var checkboxes = checker.parents('table').find('.'+checker.data('target'));
    var list = [];
    for (var i = 0; i < checkboxes.length; i++) {
        list.push(new CheckAllNode($(checkboxes[i])));
    }
    new CheckAllToggle(checker, list);
};
