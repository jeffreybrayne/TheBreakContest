// Jamroom 5 Core Javascript
// @copyright 2003-2013 by Talldude Networks LLC

var __jrcto = null;

/**
 * Set a page reload timeout on the Dashboard
 * @param seconds
 * @param auto
 */
function jrCore_dashboard_reload_page(seconds, auto)
{
    var id = '#reload';
    var ck = jrReadCookie('dash_reload');
    if (auto == '1' && typeof ck !== "undefined" && ck == 'off') {
        // We are disabled
        return true;
    }
    else if (typeof ck !== "undefined" && ck == 'on') {
        // See if we are reloading...
        if (auto != '1') {
            return jrCore_dashboard_disable_reload(seconds);
        }
    }
    else {
        jrSetCookie('dash_reload', 'on', 30);
    }

    $(id).removeClass('form_button_disabled');
    var d = 0;
    var v = seconds;
    __jrcto = setInterval(function() {
        d += 1;
        v -= 1;
        if (d >= seconds) {
            clearInterval(__jrcto);
            window.location.reload();
        }
        $(id).val(v);
    }, 1000);
}

function jrCore_dashboard_disable_reload(seconds)
{
    jrSetCookie('dash_reload', 'off');
    clearInterval(__jrcto);
    $('#reload').val(seconds).addClass('form_button_disabled');
    return true;
}

/**
 * Config the Dashboard with a custom panel
 * @param id
 * @returns {boolean}
 */
function jrCore_dashboard_panel(id)
{
    var url = core_system_url + '/' + jrCore_url + '/dashboard_panels/' + id +'/__ajax=1';
    $('#db_modal').modal();
    $.get(url, function(r) {
        $('#db_modal').html(r);
    });
    return false;
}

/**
 * Set a panel in the dashboard
 * @param row int Row to set
 * @param col int Column to set
 * @param opt string Function to set
 */
function jrCore_set_dashboard_panel(row, col, opt)
{
    var url = core_system_url + '/' + jrCore_url + '/set_dashboard_panel/row=' + Number(row) + '/col=' + Number(col) +'/opt=' + jrE(opt) + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, function(_msg) {
        if (typeof _msg.error !== "undefined") {
            alert(_msg.error);
        }
        else {
            $.modal.close();
            window.location.reload();
        }
    });
}

/**
 * Set number of rows for pagination
 * @param num
 * @param callback
 */
function jrCore_set_pager_rows(num, callback)
{
    jrSetCookie('jrcore_pager_rows', num, 30);
    return callback();
}

/**
 * Set CSRF location cookie
 * @param url
 * @returns {boolean}
 */
function jrCore_set_csrf_cookie(url)
{
    return jrSetCookie('jr_location_url', url, 1);
}

/**
 * Set location CSRF cookie and redirect
 * @param url
 */
function jrCore_window_location(url)
{
    jrCore_set_csrf_cookie(url);
    window.location = url;
}

/**
 * Creates a checkbox in form to prevent spam bots from submitting forms
 * @param {string} name Name of checkbox element to add
 * @param {number} idx Tab Index value for form
 * @return bool
 */
function jrFormSpamBotCheckbox(name,idx)
{
    $('#sb_'+ name).html('<input type="checkbox" id="'+ name +'" name="'+ name +'" tabindex="'+ idx +'">');
    return true;
}

/**
 * Handle Stream URL Errors from the Media Player
 * @param error object jPlayer error response object
 * @return bool
 */
function jrCore_stream_url_error(error)
{
    if (error.jPlayer.error.type == 'e_url') {
        // Get module_url from media URL
        var _tm = error.jPlayer.error.context.replace(core_system_url +'/', '').split('/');
        var url = _tm[0];
        $.get(core_system_url + '/' + jrCore_url +'/stream_url_error/'+ url +'/__ajax=1', function(res) {
            if (typeof res.error != "undefined" && res.error !== null) {
                alert(res.error);
            }
        });
    }
    return true;
}

/**
 * Submits a form handling validation
 * @param {string} form_id Form ID to submit
 * @param {string} vkey MD5 checksum for validation key
 * @param {string} method ajax/modal/post - post form as an AJAX form or normal (post) form
 */
function jrFormSubmit(form_id,vkey,method)
{
    var msg_id = form_id +'_msg';
    var retval = false;
    $('.field-hilight').removeClass('field-hilight');
    $('.form_submit_section input').attr("disabled","disabled").addClass('form_button_disabled');
    $('#form_submit_indicator').show(300,function() {

        var timeout = setTimeout(function() {
            // get all the inputs into an array.
            $('.form_editor').each(function(index) {
                $('#'+ this.name +'_editor_contents').val(tinyMCE.get('e' + this.name).getContent());
            });
            var values = $(form_id).serializeArray();
            // See if we have saved off entries on load
            if (typeof values !== "object" || values.length === 0) {
                $('#form_submit_indicator').hide(300,function() {
                    $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(msg_id,"Unable to serialize form elements for submitting!");
                });
                clearTimeout(timeout);
                return false;
            }
            var action = $(form_id).attr("action");
            if (typeof action === "undefined") {
                $('#form_submit_indicator').hide(300,function() {
                    $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(msg_id,"Unable to retrieve form action value for submitting");
                });
                clearTimeout(timeout);
                return false;
            }

            // Handle form validation
            if (typeof vkey !== "undefined" && vkey !== null) {

                // Submit URL for validation
                $.ajax({
                    type: 'POST',
                    data: values,
                    cache: false,
                    dataType: 'json',
                    url: core_system_url +'/'+ jrCore_url +'/form_validate/__ajax=1',
                    success: function(_msg) {
                        // Handle any messages
                        if (typeof _msg === "undefined" || _msg === null) {
                            $('#form_submit_indicator').hide(300,function() {
                                $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
                                jrFormSystemError(msg_id,'Empty response received from server - please try again');
                            });
                        }
                        else if (typeof _msg.OK === "undefined" || _msg.OK != '1') {
                            if (typeof _msg.redirect != "undefined") {
                                clearTimeout(timeout);
                                window.location = _msg.redirect;
                                return true;
                            }
                            jrFormMessages(msg_id,_msg);
                        }
                        else {
                            // _msg is "OK" - looks OK to submit now
                            if (typeof method == "undefined" || method == "ajax") {
                                $.ajax({
                                    type: 'POST',
                                    url: action +'/__ajax=1',
                                    data: values,
                                    cache: false,
                                    dataType: 'json',
                                    success: function(_pmsg) {
                                        // Check for URL redirection
                                        if (typeof _pmsg.redirect != "undefined") {
                                            window.location = _pmsg.redirect;
                                        }
                                        else {
                                            jrFormMessages(msg_id,_pmsg);
                                        }
                                        retval = true;
                                    },
                                    error: function(x,t,e) {
                                        $('#form_submit_indicator').hide(300,function() {
                                            $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
                                            // See if we got a message back from the core
                                            var msg = 'a system level error was encountered trying to validate the form values: '+ t +': '+ e;
                                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                                msg = 'JSON response error: '+ x.responseText;
                                            }
                                            jrFormSystemError(msg_id,msg);
                                        });
                                    }
                                });
                            }

                            // Modal window
                            else if (method == "modal") {

                                $('#form_submit_indicator').hide(600,function() {
                                    var k = $('#jr_html_modal_token').val();
                                    var n = 0;
                                    $('#modal_window').modal();
                                    $('#modal_indicator').show();

                                    // Setup our "listener" which will update our work progress
                                    sid = setInterval(function() {
                                        $.ajax({
                                            cache: false,
                                            dataType: 'json',
                                            url: core_system_url +'/'+ jrCore_url +'/form_modal_status/k='+ k +'/__ajax=1',
                                            success: function(tmp,stat,xhr) {
                                                n = 0;
                                                var fnc = 'jrFormModalSubmit_update_process';
                                                window[fnc](tmp,sid);
                                            },
                                            error: function(r,t,e) {
                                                // Track errors - if we get to 10 we error out
                                                n++;
                                                if (n > 10) {
                                                    clearInterval(sid);
                                                    alert('An error was encountered communicating with the server: ' + t + ': ' + e);
                                                }
                                            }
                                        })
                                    },1000);

                                    // Submit form
                                    $.ajax({
                                        type: 'POST',
                                        url: action +'/__ajax=1',
                                        data: values,
                                        cache: false,
                                        dataType: 'json',
                                        success: function(_pmsg) {
                                            clearTimeout(timeout);
                                            return true;
                                        }
                                    });
                                });
                            }

                            // normal POST submit
                            else {
                                $(form_id).submit();
                                retval = true;
                            }
                        }
                    },
                    error: function(x,t,e) {
                        $('#form_submit_indicator').hide(300,function() {
                            $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
                            // See if we got a message back from the core
                            var msg = 'a system level error was encountered trying to validate the form values: '+ t +': '+ e;
                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                msg = 'JSON response error: '+ x.responseText;
                            }
                            jrFormSystemError(msg_id,msg);
                        });
                    }
                });
            }
            // No validation
            else {

                // AJAX or normal submit?
                if (typeof method == "undefined" || method == "ajax") {
                    $.ajax({
                        type: 'POST',
                        url: action +'/__ajax=1',
                        data: values,
                        cache: false,
                        dataType: 'json',
                        success: function(_msg) {
                            // Check for URL redirection
                            if (typeof _msg.redirect != "undefined") {
                                window.location = _msg.redirect;
                            }
                            else {
                                jrFormMessages(msg_id,_msg);
                            }
                            retval = true;
                        },
                        error: function(x,t,e) {
                            $('#form_submit_indicator').hide(300,function() {
                                $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
                                // See if we got a message back from the core
                                var msg = 'a system level error was encountered trying to validate the form values: '+ t +': '+ e;
                                if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                    msg = 'JSON response error: '+ x.responseText;
                                }
                                jrFormSystemError(msg_id,msg);
                            });
                        }
                    });
                }

                // Modal window
                else if (method == "modal") {

                    $('#form_submit_indicator').hide(600,function() {
                        var k = $('#jr_html_modal_token').val();
                        var n = 0;
                        $('#modal_window').modal();
                        $('#modal_indicator').show();
                        // Setup our "listener" which will update our work progress
                        sid = setInterval(function() {
                            $.ajax({
                                cache: false,
                                dataType: 'json',
                                url: core_system_url +'/'+ jrCore_url +'/form_modal_status/k='+ k +'/__ajax=1',
                                success: function(tmp,stat,xhr) {
                                    n = 0;
                                    var fnc = 'jrFormModalSubmit_update_process';
                                    window[fnc](tmp,sid);
                                },
                                error: function(r,t,e) {
                                    // Track errors - if we get to 10 we error out
                                    n++;
                                    if (n > 10) {
                                        clearInterval(sid);
                                        alert('An error was encountered communicating with the server: ' + t + ': ' + e);
                                    }
                                }
                            })
                        },1000);

                        // Submit form
                        $.ajax({
                            type: 'POST',
                            url: action +'/__ajax=1',
                            data: values,
                            cache: false,
                            dataType: 'json',
                            success: function(_pmsg) {
                                clearTimeout(timeout);
                                return true;
                            }
                        });
                    });
                }

                else {
                    $(form_id).submit();
                    retval = true;
                }
            }
            clearTimeout(timeout);
            return retval;
        }, 500);
    });
}

/**
 * jrFormSystemError
 */
function jrFormSystemError(msg_id,text)
{
    jrFormMessages(msg_id,{"notices":[{'type':'error','text':text}]});
}

/**
 * jrFormMessages
 */
function jrFormMessages(msg_id,_msg)
{
    var rval = true;
    $('.page-notice-shown').hide(10);
    // Handle any messages
    if (typeof _msg.notices != "undefined") {
        for (var n in _msg.notices) {
            if (!_msg.notices.hasOwnProperty(n)) {
                continue;
            }
            $(msg_id).html(_msg.notices[n].text);
            $(msg_id).removeClass("error success warning notice").addClass(_msg.notices[n].type);
            if (_msg.notices[n].type == 'error') {
                rval = false;
            }
        }
    }
    // Handle any error fields
    if (typeof _msg.error_fields != "undefined") {
        for (var e in _msg.error_fields) {
            if (!_msg.error_fields.hasOwnProperty(e)) {
                continue;
            }
            $(_msg.error_fields[e]).addClass('field-hilight');
        }
    }
    else {
        // Remove any previous errors
        $('.field-hilight').removeClass('field-hilight');
    }
    $('#form_submit_indicator').hide(300,function() {
        $(msg_id).slideDown(150,function() {
            $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
        });
    });
    return rval;
}

/**
 * popwin() is a generic popup window creator
 */
function popwin(mypage,myname,w,h,scroll)
{
    var lp = (screen.width) ? (screen.width - w) / 2 : 0;
    var tp = (screen.height) ? (screen.height - h) / 2 : 0;
    var sg = 'height='+ h +',width='+ w +',top='+ tp +',left='+ lp +',scrollbars='+ scroll +',resizable';
    win = window.open(mypage,myname, sg);
    if (win.opener == null) {
        win.opener = self;
    }
}

/**
 * The jrSetCookie function will set a Javascript cookie
 */
function jrSetCookie(name,value,days)
{
    var expires = '';
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires="+ date.toGMTString();
    }
    document.cookie = name +"="+ value + expires +"; path=/";
}

/**
 * The jrReadCookie Function will return the value of a previously set cookie
 */
function jrReadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length); {
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
    }
    return null;
}

/**
 * The jrEraseCookie will remove a cookie set by jrSetCookie()
 */
function jrEraseCookie(name) {
    jrSetCookie(name,"",-1);
}

/**
 * jrAlertMessage
 */
function jrAlertMessage(msg)
{
    alert(msg);
}

/**
 * jrFormModalSubmit_update_process
 * @param data Message Object
 * @param sid Update Interval Timer
 * @param skey string Form ID
 * @return bool
 */
function jrFormModalSubmit_update_process(data,sid,skey)
{
    // Check for any error/complete messages
    var k = false;
    for (var u in data) {
        if (data.hasOwnProperty(u)) {
            // When our work is complete on the server we will get a "type"
            // message back (complete,update,error)
            if (typeof data[u].t != "undefined") {
                switch (data[u].t) {
                    case 'complete':
                        clearInterval(sid);
                        $('#modal_error').hide();
                        $('#modal_success').prepend(data[u].m +'<br><br>').show();
                        k = $('#jr_html_modal_token').val();
                        jrFormModalCleanup(k);
                        break;
                    case 'update':
                        $('#modal_updates').prepend(data[u].m +'<br>');
                        break;
                    case 'empty':
                        return true;
                        break;
                    case 'error':
                        $('#modal_updates').prepend(data[u].m +'<br>');
                        $('#modal_success').hide();
                        $('#modal_error').prepend(data[u].m +'<br><br>').show();
                        break;
                    default:
                        clearInterval(sid);
                        k = $('#jr_html_modal_token').val();
                        jrFormModalCleanup(k);
                        break;
                }
            }
            else {
                clearInterval(sid);
                k = $('#jr_html_form_token').val();
                jrFormModalCleanup(k);
            }
        }
    }
    return true;
}

/**
 * jrFormModalCleanup
 * @param skey string Form ID
 * @return bool
 */
function jrFormModalCleanup(skey)
{
    $('#modal_indicator').hide();
    $.ajax({
        cache: false,
        url:   core_system_url +'/'+ jrCore_url +'/form_modal_cleanup/k='+ skey +'/__ajax=1'
    });
    return true;
}

/**
 * jrE - encodeURIComponent
 * @param t string String to encode
 * @return string
 */
function jrE(t)
{
    return encodeURIComponent(t);
}

/**
 * replacement for jQuery $.browser
 * @param ua
 * @returns {{browser: (*|string), version: (*|string)}}
 */
jQuery.uaMatch = function( ua ) {
    ua = ua.toLowerCase();
    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) || /(webkit)[ \/]([\w.]+)/.exec( ua ) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) || /(msie) ([\w.]+)/.exec( ua ) || ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) || [];
    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};

if ( !jQuery.browser ) {
    matched = jQuery.uaMatch( navigator.userAgent );
    browser = {};
    if ( matched.browser ) {
        browser[ matched.browser ] = true;
        browser.version = matched.version;
    }
    // Chrome is Webkit, but Webkit is also Safari.
    if ( browser.chrome ) {
        browser.webkit = true;
    } else if ( browser.webkit ) {
        browser.safari = true;
    }
    jQuery.browser = browser;
}

/**
 * jrCore_Launch_Modal
 * @param id string Div ID
 */
function jrCore_Launch_Modal(id)
{
    $('#' + id).modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn(75, function () {
                dialog.container.slideDown(5, function () {
                    dialog.data.fadeIn(75);
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

/**
 * Load a URL into a DOM element with spinner and fade in/out
 * @param id {string} DOM element
 * @param url {string} URL to load
 * @param method {string} append|overwrite
 * @returns {boolean}
 */
function jrCore_load_into(id, url, method)
{
    if (typeof url == "undefined") {
        return false;
    }
    if (url == 'blank') {
        $(id).hide();
    }
    else if (id == '#hidden') {
        $(id).hide();
        $(id).load(url);
    }
    else {
        $(id).fadeTo(100, 0.5, function() {
            $(id).html('<div style="text-align:center;padding:20px;margin:0 auto;"><img src="'+ core_system_url +'/'+ jrImage_url +'/img/module/jrCore/loading.gif" style="margin:15px"></div>');
            if (method == 'overwrite') {
                $(id).load(url, function() {
                    $(id).fadeTo(100, 1);
                })
            }
            else {
                $.get(url, function(res) {
                    $(id).append(res);
                })
            }
        });
    }
    return false;
}
