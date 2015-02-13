// Jamroom 5 Support Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Load support options
 */
function jrSupport_view_options(type, name)
{
    if (typeof name !== "undefined" && name.length > 0) {
        $('#'+ type +'_submit_indicator').show(300, function () {
            $.get(core_system_url + '/' + jrSupport_url + '/options/'+ type +'/'+ name +'/__ajax=1', function(data) {
                $('#'+ type +'_submit_indicator').hide();
                $('#'+ type +'_info').html(data).slideDown(300);
            });
        });
    }
}