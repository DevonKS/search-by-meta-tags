/**
 * Created by Devon on 6/4/2015.
 */

M.local_searchbytags = M.local_searchbytags || {};
var NS = M.local_searchbytags.filter = {};

NS.init = function() {
    console.log('filter JS started');
    Y.one(Y.config.doc).delegate('input', this.filter_combobox, '#filter_name');

    Y.one('#filter_type').on('change', this.show_filter_controls);
    this.originals = Y.all('#filter_combobox > *');
};

NS.filter_combobox = function() {
    var text = Y.one('#filter_name').get('value');
    var combo_box = Y.all('#filter_combobox');

    combo_box.setHTML('');
    combo_box.append(NS.originals);

    Y.all('#filter_combobox > *').each(function (node) {
        if (!node.get('value').startsWith(text)) {
            node.remove();
        }
    });
};

NS.show_filter_controls = function() {
    var filter_type = Y.one('#filter_type').get('value');

    Y.all('#filter_type_controls > *').remove();

    if (filter_type === "exists") {
        NS.show_exists_filter_controls('exists');
    }
    else if (filter_type === "not exist") {
        NS.show_exists_filter_controls('doesn\'t exist');
    }
    else if (filter_type === "contains") {
        NS.show_text_filter_controls('contains');
    }
    else if (filter_type === "not contain") {
        NS.show_text_filter_controls('doesn\'t contain');
    }
    else if (filter_type === "greater than") {
        NS.show_numeric_filter_controls('>');
    }
    else if (filter_type === "greater than equal") {
        NS.show_numeric_filter_controls('>=');
    }
    else if (filter_type === "less than") {
        NS.show_numeric_filter_controls('<');
    }
    else if (filter_type === "less than equal") {
        NS.show_numeric_filter_controls('<=');
    }
    else if (filter_type === "equal") {
        NS.show_numeric_filter_controls('=');
    }
};

NS.show_exists_filter_controls = function (operator) {
    var submit_button = Y.Node.create('<button>Save Filter</button>');
    submit_button.on('click', function(){
        var filter_text = '"' + Y.one('#filter_combobox').get('value') + '" ' + operator + ' ExistsFilter\n';

        var current_filters = Y.one('#current_filters').get('value');
        Y.one('#current_filters').set('value', current_filters + filter_text);
    });

    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(submit_button);
};

NS.show_text_filter_controls = function (operator) {
    var label = Y.Node.create('<label for="value">' + operator + '</label>');
    var value = Y.Node.create('<input type="text" name="value" id="value" />');

    var submit_button = Y.Node.create('<button>Save Filter</button>');
    submit_button.on('click', function(){
        var filter_text = '"' + Y.one('#filter_combobox').get('value') +
                          '" ' + operator + ' "' + value.get('value') + '" TextFilter\n';

        var current_filters = Y.one('#current_filters').get('value');
        Y.one('#current_filters').set('value', current_filters + filter_text);
    });

    Y.one('#filter_type_controls').append(label);
    Y.one('#filter_type_controls').append(value);
    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(submit_button);
};

NS.show_numeric_filter_controls = function (operator) {
    var value = Y.Node.create('<input name="value" />');

    var submit_button = Y.Node.create('<button>Save Filter</button>');
    submit_button.on('click', function() {
        var filter_text = '"' + Y.one('#filter_combobox').get('value') +
                          '" ' + operator + ' ' + value.get('value') + ' NumberFilter\n';

        var current_filters = Y.one('#current_filters').get('value');
        Y.one('#current_filters').set('value', current_filters + filter_text);
    });

    Y.one('#filter_type_controls').append(value);
    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(submit_button);
};