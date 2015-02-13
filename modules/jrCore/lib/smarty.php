<?php
/**
 * Jamroom 5 System Core module
 *
 * copyright 2003 - 2015
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
 * @package Smarty Functions and Modifiers
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Get a smarty error string for a missing parameter
 * @param $param string Param name
 * @return string
 */
function jrCore_smarty_missing_error($param)
{
    $func = '';
    $_tmp = debug_backtrace();
    if (isset($_tmp[1]['function'])) {
        $func = '{' . str_replace('smarty_function_', '', $_tmp[1]['function']) . '} ';
    }
    return $func . 'required parameter "' . jrCore_entity_string($param) . '" is missing';
}

/**
 * Get a smarty error string for a missing parameter
 * @param $param string Param name
 * @return string
 */
function jrCore_smarty_invalid_error($param)
{
    $func = '';
    $_tmp = debug_backtrace();
    if (isset($_tmp[1]['function'])) {
        $func = '{' . str_replace('smarty_function_', '', $_tmp[1]['function']) . '} ';
    }
    return $func . 'invalid value received for "' . jrCore_entity_string($param) . '" parameter';
}

/**
 * Send a custom error to the template
 * @param $text string Error string
 * @return string
 */
function jrCore_smarty_custom_error($text)
{
    $func = '';
    $_tmp = debug_backtrace();
    if (isset($_tmp[1]['function'])) {
        $func = '{' . str_replace('smarty_function_', '', $_tmp[1]['function']) . '} ';
    }
    return $func . jrCore_entity_string($text);
}

/**
 * Get item action buttons for the given type
 * @param $type string one of "index", "list" or "detail"
 * @param $params array Smarty function parameters
 * @param $smarty object Smarty Object
 * @return string
 */
function jrCore_item_action_buttons($type, $params, &$smarty)
{
    global $_user, $_conf;
    if (!jrProfile_is_profile_view()) {
        return '';
    }
    switch ($type) {
        case 'detail':
        case 'list':
            if (!isset($params['item']) || !is_array($params['item'])) {
                return '';
            }
            break;
        default:
            if (!isset($params['profile_id']) || !jrCore_checktype($params['profile_id'], 'number_nz')) {
                return '';
            }
            break;
    }
    $key = "jrCore_item_{$type}_buttons";
    if (!isset($params['module'])) {
        return "{$key}: invalid module received";
    }
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['item'])) {
        $params['item'] = array();
    }
    // Get module's with registered features
    $_rs = array();
    $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
    if ($_rf && is_array($_rf)) {
        foreach ($_rf as $bmod => $_ft) {
            if (!jrCore_module_is_active($bmod)) {
                continue;
            }
            foreach ($_ft as $func => $_inf) {
                $_inf['module']   = $bmod;
                $_inf['function'] = $func;
                $_rs[]            = $_inf;
            }
        }
    }
    else {
        return '';
    }

    // Let modules exclude buttons by trigger
    $_ex = array();
    $_ex = jrCore_trigger_event('jrCore', "exclude_item_{$type}_buttons", $_ex, $params, $params['module']);

    // The admin can:
    // set a specific button to not show
    // set the ORDER the buttons appear in (left to right)
    // Our config holds the info, ordered and by function => on|off
    $_bt = array();
    $_cb = array();
    $opt = "{$params['module']}_item_{$type}_buttons";
    if (isset($_conf[$opt]{1})) {
        $_ord = json_decode($_conf[$opt], true);
        foreach ($_ord as $_ab) {
            $_cb["{$_ab['function']}"] = 1;
        }
        // "new" modules may not be present in the order until the admin actually
        // re-orders things, so let's add any extra in at the end.
        foreach ($_rs as $_inf) {
            if (!isset($_cb["{$_inf['function']}"])) {
                $_ord[] = $_inf;
            }
        }
        unset($_cb);
    }
    else {
        $_ord = $_rs;
    }

    if ($_ord && count($_ord) > 0) {
        foreach ($_ord as $_inf) {

            if (!jrCore_module_is_active($_inf['module'])) {
                continue;
            }
            $func = $_inf['function'];
            if (!function_exists($func)) {
                continue;
            }
            if (isset($_inf['active']) && $_inf['active'] == 'off') {
                // Purposefully disabled
                continue;
            }

            // Check for exclusions
            if (isset($_ex[$func]) && $_ex[$func] === true) {
                // Purposefully excluded from smarty function call
                continue;
            }

            // Each button has some info about it:
            // group  => visitor|user|multi|power|admin|master
            // quota  => #|#|#|# ...
            if (isset($_inf['group'])) {
                switch ($_inf['group']) {
                    // Special "Owner" case
                    case 'owner':
                        if (!jrUser_is_admin()) {
                            switch ($type) {
                                case 'detail':
                                case 'list':
                                    if (!jrUser_can_edit_item($params['item'])) {
                                        continue 3;
                                    }
                                    break;
                                default:
                                    if (!jrUser_is_linked_to_profile($params['profile_id'])) {
                                        continue 3;
                                    }
                                    break;
                            }
                        }
                        break;

                    default:
                        // User must be part of defined group
                        if (!jrCore_user_is_part_of_group($_inf['group'])) {
                            continue 2;
                        }
                        break;
                }
            }
            if (isset($_inf['quota']) && strlen($_inf['quota']) > 0 && $_inf['quota'] != '_' && !jrUser_is_admin()) {
                if (!jrUser_is_logged_in()) {
                    continue;
                }
                if (is_numeric($_inf['quota']) && $_user['profile_quota_id'] != $_inf['quota']) {
                    continue;
                }
                elseif (strpos($_inf['quota'], ',')) {
                    $pass = false;
                    foreach (explode(',', $_inf['quota']) as $qt) {
                        if ($_user['profile_quota_id'] == $qt) {
                            $pass = true;
                            break;
                        }
                    }
                    if (!$pass) {
                        continue;
                    }
                }
            }

            // Show our button
            $_tmp = $func($params['module'], $params['item'], $params, $smarty);
            if ($_tmp) {
                if (is_array($_tmp)) {
                    if (isset($_tmp['icon']{0}) && isset($_tmp['url']{0})) {
                        $alt = '';
                        if (isset($_tmp['alt']) && strlen($_tmp['alt']) > 0) {
                            if (is_numeric($_tmp['alt'])) {
                                $_ln = jrUser_load_lang_strings();
                                if (isset($_ln["{$params['module']}"]["{$_tmp['alt']}"])) {
                                    $alt = $_ln["{$params['module']}"]["{$_tmp['alt']}"];
                                }
                            }
                            if (strlen($alt) === 0) {
                                $alt = $_tmp['alt'];
                            }
                        }
                        $ico = jrCore_get_icon_html($_tmp['icon']);
                        if (isset($_tmp['onclick']{0})) {
                            if (strlen($alt) > 0) {
                                $_bt[] = '<a href="' . $_tmp['url'] . '" onclick="' . $_tmp['onclick'] . '" title="' . jrCore_entity_string($alt) . '">' . $ico . '</a>';
                            }
                            else {
                                $_bt[] = '<a href="' . $_tmp['url'] . '" onclick="' . $_tmp['onclick'] . '">' . $ico . '</a>';
                            }
                        }
                        else {
                            if (strlen($alt) > 0) {
                                $_bt[] = '<a href="' . $_tmp['url'] . '" title="' . jrCore_entity_string($alt) . '">' . $ico . '</a>';
                            }
                            else {
                                $_bt[] = '<a href="' . $_tmp['url'] . '">' . $ico . '</a>';
                            }
                        }
                    }
                }
                else {
                    $_bt[] = $_tmp; // raw output
                }
            }
        }
    }

    // Lastly - if this is a MASTER ADMIN viewing, let them config
    if (jrUser_is_master()) {
        $url   = jrCore_get_module_url('jrCore');
        $ico   = jrCore_get_icon_html('settings');
        $_bt[] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $url . '/item_action_buttons/' . $type . '/m=' . $params['module'] . '" title="configure these buttons and the order they appear in">' . $ico . '</a>';
    }
    if (!isset($params['separator'])) {
        $params['separator'] = '&nbsp;';
    }
    return implode($params['separator'], $_bt);
}

/**
 * Add module "action" buttons to an item index
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_index_buttons($params, &$smarty)
{
    if (!isset($params['profile_id'])) {
        return jrCore_smarty_missing_error('profile_id');
    }
    if (!jrCore_checktype($params['profile_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('profile_id');
    }
    $out = jrCore_item_action_buttons('index', $params, $smarty);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Add module "action" buttons to an item listing
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_list_buttons($params, &$smarty)
{
    if (!isset($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!is_array($params['item'])) {
        return jrCore_smarty_invalid_error('item');
    }
    $out = jrCore_item_action_buttons('list', $params, $smarty);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Add module "action" buttons to an item detail page
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_detail_buttons($params, &$smarty)
{
    if (!isset($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!is_array($params['item'])) {
        return jrCore_smarty_invalid_error('item');
    }
    $out = jrCore_item_action_buttons('detail', $params, $smarty);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Add module feature sections to item details
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_detail_features($params, &$smarty)
{
    global $_conf;
    if (!isset($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (!isset($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!is_array($params['item'])) {
        return jrCore_smarty_invalid_error('item');
    }

    // See if our order has been defined yet
    $_ord = array();
    if (isset($_conf['jrCore_detail_feature_order']) && strlen($_conf['jrCore_detail_feature_order']) > 0) {
        $_ord = array_flip(explode(',', $_conf['jrCore_detail_feature_order']));
    }

    // Prepare any excludes
    $_ex = array();
    if (isset($params['exclude']) && strlen($params['exclude']) > 0) {
        $_ex = array_flip(explode(',', $params['exclude']));
    }
    // Get all registered features
    $_tmp = jrCore_get_registered_module_features('jrCore', 'item_detail_feature');
    $text = '';
    if (isset($_tmp) && is_array($_tmp)) {

        // First get things in the right order
        $_res = array();
        foreach ($_tmp as $mod => $_ft) {
            if (!jrCore_module_is_active($mod)) {
                continue;
            }
            foreach ($_ft as $nam => $_ftr) {
                $name           = "{$mod}~{$nam}";
                $_ftr['module'] = $mod;
                $_res[$name]    = $_ftr;
            }
        }

        // Order entries first
        if (count($_ord) > 0) {
            foreach ($_ord as $name => $order) {
                $_ftr = $_res[$name];
                // Make sure it is active
                if (!jrCore_module_is_active($_ftr['module'])) {
                    continue;
                }
                // Make sure it is allowed in the Quota
                if (!isset($params['item']["quota_{$_ftr['module']}_show_detail"]) || $params['item']["quota_{$_ftr['module']}_show_detail"] != 'on') {
                    continue;
                }
                // Purposefully excluded
                if (isset($_ex[$name])) {
                    continue;
                }
                if (isset($_ftr['function']) && function_exists($_ftr['function'])) {
                    $text .= $_ftr['function']($_ftr['module'], $params['item'], $params, $smarty);
                }
                unset($_res[$name]);
            }
        }

        // Unordered (new) at bottom
        if (count($_res) > 0) {
            foreach ($_res as $name => $_ftr) {

                // Make sure it is active
                if (!jrCore_module_is_active($_ftr['module'])) {
                    continue;
                }
                // Make sure it is allowed in the Quota
                if (!isset($params['item']["quota_{$_ftr['module']}_show_detail"]) || $params['item']["quota_{$_ftr['module']}_show_detail"] != 'on') {
                    continue;
                }
                // Purposefully excluded
                if (isset($_ex[$name])) {
                    continue;
                }
                if (isset($_ftr['function']) && function_exists($_ftr['function'])) {
                    $text .= $_ftr['function']($_ftr['module'], $params['item'], $params, $smarty);
                }
                unset($_res[$name]);
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $text);
        return '';
    }
    return $text;
}

/**
 * Get all registered jrCore_format_string listeners
 * @return mixed bool|array
 */
function jrCore_get_format_string_listeners()
{
    $_tmp = jrCore_get_registered_module_features('jrCore', 'format_string');
    $_out = array();
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $_opts) {
            foreach ($_opts as $fnc => $_desc) {
                $_out[$fnc] = $_desc['label'];
            }
        }
        return $_out;
    }
    return false;
}

/**
 * Return HELP for Quota Config
 * @return mixed bool|array
 */
function jrCore_get_format_string_help()
{
    $_tmp = jrCore_get_registered_module_features('jrCore', 'format_string');
    if (isset($_tmp) && is_array($_tmp)) {
        $text = 'The following Text Formatters are are available:<br><br>';
        $_out = array();
        foreach ($_tmp as $_opts) {
            foreach ($_opts as $_desc) {
                $_out[] = "<b>{$_desc['label']}</b>: {$_desc['help']}<br><small>template whitelist name: {$_desc['wl']}</small>";
            }
        }
        $text .= implode('<br><br>', $_out);
    }
    else {
        $text = 'There are no formatters currently available in the system!';
    }
    return $text;
}

/**
 * Core Media Player
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_media_player($params, $smarty)
{
    // {jrCore_media_player type="jrAudio_blue_monday" module="jrAudio" field="audio_file" item_id=# autoplay=false}
    // {jrCore_media_player type="jrVideo_blue_monday" module="jrVideo" field="video_file" item_id=# autoplay=false}
    // {jrCore_media_player type="jrPlaylist_blue_monday" module="jrPlaylist" item_id=# autoplay=false}
    global $_conf;

    // Module is required
    if (!isset($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (strlen($params['module']) === 0) {
        return jrCore_smarty_invalid_error('module');
    }

    // Get registered players
    $_pl = jrCore_get_registered_media_players('all');

    // If we get a TYPE, then that is the type of player we use, otherwise
    // we default to the player registered by the active skin
    $type = false;
    if (isset($params['type']) && strlen($params['type']) > 0) {
        $type = $params['type'];
    }
    else {
        // Get active skin default player...
        $_tmp = jrCore_get_registered_module_features('jrCore', 'media_player_skin');
        if (isset($_tmp) && isset($_tmp["{$_conf['jrCore_active_skin']}"]) && isset($_tmp["{$_conf['jrCore_active_skin']}"]["{$params['module']}"])) {
            $type = $_tmp["{$_conf['jrCore_active_skin']}"]["{$params['module']}"];
        }
    }

    // Make sure our type is valid
    if (!$type || !isset($_pl[$type])) {
        return jrCore_smarty_invalid_error('type');
    }

    $mod = $_pl[$type];
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$type}.tpl")) {
        $mod = false;
    }
    $curl = jrCore_get_module_url('jrCore');
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/css/{$type}.css")) {
        if (jrCore_is_ajax_request()) {
            $css = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/css/{$type}.css";
        }
        else {
            $css = "{$_conf['jrCore_base_url']}/{$curl}/css/skin/{$type}.css";
        }
    }
    elseif (is_file(APP_DIR . "/modules/{$mod}/css/{$type}.css")) {
        if (jrCore_is_ajax_request()) {
            $css = APP_DIR . "/modules/{$mod}/css/{$type}.css";
        }
        else {
            $css = "{$_conf['jrCore_base_url']}/{$curl}/css/" . jrCore_get_module_url($mod) . "/{$type}.css";
        }
    }

    // Setup common parameters
    $_rep = array();

    // auto play
    $_rep['autoplay'] = 'false';
    if (isset($params['autoplay']) && ($params['autoplay'] === 1 || $params['autoplay'] === true || $params['autoplay'] == 'true' || $params['autoplay'] == 'on')) {
        $_rep['autoplay'] = 'true';
    }

    // Get our playlist info
    if (isset($params['item_id']) && jrCore_checktype($params['item_id'], 'number_nz')) {
        $_rt = array(
            jrCore_db_get_item($params['module'], $params['item_id'])
        );
        if (!isset($_rt[0]) || !is_array($_rt[0])) {
            $out = '';
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $out);
                return '';
            }
            return $out;
        }
    }
    // Single item as an array
    elseif (isset($params['item']) && is_array($params['item']) && isset($params['item']['_item_id'])) {
        $_rt = array($params['item']);
        unset($params['item']);
    }
    // Array of items
    elseif (isset($params['items']) && is_array($params['items']) && count($params['items']) > 0) {
        $_rt = $params['items'];
        unset($params['items']);
    }

    // Get items
    $_fmt = array();
    if (!isset($_rt) || !is_array($_rt)) {

        // Go get our media based on params
        $_args = array();
        foreach ($params as $k => $v) {
            // Search
            if (strpos($k, 'search') === 0) {
                if (!isset($_args['search'])) {
                    $_args['search'] = array();
                }
                $_args['search'][] = $v;
            }
            // Order by
            elseif (strpos($k, 'order_by') === 0) {
                if (!isset($_args['order_by'])) {
                    $_args['order_by'] = array();
                }
                list($fld, $dir) = explode(' ', $v);
                $fld                     = trim($fld);
                $_args['order_by'][$fld] = trim($dir);
            }
            // Group By
            elseif ($k == 'group_by') {
                $_args['group_by'] = trim($v);
            }
            // Limit
            elseif ($k == 'limit') {
                $_args['limit'] = (int) $v;
            }
        }
        if (isset($_args) && is_array($_args) && count($_args) > 0) {
            $_args['exclude_jrProfile_quota_keys'] = true;
            $_rt                                   = jrCore_db_search_items($params['module'], $_args);
            if (isset($_rt['_items']) && is_array($_rt['_items'])) {
                $_rt = $_rt['_items'];
            }
        }

        // Make sure we got media items
        if (!isset($_rt) || !is_array($_rt)) {
            $out = jrCore_smarty_custom_error('no media found for player');
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $out);
                return '';
            }
            return $out;
        }
    }

    // Send out player playlist trigger
    $_rt = jrCore_trigger_event('jrCore', 'media_playlist', $_rt, $params);

    // Our allowed formats
    $_fm = array(
        'mp3' => 1,
        'flv' => 1
    );

    // Prepare our playlist setup
    $_rep['media'] = array();
    foreach ($_rt as $k => $_item) {

        // media_playlist listeners can setup their own
        // media_playlist_url and media_playlist_ext
        if (!isset($_item['media_playlist_ext']{0}) || !isset($_item['media_playlist_url']{0})) {
            $ext = false;
            $fld = false;
            if (isset($params['field'])) {
                // We know the field, so the module is for the item
                $ext = $_item["{$params['field']}_extension"];
                $pfx = jrCore_db_get_prefix($params['module']);
                $url = jrCore_get_module_url($params['module']);
            }
            else {
                // We need to figure out our extension
                foreach ($_item as $ek => $v) {
                    if (strpos($ek, '_extension') && !strpos($ek, '_original') && isset($_fm[$v])) {
                        $fld = str_replace('_extension', '', $ek);
                        $ext = $v;
                        break;
                    }
                }
                if (!$ext) {
                    // unknown file type
                    continue;
                }
                // We have to figure out the module based on the item
                $pfx = jrCore_db_get_prefix($_item['module']);
                $url = jrCore_get_module_url($_item['module']);
            }
            $fld                    = ($fld) ? $fld : $params['field'];
            $str                    = "{$_conf['jrCore_base_url']}/{$url}/stream/{$fld}/{$_item['_item_id']}/key=[jrCore_media_play_key]/file.{$ext}";
            $img                    = "{$_conf['jrCore_base_url']}/{$url}/image/{$pfx}_image/{$_item['_item_id']}/large/image.png";
            $_item["{$pfx}_artist"] = $_item['profile_name'];
        }
        else {
            $url = jrCore_get_module_url($_item['module']);
            $pfx = jrCore_db_get_prefix($_item['module']);
            $ext = $_item['media_playlist_ext'];
            $str = $_item['media_playlist_url'];
            $img = (isset($_item['media_playlist_img'])) ? $_item['media_playlist_img'] : '';
        }
        if (isset($_fm[$ext])) {
            $_rep['media'][$k] = array(
                'title'      => jrCore_entity_string($_item["{$pfx}_title"]),
                'artist'     => jrCore_entity_string($_item['profile_name']),
                'poster'     => $img,
                'module'     => $params['module'],
                'module_url' => $url,
                'prefix'     => $pfx,
                'item_id'    => $_item['_item_id'],
                '_item'      => $_item,
                'formats'    => array(
                    $ext => $str
                )
            );
            if ($ext == 'flv' && (jrCore_is_mobile_device() || jrCore_is_tablet_device())) {
                if (!isset($fld) || strlen($fld) === 0) {
                    // See if we can figure it out...
                    $pfx = jrCore_db_get_prefix($_item['module']);
                    $fld = "{$pfx}_file";
                }
                $_rep['media'][$k]['formats']['m4v'] = "{$_conf['jrCore_base_url']}/{$url}/stream/{$fld}_mobile/{$_item['_item_id']}/key=[jrCore_media_play_key]/file.m4v";
                $_fmt['m4v']                         = 'm4v';
            }
            if ($ext == 'mp3') {
                // See if we have an OGG file as well...
                $mdir = jrCore_get_media_directory($_item['_profile_id']);
                if (is_file("{$mdir}/jrAudio_{$_item['_item_id']}_audio_file.ogg")) {
                    if (!isset($fld) || strlen($fld) === 0) {
                        // See if we can figure it out...
                        $pfx = jrCore_db_get_prefix($_item['module']);
                        $fld = "{$pfx}_file";
                    }
                    $_rep['media'][$k]['formats']['oga'] = "{$_conf['jrCore_base_url']}/{$url}/stream/{$fld}/{$_item['_item_id']}/key=[jrCore_media_play_key]/file.ogg";
                    $_fmt['oga']                         = 'oga';
                }
            }
            $_fmt[$ext] = $ext;
        }
    }

    // Additional items
    $_rep['uniqid']   = 'm' . uniqid();
    $_rep['formats']  = implode(',', $_fmt);
    $_rep['params']   = $params;
    $_rep['solution'] = 'html,flash';

    if (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
        // TEMP: IE use flash
        $_rep['solution'] = 'flash,html';
    }
    elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'firefox') && $params['module'] == 'jrVideo') {
        // TEMP: Firefox use flash for video only
        $_rep['solution'] = 'flash,html';
    }

    // Parse and return
    $out = '';
    if (isset($css) && strlen($css) > 0 && !jrCore_get_flag($css)) {
        if (jrCore_is_ajax_request()) {
            // If this is an AJAX request, we must INLINE the CSS or it won't work properly (even using jQuery $.get())
            $crl = jrCore_get_module_url('jrImage');
            $mrl = jrCore_get_module_url($mod);
            $_rp = array(
                '{$' . $mod . '_img_url}' => "{$_conf['jrCore_base_url']}/{$crl}/img/{$mrl}"
            );
            $out = '<style type="text/css">' . str_replace(array_keys($_rp), $_rp, file_get_contents($css)) . '</style>';
        }
        else {
            $out .= '<link rel="stylesheet" href="' . $css . '" media="screen" />' . "\n";
        }
        jrCore_set_flag($css, 1);
    }
    $out .= jrCore_parse_template("{$type}.tpl", $_rep, $mod);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Display a Skin Menu with registered entries
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_skin_menu($params, &$smarty)
{
    global $_conf, $_user;

    // If user is not logged in we have cached content
    $key = false;
    if (!jrUser_is_logged_in()) {
        $key = "skin_menu_visitor";
        if ($tmp = jrCore_is_cached('jrCore', $key)) {
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $tmp);
            }
            return $tmp;
        }
    }

    // Get core menu items
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT *, CONCAT_WS('/',menu_module,menu_unique) AS menu_key FROM {$tbl} ORDER BY menu_order ASC";
    $_rt = jrCore_db_query($req, 'menu_key', false, null, false);

    // See if we have anything that has registered
    $_tmp = jrCore_get_registered_module_features('jrCore', 'skin_menu_item');
    if ($_tmp) {
        // We have registered skin menu entries.  We need to go through each
        // one and make sure it is setup in the menu table
        foreach ($_tmp as $module => $_options) {
            if (!jrCore_module_is_active($module)) {
                // This module is not active
                continue;
            }
            // module, unique_id, user_group, label, action, function
            // $_tmp[$module][$label] = array($unique,$user_group,$url,$notify_function,$onclick);
            foreach ($_options as $unq => $_inf) {
                if (!isset($_rt["{$module}/{$unq}"])) {
                    // We are not setup ...
                    // We always place new entries at the bottom - find out our lowest number in
                    // our configured skin menu entries
                    $ord = 100;
                    foreach ($_tmp as $_op) {
                        foreach ($_op as $_o) {
                            if (isset($_o['order']) && $_o['order'] >= $ord) {
                                $ord = ($_o['order'] + 1);
                            }
                        }
                    }
                    $mod = jrCore_db_escape($module);
                    $lbl = jrCore_db_escape($_inf['label']);
                    $grp = jrCore_db_escape($_inf['group']);
                    $act = jrCore_db_escape($_inf['url']);
                    $fnc = (!is_null($_inf['function']) && strlen($_inf['function']) > 0) ? jrCore_db_escape($_inf['function']) : '';
                    $onc = (!is_null($_inf['onclick']) && strlen($_inf['onclick']) > 0) ? jrCore_db_escape($_inf['onclick']) : '';
                    // Check for field
                    $fad = '';
                    $fin = '';
                    $fup = '';
                    if (isset($_inf['field']) && strlen($_inf['field']) > 0) {
                        if (!isset($_fd)) {
                            $_fd = jrCore_db_table_columns('jrCore', 'menu');
                        }
                        if (isset($_fd['menu_field'])) {
                            $fld = jrCore_db_escape($_inf['field']);
                            $fad = ", menu_field";
                            $fin = ",'{$fld}'";
                            $fup = ", menu_field = '{$fld}'";
                        }
                    }
                    $req = "INSERT INTO {$tbl} (menu_module,menu_unique,menu_active,menu_label,menu_action,menu_groups,menu_order,menu_function,menu_onclick{$fad})
                            VALUES ('{$mod}','{$unq}','on','{$lbl}','{$act}','{$grp}','{$ord}','{$fnc}','{$onc}'{$fin})
                            ON DUPLICATE KEY UPDATE menu_action = '{$act}', menu_function = '{$fnc}', menu_onclick = '{$onc}'{$fup}";
                    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
                    if (!isset($cnt) || $cnt !== 1) {
                        jrCore_logger('CRI', "unable to create new menu entry for {$module}/{$act}");
                    }
                    else {
                        $_rt["{$module}/{$unq}"] = array(
                            'menu_module'   => $module,
                            'menu_unique'   => $unq,
                            'menu_active'   => 'on',
                            'menu_label'    => $_inf['label'],
                            'menu_action'   => $_inf['url'],
                            'menu_groups'   => $_inf['group'],
                            'menu_order'    => (isset($_inf['order']) && jrCore_checktype($_inf['order'], 'number_nz')) ? (int) $_inf['order'] : 90,
                            'menu_function' => $fnc,
                            'menu_onclick'  => $onc
                        );
                        if (isset($_inf['field']) && strlen($_inf['field']) > 0) {
                            $_rt["{$module}/{$unq}"]['menu_field'] = jrCore_db_escape($_inf['field']);
                        }
                    }
                }
                // Check for updates
                elseif ($_rt["{$module}/{$unq}"]['menu_action'] != $_inf['url'] ||
                    $_rt["{$module}/{$unq}"]['menu_groups'] != $_inf['group'] ||
                    (!isset($_inf['function']) && isset($_rt["{$module}/{$unq}"]['menu_function']{1})) ||
                    (isset($_inf['function']{1}) && $_inf['function'] != $_rt["{$module}/{$unq}"]['menu_function']) ||
                    (isset($_inf['field']{1}) && $_inf['field'] != $_rt["{$module}/{$unq}"]['menu_field'])
                ) {
                    $act = jrCore_db_escape($_inf['url']);
                    $fnc = (!is_null($_inf['function']) && strlen($_inf['function']) > 0) ? jrCore_db_escape($_inf['function']) : '';
                    // Check for field
                    $fup = '';
                    if (isset($_inf['field']) && strlen($_inf['field']) > 0) {
                        if (!isset($_fd)) {
                            $_fd = jrCore_db_table_columns('jrCore', 'menu');
                        }
                        if (isset($_fd['menu_field'])) {
                            $fld = jrCore_db_escape($_inf['field']);
                            $fup = ", menu_field = '{$fld}'";
                        }
                    }
                    $req = "UPDATE {$tbl} SET menu_action = '{$act}', menu_function = '{$fnc}'{$fup} WHERE menu_id = '" . intval($_rt["{$module}/{$unq}"]['menu_id']) . "'";
                    jrCore_db_query($req);
                    $_rt["{$module}/{$unq}"]['menu_action']   = $_inf['url'];
                    $_rt["{$module}/{$unq}"]['menu_groups']   = trim($_inf['group']);
                    $_rt["{$module}/{$unq}"]['menu_function'] = $_inf['function'];
                    $_rt["{$module}/{$unq}"]['menu_field']    = $_inf['field'];
                }
            }
        }
    }
    if (!isset($_rt) || !is_array($_rt)) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], '');
        }
        return '';
    }

    // Bring in strings
    $_lang = jrUser_load_lang_strings();

    // Go through each and process via template
    $alert = 0;
    $_ct   = array();
    $_ci   = array();
    $tpl   = 'skin_menu.tpl';
    $dir   = 'jrCore';
    if (isset($params['template']) && is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$params['template']}")) {
        $tpl = $params['template'];
        $dir = null;
    }
    foreach ($_rt as $k => $_opt) {

        if ($_opt['menu_module'] != 'CustomEntry' && !jrCore_module_is_active($_opt['menu_module'])) {
            // This module is not active
            unset($_rt[$k]);
            continue;
        }
        // check for groups
        if (isset($_opt['menu_groups']) && strpos($_opt['menu_groups'], ',')) {
            $_grp = explode(',', $_opt['menu_groups']);
            if (is_array($_grp)) {
                foreach ($_grp as $gk => $group) {
                    $_grp[$gk] = trim($group);
                }
            }
        }
        else {
            $_grp = array(trim($_opt['menu_groups']));
        }
        if (!isset($_grp) || !is_array($_grp)) {
            unset($_rt[$k]);
            continue;
        }
        // Check if we need to apply a quota check (not applied to master/admin users)
        $show      = false;
        $grp_check = true;
        $fld       = "quota_{$_opt['menu_module']}_allowed";
        $fvl       = $_user[$fld];
        if (isset($_opt['menu_field']) && strlen($_opt['menu_field']) > 0) {
            if (strpos($_opt['menu_field'], ':')) {
                list($home, $fld) = explode(':', $_opt['menu_field']);
                if ($home == 'home_profile') {
                    $fld = trim($fld);
                    $fvl = jrUser_get_profile_home_key($fld);
                }
            }
            else {
                $fld = trim($_opt['menu_field']);
                $fvl = (isset($_user[$fld])) ? $_user[$fld] : 'off';
            }
        }
        if (isset($fvl) && $fvl == 'off') {
            // User is not allowed based on quota - check if this menu item is setup for admin/master
            // access - if it is, and the user is a master or admin, allow it
            if ((in_array('master', $_grp) && jrUser_is_master()) || (in_array('admin', $_grp) && jrUser_is_admin())) {
                // admin user - we're good
                $grp_check = false;
                $show      = true;
            }
            else {
                unset($_rt[$k]);
                continue;
            }
        }
        // See if we have been given a specific category
        if (isset($params['category']) && strlen($params['category']) > 0 && $_opt['menu_category'] != $params['category']) {
            unset($_rt[$k]);
            continue;
        }
        if (!isset($_opt['menu_active']) || $_opt['menu_active'] != 'on') {
            unset($_rt[$k]);
            continue;
        }

        if ($grp_check) {
            foreach ($_grp as $group) {
                if (jrCore_user_is_part_of_group($group)) {
                    $show = true;
                    break;
                }
            }
        }
        if (!$show) {
            unset($_rt[$k]);
            continue;
        }

        // Build our categories...
        $cat = 'default';
        if (isset($_opt['menu_category']) && strlen($_opt['menu_category']) > 0) {
            if (is_numeric($_opt['menu_category']) && isset($_lang["{$_conf['jrCore_active_skin']}"]["{$_opt['menu_category']}"])) {
                $_opt['menu_category'] = $_lang["{$_conf['jrCore_active_skin']}"]["{$_opt['menu_category']}"];
            }
            $cat = $_opt['menu_category'];
        }
        if (!isset($_ct[$cat])) {
            $_ct[$cat] = 0;
            $_ci[$cat] = array();
        }
        $_ct[$cat]++;

        $lbl                   = $_opt['menu_label'];
        $_rt[$k]['menu_label'] = (isset($lbl) && isset($_lang["{$_opt['menu_module']}"][$lbl])) ? $_lang["{$_opt['menu_module']}"][$lbl] : $lbl;
        if (strpos($_opt['menu_action'], 'http') === 0) {
            $_rt[$k]['menu_url'] = $_opt['menu_action'];
        }
        else {
            if ($_opt['menu_module'] != 'CustomEntry' && !strpos($_opt['menu_action'], '/')) {
                $murl                = jrCore_get_module_url($_opt['menu_module']);
                $_rt[$k]['menu_url'] = "{$_conf['jrCore_base_url']}/{$murl}/{$_opt['menu_action']}";
            }
            else {
                if (strpos(trim(trim($_opt['menu_action']), '/'), '/') || $_opt['menu_module'] == 'CustomEntry') {
                    $_rt[$k]['menu_url'] = "{$_conf['jrCore_base_url']}/{$_opt['menu_action']}";
                }
                else {
                    $murl                = jrCore_get_module_url($_opt['menu_module']);
                    $_rt[$k]['menu_url'] = "{$_conf['jrCore_base_url']}/{$murl}/{$_opt['menu_action']}";
                }
            }
        }

        // See if this menu item has a FUNCTION that needs to be run
        if (isset($_opt['menu_function']) && function_exists($_opt['menu_function'])) {

            // Our menu function can return a NUMBER, an IMAGE or bool TRUE/FALSE
            $res = $_opt['menu_function']($_conf, $_user);
            if (!$res) {
                // Function returned FALSE - don't show menu item
                unset($_rt[$k]);
                continue;
            }
            elseif (isset($res) && is_numeric($res)) {
                // Number - show next to title - i.e. this is a "notification"
                $_rt[$k]['menu_function_result'] = $res;
                $alert += $res;
            }
            elseif (isset($res) && strlen($res) > 0 && is_file(APP_DIR . "/modules/{$_opt['menu_module']}/img/{$res}")) {
                // Image
                switch (jrCore_file_extension($res)) {
                    case 'gif':
                    case 'png':
                    case 'jpg':
                    case 'jpeg':
                        $_rt[$k]['menu_function_result'] = $res;
                        break;
                }
            }

        }
        // By category too
        $_ci[$cat][$k] = $_rt[$k];
    }
    $params['menu_id'] = (isset($params['menu_id'])) ? $params['menu_id'] : 'skin_menu';
    $params['label']   = (isset($params['label']) && isset($_lang["{$_conf['jrCore_active_skin']}"]["{$params['label']}"])) ? $_lang["{$_conf['jrCore_active_skin']}"]["{$params['label']}"] : $params['label'];
    $_rp               = array(
        '_items'             => $_rt,
        '_categories'        => $_ct,
        '_items_by_category' => $_ci,
        'params'             => $params,
        'alert'              => $alert
    );
    unset($_rt);
    $out = jrCore_parse_template($tpl, $_rp, $dir);

    // Save to cache
    if (!jrUser_is_logged_in()) {
        jrCore_add_to_cache('jrCore', $key, $out);
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Count a hit for a module item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_counter($params, &$smarty)
{
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        // Not installed or wrong...
        return '';
    }
    if (!isset($params['name']{0})) {
        return jrCore_smarty_missing_error('name');
    }
    if (!isset($params['item_id'])) {
        return jrCore_smarty_missing_error('_item_id');
    }
    if (!jrCore_checktype($params['item_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('_item_id');
    }
    $inc = 1;
    if (isset($params['increment']) && jrCore_checktype($params['increment'], 'number_nz')) {
        $inc = intval($params['increment']);
    }
    // Count it
    jrCore_counter($params['module'], $params['item_id'], $params['name'], $inc);
    return '';
}

/**
 * Get Count for a module item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_get_count($params, &$smarty)
{
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module']) || !jrCore_db_get_prefix($params['module'])) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], 0);
            return '';
        }
        return 0;
    }
    if (!isset($params['name']{0})) {

        // No specific field - get counts for entire module
        $_sc = array(
            'return_count'   => true,
            'ignore_pending' => true,
            'privacy_check'  => false,
            'limit'          => 1000000
        );
        if (isset($params['profile_id'])){
            $_sc['search'] = array("profile_id = {$params['profile_id']}");
        }
        $cnt = jrCore_db_search_items($params['module'], $_sc);
    }
    else {
        // If we get a profile_id it must be valid
        if (isset($params['profile_id']) && !jrCore_checktype($params['profile_id'], 'number_nz') ) {
            return jrCore_smarty_invalid_error('profile_id');
        }
        // Counts for a specific counter field
        if (!isset($params['item_id'])) {
            // We're doing ALL counts for a specific type
            $cnt = jrCore_get_count($params['module'], $params['name'], null, $params['profile_id'] );
        }
        else {
            if (!jrCore_checktype($params['item_id'], 'number_nz')) {
                return jrCore_smarty_missing_error('item_id');
            }
            $cnt = jrCore_get_count($params['module'], $params['name'], $params['item_id'], $params['profile_id']);
        }
    }
    $cnt = intval($cnt);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $cnt);
        return '';
    }
    return $cnt;
}

/**
 * Embed an image in a template
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_image($params, $smarty)
{
    global $_conf;
    if (!isset($params['image'])) {
        return jrCore_smarty_missing_error('image');
    }
    // See if we have a custom file for this image
    $skn = $_conf['jrCore_active_skin'];
    $tag = '';
    $typ = 'skin';
    if (isset($params['module'])) {
        $skn = $params['module'];
        $tag = 'mod_';
        $typ = 'module';
    }
    $_im = array();
    if (isset($_conf["jrCore_{$skn}_custom_images"]{2})) {
        $_im = json_decode($_conf["jrCore_{$skn}_custom_images"], true);
    }
    if (isset($_im["{$params['image']}"]) && isset($_im["{$params['image']}"][1]) && $_im["{$params['image']}"][1] == 'on') {
        $src = "{$_conf['jrCore_base_url']}/data/media/0/0/{$tag}{$skn}_{$params['image']}?r=" . $_im["{$params['image']}"][0];
    }
    else {
        if (isset($params['module'])) {
            $src = "{$_conf['jrCore_base_url']}/modules/{$skn}/img/{$params['image']}";
        }
        else {
            $src = "{$_conf['jrCore_base_url']}/skins/{$skn}/img/{$params['image']}";
        }
    }
    // Send out image trigger for source
    $_tm = array(
        'function'           => 'jrCore_image',
        'params'             => $params,
        'smarty'             => $smarty,
        'skin_custom_images' => $_im
    );
    $src = jrCore_trigger_event('jrImage', "{$typ}_image", $src, $_tm);

    if (isset($params['src_only']) && $params['src_only'] === true) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $src);
            return '';
        }
        return $src;
    }
    if (!isset($params['title']) && isset($params['alt'])) {
        $params['title'] = $params['alt'];
    }
    $out = "<img src=\"{$src}\" ";
    // Our other params are optional
    foreach ($params as $k => $v) {
        $k = jrCore_str_to_lower($k);
        switch ($k) {
            case 'width':
            case 'height':
            case 'id':
            case 'class':
            case 'style':
            case 'onclick':
            case 'onmouseover':
            case 'onmouseout':
                $out .= "{$k}=\"{$v}\" ";
                break;
            case 'alt':
            case 'title':
                $out .= "{$k}=\"" . jrCore_entity_string($v) . "\" ";
                break;
        }
    }
    $out = rtrim($out, ' ') . '>';
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Embed a Power List into a template
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_list($params, &$smarty)
{
    global $_conf, $_post;
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        // Not installed or wrong...
        return '';
    }
    // Check for cache
    $key = md5(json_encode($_post) . json_encode($params));
    if ((!isset($params['no_cache']) || $params['no_cache'] === false) && $tmp = jrCore_is_cached($params['module'], $key)) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $tmp);
            return '';
        }
        return $tmp . "\n<!--c-->";
    }
    $tpl_dir = null;
    if (!isset($params['template']) || strlen($params['template']) === 0) {
        // Check for Skin override
        if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$params['module']}_item_list.tpl")) {
            $params['template'] = "{$params['module']}_item_list.tpl";
        }
        // See if this module provides one
        elseif (is_file(APP_DIR . "/modules/{$params['module']}/templates/item_list.tpl")) {
            $tpl_dir            = $params['module'];
            $params['template'] = 'item_list.tpl';
        }
        else {
            return jrCore_smarty_custom_error("{$params['module']}/templates/item_list.tpl not found");
        }
    }
    else {
        if (isset($params['tpl_dir']) && is_file(APP_DIR . "/modules/{$params['tpl_dir']}/templates/{$params['template']}")) {
            $tpl_dir = $params['tpl_dir'];
        }
        // Check for template
        if (!is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$params['template']}") && is_file(APP_DIR . "/modules/{$params['module']}/templates/{$params['template']}")) {
            $tpl_dir = $params['module'];
        }
    }
    $module = $params['module'];
    unset($params['module']);

    // Next - we need to see if this is a list that is on a profile - if it is, and the
    // quota the profile belongs to has a max_items setting enabled, we have to limit
    // the number of items that are going to be shown.
    if (!jrUser_is_admin() && isset($params['profile_id']) && isset($smarty->tpl_vars['jr_template']->value) && $smarty->tpl_vars['jr_template']->value == 'item_index.tpl') {
        // We are showing an item_index - see if this profile is limited
        if (isset($smarty->tpl_vars["quota_{$module}_max_items"]->value) && jrCore_checktype($smarty->tpl_vars["quota_{$module}_max_items"]->value, 'number_nz')) {
            $max = (int) $smarty->tpl_vars["quota_{$module}_max_items"]->value;
            if ((!isset($params['limit']) || $params['limit'] > $max) && !jrProfile_is_profile_owner($params['profile_id'])) {
                $params['limit'] = $max;
            }
        }
    }

    // $params = array(
    //     'search' => array(
    //         'user_name = brian',
    //         'user_weight > 100'
    //     ),
    //     'order_by' => array(
    //         'user_name' => 'asc',
    //         'user_weight' => 'desc'
    //     ),
    //     'limit' => 50
    // );
    // {jrCore_list module="jrProfile" search1="profile_name = brian" search2="profile_name != test" order_by="created desc" template="list_profile_row.tpl" limit=5}
    // Set params for our function
    $_args = array(
        'jrcore_list_function_call_is_active' => 1
    );
    foreach ($params as $k => $v) {
        // Search
        if (strpos($k, 'search') === 0 && strlen($v) > 0) {
            if (!isset($_args['search'])) {
                $_args['search'] = array();
            }
            $_args['search'][] = $v;
        }
        // Order by
        elseif (strpos($k, 'order_by') === 0) {
            if (!isset($_args['order_by'])) {
                $_args['order_by'] = array();
            }
            list($fld, $dir) = explode(' ', $v);
            $fld                     = trim($fld);
            $_args['order_by'][$fld] = trim($dir);
        }
        // Group By
        elseif ($k == 'group_by') {
            $_args['group_by'] = trim($v);
        }
        // Limit
        elseif ($k == 'limit') {
            $_args['limit'] = (int) $v;
        }
        // Page break
        elseif ($k == 'pagebreak') {
            $_args['pagebreak'] = (int) $v;
        }
        // Page
        elseif ($k == 'page') {
            $_args['page'] = (int) $v;
        }
        elseif ($k == 'return_keys') {
            $_args['return_keys'] = explode(',', $v);
        }
        else {
            // Everything else
            $_args[$k] = $v;
        }
    }

    // Prep our data for display
    $_rs = jrCore_db_search_items($module, $_args);
    if (isset($params['return_count'])) {
        $_rs = (int) $_rs;
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $_rs);
            return '';
        }
        return $_rs;
    }
    if ($_rs && !is_array($_rs) && strpos($_rs, 'error') === 0) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $_rs);
            return '';
        }
        return $_rs;
    }
    if (isset($_rs['_items']) && is_array($_rs['_items']) && count($_rs['_items']) > 0) {
        foreach ($_rs['_items'] as $k => $_v) {
            $_rs['_items'][$k]['list_rank'] = (isset($_rs['info']['page']) && $_rs['info']['page'] > 1) ? intval($k + ((($_rs['info']['page'] - 1) * $_rs['info']['pagebreak']) + 1)) : intval($k + 1);
        }
    }

    // If we have been given NO template, just assign vars and return
    if (isset($params['template']) && $params['template'] == 'null' && !empty($params['assign'])) {
        $tmp = $_rs['_items'];
    }
    else {
        // Parse our template and return results
        $tmp = jrCore_parse_template($params['template'], $_rs, $tpl_dir);

        // See if we are including the default pager
        if (isset($params['pager']) && $params['pager'] == true && $params['pager'] !== "false") {
            $tpl = 'list_pager.tpl';
            $dir = 'jrCore';
            if (isset($params['pager_template'])) {
                $tpl = $params['pager_template'];
                $dir = $_conf['jrCore_active_skin'];
                if (!is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$params['pager_template']}") && is_file(APP_DIR . "/modules/{$module}/templates/{$params['pager_template']}")) {
                    $dir = $module;
                }
            }
            // Check for AJAX loader
            if (isset($params['pager_load_id'])) {
                if (isset($params['pager_load_template'])) {
                    $_rs['pager_load_template'] = $params['pager_load_template'];
                }
                else {
                    $_rs['pager_load_template'] = $params['template'];
                }
                $_rs['pager_load_template'] = str_replace('.tpl', '', $_rs['pager_load_template']);
                $_rs['pager_load_url']      = "{$_conf['jrCore_base_url']}/{$_rs['pager_load_template']}";
                $_rs['pager_load_id']       = $params['pager_load_id'];
            }
            $tmp .= jrCore_parse_template($tpl, $_rs, $dir);
        }
    }
    $pid = 0;
    if (isset($params['profile_id']) && jrCore_checktype($params['profile_id'], 'number_nz')) {
        $pid = (int) $params['profile_id'];
    }
    elseif (isset($_post['_profile_id']) && jrCore_checktype($_post['_profile_id'], 'number_nz')) {
        $pid = (int) $_post['_profile_id'];
    }
    jrCore_add_to_cache($module, $key, $tmp, false, $pid);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}

/**
 * Run a Smarty template function for a module
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_module_function($params, &$smarty)
{
    if (!isset($params['function']{0})) {
        return jrCore_smarty_missing_error('function');
    }
    $mod = substr($params['function'], 0, strpos($params['function'], '_'));
    if (!jrCore_module_is_active($mod)) {
        return '';
    }
    $func = "smarty_function_{$params['function']}";
    if (!function_exists($func)) {
        // Not installed or wrong...
        return '';
    }
    unset($params['function']);
    return $func($params, $smarty);
}

/**
 * Jamroom CSS SRC URL generator
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_css_src($params, &$smarty)
{
    global $_conf;
    if (!isset($params['skin']{0})) {
        $params['skin'] = $_conf['jrCore_active_skin'];
    }
    $skn = $params['skin'];
    $sum = (isset($_conf["jrCore_{$skn}_css_version"])) ? $_conf["jrCore_{$skn}_css_version"] : '';
    $prt = jrCore_get_server_protocol();
    $cdr = jrCore_get_module_cache_dir($skn);
    if (isset($prt) && $prt === 'https' || (jrUser_is_logged_in() && $_conf['jrUser_force_ssl'] == 'on')) {
        if ((strlen($sum) === 0 || !is_file("{$cdr}/S{$sum}.css")) || (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') || (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on')) {
            $sum = jrCore_create_master_css($skn);
        }
        $src = "{$_conf['jrCore_base_url']}/data/cache/{$skn}/S{$sum}.css";
    }
    else {
        if ((strlen($sum) === 0 || !is_file("{$cdr}/{$sum}.css")) || (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') || (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on')) {
            $sum = jrCore_create_master_css($skn);
        }
        $src = "{$_conf['jrCore_base_url']}/data/cache/{$skn}/{$sum}.css";
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $src);
        return '';
    }
    return $src;
}

/**
 * Jamroom Javascript SRC URL generator
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_javascript_src($params, &$smarty)
{
    global $_conf;
    if (!isset($params['skin']{0})) {
        $params['skin'] = $_conf['jrCore_active_skin'];
    }
    $skn = $params['skin'];
    $sum = (isset($_conf["jrCore_{$skn}_javascript_version"])) ? $_conf["jrCore_{$skn}_javascript_version"] : false;
    $prt = jrCore_get_server_protocol();
    $cdr = jrCore_get_module_cache_dir($skn);
    if (isset($prt) && $prt === 'https' || (jrUser_is_logged_in() && $_conf['jrUser_force_ssl'] == 'on')) {
        if (!$sum || !is_file("{$cdr}/S{$sum}.js") || (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') || (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on')) {
            $sum = jrCore_create_master_javascript($skn);
        }
        $src = "{$_conf['jrCore_base_url']}/data/cache/{$skn}/S{$sum}.js";
    }
    else {
        if (!$sum || !is_file("{$cdr}/{$sum}.js") || (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') || (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on')) {
            $sum = jrCore_create_master_javascript($skn);
        }
        $src = "{$_conf['jrCore_base_url']}/data/cache/{$skn}/{$sum}.js";
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $src);
        return '';
    }
    return $src;
}

/**
 * Generate a unique Form Session for an embedded template form
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_form_create_session($params, &$smarty)
{
    global $_conf;
    if (!isset($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['option']) || strlen($params['option']) === 0) {
        return jrCore_smarty_invalid_error('option');
    }
    $url = jrCore_get_module_url($params['module']);
    $_fm = array(
        'token'  => jrCore_form_token_create(),
        'module' => $params['module'],
        'option' => $params['option'],
        'action' => "{$_conf['jrCore_base_url']}/{$url}/{$params['option']}_save"
    );
    if (isset($params['action']{0})) {
        $_fm['action'] = $params['action'];
    }
    jrCore_form_create_session($_fm['token'], $_fm);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_fm['token']);
        return '';
    }
    return $_fm['token'];
}

/**
 * Generate a unique Form Token for use in a form
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_form_token($params, &$smarty)
{
    $out = jrCore_form_token_create();
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Display a language string by language ID
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_lang($params, &$smarty)
{
    if (!isset($params['module']{0})) {
        if (!isset($params['skin']{0})) {
            $out = 'NO_LANG_MODULE_OR_SKIN';
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $out);
                return '';
            }
            return $out;
        }
        $params['module'] = $params['skin'];
    }
    if (!isset($params['id'])) {
        $out = 'INVALID_LANG_ID';
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
        return $out;
    }

    // Bring in strings
    $_lang = jrUser_load_lang_strings();

    $out = 'NO_LANG_FOR_ID';
    if (isset($_lang["{$params['module']}"]) && isset($_lang["{$params['module']}"]["{$params['id']}"])) {
        $out = $_lang["{$params['module']}"]["{$params['id']}"];
    }
    elseif (isset($params['default']{0})) {
        if (jrUser_is_master()) {
            $out = '*' . $params['default'] . '*';
        }
        else {
            $out = $params['default'];
        }
    }
    if (strpos(' ' . $out, '%')) {
        $_rp = array();
        foreach ($params as $k => $v) {
            if (is_numeric($k)) {
                $_rp["%{$k}"] = $v;
            }
            $out = str_replace(array_keys($_rp), $_rp, $out);
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get current server protocol request came in on
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_server_protocol($params, &$smarty)
{
    $prt = jrCore_get_server_protocol();
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $prt);
        return '';
    }
    return $prt;
}

/**
 * Get the configured URL for a specific module
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_module_url($params, &$smarty)
{
    global $_urls;
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    $url = array_search($params['module'], $_urls);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $url);
        return '';
    }
    return $url;
}

/**
 * Format an epoch time stamp to the system configured time format
 * @param int $timestamp Epoch Time Stamp to convert
 * @param string $format Format for output
 * @return string
 */
function smarty_modifier_jrCore_date_format($timestamp, $format = null)
{
    return jrCore_format_time($timestamp, false, $format);
}

/**
 * @ignore
 * Convert @ tags into links to profiles
 * @deprecated - use jrCore_format_string
 * @param string $text String to convert at tags in
 * @return string
 */
function smarty_modifier_jrCore_convert_at_tags($text)
{
    global $_conf;
    if (strpos(' ' . $text, '@')) {
        $text = preg_replace('/ (@([_a-z0-9\-]+))/i', ' <a href="' . $_conf['jrCore_base_url'] . '/$2"><span class="at_link">$1</span></a>', ' ' . $text);
        return substr($text, 1);
    }
    return $text;
}

/**
 * Run internal text format functions (via triggers) on a string
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @param string $whitelist Only allow defined string format listeners to run
 * @param string $blacklist Do not allow modifiers in blacklist to run
 * @return string
 */
function smarty_modifier_jrCore_format_string($string, $quota_id = 0, $whitelist = null, $blacklist = null)
{
    // Check for white list
    $_wl = false;
    if (!is_null($whitelist) && isset($whitelist{1})) {
        $whitelist = explode(',', $whitelist);
        if (is_array($whitelist)) {
            $_wl = array();
            foreach ($whitelist as $v) {
                $v       = trim($v);
                $_wl[$v] = $v;
            }
        }
        unset($whitelist);
    }

    // Check for black list
    $_bl = false;
    if (!is_null($blacklist) && isset($blacklist{1})) {
        $blacklist = explode(',', $blacklist);
        if (is_array($blacklist)) {
            $_bl = array();
            foreach ($blacklist as $v) {
                $v       = trim($v);
                $_bl[$v] = $v;
            }
        }
        unset($blacklist);
    }

    // Get Quota info
    $_qt = array();
    if ($quota_id > 0) {
        $_qt = jrProfile_get_quota($quota_id);
        if (isset($_qt['quota_jrCore_active_formatters']{0})) {
            $_qt = explode(',', $_qt['quota_jrCore_active_formatters']);
        }
    }

    $_tmp = jrCore_get_registered_module_features('jrCore', 'format_string');
    if (isset($_tmp) && is_array($_tmp)) {

        // In order for BBCode Code blocks to work properly, we need
        // to make sure they are process FIRST in line here
        if (isset($_tmp['jrForum'])) {
            $_tm2 = array(
                'jrForum' => $_tmp['jrForum']
            );
            unset($_tmp['jrForum']);
            $_tmp = $_tm2 + $_tmp;
        }

        // If we have a white list defined, we do it in the defined order
        if (is_array($_wl) && count($_wl) > 0) {
            foreach ($_wl as $name) {
                foreach ($_tmp as $_opts) {
                    foreach ($_opts as $fnc => $_desc) {
                        if ($_desc['wl'] == $name) {
                            $string = $fnc($string, $quota_id);
                        }
                    }
                }
            }
        }
        else {

            // Process
            foreach ($_tmp as $_opts) {
                foreach ($_opts as $fnc => $_desc) {
                    if (function_exists($fnc)) {
                        // See if this formatter is active in the Quota
                        $tn = (isset($_desc['wl'])) ? $_desc['wl'] : '';
                        if ((in_array($fnc, $_qt) && (!$_wl || isset($_wl[$tn]))) && (!$_bl || !isset($_bl[$tn]))) {
                            $string = $fnc($string, $quota_id);
                        }
                    }
                }
            }
        }
    }

    // nl2br (builtin)
    if ((!$_wl || isset($_wl['nl2br'])) && (!$_bl || !isset($_bl['nl2br']))) {
        $string = nl2br($string);
    }

    // Trigger
    $_data = array(
        'string' => $string
    );
    $_data = jrCore_trigger_event('jrCore', 'format_string_display', $_data);

    // Cleanup output tags
    if (strpos($string, '/>')) {
        $_vp = array(
            "\n"         => '',
            '</p><br>'   => '</p>',
            '</p><br />' => '</p>',
            '<p><br>'    => '<p>',
            '<p><br />'  => '<p>',
            ' />'        => '>',
            '/>'         => '>'
        );
        $_data['string'] = str_replace(array_keys($_vp), $_vp, $_data['string']);
    }

    return $_data['string'];
}

/**
 * Registered Core string format - Allowed HTML
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @return string
 */
function jrCore_format_string_allowed_html($string, $quota_id = 0)
{
    // Allowed HTML
    if (jrCore_checktype($quota_id, 'number_nz') && strpos(' ' . $string, '<')) {
        // If we have an active Quota ID we need to properly strip tags
        $_qt = jrProfile_get_quota($quota_id);
        if (isset($_qt) && isset($_qt['quota_jrCore_allowed_tags']) && strlen($_qt['quota_jrCore_allowed_tags']) > 0) {
            return jrCore_strip_html($string, $_qt['quota_jrCore_allowed_tags']);
        }
    }
    // not allowed
    return jrCore_strip_html($string);
}

/**
 * Registered core string formatter - Convert @ tags
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @return string
 */
function jrCore_format_string_convert_at_tags($string, $quota_id = 0)
{
    global $_conf;
    // Convert @ tags
    if (!strpos(' ' . $string, '@')) {
        return $string;
    }
    $string = preg_replace('/ (@([_a-z0-9\-]+))/i', ' <a href="' . $_conf['jrCore_base_url'] . '/$2"><span class="at_link">$1</span></a>', ' ' . $string);
    return substr($string, 1);
}

/**
 * Registered core string formatter - Clickable URLs
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @return string
 */
function jrCore_format_string_clickable_urls($string, $quota_id = 0)
{
    // Convert URL strings
    if (!stripos(' ' . $string, 'http')) {
        return $string;
    }
    return jrCore_string_to_url($string);
}

/**
 * @ignore
 * Return portion of string up to first <!-- pagebreak -->
 * @deprecated - this functionality has moved to jrBlog
 * @param $text string String to return substring of
 * @return string
 */
function smarty_modifier_jrCore_readmore($text)
{
    if (function_exists('smarty_modifier_jrBlog_readmore')) {
        return smarty_modifier_jrBlog_readmore($text);
    }
    return $text;
}

/**
 * Close open HTML tags in a string that are left unclosed
 * http://stackoverflow.com/questions/3810230/php-how-to-close-open-html-tag-in-a-string
 * @param $html string HTML to close tags for
 * @return string
 */
function jrCore_closetags($html)
{
    preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i = 0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</' . $openedtags[$i] . '>';
        }
        else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
}

/**
 * Get Admin Menu index page for a module
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_get_module_index($params, &$smarty)
{
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    $out = jrCore_get_module_index($params['module']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Create a Create button for a new DataStore item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_create_button($params, $smarty)
{
    global $_conf, $_user;
    if (!jrUser_is_logged_in() || !jrProfile_is_profile_view()) {
        return '';
    }

    // Check for group requirement
    if (isset($params['group'])) {
        switch ($params['group']) {
            case 'master':
                if (!jrUser_is_master()) {
                    return '';
                }
                break;
            case 'admin':
                if (!jrUser_is_admin()) {
                    return '';
                }
                break;
        }
    }
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    $out = '';
    $skn = $_conf['jrCore_active_skin'];
    // See if this user has access to perform this action on this profile
    if (jrProfile_is_profile_owner($params['profile_id'])) {

        // Bring in language strings
        $_lang = jrUser_load_lang_strings();

        // See if we are using the default view
        $def = 'create';
        if (isset($params['view']{0})) {
            $def = trim($params['view']);
        }
        // Figure button ID
        $bid = "{$params['module']}_create";
        if (isset($params['id'])) {
            $bid = $params['id'];
        }
        $anc = true;
        $url = jrCore_get_module_url($params['module']);
        if (isset($params['action'])) {
            $onc = "jrCore_window_location('{$_conf['jrCore_base_url']}/{$params['action']}')";
            $url = "{$_conf['jrCore_base_url']}/{$params['action']}";
        }
        else {
            $onc = "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/{$def}')";
            $url = "{$_conf['jrCore_base_url']}/{$url}/{$def}";
        }
        // See if we are being given the onclick
        if (isset($params['onclick']{1})) {
            $onc = $params['onclick'];
            $anc = false;
        }

        // See if we are limiting the number of items that can be created by a profile in this quota
        $q_max = isset($_user["quota_{$params['module']}_max_items"]) ? (int) $_user["quota_{$params['module']}_max_items"] : 0;

        // See if we need to show the user they have reached their limit
        if (!jrUser_is_admin() && $q_max && $q_max > 0) {
            $p_cnt = jrCore_get_flag("profile_{$params['module']}_item_count");
            if (!$p_cnt) {
                $p_cnt = jrCore_db_get_item_key('jrProfile', $_user['user_active_profile_id'], "profile_{$params['module']}_item_count");
            }
            if ($p_cnt >= $q_max) {
                $onc = "alert('" . jrCore_entity_string($_lang['jrCore'][70]) . "');return false;";
                $anc = false;
            }
        }

        if (!isset($params['alt'])) {
            $params['alt'] = $_lang['jrCore'][36];
        }
        if (jrCore_checktype($params['alt'], 'number_nz') && isset($_lang["{$params['module']}"]["{$params['alt']}"])) {
            $alt = ' alt="' . jrCore_entity_string($_lang["{$params['module']}"]["{$params['alt']}"]) . '"';
        }
        else {
            $alt = ' alt="' . jrCore_entity_string($params['alt']) . '"';
        }
        $ttl = '';
        if (isset($params['title'])) {
            if (jrCore_checktype($params['title'], 'number_nz') && isset($_lang["{$params['module']}"]["{$params['title']}"])) {
                $ttl = ' title="' . jrCore_entity_string($_lang["{$params['module']}"]["{$params['title']}"]) . '"';
            }
            else {
                $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
            }
        }
        elseif (isset($alt) && strlen($alt) > 0) {
            $ttl = ' title=' . substr($alt, 5);
        }

        // Check for "icon" param
        if (isset($params['icon']{0}) || !isset($params['image'])) {

            if (!isset($params['icon']) && !isset($params['image'])) {
                $params['icon'] = 'plus';
            }
            if (isset($params['title'])) {
                if (isset($_lang["{$params['module']}"]["{$params['title']}"])) {
                    $params['title'] = $_lang["{$params['module']}"]["{$params['title']}"];
                }
                $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
            }
            elseif (isset($params['alt'])) {
                if (isset($_lang["{$params['module']}"]["{$params['alt']}"])) {
                    $params['alt'] = $_lang["{$params['module']}"]["{$params['alt']}"];
                }
                $ttl = ' title="' . jrCore_entity_string($params['alt']) . '"';
            }
            else {
                $ttl = ' title="' . jrCore_entity_string($_lang['jrCore'][36]) . '"';
            }
            $id = '';
            if (isset($params['id'])) {
                $id = ' id="' . $params['id'] . '"';
            }
            $out = "<a href=\"{$url}\"" . $id . $ttl . ' onclick="' . $onc . '">' . jrCore_get_sprite_html($params['icon']) . '</a>';
        }

        // See if we are doing an IMAGE as the button - this will override
        // any default button images setup in the Active Skin
        elseif (isset($params['image']) && strlen($params['image']) > 0) {

            $wdt = '';
            if (isset($params['width']) && jrCore_checktype($params['width'], 'number_nz')) {
                $wdt = ' width="' . intval($params['width']) . '"';
            }
            $hgt = '';
            if (isset($params['height']) && jrCore_checktype($params['height'], 'number_nz')) {
                $hgt = ' height="' . intval($params['height']) . '"';
            }

            // figure our src
            if (strpos($params['image'], $_conf['jrCore_base_url']) !== 0) {
                // See if we have a custom image...
                $_im = array();
                if (isset($_conf["jrCore_{$params['module']}_custom_images"]{2})) {
                    $_im = json_decode($_conf["jrCore_{$params['module']}_custom_images"], true);
                }
                if (isset($_im["{$params['image']}"]) && isset($_im["{$params['image']}"][1]) && $_im["{$params['image']}"][1] == 'on') {
                    $params['image'] = "{$_conf['jrCore_base_url']}/data/media/0/0/mod_{$params['module']}_{$params['image']}?r=" . $_im["{$params['image']}"][0];
                }
                else {
                    // Check for skin override
                    $bimg = basename($params['image']);
                    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$bimg}")) {
                        $params['image'] = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$bimg}";
                    }
                    else {
                        $params['image'] = "{$_conf['jrCore_base_url']}/modules/{$params['module']}/img/{$bimg}";
                    }
                }
            }

            $cls = '';
            if (isset($params['class'])) {
                $cls = ' class="' . $params['class'] . '"';
            }

            // We're using an image for our button
            if ($anc) {
                $out = '<a href="' . $url . '"><img src="' . $params['image'] . '"' . $cls . $hgt . $wdt . $alt . $ttl . '></a>';
            }
            else {
                $out = '<img id="' . $bid . '" src="' . $params['image'] . '" onclick="' . $onc . '"' . $cls . $hgt . $wdt . $alt . $ttl . '>';
            }
        }
        else {

            // Get skin image attributes
            $_tmp = jrCore_get_registered_module_features('jrCore', 'skin_action_button');

            // Check for skin override
            if (isset($_tmp[$skn]['create']) && is_array($_tmp[$skn]['create']) && $_tmp[$skn]['create']['type'] == 'image') {

                $src = "{$_conf['jrCore_base_url']}/skins/{$skn}/img/{$_tmp[$skn]['create']['image']}";
                if (isset($_conf["jrCore_{$skn}_custom_images"]{2})) {
                    $_im = json_decode($_conf["jrCore_{$skn}_custom_images"], true);
                    if (isset($_im['create.png']) && isset($_im['create.png'][1]) && $_im['create.png'][1] == 'on') {
                        $src = "{$_conf['jrCore_base_url']}/data/media/0/0/{$skn}_create.png?r=" . $_im['create.png'][0];
                    }
                }

                // Check for class
                $cls = ' class="create_img"';
                if (isset($params['class'])) {
                    $cls = ' class="create_img ' . $params['class'] . '"';
                }
                if ($anc) {
                    $out = '<a href="' . $url . '"><img src="' . $src . '"' . $cls . $ttl . $alt . '></a>';
                }
                else {
                    $out = '<img src="' . $src . '" onclick="' . $onc . '"' . $cls . $ttl . $alt . '>';
                }
            }
            else {

                // Check for button value
                $txt = (isset($_lang['jrCore'][36])) ? $_lang['jrCore'][36] : 'create';
                if (isset($params['value'])) {
                    if (is_numeric($params['value']) && isset($_lang["{$params['module']}"]["{$params['value']}"])) {
                        $txt = $_lang["{$params['module']}"]["{$params['value']}"];
                    }
                    else {
                        $txt = $params['value'];
                    }
                }
                // Check for additional options to pass to button
                $_bp = array();
                if (isset($params['style'])) {
                    $_bp['style'] = $params['style'];
                }
                $out = jrCore_page_button($bid, $txt, $onc, $_bp);
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Create an Update button for a DataStore item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_update_button($params, &$smarty)
{
    global $_conf;
    if (!jrUser_is_logged_in() || !jrProfile_is_profile_view()) {
        return '';
    }
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['action'])) {
        if (!isset($params['item_id'])) {
            return jrCore_smarty_missing_error('item_id');
        }
        if (!jrCore_checktype($params['item_id'], 'number_nz')) {
            return jrCore_smarty_invalid_error('item_id');
        }
    }
    $out = '';
    $skn = $_conf['jrCore_active_skin'];
    // See if this user has access to perform this action on this profile
    if (jrProfile_is_profile_owner($params['profile_id'])) {

        // Bring in language strings
        $_lang = jrUser_load_lang_strings();

        // See if we are using the default view
        $def = 'update';
        if (isset($params['view']{0})) {
            $def = trim($params['view']);
        }
        // Figure button ID
        $bid = "{$params['module']}_update";
        if (isset($params['id'])) {
            $bid = $params['id'];
        }
        $anc = true;
        $url = jrCore_get_module_url($params['module']);
        if (isset($params['action'])) {
            $onc = "jrCore_window_location('{$_conf['jrCore_base_url']}/{$params['action']}')";
            $url = "{$_conf['jrCore_base_url']}/{$params['action']}";
        }
        else {
            $onc = "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/{$def}/id={$params['item_id']}')";
            $url = "{$_conf['jrCore_base_url']}/{$url}/{$def}/id={$params['item_id']}";
        }
        // See if we are being given the onclick
        if (isset($params['onclick']{1})) {
            $onc = $params['onclick'];
            $anc = false;
        }

        // Check for "icon" param
        if (isset($params['icon']{0}) || !isset($params['image'])) {

            if (!isset($params['icon']) && !isset($params['image'])) {
                $params['icon'] = 'gear';
            }

            if (isset($params['title'])) {
                if (isset($_lang["{$params['module']}"]["{$params['title']}"])) {
                    $params['title'] = $_lang["{$params['module']}"]["{$params['title']}"];
                }
                $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
            }
            elseif (isset($params['alt'])) {
                if (isset($_lang["{$params['module']}"]["{$params['alt']}"])) {
                    $params['alt'] = $_lang["{$params['module']}"]["{$params['alt']}"];
                }
                $ttl = ' title="' . jrCore_entity_string($params['alt']) . '"';
            }
            else {
                $ttl = ' title="' . jrCore_entity_string($_lang['jrCore'][37]) . '"';
            }
            $out = "<a href=\"{$url}\"" . $ttl . ' onclick="' . $onc . '">' . jrCore_get_sprite_html($params['icon']) . '</a>';
        }

        // See if we are doing an IMAGE as the button - this will override
        // any default button images setup in the Active Skin
        elseif (isset($params['image']) && strlen($params['image']) > 0) {

            $wdt = '';
            if (isset($params['width']) && jrCore_checktype($params['width'], 'number_nz')) {
                $wdt = ' width="' . intval($params['width']) . '"';
            }
            $hgt = '';
            if (isset($params['height']) && jrCore_checktype($params['height'], 'number_nz')) {
                $hgt = ' height="' . intval($params['height']) . '"';
            }
            // figure our src
            if (strpos($params['image'], $_conf['jrCore_base_url']) !== 0) {
                $_im = array();
                // See if we have a custom image...
                if (isset($_conf["jrCore_{$params['module']}_custom_images"]{2})) {
                    $_im = json_decode($_conf["jrCore_{$params['module']}_custom_images"], true);
                }
                if (isset($_im["{$params['image']}"]) && isset($_im["{$params['image']}"][1]) && $_im["{$params['image']}"][1] == 'on') {
                    $params['image'] = "{$_conf['jrCore_base_url']}/data/media/0/0/mod_{$params['module']}_{$params['image']}?r=" . $_im["{$params['image']}"][0];
                }
                else {
                    // Check for skin override
                    $bimg = basename($params['image']);
                    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$bimg}")) {
                        $params['image'] = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$bimg}";
                    }
                    else {
                        $params['image'] = "{$_conf['jrCore_base_url']}/modules/{$params['module']}/img/{$bimg}";
                    }
                }
            }

            // Check for class
            $cls = '';
            if (isset($params['class'])) {
                $cls = ' class="' . $params['class'] . '"';
            }
            $alt = '';
            if (isset($params['alt'])) {
                $alt = ' alt="' . jrCore_entity_string($params['alt']) . '"';
            }
            $ttl = '';
            if (isset($params['title'])) {
                $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
            }
            elseif (isset($params['alt'])) {
                $ttl = ' title="' . jrCore_entity_string($params['alt']) . '"';
            }
            elseif (isset($alt) && strlen($alt) > 0) {
                $ttl = ' title=' . substr($alt, 5);
            }

            // We're using an image for our button
            if ($anc) {
                $out = '<a href="' . $url . '"><img src="' . $params['image'] . '"' . $cls . $hgt . $wdt . $alt . $ttl . '></a>';
            }
            else {
                $out = '<img src="' . $params['image'] . '" onclick="' . $onc . '"' . $cls . $hgt . $wdt . $alt . $ttl . '>';
            }
        }
        else {

            // Get skin image attributes
            $_tmp = jrCore_get_registered_module_features('jrCore', 'skin_action_button');

            // Check for skin override
            if (isset($_tmp[$skn]['update']) && is_array($_tmp[$skn]['update']) && $_tmp[$skn]['update']['type'] == 'image') {
                $src = "{$_conf['jrCore_base_url']}/skins/{$skn}/img/{$_tmp[$skn]['update']['image']}";
                if (isset($_conf["jrCore_{$skn}_custom_images"]{2})) {
                    $_im = json_decode($_conf["jrCore_{$skn}_custom_images"], true);
                    if (isset($_im['update.png']) && isset($_im['update.png'][1]) && $_im['update.png'][1] == 'on') {
                        $src = "{$_conf['jrCore_base_url']}/data/media/0/0/{$skn}_update.png?r=" . $_im['update.png'][0];
                    }
                }

                // Check for class
                $cls = ' class="update_img"';
                if (isset($params['class'])) {
                    $cls = ' class="update_img ' . $params['class'] . '"';
                }
                $alt = $_lang['jrCore'][37];
                if (isset($params['alt'])) {
                    $alt = $params['alt'];
                }
                $ttl = '';
                if (isset($params['title'])) {
                    $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
                }
                elseif (isset($params['alt'])) {
                    $ttl = ' title="' . jrCore_entity_string($params['alt']) . '"';
                }
                elseif (isset($alt) && strlen($alt) > 0) {
                    $ttl = ' title="' . $alt . '"';
                }
                // We're using an image for our button
                if ($anc) {
                    $out = '<a href="' . $url . '"><img src="' . $src . '"' . $cls . $ttl . ' alt="' . $alt . '"></a>';
                }
                else {
                    $out = '<img src="' . $src . '" onclick="' . $onc . '"' . $cls . $ttl . ' alt="' . $alt . '">';
                }
            }
            else {

                // Check for button value
                if (isset($_tmp[$skn]['update']) && is_array($_tmp[$skn]['update']) && isset($_tmp[$skn]['update']['image']) && jrCore_checktype($_tmp[$skn]['update']['image'], 'number_nz')) {
                    $txt = (isset($_lang[$skn]["{$_tmp[$skn]['update']['image']}"])) ? $_lang[$skn]["{$_tmp[$skn]['update']['image']}"] : $_tmp[$skn]['update']['image'];
                }
                else {
                    $txt = (isset($_lang['jrCore'][37])) ? $_lang['jrCore'][37] : 'update';
                    if (isset($params['value'])) {
                        if (is_numeric($params['value']) && isset($_lang["{$params['module']}"]["{$params['value']}"])) {
                            $txt = $_lang["{$params['module']}"]["{$params['value']}"];
                        }
                        else {
                            $txt = $params['value'];
                        }
                    }
                }
                // Check for additional options to pass to button
                $_bp = array();
                if (isset($params['style'])) {
                    $_bp['style'] = $params['style'];
                }
                $out = jrCore_page_button($bid, $txt, $onc, $_bp);
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Create a delete button for a DataStore item
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_delete_button($params, &$smarty)
{
    global $_conf;
    if (!jrUser_is_logged_in() || !jrProfile_is_profile_view()) {
        return '';
    }
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['action'])) {
        if (!isset($params['item_id'])) {
            return jrCore_smarty_missing_error('item_id');
        }
        if (!jrCore_checktype($params['item_id'], 'number_nz')) {
            return jrCore_smarty_invalid_error('item_id');
        }
    }
    $out = '';
    $skn = $_conf['jrCore_active_skin'];
    // See if this user has access to perform this action on this profile
    if (jrProfile_is_profile_owner($params['profile_id'])) {

        // Bring in language strings
        $_lang = jrUser_load_lang_strings();

        // See if we are using the default view
        $def = 'delete';
        if (isset($params['view']{0})) {
            $def = trim($params['view']);
        }

        // Figure button ID
        $bid = "{$params['module']}_delete";
        if (isset($params['id'])) {
            $bid = $params['id'];
        }
        $url = jrCore_get_module_url($params['module']);

        // Check for onclick prompt
        $ptx = (isset($_lang['jrCore'][40])) ? $_lang['jrCore'][40] : 'are you sure you want to delete this item?';
        if (isset($params['prompt'])) {
            if ($params['prompt'] === false) {
                // do not show a prompt
                $ptx = false;
            }
            elseif (is_numeric($params['prompt']) && isset($_lang["{$params['module']}"]["{$params['prompt']}"])) {
                $ptx = $_lang["{$params['module']}"]["{$params['prompt']}"];
            }
            else {
                $ptx = $params['prompt'];
            }
        }
        // See if we are being given the onclick
        if (isset($params['action'])) {
            $url = "{$_conf['jrCore_base_url']}/{$params['action']}";
        }
        else {
            $url = "{$_conf['jrCore_base_url']}/{$url}/{$def}/id={$params['item_id']}";
        }

        $ask = '';
        if (isset($params['onclick']{1})) {
            $ask = $params['onclick'];
        }
        if ($ptx) {
            $ask = "if (!confirm('" . jrCore_entity_string($ptx) . "')){ return false; }";
        }

        if ($ask) {
            $onc = ' onclick="jrCore_set_csrf_cookie(\'' . $url . '\'); ' . $ask . '"';
        }
        else {
            $onc = ' onclick="jrCore_set_csrf_cookie(\'' . $url . '\')"';
        }

        // Check for "icon" param
        if (isset($params['icon']{0}) || !isset($params['image'])) {

            if (!isset($params['icon']) && !isset($params['image'])) {
                $params['icon'] = 'trash';
            }
            if (isset($params['title'])) {
                if (isset($_lang["{$params['module']}"]["{$params['title']}"])) {
                    $params['title'] = $_lang["{$params['module']}"]["{$params['title']}"];
                }
                $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
            }
            elseif (isset($params['alt'])) {
                if (isset($_lang["{$params['module']}"]["{$params['alt']}"])) {
                    $params['alt'] = $_lang["{$params['module']}"]["{$params['alt']}"];
                }
                $ttl = ' title="' . jrCore_entity_string($params['alt']) . '"';
            }
            else {
                $ttl = ' title="' . jrCore_entity_string($_lang['jrCore'][38]) . '"';
            }
            $out = "<a href=\"{$url}\"" . $ttl . $onc . ">" . jrCore_get_sprite_html($params['icon']) . '</a>';
        }

        // See if we are doing an IMAGE as the button - this will override
        // any default button images setup in the Active Skin
        elseif (isset($params['image']) && strlen($params['image']) > 0) {

            $wdt = '';
            if (isset($params['width']) && jrCore_checktype($params['width'], 'number_nz')) {
                $wdt = ' width="' . intval($params['width']) . '"';
            }
            $hgt = '';
            if (isset($params['height']) && jrCore_checktype($params['height'], 'number_nz')) {
                $hgt = ' height="' . intval($params['height']) . '"';
            }
            // figure our src
            if (strpos($params['image'], $_conf['jrCore_base_url']) !== 0) {
                // See if we have a custom image...
                $_im = array();
                if (isset($_conf["jrCore_{$params['module']}_custom_images"]{2})) {
                    $_im = json_decode($_conf["jrCore_{$params['module']}_custom_images"], true);
                }
                if (isset($_im["{$params['image']}"]) && isset($_im["{$params['image']}"][1]) && $_im["{$params['image']}"][1] == 'on') {
                    $params['image'] = "{$_conf['jrCore_base_url']}/data/media/0/0/mod_{$params['module']}_{$params['image']}?r=" . $_im["{$params['image']}"][0];
                }
                else {
                    // Check for skin override
                    $bimg = basename($params['image']);
                    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$bimg}")) {
                        $params['image'] = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$bimg}";
                    }
                    else {
                        $params['image'] = "{$_conf['jrCore_base_url']}/modules/{$params['module']}/img/{$bimg}";
                    }
                }
            }

            // Check for class
            $cls = '';
            if (isset($params['class'])) {
                $cls = ' class="' . $params['class'] . '"';
            }
            $alt = '';
            if (isset($params['alt'])) {
                $alt = ' alt="' . jrCore_entity_string($params['alt']) . '"';
            }
            $ttl = '';
            if (isset($params['title'])) {
                $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
            }
            elseif (isset($params['alt'])) {
                $ttl = ' title="' . jrCore_entity_string($params['alt']) . '"';
            }
            elseif (isset($alt) && strlen($alt) > 0) {
                $ttl = ' title=' . substr($alt, 5);
            }
            $out = '<a href="' . $url . '"><img src="' . $params['image'] . '"' . $cls . $hgt . $wdt . $alt . $ttl . $onc . '></a>';
        }
        else {

            // Get skin image attributes
            $_tmp = jrCore_get_registered_module_features('jrCore', 'skin_action_button');

            // Check for skin override
            if (isset($_tmp[$skn]['delete']) && is_array($_tmp[$skn]['delete']) && $_tmp[$skn]['delete']['type'] == 'image') {
                $src = "{$_conf['jrCore_base_url']}/skins/{$skn}/img/{$_tmp[$skn]['delete']['image']}";
                if (isset($_conf["jrCore_{$skn}_custom_images"]{2})) {
                    $_im = json_decode($_conf["jrCore_{$skn}_custom_images"], true);
                    if (isset($_im['delete.png']) && isset($_im['delete.png'][1]) && $_im['delete.png'][1] == 'on') {
                        $src = "{$_conf['jrCore_base_url']}/data/media/0/0/{$skn}_delete.png?r=" . $_im['delete.png'][0];
                    }
                }

                // Check for class
                $cls = ' class="delete_img"';
                if (isset($params['class'])) {
                    $cls = ' class="delete_img ' . $params['class'] . '"';
                }
                $alt = $_lang['jrCore'][38];
                if (isset($params['alt'])) {
                    $alt = $params['alt'];
                }
                $ttl = '';
                if (isset($params['title'])) {
                    $ttl = ' title="' . jrCore_entity_string($params['title']) . '"';
                }
                elseif (isset($params['alt'])) {
                    $ttl = ' title="' . jrCore_entity_string($params['alt']) . '"';
                }
                elseif (isset($alt) && strlen($alt) > 0) {
                    $ttl = ' title="' . $alt . '"';
                }
                $out = '<a href="' . $url . '"><img src="' . $src . '"' . $cls . $onc . $ttl . ' alt="' . $alt . '"></a>';
            }
            else {

                // Check for button value
                if (isset($_tmp[$skn]['delete']) && is_array($_tmp[$skn]['delete']) && isset($_tmp[$skn]['delete']['image']) && jrCore_checktype($_tmp[$skn]['delete']['image'], 'number_nz')) {
                    $txt = (isset($_lang[$skn]["{$_tmp[$skn]['delete']['image']}"])) ? $_lang[$skn]["{$_tmp[$skn]['delete']['image']}"] : $_tmp[$skn]['delete']['image'];
                }
                else {
                    $txt = (isset($_lang['jrCore'][38])) ? $_lang['jrCore'][38] : 'delete';
                    if (isset($params['value'])) {
                        if (is_numeric($params['value']) && isset($_lang["{$params['module']}"]["{$params['value']}"])) {
                            $txt = $_lang["{$params['module']}"]["{$params['value']}"];
                        }
                        else {
                            $txt = $params['value'];
                        }
                    }
                }
                // Check for additional options to pass to button
                $_bp = array();
                if (isset($params['style'])) {
                    $_bp['style'] = $params['style'];
                }
                $out = jrCore_page_button($bid, $txt, $ask, $_bp);
            }
        }
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
    }
    return $out;
}

/**
 * Create an array within a template
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_item_order_button($params, &$smarty)
{
    global $_conf;
    if (!jrUser_is_logged_in()) {
        return '';
    }
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['profile_id'])) {
        return jrCore_smarty_missing_error('profile_id');
    }
    if (!jrCore_checktype($params['profile_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('profile_id');
    }
    $out = '';
    // See if this user has access to perform this action on this profile
    if (jrProfile_is_profile_owner($params['profile_id'])) {
        if (!isset($params['icon'])) {
            $params['icon'] = 'refresh';
        }
        $_ln = jrUser_load_lang_strings();
        $out = '<a href="' . $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url($params['module']) . '/item_display_order" title="' . $_ln['jrCore'][83] . '">' . jrCore_get_sprite_html($params['icon']) . '</a>';
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $out);
            return '';
        }
    }
    return $out;
}

/**
 * Create an array within a template
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_array($params, &$smarty)
{
    if (!isset($params['name']) || strlen($params['name']) === 0) {
        return jrCore_smarty_missing_error('name');
    }
    // check for our separator
    if (isset($params['explode']) && (!isset($params['separator']) || strlen($params['separator']) === 0)) {
        $params['separator'] = ',';
    }
    // See if we have a comma and our explode value
    $_tmp = array();
    if (isset($params['explode'])) {
        if (isset($params['key']) && strlen($params['key']) > 0) {
            $_tmp["{$params['key']}"] = explode($params['separator'], $params['value']);
        }
        else {
            $_tmp = explode($params['separator'], $params['value']);
        }
    }
    else {
        if (isset($params['key']) && strlen($params['key']) > 0) {
            $_tmp["{$params['key']}"] = $params['value'];
        }
        else {
            $_tmp = $params['value'];
        }
    }
    // see if we already exists - if so we need to append
    if (is_array($smarty->getTemplateVars($params['name']))) {
        $smarty->append($params['name'], $_tmp, true);
    }
    else {
        $smarty->assign($params['name'], $_tmp);
    }
    return '';
}

/**
 * Load a remote URL into a template variable
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrCore_load_url($params, &$smarty)
{
    $out = '';
    if (jrCore_checktype($params['url'], 'url')) {
        $out = jrCore_load_url($params['url']);
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Set a page title from a Template
 * @param array $params function params
 * @param object $smarty Smarty Object
 * @return string
 */
function smarty_function_jrCore_page_title($params, &$smarty)
{
    if (isset($params['title'])) {
        jrCore_page_title($params['title']);
    }
    return '';
}

/**
 * Include a Jamroom Template within another template
 * @param array $params function params
 * @param object $smarty Smarty Object
 * @return string
 */
function smarty_function_jrCore_include($params, &$smarty)
{
    global $_conf;
    $dir = $_conf['jrCore_active_skin'];
    if (isset($params['module'])) {
        $dir = $params['module'];
    }
    elseif (isset($params['skin'])) {
        $dir = $params['skin'];
    }
    if (!isset($params['template'])) {
        return jrCore_smarty_missing_error('template');
    }
    $_rp = $smarty->getTemplateVars();
    foreach ($params as $k => $v) {
        $_rp[$k] = $v;
    }
    $chk = false;
    if (isset($params['disable_override'])) {
        $chk = $params['disable_override'];
    }
    $out = jrCore_parse_template($params['template'], $_rp, $dir, $chk);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get DataStore prefix for a given module and save to template variable
 * @param array $params function params
 * @param object $smarty Smarty Object
 * @return string
 */
function smarty_function_jrCore_get_datastore_prefix($params, &$smarty)
{
    if (!isset($params['module']{0})) {
        return jrCore_smarty_missing_error('module');
    }
    $out = jrCore_db_get_prefix($params['module']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Random number generator
 * @param array $params function params
 * @param object $smarty Smarty Object
 * @return string
 */
function smarty_function_jrCore_random_number($params, &$smarty)
{
    if (!isset($params['assign'])) {
        return jrCore_smarty_missing_error('assign');
    }
    if (!isset($params['min'])) {
        $params['min'] = 0;
    }
    if (!isset($params['max'])) {
        $params['max'] = 10;
    }
    $tmp = mt_rand($params['min'], $params['max']);
    $smarty->assign($params['assign'], $tmp);
    return '';
}

/**
 * Create an Icon in a template
 * @param array $params function params
 * @param object $smarty Smarty Object
 * @return string
 */
function smarty_function_jrCore_icon($params, &$smarty)
{
    global $_conf;
    if (empty($params['icon'])) {
        return jrCore_smarty_missing_error('icon');
    }
    $size = 32;
    if (isset($params['size']) && is_numeric($params['size'])) {
        $size = (int) $params['size'];
    }
    else {
        // See if our skin has registered an icon size
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_size');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $size = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $size = (int) reset($size);
        }
    }
    $cls = null;
    if (isset($params['class'])) {
        $cls = $params['class'];
    }
    $out = jrCore_get_icon_html($params['icon'], $size, $cls);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get HTML for a Sprite Icon
 * @param $icon string Icon
 * @param $size int Icon size in pixels (square)
 * @param $class string additional icon class
 * @return string
 */
function jrCore_get_icon_html($icon, $size = null, $class = null)
{
    global $_conf;
    // See if our skin has registered an icon size
    if (is_null($size)) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_size');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $size = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $size = (int) reset($size);
        }
    }
    return jrCore_get_sprite_html($icon, $size, $class);
}

/**
 * Site Statistics for modules that have registered
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCore_stats($params, &$smarty)
{
    global $_conf;
    if (!isset($params['template']{1})) {
        return jrCore_smarty_missing_error('template');
    }
    // We piggyback on Profile Stats...
    $_tmp = jrCore_get_registered_module_features('jrProfile', 'profile_stats');
    if (!$_tmp) {
        // No registered modules
        return '';
    }
    // Get all table counts in 1 shot
    $req = "SELECT TABLE_NAME, TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$_conf['jrCore_db_name']}'";
    $_rt = jrCore_db_query($req, 'TABLE_NAME', false, 'TABLE_ROWS');
    if (!$_rt || !is_array($_rt)) {
        // No Table counts - shouldn't happen
        return '';
    }
    $_st['_stats'] = array();
    $_lang         = jrUser_load_lang_strings();
    foreach ($_tmp as $mod => $_stats) {
        foreach ($_stats as $key => $title) {
            if (is_numeric($title) && isset($_lang[$mod][$title])) {
                $title = $_lang[$mod][$title];
            }
            // See if we have been given a function
            $count = false;
            if (function_exists($key)) {
                $count = $key();
            }
            else {
                $key = jrCore_db_table_name($mod, 'item');
                if (isset($_rt[$key]) && $_rt[$key] > 0) {
                    $count = $_rt[$key];
                }
            }
            if ($count) {
                $_st['_stats'][$title] = array(
                    'count'  => $count,
                    'module' => $mod
                );
            }
        }
    }
    $out = '';
    if (isset($_st['_stats']) && is_array($_st['_stats'])) {
        $out = jrCore_parse_template($params['template'], $_st, 'jrProfile');
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get DataStore item into an array
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCore_db_get_item($params, &$smarty)
{
    if (!isset($params['module']) || !jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['item_id'])) {
        return jrCore_smarty_missing_error('item_id');
    }
    if (!jrCore_checktype($params['item_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('item_id');
    }
    if (!isset($params['assign'])) {
        return jrCore_smarty_missing_error('assign');
    }
    $st = false;
    if (isset($params['skip_triggers']) && $params['skip_triggers'] == true) {
        $st = true;
    }
    $smarty->assign($params['assign'], jrCore_db_get_item($params['module'], $params['item_id'], $st));
    return '';
}

/**
 * Show a pending item notice in a template
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCore_pending_notice($params, $smarty)
{
    if (!isset($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (!jrCore_module_is_active($params['module'])) {
        return '';
    }
    if (!isset($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!is_array($params['item'])) {
        return jrCore_smarty_invalid_error('item');
    }
    $out = jrCore_show_pending_notice($params['module'], $params['item'], true);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show an Editor Form Field in a template
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCore_editor_field($params, $smarty)
{
    global $_conf, $_mods, $_user;
    $tmp = jrCore_get_flag('jrcore_editor_js_included');
    if (!$tmp) {
        $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/tinymce/tinymce.min.js?v={$_mods['jrCore']['module_version']}");
        jrCore_create_page_element('javascript_href', $_js);
        jrCore_set_flag('jrcore_editor_js_included', 1);
    }
    $_rp = array(
        'field_name' => $params['name'],
        'form_editor_id' => 'e' . $params['name']
    );
    // Initialize fields
    $_rp['theme'] = 'modern';
    if (isset($params['theme']) && jrCore_checktype($params['theme'], 'string')) {
        $_rp['theme'] = ($params['theme'] == 'advanced') ? 'modern' : $params['theme'];
    }
    else {
        $params['theme'] = 'modern';
    }
    $allowed_tags = explode(',', $_user['quota_jrCore_allowed_tags']);
    foreach ($allowed_tags as $tag) {
        $_rp[$tag] = true;
    }

    // See what modules are providing
    $_tm = jrCore_get_registered_module_features('jrCore', 'editor_button');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_items) {
            $tag = strtolower($mod);
            $_rp[$tag] = false;
            // Make sure the user is allowed Quota access
            if (jrCore_module_is_active($mod) && isset($_user["quota_{$mod}_allowed"]) && $_user["quota_{$mod}_allowed"] == 'on') {
                if (is_file(APP_DIR ."/modules/{$mod}/tinymce/plugin.min.js")) {
                    $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/{$mod}/tinymce/plugin.min.js?v=" . $_mods[$mod]['module_version']);
                    jrCore_create_page_element('javascript_href', $_js);
                }
                $_rp[$tag] = true;
            }
        }
    }
    $ini = @jrCore_parse_template('form_editor.tpl', $_rp, 'jrCore');
    $_js = array($ini);
    jrCore_create_page_element('javascript_ready_function', $_js);

    $out = '<div class="form_editor_holder"><textarea id="e' . $params['name'] . '" class="form_textarea form_editor" name="' . $params['name'] . '" tabindex="1">';
    $val = '';
    if (isset($params['value']) && strlen($params['value']) > 0) {
        $val = $params['value'];
    }
    elseif (isset($params['default']) && strlen($params['default']) > 0) {
        $val = $params['default'];
    }
    $out .= $val . '</textarea><input type="hidden" id="' . $params['name'] . '_editor_contents" name="' . $params['name'] . '_editor_contents" value=""></div>';
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Add 'Powered by Jamroom' link
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrCore_powered_by($params, &$smarty)
{
    $key = 'footer_powered_by';
    if ($footer = jrCore_is_cached('jrCore', $key)) {
        return $footer;
    }

    $_options = array(
        'best cms ' . strftime('%Y'),
        'best community script',
        'best free cms',
        'best open source cms',
        'best social network script',
        'best social networking script',
        'best social networking scripts',
        'cms free',
        'cms open source',
        'cms software',
        'community cms',
        'community script',
        'community scripts',
        'community software',
        'content management software',
        'content management system',
        'download cms',
        'enterprise content management system',
        'free community cms',
        'free community script',
        'free social media platforms',
        'free social network script',
        'free social network scripts',
        'free social networking script',
        'free social networking scripts',
        'free socialnetwork scripts',
        'music community scripts',
        'music social network',
        'musical social network',
        'musician social network',
        'musicians social network',
        'musicians social platform',
        'open source content management system',
        'open source social network',
        'open source social network script',
        'open source social networking script',
        'open source web cms',
        'php social network',
        'php social networking script',
        'social community platform',
        'social media platform',
        'social media platforms',
        'social network cms',
        'social network platform',
        'social network platforms',
        'social network script',
        'social network scripts',
        'social networking platform',
        'social networking platforms',
        'social networking script',
        'social networking scripts',
        'web based content management system',
    );
    $k        = array_rand($_options);
    $option   = $_options[$k];
    $footer   = '<span style="font-size:9px;"><a href="http://www.jamroom.net">' . $option . '</a> | Powered by <a href="http://www.jamroom.net">Jamroom</a></span>';

    jrCore_add_to_cache('jrCore', $key, $footer);
    return $footer;
}
