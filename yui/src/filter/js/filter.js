/**
 * Created by Devon on 6/4/2015.
 */

M.local_searchbytags = M.local_searchbytags || {};
var NS = M.local_searchbytags.filter = {};

NS.init = function() {
    Y.one('#filter_name').on('input', this.filter_combobox);
    this.originals = Y.all('#filter_combobox > *');

    Y.one('#filter_type').on('change', this.show_filter_controls);
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

    if (parseInt(filter_type) === 0) {
        var checkbox_label = Y.Node.create('<label for="exists_checkbox">Exists</label>');
        var checkbox = Y.Node.create('<input type="checkbox" name="exists" value="exists" id="exists_checkbox">');
        Y.one('#filter_controls').append(checkbox_label);
        Y.one('#filter_controls').append(checkbox);
    }
};