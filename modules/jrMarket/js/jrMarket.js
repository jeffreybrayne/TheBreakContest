// Jamroom 5 Marketplace Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Update all items that have updates
 */
function jrMarket_update_all_items(token)
{
    $('#modal_window').modal();
    $('#modal_indicator').show();

    // Setup our "listener" which will update our work progress
    sid = setInterval(function() {
        $.ajax({
            cache: false,
            dataType: 'json',
            url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + token + '/__ajax=1',
            success: function(tmp, stat, xhr) {
                var fnc = 'jrFormModalSubmit_update_process';
                window[fnc](tmp, sid);
            },
            error: function(r, t, e) {
                clearInterval(sid);
                alert('An error was encountered communicating with the server: ' + t + ': ' + e);
            }
        })
    }, 1000);

    // Do our work
    $.getJSON(core_system_url + '/' + jrMarket_url + '/update_all_items/__ajax=1', function(res, stat, xhr) {
        clearTimeout(sid);
        if (res !== null && typeof res.error !== "undefined") {
            alert(res.error);
        }
    });
}

/**
 * Submits a Quick Purchase
 * @return bool
 */
function jrMarket_quick_purchase(type, price, market_id, item)
{
    $(this).attr("disabled", "disabled").addClass('form_button_disabled');
    var iid = '#fsi_' + market_id;
    $(iid).show(300, function() {
        var values = {
            type: type,
            price: price,
            market_id: market_id,
            item: item
        };
        var url = core_system_url + '/' + jrMarket_url + '/purchase/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: values,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                // Check for error
                if (typeof msg.error !== "undefined") {
                    $(iid).hide(300, function() {
                        alert(msg.error);
                    });
                }
                else {
                    url = core_system_url + '/' + jrMarket_url + '/install_item/' + type + '/' + msg.name + '/license=' + msg.license + '/__ajax=1';
                    jrCore_set_csrf_cookie(url);
                    $.getJSON(url, function(res) {
                        // Check for error
                        if (typeof res.error !== "undefined") {
                            $(iid).hide(300, function() {
                                alert(res.error);
                            });
                        }
                        else {
                            if (typeof res.redirect !== "undefined") {
                                // We had an error
                                window.location.reload();
                            }
                            else {
                                window.location = res.url;
                            }
                        }
                    });
                }
            },
            error: function(x, t, e) {
                $(iid).hide(300, function() {
                    alert('unable to communicate with server - please try again');
                });
            }
        });
    });
    return false;
}

/**
 * Install a Free Item
 * @return bool
 */
function jrMarket_install_item(type, market_id, item) {
    $(this).attr("disabled", "disabled").addClass('form_button_disabled');
    var iid = '#fsi_' + market_id;
    var mid = Number(market_id);
    $(iid).show(300, function() {
        var url = core_system_url + '/' + jrMarket_url + '/license_item/' + type + '/' + item + '/' + mid +'/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.getJSON(url, function(msg) {
            // Check for error
            if (typeof msg.error !== "undefined") {
                $(iid).hide(300, function() {
                    alert(msg.error);
                });
            }
            else if (typeof msg.license !== "undefined" && msg.license.length > 0) {
                url = core_system_url + '/' + jrMarket_url + '/install_item/' + type + '/' + item + '/' + mid + '/license=' + msg.license + '/__ajax=1';
                jrCore_set_csrf_cookie(url);
                $.getJSON(url, function(res) {
                    // Check for error
                    if (typeof res.error !== "undefined") {
                        $(iid).hide(300, function() {
                            alert(res.error);
                        });
                    }
                    else {
                        if (typeof res.redirect !== "undefined") {
                            // We had an error
                            window.location.reload();
                        }
                        else {
                            window.location = res.url;
                        }
                    }
                });
            }
            else {
                alert('Unable to retrieve a license for the item - please try again');
            }
        });
    });
    return false;
}

/**
 * Update/Reload an existing item
 * @return bool
 */
function jrMarket_update_item(type, item, reload, market_id) {
    var bid = '#u' + item;
    var img = $(bid).prev('img');
    $(bid).fadeOut(300, function() {
        $('input').attr("disabled", "disabled").addClass('form_button_disabled');
        $(img).show(300, function() {
            var url = core_system_url + '/' + jrMarket_url + '/update_item/' + type + '/' + item;
            if (typeof reload != "undefined" && reload == 'reload') {
                url = url + '/reload';
            }
            url = url + '/id=' + Number(market_id) + '/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.getJSON(url, function(res) {
                window.location = res.url;
            });
        });
    });
    return false;
}
