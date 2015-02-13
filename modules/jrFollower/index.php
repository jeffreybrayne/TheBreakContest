<?php
/**
 * Jamroom 5 Profile Followers module
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

//------------------------------
// follow
//------------------------------
function view_jrFollower_follow($_post, $_user, $_conf)
{
    // [_uri] => /fans/follow/5/__ajax=1
    // [module_url] => fans
    // [module] => jrFollower
    // [option] => follow
    // [_1] => 5 (profile_id to be followed)
    // [__ajax] => 1

    jrUser_session_require_login();
    jrCore_validate_location_url();

    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        $_rs = array('error' => 'invalid profile_id - please try again');
        jrCore_json_response($_rs);
    }

    $_ln = jrUser_load_lang_strings();
    $pid = (int) $_post['_1'];

    // First - see if this user is already following
    $_rt = jrFollower_is_follower($_user['_user_id'], $pid);
    if ($_rt) {
        // User is already a follower
        $_rs = array('OK' => 1, 'VALUE' => $_ln['jrFollower'][2]);
        jrCore_json_response($_rs);
    }

    // We need to see if this profile is requiring approval before
    // any new follower can join up.
    $_pi = jrCore_db_get_item('jrProfile', $pid);
    $act = 1;
    if (isset($_pi['profile_jrFollower_approve']) && $_pi['profile_jrFollower_approve'] == 'on') {
        $act = 0;
    }

    // Create our new following entry
    $_dt = array(
        'follow_profile_id' => $pid,
        'follow_active'     => $act
    );
    $_cr = array(
        '_profile_id' => jrUser_get_profile_home_key('_profile_id')
    );
    $fid = jrCore_db_create_item('jrFollower', $_dt, $_cr, false);
    if (isset($fid) && jrCore_checktype($fid, 'number_nz')) {
        $_owners = jrProfile_get_owner_info($pid);
        // If we are not active...
        if ($act === 0) {
            // Send out email to profile owners letting them know of the new follower
            if (isset($_owners) && is_array($_owners)) {
                $_rp = array(
                    'system_name'          => $_conf['jrCore_system_name'],
                    'follower_name'        => $_user['user_name'],
                    'follower_url'         => "{$_conf['jrCore_base_url']}/" . jrUser_get_profile_home_key('profile_url'),
                    'approve_follower_url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/{$pid}/{$_pi['profile_url']}",
                    '_profile'             => $_pi
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrFollower', 'approve', $_rp);
                foreach ($_owners as $_o) {
                    jrUser_notify($_o['_user_id'], 0, 'jrFollower', 'follower_pending', $sub, $msg);
                }
            }
            $_rs = array('PENDING' => 1, 'VALUE' => $_ln['jrFollower'][5]);
            jrCore_json_response($_rs);
        }
        else {
            if (isset($_owners) && is_array($_owners)) {
                $_rp = array(
                    'profile_url'          => $_pi['profile_url'],
                    'system_name'          => $_conf['jrCore_system_name'],
                    'follower_name'        => $_user['user_name'],
                    'follower_profile_url' => "{$_conf['jrCore_base_url']}/" . jrUser_get_profile_home_key('profile_url'),
                    'follower_browse_url'  => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/{$pid}/{$_pi['profile_url']}",
                    '_profile'             => $_pi
                );
                list($sub, $msg) = jrCore_parse_email_templates('jrFollower', 'new_follower', $_rp);
                foreach ($_owners as $_o) {
                    jrUser_notify($_o['_user_id'], 0, 'jrFollower', 'new_follower', $sub, $msg);
                }
            }

            // Increment Profile counts...
            jrCore_db_increment_key('jrProfile', $pid, 'profile_jrFollower_item_count', 1);

            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'create', 'jrFollower', $fid, $_pi, false, jrUser_get_profile_home_key('_profile_id'));
            jrProfile_reset_cache($pid);
            jrProfile_reset_cache(jrUser_get_profile_home_key('_profile_id'));
            $_rs = array('OK' => 1, 'VALUE' => $_ln['jrFollower'][2]);
            jrCore_json_response($_rs);
        }
    }
    $_rs = array('error' => 'unable to create follow request - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// unfollow
//------------------------------
function view_jrFollower_unfollow($_post, $_user, $_conf)
{
    // [_uri] => /fans/unfollow/5/__ajax=1
    // [module_url] => fans
    // [module] => jrFollower
    // [option] => follow
    // [_1] => 5 (profile_id to no longer followed)
    // [__ajax] => 1
    jrUser_session_require_login();
    jrCore_validate_location_url();

    $_ln = jrUser_load_lang_strings();
    $pid = (int) $_post['_1'];

    // Make sure user is a follower
    $_rt = jrFollower_is_follower($_user['_user_id'], $pid);
    if ($_rt) {
        // If this follower is ACTIVE, we need to decrement follower counts
        if (isset($_rt['follow_active']) && $_rt['follow_active'] == '1') {
            jrCore_db_decrement_key('jrProfile', $pid, 'profile_jrFollower_item_count', 1);
        }
        jrProfile_reset_cache($pid);
        jrCore_db_delete_item('jrFollower', $_rt['_item_id'], true, false);
    }
    $_rs = array('OK' => 1, 'VALUE' => $_ln['jrFollower'][1]);
    jrCore_json_response($_rs);
}

//------------------------------
// browse
//------------------------------
function view_jrFollower_browse($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    $_ln = jrUser_load_lang_strings();

    $pid = $_user['user_active_profile_id'];
    $prn = $_user['profile_name'];
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && jrProfile_is_profile_owner($_post['_1']) && $_post['_1'] != $pid) {
        $pid = (int) $_post['_1'];
        $prn = jrCore_db_get_item_key('jrProfile', $pid, 'profile_name');
    }
    jrCore_page_banner("{$prn} - {$_ln['jrFollower'][26]}");

    $_sc = array(
        'search'                       => array(
            "follow_profile_id = {$pid}"
        ),
        'pagebreak'                    => 12,
        'page'                         => 1,
        'order_by'                     => array(
            '_created' => 'numerical_desc'
        ),
        'exclude_jrProfile_quota_keys' => true,
        'privacy_check'                => false,
        'ignore_pending'               => true,
        'no_cache'                     => true
    );
    if (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) {
        $_sc['pagebreak'] = (int) $_COOKIE['jrcore_pager_rows'];
    }
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $_sc['page'] = (int) $_post['p'];
    }
    $_us = jrCore_db_search_items('jrFollower', $_sc);
    $_ln = jrUser_load_lang_strings();

    $dat             = array();
    $dat[1]['title'] = '&nbsp;';
    $dat[1]['width'] = '3%';
    $dat[2]['title'] = $_ln['jrFollower'][27]; // 'user name'
    $dat[2]['width'] = '31%';
    $dat[3]['title'] = $_ln['jrFollower'][28]; // 'profile name'
    $dat[3]['width'] = '31%';
    $dat[4]['title'] = $_ln['jrFollower'][29]; // 'follower since'
    $dat[4]['width'] = '25%';
    $dat[5]['title'] = $_ln['jrFollower'][30]; // 'approve'
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = $_ln['jrFollower'][31]; // 'delete'
    $dat[6]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (isset($_us['_items']) && is_array($_us['_items'])) {

        foreach ($_us['_items'] as $_usr) {
            $dat             = array();
            $_im             = array(
                'crop'  => 'auto',
                'alt'   => $_usr['user_name'],
                'title' => $_usr['user_name'],
                '_v'    => (isset($_usr['user_image_time']) && $_usr['user_image_time'] > 0) ? $_usr['user_image_time'] : false
            );
            $dat[1]['title'] = jrImage_get_image_src('jrUser', 'user_image', $_usr['_user_id'], 'xsmall', $_im);
            $dat[2]['title'] = '<h3>' . $_usr['user_name'] . '</h3>';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = "{$_usr['profile_name']}&nbsp;&nbsp;(<a href=\"{$_conf['jrCore_base_url']}/{$_usr['profile_url']}\">@{$_usr['profile_url']}</a>)";
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_format_time($_usr['_created']);
            $dat[4]['class'] = 'center';
            if (isset($_usr['follow_active']) && $_usr['follow_active'] == '0') {
                $dat[5]['title'] = jrCore_page_button("a{$_usr['_user_id']}", 'approve', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/approve/{$pid}/{$_usr['_user_id']}')");
                $dat[5]['class'] = 'center error';
            }
            else {
                $dat[5]['title'] = '-';
                $dat[5]['class'] = 'center';
            }
            $dat[6]['title'] = jrCore_page_button("d{$_usr['_user_id']}", 'delete', "if(confirm('" . addslashes($_ln['jrFollower'][33]) . "')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete/{$pid}/{$_usr['_user_id']}' )}");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_us);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>{$_ln['jrFollower'][32]}</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// approve
//------------------------------
function view_jrFollower_approve($_post, $_user, $_conf)
{
    // [_uri] => /fans/approve/1/5/__ajax=1
    // [module_url] => fans
    // [module] => jrFollower
    // [option] => follow
    // [_1] => 1 (profile_id)
    // [_2] => 5 (user_id being approved)
    // [__ajax] => 1
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_load_lang_strings();

    $pid = (int) $_post['_1'];
    $uid = (int) $_post['_2'];

    // Make sure this user has access to this profile
    if (!jrProfile_is_profile_owner($pid)) {
        jrUser_not_authorized();
    }

    // Make sure follow exists...
    $_rt = jrFollower_is_follower($uid, $pid);
    if (!$_rt) {
        jrCore_notice_page('error', 'User does not appear to have a follower entry - please try again');
    }
    $_us = jrCore_db_get_item('jrUser', $uid);
    $_dt = array(
        'follow_active' => 1
    );
    jrCore_db_update_item('jrFollower', $_rt['_item_id'], $_dt);

    // Increment Profile counts...
    jrCore_db_increment_key('jrProfile', $pid, 'profile_jrFollower_item_count', 1);

    // Get profile info of user that we just approved
    $_pr = jrCore_db_get_item('jrProfile', $pid);

    // We only send the email on first activation
    $_rp = array(
        'profile_name' => $_pr['profile_name'],
        'profile_url'  => "{$_conf['jrCore_base_url']}/{$_pr['profile_url']}"
    );
    list($sub, $msg) = jrCore_parse_email_templates('jrFollower', 'follower_approved', $_rp);
    jrUser_notify($uid, 0, 'jrFollower', 'follow_approved', $sub, $msg);
    jrCore_delete_all_cache_entries('jrFollower', $_user['_user_id']);

    // Add action
    $_save = array(
        '_user_id' => $uid
    );
    jrCore_set_flag('follower_approved', $_save);
    jrCore_run_module_function('jrAction_save', 'create', 'jrFollower', $_rt['_item_id'], $_pr, false, $_us['_profile_id']);

    jrProfile_reset_cache($_us['_profile_id']);
    jrProfile_reset_cache($pid);
    if (strpos(jrCore_get_local_referrer(), "{$_post['module_url']}/browse")) {
        jrCore_location('referrer');
    }
    jrCore_notice_page('success', 'The new follower has been approved!');
}

//------------------------------
// delete
//------------------------------
function view_jrFollower_delete($_post, $_user, $_conf)
{
    // [_uri] => /fans/delete/1/5/__ajax=1
    // [module_url] => fans
    // [module] => jrFollower
    // [option] => follow
    // [_1] => 1 (profile_id)
    // [_2] => 5 (user_id being approved)
    // [__ajax] => 1
    jrUser_session_require_login();
    jrCore_validate_location_url();

    $pid = (int) $_post['_1'];
    $uid = (int) $_post['_2'];

    // Make sure this user has access to this profile
    if (!jrProfile_is_profile_owner($pid)) {
        jrUser_not_authorized();
    }

    // Make sure follow exists...
    $_rt = jrFollower_is_follower($uid, $pid);
    if ($_rt) {
        // If this follower is ACTIVE, we need to decrement follower counts
        if (isset($_rt['follow_active']) && $_rt['follow_active'] == '1') {
            jrCore_db_decrement_key('jrProfile', $pid, 'profile_jrFollower_item_count', 1);
        }
        jrCore_db_delete_item('jrFollower', $_rt['_item_id']);
    }
    jrProfile_reset_cache($pid);
    jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// integrity_check
//------------------------------
function view_jrFollower_integrity_check($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrFollower');
    jrCore_page_banner("Integrity Check");

    // Form init
    $_tmp = array(
        'submit_value'  => 'run integrity check',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Are you sure you want to run the Profile Followers Integrity Check? Please be patient - on large systems this could take some time.',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the Integrity Check runs'
    );
    jrCore_form_create($_tmp);

    // Validate Follower Counts
    $_tmp = array(
        'name'     => 'validate_counts',
        'label'    => 'validate counts',
        'help'     => 'Check this box so the system will validate and update the number of followers each profile has',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// integrity_check_save
//------------------------------
function view_jrFollower_integrity_check_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    jrCore_logger('INF', 'follower integrity check started');
    ini_set('max_execution_time', 1978000); // 23 hours max

    // Module install validation
    if (isset($_post['validate_counts']) && $_post['validate_counts'] == 'on') {

        jrCore_form_modal_notice('update', "validating profile follower counts");

        // Get profiles
        $num = 0;
        $tot = 0;
        while (true) {
            $_sc = array(
                'search'         => array(
                    "_item_id > {$num}"
                ),
                'return_keys'    => array('_profile_id'),
                'skip_triggers'  => true,
                'ignore_pending' => true,
                'privacy_check'  => false,
                'limit'          => 100
            );
            $_rt = jrCore_db_search_items('jrProfile', $_sc);
            if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
                $_dt = array();
                $str = $_rt['_items'][0]['_profile_id'];
                foreach ($_rt['_items'] as $v) {
                    $num = $v['_profile_id'];
                    // Update counts
                    $_sc = array(
                        'search'         => array(
                            "follow_profile_id = {$v['_profile_id']}"
                        ),
                        'return_count'   => true,
                        'skip_triggers'  => true,
                        'ignore_pending' => true,
                        'privacy_check'  => false
                    );
                    $cnt = jrCore_db_search_items('jrFollower', $_sc);
                    if (!$cnt || !is_numeric($cnt)) {
                        $cnt = 0;
                    }
                    $_dt["{$v['_profile_id']}"] = array('profile_jrFollower_item_count' => intval($cnt));
                    $tot++;
                }
                $upd = count($_dt);
                if ($upd > 0) {
                    jrCore_db_update_multiple_items('jrProfile', $_dt);
                    jrCore_form_modal_notice('update', "updated follower counts for {$upd} profiles ({$str} - {$num})");
                }
            }
            else {
                // No more profiles...
                break;
            }
        }
        jrCore_form_modal_notice('update', "successfully validated profile follower counts for {$tot} profiles");
    }
    jrCore_form_delete_session();
    jrCore_logger('INF', 'follower integrity check completed');
    jrCore_form_modal_notice('complete', 'The follower integrity check options successfully completed');
    exit;
}
