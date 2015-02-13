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

//------------------------------
// check_status
//------------------------------
function view_jrUserLimit_check_stream_status($_post, $_user, $_conf)
{
    if (jrUser_is_logged_in() && isset($_user['quota_jrUserLimit_event_stream_file']) && jrCore_checktype($_user['quota_jrUserLimit_event_stream_file'], 'number_nz')) {
        // Enforce quota
        $max = (int) $_user["quota_jrUserLimit_event_stream_file"];
        $evt = jrCore_db_escape('stream_file');
        $dat = strftime('%Y%m%d');
        $tbl = jrCore_db_table_name('jrUserLimit', 'counts');
        $req = "SELECT c_count FROM {$tbl} WHERE c_user_id = '{$_user['_user_id']}'AND c_module = '". jrCore_db_escape($_post['module']) ."' AND c_event = '{$evt}' AND c_date = '{$dat}' LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            if ($_rt['c_count'] < $max) {
                jrCore_json_response(array('OK' => '1'));
            }
        }
        $_ln = jrUser_load_lang_strings();
        jrCore_json_response(array('error' => $_ln['jrUserLimit'][1]));
    }
    jrCore_json_response(array('OK' => '1'));
}

//------------------------------
// browse
//------------------------------
function view_jrUserLimit_browse($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrUserLimit', 'browse');
    jrCore_page_banner('User Limits Browser');
    if (!jrCore_module_is_active('jrAudio') && !jrCore_module_is_active('jrVideo')) {
        jrCore_set_form_notice('notice', 'User Daily Limits works with the <strong>Audio Support</strong> and <strong>Video Support</strong> modules -<br>neither are active on your system - the settings below will have no effect until either module is active.', false);
    }
    jrCore_get_form_notice();

    // Get all configured Quota events
    $_ev = jrUserLimit_get_action_options();
    $_qt = jrProfile_get_quotas();
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "SELECT * FROM {$tbl} WHERE `module` = 'jrUserLimit' AND `name` LIKE 'event-%' ORDER BY `quota_id` ASC, `module` ASC";
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12);

    $dat             = array();
    $dat[1]['title'] = 'quota';
    $dat[1]['width'] = '25%';
    $dat[2]['title'] = 'module';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'event';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'daily limit';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '10%';
    jrCore_page_table_header($dat);

    if ($_rt['_items'] && is_array($_rt['_items'])) {

        foreach ($_rt['_items'] as $k => $v) {
            $nam             = str_replace('event-', '', $v['name']);
            list($mod, $evt) = explode('-', $nam, 2);
            $dat             = array();
            $dat[1]['title'] = $_qt["{$v['quota_id']}"];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_mods[$mod]['module_name'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_ev[$evt];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $v['value'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this entry?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/c_event={$v['name']}/c_quota_id={$v['quota_id']}'); }");
            $dat[5]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no Limits configured</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
    }

    // Form init
    $_tmp = array(
        'submit_value'     => 'add new limit',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Quota ID
    $_opt = array('0' => '(all quotas)');
    $_opt = $_opt + $_qt;
    $_tmp = array(
        'name'     => 'ul_quota_id',
        'label'    => 'quota',
        'help'     => 'Select the Quota you would like to add a new Limit to.<br><br><strong>NOTE:</strong> Daily User Limits are not allpied to Master or Admin users.',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nz',
        'required' => true,
        'section'  => 'add a new daily user limit'
    );
    jrCore_form_field_create($_tmp);

    // Module
    $_opt = array();
    foreach ($_mods as $mod => $_inf) {
        switch ($mod) {
            case 'jrAudio':
            case 'jrVideo':
                $_opt[$mod] = $_inf['module_name'];
                break;
        }
    }
    natcasesort($_opt);
    $_tmp = array(
        'name'     => 'ul_mod',
        'label'    => 'module',
        'help'     => 'Select the Module you would like to add a new Limit to',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Event
    $_tmp = array(
        'name'     => 'ul_event',
        'label'    => 'event',
        'help'     => 'Select the Event you would like to add a new Limit to',
        'type'     => 'select',
        'options'  => jrUserLimit_get_action_options(),
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Limit
    $_tmp = array(
        'name'     => 'ul_limit',
        'label'    => 'daily limit',
        'help'     => 'Enter the max number of times Users in the selected Quota will be allowed to perform the selected Event.<br><br><strong>Note:</strong> Max value is 255.',
        'type'     => 'text',
        'validate' => 'number_nz',
        'min'      => 1,
        'max'      => 255,
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// browse_save
//------------------------------
function view_jrUserLimit_browse_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_form_validate($_post);
    $lim = intval($_post['ul_limit']);
    if ($lim > 0) {

        // Make sure Quota config exists
        $_tmp = array(
            'name'     => "event-{$_post['ul_mod']}-{$_post['ul_event']}",
            'type'     => 'hidden',
            'validate' => 'number_nz',
            'label'    => "user limits for {$_post['ul_mod']}/{$_post['ul_event']}",
            'help'     => "This is a hidden setting for the User Daily Limits module - do not modify",
            'default'  => '0',
            'order'    => 100
        );
        jrProfile_register_quota_setting('jrUserLimit', $_tmp);

        $qid = intval($_post['ul_quota_id']);
        if ($qid === 0) {
            // Adding to all quotas
            $_qt = jrProfile_get_quotas();
            if (is_array($_qt)) {
                foreach ($_qt as $qid => $qname) {
                    jrProfile_set_quota_value('jrUserLimit', $qid, "event-{$_post['ul_mod']}-{$_post['ul_event']}", $lim);
                }
            }
        }
        else {
            // Single Quota
            jrProfile_set_quota_value('jrUserLimit', $qid, "event-{$_post['ul_mod']}-{$_post['ul_event']}", $lim);
        }
        // Reset user cache
        jrCore_delete_all_cache_entries('jrUser');
    }
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The new Limit was successfully created');
    jrCore_location('referrer');
}

//------------------------------
// delete_save
//------------------------------
function view_jrUserLimit_delete_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    if (!isset($_post['c_event']{2})) {
        jrCore_set_form_notice('error', "invalid event value - please try again");
        jrCore_location('referrer');
    }
    if (!isset($_post['c_quota_id']) || !jrCore_checktype($_post['c_quota_id'], 'number_nz')) {
        jrCore_set_form_notice('error', "invalid quota_id value - please try again");
        jrCore_location('referrer');
    }

    // Remove quota value
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "DELETE FROM {$tbl} WHERE `module` = 'jrUserLimit' AND `quota_id` = '{$_post['c_quota_id']}' AND `name` = '". jrCore_db_escape($_post['c_event']) ."' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_delete_all_cache_entries('jrUser');
        jrCore_set_form_notice('success', 'The Limit was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to delete quota setting - please try again');
    }
    jrCore_location('referrer');
}
