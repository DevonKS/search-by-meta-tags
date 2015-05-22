/**
 * Created by Devon on 5/22/2015.
 */
M.local_searchbytags={
    Y : null,
    transaction : [],
    filter_combobox : function(){
        var text = Y.one('#filter_name').value();

        var combo_box = Y.one('#filter_combobox');

        var valid_options = combo_box.filter(function (node) {
            return node.value().startsWith(text);
        });

        console.log(valid_options);
        combo_box.setHTML(valid_options);
    },
    init : function(Y){
        console.log("Filter JS Started");
        console.log("Rubbish");
        this.Y = Y;
        console.log(Y.one('#test_101'));

        //Y.one(Y.config.doc).delegate('click', function() {
        //    console.log("Rubbish");
        //}, '#test_101');
    },
    rubbish: "Rubbish"
};