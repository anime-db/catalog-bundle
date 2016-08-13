//Model collection
var FormCollection = function(collection, button_add, rows, remove_selector, handler) {
    var that = this;
    this.collection = collection;
    this.index = rows.length;
    this.rows = [];
    this.remove_selector = remove_selector;
    this.button_add = button_add.click(function() {
        that.add();
    });
    this.row_prototype = collection.data('prototype');
    this.handler = handler;
    for (var i = 0; i < rows.length; i++) {
        var row = new FormCollectionRow($(rows[i]));
        row.setCollection(this);
        this.rows.push(row);
    }
};
FormCollection.prototype = {
    add: function() {
        var row = new FormCollectionRow($(this.row_prototype.replace(/__name__(label__)?/g, this.index + 1)));
        this.addRowObject(row);
        return row;
    },
    addRowObject: function(row) {
        row.setCollection(this);
        // notify observers
        this.handler.notify(row.row);
        // add row
        this.rows.push(row);
        this.button_add.parent().before(row.row);
        // increment index
        this.index++;
    }
};
// Model collection row
var FormCollectionRow = function(row) {
    this.row = row;
    this.collection = null;
};
FormCollectionRow.prototype = {
    remove: function() {
        this.row.remove();
        var rows = [];
        // remove row in collection
        for (var i = 0; i < this.collection.rows.length; i++) {
            if (this.collection.rows[i] !== this) {
                rows.push(this.collection.rows[i]);
            }
        }
        this.collection.rows = rows;
    },
    setCollection: function(collection) {
        this.collection = collection;
        // add handler for remove button
        var that = this;
        this.row.find(collection.remove_selector).click(function() {
            that.remove();
        });
    }
};

var FormCollectionContainer = function() {
    this.collections = [];
};
FormCollectionContainer.prototype = {
    add: function(collection) {
        this.collections[collection.collection.attr('id')] = collection;
    },
    get: function(name) {
        return this.collections[name];
    },
    remove: function(name) {
        delete this.collections[name];
    }
};
