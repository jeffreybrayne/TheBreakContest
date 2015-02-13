// Jamroom 5 Graph Module Javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * Display Graph Browser in modal window
 */
function jrGraph_modal_graph(id, module_url, name, modal)
{
    var url = core_system_url + '/' + module_url + '/graph/' + jrE(name) +'/__ajax=1';
    $(id).modal();
    $.get(url, function(r) {
        if (r.indexOf('plothover') !== -1) {
            $(id).html(r);
        }
        else {
            $.modal.close();
            alert(r);
        }
    });
    return false;
}