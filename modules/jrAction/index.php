<?php
/**
 * Jamroom 5 Activity Timeline module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// mention_profiles
//------------------------------
function view_jrAction_mention_profiles($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (isset($_post['q']) && strlen($_post['q']) > 1) {
        $_sc = array(
            'search'                       => array(
                "profile_name like {$_post['q']}%"
            ),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'ignore_pending'               => true,
            'return_keys'                  => array('_profile_id', 'profile_url', 'profile_name')
        );
        $_rt = jrCore_db_search_items('jrProfile', $_sc);
        if (is_array($_rt['_items'])) {
            $_rs = array();
            $url = jrCore_get_module_url('jrProfile');
            foreach ($_rt['_items'] as $_v) {
                $_rs[] = array(
                    'id'     => $_v['_profile_id'],
                    'name'   => "@{$_v['profile_url']}",
                    'avatar' => "{$_conf['jrCore_base_url']}/{$url}/image/profile_image/{$_v['_profile_id']}/xxsmall/crop=auto",
                    'type'   => 'contact'
                );
            }
            jrCore_json_response($_rs);
        }
    }
    exit;
}

//------------------------------
// share
//------------------------------
function view_jrAction_share($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');

    // We should get a valid action ID
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid action_id received - please try again');
    }
    $_rt = jrCore_db_get_item('jrAction', $_post['_1']);
    // Make sure we don't own it
    if (isset($_rt['_profile_id']) && $_rt['_profile_id'] == $_user['user_active_profile_id']) {
        jrCore_location('referrer');
    }
    // Make sure we have not ALREADY shared this
    $_sp = array(
        'search'         => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "action_module = {$_rt['action_module']}",
            "action_original_item_id = {$_rt['_item_id']}"
        ),
        'order_by'       => array(
            '_item_id' => 'desc'
        ),
        'limit'          => 1,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'skip_triggers'  => true
    );
    $_ex = jrCore_db_search_items('jrAction', $_sp);
    if (is_array($_ex) && isset($_ex['_items']) && isset($_ex['_items'][0])) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/" . $_ex['_items'][0]['_item_id']);
    }

    // Copy it
    $_ac = array();
    foreach ($_rt as $k => $v) {
        if (strpos($k, 'action_') === 0) {
            if (is_array($v)) {
                $_ac[$k] = json_encode($v);
            }
            else {
                $_ac[$k] = $v;
            }
        }
    }
    $_ac['action_data'] = $_ac['action_item'];
    unset($_ac['action_item']);

    $_ac['action_original_created']      = $_rt['_created'];
    $_ac['action_original_item_id']      = $_rt['_item_id'];
    $_ac['action_original_user_id']      = $_rt['_user_id'];
    $_ac['action_original_profile_name'] = $_rt['profile_name'];
    $_ac['action_original_profile_url']  = $_rt['profile_url'];
    $_ac['action_original_profile_id']   = $_rt['_profile_id'];
    $_ac['action_original_shared_by']    = $_user['_user_id'];
    $aid                                 = jrCore_db_create_item('jrAction', $_ac);
    if (!$aid) {
        jrCore_notice_page('error', 'unable to share action!');
    }

    // Add profile_id to items action_shared_by field
    $_sb = array();
    if (isset($_rt['action_original_item_id']) && jrCore_checktype($_rt['action_original_item_id'], 'number_nz')) {
        $oid = $_rt['action_original_item_id'];
        $sby = jrCore_db_get_item_key('jrAction', $oid, 'action_shared_by');
        if ($sby) {
            $_sb = explode(',', $sby);
        }
    }
    else {
        $oid = $_rt['_item_id'];
        if (isset($_rt['action_shared_by'])) {
            $_sb = explode(',', $_rt['action_shared_by']);
        }
    }
    // Cleanup
    if ($_sb && is_array($_sb) && count($_sb) > 0) {
        foreach ($_sb as $k => $v) {
            if (!is_numeric($v)) {
                unset($_sb[$k]);
            }
            else {
                $_sb[$k] = (int) $v;
            }
        }
    }
    $_sb[] = (int) $_user['_user_id'];
    $_up = array(
        'action_shared_by' => implode(',', $_sb)
    );
    jrCore_db_update_item('jrAction', $oid, $_up);

    // If this is an admin user, redirect back to their home time line
    if (jrUser_is_admin()) {
        $url = jrUser_get_profile_home_key('profile_url');
        jrCore_location("{$_conf['jrCore_base_url']}/{$url}/{$_post['module_url']}");
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// create
//------------------------------
function view_jrAction_create($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');
    jrCore_page_banner('Activity Update');

    // Form init
    $_tmp = array(
        'submit_value' => 'save',
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Activity Update
    $_tmp = array(
        'name'     => 'action_text',
        'label'    => 'Activity Update',
        'help'     => 'Enter an update for your Profile',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrAction_create_save($_post, &$_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');

    $_rt = array(
        'action_text'     => $_post['action_text'],
        'action_quota_id' => $_user['profile_quota_id'],
        'action_module'   => 'jrAction'
    );
    $url = jrCore_get_local_referrer();
    // $aid will be the INSERT_ID (_item_id) of the created item
    $aid = jrCore_db_create_item('jrAction', $_rt);
    if (!$aid) {
        jrCore_set_form_notice('error', 'Unable to create new Activity Entry - please try again');
        if (isset($_user['profile_url']) && strpos($url, "{$_conf['jrCore_base_url']}/{$_user['profile_url']}") === 0) {
            // Posting from URL - refresh
            jrCore_location($url);
        }
    }
    else {
        // Send out our Action Created trigger
        $_args = array(
            '_user_id' => $_user['_user_id'],
            '_item_id' => $aid,
        );
        jrCore_trigger_event('jrAction', 'create', $_rt, $_args);

        // Notify any users if we mention them...
        jrAction_check_for_mentions($_post['action_text']);

        jrProfile_reset_cache();
        if (isset($_user['profile_url']) && strpos($url, "{$_conf['jrCore_base_url']}/{$_user['profile_url']}") === 0) {
            // Posting from URL - refresh
            jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
        }
    }
    jrCore_form_result();
}

//------------------------------
// delete
//------------------------------
function view_jrAction_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item id');
    }
    $_rt = jrCore_db_get_item('jrAction', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid item id');
    }
    // Make sure the calling user has permissions to remove this action
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    jrCore_db_delete_item('jrAction', $_post['id']);
    jrProfile_reset_cache();
    // See where we came from
    $url = jrCore_get_local_referrer();
    if (strpos($url, "{$_post['module_url']}/{$_post['id']}")) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}");
    }
    jrCore_location('referrer');
}

//------------------------------
// update
//------------------------------
function view_jrAction_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAction');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item id');
    }
    $_rt = jrCore_db_get_item('jrAction', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 'Unable to retrieve item data');
    }
    // Can only update action text items
    if (!isset($_rt['action_text'])) {
        jrCore_notice_page('error', "Not an 'action_text' activity");
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    jrCore_page_banner(27);

    // Form init
    $_tmp = array(
        'submit_value' => 28,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Action Text
    $_tmp = array(
        'name'      => 'action_text',
        'label'     => 29,
        'help'      => 30,
        'type'      => 'textarea',
        'ban_check' => 'word',
        'validate'  => 'printable',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrAction_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrAction');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrAction', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Unable to retrieve item data');
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrAction', 'update', $_post);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrAction', $_post['id'], $_sv);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}");
}

