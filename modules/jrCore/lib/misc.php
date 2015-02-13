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
 * @package Extras
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Returns TRUE if current view is a MAGIC view
 * @return bool
 */
function jrCore_is_magic_view()
{
    return (jrCore_get_flag('jrcore_is_magic_view')) ? true : false;
}

/**
 * Check if a user is part of a specific user group
 * @param $group mixed user group OR quota ID OR comma list of group/quota_id
 * @param $_usr array User information to check (default is viewing user)
 * @return bool
 */
function jrCore_user_is_part_of_group($group, $_usr = null)
{
    global $_user;
    foreach (explode(',', $group) as $grp) {
        switch ($grp) {
            case 'all':
                return true;
                break;
            case 'master':
                if (jrUser_is_master()) {
                    return true;
                }
                break;
            case 'admin':
                if (jrUser_is_admin()) {
                    return true;
                }
                break;
            case 'power':
            case 'power_user':
                if (jrUser_is_power_user()) {
                    return true;
                }
                break;
            case 'multi':
            case 'multi_user':
                if (jrUser_is_multi_user()) {
                    return true;
                }
                break;
            case 'user':
                if (jrUser_is_logged_in()) {
                    return true;
                }
                break;
            case 'visitor':
                if (!jrUser_is_logged_in()) {
                    return true;
                }
                break;
            default:
                if (is_null($_usr)) {
                    $_usr = $_user;
                }
                if (jrCore_checktype($grp, 'number_nz')) {
                    if (isset($_usr['profile_quota_id']) && $_usr['profile_quota_id'] == $grp) {
                        return true;
                    }
                }
                break;
        }
    }
    return false;
}

/**
 * Save a URL to the user's breadcrumb stack
 *
 * @param string $url URL to save to history
 * @return bool
 */
function jrCore_save_url_history($url = null)
{
    if (!isset($_SESSION['jrcore_url_history'])) {
        $_SESSION['jrcore_url_history'] = array();
    }
    $cur = jrCore_get_current_url();
    if (isset($_SESSION['jrcore_url_history'][$cur])) {
        // Keep it to a good size
        $num = count($_SESSION['jrcore_url_history']);
        if ($num > 30) {
            $_SESSION['jrcore_url_history'] = array_slice($_SESSION['jrcore_url_history'], -30);
        }
        // If we have already been set, we need to cut everything AFTER
        // this entry in our history (if any) so if we come in from a
        // different direction it can be set
        $found = false;
        foreach ($_SESSION['jrcore_url_history'] as $k => $v) {
            if (!$found) {
                if ($k == $cur) {
                    $found = true;
                }
            }
            else {
                unset($_SESSION['jrcore_url_history'][$k]);
            }
        }
        return $_SESSION['jrcore_url_history'][$cur];
    }
    if (is_null($url) || strlen($url) === 0) {
        $url = jrCore_get_local_referrer();
    }
    $_SESSION['jrcore_url_history'][$cur] = $url;
    return $url;
}

/**
 * Get the last URL from the user's breadcrumb stack
 *
 * @param string $url URL to return if no history is set
 * @return string
 */
function jrCore_get_last_history_url($url = 'referrer')
{
    if (!isset($_SESSION['jrcore_url_history'])) {
        if ($url == 'referrer') {
            $url = jrCore_get_local_referrer();
        }
        return $url;
    }
    $cur = jrCore_get_current_url();
    $url = $_SESSION['jrcore_url_history'][$cur];
    return $url;
}

/**
 * Return all registered event listeners for a given event name
 * @param string $module Module that registered event
 * @param string $event Name of the event
 * @return array
 */
function jrCore_get_event_listeners($module, $event)
{
    $_tmp  = jrCore_get_flag('jrcore_event_listeners');
    $event = "{$module}_{$event}";
    if (!$_tmp || (!isset($_tmp[$event]) && !isset($_tmp[$event]) && !isset($_tmp['jrCore_all_events']))) {
        // No one is listening for this event
        return false;
    }
    return $_tmp[$event];
}

/**
 * Trigger a module event
 *
 * The jrCore_trigger_event is used by a module to tell the Core
 * that it is running an "action".  Other modules can listen for
 * this action, and can execute code in response to the action.
 *
 * @param string $module broadcasting module name
 * @param string $event Action that listening modules will receive
 * @param array $_data information passed to the listening event function to be modified
 * @param array $_args additional info pertaining to the event (non modifiable)
 * @param mixed $only_listener Set to a specific module (or array of modules) to broadcast to only those modules
 * @return array
 */
function jrCore_trigger_event($module, $event, $_data = null, $_args = null, $only_listener = false)
{
    global $_mods, $_conf, $_user;

    // Make sure module is valid
    if (!isset($_mods[$module])) {
        return $_data;
    }

    // Our event name
    $ename = "{$module}_{$event}";
    $mname = "{$module}_all_events";

    // See if we have any listeners for this event...
    // NOTE: We do not use jrCore_get_event_listeners() here since we need access to ALL events
    $_tmp = jrCore_get_flag('jrcore_event_listeners');
    if (!$_tmp || (!isset($_tmp[$ename]) && !isset($_tmp[$mname]) && !isset($_tmp['jrCore_all_events']))) {
        // No one is listening for this event
        return $_data;
    }

    // See if we are sending to specific modules only
    if ($only_listener && is_string($only_listener)) {
        $only_listener = array($only_listener);
    }
    if ($only_listener) {
        $only_listener = array_flip($only_listener);
    }

    // Our data MUST come in as an array, or it will cause issues for
    // the listeners - check for it here
    if (is_null($_data) || !isset($_data) || $_data === false) {
        if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
            fdebug("INVALID _data array received in jrCore_trigger_event for event: {$module}/{$event} - {$_data}"); // OK
        }
        $_data = array();
    }

    // Check for recursion - this prevents a module trigger from creating another
    // trigger call to the same module/action resulting in a recursive loop
    $key = md5("{$ename}_" . json_encode($_data));
    $tmp = jrCore_get_flag("jr_module_trigger_event_active_{$key}");
    if ($tmp) {
        // We have recursion...
        jrCore_logger('CRI', 'recursive module trigger detected', array($event, $module, $_data, $_args));
        jrCore_notice('error', 'an internal trigger error has occurred - please try again');
    }
    jrCore_set_flag("jr_module_trigger_event_active_{$key}", 1);

    // Make sure module is part of $_args
    if (is_null($_args) || !isset($_args) || $_args === false || !is_array($_args)) {
        if (!is_array($_args) && strlen($_args) > 0) {
            fdebug("INVALID _args array received in jrCore_trigger_event for event: {$module}/{$event} - {$_args}"); // OK
        }
        $_args = array();
    }
    if (!isset($_args['module'])) {
        $_args['module'] = $module;
    }
    $_args['jrcore_unique_trigger_id'] = $key;

    // Set our active args
    jrCore_set_active_trigger_args($_args);

    // We can register 1 of 3 events:
    // a specific event from a specific module - i.e. 'jrUser','get_info_by_id' - $ename will be 'jrUser_get_info_by_id'
    // all events from a specific module - i.e. 'jrUser','all_events' - $ename will be 'jrUser_all_events'
    // all events for the whole system - i.e. 'all','all_events' - $ename will be 'all_all_events'

    // Start with specific event
    if (isset($_tmp[$ename]) && is_array($_tmp[$ename])) {
        foreach ($_tmp[$ename] as $func) {
            // See if we are only doing specific listeners
            if ($only_listener) {
                $lmod = substr($func, 0, strpos($func, '_'));
                if (!isset($only_listener[$lmod])) {
                    continue;
                }
            }
            if (function_exists($func)) {
                $_temp = $func($_data, $_user, $_conf, jrCore_get_flag("jrcore_active_trigger_args_{$key}"), $event);
                if (!empty($_temp)) {
                    $_data = $_temp;
                }
            }
            else {
                jrCore_logger('CRI', "jrCore_trigger_event: defined event listener function does not exist: {$func}");
                jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
                return $_data;
            }
        }
    }

    // all events for given module
    if (isset($_tmp[$mname]) && is_array($_tmp[$mname])) {
        foreach ($_tmp[$mname] as $func) {
            // See if we are only doing specific listeners
            if ($only_listener) {
                $lmod = substr($func, 0, strpos($func, '_'));
                if (!isset($only_listener[$lmod])) {
                    continue;
                }
            }
            if (function_exists($func)) {
                $_temp = $func($_data, $_user, $_conf, jrCore_get_flag("jrcore_active_trigger_args_{$key}"), $event);
                if (!empty($_temp)) {
                    $_data = $_temp;
                }
            }
            else {
                jrCore_logger('CRI', "jrCore_trigger_event: defined event listener function does not exist: {$func}");
                jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
                return $_data;
            }
        }
    }
    // all events
    if (isset($_tmp['jrCore_all_events']) && is_array($_tmp['jrCore_all_events'])) {
        foreach ($_tmp['jrCore_all_events'] as $func) {
            // See if we are only doing specific listeners
            if ($only_listener) {
                $lmod = substr($func, 0, strpos($func, '_'));
                if (!isset($only_listener[$lmod])) {
                    continue;
                }
            }
            if (function_exists($func)) {
                $_temp = $func($_data, $_user, $_conf, jrCore_get_flag("jrcore_active_trigger_args_{$key}"), $event);
                if (!empty($_temp)) {
                    $_data = $_temp;
                }
            }
            else {
                jrCore_logger('CRI', "jrCore_trigger_event: defined event listener function does not exist: {$func}");
                jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
                return $_data;
            }
        }
    }
    jrCore_delete_flag("jr_module_trigger_event_active_{$key}");
    return $_data;
}

/**
 * Set the "active" trigger args
 * @param $_args array
 * @return bool
 */
function jrCore_set_active_trigger_args($_args)
{
    if (is_array($_args) && isset($_args['jrcore_unique_trigger_id'])) {
        return jrCore_set_flag("jrcore_active_trigger_args_{$_args['jrcore_unique_trigger_id']}", $_args);
    }
    return false;
}

/**
 * Get custom form fields defined in the Form Designer for a view
 *
 * @param string $module Module that has registered a designer form view
 * @param string $view View to get form fields for
 * @return array
 */
function jrCore_get_designer_form_fields($module, $view = null)
{
    $ckey = "jrcore_get_designer_form_fields_{$module}_{$view}";
    $_tmp = jrCore_get_flag($ckey);
    if ($_tmp) {
        if (isset($_tmp) && is_array($_tmp) && count($_tmp) > 0) {
            return $_tmp;
        }
        return false;
    }
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    if (is_null($view) || strlen($view) === 0) {
        $req = "SELECT * FROM {$tbl} WHERE `module` = '{$mod}' ORDER by `order` ASC";
    }
    else {
        $opt = jrCore_db_escape($view);
        $req = "SELECT * FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' ORDER by `order` ASC";
    }
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!isset($_rt) || !is_array($_rt) || count($_rt) === 0) {
        jrCore_set_flag($ckey, 1);
        return false;
    }
    $_out = array();
    foreach ($_rt as $_v) {
        $_out["{$_v['name']}"] = $_v;
    }
    jrCore_set_flag($ckey, $_out);
    return $_out;
}

/**
 * Verify a form field is setup in the Form Designer
 *
 * @param string $module Module that has registered a designer form view
 * @param string $view View to get form fields for
 * @param array $_field Array of field information
 * @return mixed Returns false on error, 1 on update and INSERT_ID on create
 */
function jrCore_verify_designer_form_field($module, $view, $_field)
{
    global $_user;
    if (!isset($_field) || !is_array($_field) || (isset($_field['form_designer']) && $_field['form_designer'] === false)) {
        return false;
    }
    // we MUST get a field name
    if (!isset($_field['name'])) {
        jrCore_logger('CRI', "field received without field name", array('module' => $module, 'view' => $view, '_field' => $_field));
        return false;
    }
    // We don't do hidden fields...
    if (isset($_field['type']) && $_field['type'] == 'hidden') {
        return true;
    }
    // The "type" must be a valid form field
    $_fld = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'form_field');
    foreach ($_tmp as $m => $_v) {
        foreach ($_v as $k => $v) {
            $_fld[$k] = $m;
        }
    }
    if (!isset($_fld["{$_field['type']}"])) {
        // Not a form field
        return true;
    }
    // Cleanup field options...
    if (isset($_field['options']) && is_array($_field['options'])) {
        $_field['options'] = json_encode($_field['options']);
    }

    // There are some fields that we do not override here
    unset($_field['module'], $_field['view'], $_field['created'], $_field['updated'], $_field['user']);
    // Create
    $_cm = jrCore_db_table_columns('jrCore', 'form');
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    $opt = jrCore_db_escape($view);
    $usr = (isset($_user['user_name']) && strlen($_user['user_name']) > 0) ? $_user['user_name'] : (isset($_user['user_email']) ? $_user['user_email'] : 'installer');
    $_tm = jrCore_get_designer_form_fields($module, $view);
    if (!isset($_tm["{$_field['name']}"])) {
        if (!isset($_field['locked'])) {
            $_field['locked'] = '1';
        }
        unset($_field['order']);
        $_cl = array();
        $_vl = array();
        foreach ($_field as $k => $v) {
            if (isset($_cm[$k])) {
                $_cl[] = "`{$k}`";
                $_vl[] = jrCore_db_escape($_field[$k]);
            }
        }
        if (!isset($_cl) || !is_array($_cl) || count($_cl) < 1) {
            return false;
        }
        // On insert we have to go in at the end of the form...
        $ord = 1;
        if (isset($_tm) && is_array($_tm)) {
            $ord = (count($_tm) + 1);
        }
        $req = "INSERT INTO {$tbl} (`module`,`view`,`created`,`updated`,`user`,`order`," . implode(',', $_cl) . ")
                VALUES ('{$mod}','{$opt}',UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'{$usr}','{$ord}','" . implode("','", $_vl) . "')
                ON DUPLICATE KEY UPDATE `updated` = UNIX_TIMESTAMP()";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (isset($cnt) && $cnt === 1) {
            // If our 'jrcore_designer_create_custom_field' flag is set, it is
            // a field that is being created by the site admin.
            $crf = jrCore_get_flag('jrcore_designer_create_custom_field');
            if ($crf) {
                // NOTE: custom user lang keys are greater than 10000
                $tbl = jrCore_db_table_name('jrUser', 'language');
                $req = "SELECT (MAX(lang_key + 0) + 1) AS maxl FROM {$tbl} WHERE lang_module = '{$mod}'";
                $_nk = jrCore_db_query($req, 'SINGLE');
                $num = (isset($_nk['maxl'])) ? (int) $_nk['maxl'] : 1;
                if ($num < 10000) {
                    // This is our first custom entry for this module
                    $num = 10001;
                }
                $_done = array();
                $_todo = array(
                    'label'    => jrCore_db_escape("{$_field['name']} label *change this*"),
                    'sublabel' => '',
                    'help'     => jrCore_db_escape("{$_field['name']} help *change this*")
                );
                // Get support languages...
                $req = "SELECT lang_code, lang_ltr FROM {$tbl} GROUP BY lang_code";
                $_lc = jrCore_db_query($req, 'lang_code', false, 'lang_ltr');
                if ($_lc && is_array($_lc)) {
                    $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES\n";
                    foreach ($_lc as $code => $ltr) {
                        foreach ($_todo as $str => $val) {
                            if (!isset($_done[$str])) {
                                $_done[$str] = $num++;
                            }
                            $req .= "('{$mod}','{$code}','utf-8','{$ltr}','" . $_done[$str] . "','{$val}','{$val}'),";
                        }
                    }
                }
                $req = substr($req, 0, strlen($req) - 1);
                $cnt = jrCore_db_query($req, 'COUNT');
                if (isset($cnt) && $cnt > 0) {
                    // Go back in and update our new form entry with the proper lang entries
                    $tbl = jrCore_db_table_name('jrCore', 'form');
                    $req = "UPDATE {$tbl} SET `label` = '" . intval($_done['label']) . "', `sublabel` = '" . intval($_done['sublabel']) . "', `help` = '" . intval($_done['help']) . "'
                             WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '" . jrCore_db_escape($_field['name']) . "'";
                    $cnt = jrCore_db_query($req, 'COUNT');
                }
            }
        }
        if (isset($cnt) && $cnt > 0) {
            // Reset designer form field key so it contains the new field
            $ckey = "jrcore_get_designer_form_fields_{$module}_{$view}";
            jrCore_delete_flag($ckey);
            return true;
        }
    }
    // Update
    else {
        // We can't change 'locked' status on update
        unset($_field['locked']);
        $req = "UPDATE {$tbl} SET `updated` = UNIX_TIMESTAMP(), `user` = '{$usr}', ";
        foreach ($_field as $k => $v) {
            if (isset($_cm[$k])) {
                switch ($k) {
                    case 'required':
                    case 'active':
                        if (isset($v) && jrCore_checktype($v, 'number_nn')) {
                            $req .= "`{$k}` = " . intval($v) . ',';
                        }
                        break;
                    case 'min':
                    case 'max':
                        if (isset($v) && jrCore_checktype($v, 'number_nz')) {
                            $req .= "`{$k}` = " . intval($v) . ',';
                        }
                        else {
                            $req .= "`{$k}` = 0,";
                        }
                        break;
                    default:
                        $req .= "`{$k}` = '" . jrCore_db_escape($_field[$k]) . "',";
                        break;
                }
            }
        }
        if (!isset($req) || !strpos($req, '=')) {
            return false;
        }
        $req = substr($req, 0, strlen($req) - 1) . " WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '" . jrCore_db_escape($_field['name']) . "' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (isset($cnt) && $cnt === 1) {
            return true;
        }
    }
    return false;
}

/**
 * Set the order of fields in a Designer Form
 * @param string $module Module that has registered a designer form view
 * @param string $view View to get form fields for
 * @param string $field Field Name to set specific order for
 * @param int $order Order Value to Set for $field
 * @return bool
 */
function jrCore_set_form_designer_field_order($module, $view, $field = null, $order = 1)
{
    $_rt = jrCore_get_designer_form_fields($module, $view);
    if (!isset($_rt) || !is_array($_rt) || count($_rt) === 0) {
        // NO designer fields
        return true;
    }
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    $opt = jrCore_db_escape($view);
    $ord = 1;
    if (isset($field) && strlen($field) > 0) {
        $fld = jrCore_db_escape($field);
        $req = "UPDATE {$tbl} SET `order` = '" . intval($order) . "' WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '{$fld}' LIMIT 1";
        jrCore_db_query($req);
    }
    foreach ($_rt as $_field) {
        if (isset($_field['name']) && $_field['name'] != $field) {
            if ($ord == $order) {
                $ord++;
            }
            $fld = jrCore_db_escape($_field['name']);
            $req = "UPDATE {$tbl} SET `order` = '{$ord}' WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '{$fld}' LIMIT 1";
            jrCore_db_query($req);
            $ord++;
        }
    }
    return true;
}

/**
 * Delete an existing Designer Form field from the form table
 * @param string $module Module that has registered a designer form view
 * @param string $view View field belongs to
 * @param string $name Name of field to delete
 * @return bool
 */
function jrCore_delete_designer_form_field($module, $view, $name)
{
    $_rt = jrCore_get_designer_form_fields($module, $view);
    if (!isset($_rt) || !is_array($_rt) || count($_rt) === 0) {
        // NO designer fields
        return true;
    }
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $mod = jrCore_db_escape($module);
    $opt = jrCore_db_escape($view);
    $fld = jrCore_db_escape($name);
    $req = "DELETE FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' AND `name` = '{$fld}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Check if system is in maintenance mode
 *
 * The jrCore_is_maintenance_mode function will redirect a non-logged in,
 * non-master user to the maintenance page.  Allows log ins from masters
 *
 * @param array $_conf Global Configuration array
 * @param array $_post jrCore_parse_url return
 * @return bool
 */
function jrCore_is_maintenance_mode($_conf, $_post)
{
    if (isset($_conf['jrCore_maintenance_mode']) && $_conf['jrCore_maintenance_mode'] == 'on') {
        if (!jrUser_is_admin()) {
            // We're in maintenance mode - check for our modes.  We are going to allow "login" and "login_save"
            // from the user module so system admins can log into the site and use it
            if (!isset($_post['option'])) {
                jrUser_session_destroy();
                return true;
            }
            switch ($_post['option']) {
                case 'login':
                case 'login_save':
                case 'logout':
                case 'form_validate':
                    return false;
                    break;
                default:
                    // We need to check if this is a "no session" login - if so,
                    // we are going to return false
                    $_tmp = jrCore_get_registered_module_features('jrUser', 'skip_session');
                    if (isset($_tmp) && is_array($_tmp)) {
                        foreach ($_tmp as $mod => $_opts) {
                            if (isset($_opts["{$_post['option']}"]) && ($mod == $_post['module'] || $_opts["{$_post['option']}"] == 'magic_view')) {
                                return false;
                            }
                        }
                    }
                    break;
            }
            jrUser_session_destroy();
            return true;
        }
    }
    return false;
}

/**
 * Get registered system plugins for given type
 *
 * @param string $type Type of Plugin to get
 * @return array
 */
function jrCore_get_system_plugins($type)
{
    $_tmp = jrCore_get_flag('jr_register_system_plugin');
    if (!isset($_tmp[$type]) || !is_array($_tmp[$type]) || count($_tmp[$type]) === 0) {
        return false;
    }
    $_out = array();
    foreach ($_tmp[$type] as $module => $_mod) {
        foreach ($_mod as $plugin => $desc) {
            $_out["{$module}_{$plugin}"] = $desc;
        }
    }
    return $_out;
}

/**
 * Register a module function for an event trigger
 *
 * @param string $module Module registering for event trigger
 * @param string $event Event name registering for
 * @param string $function Function to execute when event is triggered
 * @return bool
 */
function jrCore_register_event_listener($module, $event, $function)
{
    // We can register 1 of 3 events:
    // a specific event from a specific module - i.e. 'jrUser','get_info_by_id'
    // all events from a specific module - i.e. 'jrUser','all_events'
    // all events for the whole system - i.e. 'jrCore','all_events'
    if (!isset($GLOBALS['__JR_FLAGS']['jrcore_event_listeners'])) {
        $GLOBALS['__JR_FLAGS']['jrcore_event_listeners'] = array();
    }
    $ename = "{$module}_{$event}";
    if (!isset($GLOBALS['__JR_FLAGS']['jrcore_event_listeners'][$ename])) {
        $GLOBALS['__JR_FLAGS']['jrcore_event_listeners'][$ename] = array();
    }
    $GLOBALS['__JR_FLAGS']['jrcore_event_listeners'][$ename][] = $function;
    return true;
}

/**
 * Register an event trigger that modules can listen for
 *
 * @param string $module Module registering the new event trigger
 * @param string $event Event name being registered
 * @param string $description Descriptive text used when jrDeveloper module is installed outlining what the event trigger is for
 * @return bool
 */
function jrCore_register_event_trigger($module, $event, $description)
{
    // We can register 1 of 3 events:
    // a specific event from a specific module - i.e. 'jrUser','get_info_by_id'
    // all events from a specific module - i.e. 'jrUser','all_events'
    // all events for the whole system - i.e. 'jrCore','all_events'
    $_tmp = jrCore_get_flag('jrcore_event_triggers');
    if (!$_tmp) {
        $_tmp = array();
    }
    $ename = "{$module}_{$event}";
    if (!isset($_tmp[$ename])) {
        $_tmp[$ename] = array();
    }
    $_tmp[$ename] = $description;
    jrCore_set_flag('jrcore_event_triggers', $_tmp);
    return true;
}

/**
 * Register a Core System architecture plugin
 * @param string $module Module that provides the plugin capability
 * @param string $type one of "email", "cache" or "media"
 * @param string $plugin Plugin Name
 * @param string $description Plugin Description
 * @return bool
 */
function jrCore_register_system_plugin($module, $type, $plugin, $description)
{
    // Make sure we get a valid plugin
    switch ($type) {
        case 'email':
        case 'cache':
        case 'media':
            break;
        default:
            jrCore_logger('CRI', "jrCore_register_system_plugin: invalid plugin type: {$type}");
            return false;
            break;
    }
    $_tmp = jrCore_get_flag('jr_register_system_plugin');
    if (!$_tmp) {
        $_tmp = array();
    }
    if (!isset($_tmp[$type])) {
        $_tmp[$type] = array();
    }
    if (!isset($_tmp[$type][$module])) {
        $_tmp[$type][$module] = array();
    }
    $_tmp[$type][$module][$plugin] = $description;
    jrCore_set_flag('jr_register_system_plugin', $_tmp);
    return true;
}

/**
 * Create a new entry in the settings table
 *
 * @param string $module Module registering setting for
 * @param array $_field Array of setting information
 * @return bool
 */
function jrCore_register_setting($module, $_field)
{
    if (!isset($_field['type'])) {
        jrCore_notice('CRI', "jrCore_register_setting() required field: type missing for setting");
    }
    // example $_field:
    // $_tmp = array(
    //     'name'     => 'from_email',
    //     'label'    => 'from email address',
    //     'default'  => '',
    //     'type'     => 'text',
    //     'validate' => 'email',
    //     'help'     => 'When the system sends an automated / system message, what email address should the email be sent from?',
    //     'section'  => 'general email settings'
    // );
    // Optional:
    //     'min'      => (int) (minimum allow numerical value - validated)
    //     'max'      => (int) (maximum allow numerical value - validated)
    //     'options'  => array() (array of key => value pairs for fields with "select" type

    // See if we have already been called for this module/key in this process
    $key = jrCore_db_escape($_field['name']);
    $_tm = jrCore_get_flag('jrcore_register_setting');
    if (isset($_tm) && is_array($_tm) && isset($_tm["{$module}_{$key}"])) {
        return true;
    }
    if (!isset($_tm) || !is_array($_tm)) {
        $_tm = array();
    }
    $_tm["{$module}_{$key}"] = 1;
    jrCore_set_flag('jrcore_register_setting', $_tm);

    // Some items are required for form fields
    $_ri = array_flip(array('name', 'default', 'validate', 'label', 'help'));
    switch ($_field['type']) {
        // we already internally validate hidden and select elements
        case 'hidden':
            unset($_ri['validate'], $_ri['label'], $_ri['help']);
            break;
        case 'radio':
        case 'select':
        case 'select_multiple':
        case 'optionlist':
            unset($_ri['validate']);
            // Handle field options for select statements if set
            if (isset($_field['options']) && is_array($_field['options'])) {
                $_field['options'] = json_encode($_field['options']);
            }
            elseif (isset($_field['options']) && !function_exists($_field['options'])) {
                // These select options are generated at display time by a function
                jrCore_notice('CRI', "jrCore_register_setting() option function defined for field: {$_field['name']} does not exist");
            }
            break;
    }
    foreach ($_ri as $k => $v) {
        if (!isset($_field[$k])) {
            jrCore_notice('CRI', "jrCore_register_setting() required field: {$k} missing for setting: {$_field['name']}");
        }
    }
    // Make sure setting is properly updated
    return jrCore_update_setting($module, $_field);
}

/**
 * Verify a Global Setting is configured correctly in the settings table
 *
 * @param string $module Module to create global setting for
 * @param array $_field Array of setting information
 * @return bool
 */
function jrCore_update_setting($module, $_field)
{
    global $_conf, $_user;
    $usr = (isset($_user['user_name']) && strlen($_user['user_name']) > 0) ? $_user['user_name'] : (isset($_user['user_email']) ? $_user['user_email'] : 'installer');
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT `created` FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($_field['name']) . "'";
    $_ex = jrCore_db_query($req, 'SINGLE');
    $_rt = jrCore_db_table_columns('jrCore', 'setting');

    // Create
    if (!isset($_ex) || !is_array($_ex)) {
        $_cl = array();
        $_vl = array();

        // When creating a NEW entry in settings, our value is set to the default
        $_field['value'] = $_field['default'];

        foreach ($_rt as $k => $v) {
            if (isset($_field[$k])) {
                $_cl[] = "`{$k}`";
                $_vl[] = jrCore_db_escape($_field[$k]);
            }
        }
        if (!isset($_cl) || !is_array($_cl) || count($_cl) < 1) {
            return false;
        }
        $req = "INSERT INTO {$tbl} (`module`,`created`,`updated`,`user`," . implode(',', $_cl) . ") VALUES ('" . jrCore_db_escape($module) . "',UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'{$usr}','" . implode("','", $_vl) . "')";
    }
    // Update
    else {
        $req = "UPDATE {$tbl} SET `updated` = UNIX_TIMESTAMP(), `user` = '" . jrCore_db_escape($usr) . "', ";
        foreach ($_rt as $k => $v) {
            if (isset($_field[$k])) {
                $req .= "`{$k}` = '" . jrCore_db_escape($_field[$k]) . "',";
            }
            else {
                // Some fields can be unset
                switch ($k) {
                    case 'section':
                    case 'sublabel':
                        $req .= "`{$k}` = '',";
                        break;
                }
            }
        }
        if (!isset($req) || !strpos($req, '=')) {
            return false;
        }
        $req = substr($req, 0, strlen($req) - 1) . " WHERE module = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($_field['name']) . "' LIMIT 1";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        // Make sure this is updated in process
        $_conf["{$module}_{$_field['name']}"] = (isset($_field['value'])) ? $_field['value'] : $_field['default'];
        if (isset($_cl)) {
            jrCore_logger('INF', "created global setting for {$module} module: {$_field['name']}");
        }
        jrCore_delete_config_cache();
        return true;
    }
    return false;
}

/**
 * Delete an existing global setting from the settings table
 * @param string $module Module Name
 * @param string $name Setting Name
 * @return bool
 */
function jrCore_delete_setting($module, $name)
{
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "DELETE FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($name) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_delete_config_cache();
        jrCore_logger('INF', "global setting {$module}_{$name} was successfully deleted");
    }
    return true;
}

/**
 * Update a Global Config setting value
 *
 * @param string $module Module that owns the setting
 * @param string $name Name of the setting
 * @param string $value New Value for setting
 * @return bool
 */
function jrCore_set_setting_value($module, $name, $value)
{
    global $_conf, $_user;
    $usr = (isset($_user['user_name']) && strlen($_user['user_name']) > 0) ? $_user['user_name'] : (isset($_user['user_email']) ? $_user['user_email'] : 'installer');
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "UPDATE {$tbl} SET
              `updated` = UNIX_TIMESTAMP(),
              `value`   = '" . jrCore_db_escape($value) . "',
              `user`    = '{$usr}'
             WHERE `module` = '" . jrCore_db_escape($module) . "' AND `name` = '" . jrCore_db_escape($name) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        $_conf["{$module}_{$name}"] = $value;
        return true;
    }
    return false;
}

//--------------------------------------
// EMAIL wrapper functions
//--------------------------------------

/**
 * Parse subject and message email templates
 * @param string $module Module name
 * @param string $template Base email template name to parse
 * @param array $_replace Replacement Key => Value array
 * @return mixed
 */
function jrCore_parse_email_templates($module, $template, $_replace = null)
{
    $sub_file = APP_DIR . "/modules/{$module}/templates/email_{$template}_subject.tpl";
    if (!is_file($sub_file)) {
        return false;
    }
    $msg_file = APP_DIR . "/modules/{$module}/templates/email_{$template}_message.tpl";
    if (!is_file($msg_file)) {
        return false;
    }
    $_out = array();
    ob_start();
    $_out[] = trim(jrCore_parse_template("email_{$template}_subject.tpl", $_replace, $module));
    $_out[] = trim(jrCore_parse_template("email_{$template}_message.tpl", $_replace, $module));
    ob_end_clean();
    return $_out;
}

/**
 * @ignore
 * jrCore_get_email_system_plugins
 * @return array
 */
function jrCore_get_email_system_plugins()
{
    return jrCore_get_system_plugins('email');
}

/**
 * @ignore
 * jrCore_get_active_email_system
 * @return string
 */
function jrCore_get_active_email_system()
{
    // Find our active email system plugin
    global $_conf;
    if (isset($_conf['jrMailer_active_email_system']{1})) {
        // Make sure function exists...
        $func = "_{$_conf['jrMailer_active_email_system']}_send_email";
        if (function_exists($func)) {
            return $_conf['jrMailer_active_email_system'];
        }
    }
    return 'jrCore_debug';
}

/**
 * Send an email to single or multiple recipients
 * @param mixed $_add Email addresses (single address as a string, multiple addresses as an array) to send to
 * @param string $subject Message Subject
 * @param string $message Message Body
 * @param array $_options Email options
 * @return int
 */
function jrCore_send_email($_add, $subject, $message, $_options = null)
{
    global $_conf;
    // message and subject are required
    if (!isset($subject) || strlen($subject) === 0) {
        jrCore_logger('CRI', "jrCore_send_email: empty subject received - verify usage", func_get_args());
        return false;
    }
    if (!isset($message) || strlen($message) === 0) {
        jrCore_logger('CRI', "jrCore_send_email: empty message received - verify usage", func_get_args());
        return false;
    }


    // our addresses must be an incoming array
    if (!is_array($_add)) {
        $_add = array($_add);
    }
    // Validate email addresses
    foreach ($_add as $k => $address) {
        if (!jrCore_checktype($address, 'email')) {
            unset($_add[$k]);
        }
    }
    // Make sure we still have at least 1 good email
    if (count($_add) === 0) {
        return false;
    }

    // Make sure we have our mail options
    if (is_null($_options) || !isset($_options) || !is_array($_options)) {
        $_options = array();
    }

    // Start options we will pass in
    $_options['subject'] = $subject;
    $_options['message'] = $message;

    // figure our from email address
    if (!isset($_options['from']) || !jrCore_checktype($_options['from'], 'email')) {
        $_options['from'] = (isset($_conf['jrMailer_from_email'])) ? $_conf['jrMailer_from_email'] : $_SERVER['SERVER_ADMIN'];
    }

    // If Email throttling is on we have to create a queue entry
    // for EACH email address as we have to be able to count them
    if (isset($_conf['jrMailer_throttle']) && jrCore_checktype($_conf['jrMailer_throttle'], 'number_nz')) {

        foreach ($_add as $address) {
            $_queue = array(
                'address'  => array($address),
                'subject'  => $subject,
                'message'  => $message,
                '_options' => $_options
            );
            jrCore_queue_create('jrCore', 'send_email', $_queue);
        }

    }
    else {

        // Add to send_email queue - note that without throttling
        // we can send to all email addresses in the same queue
        $_queue = array(
            'address'  => $_add,
            'subject'  => $subject,
            'message'  => $message,
            '_options' => $_options
        );
        jrCore_queue_create('jrCore', 'send_email', $_queue);

    }
    return count($_add);
}

/**
 * Worker that processes the Core send_email Queue
 * @param $_queue array Queue entry
 * @return bool
 */
function jrCore_send_email_queue_worker($_queue)
{
    global $_conf, $_user;

    if (!is_array($_queue)) {
        return true; // bad queue entry
    }

    // See if are throttling...
    if (isset($_conf['jrMailer_throttle']) && jrCore_checktype($_conf['jrMailer_throttle'], 'number_nz')) {

        // See if we have already tried on this run...
        $min = strftime('%y%m%d%H%M');
        if ($tmp = jrCore_get_flag('jrcore_send_email_throttle_min')) {
            if ($tmp == $min) {
                // We cannot send now - return "60" tells the core to sleep the entry for 60 more seconds...
                return 60;
            }
            jrCore_delete_flag('jrcore_send_email_throttle_min');
        }

        // We're throttling, and need to make sure we only send X number per minute
        $max = (int) $_conf['jrMailer_throttle'];
        $tbl = jrCore_db_table_name('jrMailer', 'throttle');
        $req = "INSERT INTO {$tbl} (t_min, t_cnt) VALUES ('{$min}', 1) ON DUPLICATE KEY UPDATE t_cnt = IF(t_cnt < {$max}, (t_cnt + 1), t_cnt)";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 2) {
            // If we get back "1" it means it is a NEW entry OR we have it our max
            $req = "SELECT t_cnt FROM {$tbl} WHERE t_min = '{$min}'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if ($_rt && isset($_rt['t_cnt']) && $_rt['t_cnt'] >= $max) {
                // We cannot send now - return "60" tells the core to sleep the entry for 60 more seconds...
                jrCore_set_flag('jrcore_send_email_throttle_min', $min);
                return 60;
            }
        }
    }

    // Get our active mailer sub system and send email
    $smtp = jrCore_get_active_email_system();
    $func = "_{$smtp}_send_email";
    if (function_exists($func)) {
        $temp = $func($_queue['address'], $_user, $_conf, $_queue['_options']);
        if ($temp !== false) {
            return true;
        }
        return false;
    }
    jrCore_logger('CRI', "active email system function: {$func} is not defined");
    return false;
}

/**
 * @ignore
 * Core provided Send Email function that logs all sent email to the debug log
 * @param $_email_to mixed address or array of addresses to
 * @param $_user array Current User info
 * @param $_conf array Global Config
 * @param $_email_info array Email options (subject, message, etc.)
 * @return int
 */
function _jrCore_debug_send_email($_email_to, $_user, $_conf, $_email_info)
{
    $_out = array(
        '_email_to'   => $_email_to,
        '_email_info' => $_email_info
    );
    fdebug($_out); // OK
    return count($_email_to);
}

/**
 * Test if a given value for a type is a banned item
 * @DEPRECATED - use jrBanned_is_banned via jrCore_run_module_function()
 * @param string $type Type of Banned Item
 * @param string $value Value to check
 * @return bool
 */
function jrCore_is_banned($type, $value = null)
{
    if (jrCore_module_is_active('jrBanned')) {
        return jrBanned_is_banned($type, $value);
    }
    return false;
}

//---------------------------------------------------------
// Counter functions
//---------------------------------------------------------

/**
 * Get count value for a given module/id/name
 * @param string $module Module to check unique hit for
 * @param string $name Type of count to store
 * @param string $uid Unique Identifier that identifies this entry in count table
 * @param string $pid Profile_id to limit results that belong to a single profile
 * @return int
 */
function jrCore_get_count($module, $name, $uid = null, $pid = null)
{
    // counts for a specific item_id
    if (isset($uid) && is_numeric($uid) && isset($name{0}) && $pid == null) {
        if ($cnt = jrCore_db_get_item_key($module, $uid, "{$name}_count")) {
            return intval($cnt);
        }
    }
    // Get ALL counts for a profile_id
    elseif (isset($pid) && jrCore_checktype($pid, 'number_nz')) {
        $key = "{$name}_count";
        $_sp = array(
            'search'        => array(
                "_profile_id = {$pid}"
            ),
            'return_keys'   => array($key),
            'skip_triggers' => true,
            'privacy_check' => false,
            'limit'         => 1000000
        );
        $_rt = jrCore_db_search_items($module, $_sp);
        if (isset($_rt) && is_array($_rt['_items'])) {
            $tcount = 0;
            foreach ($_rt['_items'] as $_item) {
                if (isset($_item[$key]) && ($_item[$key] > 0)) {
                    $tcount += (int) $_item[$key];
                }
            }
            return intval($tcount);
        }
    }
    else {
        return intval(jrCore_db_run_key_function($module, $name, '*', 'sum'));
    }
    return 0;
}

/**
 * Count a hit for a module item with user ip tracking
 * @param string $module Module to check unique hit for
 * @param string $iid Unique Item ID
 * @param string $name Name of DS key for counter
 * @param int $amount Amount to increment counter by
 * @param bool $unique Check IP Address if true
 * @return bool
 */
function jrCore_counter($module, $iid, $name, $amount = 1, $unique = true)
{
    // Our steps:
    // - check IP status of hitting user
    // - if user passes IP check, increment counter
    $iid = intval($iid);
    $nam = (strpos($name, '_count')) ? $name : "{$name}_count";
    if (!$unique || jrCore_counter_is_unique_viewer($module, $iid, $nam)) {
        return jrCore_db_increment_key($module, $iid, $nam, intval($amount));
    }
    return true;
}

/**
 * Check that viewer is a making a unique count request
 * @param string $module Module to check unique hit for
 * @param string $iid Unique Identifier that identifies this entry in count table
 * @param string $name Type of count to store
 * @param int $timeframe Timeframe (in seconds) that if elapsed, will count as new "hit" - max 86400
 * @return bool
 */
function jrCore_counter_is_unique_viewer($module, $iid, $name, $timeframe = 86400)
{
    global $_user;
    // See if have a hit in our timeframe...
    $uip = jrCore_db_escape(jrCore_get_ip());
    $iid = (int) $iid;
    $uid = (isset($_user['_user_id'])) ? (int) $_user['_user_id'] : 0;
    $nam = (strpos($name, '_count')) ? $name : "{$name}_count";
    $typ = jrCore_db_escape($nam);
    $tbl = jrCore_db_table_name('jrCore', 'count_ip');
    $req = "SELECT count_time FROM {$tbl} WHERE count_ip = '{$uip}' AND count_user_id = '{$uid}' AND count_time < (UNIX_TIMESTAMP() - {$timeframe}) AND count_uid = '{$iid}' AND count_name = '{$typ}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt) && is_array($_rt)) {
        // user has already hit us...
        return false;
    }
    // Insert
    $req = "INSERT INTO {$tbl} (count_ip,count_uid,count_user_id,count_name,count_time)
            VALUES ('{$uip}','{$iid}','{$uid}','{$typ}',UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE count_time = UNIX_TIMESTAMP()";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        return true;
    }
    return false;
}

//---------------------------------------------------------
// MOBILE functions
//---------------------------------------------------------

/**
 * Return true if viewing browser is a mobile device
 * @return bool
 */
function jrCore_is_mobile_device()
{
    $tmp = jrCore_get_flag('jrcore_is_mobile_device');
    if (!$tmp) {
        if (!class_exists('Mobile_Detect')) {
            require_once APP_DIR . '/modules/jrCore/contrib/mobile_detect/Mobile_Detect.php';
        }
        $d = new Mobile_Detect();
        if ($d->isMobile() && !$d->isTablet()) {
            $tmp = 'yes';
            $ret = true;
        }
        else {
            $tmp = 'no';
            $ret = false;
        }
        jrCore_set_flag('jrcore_is_mobile_device', $tmp);
        return $ret;
    }
    return ($tmp == 'yes') ? true : false;
}

/**
 * Return true if viewing browser is a tablet device
 * @return bool
 */
function jrCore_is_tablet_device()
{
    $tmp = jrCore_get_flag('jrcore_is_tablet_device');
    if (!$tmp) {
        if (!class_exists('Mobile_Detect')) {
            require_once APP_DIR . '/modules/jrCore/contrib/mobile_detect/Mobile_Detect.php';
        }
        $d = new Mobile_Detect();
        if ($d->isTablet() || $d->isMobile()) {
            $tmp = 'yes';
            $ret = true;
        }
        else {
            $tmp = 'no';
            $ret = false;
        }
        jrCore_set_flag('jrcore_is_tablet_device', $tmp);
        return $ret;
    }
    return ($tmp == 'yes') ? true : false;
}
