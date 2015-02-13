<?php
/**
 * Jamroom 5 User Accounts module
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrUser_meta
 */
function jrUser_meta()
{
    $_tmp = array(
        'name'        => 'User Accounts',
        'url'         => 'user',
        'version'     => '1.5.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Core support for User Accounts, Sessions and Languages',
        'category'    => 'users',
        'license'     => 'mpl',
        'priority'    => 1, // HIGHEST load priority
        'locked'      => true,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * jrUser_init
 */
function jrUser_init()
{
    // register our triggers
    jrCore_register_event_trigger('jrUser', 'signup_created', 'Fired when a user successfully signs up for a new account');
    jrCore_register_event_trigger('jrUser', 'signup_activated', 'Fired when a user successfully validates their account');
    jrCore_register_event_trigger('jrUser', 'login_success', 'Fired when a user successfully logs in');
    jrCore_register_event_trigger('jrUser', 'logout', 'Fired when a user logs out (before session destroyed)');
    jrCore_register_event_trigger('jrUser', 'session_init', 'Fired when session handler is initialized');
    jrCore_register_event_trigger('jrUser', 'session_started', 'Fired when a session is created');
    jrCore_register_event_trigger('jrUser', 'user_updated', 'Fired when a User Account is updated');
    jrCore_register_event_trigger('jrUser', 'account_tabs', 'Fired when the Tabs are created in the User Account');
    jrCore_register_event_trigger('jrUser', 'delete_user', 'Fired when a User Account is deleted');
    jrCore_register_event_trigger('jrUser', 'notify_user', 'Fired when a User is sent a notification');

    // If the tracer module is installed, we have a few events for it
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrUser', 'signup_activated', 'A new user activates their account');
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrUser', 'login_success', 'User logs into the system');

    // core event listeners
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrUser_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrUser_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'form_field_create', 'jrUser_form_field_create_listener');
    jrCore_register_event_listener('jrCore', 'process_exit', 'jrUser_process_exit_listener');

    // Admin notifications on new signup
    jrCore_register_event_listener('jrUser', 'signup_activated', 'jrUser_signup_activated_listener');

    // Listen for force User SSL
    jrCore_register_event_listener('jrCore', 'view_results', 'jrUser_view_results_listener');

    // Once a day we cleanup old remember me cookies
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrUser_daily_maintenance_listener');

    // Listen for site pages and check site against site privacy setting
    jrCore_register_event_listener('jrUser', 'session_started', 'jrUser_session_started_listener');

    // User tool views
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'browser', array('Browse User Accounts', 'Browse User Accounts in your system'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'online', array('Whos Online', 'View active User Sessions'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'create', array('Create a New User', 'Create a new User Account'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'create_language', array('Clone a Language', 'Create or Update a Language by cloning an existing Language'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'delete_language', array('Delete a Language', 'Delete a language that is no longer used'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrUser', 'reset_language', array('Reset Language Strings', 'Reset language strings for a module or skin'));

    // We provide our own data browser
    jrCore_register_module_feature('jrCore', 'data_browser', 'jrUser', 'jrUser_data_browser');

    // Register our account tabs..
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrUser', 'account', 42);
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrUser', 'notifications', 64);

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrUser', 'account');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrUser', 'signup');

    // User Account
    $_tmp = array(
        'group' => 'user',
        'label' => 'account settings',
        'url'   => 'account',
        'order' => 1
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrUser', 'account', $_tmp);

    // User Logout
    $_tmp = array(
        'group' => 'user',
        'label' => 'logout',
        'url'   => 'logout',
        'order' => 100
    );
    jrCore_register_module_feature('jrCore', 'skin_menu_item', 'jrUser', 'logout', $_tmp);

    // Admin Notifications
    $_tmp = array(
        'label' => 'new account notify',
        'help'  => 'Do you want to be notified when a new User Account is created?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrUser', 'signup_notify', $_tmp);

    // register our custom CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrUser', 'jrUser.css');

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrUser', 'users online', 'jrUser_dashboard_panels');

    return true;
}

//---------------------------------------------------------
// DASHBOARD
//---------------------------------------------------------

/**
 * User Accounts Dashboard Panels
 * @param $panel
 * @return bool|int
 */
function jrUser_dashboard_panels($panel)
{
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'users online':
            $num = jrUser_session_online_user_count(900);
            if ($num == 0) {
                $num = 1;  // We always have the dashboard viewing user online
            }
            $out = array('title' => jrCore_number_format($num));
            break;

    }
    return ($out) ? $out : false;
}

//---------------------------------------------------------
// USER EVENT LISTENERS
//---------------------------------------------------------

/**
 * Optionally display Sign In page to non-logged in users dependent upon dite privacy setting
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_session_started_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_conf['jrCore_maintenance_mode'] != 'on' && !jrCore_is_ajax_request() && isset($_conf['jrUser_site_privacy']) && jrCore_checktype($_conf['jrUser_site_privacy'], 'number_nz') && $_conf['jrUser_site_privacy'] > 1 && !jrUser_is_logged_in()) {

        if (!isset($_post['module_url']) && isset($_conf['jrUser_site_privacy']) && $_conf['jrUser_site_privacy'] == 2) {
            return $_data;
        }
        elseif (isset($_post['option'])) {
            // See if we have requested an allowed module/view
            switch ($_post['option']) {
                case 'login':
                case 'login_save':
                case 'forgot':
                case 'forgot_save':
                case 'logout':
                case 'form_validate':
                case 'signup':
                case 'signup_save':
                case 'activate':
                    return $_data;
                    break;
                default:
                    jrUser_session_require_login();
                    break;
            }
        }
        // redirect user to login
        elseif (isset($_conf['jrUser_site_privacy']) && $_conf['jrUser_site_privacy'] == '2') {
            if (isset($_post['module_url']) && !isset($_urls["{$_post['module_url']}"])) {
                jrUser_session_require_login();
            }
        }

        // See if we have any signup quotas
        $_data['show_signup'] = 'no';
        $_qt = jrProfile_get_signup_quotas();
        if ($_qt && is_array($_qt) && count($_qt) > 0) {
            $_data['show_signup'] = 'yes';
        }
        jrCore_page_title($_conf['jrCore_system_name']);
        $out  = jrCore_parse_template('meta.tpl', array());
        $out .= jrCore_parse_template('index.tpl', $_data, 'jrUser');
        header('Connection: close');
        header("Content-Type: text/html; charset=utf-8");
        header('Content-Length: ' . strlen($out));
        echo $out;
        exit;
    }
    return $_data;
}

/**
 * Keep session table clean
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_process_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    // Random Session Cleanup
    if (mt_rand(1, 20) === 5) {
        $tbl = jrCore_db_table_name('jrUser', 'session');
        $req = "DELETE FROM {$tbl} WHERE session_updated < (UNIX_TIMESTAMP() - ({$_conf['jrUser_session_expire_min']} * 60))";
        jrCore_db_query($req);
    }
    return $_data;
}

/**
 * Rewrite non-SSL URLs to SSL
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrUser_is_logged_in() && isset($_conf['jrUser_force_ssl']) && $_conf['jrUser_force_ssl'] == 'on' && (strpos($_conf['jrCore_base_url'], 'https:') === 0 || !stripos($_conf['jrCore_base_url'], $_SERVER['HTTP_HOST']))) {
        // See if there are NON-SSL local URLS embedded in our SSL content
        $url = str_replace('https://', 'http://', $_conf['jrCore_base_url']);
        if (strpos($_data, $url)) {
            $_data = str_replace($url, str_replace('http://', 'https://', $_conf['jrCore_base_url']), $_data);
        }
    }
    return $_data;
}

/**
 * Keeps remember me cookie entries cleaned up
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUser_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Old Remember Me cookies
    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    $req = "DELETE FROM {$tbl} WHERE cookie_time < (UNIX_TIMESTAMP() - (14 * 86400))";
    jrCore_db_query($req);

    // Old Brute Force entries
    jrCore_clean_temp('jrUser', 7200);

    return $_data;
}

/**
 * Make sure our signup form fields are always required
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_signup_activated_listener($_data, $_user, $_conf, $_args, $event)
{
    // We have a new account - notify admins
    if (isset($_conf['jrUser_signup_notify']) && $_conf['jrUser_signup_notify'] == 'on') {
        $_ad = jrUser_get_admin_user_ids();
        if (is_array($_ad)) {
            $_rp                    = $_data;
            $_rp['system_name']     = $_conf['jrCore_system_name'];
            $_rp['ip_address']      = jrCore_get_ip();
            $new_profile_url        = jrCore_db_get_item_key('jrProfile', $_rp['_profile_id'], 'profile_url');
            $_rp['new_profile_url'] = "{$_conf['jrCore_base_url']}/{$new_profile_url}";
            list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'notify_signup', $_rp);
            foreach ($_ad as $uid) {
                jrUser_notify($uid, 0, 'jrUser', 'signup_notify', $sub, $msg);
            }
        }
    }
    return $_data;
}

/**
 * Make sure our signup form fields are always required
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_form_field_create_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['form_name'] == 'jrUser_signup' && isset($_data['name'])) {
        switch ($_data['name']) {
            case 'user_name':
            case 'user_email':
            case 'user_passwd1':
            case 'user_passwd2':
                $_data['required'] = true;
                break;
        }
    }
    return $_data;
}

/**
 * Adds support for "user_id" parameter to jrCore_list
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    // user_id=(id)[,id][,id][,..]
    if (isset($_data['user_id'])) {
        if (jrCore_checktype($_data['user_id'], 'number_nz')) {
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "_user_id = " . intval($_data['user_id']);
        }
        elseif (strpos($_data['user_id'], ',')) {
            $_tmp = explode(',', $_data['user_id']);
            if ($_tmp && is_array($_tmp)) {
                $_pi = array();
                foreach ($_tmp as $pid) {
                    if (is_numeric($pid)) {
                        $_pi[] = (int) $pid;
                    }
                }
                if ($_pi && is_array($_pi) && count($_pi) > 0) {
                    if (!isset($_data['search'])) {
                        $_data['search'] = array();
                    }
                    $_data['search'][] = "_user_id in " . implode(',', $_pi);
                }
            }
        }
    }
    return $_data;
}

/**
 * Add user info to return DS items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrUser_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] != 'jrUser' && $_args['module'] != 'jrProfile' && isset($_data['_items'][0]) && isset($_data['_items'][0]['_user_id'])) {

        // See if we do NOT include User keys in our results
        if (isset($_args['exclude_jrUser_keys']) && $_args['exclude_jrUser_keys'] === true) {
            return $_data;
        }

        // See if only specific keys are being requested - if none of them are user keys
        // then we do not need to go back to the DB to get any user info
        if (isset($_params['return_keys']) && is_array($_params['return_keys']) && count($_params['return_keys']) > 0) {
            $found = false;
            foreach ($_params['return_keys'] as $key) {
                if (strpos($key, 'user_') === 0) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return $_data;
            }
            unset($found);
        }

        // Add User keys into the data
        $_us = array();
        foreach ($_data['_items'] as $v) {
            if (isset($v['_user_id']) && jrCore_checktype($v['_user_id'], 'number_nz') && !isset($v['user_group'])) {
                $_us[] = (int) $v['_user_id'];
            }
        }
        if ($_us && is_array($_us) && count($_us) > 0) {
            $_rt = jrCore_db_get_multiple_items('jrUser', $_us);
            if ($_rt && is_array($_rt)) {
                // We've found user info - go though and setup by _user_id
                $_pr = array();
                $_up = array();
                foreach ($_rt as $v) {
                    $_pr["{$v['_user_id']}"] = $v;
                    unset($_pr["{$v['_user_id']}"]['_created']);
                    unset($_pr["{$v['_user_id']}"]['_updated']);
                    unset($_pr["{$v['_user_id']}"]['_item_id']);
                    unset($_pr["{$v['_user_id']}"]['_profile_id']);
                    $_up["{$v['_user_id']}"] = array($v['_created'], $v['_updated']);
                }
                // Add to results
                foreach ($_data['_items'] as $k => $v) {
                    if (isset($_pr["{$v['_user_id']}"]) && is_array($_pr["{$v['_user_id']}"])) {
                        $_data['_items'][$k] = array_merge($v, $_pr["{$v['_user_id']}"]);
                        unset($_data['_items'][$k]['user_password']);
                        $_data['_items'][$k]['user_created'] = $_up["{$v['_user_id']}"][0];
                        $_data['_items'][$k]['user_updated'] = $_up["{$v['_user_id']}"][1];
                    }
                }
            }
        }
    }
    return $_data;
}

//---------------------------------------------------------
// USER FUNCTIONS
//---------------------------------------------------------

/**
 * Check if this is a new device for a user and notify
 * @param $user_id int User ID
 * @return mixed
 */
function jrUser_notify_if_new_device($user_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrUser', 'device');
    if ($did = jrCore_get_cookie('jruser_device')) {
        // See if the device ID is valid
        $req = "SELECT notified FROM {$tbl} WHERE device_id = '" . jrCore_db_escape($did) . "' AND user_id = '{$uid}' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt) && isset($_rt['notified']) && $_rt['notified'] == '1') {
            // We are not new...
            jrCore_set_cookie('jruser_device', $did, 365);
            return false;
        }
    }
    // If this is the FIRST time a user is in the device list,
    // save the device but don't send them any email
    $did = md5(microtime());
    $uip = jrCore_db_escape(jrCore_get_ip());
    $req = "SELECT user_id FROM {$tbl} WHERE user_id = '{$uid}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        // We already have entries - see if this is a new one
        $req = "INSERT INTO {$tbl} (user_id, device_id, ip_address, notified) VALUES ('{$uid}', '{$did}', '{$uip}', 1) ON DUPLICATE KEY UPDATE notified = 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {

            // It was a NEW device (1 = "inserted", 2 = "updated")
            jrCore_set_cookie('jruser_device', $did, 365);

            // Send out notification email
            $_us = jrCore_db_get_item('jrUser', $uid);
            if ($_us && is_array($_us) && isset($_us['user_email'])) {
                list($sub, $msg) = jrCore_parse_email_templates('jrUser', 'new_device', $_us);
                jrCore_send_email($_us['user_email'], $sub, $msg);
            }
            return true;
        }
    }
    else {
        // New
        $req = "INSERT INTO {$tbl} (user_id, device_id, ip_address, notified) VALUES ('{$uid}', '{$did}', '{$uip}', 1)";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            jrCore_logger('MAJ', "unable to save unique device ID to device table - check debug log");
        }
    }
    jrCore_set_cookie('jruser_device', $did, 365);
    return false;
}

/**
 * Return an array of admin/master user id's
 */
function jrUser_get_admin_user_ids()
{
    $tbl = jrCore_db_table_name('jrUser', 'item_key');
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = 'user_group' AND `value` IN('admin', 'master')";
    return jrCore_db_query($req, '_item_id', false, '_item_id');
}

/**
 * Return an array of master user id's
 */
function jrUser_get_master_user_ids()
{
    $tbl = jrCore_db_table_name('jrUser', 'item_key');
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = 'user_group' AND `value` = 'master'";
    return jrCore_db_query($req, '_item_id', false, '_item_id');
}

/**
 * Returns true if viewing user is linked to the profile_id
 * Note: Master/Admin users will return false!
 * @param $profile_id integer Profile ID to check
 * @return bool
 */
function jrUser_is_linked_to_profile($profile_id)
{
    // User can always see their home profile
    if ($profile_id == jrUser_get_profile_home_key('_profile_id')) {
        return true;
    }
    if (jrUser_is_admin()) {
        if (isset($_SESSION['user_linked_profile_ids']) && in_array($profile_id, explode(',', $_SESSION['user_linked_profile_ids']))) {
            return true;
        }
        return false;
    }
    // validate id
    if (!jrCore_checktype($profile_id, 'number_nz')) {
        return false;
    }
    if (isset($_SESSION['user_linked_profile_ids']) && in_array($profile_id, explode(',', $_SESSION['user_linked_profile_ids']))) {
        // The viewing user is linked to this profile
        return true;
    }
    return false;
}

/**
 * Pending Users browser
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrUser_dashboard_pending_users($_post, $_user, $_conf)
{
    // get our items
    $_pr = array(
        'search'                       => array(
            'user_validated = 0'
        ),
        'order_by'                     => array(
            '_created' => 'numerical_desc'
        ),
        'include_jrProfile_keys'       => true,
        'include_jrProfile_quota_keys' => true,
        'ignore_pending'               => true,
        'no_cache'                     => true,
        'privacy_check'                => false
    );
    $_us = jrCore_db_search_items('jrUser', $_pr);

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'id';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'user name';
    $dat[2]['width'] = '35%';
    $dat[3]['title'] = 'email';
    $dat[3]['width'] = '40%';
    $dat[4]['title'] = 'activate';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'resend';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'modify';
    $dat[6]['width'] = '5%';
    $dat[7]['title'] = 'delete';
    $dat[7]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (is_array($_us['_items'])) {
        $uurl = jrCore_get_module_url('jrUser');
        $purl = jrCore_get_module_url('jrProfile');
        foreach ($_us['_items'] as $k => $_usr) {
            $dat             = array();
            $dat[1]['title'] = $_usr['_user_id'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_usr['user_name'];
            $dat[3]['title'] = $_usr['user_email'];
            $dat[4]['title'] = jrCore_page_button("a{$k}", 'activate', "if (confirm('Activate this User Account and send them an email?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$uurl}/user_activate/user_id={$_usr['_user_id']}') }");
            if (isset($_usr['quota_jrUser_signup_method']) && $_usr['quota_jrUser_signup_method'] == 'admin') {
                $dat[5]['title'] = jrCore_page_button("r{$k}", 'resend', 'disabled');
            }
            else {
                $dat[5]['title'] = jrCore_page_button("r{$k}", 'resend', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$uurl}/user_resend/user_id={$_usr['_user_id']}')");
            }
            $dat[6]['title'] = jrCore_page_button("m{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$uurl}/account/user_id={$_usr['_user_id']}')");
            $dat[7]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this User Account? This will also deleted the User Profile associated with this account.')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$purl}/delete_save/id={$_usr['_profile_id']}') }");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no pending user accounts at this time</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
}

/**
 * Custom Data Store browser tool
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function jrUser_data_browser($_post, $_user, $_conf)
{
    $order_dir = 'desc';
    $order_opp = 'asc';
    if (isset($_post['order_dir']) && ($_post['order_dir'] == 'asc' || $_post['order_dir'] == 'numerical_asc')) {
        $order_dir = 'asc';
        $order_opp = 'desc';
    }

    $order_by = '_created';
    if (isset($_post['order_by'])) {
        switch ($_post['order_by']) {
            case '_item_id':
            case 'user_last_login':
                $order_dir = 'numerical_' . $order_dir;
                $order_opp = 'numerical_' . $order_opp;
            case 'user_name':
            case 'user_email':
                $order_by = $_post['order_by'];
                break;
        }
    }

    // get our items
    $_pr = array(
        'search'         => array(
            '_item_id > 0'
        ),
        'pagebreak'      => (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 12,
        'page'           => 1,
        'order_by'       => array(
            $order_by => $order_dir
        ),
        'return_keys'    => array('_created', '_item_id', '_user_id', '_profile_id', 'user_name', 'user_group', 'user_image_time', 'user_email', 'user_last_login'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'privacy_check'  => false
    );
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $_pr['page'] = (int) $_post['p'];
    }
    // See we have a search condition
    $_ex = false;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_ex = array('search_string' => $_post['search_string']);
        // Check for passing in a specific key name for search
        if (strpos($_post['search_string'], ':')) {
            list($sf, $ss) = explode(':', $_post['search_string'], 2);
            $_post['search_string'] = $ss;
            if (is_numeric($ss)) {
                $_pr['search'][] = "{$sf} = {$ss}";
            }
            else {
                $_pr['search'][] = "{$sf} like {$ss}%";
            }
        }
        else {
            $_pr['search'][] = "% like %{$_post['search_string']}%";
        }
    }
    $_us = jrCore_db_search_items('jrUser', $_pr);

    // Start our output
    $url             = $_conf['jrCore_base_url'] . jrCore_strip_url_params($_post['_uri'], array('order_by', 'order_dir'));
    $dat             = array();
    $dat[1]['title'] = 'img';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'id';
    $dat[2]['width'] = '5%';
    $dat[3]['title'] = '<a href="' . $url . '/order_by=user_name/order_dir=' . $order_opp . '">user name</a>';
    $dat[3]['width'] = '22%';
    $dat[4]['title'] = 'profile name';
    $dat[4]['width'] = '22%';
    $dat[5]['title'] = '<a href="' . $url . '/order_by=user_email/order_dir=' . $order_opp . '">email</a>';
    $dat[5]['width'] = '21%';
    $dat[6]['title'] = '<a href="' . $url . '/order_by=user_last_login/order_dir=' . $order_opp . '">last login</a>';
    $dat[6]['width'] = '15%';
    $dat[7]['title'] = 'modify';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%';

    jrCore_page_table_header($dat);

    if (isset($_us['_items']) && is_array($_us['_items'])) {

        // Get profile info for these users
        $_pi = array();
        foreach ($_us['_items'] as $_usr) {
            $_pi[] = (int) $_usr['_profile_id'];
        }
        $_pi = jrCore_db_get_multiple_items('jrProfile', $_pi);
        $_pd = array();
        if ($_pi && is_array($_pi)) {
            foreach ($_pi as $_prf) {
                $_pd["{$_prf['_profile_id']}"] = $_prf;
            }
        }
        unset($_pi);

        foreach ($_us['_items'] as $k => $_usr) {
            $_prf            = (isset($_pd["{$_usr['_profile_id']}"])) ? $_pd["{$_usr['_profile_id']}"] : array();
            $dat             = array();
            $_im             = array(
                'crop'  => 'auto',
                'alt'   => $_usr['user_name'],
                'title' => $_usr['user_name'],
                '_v'    => (isset($_usr['user_image_time']) && $_usr['user_image_time'] > 0) ? $_usr['user_image_time'] : false
            );
            $dat[1]['title'] = jrImage_get_image_src('jrUser', 'user_image', $_usr['_user_id'], 'xsmall', $_im);
            $dat[2]['title'] = $_usr['_user_id'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = '<h3>' . $_usr['user_name'] . '</h3>';
            $dat[4]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_prf['profile_url']}\">{$_prf['profile_name']}</a>";
            $dat[5]['title'] = $_usr['user_email'];
            $dat[6]['title'] = (isset($_usr['user_last_login'])) ? jrCore_format_time($_usr['user_last_login']) : '-';
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = jrCore_page_button("m{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/account/user_id={$_usr['_user_id']}')");
            if (!jrUser_is_master() && ($_usr['user_group'] == 'admin' || $_usr['user_group'] == 'master')) {
                $dat[8]['title'] = jrCore_page_button("d{$k}", 'delete', 'disabled');
            }
            else {
                $dat[8]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this User Account? The associated User Profile will NOT be deleted.')){jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$_usr['_user_id']}')}");
            }
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_us, $_ex);
    }
    else {
        $dat = array();
        if (isset($_post['search_string'])) {
            $dat[1]['title'] = '<p>No Results found for your Search Criteria.</p>';
        }
        else {
            $dat[1]['title'] = '<p>No User Accounts found!</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return true;
}

/**
 * Creates tab bar in User Account section
 * @param string $active active string active tab can be one of: global,quota,tools,language,templates,info
 * @param array $_active_user - Active User info array
 * @return bool
 */
function jrUser_account_tabs($active = 'account', $_active_user = null)
{
    global $_conf, $_post, $_user;
    $pid = $_user['_profile_id'];
    $uid = $_user['_user_id'];
    if (jrUser_is_admin()) {
        if (isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
            $pid = (int) $_post['profile_id'];
        }
        elseif (is_array($_active_user) && isset($_active_user['_profile_id'])) {
            $pid = (int) $_active_user['_profile_id'];
        }
        if (isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
            // Make sure user account exists..
            if ($tst = jrCore_db_get_item_key('jrUser', $_post['user_id'], '_profile_id')) {
                $uid = (int) $_post['user_id'];
            }
        }
        elseif (is_array($_active_user) && isset($_active_user['_user_id'])) {
            if ($tst = jrCore_db_get_item_key('jrUser', $_active_user['_user_id'], '_profile_id')) {
                $uid = (int) $_active_user['_user_id'];
            }
        }

    }
    $_tbs = array();
    // Check for registered user tabs
    $_tmp = jrCore_get_registered_module_features('jrUser', 'account_tab');
    if ($_tmp) {

        // Make sure tabs from Profile and User modules load up first
        $_tm2 = array(
            'jrProfile' => $_tmp['jrProfile'],
            'jrUser'    => $_tmp['jrUser']
        );
        unset($_tmp['jrProfile'], $_tmp['jrUser']);
        $_tmp = array_merge($_tm2, $_tmp);

        $_lng = jrUser_load_lang_strings();
        foreach ($_tmp as $mod => $_entries) {

            $url = jrCore_get_module_url($mod);
            foreach ($_entries as $view => $label) {

                // $label can come in as an array
                if (is_array($label)) {
                    $_tbs["{$mod}/{$view}"] = array(
                        'label' => (isset($_lng[$mod]["{$label['label']}"])) ? $_lng[$mod]["{$label['label']}"] : $label['label'],
                        'url'   => "{$_conf['jrCore_base_url']}/{$url}/{$view}"
                    );
                    if (!isset($label['quota_check']) || $label['quota_check'] === true) {
                        // Check for specific field access
                        $fld = "quota_{$mod}_allowed";
                        if (isset($label['field']) && strlen($label['field']) > 0) {
                            $fld = $label['field'];
                        }
                        if (isset($_user[$fld]) && $_user[$fld] != 'on') {
                            unset($_tbs["{$mod}/{$view}"]);
                            continue;
                        }
                    }
                }
                else {
                    // Make sure the viewing user has Quota access to this module
                    if (isset($_user["quota_{$mod}_allowed"]) && $_user["quota_{$mod}_allowed"] != 'on') {
                        continue;
                    }
                    $_tbs["{$mod}/{$view}"] = array(
                        'label' => (isset($_lng[$mod][$label])) ? $_lng[$mod][$label] : $label,
                        'url'   => "{$_conf['jrCore_base_url']}/{$url}/{$view}"
                    );
                }
                // If this is an admin user doing the modification, and it is NOT their
                // own account, then we must add in profile_id and user_id info on the URL
                if (jrUser_is_admin()) {
                    $_tbs["{$mod}/{$view}"]['url'] .= "/profile_id={$pid}/user_id={$uid}";
                }
                if (isset($_post['module']) && $_post['module'] == $mod && isset($_post['option']) && $_post['option'] == $view) {
                    $_tbs["{$mod}/{$view}"]['active'] = true;
                }
                elseif ($active == $view) {
                    $_tbs["{$mod}/{$view}"]['active'] = true;
                }
            }
        }
    }
    $_tbs = jrCore_trigger_event('jrUser', 'account_tabs', $_tbs);
    jrCore_page_tab_bar($_tbs);
}

/**
 * Register a setting to be shown in the User Account
 * @param $module string Module registering setting for
 * @param $_field array Array of setting information
 * @return bool
 */
function jrUser_register_setting($module, $_field)
{
    if (!isset($_field['name'])) {
        jrCore_set_form_notice('error', "You must provide a valid field name");
        return false;
    }
    $_tmp = jrCore_get_flag('jruser_register_setting');
    if (!$_tmp) {
        $_tmp = array();
    }
    if (!isset($_tmp[$module])) {
        $_tmp[$module] = array();
    }
    $_field['name']  = "user_{$module}_{$_field['name']}";
    $_tmp[$module][] = $_field;
    jrCore_set_flag('jruser_register_setting', $_tmp);
    return true;
}

/**
 * Notify a User about a specific event
 * @param mixed $to_user_id User ID to send notification to (int or array of int)
 * @param int $from_user_id User ID notification is from
 * @param string $module Module that has registered the notification event
 * @param string $event Event Name
 * @param string $subject Subject of notification
 * @param string $message Message of notification
 * @return bool
 */
function jrUser_notify($to_user_id, $from_user_id, $module, $event, $subject, $message)
{
    global $_conf;
    // Make sure we're not recursive
    if (jrCore_get_flag('jruser_notify_is_running')) {
        return true;
    }
    jrCore_set_flag('jruser_notify_is_running', 1);

    // Make sure module has registered
    $_tmp = jrCore_get_registered_module_features('jrUser', 'notification');
    if (!isset($_tmp[$module][$event])) {
        // Module did not register this event
        jrCore_logger('MAJ', "{$module} has not registered the {$event} notification event - not sending."); // log an error to the activity log
        jrCore_delete_flag('jruser_notify_is_running');
        return false;
    }

    // Get User info
    if (!is_array($to_user_id)) {
        $to_user_id = array($to_user_id);
    }
    // Validate
    foreach ($to_user_id as $k => $uid) {
        if (!jrCore_checktype($uid, 'number_nz')) {
            unset($to_user_id[$k]);
        }
    }
    if (count($to_user_id) === 0) {
        // We came out with nothing
        jrCore_delete_flag('jruser_notify_is_running');
        return false;
    }

    // Get user info
    $_rt = jrCore_db_get_multiple_items('jrUser', $to_user_id);
    if (!$_rt || !is_array($_rt)) {
        jrCore_delete_flag('jruser_notify_is_running');
        return false;
    }

    // Prune
    $key = "user_{$module}_{$event}_notifications";
    foreach ($_rt as $k => $_usr) {

        // Check for valid email
        if (!isset($_usr['user_email']) || !jrCore_checktype($_usr['user_email'], 'email')) {
            unset($_rt[$k]);
            continue;
        }

        // See if this user has disabled ALL notifications
        if (isset($_usr['user_notifications_disabled']) && $_usr['user_notifications_disabled'] == 'on') {
            unset($_rt[$k]);
            continue;
        }

        // See if notifications are enabled for this specific event
        if (isset($_usr[$key]) && $_usr[$key] == 'off') {
            unset($_rt[$k]);
            continue;
        }
        elseif (!isset($_usr[$key]) ||(isset($_tmp[$module][$event]['email_only']) && $_tmp[$module][$event]['email_only'] === true)) {
            // Not set OR Forced email on this notification event
            $_rt[$k][$key] = 'email';
        }
    }
    if (count($_rt) === 0) {
        // Came out empty
        jrCore_delete_flag('jruser_notify_is_running');
        return true;
    }

    // notify user trigger
    $_data = array(
        'to_user_id'   => $to_user_id,
        'from_user_id' => $from_user_id,
        'module'       => $module,
        'event'        => $event,
        'subject'      => $subject,
        'message'      => $message,
        'registered'   => $_tmp
    );
    jrCore_trigger_event('jrUser', 'notify_user', $_data);

    // Process
    $url = jrCore_get_module_url('jrUser');
    foreach ($_rt as $k => $_usr) {

        // We're sending an email - make sure we add in our preferences notice
        if (isset($_usr[$key]) && $_usr[$key] == 'email') {
            if (!isset($_usr['user_validate']{30})) {
                $_save = array(
                    'user_validate' => md5(microtime())
                );
                jrCore_db_update_item('jrUser', $_usr['_user_id'], $_save);
                $_usr['user_validate'] = $_save['user_validate'];
                unset($_save);
            }
            $_usr['module_url']      = $url;
            $_usr['preferences_url'] = "{$_conf['jrCore_base_url']}/{$url}/notifications";
            $_usr['unsubscribe_url'] = "{$_conf['jrCore_base_url']}/{$url}/unsubscribe/{$_usr['user_validate']}";

            // check for options
            $_opts = null;
            if (isset($_tmp[$module][$event]['html_email']) && $_tmp[$module][$event]['html_email'] === true) {
                $_opts = array('send_as_html' => true);
                $message .= "<br><br>" . nl2br(jrCore_parse_template("email_preferences_footer.tpl", $_usr, 'jrUser'));
            }
            else {
                $message .= "\n\n" . jrCore_parse_template("email_preferences_footer.tpl", $_usr, 'jrUser');
            }
            jrCore_send_email($_usr['user_email'], $subject, $message, $_opts);
        }

        // Send Private note if module enabled
        elseif (jrCore_module_is_active('jrPrivateNote')) {
            jrPrivateNote_send_note($_usr['_user_id'], $from_user_id, $subject, $message);
        }
    }
    jrCore_delete_flag('jruser_notify_is_running');
    return true;
}

/**
 * Check if a User's Profile Quota allows Access to a module
 * @param $module string Module Name
 * @return bool
 */
function jrUser_check_quota_access($module = null)
{
    global $_post, $_user;
    if (is_null($module) || strlen($module) === 0) {
        $module = $_post['module'];
    }
    if (jrUser_is_admin()) {
        // Master and Admin users are not bound by the Quota Access
        // however we want to let them know that access is turned off
        // in case they are not aware of that for a profile
        if (isset($_user["quota_{$module}_allowed"]) && $_user["quota_{$module}_allowed"] != 'on') {
            // Disabled - see if we are on a create form
            if (strpos($_post['option'], 'create') === 0 && !strpos($_post['option'], 'save')) {
                jrCore_set_form_notice('notice', 'Quota access to this module is currently disabled');
            }
        }
        // User has access - check that they are not on a CREATE form and have reached max items
        if (isset($_post['option']) && strpos(' ' . $_post['option'], 'create')) {
            jrUser_session_sync($_user['_user_id']);
            if (isset($_user["quota_{$module}_max_items"]) && jrCore_checktype($_user["quota_{$module}_max_items"], 'number_nz') && isset($_user["profile_{$module}_item_count"]) && $_user["profile_{$module}_item_count"] >= $_user["quota_{$module}_max_items"]) {
                jrCore_set_form_notice('notice', 'This profile has reached the max allowed items of this type');
            }
        }
        return true;
    }
    if (isset($_user["quota_{$module}_allowed"]) && $_user["quota_{$module}_allowed"] != 'on') {
        jrUser_not_authorized();
    }
    // User has access - check that they are not on a CREATE form and have reached max items
    if (isset($_post['option']) && strpos(' ' . $_post['option'], 'create')) {
        jrUser_session_sync($_user['_user_id']);
        if (isset($_user["quota_{$module}_max_items"]) && jrCore_checktype($_user["quota_{$module}_max_items"], 'number_nz') && isset($_user["profile_{$module}_item_count"]) && $_user["profile_{$module}_item_count"] >= $_user["quota_{$module}_max_items"]) {
            jrUser_reset_cache($_user['_user_id']);
            $_lang = jrUser_load_lang_strings();
            jrCore_set_form_notice('error', $_lang['jrCore'][70]);
            $_SESSION['quota_max_items_reached'] = true;
        }
    }
    return true;
}

/**
 * View a module's language strings
 * @param $type string "module" or "skin"
 * @param $module string Module or Skin directory name
 * @param $_post array array from jrCore_parseUrl()
 * @param $_user array viewing User info
 * @param $_conf array System Config
 * @return mixed
 */
function jrUser_show_module_lang_strings($type, $module, $_post, $_user, $_conf)
{
    global $_mods;
    $_lang = jrUser_load_lang_strings();

    // Generate our output
    if (isset($type) && $type == 'module') {
        $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/language";
        jrCore_page_admin_tabs($module, 'language');

        // Setup our module jumper
        $subtitle = '<select name="mod_select" class="form_select form_select_item_jumper" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/'+ v +'/admin/language')\">";
        $_tmpm    = array();
        foreach ($_mods as $mod_dir => $_info) {
            $_tmpm[$mod_dir] = $_info['module_name'];
        }
        asort($_tmpm);
        foreach ($_tmpm as $mod_dir => $title) {
            if (!jrCore_module_is_active($mod_dir)) {
                continue;
            }
            if (isset($_lang[$mod_dir])) {
                if ($mod_dir == $_post['module']) {
                    $subtitle .= '<option value="' . $_post['module_url'] . '" selected="selected"> ' . $title . "</option>\n";
                }
                else {
                    $murl = jrCore_get_module_url($mod_dir);
                    $subtitle .= '<option value="' . $murl . '"> ' . $title . "</option>\n";
                }
            }
        }
        $subtitle .= '</select>';
        jrCore_page_banner("Language Strings", $subtitle);

        // See if we are disabled
        if (!jrCore_module_is_active($module)) {
            jrCore_set_form_notice('notice', 'This module is currently disabled');
        }
    }
    else {
        $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/language/skin={$module}";
        jrCore_page_skin_tabs($module, 'language');

        $murl     = jrCore_get_module_url('jrCore');
        $subtitle = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$murl}/skin_admin/language/skin='+ v)\">";
        $_tmpm    = jrCore_get_skins();
        foreach ($_tmpm as $skin_dir => $_skin) {
            if (is_dir(APP_DIR ."/skins/{$skin_dir}/lang")) {
                $_mta = jrCore_skin_meta_data($skin_dir);
                $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;
                if ($skin_dir == $_post['skin']) {
                    $subtitle .= '<option value="' . $skin_dir . '" selected="selected"> ' . $name . "</option>\n";
                }
                else {
                    $subtitle .= '<option value="' . $skin_dir . '"> ' . $name . "</option>\n";
                }
            }
        }
        $subtitle .= '</select>';
        jrCore_page_banner("Language Strings", $subtitle);
    }
    jrCore_get_form_notice();

    // Get the different languages supported
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "SELECT lang_code FROM {$tbl} WHERE lang_module = '" . jrCore_db_escape($module) . "' GROUP BY lang_code ORDER BY lang_code ASC";
    $_qt = jrCore_db_query($req, 'lang_code', false, 'lang_code');
    if (!isset($_post['lang_code']{0})) {
        if (isset($_conf['jrUser_default_language']{0})) {
            $_post['lang_code'] = $_conf['jrUser_default_language'];
        }
        else {
            $_post['lang_code'] = 'en-US';
        }
    }
    if (!isset($_qt["{$_post['lang_code']}"])) {
        $_post['lang_code'] = 'en-US';
    }

    jrCore_page_search('search', $url);

    // Form init
    if (isset($type) && $type == 'module') {
        $_tmp = array(
            'submit_value' => 'save changes',
            'action'       => 'admin_save/language'
        );
    }
    else {
        $_tmp = array(
            'submit_value' => 'save changes',
            'action'       => "skin_admin_save/language/skin={$module}"
        );
    }
    jrCore_form_create($_tmp);

    // Our "select" jumper for installed languages
    $_tmp = array(
        'name'     => 'lang_code',
        'label'    => 'Language',
        'help'     => false,
        'type'     => 'select',
        'options'  => $_qt,
        'value'    => $_post['lang_code'],
        'onchange' => "var l=this.options[this.selectedIndex].value;self.location='{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/language/lang_code='+ l"
    );
    if (isset($type) && $type == 'skin') {
        $curl             = jrCore_get_module_url('jrCore');
        $_tmp['onchange'] = "var l=this.options[this.selectedIndex].value;self.location='{$_conf['jrCore_base_url']}/{$curl}/skin_admin/language/lang_code='+ l";
    }
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'p',
        'label' => 'page number',
        'help'  => false,
        'type'  => 'hidden',
        'value' => (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? $_post['p'] : 1
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'lang_code',
        'label' => 'Language',
        'help'  => false,
        'type'  => 'hidden',
        'value' => $_post['lang_code']
    );
    jrCore_form_field_create($_tmp);

    // Get this module's language strings out of the database
    $_ex = false;
    $req = "SELECT * FROM {$tbl} WHERE lang_module = '" . jrCore_db_escape($module) . "' AND lang_code = '" . jrCore_db_escape($_post['lang_code']) . "' ";
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_post['search_string'] = trim(urldecode($_post['search_string']));
        $str                    = jrCore_db_escape($_post['search_string']);
        $req .= "AND lang_text LIKE '%{$str}%' ";
        $_ex = array('search_string' => $_post['search_string']);
    }
    $req .= "ORDER BY (lang_key + 0) ASC";
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');
    if (isset($_rt['_items']) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_lstr) {
            if (isset($_lstr['lang_key']) && is_numeric($_lstr['lang_key'])) {
                if (!isset($module_lang_header)) {
                    jrCore_page_section_header("Default Language Strings");
                    $module_lang_header = true;
                }
            }
            else {
                if (!isset($custom_lang_header)) {
                    jrCore_page_section_header("Custom Language Strings");
                    $custom_lang_header = true;
                }
            }
            $lid = "lang_{$_lstr['lang_id']}";
            $err = '';
            if (isset($_SESSION['jr_form_field_highlight'][$lid])) {
                unset($_SESSION['jr_form_field_highlight'][$lid]);
                $err = ' field-hilight';
            }
            $html = '<input type="text" class="form_text lang_input' . $err . '" id="l' . $_lstr['lang_id'] . '" name="lang_' . $_lstr['lang_id'] . '" value="' . jrCore_entity_string($_lstr['lang_text']) . '">';
            if ($_lstr['lang_default'] != $_lstr['lang_text']) {
                $html .= ' <input type="button" class="form_button" value="use default" title="default value: ' .  jrCore_entity_string($_lstr['lang_default']) . '" onclick="var v=$(this).val();if (v==\'use default\'){$(\'#l' . $_lstr['lang_id'] . '\').val(\'' . addslashes($_lstr['lang_default']) . '\');$(this).val(\'cancel\');} else {$(\'#l' . $_lstr['lang_id'] . '\').val(\'' . addslashes($_lstr['lang_text']) . '\');$(this).val(\'use default\');}">';
            }
            $_tmp = array(
                'type'     => 'page_link_cell',
                'label'    => $_lstr['lang_key'],
                'url'      => $html,
                'module'   => 'jrCore',
                'template' => 'page_link_cell.tpl'
            );
            jrCore_create_page_element('page', $_tmp);
        }
        jrCore_set_flag('jr_html_page_table_header_colspan', 2);
        jrCore_page_table_pager($_rt, $_ex);
    }
    jrCore_page_set_no_header_or_footer();
    return jrCore_page_display(true);
}

/**
 * Returns an array of active user accounts user_id => $field
 * @param $field string User Field to return - default is "user_name"
 * @return array Returns array of user info
 */
function jrUser_get_users($field = 'user_name')
{
    $_params = array(
        'search'      => array(
            "user_active = 1"
        ),
        'order_by'    => array(
            $field => 'asc'
        ),
        'return_keys' => array(
            '_user_id',
            $field
        ),
        'limit'       => 1000000
    );
    $_rt     = jrCore_db_search_items('jrUser', $_params);
    $_us     = array();
    if (isset($_rt) && is_array($_rt) && isset($_rt['info']['total_items']) && jrCore_checktype($_rt['info']['total_items'], 'number_nz')) {
        foreach ($_rt['_items'] as $_usr) {
            $_us["{$_usr['_user_id']}"] = $_usr[$field];
        }
    }
    return $_us;
}

/**
 * Load a user's active language strings
 * @param $lang string Language to load strings for
 * @param $cache bool set to false to force lang string reload
 * @return array Returns array of Language strings
 */
function jrUser_load_lang_strings($lang = null, $cache = true)
{
    global $_mods, $_user, $_post, $_conf;

    if ($cache) {
        $_tmp = jrCore_get_flag('jr_lang');
        if ($_tmp) {
            // We've already loaded in this process
            return $_tmp;
        }
    }

    // Check for user changing languages
    if (isset($_post['set_user_language']{0})) {
        $lang = basename($_post['set_user_language']);
        // Make sure this is a VALID language
        $_valid = jrUser_get_languages();
        if (isset($_valid[$lang])) {
            // See if this user is logged in
            if (jrUser_is_logged_in()) {
                $tbl = jrCore_db_table_name('jrUser', 'item_key');
                $req = "UPDATE {$tbl} SET `value` = '" . jrCore_db_escape($lang) . "' WHERE `_item_id` = '{$_user['_user_id']}' AND `key` = 'user_language' LIMIT 1";
                jrCore_db_query($req);
                $_user['user_language'] = $lang;
            }
            setcookie('jr_lang', $lang, time() + 86400000);
            $_COOKIE['jr_lang'] = $lang;
        }
    }
    if (isset($_COOKIE['jr_lang']) && strlen($_COOKIE['jr_lang']) > 0) {
        $lang = $_COOKIE['jr_lang'];
    }
    if (!$lang || is_null($lang)) {
        if (jrUser_is_logged_in() && isset($_user['user_language'])) {
            $_valid = jrUser_get_languages();
            if ($_valid && isset($_valid[$_user['user_language']])) {
                $lang = $_user['user_language'];
            }
        }
        elseif (isset($_COOKIE['jr_lang'])) {
            // We've been set by COOKIE
            $lang = substr($_COOKIE['jr_lang'], 0, 5);
        }
        // Fall through - make sure we use default
        if (!isset($lang{0})) {
            $lang = (isset($_conf['jrUser_default_language']{2})) ? $_conf['jrUser_default_language'] : 'en-US';
        }
    }

    // Check for cache
    $ckey = "load_lang_string_{$lang}";
    $_tmp = false;
    if ($cache) {
        $_tmp = jrCore_is_cached('jrUser', $ckey, false);
    }
    if (!$_tmp) {
        // en-US is our default
        $tbl = jrCore_db_table_name('jrUser', 'language');
        if (isset($lang) && $lang == 'en-US') {
            $req = "SELECT * FROM {$tbl} WHERE lang_code = 'en-US'";
        }
        else {
            // Get user's language + en-US (for defaults)
            $req = "SELECT * FROM {$tbl} WHERE (lang_code = 'en-US' OR lang_code = '" . jrCore_db_escape($lang) . "')";
        }
        $req .= " AND lang_module IN('" . implode("','", array_keys($_mods)) . "','{$_conf['jrCore_active_skin']}')";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if (!isset($_rt[0]) || !is_array($_rt[0])) {
            jrCore_logger('CRI', "jrUser_load_lang_strings: unable to load any language strings for lang: {$lang}");
            return false;
        }
        // Setup some default lang settings
        $_tmp              = array();
        $_tmp['_settings'] = array(
            'lang'      => 'en',
            'code'      => 'en-US',
            'charset'   => 'utf-8',
            'direction' => 'ltr'
        );
        foreach ($_rt as $num => $_lang) {
            if ($lang && $_lang['lang_code'] == $lang) {
                // Looks like we have mixed en-US + other lang - update so the other is primary
                $_tmp['_settings'] = array(
                    'lang'      => substr($_lang['lang_code'], 0, 2),
                    'code'      => $_lang['lang_code'],
                    'direction' => $_lang['lang_ltr']
                );
            }
            if (!isset($_tmp["{$_lang['lang_module']}"]["{$_lang['lang_key']}"])) {
                $_tmp["{$_lang['lang_module']}"]["{$_lang['lang_key']}"] = $_lang['lang_text'];
            }
            elseif (isset($_tmp["{$_lang['lang_module']}"]["{$_lang['lang_key']}"]) && $_lang['lang_code'] != 'en-US') {
                $_tmp["{$_lang['lang_module']}"]["{$_lang['lang_key']}"] = $_lang['lang_text'];
            }
            unset($_rt[$num]);
        }
        jrCore_add_to_cache('jrUser', $ckey, $_tmp, 0, 0, false);
    }
    jrCore_set_flag('jr_lang', $_tmp);
    return $_tmp;
}

/**
 * Installs and Updates language strings for modules and skins.
 * @param $type string one of "module" or "skin"
 * @param $dir string Module or Skin Directory
 * @return bool Returns true
 */
function jrUser_install_lang_strings($type, $dir)
{
    $lang_dir = APP_DIR . "/{$type}s/{$dir}/lang";
    if (!is_dir($lang_dir)) {
        // No lang strings
        return false;
    }
    if ($h = opendir($lang_dir)) {
        $_lng = array();
        while (($file = readdir($h)) !== false) {
            if (jrCore_file_extension($file) == 'php') {
                $lang = array();
                $code = str_replace('.php', '', $file);
                include_once "{$lang_dir}/{$file}"; // $lang will be set here if we have strings...
                if (isset($lang) && is_array($lang)) {
                    // Note that we do not delete previously entered lang strings
                    $_ins = array();
                    $_upd = array();
                    $_new = array();
                    // Go through and update any defaults, or insert any lang strings that we haven't installed yet
                    $mod = jrCore_db_escape($dir);
                    $cod = jrCore_db_escape($code);
                    $tbl = jrCore_db_table_name('jrUser', 'language');
                    $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '{$cod}'";
                    $_rt = jrCore_db_query($req, 'lang_key');

                    // okay - check if we have existing lang keys for this module - we need to
                    // go through the $_lang array and prune out any entries that
                    // are not being changed or inserted.
                    foreach ($lang as $lid => $lstr) {
                        if (!isset($_rt[$lid])) {
                            // This is a new string - insert
                            $_ins[$lid] = $lstr;
                        }
                        elseif (isset($_rt[$lid]['lang_default']) && $_rt[$lid]['lang_default'] != $lstr) {
                            // See if it has been changed at all...
                            if (isset($_rt[$lid]['lang_text']) && $_rt[$lid]['lang_text'] == $_rt[$lid]['lang_default']) {
                                // Never been changed - update both
                                $_new[$lid] = $lstr;
                            }
                            else {
                                $_upd[$lid] = $lstr;
                            }
                        }
                    }
                    // Text flow direction
                    $ltr = 'ltr';
                    if (isset($lang['direction']) && $lang['direction'] = 'rtl') {
                        $ltr = 'rtl';
                    }
                    // Insert new entries if we have any
                    if (isset($_ins) && count($_ins) > 0) {
                        $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES\n";
                        foreach ($_ins as $key => $str) {
                            $req .= "('{$mod}','{$cod}','utf-8','{$ltr}','" . jrCore_db_escape($key) . "','" . jrCore_db_escape($str) . "','" . jrCore_db_escape($str) . "'),";
                        }
                        $req = substr($req, 0, strlen($req) - 1);
                        $cnt = jrCore_db_query($req, 'COUNT');
                        if (isset($cnt) && $cnt > 0) {
                            jrCore_logger('INF', "{$dir} {$type} installed {$cnt} new {$code} language strings");
                        }
                    }
                    // Update existing entries with new default
                    if (isset($_upd) && count($_upd) > 0) {
                        foreach ($_upd as $key => $str) {
                            $req = "UPDATE {$tbl} SET lang_default = '" . jrCore_db_escape($str) . "' WHERE lang_module = '{$mod}' AND lang_code = '{$cod}' AND lang_key = '{$key}'";
                            jrCore_db_query($req);
                        }
                        unset($_upd);
                    }
                    // Update existing entries with new default AND text
                    if (isset($_new) && count($_new) > 0) {
                        foreach ($_new as $key => $str) {
                            $req = "UPDATE {$tbl} SET lang_text = '" . jrCore_db_escape($str) . "', lang_default = '" . jrCore_db_escape($str) . "' WHERE lang_module = '{$mod}' AND lang_code = '{$cod}' AND lang_key = '{$key}'";
                            jrCore_db_query($req);
                        }
                        unset($_new);
                    }
                    // Save for below - cloned languages will not have a lang file
                    $_lng[$code] = jrCore_db_escape($code);
                }
            }
        }
        closedir($h);

        // Lastly, we now need to go through and update any CLONED languages
        // that might need to have new language strings inserted based on en-US
        if (count($_lng) > 0) {
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $req = "SELECT lang_code FROM {$tbl} WHERE lang_code NOT IN('" . implode("','", $_lng) . "') GROUP BY lang_code";
            $_cc = jrCore_db_query($req, 'lang_code');
            if ($_cc && is_array($_cc)) {
                $mod = jrCore_db_escape($dir);
                foreach ($_cc as $code => $ignore) {
                    // Make sure all lang strings for this module are setup in this language
                    $cod = jrCore_db_escape($code);
                    // First - get existing lang strings
                    $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '{$cod}'";
                    $_ns = jrCore_db_query($req, 'lang_key');
                    if ($_ns && is_array($_ns)) {
                        $ltr = 'ltr';
                        $_tm = reset($_ns);
                        if ($_tm) {
                            $ltr = $_tm['lang_ltr'];
                        }
                        // Next, get English lang strings
                        $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = 'en-US'";
                        $_es = jrCore_db_query($req, 'lang_key');
                        if ($_es && is_array($_es)) {
                            // Go through each English string and make sure it exists in the Cloned language
                            foreach ($_es as $lid => $_inf) {
                                if (!$_ns || !is_array($_ns) || !isset($_ns[$lid])) {
                                    $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES ('{$mod}','{$cod}','utf-8','{$ltr}','{$lid}','" . jrCore_db_escape($_inf['lang_text']) . "','" . jrCore_db_escape($_inf['lang_default']) . "')";
                                    jrCore_db_query($req);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return true;
}

/**
 * jrUser_get_languages()
 *
 * @return array Returns array of Languages
 */
function jrUser_get_languages()
{
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "SELECT lang_code FROM {$tbl} GROUP BY lang_code ORDER BY lang_code ASC";
    $_rt = jrCore_db_query($req, 'lang_code', false, 'lang_code');
    foreach ($_rt as $k => $v) {
        $_rt[$k] = jrUser_get_lang_name(substr($v, 0, 2)) . ' (' . $k . ')';
    }
    return $_rt;
}

/**
 * jrUser_brute_force_check
 * this function will check to ensure
 * a user account is not being brute force hacked, but making
 * the user wait longer and longer on each unsuccessful try.
 *
 * @param $user_id integer User ID to brute force check for
 *
 * @return bool
 */
function jrUser_brute_force_check($user_id)
{
    // see if this IP is nailing us
    $key = "jr_login_attempts_{$user_id}";
    $ula = jrCore_get_temp_value('jrUser', $key);
    if (isset($ula) && jrCore_checktype($ula, 'number_nz')) {
        // looks like they have already hit us - update db
        $new = ($ula + 1);
        jrCore_set_temp_value('jrUser', $key, $new);
        if ($new > 3) {
            sleep($new * 2);
        }
    }
    else {
        // First hit
        jrCore_set_temp_value('jrUser', $key, 1);
    }
    return true;
}

/**
 * Removes any brute force check entry in the temp table set by a user.
 * @param $user_id integer User ID
 * @return bool
 */
function jrUser_brute_force_cleanup($user_id)
{
    $key = "jr_login_attempts_{$user_id}";
    jrCore_delete_temp_value('jrUser', $key);
    return true;
}

/**
 * The jrUserUpdateLastLoginTime function is used to update the "Last Login"
 * time for a user when they log into Jamroom.
 * @param $user_id integer User_ID to update user_last_login time for
 * @return bool
 */
function jrUser_update_last_login_time($user_id)
{
    if (!isset($user_id) || !is_numeric($user_id)) {
        return false;
    }
    $tbl = jrCore_db_table_name('jrUser', 'user');
    $req = "UPDATE {$tbl} SET user_last_login = UNIX_TIMESTAMP() WHERE user_id = '{$user_id}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * List online users
 * @param $_post array Post Info
 * @param $_user array Viewing User Info
 * @param $_conf array Global Config
 * @return bool
 */
function jrUser_online_users($_post, $_user, $_conf)
{
    // Get all our users...
    $dif = 900;
    if (isset($_post['active_time']) && jrCore_checktype($_post['active_time'], 'number_nz')) {
        $dif = (int) $_post['active_time'];
    }
    $_rt = jrUser_session_online_user_info($dif, $_post['search_string']);

    // See we have a search condition
    $val = '';
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $val = $_post['search_string'];
    }
    $url = jrCore_get_module_url('jrCore');
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$url}/dashboard/online", $val);

    $dat             = array();
    $dat[1]['title'] = 'user';
    $dat[1]['width'] = '16%;';
    $dat[2]['title'] = 'group';
    $dat[2]['width'] = '10%;';
    $dat[3]['title'] = 'location';
    $dat[3]['width'] = '37%;';
    $dat[4]['title'] = 'ip';
    $dat[4]['width'] = '10%;';
    $dat[5]['title'] = 'updated';
    $dat[5]['width'] = '12%;';
    $dat[6]['title'] = 'log&nbsp;off';
    $dat[6]['width'] = '5%;';

    $burl = false;
    if (jrCore_module_is_active('jrBanned')) {
        $dat[7]['title'] = 'ban&nbsp;IP';
        $dat[7]['width'] = '2%;';
        $dat[8]['title'] = 'modify';
        $dat[8]['width'] = '3%;';
        $burl            = jrCore_get_module_url('jrBanned');
    }
    else {
        $dat[8]['title'] = 'modify';
        $dat[8]['width'] = '5%;';
    }
    jrCore_page_table_header($dat);

    $curl = jrCore_get_module_url('jrUser');
    $myip = jrCore_get_ip();
    //-------------------------------
    // USERS
    //-------------------------------

    foreach ($_rt as $k => $_us) {

        // Don't show AJAX actions
        if (strpos($_us['session_user_action'], '__ajax')) {
            continue;
        }
        if (!isset($_us['session_user_name']) || strlen($_us['session_user_name']) === 0 || strpos($_us['session_user_name'], 'bot:') === 0) {
            continue;
        }
        $dat = array();
        if (strpos($_us['session_user_action'], '?')) {
            $_us['session_user_action'] = substr($_us['session_user_action'], 0, strpos($_us['session_user_action'], '?'));
        }
        $dat[1]['title'] = $_us['session_user_name'];
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = $_us['session_user_group'];
        $dat[2]['class'] = 'center';
        $show            = htmlentities($_us['session_user_action'], ENT_QUOTES, 'UTF-8');
        if (strlen($show) > 60) {
            $show = implode('<br>', str_split($show, 60));
        }
        $dat[3]['title'] = $show;
        $dat[3]['style'] = 'overflow:scroll';
        $dat[4]['title'] = '<a href="http://whois.domaintools.com/' . $_us['session_user_ip'] . '" target="_blank">' . $_us['session_user_ip'] . '</a>';
        $dat[4]['class'] = 'center';
        $dat[5]['title'] = jrCore_format_time($_us['session_updated']);
        $dat[5]['class'] = 'center';

        // Show "Ban IP" button if jrBanned is installed
        if ($burl) {

            if ((isset($_us['session_user_group']) && $_us['session_user_group'] == 'master') || $_us['session_user_ip'] == $myip) {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', 'disabled');
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', 'disabled');
            }
            elseif (isset($_us['session_user_id']) && jrCore_checktype($_us['session_user_id'], 'number_nz')) {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/session_remove_save/{$_us['session_user_id']}')");
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
            else {
                $dat[6]['title'] = 'n/a';
                $dat[6]['class'] = 'center';
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
        }
        else {

            if (isset($_us['session_user_group']) && $_us['session_user_group'] == 'master') {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', 'disabled');
            }
            elseif (isset($_us['session_user_id']) && jrCore_checktype($_us['session_user_id'], 'number_nz')) {
                $dat[6]['title'] = jrCore_page_button("r{$k}", 'log off', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/session_remove_save/{$_us['session_user_id']}')");
            }
            else {
                $dat[6]['title'] = 'n/a';
                $dat[6]['class'] = 'center';
            }
        }
        $dat[8]['title'] = jrCore_page_button("m{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$curl}/account/profile_id={$_us['session_profile_id']}/user_id={$_us['session_user_id']}')");
        jrCore_page_table_row($dat);
    }

    //-------------------------------
    // VISITORS
    //-------------------------------
    foreach ($_rt as $k => $_us) {

        // Don't show AJAX actions
        if (strpos($_us['session_user_action'], '__ajax')) {
            continue;
        }
        if (isset($_us['session_user_name']) && strlen($_us['session_user_name']) > 0) {
            continue;
        }
        $dat             = array();
        $dat[1]['title'] = 'visitor';
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = '-';
        $dat[2]['class'] = 'center';
        $show            = htmlentities($_us['session_user_action'], ENT_QUOTES, 'UTF-8');
        if (strlen($show) > 60) {
            $show = implode('<br>', str_split($show, 60));
        }
        $dat[3]['title'] = $show;
        $dat[4]['title'] = '<a href="http://whois.domaintools.com/' . $_us['session_user_ip'] . '" target="_blank">' . $_us['session_user_ip'] . '</a>';
        $dat[4]['class'] = 'center';
        $dat[5]['title'] = jrCore_format_time($_us['session_updated']);
        $dat[5]['class'] = 'center';
        $dat[6]['title'] = 'n/a';
        $dat[6]['class'] = 'center';
        if ($burl) {
            // Show "Ban IP" button if jrBanned is installed
            if ($_us['session_user_ip'] == $myip) {
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', 'disabled');
            }
            else {
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
        }
        $dat[8]['title'] = '-';
        $dat[8]['class'] = 'center';
        jrCore_page_table_row($dat);
    }

    //-------------------------------
    // BOTS
    //-------------------------------
    if (isset($_post['show_bots']) && $_post['show_bots'] == '1') {
        foreach ($_rt as $k => $_us) {
            // Don't show AJAX actions
            if (strpos($_us['session_user_action'], '__ajax')) {
                continue;
            }
            if (!isset($_us['session_user_name']) || strpos($_us['session_user_name'], 'bot:') !== 0) {
                continue;
            }
            $dat             = array();
            $dat[1]['title'] = $_us['session_user_name'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = '-';
            $dat[2]['class'] = 'center';
            $show            = htmlentities($_us['session_user_action'], ENT_QUOTES, 'UTF-8');
            if (strlen($show) > 60) {
                $show = implode('<br>', str_split($show, 60));
            }
            $dat[3]['title'] = $show;
            $dat[4]['title'] = '<a href="http://whois.domaintools.com/' . $_us['session_user_ip'] . '" target="_blank">' . $_us['session_user_ip'] . '</a>';
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_format_time($_us['session_updated']);
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = 'n/a';
            $dat[6]['class'] = 'center';
            if ($burl) {
                // Show "Ban IP" button if jrBanned is installed
                $dat[7]['title'] = jrCore_page_button("b{$k}", 'ban IP', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$burl}/item_save/ban_type=ip/ban_value={$_us['session_user_ip']}')");
            }
            $dat[8]['title'] = '-';
            $dat[8]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }

    jrCore_page_table_footer();
}

//---------------------------------------------------------
// User Checks
//---------------------------------------------------------

/**
 * The jrUser_is_logged_in function will return true if a user is logged
 * in to Jamroom, and false if they are a viewer.
 *
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_logged_in()
{
    if (isset($_SESSION['_user_id']) && $_SESSION['_user_id'] > 0) {
        return true;
    }
    return false;
}

/**
 * The jrUser_is_master function will return true if the user
 * is the Master Admin
 *
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_master()
{
    if (jrUser_is_logged_in() && isset($_SESSION['user_group']) && $_SESSION['user_group'] == 'master') {
        return true;
    }
    return false;
}

/**
 * The jrUser_is_admin function will return true if a user is logged
 * in to Jamroom, and is an Admin User
 *
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_admin()
{
    if (jrUser_is_logged_in()) {
        switch ($_SESSION['user_group']) {
            case 'master':
            case 'admin':
                return true;
                break;
        }
    }
    return false;
}

/**
 * The jrUser_is_power_user function will return true if a user is logged
 * in to Jamroom, and is a Power User (more than 1 profile)
 *
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_power_user()
{
    if (jrUser_is_logged_in() && (jrUser_is_admin() || jrUser_get_profile_home_key('quota_jrUser_power_user') == 'on')) {
        return true;
    }
    return false;
}

/**
 * Check if user manages multiple profiles
 *
 * @return bool Returns true/false on success/fail
 */
function jrUser_is_multi_user()
{
    if (jrUser_is_logged_in() && (jrUser_is_admin() || (isset($_SESSION['user_linked_profile_ids']) && strpos($_SESSION['user_linked_profile_ids'], ',')))) {
        return true;
    }
    return false;
}

/**
 * jrUser_in_quota
 * Checks if the calling user is in the specified Quota
 * @param $quota_id integer Quota ID
 * @return bool
 */
function jrUser_in_quota($quota_id)
{
    if (jrUser_is_logged_in() && isset($_SESSION['profile_quota_id']) && intval($_SESSION['profile_quota_id']) === intval($quota_id)) {
        return true;
    }
    return false;
}

/**
 * The jrUser_master_only function is used to ensure access to a code section is
 * for Master Users only - any other access by anyone else will result
 * in a log message, and they will be logged out.
 *
 * @return null
 */
function jrUser_master_only()
{
    jrUser_session_require_login();
    if (isset($_SESSION['user_group']) && $_SESSION['user_group'] == 'master') {
        return true;
    }
    return jrUser_not_authorized();
}

/**
 * jrUser_admin_only
 * The jrUser_admin_only function is used to ensure access to a code section is
 * for Profile Admins only - any other access by anyone else will result in error
 *
 * @return null
 */
function jrUser_admin_only()
{
    jrUser_session_require_login();
    switch ($_SESSION['user_group']) {
        case 'master':
        case 'admin':
            return true;
            break;
    }
    return jrUser_not_authorized();
}

/**
 * jrUser_not_authorized
 * Exits with a "not authorized" message.
 *
 * @return null
 */
function jrUser_not_authorized()
{
    $_lang = jrUser_load_lang_strings();
    jrCore_notice_page('error', $_lang['jrCore'][41]);
}

/**
 * jrUser_is_profile_owner
 * Returns true of the viewing user is allowed to edit the viewed profile
 *
 * @param $profile_id integer Profile ID
 *
 * @return bool
 */
function jrUser_is_profile_owner($profile_id)
{
    global $_user;
    if (!isset($profile_id) || !jrCore_checktype($profile_id, 'number_nz')) {
        return false;
    }
    if (jrUser_is_admin() || (isset($_user['user_linked_profile_ids']) && in_array($profile_id, explode(',', $_user['user_linked_profile_ids'])))) {
        return true;
    }
    return false;
}

/**
 * jrUser_can_edit_item
 * Returns true/false if the current user has the proper credentials to edit the given item
 *
 * @param $_item array Array of Item information returned from jrCore_db_get_item()
 *
 * @return bool
 */
function jrUser_can_edit_item($_item)
{
    global $_user;
    if (jrUser_is_admin() || (isset($_user['user_linked_profile_ids']) && isset($_item['_profile_id']) && in_array($_item['_profile_id'], explode(',', $_user['user_linked_profile_ids'])))) {
        return true;
    }
    return false;
}

/**
 * jrUser_reset_cache
 * Reset cached pages for a specific user_id
 * @param $uid integer User ID to reset cached pages for
 * @return mixed
 */
function jrUser_reset_cache($uid)
{
    if (!jrCore_checktype($uid, 'number_nn')) {
        return false;
    }
    return jrCore_delete_all_cache_entries(null, $uid);
}

//---------------------------------------------------------
// User Session
//---------------------------------------------------------

/**
 * @ignore
 * jrCore_get_active_cache_system
 * @return string
 */
function jrUser_get_active_session_system()
{
    global $_conf;
    if (isset($_conf['jrUser_active_session_system']{1})) {
        // Make sure it is valid...
        $func = "_{$_conf['jrUser_active_session_system']}_session_open";
        if (function_exists($func)) {
            return $_conf['jrUser_active_session_system'];
        }
    }
    return 'jrUser_mysql';
}

/**
 * jrUser_ignore_action
 */
function jrUser_ignore_action()
{
    jrCore_set_flag('jruser_ignore_action', 1);
    return true;
}

/**
 * Used internally by Jamroom
 * @ignore
 */
function jrUser_unique_install_id()
{
    global $_conf;
    return substr(md5($_conf['jrCore_base_url']), 0, 12);
}

/**
 * Used internally by Jamroom
 * @ignore
 */
function jrUser_session_regenerate()
{
    if (!isset($_SESSION)) {
        // No session....
        return false;
    }
    session_regenerate_id(true);
    // TODO: This does not look right
    $new = session_id();
    session_write_close();
    session_id($new);
    session_name('sess' . jrUser_unique_install_id());
    session_start();
    return true;
}

/**
 * Initialize a Jamroom Session
 * @return true
 */
function jrUser_session_init()
{
    global $_conf;
    if (isset($_SESSION)) {
        // We already have a session up...
        return true;
    }
    // Set PHPs garbage collection higher than our own collection -
    // this prevents PHP from stepping in and messing with our sessions
    $exp = isset($_conf['jrUser_session_expire_min']) ? ($_conf['jrUser_session_expire_min'] * 60) : 7200;

    // Trigger event so add on modules can override this if needed
    $res = array('expire_length' => $exp);
    $res = jrCore_trigger_event('jrUser', 'session_init', $res, $res);
    if (!is_array($res)) {
        // Our session support was initialized by a listener
        return true;
    }

    ini_set('session.gc_maxlifetime', ($exp + 7200));
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    session_set_save_handler("_{$act}_session_open", "_{$act}_session_close", "_{$act}_session_read", "_{$act}_session_write", "_{$act}_session_destroy", "_{$act}_session_collect");
    session_name('sess' . jrUser_unique_install_id());
    session_start();
    return true;
}

/**
 * End a Jamroom session
 * @return bool Returns true
 */
function jrUser_session_destroy()
{
    jrUser_session_init();
    $_SESSION = array();
    @session_unset();
    @session_destroy();
    jrUser_session_delete_login_cookie();
    return true;
}

/**
 * Remove all sessions for a given User ID
 * @param $user_id mixed User ID or array of User IDs
 * @return bool
 */
function jrUser_session_remove($user_id)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_remove";
    if (function_exists($fnc)) {
        return $fnc($user_id);
    }
    return false;
}

/**
 * Return true if a session is online
 * @param $user_id mixed single user_id or array of user_id's
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @return mixed
 */
function jrUser_session_user_is_online($user_id, $length = 900)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_user_is_online";
    if (function_exists($fnc)) {
        return $fnc($user_id, $length);
    }
    return false;
}

/**
 * Get session information by SID
 * @param $sid string Active Session ID
 * @return bool
 */
function jrUser_session_is_valid_session($sid)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_is_valid_session";
    if (function_exists($fnc)) {
        return $fnc($sid);
    }
    return false;
}

/**
 * Get total number of online users
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @return int
 */
function jrUser_session_online_user_count($length = 900)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_online_user_count";
    if (function_exists($fnc)) {
        return $fnc($length);
    }
    return false;
}

/**
 * Get information about online users
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @param $search string Optional Search string
 * @return array
 */
function jrUser_session_online_user_info($length = 900, $search = null)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_online_user_info";
    if (function_exists($fnc)) {
        return $fnc($length, $search);
    }
    return false;
}

/**
 * Set the session_sync flag for a Quota or array of Quotas
 * @param $quota_id mixed Quota ID or array of Quota IDs to set session_sync flag for
 * @param $state string on|off
 * @return bool
 */
function jrUser_set_session_sync_for_quota($quota_id, $state)
{
    $act = jrUser_get_active_session_system();
    if (!$act) {
        $act = 'jrUser_mysql';
    }
    $fnc = "_{$act}_session_sync_for_quota";
    if (function_exists($fnc)) {
        return $fnc($quota_id, $state);
    }
    return false;
}

/**
 * The jrUser_session_sync function will "re-sync" an existing Jamroom Session with all
 * of the user, profile and quota information as it exists in the database - this needs
 * to be called if the database information is changed, and you need the user to re-sync
 * their session info.
 * @param int $user_id User to Synchronize session for
 * @return bool
 */
function jrUser_session_sync($user_id)
{
    // Get latest user info
    $_rt = jrCore_db_get_item('jrUser', $user_id, true, true);
    if (isset($_rt) && is_array($_rt)) {

        // Delete any cache
        jrUser_reset_cache($_SESSION['_user_id']);
        jrProfile_reset_cache($_SESSION['user_active_profile_id']);

        // Start with a clean session
        $_SESSION = array(
            'user_active_profile_id' => (int) $_SESSION['user_active_profile_id'],
            'profile_home_data'      => $_SESSION['profile_home_data']
        );

        $_tm = jrCore_db_get_item('jrProfile', $_SESSION['user_active_profile_id'], false, true);

        if (isset($_tm) && is_array($_tm)) {
            unset($_tm['_item_id']);
            $_rt = $_rt + $_tm;
        }

        // See what profiles we link to
        $_pn = jrProfile_get_user_linked_profiles($user_id);
        if ($_pn && is_array($_pn)) {
            $_rt['user_linked_profile_ids'] = implode(',', array_keys($_pn));
        }

        foreach ($_rt as $key => $val) {
            $_SESSION[$key] = $val;
        }
        $ckey = "{$user_id}-{$_SESSION['user_active_profile_id']}-" . session_id();
        jrCore_add_to_cache('jrUser', $ckey, $_rt);

        // We also need to reset our skin_cache menu here...
        $ckey = "skin_menu_{$user_id}";
        jrCore_delete_cache('jrCore', $ckey);
    }
    return true;
}

/**
 * The jrUser_session_require_login function will redirect a user to the /user/login
 * page if they are not logged in.
 * @return mixed returns bool true if user is logged on, redirects to login page if not
 */
function jrUser_session_require_login()
{
    global $_conf;
    if (!jrUser_is_logged_in()) {

        jrUser_session_init();
        // Save where they were trying to go before they needed to log in so we
        // can send them back to that location once logged in.
        jrUser_save_location();

        // Redirect them to login
        $_ln = jrUser_load_lang_strings();
        jrCore_set_form_notice('error', $_ln['jrUser'][108]);
        $url = jrCore_get_module_url('jrUser');
        jrCore_location("{$_conf['jrCore_base_url']}/{$url}/login?r=1");
    }
    return true;
}

/**
 * Get Name of unique auto login cookie
 * @return string
 */
function jrUser_session_get_login_cookie_name()
{
    return 'auto' . jrUser_unique_install_id();
}

/**
 * Sets a Browser "Remember Me" Login cookie
 * @param $user_id integer User ID to set cookie for
 * @param $cookie_id integer If given $cookie_id will be updated with value
 * @return bool
 */
function jrUser_session_set_login_cookie($user_id, $cookie_id = 0)
{
    global $_conf;
    if (!isset($user_id) || !jrCore_checktype($user_id, 'number_nz')) {
        return false;
    }
    $val = md5(microtime());
    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    if ($cookie_id === 0) {
        $req = "INSERT INTO {$tbl} (cookie_user_id,cookie_time,cookie_value) VALUES ('{$user_id}',UNIX_TIMESTAMP(),'" . jrCore_db_escape(sha1($val)) . "')";
    }
    else {
        $req = "UPDATE {$tbl} SET cookie_time = UNIX_TIMESTAMP(), cookie_value = '" . jrCore_db_escape(sha1($val)) . "' WHERE cookie_id = " . intval($cookie_id);
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_logger('CRI', "jrUser_session_set_login_cookie() unable to set autologin cookie for user_id: {$user_id} - check error log");
    }
    else {
        // Create new cookie
        $tim = (14 * 86400); // Default: 14 days
        $cid = jrUser_session_get_login_cookie_name();
        if (isset($_conf['jrUser_autologin']) && jrCore_checktype($_conf['jrUser_autologin'], 'number_nz')) {
            switch (intval($_conf['jrUser_autologin'])) {
                case 7:
                case 30:
                case 60:
                case 90:
                    $tim = (intval($_conf['jrUser_autologin']) * 86400);
                    break;
            }
        }
        setcookie($cid, "{$user_id}-{$val}", time() + $tim, '/');
        $_COOKIE[$cid] = "{$user_id}-{$val}";
    }
    return true;
}

/**
 * Delete an auto-login cookie
 * @param string $cid unique cookie ID
 * @return bool
 */
function jrUser_session_delete_login_cookie($cid = null)
{
    if (is_null($cid)) {
        $cid = jrUser_session_get_login_cookie_name();
    }
    if (isset($_COOKIE[$cid])) {

        // DB Cleanup
        $tbl = jrCore_db_table_name('jrUser', 'cookie');
        $req = "DELETE FROM {$tbl} WHERE cookie_value = '" . jrCore_db_escape($_COOKIE[$cid]) ."'";
        jrCore_db_query($req);

        // Remove actual cookie
        setcookie($cid, '', time() - 8640000, '/');
        unset($_COOKIE[$cid]);
    }
    return true;
}

/**
 * Get a User-ID from a valid auto-login cookie
 * @see http://fishbowl.pastiche.org/2004/01/19/persistent_login_cookie_best_practice/
 * @return bool|int
 */
function jrUser_get_user_id_from_login_cookie()
{
    // Check for "remember me" cookie being set on login.
    $cid = jrUser_session_get_login_cookie_name();
    if (!isset($_COOKIE[$cid]{0}) || !strpos($_COOKIE[$cid], '-')) {
        return false;
    }
    // Looks like we have an auto login cookie - process
    list($user_id, $hash_id) = explode('-', $_COOKIE[$cid], 2);
    if (!$user_id || !jrCore_checktype($user_id, 'number_nz')) {
        // Bad request - although since we don't know the
        // user ID, we can't do a cleanup - remove cookie
        jrUser_session_delete_login_cookie($cid);
        return false;
    }
    // Make sure we get a valid MD5
    // NOTE: Our cookie value comes in as an MD5
    if (!isset($hash_id{30}) || !jrCore_checktype($hash_id, 'md5')) {
        jrUser_session_delete_login_cookie($cid);
        return false;
    }
    // Check for expired cookie
    $val = (14 * 86400); // Default: 14 days
    if (isset($_conf['jrUser_autologin']) && jrCore_checktype($_conf['jrUser_autologin'], 'number_nz')) {
        switch (intval($_conf['jrUser_autologin'])) {
            case 7:
            case 30:
            case 60:
            case 90:
                $val = (intval($_conf['jrUser_autologin']) * 86400);
                break;
        }
    }
    $tbl = jrCore_db_table_name('jrUser', 'cookie');
    $req = "SELECT cookie_id, cookie_time FROM {$tbl} WHERE cookie_user_id = '{$user_id}' AND cookie_value = '" . jrCore_db_escape(sha1($hash_id)) . "' AND cookie_time > (UNIX_TIMESTAMP() - {$val})";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        // Not found or expired
        jrUser_session_delete_login_cookie($cid);
        return false;
    }
    // Looks good - let's update this current cookie with a new value
    if (jrUser_session_set_login_cookie($user_id, $_rt['cookie_id'])) {
        return intval($user_id);
    }
    return false;
}

/**
 * Start a User Session
 * @param $option_check bool set to false to skip the option check
 * @return array Returns array of user information on success
 */
function jrUser_session_start($option_check = true)
{
    global $_post, $_conf;

    // Some "options" have no need for a full session load - we can
    // check for those here and just return true.
    if ($option_check && isset($_post['option']) && strlen($_post['option']) > 0) {
        $_tmp = jrCore_get_registered_module_features('jrUser', 'skip_session');
        if (is_array($_tmp)) {
            // Quick check for exact option
            if (isset($_tmp["{$_post['module']}"]["{$_post['option']}"])) {
                return true;
            }
            // Fall through for magic view check
            foreach ($_tmp as $mod => $_opts) {
                if (isset($_opts["{$_post['option']}"]) && $_opts["{$_post['option']}"] === 'magic_view') {
                    return true;
                }
            }
        }
    }

    jrUser_session_init();

    // If we are requiring a login, check
    $trigger = false;
    $sess_id = false;
    if (!jrUser_is_logged_in()) {

        if ($sess_id = jrUser_get_user_id_from_login_cookie()) {
            // Update last login time for user
            $_dt = array('user_last_login' => time());
            jrCore_db_update_item('jrUser', $sess_id, $_dt);
            $trigger = true;
        }
    }

    if (jrUser_is_logged_in() || is_numeric($sess_id)) {

        $uid = (is_numeric($sess_id)) ? $sess_id : $_SESSION['_user_id'];

        // See if we have cached this user's info
        $ckey = false;
        $_tmp = false;
        if (isset($_SESSION['user_active_profile_id'])) {
            $ckey = "{$uid}-{$_SESSION['user_active_profile_id']}-" . session_id();
            $_tmp = jrCore_is_cached('jrUser', $ckey);
        }

        // We need to get this user's info + the profile info for their active profile ID
        if (!$_tmp || !is_array($_tmp)) {
            $_rt = jrCore_db_get_item('jrUser', $uid, true);
            if ($_rt && is_array($_rt)) {

                $_SESSION['is_logged_in'] = 'yes';
                if (!isset($_SESSION['user_active_profile_id']) && isset($_rt['_profile_id'])) {
                    $_SESSION['user_active_profile_id'] = $_rt['_profile_id'];
                }

                $_tm = jrCore_db_get_item('jrProfile', $_SESSION['user_active_profile_id']);
                if ($_tm && is_array($_tm)) {
                    unset($_tm['_item_id']);
                    $_rt = $_rt + $_tm;
                }

                // See what profiles we link to
                $_pn = jrProfile_get_user_linked_profiles($uid);
                if ($_pn && is_array($_pn)) {
                    $_rt['user_linked_profile_ids'] = implode(',', array_keys($_pn));
                }
                foreach ($_rt as $key => $val) {
                    $_SESSION[$key] = $val;
                }
                jrCore_add_to_cache('jrUser', $ckey, $_rt);

                // Save home profile keys
                jrUser_save_profile_home_keys($_SESSION);

                // See if this is an auto login (from cookie above)
                if ($trigger) {
                    $_SESSION = jrCore_trigger_event('jrUser', 'login_success', $_SESSION);
                }
            }
            else {
                // Bad user account
                jrUser_session_destroy();
            }
        }
        else {
            $_SESSION['is_logged_in'] = 'yes';
            foreach ($_tmp as $key => $val) {
                $_SESSION[$key] = $val;
            }
        }
    }

    if (!isset($_SESSION['is_logged_in'])) {
        // Defaults for users that are not logged in
        $_SESSION['quota_id']      = 0;
        $_SESSION['user_language'] = $_conf['jrUser_default_language'];
        $_SESSION['is_logged_in']  = 'no';
    }

    // Trigger session started event
    return jrCore_trigger_event('jrUser', 'session_started', $_SESSION);
}

/**
 * Get a specific HOME PROFILE Key for a user
 * @param $key string Key to return value for
 * @return mixed string|bool
 */
function jrUser_get_profile_home_key($key)
{
    if (isset($_SESSION['profile_home_data']{0})) {
        $_tmp = json_decode($_SESSION['profile_home_data'], true);
        if (isset($_tmp[$key])) {
            return $_tmp[$key];
        }
    }
    return false;
}

/*
 * Saves profile_home keys to user session
 * @param array $_data User Information to use for HOME profile info
 * @param bool $reload Force reload of profile home keys
 */
function jrUser_save_profile_home_keys($_data, $reload = false)
{
    // Save off our HOME PROFILE info - only happens on first login/access
    // of a session and remains throughout session
    if ((!isset($_SESSION['profile_home_data']) && isset($_data['profile_name'])) || $reload) {
        // There are some home fields that can be very
        // large we are going to unset here
        unset($_data['profile_bio']);
        $_tmp = array();
        foreach ($_data as $k => $v) {
            if (strtolower($k) === $k && strpos($k, 'profile_') === 0 || strpos($k, 'quota_') === 0 || $k == '_profile_id') {
                $_tmp[$k] = $v;
            }
        }
        $_SESSION['profile_home_data'] = json_encode($_tmp);
    }
    return true;
}

/**
 * jrUser_save_location
 * Save the current location as a saved location
 */
function jrUser_save_location()
{
    // We never "save" an AJAX request, since that does not have a view
    if (!jrCore_is_ajax_request()) {
        $_SESSION['jruser_save_location'] = jrCore_get_current_url();
    }
    return true;
}

/**
 * jrUser_get_saved_location
 * Returns a saved location if one is set
 */
function jrUser_get_saved_location()
{
    if (isset($_SESSION['jruser_save_location']{1})) {
        $url = $_SESSION['jruser_save_location'];
        unset($_SESSION['jruser_save_location']);
        return $url;
    }
    return false;
}

/**
 * Save URL location to temp table for a user
 * @param null $url string URL to save
 * @return bool|mixed
 */
function jrUser_save_url_location($url = null)
{
    global $_user;
    if (!jrUser_is_logged_in()) {
        return false;
    }
    if (is_null($url)) {
        $url = jrCore_get_current_url();
    }
    $uid = (int) $_user['_user_id'];
    $url = jrCore_db_escape($url);
    $tbl = jrCore_db_table_name('jrUser', 'url');
    $req = "INSERT INTO {$tbl} (user_id, user_url) VALUES ('{$uid}', '{$url}') ON DUPLICATE KEY UPDATE user_url = '{$url}'";
    return jrCore_db_query($req, 'COUNT', false, null, false);
}

/**
 * Get a previously saved URL location for a user
 * @param int $user_id User ID to get URL for
 * @return bool|mixed
 */
function jrUser_get_saved_url_location($user_id = 0)
{
    global $_user;
    if (!jrUser_is_logged_in()) {
        return false;
    }
    if ($user_id === 0 || !jrCore_checktype($user_id, 'number_nz')) {
        $user_id = (int) $_user['_user_id'];
    }
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrUser', 'url');
    $req = "SELECT user_url FROM {$tbl} WHERE user_id = '{$uid}'";
    $_rt = jrCore_db_query($req, 'SINGLE', false, null, false);
    if ($_rt && isset($_rt['user_url'])) {
        return $_rt['user_url'];
    }
    return false;
}

/**
 * Delete a previously saved URL location for a user
 * @param int $user_id User ID to get URL for
 * @return bool|mixed
 */
function jrUser_delete_saved_url_location($user_id = 0)
{
    global $_user;
    if (!jrUser_is_logged_in()) {
        return false;
    }
    if ($user_id === 0 || !jrCore_checktype($user_id, 'number_nz')) {
        $user_id = (int) $_user['_user_id'];
    }
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrUser', 'url');
    $req = "DELETE FROM {$tbl} WHERE user_id = '{$uid}'";
    return jrCore_db_query($req, 'COUNT', false, null, false);
}

/**
 * Returns the Language Name for a given ISO-639-1 Code
 * @param string $code Language code to return name of
 * @return string
 */
function jrUser_get_lang_name($code)
{
    $_codes = array(
        "aa" => "Afar",
        "ab" => "Abkhazian",
        "ae" => "Avestan",
        "af" => "Afrikaans",
        "ak" => "Akan",
        "am" => "Amharic",
        "an" => "Aragonese",
        "ar" => "Arabic",
        "as" => "Assamese",
        "av" => "Avaric",
        "ay" => "Aymara",
        "az" => "Azerbaijani",
        "ba" => "Bashkir",
        "be" => "Belarusian",
        "bg" => "Bulgarian",
        "bh" => "Bihari",
        "bi" => "Bislama",
        "bm" => "Bambara",
        "bn" => "Bengali",
        "bo" => "Tibetan",
        "br" => "Breton",
        "bs" => "Bosnian",
        "ca" => "Catalan",
        "ce" => "Chechen",
        "ch" => "Chamorro",
        "co" => "Corsican",
        "cr" => "Cree",
        "cs" => "Czech",
        "cu" => "Church Slavic",
        "cv" => "Chuvash",
        "cy" => "Welsh",
        "da" => "Danish",
        "de" => "German",
        "dv" => "Divehi",
        "dz" => "Dzongkha",
        "ee" => "Ewe",
        "el" => "Greek",
        "en" => "English",
        "eo" => "Esperanto",
        "es" => "Spanish",
        "et" => "Estonian",
        "eu" => "Basque",
        "fa" => "Persian",
        "ff" => "Fulah",
        "fi" => "Finnish",
        "fj" => "Fijian",
        "fo" => "Faroese",
        "fr" => "French",
        "fy" => "Western Frisian",
        "ga" => "Irish",
        "gd" => "Scottish Gaelic",
        "gl" => "Galician",
        "gn" => "Guarani",
        "gu" => "Gujarati",
        "gv" => "Manx",
        "ha" => "Hausa",
        "he" => "Hebrew",
        "hi" => "Hindi",
        "ho" => "Hiri Motu",
        "hr" => "Croatian",
        "ht" => "Haitian",
        "hu" => "Hungarian",
        "hy" => "Armenian",
        "hz" => "Herero",
        "ia" => "Interlingua (International Auxiliary Language Association)",
        "id" => "Indonesian",
        "ie" => "Interlingue",
        "ig" => "Igbo",
        "ii" => "Sichuan Yi",
        "ik" => "Inupiaq",
        "io" => "Ido",
        "is" => "Icelandic",
        "it" => "Italian",
        "iu" => "Inuktitut",
        "ja" => "Japanese",
        "jv" => "Javanese",
        "ka" => "Georgian",
        "kg" => "Kongo",
        "ki" => "Kikuyu",
        "kj" => "Kwanyama",
        "kk" => "Kazakh",
        "kl" => "Kalaallisut",
        "km" => "Khmer",
        "kn" => "Kannada",
        "ko" => "Korean",
        "kr" => "Kanuri",
        "ks" => "Kashmiri",
        "ku" => "Kurdish",
        "kv" => "Komi",
        "kw" => "Cornish",
        "ky" => "Kirghiz",
        "la" => "Latin",
        "lb" => "Luxembourgish",
        "lg" => "Ganda",
        "li" => "Limburgish",
        "ln" => "Lingala",
        "lo" => "Lao",
        "lt" => "Lithuanian",
        "lu" => "Luba-Katanga",
        "lv" => "Latvian",
        "mg" => "Malagasy",
        "mh" => "Marshallese",
        "mi" => "Maori",
        "mk" => "Macedonian",
        "ml" => "Malayalam",
        "mn" => "Mongolian",
        "mr" => "Marathi",
        "ms" => "Malay",
        "mt" => "Maltese",
        "my" => "Burmese",
        "na" => "Nauru",
        "nb" => "Norwegian Bokmal",
        "nd" => "North Ndebele",
        "ne" => "Nepali",
        "ng" => "Ndonga",
        "nl" => "Dutch",
        "nn" => "Norwegian Nynorsk",
        "no" => "Norwegian",
        "nr" => "South Ndebele",
        "nv" => "Navajo",
        "ny" => "Chichewa",
        "oc" => "Occitan",
        "oj" => "Ojibwa",
        "om" => "Oromo",
        "or" => "Oriya",
        "os" => "Ossetian",
        "pa" => "Panjabi",
        "pi" => "Pali",
        "pl" => "Polish",
        "ps" => "Pashto",
        "pt" => "Portuguese",
        "qu" => "Quechua",
        "rm" => "Raeto-Romance",
        "rn" => "Kirundi",
        "ro" => "Romanian",
        "ru" => "Russian",
        "rw" => "Kinyarwanda",
        "sa" => "Sanskrit",
        "sc" => "Sardinian",
        "sd" => "Sindhi",
        "se" => "Northern Sami",
        "sg" => "Sango",
        "si" => "Sinhala",
        "sk" => "Slovak",
        "sl" => "Slovenian",
        "sm" => "Samoan",
        "sn" => "Shona",
        "so" => "Somali",
        "sq" => "Albanian",
        "sr" => "Serbian",
        "ss" => "Swati",
        "st" => "Southern Sotho",
        "su" => "Sundanese",
        "sv" => "Swedish",
        "sw" => "Swahili",
        "ta" => "Tamil",
        "te" => "Telugu",
        "tg" => "Tajik",
        "th" => "Thai",
        "ti" => "Tigrinya",
        "tk" => "Turkmen",
        "tl" => "Tagalog",
        "tn" => "Tswana",
        "to" => "Tonga",
        "tr" => "Turkish",
        "ts" => "Tsonga",
        "tt" => "Tatar",
        "tw" => "Twi",
        "ty" => "Tahitian",
        "ug" => "Uighur",
        "uk" => "Ukrainian",
        "ur" => "Urdu",
        "uz" => "Uzbek",
        "ve" => "Venda",
        "vi" => "Vietnamese",
        "vo" => "Volapuk",
        "wa" => "Walloon",
        "wo" => "Wolof",
        "xh" => "Xhosa",
        "yi" => "Yiddish",
        "yo" => "Yoruba",
        "za" => "Zhuang",
        "zh" => "Chinese",
        "zu" => "Zulu"
    );
    if (isset($_codes[$code])) {
        return $_codes[$code];
    }
    return $code;
}

/**
 * Returns name of common web bots
 * @return bool|string
 */
function jrUser_get_bot_name()
{
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return '';
    }
    $_to_check = array(
        'googlebot'    => 'google',
        'adsbot'       => 'google',
        'facebook'     => 'facebook',
        'pingdom'      => 'pingdom',
        'feedburner'   => 'feedburner',
        'msnbot'       => 'bing',
        'livebot'      => 'bing',
        'bingbot'      => 'bing',
        'jeeves'       => 'ask.com',
        'baiduspider'  => 'baidu',
        'ccbot'        => 'common crawl',
        'ahrefsbot'    => 'ahref',
        'yandexbot'    => 'yandex',
        'yandeximages' => 'yandex',
        'spinn3r'      => 'spinn3r',
        'twitterbot'   => 'twitter',
        'exabot'       => 'exalead',
        'gigabot'      => 'gigablast'
    );
    foreach ($_to_check as $bot => $name) {
        if (stripos(' ' . $_SERVER['HTTP_USER_AGENT'], $bot)) {
            return "bot: {$name}";
        }
        elseif (stripos(' ' . $_SERVER['HTTP_USER_AGENT'], 'bot')) {
            return "bot: other";
        }
    }
    return '';
}

//--------------------------------------------------------
// MySQL Session Handler replacement
//--------------------------------------------------------

/**
 * @ignore
 * Open a MySQL Session
 * @param $path string
 * @param $name string
 * @return bool
 */
function _jrUser_mysql_session_open($path, $name)
{
    return true;
}

/**
 * @ignore
 * Close a MySQL Session
 * @return bool
 */
function _jrUser_mysql_session_close()
{
    return true;
}

/**
 * @ignore
 * Read an active MySQL Session
 * @param $sid String Current Session ID
 * @return string
 */
function _jrUser_mysql_session_read($sid)
{
    global $_conf;
    $exp = ($_conf['jrUser_session_expire_min'] * 60);
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_updated, session_data FROM {$tbl} WHERE session_id = '" . jrCore_db_escape($sid) . "' AND session_updated > (UNIX_TIMESTAMP() - {$exp})";
    $_rt = jrCore_db_query($req, 'SINGLE');
    return ($_rt && isset($_rt['session_data']{0})) ? $_rt['session_data'] : '';
}

/**
 * @ignore
 * Write an existing User session
 * @param $sid string Session ID to write to
 * @param $val string Session Value to save (set automatically by PHP)
 * @return bool
 */
function _jrUser_mysql_session_write($sid, $val)
{
    global $_post;
    // check out _user_id
    if (!isset($_SESSION['_user_id']) || !is_numeric($_SESSION['_user_id'])) {
        $_SESSION['_user_id'] = '0';
    }
    if (!isset($_SESSION['_profile_id']) || !is_numeric($_SESSION['_profile_id'])) {
        $_SESSION['_profile_id'] = '0';
    }
    // Check for user action for Who is Online...
    $ad1 = '';
    $ad2 = '';
    $ad3 = '';
    $act = jrCore_get_flag('jruser_ignore_action');
    if (!$act) {
        if (!isset($_post['_uri']) || strlen($_post['_uri']) === 0) {
            $_post['_uri'] = '/';
        }
        if (!strpos($_post['_uri'], '__ajax') && !strpos($_post['_uri'], 'icon_css') && !strpos($_post['_uri'], '/image/')) {
            $act = jrCore_db_escape(substr($_post['_uri'], 0, 255));
            $ad1 = ',session_user_action';
            $ad2 = ",'{$act}'";
            $ad3 = ",session_user_action = '{$act}'";
        }
    }
    $qid = (isset($_SESSION['profile_quota_id']) && is_numeric($_SESSION['profile_quota_id'])) ? intval($_SESSION['profile_quota_id']) : 0;
    $val = jrCore_db_escape($val);
    $uip = jrCore_db_escape(jrCore_get_ip());
    $nam = (isset($_SESSION['user_name']) && strlen($_SESSION['user_name']) > 0) ? jrCore_db_escape($_SESSION['user_name']) : jrCore_db_escape(jrUser_get_bot_name());
    $grp = (isset($_SESSION['user_group'])) ? jrCore_db_escape($_SESSION['user_group']) : '';
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "INSERT INTO {$tbl} (session_id,session_updated,session_user_id,session_user_name,session_user_group,session_profile_id,session_quota_id,session_user_ip{$ad1},session_data)
            VALUES ('{$sid}',UNIX_TIMESTAMP(),'{$_SESSION['_user_id']}','{$nam}','{$grp}','{$_SESSION['_profile_id']}','{$qid}','{$uip}'{$ad2},'{$val}')
            ON DUPLICATE KEY UPDATE session_updated = UNIX_TIMESTAMP(),session_user_id = '{$_SESSION['_user_id']}',session_user_name = '{$nam}',session_user_group = '{$grp}',
            session_profile_id = '{$_SESSION['_profile_id']}',session_quota_id = '{$qid}',session_user_ip = '{$uip}'{$ad3},session_data = '{$val}'";
    jrCore_db_query($req);
    return true;
}

/**
 * @ignore
 * Destroy an active session
 * @param $sid string SessionID
 * @return resource
 */
function _jrUser_mysql_session_destroy($sid)
{
    // Session table cleanup
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $exp = isset($_conf['jrUser_session_expire_min']) ? ($_conf['jrUser_session_expire_min'] * 60) : 7200;
    $sid = jrCore_db_escape($sid);
    $req = "DELETE FROM {$tbl} WHERE session_id = '{$sid}' OR session_updated < (UNIX_TIMESTAMP() - {$exp})";
    jrCore_db_query($req);
    return true;
}

/**
 * @ignore
 * Garbage collection for sessions
 * @param $max integer length of time session can be valid for
 * @return bool
 */
function _jrUser_mysql_session_collect($max)
{
    // Note - GC handled in JR process exit listener
    return true;
}

/**
 * @ignore
 * Remove all sessions for a specific user id
 * @param $user_id mixed User ID or array of User ID's
 * @return mixed
 */
function _jrUser_mysql_session_remove($user_id)
{
    // Remove all session entries for a user id
    $tbl = jrCore_db_table_name('jrUser', 'session');
    if (!is_array($user_id)) {
        $user_id = array(intval($user_id));
    }
    else {
        foreach ($user_id as $k => $uid) {
            if (!jrCore_checktype($uid, 'number_nz')) {
                unset($user_id[$k]);
            }
        }
        if (count($user_id) === 0) {
            return false;
        }
    }
    $req = "DELETE FROM {$tbl} WHERE session_user_id IN(" . implode(',', $user_id) . ')';
    return jrCore_db_query($req, 'COUNT');
}

/**
 * @ignore
 * Check if a given Session ID is a valid Session ID
 * @param $sid string Session ID
 * @return mixed
 */
function _jrUser_mysql_session_is_valid_session($sid)
{
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_id FROM {$tbl} WHERE session_id = '" . jrCore_db_escape($sid) ."' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return $_rt['session_id'];
    }
    return false;
}

/**
 * Set the session_sync flag for a Quota ID
 * @param $quota_id mixed Quota ID or array of Quota IDs
 * @param $state string on|off
 * @return mixed
 */
function _jrUser_mysql_session_sync_for_quota($quota_id, $state)
{
    $flg = ($state == 'on') ? 1 : 0;
    $_qi = array();
    if (jrCore_checktype($quota_id, 'number_nz')) {
        $_qi[] = (int) $quota_id;
    }
    elseif (is_array($quota_id)) {
        foreach ($quota_id as $qid) {
            if (jrCore_checktype($qid, 'number_nz')) {
                $_qi[] = (int) $qid;
            }
        }
    }
    if (count($_qi) === 0) {
        return false;
    }
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "UPDATE {$tbl} SET session_sync = {$flg} WHERE session_quota_id IN(" . implode(',', $_qi) . ')';
    return jrCore_db_query($req);
}

/**
 * @ignore
 * Get number of active online users
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @return int
 */
function _jrUser_mysql_session_online_user_count($length)
{
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT COUNT(DISTINCT(session_user_ip)) AS online FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ") AND session_user_name NOT LIKE 'bot:%'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['online'])) {
        return intval($_rt['online']);
    }
    return 0;
}

/**
 * @ignore
 * Return information about users that are active and online
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @param $search string Optional Search String
 * @return array
 */
function _jrUser_mysql_session_online_user_info($length = 900, $search = null)
{
    $tbl = jrCore_db_table_name('jrUser', 'session');
    if (!is_null($search) && strlen($search) > 0) {
        $req = "SELECT session_id, session_updated, session_user_id, session_user_name, session_user_group, session_profile_id, session_quota_id, session_user_ip, session_user_action
                  FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ")
                   AND session_user_name LIKE '%" . jrCore_db_escape($search) . "%' ORDER BY session_updated DESC";
    }
    else {
        $req = "SELECT session_id, session_updated, session_user_id, session_user_name, session_user_group, session_profile_id, session_quota_id, session_user_ip, session_user_action, CONCAT(session_user_name, session_user_ip) AS gb
                  FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - " . intval($length) . ")
                 GROUP BY gb ORDER BY session_updated DESC, session_user_id DESC";
    }
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * @ignore
 * Check if a given user_id or array of users has an active session
 * @param $user_id mixed User ID or array of User ID's
 * @param $length int Max number of seconds with no activity a session is considered "active"
 * @return mixed array on success, bool false on no users online
 */
function _jrUser_mysql_session_user_is_online($user_id, $length = 900)
{
    if (jrCore_checktype($user_id, 'number_nz')) {
        $user_id = array($user_id);
    }
    elseif (!is_array($user_id)) {
        return false;
    }
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT session_user_id, session_user_name, session_user_group, session_profile_id, session_quota_id, session_user_action
              FROM {$tbl} WHERE session_user_id IN (" . implode(',', $user_id) . ") AND session_updated >= (UNIX_TIMESTAMP() - " . intval($length) . ')';
    $_rt = jrCore_db_query($req, 'session_user_id');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

//--------------------------------------------
// SMARTY functions
//--------------------------------------------

/**
 * Get a Key from a user's home profile
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrUser_home_profile_key($params, $smarty)
{
    if (!isset($params['key']{0})) {
        return '';
    }
    $tmp = jrUser_get_profile_home_key($params['key']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}

/**
 * Get User's Online Status
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrUser_online_status($params, $smarty)
{
    global $_conf;
    if (isset($params['user_id']) && strlen($params['user_id']) > 0) {
        $type = 'user';
        if (strpos(' ' . $params['user_id'], ',')) {
            $_us = explode(',', $params['user_id']);
            foreach ($_us as $k => $uid) {
                if (!jrCore_checktype($uid, 'number_nz')) {
                    unset($_us[$k]);
                }
            }
        }
        else {
            $_us = array($params['user_id']);
        }
        if (count($_us) === 0) {
            return 'jrUser_online_status: invalid user_id parameter';
        }
        $osid = implode(',', $_us);
    }
    elseif (isset($params['profile_id']) && jrCore_checktype($params['profile_id'], 'number_nz')) {
        $type = 'profile';
        $osid = (int) $params['profile_id'];
    }
    else {
        return 'jrUser_online_status: user_id or profile_id parameter required';
    }
    if (isset($params['template']{0}) && !strpos($params['template'], '.tpl')) {
        $cdr = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
        $md5 = md5($params['template']);
        if (!is_file("{$cdr}/{$md5}.tpl")) {
            jrCore_write_to_file("{$cdr}/{$md5}.tpl", $params['template']);
        }
        $params['template'] = $md5;
    }
    else {
        $params['template'] = 'default';
    }
    $_rp            = array(
        'type'      => $type,
        'unique_id' => $osid,
        'template'  => $params['template'],
        'id'        => 'u' . substr(md5(microtime()), 0, 6)
    );
    $_rp['seconds'] = 900; // 15 minutes default
    if (isset($params['seconds']) && jrCore_checktype($params['seconds'], 'number_nz')) {
        $_rp['seconds'] = (int) $params['seconds'];
    }
    $tmp = jrCore_parse_template('online_status.tpl', $_rp, 'jrUser');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}

/**
 * Show Users Online
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrUser_whos_online($params, $smarty)
{
    if (!isset($params['template'])) {
        return 'jrUser_whos_online: template parameter required';
    }

    // Check for cache
    $key = json_encode($params);
    if ($tmp = jrCore_is_cached('jrUser', $key)) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $tmp);
            return '';
        }
        return $tmp . "\n<!--c-->";
    }

    // Initialize counters
    $_rp = array(
        'all_count'           => 0,
        'visitor_count'       => 0,
        'user_count'          => 0,
        'admin_count'         => 0,
        'master_count'        => 0,
        'logged_in_count'     => 0,
        'not_logged_in_count' => 0
    );

    // Get active session users
    $tmp = '';
    $tim = 900; // default to active in last 15 minutes
    if (isset($params['length']) && jrCore_checktype($params['length'], 'number_nz')) {
        $tim = intval($params['length'] * 60); //  override (in minutes)
    }
    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "SELECT CONCAT_WS('_',session_user_ip,session_user_group) AS session_uniq, session_updated, session_user_id, session_quota_id, session_user_action FROM {$tbl} WHERE session_updated > (UNIX_TIMESTAMP() - {$tim})";
    if (isset($params['quota_id']) && jrCore_checktype($params['quota_id'], 'number_nz')) {
        $req .= " AND session_quota_id = '" . intval($params['quota_id']) . "'";
    }
    $req .= " GROUP BY session_uniq ORDER BY session_updated DESC";

    $_su = jrCore_db_query($req, 'NUMERIC');
    if (isset($_su) && is_array($_su)) {
        $_id = array();
        foreach ($_su as $_session) {
            if ($_session['session_user_id'] > 0) {
                $_id["{$_session['session_user_id']}"] = $_session;
            }
            else {
                if (!isset($_rp['visitor'])) {
                    $_rp['visitor'] = array();
                }
                $_rp['visitor_count']++;
                $_rp['not_logged_in_count']++;
                $_rp['visitor'][] = $_session;
            }
        }
        if (count($_id) > 0) {
            $_sp = array(
                'search'      => array(
                    '_user_id in ' . implode(',', array_keys($_id)),
                ),
                'return_keys' => array('_created', '_updated', '_profile_id', '_user_id', 'user_name', 'user_language', 'user_group'),
                'limit'       => 10000
            );
            $_su = jrCore_db_search_items('jrUser', $_sp);
            if (isset($_su) && isset($_su['_items'])) {
                $_pi = array();
                $_gr = array();
                foreach ($_su['_items'] as $_usr) {
                    unset($_id["{$_usr['_user_id']}"]['session_uniq']);
                    $_rp['all_count']++;
                    $_rp['logged_in_count']++;
                    $_rp["{$_usr['user_group']}_count"]++;
                    $_rp["{$_usr['user_group']}"]["{$_usr['_user_id']}"] = isset($_id["{$_usr['_user_id']}"]) ? array_merge($_usr, $_id["{$_usr['_user_id']}"]) : $_usr;
                    if (!isset($_pi["{$_usr['_profile_id']}"])) {
                        $_pi["{$_usr['_profile_id']}"] = array();
                    }
                    $_pi["{$_usr['_profile_id']}"]["{$_usr['_user_id']}"] = 1;
                    $_gr["{$_usr['_profile_id']}"]                        = $_usr['user_group'];
                }
                unset($_id);
                if (count($_pi) > 0) {
                    $_sp = array(
                        'search'      => array(
                            '_profile_id in ' . implode(',', array_keys($_pi)),
                        ),
                        'return_keys' => array('_profile_id', 'profile_name', 'profile_url'),
                        'limit'       => 10000
                    );
                    $_su = jrCore_db_search_items('jrProfile', $_sp);
                    if (isset($_su) && isset($_su['_items'])) {
                        foreach ($_su['_items'] as $_pr) {
                            $grp = $_gr["{$_pr['_profile_id']}"];
                            if (isset($_pi["{$_pr['_profile_id']}"]) && is_array($_pi["{$_pr['_profile_id']}"])) {
                                foreach ($_pi["{$_pr['_profile_id']}"] as $uid => $one) {
                                    $_rp[$grp][$uid] = array_merge($_rp[$grp][$uid], $_pr);
                                }
                            }
                        }
                        unset($_pi);
                    }
                }
            }
            $_rp['all_count'] += (int) $_rp['not_logged_in_count'];
            // Parse template and cache results
            $tmp = jrCore_parse_template($params['template'], $_rp);
            jrCore_add_to_cache('jrUser', $key, $tmp);
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}
