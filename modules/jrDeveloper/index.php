<?php
/**
 * Jamroom 5 Developer Tools module
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
// reset_system
//------------------------------
function view_jrDeveloper_reset_system($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrDeveloper');
    jrCore_set_form_notice('error', "<strong>WARNING!</strong><br>Running this tool will reset your system to the state of a fresh install!<br>It will truncate and delete your system tables!<br>This is serious!", false);
    jrCore_page_banner('Reset System', "delete and reset all system modules");

    // Form init
    $_tmp = array(
        'submit_value' => 'reset system',
    );
    jrCore_form_create($_tmp);

    // Form init
    $_tmp = array(
        'submit_value'  => 'reset system',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Are you SURE you want to DELETE EVERYTHING (except your account)? This cannot be undone (unless you have a backup)!',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the system is reset'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'reset',
        'type'     => 'checkbox',
        'required' => 'on',
        'default'  => 'off',
        'label'    => 'reset system',
        'help'     => 'Check this box to reset the system.  This will delete all DataStore items except your current User Account and Profile.'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// reset_system_save
//------------------------------
function view_jrDeveloper_reset_system_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['reset']) || $_post['reset'] != 'on') {
        jrCore_form_modal_notice('error', 'You need to check the Reset System checkbox!');
        jrCore_form_modal_notice('complete', 'errors were encountered resetting the system');
        exit;
    }

    // Bring in our account info
    $tbl = jrCore_db_table_name('jrUser', 'item_key');
    $req = "SELECT * FROM {$tbl} WHERE `_item_id` = '{$_user['_user_id']}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt) {
        jrCore_form_modal_notice('error', 'Unable to retrieve your account info from the User DataStore!');
        jrCore_form_modal_notice('complete', 'errors were encountered resetting the system');
        exit;
    }

    // Bring in our profile info
    $tbl = jrCore_db_table_name('jrProfile', 'item_key');
    $req = "SELECT * FROM {$tbl} WHERE `_item_id` = '{$_user['_profile_id']}'";
    $_pr = jrCore_db_query($req, 'NUMERIC');
    if (!$_pr) {
        jrCore_form_modal_notice('error', 'Unable to retrieve your profile info from the User DataStore!');
        jrCore_form_modal_notice('complete', 'errors were encountered resetting the system');
        exit;
    }

    // Delete Users
    jrCore_form_modal_notice('update', "resetting user accounts...");

    $tbl = jrCore_db_table_name('jrUser', 'item');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    $req = "INSERT INTO {$tbl} (`_item_id`) VALUES ('{$_user['_user_id']}')";
    jrCore_db_query($req);
    $req = "ALTER TABLE {$tbl} AUTO_INCREMENT = " . intval(($_user['_user_id'] + 1));
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrUser', 'item_key');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    $_vl = array();
    foreach ($_rt as $k => $v) {
        $_vl[] = "('{$_user['_user_id']}', '{$v['key']}', '{$v['index']}', '" . jrCore_db_escape($v['value']) . "')";
    }
    $req = "INSERT INTO {$tbl} (`_item_id`, `key`, `index`, `value`) VALUES " . implode(',', $_vl);
    jrCore_db_query($req);

    // Delete Profiles
    jrCore_form_modal_notice('update', "resetting user profiles...");

    $tbl = jrCore_db_table_name('jrProfile', 'item');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    $req = "INSERT INTO {$tbl} (`_item_id`) VALUES ('{$_user['_profile_id']}')";
    jrCore_db_query($req);
    $req = "ALTER TABLE {$tbl} AUTO_INCREMENT = " . intval(($_user['_profile_id'] + 1));
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrProfile', 'item_key');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    $_vl = array();
    foreach ($_pr as $k => $v) {
        $_vl[] = "('{$_user['_profile_id']}', '{$v['key']}', '{$v['index']}', '" . jrCore_db_escape($v['value']) . "')";
    }
    $req = "INSERT INTO {$tbl} (`_item_id`, `key`, `index`, `value`) VALUES " . implode(',', $_vl);
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    jrProfile_create_user_link($_user['_user_id'], $_user['_profile_id']);

    // Handle DataStores
    jrCore_form_modal_notice('update', "resetting module datastores...");
    $_mds = jrCore_get_datastore_modules();
    if (isset($_mds) && is_array($_mds)) {
        foreach ($_mds as $mod => $pfx) {
            switch ($mod) {
                case 'jrUser':
                case 'jrProfile':
                    break;
                default:
                    jrCore_db_truncate_datastore($mod);
                    jrCore_form_modal_notice('update', "reset {$mod} module datastore...");
                    break;
            }
        }
    }

    // Extras...
    $_todo = array(
        'log'            => 'jrCore',
        'log_debug'      => 'jrCore',
        'form'           => 'jrCore',
        'form_session'   => 'jrCore',
        'pending'        => 'jrCore',
        'pending_reason' => 'jrCore',
        'count_ip'       => 'jrCore',
        'play_key'       => 'jrCore',
        'queue'          => 'jrCore',
        'cookie'         => 'jrUser',
        'url'            => 'jrUser',
        'forgot'         => 'jrUser'
    );
    foreach ($_todo as $name => $mod) {
        $tbl = jrCore_db_table_name($mod, $name);
        $req = "TRUNCATE TABLE {$tbl}";
        jrCore_db_query($req);
        $req = "OPTIMIZE TABLE {$tbl}";
        jrCore_db_query($req);
    }

    // User Sessions
    $_us = jrUser_session_online_user_info(1000000);
    if ($_us && is_array($_us)) {
        $_id = array();
        foreach ($_us as $uid => $ignore) {
            $_id[] = (int) $uid;
        }
        jrUser_session_remove($_id);
    }

    $tbl = jrCore_db_table_name('jrUser', 'session');
    $req = "OPTIMIZE TABLE {$tbl}";
    jrCore_db_query($req);

    // User Language
    jrCore_form_modal_notice('update', 'deleting custom language strings...');
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "DELETE FROM {$tbl} WHERE lang_key > 9999 AND lang_key != 'menu'";
    jrCore_db_query($req);

    // Media directories
    $i = 1;
    jrCore_form_modal_notice('update', 'deleting media directories...');
    while ($i <= 1000) {
        if (is_dir(APP_DIR . "/data/media/{$i}")) {
            jrCore_delete_dir_contents(APP_DIR . "/data/media/{$i}", false);
            rmdir(APP_DIR . "/data/media/{$i}");
        }
        $i++;
    }

    // Cleanup system directory
    jrCore_delete_dir_contents(APP_DIR . "/data/media/0/0", false);

    // Reset caches
    jrCore_delete_all_cache_entries();

    jrCore_form_modal_notice('update', 'finished resetting the system');
    jrCore_form_delete_session();
    jrCore_form_modal_notice('complete', 'The system has been successfully reset');
    exit;
}

//------------------------------
// get_license
//------------------------------
function view_jrDeveloper_get_license($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['type'])) {
        $_rs = array('error', 'invalid type - most be one of module, skin');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['dir'])) {
        $_rs = array('error', 'invalid type directory');
        jrCore_json_response($_rs);
    }
    switch ($_post['type']) {
        case 'module':
            $_mt = jrCore_module_meta_data($_post['dir']);
            break;
        case 'skin':
            $_mt = jrCore_skin_meta_data($_post['dir']);
            break;
        default:
            $_rs = array('error', 'invalid type directory (2)');
            jrCore_json_response($_rs);
            break;
    }
    if (isset($_mt['license'])) {
        $_rs = array('success' => $_mt['license']);
    }
    else {
        $_rs = array('empty' => 'no_license');
    }
    jrCore_json_response($_rs);
}

//------------------------------
// package_skin
//------------------------------
function view_jrDeveloper_package_skin($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_conf['jrDeveloper_developer_prefix']) || strlen($_conf['jrDeveloper_developer_prefix']) === 0 || !isset($_conf['jrDeveloper_developer_name']) || strlen($_conf['jrDeveloper_developer_name']) === 0) {
        jrCore_set_form_notice('error', 'You have not set your Developer Name or Developer Prefix - both are required to package a module or skin');
        $hl = '';
        if (!isset($_conf['jrDeveloper_developer_prefix']) || strlen($_conf['jrDeveloper_developer_prefix']) === 0) {
            $hl .= '/hl[]=developer_prefix';
        }
        if (!isset($_conf['jrDeveloper_developer_name']) || strlen($_conf['jrDeveloper_developer_name']) === 0) {
            $hl .= '/hl[]=developer_name';
        }
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/global{$hl}");
    }

    // Get all skins
    $_skins = jrCore_get_skins();
    $mdir   = jrCore_get_media_directory(0);
    $_mds   = glob("{$mdir}/{$_conf['jrDeveloper_developer_prefix']}*.zip");
    foreach ($_mds as $k => $file) {
        $nam = basename($file);
        list($dir,) = explode('-', $nam, 2);
        if (!isset($_skins[$dir])) {
            unset($_mds[$k]);
            continue;
        }
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrDeveloper');

    $btn = false;
    if ($_mds && is_array($_mds) && count($_mds) > 0) {
        $btn = jrCore_page_button('del', 'delete all zip files', "if(confirm('Are you sure you want to delete all the module ZIP files that have been created?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_all/skin') }");
    }
    jrCore_page_banner('Create Skin ZIP', $btn);

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'icon';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'skin';
    $dat[2]['width'] = '30%';
    $dat[3]['title'] = 'file';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'license';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'size';
    $dat[5]['width'] = '10%';
    $dat[6]['title'] = 'created';
    $dat[6]['width'] = '15%';
    $dat[7]['title'] = 'download';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%';
    jrCore_page_table_header($dat);

    // Show existing module packages that can be downloaded
    if (isset($_mds) && is_array($_mds) && count($_mds) > 0) {
        foreach ($_mds as $k => $file) {
            $nam = basename($file);
            list($dir,) = explode('-', $nam, 2);
            $_sk             = jrCore_skin_meta_data($dir);
            $dat             = array();
            $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/skins/' . $dir . '/icon.png" width="32" height="32" alt="' . $dir . '">';
            $dat[2]['title'] = (isset($_sk['title'])) ? $_sk['title'] : $_sk['name'];
            $dat[3]['title'] = $nam;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = (isset($_sk['license'])) ? $_sk['license'] : '?';
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_format_size(filesize($file));
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_format_time(filemtime($file));
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = jrCore_page_button("d{$k}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/download_skin/{$nam}')");
            $dat[8]['title'] = jrCore_page_button("r{$k}", 'delete', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_skin/{$nam}')");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No skin ZIP files to be downloaded</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    $_opt = array();
    foreach ($_skins as $m => $v) {
        if (strpos($m, $_conf['jrDeveloper_developer_prefix']) === 0) {
            $_mta     = jrCore_skin_meta_data($m);
            $_opt[$m] = (isset($_mta['title'])) ? $_mta['title'] : $_mta['name'];
        }
    }
    if (count($_opt) > 0) {

        // Form init
        $_tmp = array(
            'submit_value'     => 'create skin ZIP',
            'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'form_ajax_submit' => false
        );
        jrCore_form_create($_tmp);

        $_lic = array(
            'mpl'      => 'Mozilla Public License version 2.0',
            'jcl'      => 'Jamroom Commercial License',
            'mit'      => 'MIT License',
            'freeware' => 'Freeware License (no restrictions)',
        );
        jrCore_page_custom('<div id="zip_license_error" class="page_notice error" style="display:none">Invalid <strong>license</strong> field in the module meta data - ensure the license field is set to one of: ' . implode(', ', array_keys($_lic)) . '</div>');

        $_tmp = array(
            'name'     => 'zip_skin',
            'type'     => 'select',
            'options'  => $_opt,
            'required' => 'on',
            'label'    => 'Skin to ZIP',
            'help'     => 'Select the skin you would like to create a ZIP file for. This ZIP file can be used in the Jamroom Marketplace.',
            'onchange' => "var a=this.options[this.selectedIndex].value;jrDeveloper_get_license('skin',a);"
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'     => 'zip_license',
            'type'     => 'select',
            'options'  => $_lic,
            'required' => 'on',
            'label'    => 'Module License',
            'help'     => 'Select the license you would like to use for this product.<br><br><strong>Mozilla Public License version 2.0</strong> - recommended for free modules and skins.<br><br><strong>Jamroom Commercial License</strong> - recommended for paid modules and skins.<br><br><strong>MIT License</strong> - user can resell or rebrand without restriction, but must include attribution.<br><br><strong>Freeware License</strong> - no restriction of any kind.'
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        jrCore_page_notice('error', 'There are no skins found that match your Developer Prefix');
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    jrCore_page_display();
}

//------------------------------
// package_skin_save
//------------------------------
function view_jrDeveloper_package_skin_save($_post, &$_user, &$_conf)
{
    $_skins = jrCore_get_skins();
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_skins["{$_post['zip_skin']}"])) {
        jrCore_set_form_notice('error', 'Invalid skin - please select a skin from the list');
        jrCore_form_field_hilight('zip_skin');
        jrCore_form_result();
    }
    // Get version
    $_mta = jrCore_skin_meta_data($_post['zip_skin']);
    if (!isset($_mta['version'])) {
        jrCore_set_form_notice('error', "The skin is missing the required &quot;version&quot; attribute in the {$_post['zip_skin']}_meta() function");
        jrCore_form_result();
    }

    // Reset template caches for Core and Active skin
    jrCore_delete_dir_contents(APP_DIR . "/data/cache/jrCore");
    jrCore_delete_dir_contents(APP_DIR . "/data/cache/{$_conf['jrCore_active_skin']}");

    $mdir = jrCore_get_media_directory(0);
    if (is_file("{$mdir}/{$_post['zip_skin']}-{$_mta['version']}.zip")) {
        unlink("{$mdir}/{$_post['zip_skin']}-{$_mta['version']}.zip");
    }
    if (is_dir("{$mdir}/skins")) {
        jrCore_delete_dir_contents("{$mdir}/skins", false);
        rmdir("{$mdir}/skins");
    }
    mkdir("{$mdir}/skins", $_conf['jrCore_dir_perms'], true);

    // Copy files to work directory
    jrCore_copy_dir_recursive(APP_DIR . "/skins/{$_post['zip_skin']}", "{$mdir}/skins/{$_post['zip_skin']}");

    $_temp = jrCore_get_directory_files("{$mdir}/skins/{$_post['zip_skin']}");
    if (!$_temp || !is_array($_temp)) {
        jrCore_set_form_notice('error', "Invalid skin - unable to find any skin files");
        jrCore_form_result();
    }
    $_files = array();
    foreach ($_temp as $fullpath => $file) {

        // Cleanup some files we do not need
        switch ($file) {
            case '.DS_Store':
                unlink($fullpath);
                continue 2;
                break;
            default:
                break;
        }

        // Add in the license header depending on the license we got
        if (!strpos($fullpath, '/contrib/') && !strpos($fullpath, '/tools/') && jrCore_file_extension($file) === 'php') {
            jrDeveloper_add_license_header('skin', $_post['zip_skin'], $fullpath, $_post['zip_license']);
        }

        // Included for ZIP file
        $_files["skins/{$_post['zip_skin']}/{$file}"] = $fullpath;
    }

    // Add in full license file
    $_rep = array(
        'item_name'      => $_post['zip_skin'],
        'item_directory' => $_post['zip_skin'],
        'item_type'      => 'skin'
    );
    $temp = jrCore_parse_template("{$_post['zip_license']}.tpl", $_rep, 'jrDeveloper');
    jrCore_write_to_file("{$mdir}/skins/{$_post['zip_skin']}/license.html", $temp);
    $_files["skins/{$_post['zip_skin']}/license.html"] = "{$mdir}/skins/{$_post['zip_skin']}/license.html";

    jrCore_create_zip_file("{$mdir}/{$_post['zip_skin']}-{$_mta['version']}.zip", $_files);
    jrCore_delete_dir_contents("{$mdir}/skins", false);
    rmdir("{$mdir}/skins");
    jrCore_form_delete_session();
    jrCore_location('referrer');
}

//------------------------------
// download_skin
//------------------------------
function view_jrDeveloper_download_skin($_post, $_user, $_conf)
{
    jrUser_master_only();
    $mdir = jrCore_get_media_directory(0);
    if (!isset($_post['_1']) || !is_file("{$mdir}/{$_post['_1']}")) {
        jrCore_set_form_notice('error', 'Invalid ZIP file');
        jrCore_location('referrer');
    }
    jrCore_send_download_file("{$mdir}/{$_post['_1']}");
    session_write_close();
    exit();
}

//------------------------------
// delete_skin
//------------------------------
function view_jrDeveloper_delete_skin($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    $mdir = jrCore_get_media_directory(0);
    if (!isset($_post['_1']) || !is_file("{$mdir}/{$_post['_1']}")) {
        jrCore_set_form_notice('error', 'Invalid ZIP file');
        jrCore_location('referrer');
    }
    unlink("{$mdir}/{$_post['_1']}");
    jrCore_location('referrer');
}

//------------------------------
// package_module
//------------------------------
function view_jrDeveloper_package_module($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_conf['jrDeveloper_developer_prefix']) || strlen($_conf['jrDeveloper_developer_prefix']) === 0 || !isset($_conf['jrDeveloper_developer_name']) || strlen($_conf['jrDeveloper_developer_name']) === 0) {
        jrCore_set_form_notice('error', 'You have not set your Developer Name or Developer Prefix - both are required to package a module or skin');
        $hl = '';
        if (!isset($_conf['jrDeveloper_developer_prefix']) || strlen($_conf['jrDeveloper_developer_prefix']) === 0) {
            $hl .= '/hl[]=developer_prefix';
        }
        if (!isset($_conf['jrDeveloper_developer_name']) || strlen($_conf['jrDeveloper_developer_name']) === 0) {
            $hl .= '/hl[]=developer_name';
        }
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/global{$hl}");
    }

    // Get all ZIPs in media dir
    $mdir = jrCore_get_media_directory(0);
    $_mds = glob("{$mdir}/{$_conf['jrDeveloper_developer_prefix']}*.zip");
    foreach ($_mds as $k => $file) {
        $nam = basename($file);
        list($mod_dir,) = explode('-', $nam, 2);
        if (!isset($_mods[$mod_dir])) {
            unset($_mds[$k]);
        }
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrDeveloper');
    $btn = false;
    if ($_mds && is_array($_mds) && count($_mds) > 0) {
        $btn = jrCore_page_button('del', 'delete all zip files', "if(confirm('Are you sure you want to delete all the module ZIP files that have been created?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_all/module') }");
    }
    jrCore_page_banner('Create Module ZIP', $btn);

    // Start our output
    $dat             = array();
    $dat[1]['title'] = 'icon';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'module';
    $dat[2]['width'] = '30%';
    $dat[3]['title'] = 'file';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'license';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'size';
    $dat[5]['width'] = '10%';
    $dat[6]['title'] = 'created';
    $dat[6]['width'] = '15%';
    $dat[7]['title'] = 'download';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%';
    jrCore_page_table_header($dat);

    // Show existing module packages that can be downloaded
    if (isset($_mds) && is_array($_mds) && count($_mds) > 0) {
        foreach ($_mds as $k => $file) {
            $nam = basename($file);
            list($mod_dir,) = explode('-', $nam, 2);
            $_mt             = jrCore_module_meta_data($mod_dir);
            $dat             = array();
            $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/modules/' . $mod_dir . '/icon.png" width="32" height="32" alt="' . $mod_dir . '">';
            $dat[2]['title'] = $_mods[$mod_dir]['module_name'];
            $dat[3]['title'] = $nam;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = (isset($_mt['license'])) ? $_mt['license'] : '?';
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_format_size(filesize($file));
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_format_time(filemtime($file));
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = jrCore_page_button("d{$k}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/download_module/{$nam}')");
            $dat[8]['title'] = jrCore_page_button("r{$k}", 'delete', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_module/{$nam}')");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No module ZIP files to be downloaded</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    $_opt = array();
    foreach ($_mods as $m => $v) {
        if (strpos($m, $_conf['jrDeveloper_developer_prefix']) === 0) {
            $_opt[$m] = $v['module_name'];
        }
    }
    if (count($_opt) > 0) {
        asort($_opt);

        // Form init
        $_tmp = array(
            'submit_value'     => 'create module ZIP',
            'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
            'form_ajax_submit' => false

        );
        jrCore_form_create($_tmp);

        $_lic = array(
            'mpl'      => 'Mozilla Public License version 2.0',
            'jcl'      => 'Jamroom Commercial License',
            'mit'      => 'MIT License',
            'freeware' => 'Freeware License (no restrictions)',
        );
        jrCore_page_custom('<div id="zip_license_error" class="page_notice error" style="display:none">Invalid <strong>license</strong> field in the module meta data - ensure the license field is set to one of: ' . implode(', ', array_keys($_lic)) . '</div>');

        $_tmp = array(
            'name'     => 'zip_mod',
            'type'     => 'select',
            'options'  => $_opt,
            'required' => 'on',
            'label'    => 'Module to ZIP',
            'help'     => 'Select the module you would like to create a ZIP file for.  This ZIP file can be used in the Jamroom Marketplace.',
            'onchange' => "var a=this.options[this.selectedIndex].value;jrDeveloper_get_license('module',a);"
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'     => 'zip_license',
            'type'     => 'select',
            'options'  => $_lic,
            'required' => 'on',
            'label'    => 'Module License',
            'help'     => 'Select the license you would like to use for this product.<br><br><strong>Mozilla Public License version 2.0</strong> - recommended for free modules and skins.<br><br><strong>Jamroom Commercial License</strong> - recommended for paid modules and skins.<br><br><strong>MIT License</strong> - user can resell or rebrand without restriction, but must include attribution.<br><br><strong>Freeware License</strong> - no restriction of any kind.'
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'     => 'zip_designer',
            'type'     => 'checkbox',
            'default'  => 'off',
            'required' => false,
            'label'    => 'Export Form Designer',
            'help'     => 'If checked, any fields added with the Form Designer will be included in the modules code when exported. - useful if the module was created via Aparna then fields added via the <strong>Form Designer</strong>.'
        );
        jrCore_form_field_create($_tmp);

    }
    else {
        jrCore_page_notice('error', 'There are no modules found that match your Developer Prefix');
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    jrCore_page_display();
}

//------------------------------
// package_module_save
//------------------------------
function view_jrDeveloper_package_module_save($_post, &$_user, &$_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_mods["{$_post['zip_mod']}"])) {
        jrCore_set_form_notice('error', 'Invalid module - please select a module from the list');
        jrCore_form_field_hilight('zip_mod');
        jrCore_form_result();
    }

    // Reset template caches for Core and Active skin
    jrCore_delete_dir_contents(APP_DIR . "/data/cache/jrCore");
    jrCore_delete_dir_contents(APP_DIR . "/data/cache/{$_conf['jrCore_active_skin']}");

    // Get version
    $_mta = jrCore_module_meta_data($_post['zip_mod']);
    if (!isset($_mta['version'])) {
        jrCore_set_form_notice('error', "The module is missing the required &quot;version&quot; attribute in the {$_post['zip_mod']}_meta() function");
        jrCore_form_result();
    }
    $mdir = jrCore_get_media_directory(0);
    if (is_file("{$mdir}/{$_post['zip_mod']}-{$_mta['version']}.zip")) {
        unlink("{$mdir}/{$_post['zip_mod']}-{$_mta['version']}.zip");
    }
    if (is_dir("{$mdir}/modules")) {
        jrCore_delete_dir_contents("{$mdir}/modules", false);
        rmdir("{$mdir}/modules");
    }
    mkdir("{$mdir}/modules", $_conf['jrCore_dir_perms'], true);

    // Copy files to work directory
    jrCore_copy_dir_recursive(APP_DIR . "/modules/{$_post['zip_mod']}", "{$mdir}/modules/{$_post['zip_mod']}");

    $path  = "{$mdir}/modules/{$_post['zip_mod']}";
    $_temp = jrCore_get_directory_files($path);
    if (!$_temp || !is_array($_temp)) {
        jrCore_set_form_notice('error', "Invalid module - unable to find any module files");
        jrCore_form_result();
    }
    $_files = array();
    foreach ($_temp as $fullpath => $file) {

        // Cleanup some files we do not need
        switch ($file) {
            case '.DS_Store':
                unlink($fullpath);
                continue 2;
                break;
            default:
                break;
        }

        // Add in the license header depending on the license we got
        if (!strpos($fullpath, '/contrib/') && !strpos($fullpath, '/tools/') && !strpos($fullpath, '/root/install.php') && $file != 'adminer.php' && jrCore_file_extension($file) === 'php') {
            jrDeveloper_add_license_header('module', $_post['zip_mod'], $fullpath, $_post['zip_license']);
        }

        //export form designer fields, then update the lang strings file too
        if (isset($_post['zip_designer']) && $_post['zip_designer'] == 'on' && $file == 'lang/en-US.php') {
            jrDeveloper_export_lang_strings($_post['zip_mod'], $fullpath);
        }

        // Included for ZIP file
        $_files["modules/{$_post['zip_mod']}/{$file}"] = $fullpath;
    }

    //export form designer fields.
    if (isset($_post['zip_designer']) && $_post['zip_designer'] == 'on') {
        $fullpath = jrDeveloper_export_form_designer_fields($_post['zip_mod'], $path);
        if ($fullpath) {
            $_files["modules/{$_post['zip_mod']}/custom_form_fields.json"] = $fullpath;
        }
    }

    // Add in full license file
    $_rep = array(
        'item_name'      => $_mods["{$_post['zip_mod']}"]['module_name'],
        'item_directory' => $_mods["{$_post['zip_mod']}"]['module_directory'],
        'item_type'      => 'module'
    );
    $temp = jrCore_parse_template("{$_post['zip_license']}.tpl", $_rep, 'jrDeveloper');
    jrCore_write_to_file("{$mdir}/modules/{$_post['zip_mod']}/license.html", $temp);
    $_files["modules/{$_post['zip_mod']}/license.html"] = "{$mdir}/modules/{$_post['zip_mod']}/license.html";

    jrCore_create_zip_file("{$mdir}/{$_post['zip_mod']}-{$_mta['version']}.zip", $_files);
    jrCore_delete_dir_contents("{$mdir}/modules", false);
    rmdir("{$mdir}/modules");
    jrCore_form_delete_session();
    jrCore_location('referrer');
}

//------------------------------
// download_module
//------------------------------
function view_jrDeveloper_download_module($_post, $_user, $_conf)
{
    jrUser_master_only();
    $mdir = jrCore_get_media_directory(0);
    if (!isset($_post['_1']) || !is_file("{$mdir}/{$_post['_1']}")) {
        jrCore_set_form_notice('error', 'Invalid ZIP file');
        jrCore_location('referrer');
    }
    jrCore_send_download_file("{$mdir}/{$_post['_1']}");
    session_write_close();
    exit();
}

//------------------------------
// delete_module
//------------------------------
function view_jrDeveloper_delete_module($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    $mdir = jrCore_get_media_directory(0);
    if (!isset($_post['_1']) || !is_file("{$mdir}/{$_post['_1']}")) {
        jrCore_set_form_notice('error', 'Invalid ZIP file');
        jrCore_location('referrer');
    }
    unlink("{$mdir}/{$_post['_1']}");
    jrCore_location('referrer');
}

//------------------------------
// delete_all
//------------------------------
function view_jrDeveloper_delete_all($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();
    $_sk = jrCore_get_skins();
    $ddr = jrCore_get_media_directory(0);
    $_fl = glob("{$ddr}/*.zip");
    if ($_fl && is_array($_fl)) {
        foreach ($_fl as $file) {
            list($dir,) = explode('-', basename($file), 2);
            if ($_post['_1'] == 'module' && isset($_mods[$dir])) {
                unlink($file);
            }
            elseif ($_post['_1'] == 'skin' && isset($_sk[$dir])) {
                unlink($file);
            }
        }
    }
    jrCore_location('referrer');
}

//------------------------------
// clone_skin
//------------------------------
function view_jrDeveloper_clone_skin($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrDeveloper');

    // Make sure the skin directory is writable by the web user
    if (!is_writable(APP_DIR . '/skins')) {
        jrCore_set_form_notice('error', 'The skin directory is not writable by the web user - unable to clone a skin');
        jrCore_page_banner('Clone Skin');
        jrCore_get_form_notice();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    else {

        jrCore_set_form_notice('success', "&bull; <strong>Active</strong> customized templates will be copied to the template file of the new skin<br>&bull; <strong>Inactive</strong> customized templates will be copied to the new skin but left inactive<br>&bull; Skin Settings will be copied with their existing values to the new skin<br>&bull; Custom Skin Images will be copied to the new skin<br>&bull;Customized Language strings will be copied to the new skin<br>Custom Style changes will be copied to the new skin", false);
        jrCore_page_banner('Clone Skin');

        // Form init
        $_tmp = array(
            'submit_value'  => 'clone skin',
            'cancel'        => 'referrer',
            'submit_prompt' => 'Are you sure you want to clone the selected skin?',
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'     => 'skin_to_clone',
            'type'     => 'select',
            'options'  => jrCore_get_skins(),
            'default'  => $_conf['jrCore_active_skin'],
            'required' => 'on',
            'label'    => 'Skin to Clone',
            'help'     => 'Select the skin that you want to make a clone of.',
            'section'  => 'clone skin'
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'       => 'skin_name',
            'label'      => 'New Skin Name',
            'help'       => "Enter the name you would like to save this new skin as.<br><br><b>NOTE:</b> Only letters, numbers and underscores are allowed in the name.<br><br> If its just for you, not distribution, use xx as the developer prefix.<br>  eg: jrNova use <b>xx</b>Yourname.",
            'type'       => 'text',
            'value'      => '',
            'min'        => 3,
            'validate'   => 'core_string',
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// clone_skin_save
//------------------------------
function view_jrDeveloper_clone_skin_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $_rt = jrCore_get_skins();
    if (!isset($_post['skin_to_clone']) || !in_array($_post['skin_to_clone'], $_rt)) {
        jrCore_set_form_notice('error', 'You have selected an invalid Skin To Clone - please select a valid Skin To Clone from the list of available skins');
        jrCore_form_result();
    }
    if (isset($_post['skin_name']) && in_array($_post['skin_name'], $_rt)) {
        jrCore_set_form_notice('error', 'New skin already exists');
        jrCore_form_result();
    }

    // New Skin name must start with letter
    if (!preg_match('/^[a-z]/i', trim($_post['skin_name']))) {
        jrCore_form_field_hilight('skin_name');
        jrCore_set_form_notice('error', 'Invalid New Skin Name - skin name must begin with a letter');
        jrCore_form_result();
    }

    $_post['skin_name'] = trim($_post['skin_name']);

    // clone the skin
    $_rp = array(
        $_post['skin_to_clone']                        => $_post['skin_name'],
        strtolower($_post['skin_to_clone'])            => strtolower($_post['skin_name']),
        strtoupper($_post['skin_to_clone'])            => strtoupper($_post['skin_name']),
        substr($_post['skin_to_clone'], 2)             => substr($_post['skin_name'], 2),
        strtolower(substr($_post['skin_to_clone'], 2)) => strtolower(substr($_post['skin_name'], 2)),
        strtoupper(substr($_post['skin_to_clone'], 2)) => strtoupper(substr($_post['skin_name'], 2)),
    );

    // Bring in include
    if (is_file(APP_DIR . "/skins/{$_post['skin_to_clone']}/include.php")) {
        require_once APP_DIR . "/skins/{$_post['skin_to_clone']}/include.php";
    }

    // skin title
    $func = $_post['skin_to_clone'] . '_skin_meta';
    if (function_exists($func)) {
        $_orig = $func();
        if (isset($_orig['title'])) {
            $_rp[$_orig['title']] = substr($_post['skin_name'], 2); // skin titles don't always match the file name. 'NingJa vs Ningja' 'PhotoPro vs Photo Pro'
        }
    }

    $res = jrCore_copy_dir_recursive(APP_DIR . "/skins/{$_post['skin_to_clone']}", APP_DIR . "/skins/{$_post['skin_name']}", $_rp);
    if (!$res) {
        jrCore_set_form_notice('error', "An error was encountered trying to copy the skin directory - check Error Log");
    }
    else {

        // Bring in include
        if (is_file(APP_DIR . "/skins/{$_post['skin_name']}/include.php")) {
            require_once APP_DIR . "/skins/{$_post['skin_name']}/include.php";
        }

        // Load config
        if (is_file(APP_DIR . "/skins/{$_post['skin_name']}/config.php")) {
            require_once APP_DIR . "/skins/{$_post['skin_name']}/config.php";
            $func = "{$_post['skin_name']}_skin_config";
            if (function_exists($func)) {
                $func();
            }
        }

        // remove old entries (if any)
        $new = jrCore_db_escape($_post['skin_name']);
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "DELETE FROM {$tbl} WHERE template_module = '{$new}'";
        jrCore_db_query($req);

        // copy template alterations over to new skin
        $skin = jrCore_db_escape($_post['skin_to_clone']);
        $req  = "SELECT * FROM {$tbl} WHERE template_module = '{$skin}'";
        $_rt  = jrCore_db_query($req, 'NUMERIC');
        // Overwrite templates with our custom templates
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $_row) {
                if ($_row['template_active'] == '1') {
                    jrCore_write_to_file(APP_DIR . "/skins/{$_post['skin_name']}/{$_row['template_name']}", strtr($_row['template_body'], $_rp), 'overwrite');
                }
                else {
                    // Load it into the DB but make it INACTIVE
                    $usr = jrCore_db_escape($_row['template_user']);
                    $nam = jrCore_db_escape($_row['template_name']);
                    $typ = jrCore_db_escape($_row['template_type']);
                    $bod = jrCore_db_escape($_row['template_body']);
                    $req = "INSERT INTO {$tbl} (template_module,template_created,template_updated,template_user,template_active,template_name,template_type,template_body)
                            VALUES ('{$new}','{$_row['template_created']}','{$_row['template_updated']}','{$usr}','0','{$nam}','{$typ}','{$bod}')
                            ON DUPLICATE KEY UPDATE template_created = '{$_row['template_created']}', template_updated = '{$_row['template_updated']}', template_user = '{$usr}', template_active = '0', template_type = '{$typ}', template_body = '{$bod}'";
                    $cnt = jrCore_db_query($req, 'COUNT');
                    if (!$cnt || $cnt === 0) {
                        jrCore_logger('MAJ', "Unable to copy inactive custom template: {$_row['template_name']} from {$_post['skin_to_clone']} to {$_post['skin_name']}");
                    }
                }
            }
        }

        // copy settings
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        $req = "SELECT * FROM {$tbl} WHERE `module` = '{$skin}'";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {

            // remove old entries (if they exist)
            $req = "DELETE FROM {$tbl} WHERE `module` = '{$new}'";
            jrCore_db_query($req);

            $tim = time();
            foreach ($_rt as $_row) {
                $_inf = array();
                foreach ($_row as $name => $value) {
                    $_inf["`{$name}`"] = jrCore_db_escape(str_replace($_post['skin_to_clone'], $_post['skin_name'], $value));
                }
                $_inf['`created`'] = $tim;
                $_inf['`updated`'] = $tim;
                $req               = "INSERT INTO {$tbl} (" . implode(',', array_keys($_inf)) . ") VALUES ('" . implode("','", $_inf) . "')";
                jrCore_db_query($req);
            }
        }

        // copy any image over-rides over to the new skin
        $_im = array();
        if (isset($_conf["jrCore_{$skin}_custom_images"]{2})) {
            $_im = json_decode($_conf["jrCore_{$skin}_custom_images"], true);
        }
        if (is_array($_im)) {
            $media_dir = jrCore_get_media_directory(0);
            foreach ($_im as $source_file => $junk) {
                jrCore_copy_media_file(0, "{$media_dir}/{$_post['skin_to_clone']}_{$source_file}", "{$_post['skin_name']}_{$source_file}");
            }
        }

        // copy custom CSS
        $tbl = jrCore_db_table_name('jrCore', 'skin');
        $req = "DELETE FROM {$tbl} WHERE skin_directory = '{$new}'";
        jrCore_db_query($req);

        $req = "SELECT * FROM {$tbl} WHERE skin_directory = '{$skin}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt)) {
            $_cl = array();
            foreach ($_rt as $k => $v) {
                $_cl[$k] = jrCore_db_escape($v);
            }
            $_cl['skin_directory'] = jrCore_db_escape($_post['skin_name']);
            $req                   = "INSERT INTO {$tbl} (" . implode(',', array_keys($_cl)) . ") VALUES ('" . implode("','", $_cl) . "')";
            jrCore_db_query($req);
        }

        // Install lang strings
        $tbl = jrCore_db_table_name('jrUser', 'language');
        $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$skin}'";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            $req = "INSERT INTO {$tbl} (lang_module,lang_code,lang_charset,lang_ltr,lang_key,lang_text,lang_default) VALUES\n";
            foreach ($_rt as $_ln) {
                $cod = jrCore_db_escape($_ln['lang_code']);
                $ltr = jrCore_db_escape($_ln['lang_ltr']);
                $key = jrCore_db_escape($_ln['lang_key']);
                $str = jrCore_db_escape($_ln['lang_text']);
                $def = jrCore_db_escape($_ln['lang_default']);
                $req .= "('{$new}','{$cod}','utf-8','{$ltr}','" . jrCore_db_escape($key) . "','" . jrCore_db_escape($str) . "','" . jrCore_db_escape($def) . "'),";
            }
            $req = substr($req, 0, strlen($req) - 1);
            $cnt = jrCore_db_query($req, 'COUNT');
            if (!$cnt || $cnt === 0) {
                jrUser_install_lang_strings('skin', $_post['skin_name']);
            }
        }

        jrCore_create_master_css($_post['skin_name']);
        jrCore_create_master_javascript($_post['skin_name']);

        jrCore_form_delete_session();
        jrCore_set_form_notice('success', "The {$_post['skin_name']} skin has been cloned from the {$_post['skin_to_clone']} skin");
        $url = jrCore_get_module_url('jrCore');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$url}/skin_admin/info/skin={$_post['skin_name']}");
    }
    jrCore_form_result();
}

//------------------------------
// rebase_modules
//------------------------------
function view_jrDeveloper_rebase_modules($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrDeveloper');

    // Make sure the skin directory is writable by the web user
    if (!is_writable(APP_DIR . '/modules')) {
        jrCore_set_form_notice('error', 'The modules directory is not writable by the web user - unable to rebase modules');
        jrCore_page_banner('Rebase Modules');
        jrCore_get_form_notice();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    jrCore_page_banner('Rebase Modules');

    // Form init
    $_tmp = array(
        'submit_value'  => 'Rebase Modules',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Are you sure you want to rebase the modules?',
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'delete_old',
        'label' => 'Delete Old Versions',
        'help'  => "After rebasing, would you like to delete old versions of the module from the file system?",
        'type'  => 'checkbox'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// rebase_modules_save
//------------------------------
function view_jrDeveloper_rebase_modules_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    list($i, $x) = jrDeveloper_rebase_directory('modules', $_post['delete_old']);
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', $i . " Modules have been successfully rebased.<br>" . $x . ' Old release directories have been removed.', false);
    jrCore_form_result();
}

//------------------------------
// rebase_skins
//------------------------------
function view_jrDeveloper_rebase_skins($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrDeveloper');

    // Make sure the skin directory is writable by the web user
    if (!is_writable(APP_DIR . '/skins')) {
        jrCore_set_form_notice('error', 'The skins directory is not writable by the web user - unable to rebase modules');
        jrCore_page_banner('Rebase Skins');
        jrCore_get_form_notice();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }

    jrCore_page_banner('Rebase Skins');

    // Form init
    $_tmp = array(
        'submit_value'  => 'Rebase Skins',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Are you sure you want to rebase the skins?',
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'delete_old',
        'label' => 'Delete Old Versions',
        'help'  => "After rebasing, would you like to delete old versions of the skins from the file system?",
        'type'  => 'checkbox'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// rebase_skins_save
//------------------------------
function view_jrDeveloper_rebase_skins_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    list($i, $x) = jrDeveloper_rebase_directory('skins', $_post['delete_old']);
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', $i . " Skins have been successfully rebased.<br>" . $x . ' Old release directories have been removed.', false);
    jrCore_form_result();
}
