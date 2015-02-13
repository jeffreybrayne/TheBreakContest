/**
 * jrSearch Javascript functions
 * @copyright 2012 Talldude Networks, LLC.
 */

/**
 * Search for results within a specific module
 */
function jrSearch_module_index(url, fields)
{
    var ss = $('#search_module').val();
    if (ss.length > 0) {
        $('#form_submit_indicator').show(300,function() {
            window.location = core_system_url + '/' + url + '/ss=' + jrE(ss);
        });
    }
    return false;
}

/**
 * Display a modal search form
 */
function jrSearch_modal_form()
{
    $('#searchform').modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn(75, function () {
                dialog.container.slideDown(5, function () {
                    dialog.data.fadeIn(75);
                    $('#search_input').focus();
                });
            });
        },
        onClose: function (dialog) {
            dialog.data.fadeOut('fast', function () {
                dialog.container.hide('fast', function () {
                    dialog.overlay.fadeOut('fast', function () {
                        $.modal.close();
                    });
                });
            });
        },
        overlayClose:true
    });
}