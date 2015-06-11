/**
 * Created by Devon on 6/4/2015.
 */

M.local_searchbytags = M.local_searchbytags || {};
var NS = M.local_searchbytags.filter = {};

NS.init = function() {
    console.log('filter JS started');
    Y.one('#filter_name').on('textInput', this.filter_combobox);

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
        var exists_radio_button_label = Y.Node.create('<label for="exists_radio_button">Exists</label>');
        var exists_radio_button = Y.Node.create('<input type="radio" name="exists" value="exists" id="exists_radio_button">');

        var not_exists_radio_button_label = Y.Node.create('<label for="not_exists_radio_button">Not Exists</label>');
        var not_exists_radio_button = Y.Node.create('<input type="radio" name="exists" value="not exists" id="not_exists_radio_button">');

        var submit_button = Y.Node.create('<button>Save Filter</button>');
        submit_button.on('click', function(){
            var filter_text = Y.one('#filter_combobox').get('value') + ' tag';
            if (exists_radio_button.get('checked')) {
                filter_text += ' ' + exists_radio_button.get('value');
            }

            if (not_exists_radio_button.get('checked')) {
                filter_text +=  ' ' + not_exists_radio_button.get('value');
            }

            Y.one('#current_filters').append(filter_text + '&#13;&#10;');
        });

        Y.one('#filter_type_controls').append(exists_radio_button);
        Y.one('#filter_type_controls').append(exists_radio_button_label);
        Y.one('#filter_type_controls').append('<br>');
        Y.one('#filter_type_controls').append(not_exists_radio_button);
        Y.one('#filter_type_controls').append(not_exists_radio_button_label);
        Y.one('#filter_type_controls').append('<br>');
        Y.one('#filter_type_controls').append(submit_button);
    }
};