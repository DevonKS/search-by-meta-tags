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

    if (parseInt(filter_type) === 0) {
        NS.show_exists_filter_controls();
    }
    else if (parseInt(filter_type) === 1) {
        NS.show_text_filter_controls();
    }
    else if (parseInt(filter_type) === 2) {
        NS.show_numeric_filter_controls();
    }
};

NS.show_exists_filter_controls = function () {
    var exists_radio_button_label = Y.Node.create('<label for="exists_radio_button">Exists</label>');
    var exists_radio_button = Y.Node.create('<input type="radio" name="exists" value="exists" id="exists_radio_button">');

    var not_exists_radio_button_label = Y.Node.create('<label for="not_exists_radio_button">Not Exists</label>');
    var not_exists_radio_button = Y.Node.create('<input type="radio" name="exists" value="doesn\'t exist" id="not_exists_radio_button">');

    var submit_button = Y.Node.create('<button>Save Filter</button>');
    submit_button.on('click', function(){
        var filter_text = '"' + Y.one('#filter_combobox').get('value') + '"';
        if (exists_radio_button.get('checked')) {
            filter_text += ' ' + exists_radio_button.get('value');
        }

        if (not_exists_radio_button.get('checked')) {
            filter_text +=  ' ' + not_exists_radio_button.get('value');
        }

        filter_text += ' ExistsFilter';

        Y.one('#current_filters').append(filter_text + '&#13;&#10;');
    });

    Y.one('#filter_type_controls').append(exists_radio_button);
    Y.one('#filter_type_controls').append(exists_radio_button_label);
    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(not_exists_radio_button);
    Y.one('#filter_type_controls').append(not_exists_radio_button_label);
    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(submit_button);
};

NS.show_text_filter_controls = function () {
    var contain_label = Y.Node.create('<label for="contains">Contains</label>');
    var contains = Y.Node.create('<input type="text" name="contains" id="contains" />');

    var not_contain_label = Y.Node.create('<label for="doesn\'t contain">Doesn\'t Contain</label>');
    var not_contain = Y.Node.create('<input type="text" name="doesn\'t contain" id="doesn\'t contain" />');

    var submit_button = Y.Node.create('<button>Save Filter</button>');
    submit_button.on('click', function(){
        var filter_text = '"' + Y.one('#filter_combobox').get('value') + '"';
        var contains_text = contains.get('value');
        var not_contain_text = not_contain.get('value');

        if (contains_text !== '') {
            filter_text += ' contains "' + contains_text + '"';
        }
        else {
            filter_text += ' doesn\'t contain "' + not_contain_text + '"';
        }

        filter_text += ' TextFilter';
        Y.one('#current_filters').append(filter_text + '&#13;&#10;');
    });

    Y.one('#filter_type_controls').append(contain_label);
    Y.one('#filter_type_controls').append(contains);
    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(not_contain_label);
    Y.one('#filter_type_controls').append(not_contain);
    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(submit_button);
};

NS.show_numeric_filter_controls = function () {
    var combobox = Y.Node.create('<select name ="locfilter"></select>');
    var choose_option = Y.Node.create('<option value="0">Choose...</option>');
    var less_than_option = Y.Node.create('<option value="<"><</option>');
    var equals_option = Y.Node.create('<option value="=">=</option>');
    var greater_than_option = Y.Node.create('<option value=">">></option>');

    combobox.append(choose_option);
    combobox.append(less_than_option);
    combobox.append(equals_option);
    combobox.append(greater_than_option);

    var value = Y.Node.create('<input name="locvalue" />');

    var submit_button = Y.Node.create('<button>Save Filter</button>');
    submit_button.on('click', function() {
        var filter_text = '"' + Y.one('#filter_combobox').get('value') + '"';
        var operator = combobox.get('value');
        var number = value.get('value');

        filter_text += ' ' + operator + ' ' + number + ' NumberFilter';
        Y.one('#current_filters').append(filter_text + '&#13;&#10;');
    });

    Y.one('#filter_type_controls').append(combobox);
    Y.one('#filter_type_controls').append(value);
    Y.one('#filter_type_controls').append('<br>');
    Y.one('#filter_type_controls').append(submit_button);
};