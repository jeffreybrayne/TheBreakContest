// Jamroom 5 Tour Module Javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * Stop showing tours to the user
 */
function jrTips_stop_tour()
{
    var url = core_system_url + '/' + jrTips_url + '/stop_tour/__ajax=1';
    $.post(url, function(data) {
        if (typeof data !== "undefined" && data.success) {
            $('.qtip').remove();
            if (typeof __tt != "undefined") {
                var _tmp = __tt.split(',');
                for (var i = 0; i < _tmp.length; i++) {
                    $(_tmp[i]).qtip('hide').qtip('destroy', true);
                }
            }
            alert(data.success);
            window.location.reload();
        }
        else {
            alert(data.error);
        }
    });
}

/**
 * Close an in progress tour
 * @param module string Module for tips being closed
 * @param modal int set to 1 to close open modal window
 */
function jrTips_close_tour(module, modal)
{
    $('.qtip').remove();
    if (typeof __tt != "undefined") {
        var _map = jrReadCookie('jrTips_hide');
        if (typeof _map != "undefined" && _map != null) {
            _map = jQuery.parseJSON(_map);
        }
        else {
            _map = {};
        }
        var _tmp = __tt.split(',');
        for (var i = 0; i < _tmp.length; i++) {
            $(_tmp[i]).qtip('hide').qtip('destroy', true);
        }
        _map[module] = 1;
        jrSetCookie('jrTips_hide', JSON.stringify(_map), 28);
        if (modal == 1) {
            $.modal.close();
        }
        return true;
    }
    return false;
}

/**
 * Close a tip but don't turn off tours
 */
function jrTips_close_tip()
{
    $('.qtip').remove();
    if (typeof __tt != "undefined") {
        var _tmp = __tt.split(',');
        for (var i = 0; i < _tmp.length; i++) {
            $(_tmp[i]).qtip('hide').qtip('destroy', true);
        }
    }
    return true;
}

/**
 * Restart a Tips tour for a module
 * @param module string Module to restart Tips for
 * @param url string URL to load to start Tour
 */
function jrTips_restart_tour(module, url)
{
    var _map = jrReadCookie('jrTips_hide');
    if (typeof _map != "undefined" && _map != null) {
        _map = jQuery.parseJSON(_map);
        if (typeof _map[module] !== "undefined") {
            delete _map[module];
        }
        jrSetCookie('jrTips_hide', JSON.stringify(_map), 28);
    }
    jrCore_window_location(url);
}

/**
 * Show a YouTube Video in a Modal Window
 * @param s string QTip selector
 * @param id string YouTube video ID
 */
function jrTips_play_youtube(s, id)
{
    $(s + ' .qtip').remove();
    if (typeof __tt != "undefined") {
        var _tmp = __tt.split(',');
        for (var i = 0; i < _tmp.length; i++) {
            if (s == _tmp[i]) {
                $(_tmp[i]).qtip('destroy', true);
            }
        }
    }
    $('#y' + id).modal()
}
