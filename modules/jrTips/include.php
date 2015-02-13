<?php
/**
 * Jamroom 5 System Tips module
 *
 * copyright 2003 - 2014
 * by The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
 *
 * This software is provided "as is" and any express or implied
 * warranties, including, but not limited to, the implied warranties
 * of merchantability and fitness for a particular purpose are
 * disclaimed.  In no event shall the Jamroom Network be liable for
 * any direct, indirect, incidental, special, exemplary or
 * consequential damages (including but not limited to, procurement
 * of substitute goods or services; loss of use, data or profits;
 * or business interruption) however caused and on any theory of
 * liability, whether in contract, strict liability, or tort
 * (including negligence or otherwise) arising from the use of this
 * software, even if advised of the possibility of such damage.
 * Some jurisdictions may not allow disclaimers of implied warranties
 * and certain statements in the above disclaimer may not apply to
 * you as regards implied warranties; the other terms and conditions
 * remain enforceable notwithstanding. In some jurisdictions it is
 * not permitted to limit liability and therefore such limitations
 * may not apply to you.
 *
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrTips_meta
 */
function jrTips_meta()
{
    $_tmp = array(
        'name'        => 'System Tips',
        'url'         => 'tips',
        'version'     => '1.0.1',
        'developer'   => 'The Jamroom Network, &copy;'. strftime('%Y'),
        'description' => 'Provides User Tips and Tours functionality for all modules',
        'license'     => 'mpl',
        'category'    => 'core'
    );
    return $_tmp;
}

/**
 * jrTips_init
 */
function jrTips_init()
{
    // Custom CSS and JS
    jrCore_register_module_feature('jrCore', 'css', 'jrTips', 'jrTips.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrTips', 'jrTips.js');

    // Add tip on/off checkbox to User Settings
    jrCore_register_event_listener('jrCore', 'form_display', 'jrTips_form_display_listener');

    // Show tips
    jrCore_register_event_listener('jrCore', 'module_view', 'jrTips_create_view_listener');
    jrCore_register_event_listener('jrCore', 'index_template', 'jrTips_create_view_listener');
    jrCore_register_event_listener('jrCore', 'skin_template', 'jrTips_create_view_listener');
    jrCore_register_event_listener('jrProfile', 'profile_view', 'jrTips_create_view_listener');
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Adds a "show tips" field to the User Settings
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrTips_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!jrCore_module_is_active('jrTips')) {
        return $_data;
    }
    if ($_data['form_view'] == 'jrUser/account') {
        $_lng = jrUser_load_lang_strings();
        $_tmp = array(
            'name'          => "user_jrTips_enabled",
            'type'          => 'checkbox',
            'default'       => 'on',
            'validate'      => 'onoff',
            'label'         => $_lng['jrTips'][6],
            'help'          => $_lng['jrTips'][7],
            'required'      => false,
            'form_designer' => false // no form designer or we can't turn it off
        );
        jrCore_form_field_create($_tmp);
    }
    return $_data;
}

/**
 * Check for interface tips
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrTips_create_view_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post, $_mods;
    if (isset($_conf['jrTips_enabled']) && $_conf['jrTips_enabled'] == 'off') {
        // We are not enabled globally
        return $_data;
    }
    if (isset($_user['user_jrTips_enabled']) && $_user['user_jrTips_enabled'] == 'off') {
        // We've been disabled by the user
        return $_data;
    }
    if (jrCore_is_mobile_device()) {
        // We don't show to mobile - too busy
        return $_data;
    }
    if (strpos($_SERVER['REQUEST_URI'], '__ajax') || strpos($_SERVER['REQUEST_URI'], '_v=') || jrCore_get_flag('jrTips_qtip_loaded')) {
        // already run or not active
        return $_data;
    }
    // We don't load on image views
    $url = jrCore_get_module_url('jrImage');
    if (strpos($_SERVER['REQUEST_URI'], "/{$url}/img/")) {
        return $_data;
    }

    // Check for registered tips
    $_tm = jrCore_get_registered_module_features('jrTips', 'tip');
    if (!$_tm) {
        // no registered tips
        return $_data;
    }
    jrCore_set_flag('jrTips_qtip_loaded', 1);

    $_ck = false;
    if (isset($_COOKIE['jrTips_hide']) && strlen($_COOKIE['jrTips_hide']) > 0) {
        $_ck = json_decode($_COOKIE['jrTips_hide'], true);
    }

    $_ln = jrUser_load_lang_strings();
    // See if we have modules that have registered a tip for this view
    $_tt = array();
    $_sl = array();
    $_md = array();
    $num = 0;
    foreach ($_tm as $mod => $view) {

        // Make sure we're not turned off purposefully for this module
        if ($_ck && isset($_ck[$mod])) {
            continue;
        }

        $_js = array();
        // Make sure we have tips...
        $func = "{$mod}_tips";
        if (!is_file(APP_DIR . "/modules/{$mod}/tips.php")) {
            continue;
        }
        if (!function_exists($func)) {
            require_once APP_DIR . "/modules/{$mod}/tips.php";
            if (!function_exists($func)) {
                continue;
            }
        }
        $_view = $func($_post, $_user, $_conf);

        // Multiple tip views...
        foreach ($_view as $_inf) {

            if (!isset($_inf['view']) || strlen($_inf['view']) === 0) {
                continue;
            }

            // View matching
            if (strpos($_inf['view'], $_conf['jrCore_base_url']) === 0) {
                $view = $_inf['view'];
            }
            else {
                $view = "{$_conf['jrCore_base_url']}/{$_inf['view']}";
            }
            // Check for anchoring
            $m_url = rtrim(trim(jrCore_get_current_url()), '/');
            $match = false;
            if (strpos($view, '$') && strrpos($view, '$') === (strlen($view) - 1)) {
                $view = substr($view, 0, strlen($view) - 1);
                // We must match exactly
                if ($m_url == $view) {
                    $match = true;
                }
            }
            else {
                if (strpos($m_url, $view) === 0) {
                    $match = true;
                }
            }
            if ($match) {
                // We have a match - check group
                if (!isset($_inf['group'])) {
                    $_inf['group'] = 'master';
                }
                switch ($_inf['group']) {
                    case 'master':
                        if (!jrUser_is_master()) {
                            continue 2;
                        }
                        break;
                    case 'admin':
                        if (!jrUser_is_admin()) {
                            continue 2;
                        }
                        break;
                    case 'power':
                        if (!jrUser_is_power_user()) {
                            continue 2;
                        }
                        break;
                    case 'multi':
                        if (!jrUser_is_multi_user()) {
                            continue 2;
                        }
                        break;
                    case 'visitor':
                        if (jrUser_is_logged_in()) {
                            continue 2;
                        }
                        break;
                    default:
                        if (!jrUser_is_logged_in()) {
                            continue 2;
                        }
                        break;
                }
                $_js[$num] = $_inf;
                $_md[$num] = $mod;
                $_sl[] = (isset($_inf['selector'])) ? $_inf['selector'] : '#content';
                $num++;
            }
        }
        $cnt = count($_js);
        if ($cnt > 0) {
            foreach ($_js as $k => $_inf) {
                $sel = '#content';
                if (isset($_inf['selector'])) {
                    $sel = $_inf['selector'];
                }
                $pos = 'bottom right';
                $add = '';
                if (isset($_inf['position'])) {
                    $pos = $_inf['position'];
                    if ($pos == 'top center') {
                        $add = ", my: 'top center' ";
                    }
                }
                if (isset($_inf['my_position'])) {
                    $add = ", my: '{$_inf['my_position']}'";
                }
                $sty = '';
                if (isset($_inf['pointer']) && $_inf['pointer'] === false) {
                    $sty = ', style: { tip: false }';
                }

                // See if we have a NEXT tip
                $nxt = ($k + 1);
                if (isset($_js[$nxt])) {
                    $api = ", events: { hide: function(event, api) { $('.qtip.ui-tooltip').qtip('hide'); $('" . $_js[$nxt]['selector'] . "').qtip('enable').qtip('show');  } } ";
                    $txt = $_ln['jrTips'][3];
                    $xtr = '';
                }
                else {
                    $api = ", events: { hide: function(event, api) { $('.qtip').remove(); } } ";
                    $txt = $_ln['jrTips'][4];
                    // See what type of closing button we are offering...
                    if (isset($_inf['cookie']) && $_inf['cookie'] === false) {
                        $xtr = ' onclick="jrTips_close_tip();"';
                    }
                    else {
                        $xtr = ' onclick="jrTips_close_tour(&#39;' . $_md[$k] . '&#39;, 0);"';
                    }
                }
                $tag = $txt;
                if (isset($_inf['button'])) {
                    $txt = $_inf['button'];
                }

                if (isset($_inf['button_url']) && jrCore_checktype($_inf['button_url'], 'url')) {
                    $txt = '<div class="qtip-close" onclick="jrCore_window_location(&#39;' . $_inf['button_url'] . '&#39;);">' . $txt . '</div>';
                }
                else {
                    $txt = "<div class=\"qtip-close\"{$xtr}>{$txt}</div>";
                }

                // Extra elements (video, documentation, etc.)
                $_xl = array();

                // Check for video
                if (isset($_inf['video_url']) && jrCore_checktype($_inf['video_url'], 'url')) {
                    $vtext = $_inf['video_url'];
                    if (isset($_inf['video_title'])) {
                        $vtext = $_inf['video_title'];
                    }
                    $_xl['video'] = "<a href=\"{$_inf['video_url']}\" target=\"_blank\">{$vtext}</a>";
                }

                // Check for youtube
                if (isset($_inf['youtube_id']) && strlen($_inf['youtube_id']) > 0) {
                    $img = "{$_conf['jrCore_base_url']}/{$url}/img/module/jrTips/youtube.png";
                    $scheme = jrCore_get_server_protocol();
                    $_xl['youtube'] = '<a onclick="jrTips_play_youtube(\'' . $sel . '\',\'' . $_inf['youtube_id'] . '\');return false"><img src="' . $img . '" width="24" height="24"></a>&nbsp;<a onclick="jrTips_play_youtube(\'' . $sel . '\',\'' . $_inf['youtube_id'] . '\');return false">'. $_ln['jrTips'][8] .'</a>';
                    $html = '<div id="y' . $_inf['youtube_id'] . '" class="tour-youtube-modal"><iframe type="text/html" width="600" height="400" src="' . $scheme .'://www.youtube.com/embed/' . $_inf['youtube_id'] .'?autoplay=1&amp;wmode=transparent" frameborder="0"></iframe><br>
                             <div class="qtip-youtube-close"><a onclick="jrTips_close_tour(\'' . $_md[$k] . '\', 1); return false">Close</a></div>
                             <input type="button" value="' . $tag .'" class="qtip-icon tour-youtube-close" onclick="$.modal.close();$(\'' . $_js[$nxt]['selector'] . '\').qtip(\'enable\').qtip(\'show\');"></div>';
                    jrCore_page_custom($html);
                }

                // Check for Documentation
                if (isset($_inf['doc_url']) && jrCore_checktype($_inf['doc_url'], 'url')) {
                    $vtext = $_inf['doc_url'];
                    if (isset($_inf['doc_title'])) {
                        $vtext = $_inf['doc_title'];
                    }
                    $_xl['doc'] = "<a href=\"{$_inf['doc_url']}\" target=\"_blank\">{$vtext}</a>";
                }

                // Close
                if (isset($_inf['cookie']) && $_inf['cookie'] === false) {
                    $_xl['close'] = "<a onclick=\"jrTips_close_tip()\">{$_ln['jrTips'][4]}</a>";
                }
                else {
                    $_xl['close'] = "<a onclick=\"jrTips_close_tour('{$_md[$k]}', 0)\">{$_ln['jrTips'][4]}</a>";
                }

                foreach ($_xl as $xk => $x) {
                    $_xl[$xk] = "<li class=\"tour-extra tour-{$xk}\">{$x}</li>";
                }
                $_inf['text'] .= '<br><ul class="tour-list">' . implode(' ', $_xl) . '</ul>';
                if (jrUser_is_logged_in()) {
                    $_inf['text'] .= "<div class=\"tour-stop\"><a onclick=\"jrTips_stop_tour()\">" . jrCore_entity_string($_ln['jrTips'][5]) . "</a></div>";
                }

                $xtr = '';
                if ($k == 0) {
                    $xtr = ', ready: true';
                }
                $hid = ';';
                if ($k > 0) {
                    $hid = ".qtip('disable');";
                }
                switch ($sel) {
                    case 'window':
                    case 'document.body':
                        break;
                    default:
                        $sel = "'{$sel}'";
                }
                $_tt[] = "$({$sel}).qtip({ content: { button: $('{$txt}'), title: '" . addslashes($_inf['title']) . "', text: '" . addslashes($_inf['text']) . "' }, show: { modal: true, solo: 'html'{$xtr} }, position: { at: '{$pos}'{$add} }, hide: { fixed: true, event: 'unfocus' }{$api}{$sty} }){$hid}";
            }
        }
    }
    if (count($_tt) > 0) {

        // Dependencies
        if (!jrCore_get_flag('jrTips_qtip_js_loaded')) {
            $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrTips/contrib/qtips2/jquery.qtip.css?_v={$_mods['jrTips']['module_version']}");
            jrCore_create_page_element('css_footer_href', $_tmp);
            $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrTips/contrib/qtips2/imagesloaded.pkg.min.js?_v={$_mods['jrTips']['module_version']}");
            jrCore_create_page_element('javascript_footer_href', $_tmp);
            $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrTips/contrib/qtips2/jquery.qtip.min.js?_v={$_mods['jrTips']['module_version']}");
            jrCore_create_page_element('javascript_footer_href', $_tmp);
            jrCore_set_flag('jrTips_qtip_js_loaded', 1);
        }

        jrCore_create_page_element('javascript_ready_function', $_tt);
        jrCore_create_page_element('javascript_embed', array("var __tt = '". implode(',', $_sl) ."'"));
    }
    return $_data;
}
