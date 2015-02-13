<?php
/**
 * Jamroom 5 Page Creator module
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
// default (view a page)
//------------------------------
function view_jrPage_default($_post, $_user, $_conf)
{
    // We will get our page_id and page_url on the URL - i.e.
    // http://site.com/pages/1/this-is-the-page-title
    // so $_post['option'] will be set with our page_id (1)
    if (isset($_post['option']) && strlen($_post['option']) > 0) {

        if (!jrCore_checktype($_post['option'], 'number_nz')) {
            // Bad page id
            jrCore_page_not_found();
        }

        $pid = (int) $_post['option'];
        $key = $_post['_uri'];
        if ($out = jrCore_is_cached('jrPage', $key)) {
            return $out;
        }
        $_rt = jrCore_db_get_item('jrPage', $pid);
        if (!isset($_rt) || !is_array($_rt)) {
            jrCore_page_not_found();
        }

        // Set title, parse and return
        jrCore_page_title($_rt['page_title']);

        // Parse template
        $out  = jrCore_parse_template('header.tpl', $_post);
        $out .= jrCore_parse_template('item_detail.tpl', array('item' => $_rt), 'jrPage');
        $out .= jrCore_parse_template('footer.tpl', $_post);

        if ($_rt['page_location'] == '0') {
            jrCore_add_to_cache('jrPage', $key, $out, 0, 0);
        }
        else {
            jrCore_add_to_cache('jrPage', $key, $out, 0, $_rt['_profile_id']);
        }
        return $out;
    }

    // Fall through - page index
    return jrCore_parse_template('index.tpl', $_post, 'jrPage');
}

//------------------------------
// browse (browse existing)
//------------------------------
function view_jrPage_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrPage', 'tools');
    jrCore_page_banner('page browser');
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $_sc = array(
        'search' => array(
            "page_location = 0"
        ),
        'order_by' => array(
            '_updated' => 'numerical_desc'
        ),
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true,
        'pagebreak'      => 12,
        'page'           => $page
    );
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_sc['search'][] = "page_body like %{$_post['search_string']}%";
    }
    $_pg = jrCore_db_search_items('jrPage', $_sc);

    $dat = array();
    $dat[1]['title'] = 'title';
    $dat[1]['width'] = '70%';
    $dat[2]['title'] = 'updated';
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = 'modify';
    $dat[3]['width'] = '5%';
    $dat[4]['title'] = 'delete';
    $dat[4]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (isset($_pg['_items']) && is_array($_pg['_items'])) {
        foreach ($_pg['_items'] as $k => $_page) {
            $dat = array();
            $dat[1]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$_page['_item_id']}/{$_page['page_title_url']}\" target=\"_blank\">{$_page['page_title']}</a>";
            $dat[2]['title'] = jrCore_format_time($_page['_updated']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_page_button("r{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_page['_item_id']}')");
            $dat[4]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this page?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete/id={$_page['_item_id']}') }");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_pg);
    }
    else {
        $dat = array();
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $dat[1]['title'] = "<p>There were no pages found that match your search criteria</p>";
        }
        else {
            $dat[1]['title'] = "<p>There have been no pages created for this site</p>";
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// create
//------------------------------
function view_jrPage_create($_post, $_user, $_conf)
{
    // Must be logged in to create a page
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPage');

    // Bring in language
    jrUser_load_lang_strings();
    $_lang = jrCore_get_flag('jr_lang');

    // Start our create form
    jrCore_page_banner(1);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Page Title
    $_tmp = array(
        'name'     => 'page_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Page Location (profile or main site)
    // NOTE: master admin only
    $_opt = array(
        0 => $_lang['jrPage'][15],
        1 => $_lang['jrPage'][16]
    );
    $_tmp = array(
        'name'     => 'page_location',
        'group'    => 'master',
        'label'    => 13,
        'help'     => 14,
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 1,
        'validate' => 'number_nn',
        'min'      => 0,
        'max'      => 1,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Page Body
    $_tmp = array(
        'name'     => 'page_body',
        'label'    => 5,
        'help'     => 6,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Include Header
    $_tmp = array(
        'name'     => 'page_header',
        'label'    => 22,
        'help'     => 23,
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Item Features
    $_tmp = array(
        'name'     => 'page_features',
        'label'    => 24,
        'help'     => 25,
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrPage_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPage');
    jrCore_form_validate($_post);

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrPage', 'create', $_post);

    // If we are NOT a master admin, page_location will not be set
    $pcnt = true;
    if (!jrUser_is_master()) {
        $_rt['page_location'] = '1';
    }
    else {
        if (isset($_rt['page_location']) && $_rt['page_location'] == '0') {
            // Page is being created for SITE - no profile counter
            $pcnt = false;
        }
    }

    // Next, we need to create the "slug" from the title and save it
    $_rt['page_title_url'] = jrCore_url_string($_rt['page_title']);

    // $aid will be the INSERT_ID (_item_id) of the created item
    $aid = jrCore_db_create_item('jrPage', $_rt, null, $pcnt);
    if (!$aid) {
        jrCore_set_form_notice('error', 7);
        jrCore_form_result();
    }

    // If this is a MASTER user, then any pages they have created for the SITE
    // will show up in a profile / user count - so we run that separate here
    if (jrUser_is_master()) {

        // User Counts
        $_sc = array(
            'search' => array(
                "_user_id = {$_user['_user_id']}"
            ),
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true,
            'return_count'   => true,
            'nocache'        => true,
            'limit'          => 100000
        );
        $cnt = jrCore_db_search_items('jrPage', $_sc);
        if ($cnt && jrCore_checktype($cnt, 'number_nn')) {
            $_up = array('user_jrPage_item_count' => $cnt);
            jrCore_db_update_item('jrUser', $_user['_user_id'], $_up);
        }

        // Profile Counts
        if ($pcnt && $_user['user_active_profile_id'] == jrUser_get_profile_home_key('_profile_id')) {
            // We are a MASTER ADMIN creating a new page on OUR profile
            $_sc = array(
                'search'         => array(
                    "_profile_id = {$_user['user_active_profile_id']}",
                    "page_location = 1"
                ),
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'return_count'   => true,
                'nocache'        => true,
                'limit'          => 100000
            );
            $cnt = jrCore_db_search_items('jrPage', $_sc);
            if ($cnt && jrCore_checktype($cnt, 'number_nn')) {
                $_up = array('profile_jrPage_item_count' => $cnt);
                jrCore_db_update_item('jrProfile', $_user['user_active_profile_id'], $_up);
            }
        }
    }

    // Save any uploaded media files added in by our Page Designer
    jrCore_save_all_media_files('jrPage', 'create', $_user['user_active_profile_id'], $aid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrPage', $aid);

    jrCore_form_delete_session();

    // See where we redirect here...
    if (!isset($_rt['page_location']) || $_rt['page_location'] === '1') {
        jrProfile_reset_cache();
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$aid}/{$_rt['page_title_url']}");
    }
    else {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$aid}/{$_rt['page_title_url']}");
    }
}

//------------------------------
// update
//------------------------------
function view_jrPage_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPage');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 9);
    }
    $_rt = jrCore_db_get_item('jrPage', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 9);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Bring in language
    jrUser_load_lang_strings();
    $_lang = jrCore_get_flag('jr_lang');

    // Start output
    // Note - we're going to show different page jumpers here depending
    // on if we are modifying a SITE page or a PROFILE page
    if (isset($_rt['page_location']) && intval($_rt['page_location']) === 0) {
        $_sr = array(
            'page_location = 0'
        );
        $tmp = jrCore_page_banner_item_jumper('jrPage', 'page_title', $_sr, 'create', 'update');
    }
    else {
        $_sr = array(
            "_profile_id = {$_rt['_profile_id']}",
            'page_location = 1'
        );
        $tmp = jrCore_page_banner_item_jumper('jrPage', 'page_title', $_sr, 'create', 'update');
    }
    jrCore_page_banner(10, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 11,
        'values'       => $_rt
    );
    if (isset($_rt['page_location']) && intval($_rt['page_location']) === 0) {
        $_tmp['cancel'] = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse";
        $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$_rt['_item_id']}/{$_rt['page_title_url']}";
    }
    else {
        $_tmp['cancel'] = jrCore_is_profile_referrer();
    }
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    if (isset($url)) {
        jrCore_page_custom("<a href=\"{$url}\" target=\"_blank\">{$url}</a>", 'page url');
    }

    // Page Title
    $_tmp = array(
        'name'     => 'page_title',
        'label'    => 3,
        'help'     => 4,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Page Location (profile or main site)
    // NOTE: master admin only
    $_opt = array(
        0 => $_lang['jrPage'][15],
        1 => $_lang['jrPage'][16]
    );
    $_tmp = array(
        'name'     => 'page_location',
        'group'    => 'master',
        'label'    => 13,
        'help'     => 14,
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 1,
        'validate' => 'number_nn',
        'min'      => 0,
        'max'      => 1,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Page Body
    $_tmp = array(
        'name'     => 'page_body',
        'label'    => 5,
        'help'     => 6,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Include Header
    $_tmp = array(
        'name'     => 'page_header',
        'label'    => 22,
        'help'     => 23,
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Item Features
    $_tmp = array(
        'name'     => 'page_features',
        'label'    => 24,
        'help'     => 25,
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrPage_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPage');

    // Validate all incoming posted data
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 9);
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrPage', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 9);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrPage', 'update', $_post);

    // Add in our SEO URL names
    $_sv['page_title_url'] = jrCore_url_string($_sv['page_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrPage', $_post['id'], $_sv);

    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('jrPage', 'update', $_rt['_profile_id'], $_post['id']);

    jrCore_form_delete_session();
    // See where we redirect here...
    if (!isset($_sv['page_location']) || $_sv['page_location'] === '1') {
        jrProfile_reset_cache();
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['page_title_url']}");
    }
    else {
        jrCore_delete_all_cache_entries('jrPage');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['page_title_url']}");
    }
}

//------------------------------
// delete
//------------------------------
function view_jrPage_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_check_quota_access('jrPage');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 9);
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrPage', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 9);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // If we are deleting a SITE PAGE, no profile count update
    $cnt = true;
    if (jrUser_is_master()) {
        if (isset($_rt['page_location']) && $_rt['page_location'] == '0') {
            $cnt = false;
        }
    }

    // Delete item and any associated files
    jrCore_db_delete_item('jrPage', $_post['id'], true, $cnt);
    if (!isset($_rt['page_location']) || $_rt['page_location'] === '1') {

        // Profile Counts
        if ($cnt && jrUser_is_master() && $_user['user_active_profile_id'] == jrUser_get_profile_home_key('_profile_id')) {
            $_sc = array(
                'search'         => array(
                    "_profile_id = {$_user['user_active_profile_id']}",
                    "page_location = 1"
                ),
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'return_count'   => true,
                'nocache'        => true,
                'limit'          => 100000
            );
            $cnt = jrCore_db_search_items('jrPage', $_sc);
            if ($cnt && jrCore_checktype($cnt, 'number_nn')) {
                $_up = array('profile_jrPage_item_count' => $cnt);
                jrCore_db_update_item('jrProfile', $_user['user_active_profile_id'], $_up);
            }
        }

        jrProfile_reset_cache();
    }
    else {
        jrCore_delete_all_cache_entries('jrPage');
    }

    // If this is a MASTER admin deleting a site page we created,
    // We need to decrement our User Page count
    if (jrUser_is_master() && isset($_rt['_user_id']) && $_rt['_user_id'] == $_user['_user_id']) {
        // User Counts
        $_sc = array(
            'search' => array(
                "_user_id = {$_user['_user_id']}"
            ),
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true,
            'return_count'   => true,
            'nocache'        => true,
            'limit'          => 100000
        );
        $cnt = jrCore_db_search_items('jrPage', $_sc);
        if ($cnt && jrCore_checktype($cnt, 'number_nn')) {
            $_up = array('user_jrPage_item_count' => $cnt);
            jrCore_db_update_item('jrUser', $_user['_user_id'], $_up);
        }
    }

    jrCore_form_result('delete_referrer');
}
