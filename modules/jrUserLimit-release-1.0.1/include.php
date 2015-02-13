<?php
/**
 * Jamroom 5 User Daily Limits module
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
 * jrUserLimit_meta
 */
function jrUserLimit_meta()
{
    $_tmp = array(
        'name'        => 'User Daily Limits',
        'url'         => 'userlimit',
        'version'     => '1.0.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Set Limits on the number of specified actions a User can do in a day',
        'license'     => 'mpl',
        'category'    => 'users'
    );
    return $_tmp;
}

/**
 * jrUserLimit_init
 */
function jrUserLimit_init()
{
    // Tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUserLimit', 'browse', array('Quota Limits Config', 'Create, Update, and Delete User Quota Limits'));

    jrCore_register_event_listener('jrCore', 'stream_file', 'jrUserLimit_apply_limits_listener');
    jrCore_register_event_listener('jrCore', 'download_file', 'jrUserLimit_apply_limits_listener');
    jrCore_register_event_listener('jrCore', 'stream_url_error', 'jrUserLimit_stream_url_error_listener');

    // keep counts table clean
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrUserLimit_daily_maintenance_listener');

    // Config tab
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrUserLimit', 'browse', 'Quota Limits Config');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrUserLimit', 'browse');

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * See if a URL error was caused by us
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUserLimit_stream_url_error_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!jrUser_is_logged_in() && strpos(' ' . $_conf['jrUserLimit_require_login'], 'stream_file')) {
        $_ln = jrUser_load_lang_strings();
        $_data['error'] = $_ln['jrUserLimit'][3];
    }
    if (jrUser_is_logged_in()) {
        $evt = "event-{$_args['module']}-stream_file";
        if (isset($_user["quota_jrUserLimit_{$evt}"]) && jrCore_checktype($_user["quota_jrUserLimit_{$evt}"], 'number_nz')) {
            // Enforce quota
            $max = (int) $_user["quota_jrUserLimit_{$evt}"];
            $mod = jrCore_db_escape($_args['module']);
            $dat = strftime('%Y%m%d');
            $tbl = jrCore_db_table_name('jrUserLimit', 'counts');
            $req = "SELECT c_count FROM {$tbl} WHERE c_user_id = '{$_user['_user_id']}' AND c_module = '{$mod}' AND c_event = 'stream_file' AND c_date = '{$dat}' LIMIT 1";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if ($_rt && is_array($_rt)) {
                if ($_rt['c_count'] >= $max) {
                    // We're over
                    $_ln = jrUser_load_lang_strings();
                    $_data['error'] = $_ln['jrUserLimit'][1];
                }
            }
        }
    }
    return $_data;
}

/**
 * Apply action limits to a user
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUserLimit_apply_limits_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_get_flag('jruserlimit_check')) {
        // already checked in this process
        return $_data;
    }
    // Try to prevent counting double scans
    if (isset($_SERVER['HTTP_RANGE']) && strpos($_SERVER['HTTP_RANGE'], 'bytes=0') !== 0) {
        return $_data;
    }
    jrCore_set_flag('jruserlimit_check', 1);
    if (!jrUser_is_logged_in() && strpos(' ' . $_conf['jrUserLimit_require_login'], $event)) {
        $_ln = jrUser_load_lang_strings();
        $url = jrCore_get_module_url('jrUser');
        jrCore_set_form_notice('error', $_ln['jrUserLimit'][3]);
        jrCore_location("{$_conf['jrCore_base_url']}/{$url}/login");
    }
    $evt = "event-{$_args['module']}-{$event}";
    if (jrUser_is_logged_in() && isset($_user["quota_jrUserLimit_{$evt}"]) && jrCore_checktype($_user["quota_jrUserLimit_{$evt}"], 'number_nz')) {
        // Enforce quota
        $max = (int) $_user["quota_jrUserLimit_{$evt}"];
        $mod = jrCore_db_escape($_args['module']);
        $evt = jrCore_db_escape($event);
        $dat = strftime('%Y%m%d');
        $tim = (time() - 5); // handle browser scans
        $tbl = jrCore_db_table_name('jrUserLimit', 'counts');
        $req = "UPDATE {$tbl} SET c_count = (c_count + 1) WHERE c_user_id = '{$_user['_user_id']}' AND c_module = '{$mod}' AND c_event = '{$evt}' AND c_date = '{$dat}' AND c_time < {$tim} AND c_count < {$max} LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt === 0) {
            // We are either NEW or exceeded MAX
            $req = "SELECT c_count FROM {$tbl} WHERE c_user_id = '{$_user['_user_id']}' AND c_module = '{$mod}' AND c_event = '{$evt}' AND c_date = '{$dat}' LIMIT 1";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if ($_rt && is_array($_rt)) {
                if ($_rt['c_count'] >= $max) {
                    header('HTTP/1.0 403 Forbidden');
                    header('Connection: close');
                    $_ln = jrUser_load_lang_strings();
                    jrCore_notice_page('error', $_ln['jrUserLimit'][2]);
                }
                // browser scan - we're OK
            }
            else {
                // NEW for the day
                $req = "INSERT INTO {$tbl} (c_user_id, c_module, c_event, c_date, c_time, c_count) VALUES ('{$_user['_user_id']}', '{$mod}', '{$evt}', '{$dat}', {$tim}, 1) ON DUPLICATE KEY UPDATE c_count = 1";
                jrCore_db_query($req);
            }
        }

    }
    return $_data;
}

/**
 * Keep counts table cleaned up
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrUserLimit_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    $dat = strftime('%Y%m%d');
    $tbl = jrCore_db_table_name('jrUserLimit', 'counts');
    $req = "DELETE FROM {$tbl} WHERE c_date < {$dat}";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        jrCore_logger('INF', "successfully deleted {$cnt} expired user limit keys");
    }
    return $_data;
}

//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Actions supported by module
 * @return array
 */
function jrUserLimit_get_action_options()
{
    return array(
        'stream_file'   => 'Stream File',
        'download_file' => 'Download File'
    );
}
