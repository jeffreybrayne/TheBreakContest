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
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit;

// Bring in functions
require_once APP_DIR . '/modules/jrCore/lib/view.php';

//------------------------------
// delete
//------------------------------
function view_jrCore_delete($_post,$_user,$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    // [module_url] => image
    // [module] => jrImage
    // [option] => delete
    // [_1] => jrProfile
    // [_2] => profile_bg_image
    // [_3] => 1
    if (!isset($_post['_1']) || !jrCore_db_get_prefix($_post['_1'])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['_3']) || !jrCore_checktype($_post['_3'],'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item_id');
        jrCore_location('referrer');
    }
    // Get info about this item to be sure the requesting user is allowed
    $_rt = jrCore_db_get_item($_post['_1'], $_post['_3'], SKIP_TRIGGERS);
    if (!isset($_rt) || !is_array($_rt) || !isset($_rt['_profile_id'])) {
        jrCore_set_form_notice('error', 'Invalid item_id (2)');
        jrCore_location('referrer');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_rt['_profile_id'])) {
        jrUser_not_authorized();
    }

    // Remove file
    jrCore_delete_item_media_file($_post['_1'], $_post['_2'], $_rt['_profile_id'], $_post['_3']);

    // If this was a user or profile image, reload session
    switch ($_post['_1']) {
        case 'jrUser':
        case 'jrProfile':
            jrUser_session_sync($_user['_user_id']);
            break;
    }
    jrProfile_reset_cache($_rt['_profile_id']);
    jrCore_set_form_notice('success', 'The file was successfully deleted');
    jrCore_location('referrer');
}

//------------------------------
// url_stream_error
//------------------------------
function view_jrCore_stream_url_error($_post, $_user, $_conf)
{
    global $_urls;
    if (!isset($_post['_1']) || !isset($_urls["{$_post['_1']}"])) {
        $_er = array('error' => 'Invalid module received in stream_url_error');
        jrCore_json_response($_er);
    }
    $_er = array('error' => 'An error was encountered loading the media URL');
    $_xx = array(
        'module' => $_urls["{$_post['_1']}"]
    );
    $_er = jrCore_trigger_event('jrCore', 'stream_url_error', $_er, $_xx);
    jrCore_json_response($_er);
}

//------------------------------
// queue_viewer
//------------------------------
function view_jrCore_queue_view($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');

    if (isset($_conf['jrCore_queues_active']) && $_conf['jrCore_queues_active'] == 'off') {
        $buttons = jrCore_page_button('pause', 'resume queues', "if(confirm('Resume all queue workers?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/queue_pause/on'); }");
    }
    else {
        $buttons = jrCore_page_button('pause', 'pause queues', "if(confirm('Pause all queue workers?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/queue_pause/off'); }");
    }
    $buttons .= jrCore_page_button('refresh', 'refresh', "location.reload();");
    jrCore_page_banner('Queue Viewer', $buttons);
    jrCore_set_form_notice('success', 'Queue Workers are background processes that perform &quot;queued tasks&quot;<br>such as media conversion, notifications, cache cleanup, system backups, etc.', false);
    if (isset($_conf['jrCore_queues_active']) && $_conf['jrCore_queues_active'] == 'off') {
        jrCore_set_form_notice('error', 'Queue Workers are currently <strong>PAUSED</strong><br>New queue jobs will not be performed while Queue Workers are paused', false);
    }
    jrCore_get_form_notice();

    $tbl = jrCore_db_table_name('jrCore', 'queue');

    // Queue Counts
    $req = "SELECT COUNT(queue_id) as qcount, MIN(queue_created) AS q_min, queue_name, queue_module FROM {$tbl} ";
    if (isset($_post['queue_name']) && strlen($_post['queue_name']) > 0) {
        $req .= "WHERE queue_name = '". jrCore_db_escape($_post['queue_name']) ."' ";
    }
    $req .= "GROUP BY queue_name ORDER BY queue_name ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    // Worker Count
    $req = "SELECT COUNT(queue_worker) as qworkers, queue_name FROM {$tbl} WHERE queue_started > 0 GROUP BY queue_name";
    $_qw = jrCore_db_query($req, 'queue_name', false, 'qworkers');

    $dat = array();
    $dat[1]['title'] = 'queue module';
    $dat[1]['width'] = '29%';
    $dat[2]['title'] = 'queue name';
    $dat[2]['width'] = '29%';
    $dat[3]['title'] = 'active workers';
    $dat[3]['width'] = '14%';
    $dat[4]['title'] = 'queue entries';
    $dat[4]['width'] = '14%';
    $dat[5]['title'] = 'queue latency';
    $dat[5]['width'] = '14%';
    jrCore_page_table_header($dat);

    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $v) {
            $dat = array();
            $dat[1]['title'] = $_mods["{$v['queue_module']}"]['module_name'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $v['queue_name'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = (isset($_qw["{$v['queue_name']}"])) ? $_qw["{$v['queue_name']}"] : 0;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $v['qcount'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrCore_number_format((time() - $v['q_min'])) . ' s';
            $dat[5]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat = array();
        $dat[1]['title'] = '<p>There are no queue entries to show</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// queue_pause
//------------------------------
function view_jrCore_queue_pause($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'onoff')) {
        jrCore_set_form_notice('error', 'invalid queue state recevied - please try again');
        jrCore_location('referrer');
    }
    jrCore_set_setting_value('jrCore', 'queues_active', $_post['_1']);
    jrCore_delete_config_cache();
    jrCore_location('referrer');
}

//------------------------------
// css
//------------------------------
function view_jrCore_css($_post, $_user, $_conf)
{
    global $_urls;
    // http://site.com/core/css/audio/jrAudio_jplayer_dark.css
    if ($_post['_1'] != 'skin' && !isset($_urls["{$_post['_1']}"])) {
        jrCore_notice('CRI', "invalid module or skin");
    }
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_notice('CRI', "invalid css files");
    }
    $key = md5(json_encode($_post));
    if ($css = jrCore_is_cached('jrCore', $key, false)) {
        header('Content-Length: ' . strlen($css));
        header('Content-Type: text/css');
        echo $css;
        exit;
    }
    $crl = jrCore_get_module_url('jrImage');
    if ($_post['_1'] == 'skin') {
        $css = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/css/{$_post['_2']}";
        $_rp = array(
            '{$' . $_conf['jrCore_active_skin'] .'_img_url}' => "{$_conf['jrCore_base_url']}/{$crl}/img/skin/{$_conf['jrCore_active_skin']}"
        );
    }
    else {
        $mod = $_urls["{$_post['_1']}"];
        $css = APP_DIR . "/modules/{$mod}/css/{$_post['_2']}";
        $_rp = array(
            '{$' . $mod .'_img_url}' => "{$_conf['jrCore_base_url']}/{$crl}/img/module/{$mod}"
        );
    }
    if (!$size = filesize($css)) {
        jrCore_notice('error', 'CSS file not found');
        exit;
    }
    $css = str_replace(array_keys($_rp), $_rp, file_get_contents($css));

    // Next, get our customized style from the database
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($_conf['jrCore_active_skin']) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt['skin_custom_css']{1})) {
        $css .= "\n";
        $_custom = json_decode($_rt['skin_custom_css'], true);
        if (isset($_custom) && is_array($_custom)) {
            foreach ($_custom as $sel => $_rules) {
                if (' ' . strpos($sel, $css)) {
                    $css .= $sel . " {";
                    $_cr = array();
                    foreach ($_rules as $k => $v) {
                        if (!strpos($v, '!important')) {
                            $_cr[] = $k . ':' . $v . ' !important;';
                        }
                        else {
                            $_cr[] = $k . ':' . $v;
                        }
                    }
                    $css .= implode(' ', $_cr) . "}\n";
                }
            }
        }
    }
    jrCore_add_to_cache('jrCore', $key, $css, 0, 0, false);
    header('Content-Length: ' . strlen($css));
    header('Content-Type: text/css');
    echo $css;
    exit;
}

//------------------------------
// icon_css
//------------------------------
function view_jrCore_icon_css($_post, $_user, $_conf)
{
    jrUser_ignore_action();
    $width = 64;
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && $_post['_1'] < 64) {
        $width = intval($_post['_1']);
    }
    $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
    if (!is_file("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$width}.css") || !is_file("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$width}.png")) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_color');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $color = reset($color);
        }
        else {
            $color = 'black';
        }
        jrCore_create_css_sprite($_conf['jrCore_active_skin'], $color, $width);
    }
    header("Content-type: text/css");
    header('Content-Disposition: inline; filename="sprite_' . $width . '.css"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
    echo file_get_contents("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$width}.css");
    exit;
}

//------------------------------
// icon_sprite
//------------------------------
function view_jrCore_icon_sprite($_post, $_user, $_conf)
{
    jrUser_ignore_action();
    $width = 64;
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && $_post['_1'] < 64) {
        $width = intval($_post['_1']);
    }
    $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
    if (!is_file("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$width}.png")) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_color');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $color = reset($color);
        }
        else {
            $color = 'black';
        }
        jrCore_create_css_sprite($_conf['jrCore_active_skin'], $color, $width);
    }
    header("Content-type: image/png");
    header('Content-Disposition: inline; filename="sprite_' . $width . '.png"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
    echo file_get_contents("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$width}.png");
    exit;
}

//------------------------------
// form_validate
//------------------------------
function view_jrCore_form_validate($_post, $_user, $_conf)
{
    jrUser_ignore_action();
    return jrCore_form_validate($_post);
}

//------------------------------
// form_modal_status
//------------------------------
function view_jrCore_form_modal_status($_post, $_user, $_conf)
{
    if (!isset($_post['k'])) {
        $_tmp = array('t' => 'error', 'm' => 'invalid key');
        jrCore_json_response($_tmp);
    }
    // Get the results from the DB of our status
    $tbl = jrCore_db_table_name('jrCore', 'modal');
    $req = "SELECT modal_id AS i, modal_value AS m FROM {$tbl} WHERE modal_key = '" . jrCore_db_escape($_post['k']) . "' ORDER BY modal_id ASC";
    $_rt = jrCore_db_query($req, 'i', false, 'm');
    if (isset($_rt) && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE modal_id IN(" . implode(',', array_keys($_rt)) . ")";
        jrCore_db_query($req);
        foreach ($_rt as $k => $v) {
            $_rt[$k] = json_decode($v, true);
        }
        jrCore_json_response($_rt);
        exit;
    }
    $_tmp = array(array('t' => 'empty', 'm' => 'no results found for key'));
    jrCore_json_response($_tmp);
    exit;
}

//------------------------------
// form_modal_cleanup
//------------------------------
function view_jrCore_form_modal_cleanup($_post, $_user, $_conf)
{
    if (!isset($_post['k'])) {
        $_tmp = array(array('t' => 'error', 'm' => 'invalid key'));
        jrCore_json_response($_tmp);
    }
    jrCore_form_modal_cleanup($_post['k']);
    exit;
}

//------------------------------
// module_detail_features
//------------------------------
function view_jrCore_module_detail_features($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('item detail features','features provided by modules for item detail pages');
    jrCore_get_form_notice();

    // Get all registered features
    $_tmp = jrCore_get_registered_module_features('jrCore', 'item_detail_feature');
    if (!isset($_tmp) || !is_array($_tmp)) {
        jrCore_notice_page('notice', 'There are no modules in the system that provide Item Detail Features');
    }

    $_ord = array();
    if (isset($_conf['jrCore_detail_feature_order']) && strlen($_conf['jrCore_detail_feature_order']) > 0) {
        $_ord = array_flip(explode(',', $_conf['jrCore_detail_feature_order']));
    }
    else {
        foreach ($_tmp as $mod => $_ft) {
            foreach ($_ft as $name => $_ftr) {
                $_ord[] = "{$mod}~{$name}";
            }
        }
        jrCore_set_setting_value('jrCore', 'detail_feature_order', implode(',', $_ord));
        $_ord = array_flip($_ord);
    }

    // First get things in the right order
    $_res = array();
    foreach ($_tmp as $mod => $_ft) {
        foreach ($_ft as $nam => $_ftr) {
            $name = "{$mod}~{$nam}";
            $_ftr['module'] = $mod;
            $_res[$name] = $_ftr;
        }
    }

    $dat = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'order';
    $dat[2]['width'] = '2%';
    $dat[3]['title'] = 'module';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'feature';
    $dat[4]['width'] = '20%';
    $dat[5]['title'] = 'description';
    $dat[5]['width'] = '46%';
    $dat[6]['title'] = 'TPL name';
    $dat[6]['width'] = '10%';
    jrCore_page_table_header($dat);

    $cnt = 0;
    // First do our items that have been ordered
    if (count($_ord) > 0) {
        foreach ($_ord as $name => $order) {
            $_ftr = $_res[$name];
            $dat = array();
            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/{$_ftr['module']}/icon.png\" width=\"32\" alt=\"" . $_mods["{$_ftr['module']}"]['module_name'] . "\">";
            if ($cnt === 0) {
                $dat[2]['title'] = '';
            }
            else {
                $dat[2]['title'] = jrCore_page_button("f{$cnt}", '^', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/module_detail_feature_order/{$name}/{$cnt}')");
            }
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_mods["{$_ftr['module']}"]['module_name'];
            $dat[4]['title'] = $_ftr['label'];
            $dat[5]['title'] = $_ftr['help'];
            list(,$tpl_name) = explode('~', $name);
            $dat[6]['title'] = $tpl_name;
            jrCore_page_table_row($dat);
            $cnt++;
            unset($_res[$name]);
        }
    }
    // Any left overs
    if (count($_res) > 0) {
        foreach ($_res as $name => $_ftr) {
            $dat = array();
            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/{$_ftr['module']}/icon.png\" width=\"32\" alt=\"jrCore\">";
            if ($cnt === 0) {
                $dat[2]['title'] = '';
            }
            else {
                $dat[2]['title'] = jrCore_page_button("f{$cnt}", '^', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/module_detail_feature_order/{$name}/{$cnt}')");
            }
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_mods["{$_ftr['module']}"]['module_name'];
            $dat[4]['title'] = $_ftr['label'];
            $dat[5]['title'] = $_ftr['help'];
            list(,$tpl_name) = explode('~', $name);
            $dat[6]['title'] = $tpl_name;
            jrCore_page_table_row($dat);
            $cnt++;
        }
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// module_detail_feature_order
//------------------------------
function view_jrCore_module_detail_feature_order($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // [_1] => jrTags~item_tags
    // [_2] => 1 (current order)
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_set_form_notice('error', 'Invalid item detail feature');
        jrCore_location('referrer');
    }
    $nam = trim($_post['_1']);
    list($mod,$feat) = explode('~', $nam);
    $_tmp = jrCore_get_registered_module_features('jrCore', 'item_detail_feature');
    if (!$_tmp || !is_array($_tmp) || !isset($_tmp[$mod]) || !isset($_tmp[$mod][$feat])) {
        jrCore_set_form_notice('error', 'Invalid item feature - feature is not registered');
        jrCore_location('referrer');
    }
    $idx = (int) $_post['_2'];
    $_cfg = array();
    $_don = array();
    if (isset($_conf['jrCore_detail_feature_order']) && strlen($_conf['jrCore_detail_feature_order']) > 0) {
        $_cfg = explode(',', $_conf['jrCore_detail_feature_order']);
        if (isset($_cfg) && is_array($_cfg)) {
            foreach ($_cfg as $k => $v) {
                $_don[$v] = 1;
                $tmp = ($idx - 1);
                if ($k == $tmp) {
                    // We have found our swap
                    $_cfg[$idx] = $v;
                    $_cfg[$tmp] = $nam;
                }
            }
        }
    }
    // Add in reset of detail features
    foreach ($_tmp as $mod => $_ft) {
        foreach ($_ft as $name => $_ftr) {
            $nam = "{$mod}~{$name}";
            if (!isset($_don[$nam])) {
                $_cfg[] = $nam;
            }
        }
    }
    jrCore_set_setting_value('jrCore', 'detail_feature_order', implode(',', $_cfg));
    jrCore_delete_config_cache();
    if (jrCore_checktype($_conf['jrCore_default_cache_seconds'], 'number_nz')) {
        jrCore_set_form_notice('success', 'The item feature order has been updated.<br>Make sure and <a href="'. $_conf['jrCore_base_url'] .'/'. $_post['module_url'] .'/cache_reset"><u>Reset Caches</u></a> for your changes to take effect',false);
    }
    else {
        jrCore_set_form_notice('success', 'The item feature order has been updated');
    }
    jrCore_location('referrer');
}

//------------------------------
// performance_history
//------------------------------
function view_jrCore_performance_history($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('performance history');

    // See if we have an existing value
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} ORDER BY p_id DESC";
    $_tm = jrCore_db_paged_query($req, $page, 12);
    if ($_tm && is_array($_tm) && isset($_tm['_items'])) {

        $dat = array();
        $dat[1]['title'] = 'date';
        $dat[1]['width'] = '20%';
        $dat[2]['title'] = 'processor';
        $dat[2]['width'] = '20%';
        $dat[3]['title'] = 'database';
        $dat[3]['width'] = '20%';
        $dat[4]['title'] = 'filesystem';
        $dat[4]['width'] = '20%';
        $dat[5]['title'] = 'total';
        $dat[5]['width'] = '20%';
        jrCore_page_table_header($dat);

        foreach ($_tm['_items'] as $k => $_v) {
            $_pt = json_decode($_v['p_val'], true);
            $tot = round((10 / $_pt['total']) * 800);
            $cls = 'success';
            if ($tot < 200) {
                $cls = 'error';
            }
            elseif ($tot < 400) {
                $cls = 'error';
            }
            elseif ($tot < 600) {
                $cls = 'notice';
            }
            elseif ($tot < 800) {
                $cls = 'notice';
            }
            $dat = array();
            $dat[1]['title'] = jrCore_format_time($_v['p_time']);
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_pt['cpu'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_pt['db'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_pt['fs'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = '<strong>' . jrCore_number_format($tot) . '</strong>';
            $dat[5]['class'] = "{$cls} center";
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_tm);
        jrCore_page_table_footer();
    }
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// performance_check
//------------------------------
function view_jrCore_performance_check($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');

    // See if we have an existing value
    $btn = false;
    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} ORDER BY p_id DESC LIMIT 1";
    $_tm = jrCore_db_query($req, 'SINGLE');

    if ($_tm && is_array($_tm)) {
        $btn = jrCore_page_button('performance', 'history', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_history');");
    }

    $share = false;
    // If we are properly configured for the Marketplace, let the user submit a result
    if (jrCore_module_is_active('jrMarket')) {
        if ($_mkt = jrMarket_get_active_release_system()) {
            if (isset($_mkt['system_url']) && strpos($_mkt['system_url'], 'jamroom.net')) {
                $share = true;
            }
        }
    }

    jrCore_page_banner('performance check', $btn);
    jrCore_set_form_notice('notice', "The Performance Check will run a series of performance tests to assess how well Jamroom is likely to<br>perform on your server - it is recommended to run this test at a low traffic time on your server.", false);
    jrCore_get_form_notice();

    if ($_tm && is_array($_tm)) {

        $_tm = json_decode($_tm['p_val'], true);

        $_inf = jrCore_get_proc_info();
        $mysi = jrCore_db_connect();
        $mver = mysqli_get_server_info($mysi);
        if (strpos($mver, '-')) {
            list($mver, ) = explode('-', $mver);
        }
        $_dsk = jrCore_get_disk_usage();

        // Our "baseline" for a high performance systems is 6.00
        // $_tm = array( cpu, db, fs, total)
        $total = round((10 / $_tm['total']) * 800);

        // Baselines...
        $_bs = array(
            'cpu' => '0.5',
            'db'  => '6.5',
            'fs'  => '1.0'
        );

        // baselines:
        // cpu = 2
        // db = 6
        // fs = 4

        $cpu = round($_tm['cpu'], 2);
        $db  = round($_tm['db'], 2);
        $fs  = round($_tm['fs'], 2);

        $msg = 'success';
        $txt = 'Jamroom should run <strong>excellent</strong> on your server!';
        if ($total < 200) {
            $msg = 'error';
            $txt = 'Jamroom will run <strong>very slowly</strong> on your server - check out hosting alternatives';
        }
        elseif ($total < 400) {
            $msg = 'error';
            $txt = 'Jamroom is <strong>likely to run slowly</strong> on your server - check out hosting alternatives';
        }
        elseif ($total < 600) {
            $msg = 'notice';
            $txt = 'Jamroom <strong>may run slowly</strong> on your server - check out tips on improving performance';
        }
        elseif ($total < 800) {
            $msg = 'notice';
            $txt = 'Jamroom <strong>may run a bit slower than optimal</strong> on your server';
        }
        $btn = '';
        if ($share) {
            $btn = jrCore_page_button('share', 'share your results', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_share');");
        }
        $htm = '<div style="padding:12px;">
        <table class="page_table bigtable" style="width:100%">
        <tr><th class="page_table_header" colspan="2" style="width:55%">Component Score</th><th class="page_table_header" style="width:45%">Overall Score</th></tr>
        <tr><td class="page_table_header" style="width:20%">Processor<br><span style="color:#888;font-weight:normal">' . count($_inf) . ' @' . $_inf[1]['mhz'] . '</span></td><td class="page_table_cell bignum bignum2">' . $cpu . '<br><span>Baseline ' . $_bs['cpu'] . '</td>
        <td class="page_table_cell bignum bignum1" rowspan="3"><big>' . jrCore_number_format($total) . '</big><br><span>Baseline: 1,000</span>' . $btn . '</td></tr>
        <tr><td class="page_table_header">Database<br><span style="color:#888;font-weight:normal">MySQL ' . $mver . '</span></td><td class="page_table_cell bignum bignum3">' . $db . '<br><span>Baseline ' . $_bs['db'] . '</span></td></tr>
        <tr><td class="page_table_header">Filesystem<br><span style="color:#888;font-weight:normal">In Use: ' . $_dsk['percent_used'] . '%</span></td><td class="page_table_cell bignum bignum4">' . $fs . '<br><span>Baseline ' . $_bs['fs'] . '</span></td></tr>
        </table>' . $txt .'<br><small>Baseline is a XEON @ 2.8GHz, 1 GB RAM and Fast SSD Disk</small></div>';
        jrCore_set_form_notice($msg, $htm, false);
        jrCore_get_form_notice();
    }

    $_tmp = array(
        'submit_value'  => 'run performance check',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Please be patient - depending on the speed of your servers this could take a few minutes to run'
    );
    jrCore_form_create($_tmp);

    // New Menu Entry
    $_tmp = array(
        'name'  => 'hidden',
        'type'  => 'hidden',
        'value' => 1
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// performance_share
//------------------------------
function view_jrCore_performance_share($_post, $_user, $_conf)
{
    jrUser_master_only();
    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} ORDER BY p_id DESC LIMIT 1";
    $_tm = jrCore_db_query($req, 'SINGLE');
    if (!$_tm || !is_array($_tm)) {
        jrCore_notice_page('error', 'There are no results to share - please run the Performance Check tool at least once');
    }
    if (isset($_tm['p_provider']) && strlen($_tm['p_provider']) > 0) {
        // See if they JUST shared this
        if (!$tmp = jrCore_get_temp_value('jrCore', 'performance_shared')) {
            jrCore_set_form_notice('warning', "This performance result has already been shared - resubmitting will overwrite your previous entry");
        }
        else {
            jrCore_delete_temp_value('jrCore', 'performance_shared');
        }
    }

    // If we are EMPTY on provider, price and rating get the LAST entry so we can pre-fill
    if (!isset($_tm['p_provider']) || strlen($_tm['p_provider']) === 0) {
        $req = "SELECT * FROM {$tbl} WHERE LENGTH(p_provider) > 0 ORDER BY p_id DESC LIMIT 1";
        $_ol = jrCore_db_query($req, 'SINGLE');
        if ($_ol && is_array($_ol)) {
            $_tm['p_provider'] = $_ol['p_provider'];
            $_tm['p_price']    = $_ol['p_price'];
            $_tm['p_rating']   = $_ol['p_rating'];
            $_tm['p_type']     = (strlen($_ol['p_type']) > 0) ? $_ol['p_type'] : 'none';
        }
        unset($_ol);
    }

    $pid = (int) $_tm['p_id'];
    $_pr = json_decode($_tm['p_val'], true);
    $tot = jrCore_number_format(round((10 / $_pr['total']) * 800));

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('share your performance results');

    $_tmp = array(
        'submit_value'     => 'share these results',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_check",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $titl = '';
    $line = '';
    $cadd = '';
    $_cpu = jrCore_get_proc_info();
    if ($_cpu && is_array($_cpu) && isset($_cpu[1]['mhz'])) {
        $cadd = "&nbsp;<b>Server CPU:</b> {$_cpu[1]['mhz']}<br>";
    }
    $madd = '';
    $_mem = jrCore_get_system_memory();
    if ($_mem && is_array($_mem) && isset($_mem['memory_total'])) {
        $madd = "&nbsp;<b>Server RAM:</b> " . jrCore_format_size($_mem['memory_total']) . '<br>';
    }
    if (strlen("{$cadd}{$madd}") > 0) {
        $titl = '- Server Info -<br><br>';
        $line = '<br>- Performance Results -<br><br>';
    }

    // Results
    $_tmp = array(
        'name'  => 'result',
        'type'  => 'hidden',
        'value' => jrCore_url_encode_string($_tm['p_val'])
    );
    jrCore_form_field_create($_tmp);

    // Performance ID
    $_tmp = array(
        'name'  => 'p_id',
        'type'  => 'hidden',
        'value' => $pid
    );
    jrCore_form_field_create($_tmp);

    // Hardware
    if (strlen($cadd) > 0) {
        $_tmp = array(
            'name'  => 'p_cpu',
            'type'  => 'hidden',
            'value' => $_cpu[1]['mhz']
        );
        jrCore_form_field_create($_tmp);
    }
    if (strlen($madd) > 0) {
        $_tmp = array(
            'name'  => 'p_mem',
            'type'  => 'hidden',
            'value' => jrCore_format_size($_mem['memory_total'])
        );
        jrCore_form_field_create($_tmp);
    }

    // Provider
    $_tmp = array(
        'name'     => 'provider',
        'label'    => 'Provider Name',
        'help'     => 'Enter the name of your Hosting Provider',
        'type'     => 'text',
        'validate' => 'not_empty',
        'section'  => 'required information',
        'required' => true,
        'value'    => (isset($_tm['p_provider']) && strlen($_tm['p_provider']) > 0) ? jrCore_entity_string($_tm['p_provider']) : ''
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_custom("<div class=\"success p20\" style=\"font-family:monospace\">{$titl}{$cadd}{$madd}{$line}&nbsp;&nbsp;<b>Processor:</b> {$_pr['cpu']}<br>&nbsp;&nbsp;&nbsp;<b>Database:</b> {$_pr['db']}<br>&nbsp;<b>Filesystem:</b> {$_pr['fs']}<br><b>Total Score:</b> {$tot}</div>", 'Results To Share');

    // Comment
    $_tmp = array(
        'name'     => 'comment',
        'label'    => 'Result Comments',
        'sublabel' => '(max 512 characters)',
        'help'     => 'If you would like to provide a short Comment or Review of your Provider or Test Results, enter it here and it will be shared.',
        'type'     => 'textarea',
        'validate' => 'not_empty',
        'section'  => 'optional',
        'required' => false,
        'value'    => (isset($_tm['p_comment']) && strlen($_tm['p_comment']) > 0) ? jrCore_entity_string($_tm['p_comment']) : ''
    );
    jrCore_form_field_create($_tmp);

    // Price
    $_val = array(
        0 => '-',
        1 => 'Under &#36;10',
        2 => '&#36;10 to &#36;25',
        3 => '&#36;25 to &#36;50',
        4 => '&#36;50 to &#36;100',
        5 => 'Over &#36;100'
    );
    $_tmp = array(
        'name'     => 'price',
        'label'    => 'Monthly Hosting Cost',
        'help'     => 'How much do you pay <strong>monthly</strong> for your Jamroom hosting account?',
        'type'     => 'select',
        'options'  => $_val,
        'default'  => 0,
        'validate' => 'number_nz',
        'required' => false,
        'value'    => (isset($_tm['p_price']) && is_numeric($_tm['p_price'])) ? intval($_tm['p_price']) : 0
    );
    jrCore_form_field_create($_tmp);

    // Type
    $_val = array(
        'none'      => '-',
        'shared'    => 'Shared Hosting',
        'reseller'  => 'Reseller Hosting',
        'vps'       => 'VPS (Virtual Private Server)',
        'dedicated' => 'Dedicated Server',
        'cloud'     => 'Cloud Provider (Amazon Web Services, Rackspace Cloud, Heroku, etc.)'
    );
    $_tmp = array(
        'name'     => 'type',
        'label'    => 'Hosting Type',
        'help'     => 'What type of Hosting Account do you run Jamroom on?  Select the account type that fits your hosting account the closest.',
        'type'     => 'select',
        'options'  => $_val,
        'default'  => 'none',
        'validate' => 'core_string',
        'required' => false,
        'value'    => (isset($_tm['p_type']) && strlen($_tm['p_type']) > 0) ? $_tm['p_type'] : 'none'
    );
    jrCore_form_field_create($_tmp);

    // Rating
    $_val = array(
        0 => '-',
        5 => 'Excellent',
        4 => 'Above Average',
        3 => 'Average',
        2 => 'Below Average',
        1 => 'Poor'
    );
    $_tmp = array(
        'name'     => 'rating',
        'label'    => 'Provider Rating',
        'help'     => 'How would you rate the value of the service you receive from your provider?',
        'type'     => 'select',
        'options'  => $_val,
        'default'  => 0,
        'validate' => 'number_nz',
        'required' => false,
        'value'    => (isset($_tm['p_rating']) && is_numeric($_tm['p_rating'])) ? intval($_tm['p_rating']) : 0
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// performance_share_save
//------------------------------
function view_jrCore_performance_share_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure performance results exist
    $pid = intval($_post['p_id']);
    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} WHERE p_id = '{$pid}' LIMIT 1";
    $_tm = jrCore_db_query($req, 'SINGLE');
    if (!$_tm || !is_array($_tm)) {
        jrCore_set_form_notice('error', 'Unable to find results to share - please try again');
        jrCore_location('referrer');
    }

    // Save our updated results
    $prv = jrCore_db_escape($_post['provider']);
    $cmt = jrCore_db_escape($_post['comment']);
    $typ = jrCore_db_escape($_post['type']);
    $prc = (int) $_post['price'];
    $rtg = (int) $_post['rating'];
    $req = "UPDATE {$tbl} SET p_provider = '{$prv}', p_comment = '{$cmt}', p_price = '{$prc}', p_rating = '{$rtg}', p_type = '{$typ}' WHERE p_id = '{$pid}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_set_form_notice('error', 'Unable to save share results - please try again');
        jrCore_location('referrer');
    }

    // Active Marketplace info
    $_mk = jrMarket_get_active_release_system();

    // Our payload
    $_up = array(
        'payload' => array(
            'system_id' => $_mk['system_code'],
            'result'    => $_post['result'],
            'provider'  => jrCore_strip_html($_post['provider']),
            'price'     => intval($_post['price']),
            'comment'   => jrCore_strip_html($_post['comment']),
            'rating'    => intval($_post['rating']),
            'type'      => $_post['type'],
            'cpu'       => floatval($_post['p_cpu']),
            'mem'       => $_post['p_mem'],
        )
    );
    $_up['payload'] = jrCore_url_encode_string(json_encode($_up['payload']));
    $_rs = jrCore_load_url("http://www.jamroom.net/networkperformance/submit", $_up, 'POST');
    if (!$_rs || strlen($_rs) < 3) {
        jrCore_set_form_notice('error', 'Unable to share results to server - please try again');
        jrCore_location('referrer');
    }
    $_rs = json_decode($_rs, true);
    if (!$_rs || !is_array($_rs)) {
        jrCore_set_form_notice('error', 'Invalid results received from share server - please try again');
        jrCore_location('referrer');
    }
    if (isset($_rs['error'])) {
        jrCore_set_form_notice('error', $_rs['error']);
    }
    else {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', $_rs['success']);
    }
    jrCore_set_temp_value('jrCore', 'performance_shared', 1);
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_share");
}

//------------------------------
// performance_check_save
//------------------------------
function view_jrCore_performance_check_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_run_performance_check();
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_check");
}

//------------------------------
// system_check
//------------------------------
function view_jrCore_system_check($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    $tmp = jrCore_page_button('pcheck', 'performance check', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_check');");
    jrCore_page_banner('system check', $tmp);
    jrCore_get_form_notice();

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    jrCore_page_section_header('core');

    $dat = array();
    $dat[1]['title'] = 'checked';
    $dat[1]['width'] = '20%';
    $dat[2]['title'] = 'value';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'result';
    $dat[3]['width'] = '8%';
    $dat[4]['title'] = 'note';
    $dat[4]['width'] = '47%';
    jrCore_page_table_header($dat);

    // Get our core version from file and compare to what's in the DB
    $_mta = jrCore_module_meta_data('jrCore');
    $dat = array();
    $dat[1]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info\">{$_mods['jrCore']['module_name']}</a>";
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = $_mta['version'];
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = $_mods['jrCore']['module_version'];
    if ($_mta['version'] != $_mods['jrCore']['module_version']) {
        $dat[3]['title'] = $fail;
        $dat[4]['title'] .= '&nbsp;&nbsp;&nbsp;<a href="'. $_conf['jrCore_base_url'] .'/'. jrCore_get_module_url('jrCore') .'/integrity_check"><strong><u>Integrity Check Required!</u></strong>';
    }
    jrCore_page_table_row($dat);

    // Server
    $dat = array();
    $dat[1]['title'] = 'Server OS';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = @php_uname();
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'Linux or Mac OS X based server.';
    jrCore_page_table_row($dat);

    // Web Server
    $dat = array();
    $dat[1]['title'] = 'Web Server';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = php_sapi_name();
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'Apache Web Server required.';
    jrCore_page_table_row($dat);

    // PHP Version
    $result = $fail;
    if (version_compare(phpversion(), '5.3.0') != -1) {
        $result = $pass;
    }
    $dat = array();
    $dat[1]['title'] = 'PHP Version';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = phpversion() . ' <a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/phpinfo" target="_blank">[phpinfo]</a>';
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $result;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'PHP 5.3+ required.';
    jrCore_page_table_row($dat);

    // MySQL Version
    $msi = jrCore_db_connect();
    $ver = mysqli_get_server_info($msi);
    $result = $pass;
    if (strpos($ver, '3.') === 0 || strpos($ver, '4.') === 0) {
        $result = $fail;
    }
    $dat = array();
    $dat[1]['title'] = 'MySQL Version';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = $ver;
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $result;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'MySQL 5.0+ required, 5.1.51+ recommended.';
    jrCore_page_table_row($dat);

    // Disabled Functions
    $dis_funcs = ini_get('disable_functions');
    if (isset($dis_funcs) && $dis_funcs != '') {
        $dis_funcs = explode(',', $dis_funcs);
        if (isset($dis_funcs) && is_array($dis_funcs)) {
            foreach ($dis_funcs as $k => $fnc) {
                // We don't care about disabled process control functions
                $fnc = trim($fnc);
                if (strlen($fnc) === 0 || strpos($fnc, 'pcntl') === 0) {
                    unset($dis_funcs[$k]);
                }
                // Other functions we do not care about as Jamroom does not use them
                switch ($fnc) {
                    case 'dl':
                        unset($dis_funcs[$k]);
                        break;
                }
            }
        }
        if (isset($dis_funcs) && count($dis_funcs) > 0) {
            $dis_funcs = implode('<br>', $dis_funcs);
            $result = $fail;

            $dat = array();
            $dat[1]['title'] = 'Disabled Functions';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $dis_funcs;
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $result;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = 'Disabled PHP Functions can impact system functionality.';
            jrCore_page_table_row($dat);
        }
    }

    // FFMPeg install
    $dat = array();
    $dat[1]['title'] = 'FFMpeg binary';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'executable';
    $dat[2]['class'] = 'center';
    if ($ffmpeg = jrCore_check_ffmpeg_install(false)) {

        $dir = jrCore_get_module_cache_dir('jrCore');
        $tmp = tempnam($dir, 'system_check_');
        ob_start();
        system("nice -n 9 {$ffmpeg} >{$tmp} 2>&1", $ret);
        ob_end_clean();
        if (is_file($tmp) && strpos(file_get_contents($tmp), 'usage: ffmpeg')) {
            $dat[3]['title'] = $pass;
            $dat[4]['title'] = str_replace(APP_DIR .'/', '', $ffmpeg) .' is executable by web user';
        }
        else {
            $dat[3]['title'] = $fail;
            $dat[4]['title'] = str_replace(APP_DIR .'/', '', $ffmpeg) .' is not functioning properly';
        }
        unlink($tmp);
    }
    else {
        $dat[3]['title'] = $fail;
        $dat[4]['title'] = str_replace(APP_DIR .'/', '', $ffmpeg) .' is not executable';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);

    // DIFF install
    $dat = array();
    $dat[1]['title'] = 'diff binary';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'executable';
    $dat[2]['class'] = 'center';
    if ($diff = jrCore_get_diff_binary()) {
        $dat[3]['title'] = $pass;
        $dat[4]['title'] = str_replace(APP_DIR .'/', '', $diff) .' is executable by web user';
    }
    else {
        $dat[3]['title'] = $fail;
        $dat[4]['title'] = 'diff binary is not executable by web user<br>modules/jrCore/tools/diff';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);

    // Directories
    $_to_check = array('cache', 'logs', 'media');
    $_bad = array();
    foreach ($_to_check as $dir) {
        if (!is_dir(APP_DIR . "/data/{$dir}")) {
            // See if we can create it
            if (!mkdir(APP_DIR . "/data/{$dir}", $_conf['jrCore_dir_perms'], true)) {
                $_bad[] = "data/{$dir} does not exist";
            }
        }
        elseif (!is_writable(APP_DIR . "/data/{$dir}")) {
            chmod(APP_DIR . "/data/{$dir}", $_conf['jrCore_dir_perms']);
            if (!is_writable(APP_DIR . "/data/{$dir}")) {
                $_bad[] = "data/{$dir} is not writable";
            }
        }
    }
    if (isset($_bad) && is_array($_bad) && count($_bad) > 0) {
        $note = 'All directories <strong>must be writable</strong> by web user!';
        $dirs = implode('<br>', $_bad);
        $result = $fail;
    }
    else {
        $note = 'All directories are writable';
        $dirs = 'all writable';
        $result = $pass;
    }
    $dat = array();
    $dat[1]['title'] = 'Data Directories';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = $dirs;
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $result;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = $note;
    jrCore_page_table_row($dat);

    $upl = jrCore_get_max_allowed_upload();
    $dat = array();
    $dat[1]['title'] = 'Max Upload';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = jrCore_format_size($upl);
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = ($upl <= 2097152) ? $fail : $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = ($upl <= 2097152) ? 'increase post_max_size and upload_max_filesize in your php.ini to allow larger uploads' : 'post_max_size and upload_max_filesize are set properly';
    $dat[4]['title'] .= '<br><a href="https://www.jamroom.net/the-jamroom-network/documentation/problems/748/how-do-i-increase-phps-upload-limit" target="_blank"><u>View the FAQ on increasing your upload size</u></a>';

    jrCore_page_table_row($dat);

    // Apache rlimits
    if (function_exists('posix_getrlimit')) {
        $_rl = posix_getrlimit();

        // Apache RlimitMEM
        if ((jrCore_checktype($_rl['soft totalmem'], 'number_nz') && $_rl['soft totalmem'] < 67108864) || (jrCore_checktype($_rl['hard totalmem'], 'number_nz') && $_rl['hard totalmem'] < 67108864)) {
            $apmem = $_rl['soft totalmem'];
            if (jrCore_checktype($_rl['hard totalmem'], 'number_nz') && $_rl['hard totalmem'] < $_rl['soft totalmem']) {
                $apmem = $_rl['hard totalmem'];
            }
            $show = (($apmem / 1024) / 1024);
            $dat = array();
            $dat[1]['title'] = 'Apache Memory Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $show . 'MB';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the  memory you can use - this could cause issues, especially when doing Media Conversions. Apache Memory Limits are put in place by your hosting provider, and cannot be modified - contact your hosting provider and have them increase the limit, or set it to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }
        // Apache RlimitCPU
        if (jrCore_checktype($_rl['soft cpu'], 'number_nz') && $_rl['soft cpu'] < 20) {
            $dat = array();
            $dat[1]['title'] = 'Apache Soft CPU Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_rl['soft cpu'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the amount of CPU you can use - this could cause issues, especially when doing Media Conversions. Apache CPU Limits are put in place by your hosting provider, and cannot be modified - you will want to contact your hosting provider and have them set the soft cpu limit to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }
        elseif (jrCore_checktype($_rl['hard cpu'], 'number_nz') && $_rl['hard cpu'] < 40) {
            $dat = array();
            $dat[1]['title'] = 'Apache Hard CPU Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_rl['hard cpu'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the amount of CPU you can use - this could cause issues, especially when doing Media Conversions. Apache CPU Limits are put in place by your hosting provider, and cannot be modified - you will want to contact your hosting provider and have them set the soft cpu limit to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }

        // Apache RlimitNPROC
        if ((jrCore_checktype($_rl['soft maxproc'], 'number_nz') && $_rl['soft maxproc'] < 200) || (jrCore_checktype($_rl['hard maxproc'], 'number_nz') && $_rl['hard maxproc'] < 200)) {
            $approc = $_rl['soft maxproc'];
            if (jrCore_checktype($_rl['hard maxproc'], 'number_nz') && $_rl['hard maxproc'] < $_rl['soft maxproc']) {
                $approc = $_rl['hard maxproc'];
            }
            $dat = array();
            $dat[1]['title'] = 'Apache Process Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $approc;
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the amount of Processes you can use - this could cause issues, especially when doing Media Conversions. Apache PROC Limits are put in place by your hosting provider, and cannot be modified - you will want to contact your hosting provider and have them set the soft and hard maxproc limits to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }
    }
    jrCore_page_table_footer();

    jrCore_page_section_header('modules');

    $dat = array();
    $dat[1]['title'] = 'checked';
    $dat[1]['width'] = '20%';
    $dat[2]['title'] = 'value';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'result';
    $dat[3]['width'] = '8%';
    $dat[4]['title'] = 'note';
    $dat[4]['width'] = '47%';
    jrCore_page_table_header($dat);

    // Go through installed modules
    foreach ($_mods as $mod => $_inf) {
        if ($mod == 'jrCore' || !jrCore_module_is_active($mod)) {
            continue;
        }
        // Check if this module requires other modules to function - make sure they exist and are activated
        if (isset($_inf['module_requires']{1})) {
            $_req = explode(',', $_inf['module_requires']);
            if (is_array($_req)) {
                foreach ($_req as $rmod) {
                    // See if we have been given an explicit version - i.e. jrImage:1.1.5
                    if (strpos($rmod,':')) {
                        list($rmod, $vers) = explode(':', $rmod);
                        $rmod = trim($rmod);
                        $vers = trim($vers);
                    }
                    else {
                        $rmod = trim($rmod);
                        $vers = '0.0.0';
                    }
                    if (!is_dir(APP_DIR ."/modules/{$rmod}")) {
                        $dat = array();
                        $dat[1]['title'] = $_mods[$mod]['module_name'];
                        $dat[1]['class'] = 'center';
                        $dat[2]['title'] = 'required module: ' . $rmod;
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $fail;
                        $dat[3]['class'] = 'center';
                        $dat[4]['title'] = "<strong>{$rmod}</strong> module is missing";
                        jrCore_page_table_row($dat);
                    }
                    elseif (!jrCore_module_is_active($rmod)) {
                        $dat = array();
                        $dat[1]['title'] = $_mods[$mod]['module_name'];
                        $dat[1]['class'] = 'center';
                        $dat[2]['title'] = 'required module: ' . $rmod;
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $fail;
                        $dat[3]['class'] = 'center';
                        $dat[4]['title'] = "<strong>{$rmod}</strong> module is not active";
                        jrCore_page_table_row($dat);
                    }
                    elseif (version_compare($_mods[$rmod]['module_version'], $vers, '<')) {
                        $dat = array();
                        $dat[1]['title'] = $_inf['module_name'];
                        $dat[1]['class'] = 'center';
                        if ($vers != '0.0.0') {
                            $dat[2]['title'] = 'required module: ' . $rmod .' '. $vers;
                        }
                        else {
                            $dat[2]['title'] = 'required module: ' . $rmod;
                        }
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $fail;
                        $dat[3]['class'] = 'center';
                        $dat[4]['title'] = "<strong>{$rmod}</strong> version {$vers} required (current: {$_inf["{$rmod}"]['module_version']})";
                        jrCore_page_table_row($dat);
                    }
                }
            }
        }
        // See if this module has any additional checks to add
        $_inf['pass'] = $pass;
        $_inf['fail'] = $fail;
        jrCore_trigger_event('jrCore', 'system_check', array(), $_inf, $mod);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// phpinfo
//------------------------------
function view_jrCore_phpinfo($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (function_exists('phpinfo')) {
        phpinfo();
        exit;
    }
    jrCore_notice_page('error', 'The phpinfo() function has been disabled in your install');
}

//------------------------------
// skin_menu
//------------------------------
function view_jrCore_skin_menu($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');

    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl} ORDER BY menu_order ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    jrCore_page_banner('skin menu');
    jrCore_set_form_notice('notice', 'Menu Entries marked with a * are dynamic and may (or may not) show depending on the User');
    jrCore_get_form_notice();

    $_lang = jrUser_load_lang_strings();

    $dat = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '5%;';
    $dat[2]['title'] = 'module';
    $dat[2]['width'] = '10%;';
    $dat[3]['title'] = 'label';
    $dat[3]['width'] = '25%;';
    $dat[4]['title'] = 'category';
    $dat[4]['width'] = '15%;';
    $dat[5]['title'] = 'URL';
    $dat[5]['width'] = '20%;';
    $dat[6]['title'] = 'active';
    $dat[6]['width'] = '5%;';
    $dat[7]['title'] = 'groups';
    $dat[7]['width'] = '10%;';
    $dat[8]['title'] = 'modify';
    $dat[8]['width'] = '5%;';
    $dat[9]['title'] = 'action';
    $dat[9]['width'] = '5%;';
    jrCore_page_table_header($dat);

    if (isset($_rt) && is_array($_rt)) {

        // let's make sure these are sanely ordered
        $_od = array();
        $_sn = array();
        $ord = false;
        foreach ($_rt as $_v) {
            $_od["{$_v['menu_id']}"] = $_v['menu_order'];
            if (isset($_sn["{$_v['menu_order']}"])) {
                $ord = true;
            }
            $_sn["{$_v['menu_order']}"] = 1;
        }
        if ($ord) {
            asort($_od, SORT_NUMERIC);
            $req = "UPDATE {$tbl} SET menu_order = CASE menu_id\n";
            $num = 100;
            foreach ($_od as $mid => $mord) {
                $req .= "WHEN {$mid} THEN {$num}\n";
                $num++;
            }
            $req .= "ELSE menu_id END";
            jrCore_db_query($req);

            // Refresh
            $req = "SELECT * FROM {$tbl} ORDER BY menu_order ASC";
            $_rt = jrCore_db_query($req, 'NUMERIC');
        }
        unset($_od, $_sn, $ord);

        $top = 0;
        $_qt = jrProfile_get_quotas();
        foreach ($_rt as $k => $_v) {
            $dat = array();
            if (isset($k) && $k > 0) {
                $dat[1]['title'] = jrCore_page_button("u{$k}", '^', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_move_save/id={$_v['menu_id']}/top={$top}')");
            }
            else {
                $dat[1]['title'] = '&nbsp;';
            }
            $top = $_v['menu_id'];
            if (isset($_v['menu_module']) && isset($_mods["{$_v['menu_module']}"])) {
                $dat[2]['title'] = $_v['menu_module'];
            }
            else {
                $dat[2]['title'] = '-';
            }
            $dat[2]['class'] = 'center';
            if (isset($_lang["{$_v['menu_module']}"]["{$_v['menu_label']}"])) {
                $dat[3]['title'] = $_lang["{$_v['menu_module']}"]["{$_v['menu_label']}"] . ' (id: ' . $_v['menu_label'] . ')';
            }
            else {
                $dat[3]['title'] = $_v['menu_label'];
            }
            if (isset($_v['menu_function']{1})) {
                $dat[3]['title'] .= '&nbsp;*';
            }
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_v['menu_category'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_v['menu_action'];
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = (isset($_v['menu_active']) && $_v['menu_active'] === 'on') ? '<strong>yes</strong>' : 'no';
            $dat[6]['class'] = 'center';
            if (strpos($_v['menu_groups'], ',')) {
                $_ot = array();
                foreach (explode(',', $_v['menu_groups']) as $grp) {
                    if (isset($grp) && is_numeric($grp) && isset($_qt[$grp])) {
                        $_ot[] = $_qt[$grp];
                    }
                    else {
                        $_ot[] = $grp;
                    }
                }
                $dat[7]['title'] = implode('<br>', $_ot);
            }
            else {
                $dat[7]['title'] = $_v['menu_groups'];
            }
            $dat[7]['class'] = 'center';
            $dat[8]['title'] = jrCore_page_button("m{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_modify/id={$_v['menu_id']}')");

            // We can only delete entries that we have created
            if (isset($_v['menu_module']) && isset($_mods["{$_v['menu_module']}"])) {
                $dat[9]['title'] = jrCore_page_button("d{$k}", 'delete', 'disabled');
            }
            else {
                $dat[9]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this entry?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_delete_save/id={$_v['menu_id']}') }");
            }
            $dat[9]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat = array();
        $dat[1]['title'] = '<p>There are no custom skin menu entries</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    $_tmp = array(
        'submit_value' => 'create new entry',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools"
    );
    jrCore_form_create($_tmp);

    // New Menu Entry
    $_tmp = array(
        'name'     => 'new_menu_label',
        'label'    => 'new menu label',
        'help'     => 'Enter the label you would like to appear on this new Menu Entry.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// skin_menu_save
//------------------------------
function view_jrCore_skin_menu_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "INSERT INTO {$tbl} (menu_module,menu_active,menu_label,menu_order)
            VALUES ('CustomEntry','0','" . jrCore_db_escape($_post['new_menu_label']) . "',100)";
    $mid = jrCore_db_query($req, 'INSERT_ID');
    if (isset($mid) && jrCore_checktype($mid, 'number_nz')) {
        jrCore_delete_all_cache_entries();
        jrCore_set_form_notice('success', 'The new menu item was successfully created');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_modify/id={$mid}");
    }
    jrCore_set_form_notice('error', 'Unable to create new menu entry in database - please try again');
    jrCore_form_result();
}

//------------------------------
// skin_menu_move_save
//------------------------------
function view_jrCore_skin_menu_move_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    if (!isset($_post['top']) || !jrCore_checktype($_post['top'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid top id - please try again');
        jrCore_location('referrer');
    }
    $pid = (int) $_post['id'];
    $tid = (int) $_post['top'];
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl} WHERE menu_id IN('{$pid}','{$tid}')";
    $_rt = jrCore_db_query($req, 'menu_id');
    if (isset($_rt) && is_array($_rt)) {
        if (!isset($_rt[$pid])) {
            jrCore_set_form_notice('error', 'invalid menu_id - please try again');
            jrCore_location('referrer');
        }
        if (!isset($_rt[$tid])) {
            jrCore_set_form_notice('error', 'invalid top id - please try again');
            jrCore_location('referrer');
        }
        // Move Up
        if ($_rt[$pid]['menu_order'] == $_rt[$tid]['menu_order']) {
            $ord = $_rt[$tid]['menu_order'] - 1;
        }
        else {
            $ord = $_rt[$tid]['menu_order'];
        }
        $req = "UPDATE {$tbl} SET menu_order = '{$ord}' WHERE menu_id = '{$pid}' LIMIT 1";
        jrCore_db_query($req);

        $ord = $_rt[$pid]['menu_order'];
        $req = "UPDATE {$tbl} SET menu_order = '{$ord}' WHERE menu_id = '{$tid}' LIMIT 1";
        jrCore_db_query($req);
    }
    jrCore_delete_all_cache_entries();
    jrCore_location('referrer');
    return true;
}

//------------------------------
// skin_menu_disable_save
//------------------------------
function view_jrCore_skin_menu_disable_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "UPDATE {$tbl} SET menu_active = 'off' WHERE menu_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The menu item was successfully disabled');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to disable menu entry in database - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// skin_menu_delete_save
//------------------------------
function view_jrCore_skin_menu_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "DELETE FROM {$tbl} WHERE menu_id = '{$_post['id']}' AND menu_module = 'CustomEntry' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The menu item was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to delete menu entry from database - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// skin_menu_modify
//------------------------------
function view_jrCore_skin_menu_modify($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    // Get info
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl}";
    $_me = jrCore_db_query($req, 'NUMERIC');

    $_rt = array();
    $_ct = array();
    foreach ($_me as $_v) {
        if (isset($_v['menu_id']) && $_v['menu_id'] == $_post['id']) {
            $_rt = $_v;
        }
        if (isset($_v['menu_category']) && strlen($_v['menu_category']) > 0) {
            $_ct["{$_v['menu_category']}"] = $_v['menu_category'];
        }
    }
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('modify menu entry');

    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu",
        'values'           => $_rt,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Label
    $_tmp = array(
        'name'     => 'menu_label',
        'label'    => 'label',
        'help'     => 'This is the text that will appear as the label for the menu entry.<br><br><strong>Note:</strong> You can enter a language index ID here to use a language entry in place of a text label.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Category
    $_tmp = array(
        'name'     => 'menu_category',
        'label'    => 'category',
        'help'     => 'If your skin menu supports grouping menu entries into categories, you can enter the category for this link here.',
        'type'     => 'select_and_text',
        'options'  => $_ct,
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // URL
    if (isset($_rt['menu_module']) && $_rt['menu_module'] == 'CustomEntry') {
        $_pt = array();
        if (jrCore_module_is_active('jrPage')) {
            $_sp = array(
                'search'   => array(
                    'page_location = 0'
                ),
                'return_keys' => array( 'page_title', 'page_title_url' ),
                'order_by' => array(
                    'page_title' => 'asc'
                ),
                'skip_triggers' => true,
                'limit'    => 250
            );
            $_pg = jrCore_db_search_items('jrPage', $_sp);
            if (isset($_pg) && is_array($_pg) && isset($_pg['_items']) && is_array($_pg['_items'])) {
                $purl = jrCore_get_module_url('jrPage');
                foreach ($_pg['_items'] as $_page) {
                    $_pt["{$purl}/{$_page['_item_id']}/{$_page['page_title_url']}"] = $_page['page_title'];
                }
            }
            // If we have a custom URL, insert int
            if (isset($_rt['menu_action']) && strlen($_rt['menu_action']) > 0) {
                $_pt["{$_rt['menu_action']}"] = $_rt['menu_action'];
            }
        }
        if (isset($_pt) && is_array($_pt) && count($_pt) > 0) {
            $_tmp = array(
                'name'     => 'menu_action',
                'label'    => 'linked URL',
                'help'     => 'This is the module/view or page that will be loaded when the menu item is clicked on',
                'type'     => 'select_and_text',
                'options'  => $_pt,
                'validate' => 'printable',
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }
        else {
            $_tmp = array(
                'name'     => 'menu_action',
                'label'    => 'linked URL',
                'help'     => 'This is the module/view or page that will be loaded when the menu item is clicked on',
                'type'     => 'text',
                'validate' => 'printable',
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }
    }

    // Group
    $_grp = array(
        'all'     => 'Everyone',
        'master'  => 'Master Admins',
        'admin'   => 'Admin Users',
        'power'   => 'Power Users',
        'multi'   => 'Multi Profile Users',
        'user'    => 'Users Only (logged in)',
        'visitor' => 'Visitors Only (not logged in)'
    );
    $_qt = jrProfile_get_quotas();
    if (isset($_qt) && is_array($_qt)) {
        foreach ($_qt as $qid => $qname) {
            $_grp[$qid] = "Quota: {$qname}";
        }
    }
    $_tmp = array(
        'name'     => 'menu_groups',
        'label'    => 'visible to',
        'sublabel' => 'select multiple',
        'help'     => 'Select the group(s) of users that will be able to see this menu entry.',
        'type'     => 'select_multiple',
        'options'  => $_grp,
        'required' => true,
        'size'     => count($_grp)
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'menu_active',
        'label'    => 'active',
        'help'     => 'Is this menu entry active?',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'on',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// skin_menu_modify_save
//------------------------------
function view_jrCore_skin_menu_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_form_result('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl} WHERE menu_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_form_result('referrer');
    }

    // Update...
    $cat = jrCore_db_escape($_post['menu_category']);
    $act = '';
    if (isset($_rt['menu_module']) && $_rt['menu_module'] == 'CustomEntry') {
        $sav = jrCore_db_escape($_post['menu_action']);
        $act = "menu_unique = '{$sav}',menu_action = '{$sav}',";

        // Make sure we are unique...
        $req = "SELECT * FROM {$tbl} WHERE menu_module = 'CustomEntry' AND menu_category = '{$cat}' AND menu_action = '{$sav}' AND menu_id != '{$_post['id']}' LIMIT 1";
        $_ex = jrCore_db_query($req, 'SINGLE');
        if ($_ex && is_array($_ex)) {
            jrCore_set_form_notice('error', 'There is already a menu entry using the Category and Linked URL - please enter something different');
            jrCore_form_result('referrer');
        }
    }
    $req = "UPDATE {$tbl} SET
              menu_label    = '" . jrCore_db_escape($_post['menu_label']) . "',{$act}
              menu_category = '{$cat}',
              menu_groups   = '" . jrCore_db_escape(implode(',', $_post['menu_groups'])) . "',
              menu_active   = '" . jrCore_db_escape($_post['menu_active']) . "'
             WHERE menu_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!isset($cnt) || $cnt !== 1) {
        jrCore_set_form_notice('error', 'Error updating menu entry in the database - please try again');
    }
    else {
        jrCore_set_form_notice('success', 'The menu entry was successfully updated');
        jrCore_form_delete_session();
    }
    jrCore_form_result('referrer');
}

//------------------------------
// search
//------------------------------
function view_jrCore_search($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();

    if (isset($_post['sa']) && $_post['sa'] = 'skin') {
        jrCore_page_admin_tabs($_post['skin'], 'global');
    }
    else {
        jrCore_page_admin_tabs('jrCore', 'global');
    }

    if (!isset($_post['ss']) || strlen($_post['ss']) === 0) {
        jrCore_page_banner('search results');
        jrCore_set_form_notice('error', 'You forgot to enter a search string');
        jrCore_get_form_notice();
    }
    else {
        $fnd = false;
        $src = jrCore_db_escape($_post['ss']);

        // Check if we are searching modules or skins
        $_mi = array();
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        if (isset($_post['sa']) && $_post['sa'] = 'skin') {
            $_sk = jrCore_get_skins();
            $req = "SELECT * FROM {$tbl} WHERE (`module` LIKE '%{$src}%' OR `name` LIKE '%{$src}%' OR `label` LIKE '%{$src}%') AND `type` != 'hidden' AND module IN('" . implode("','", $_sk) . "') ORDER BY `label` ASC";
            // See if we have matching skins
            $_ms = jrCore_get_skins();
            if ($_ms && is_array($_ms)) {
                foreach ($_ms as $sd => $sn) {
                    $_mt = jrCore_skin_meta_data($sd);
                    if (stripos(' ' . $sd, $_post['ss']) || (isset($_mt['title']) && stripos(' ' . $_mt['title'], $_post['ss']))) {
                        $_mi[$sd] = (isset($_mt['title'])) ? $_mt['title'] : $sd;
                    }
                }
            }
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE (`module` LIKE '%{$src}%' OR `name` LIKE '%{$src}%' OR `label` LIKE '%{$src}%') AND `type` != 'hidden' AND module IN('" . implode("','", array_keys($_mods)) . "') ORDER BY `label` ASC";
            foreach ($_mods as $sd => $sn) {
                if (stripos(' ' . $sd, $_post['ss']) || stripos(' ' . $sn['module_name'], $_post['ss'])) {
                    $_mi[$sd] = $sn['module_name'];
                }
            }
        }
        $_cf = jrCore_db_query($req, 'NUMERIC');

        if ((isset($_cf) && is_array($_cf)) || count($_mi) > 0) {
            $fnd = true;
            jrCore_page_banner("search results for &quot;" . jrCore_entity_string($_post['ss']) . '&quot;');
        }

        // Show matching modules or skins
        if (count($_mi) > 0) {
            if (isset($_post['sa']) && $_post['sa'] = 'skin') {
                jrCore_page_section_header('Skins');
                $tag = 'skin name';
            }
            else {
                jrCore_page_section_header('Modules');
                $tag = 'module name';
            }
            $dat = array();
            $dat[1]['title'] = 'img';
            $dat[1]['width'] = '5%';
            $dat[2]['title'] = $tag;
            $dat[2]['width'] = '85%';
            $dat[3]['title'] = 'info';
            $dat[3]['width'] = '10%';
            jrCore_page_table_header($dat);

            foreach ($_mi as $dir => $name) {
                $dat = array();
                $dat[2]['title'] = "<h3>{$name}</h3>";
                if (isset($_post['sa']) && $_post['sa'] = 'skin') {
                    $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/skins/' . $dir . '/icon.png" alt="' . $name . '" title="' . $name . '" width="48" height="48">';
                    $dat[3]['title'] = jrCore_page_button("v{$dir}", 'skin info', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_mods[$dir]['module_url']}/skin_admin/info/skin={$dir}')");
                }
                else {
                    $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/modules/' . $dir . '/icon.png" alt="' . $name  . '" title="' . $name . '" width="48" height="48">';
                    $dat[3]['title'] = jrCore_page_button("v{$dir}", 'module info', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_mods[$dir]['module_url']}/admin/info')");
                }
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
        }

        if (isset($_cf) && is_array($_cf)) {

            jrCore_page_section_header('Global Settings');

            $dat = array();
            $dat[1]['title'] = 'module';
            $dat[1]['width'] = '5%;';
            $dat[2]['title'] = 'label';
            $dat[2]['width'] = '25%;';
            $dat[3]['title'] = 'help';
            $dat[3]['width'] = '60%;';
            $dat[4]['title'] = 'modify';
            $dat[4]['width'] = '10%;';
            jrCore_page_table_header($dat);

            foreach ($_cf as $_fld) {

                $dat = array();
                if (isset($_post['sa']) && $_post['sa'] = 'skin') {
                    if (!is_dir(APP_DIR . "/skins/{$_fld['module']}")) {
                        continue;
                    }
                    $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/skins/' . $_fld['module'] . '/icon.png" alt="' . $_fld['module'] . '" title="' . $_fld['module'] . '" width="48" height="48">';
                    $dat[4]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/global/skin={$_fld['module']}/hl={$_fld['name']}#ff-{$_fld['name']}')");
                }
                else {
                    if (!is_dir(APP_DIR . "/modules/{$_fld['module']}")) {
                        continue;
                    }
                    $murl = jrCore_get_module_url($_fld['module']);
                    $name = jrCore_entity_string($_mods["{$_fld['module']}"]['module_name']);
                    $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/modules/' . $_fld['module'] . '/icon.png" alt="' . $name  . '" title="' . $name . '" width="48" height="48">';
                    $dat[4]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/admin/global/hl={$_fld['name']}#ff-{$_fld['name']}')");
                }
                $dat[1]['class'] = 'center';
                $dat[2]['title'] = '<h3>' . ucwords($_fld['label']) . '</h3>';
                $dat[3]['title'] = $_fld['help'];
                $dat[4]['class'] = 'center';
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
        }

        $tbl = jrCore_db_table_name('jrProfile', 'quota_setting');
        $req = "SELECT * FROM {$tbl} WHERE (`module` LIKE '%{$src}%' OR `name` LIKE '%{$src}%' OR `label` LIKE '%{$src}%') AND `type` != 'hidden' AND module IN('" . implode("','", array_keys($_mods)) . "') ORDER BY `label` ASC";
        $_cf = jrCore_db_query($req, 'NUMERIC');

        if (isset($_cf) && is_array($_cf)) {

            $fnd = true;
            jrCore_page_section_header('Quota Settings');

            $dat = array();
            $dat[1]['title'] = 'module';
            $dat[1]['width'] = '5%;';
            $dat[2]['title'] = 'label';
            $dat[2]['width'] = '25%;';
            $dat[3]['title'] = 'help';
            $dat[3]['width'] = '60%;';
            $dat[4]['title'] = 'modify';
            $dat[4]['width'] = '10%;';
            jrCore_page_table_header($dat);

            foreach ($_cf as $_fld) {
                if (!is_dir(APP_DIR . "/modules/{$_fld['module']}")) {
                    continue;
                }
                $dat = array();
                $nam = jrCore_entity_string($_mods["{$_fld['module']}"]['module_name']);
                $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/modules/' . $_fld['module'] . '/icon.png" alt="' . $nam . '" title="' . $nam . '" width="48" height="48">';
                $dat[1]['class'] = 'center';
                $dat[2]['title'] = '<h3>' . ucwords($_fld['label']) . '</h3>';
                $dat[3]['title'] = $_fld['help'];
                $murl = jrCore_get_module_url($_fld['module']);
                $dat[4]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/admin/quota/hl={$_fld['name']}#ff-{$_fld['name']}')");
                $dat[4]['class'] = 'center';
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
        }

        // Tools
        $_tool = jrCore_get_registered_module_features('jrCore', 'tool_view');
        $_show = array();
        if (isset($_tool) && is_array($_tool)) {
            foreach ($_tool as $tool_mod => $_tools) {
                foreach ($_tools as $view => $_inf) {
                    if (stristr($_inf[0], $_post['ss']) || stristr($_inf[1], $_post['ss'])) {
                        $fnd = true;
                        $_show[] = array(
                            'module' => $tool_mod,
                            'view'   => $view,
                            'label'  => $_inf[0],
                            'help'   => $_inf[1]
                        );
                    }
                }
            }
            if (isset($_show) && is_array($_show) && count($_show) > 0) {

                jrCore_page_section_header('Module Tools');

                $dat = array();
                $dat[1]['title'] = 'module';
                $dat[1]['width'] = '5%;';
                $dat[2]['title'] = 'tool name';
                $dat[2]['width'] = '25%;';
                $dat[3]['title'] = 'help';
                $dat[3]['width'] = '60%;';
                $dat[4]['title'] = 'view';
                $dat[4]['width'] = '10%;';
                jrCore_page_table_header($dat);

                foreach ($_show as $k => $_fld) {
                    $dat = array();
                    $nam = jrCore_entity_string($_mods["{$_fld['module']}"]['module_name']);
                    $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/modules/' . $_fld['module'] . '/icon.png" alt="' . $nam . '" title="' . $nam . '" width="48" height="48">';
                    $dat[1]['class'] = 'center';
                    $dat[2]['title'] = '<h3>' . ucwords($_fld['label']) . '</h3>';
                    $dat[3]['title'] = $_fld['help'];
                    $murl = jrCore_get_module_url($_fld['module']);
                    if (!strpos($_fld['view'], 'http')) {
                        $dat[4]['title'] = jrCore_page_button("m{$k}", 'view', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/{$_fld['view']}')");
                    }
                    else {
                        $dat[4]['title'] = jrCore_page_button("m{$k}", 'view', "jrCore_window_location('{$_fld['view']}')");
                    }
                    $dat[4]['class'] = 'center';
                    jrCore_page_table_row($dat);
                }
                jrCore_page_table_footer();
            }
        }

        if (!$fnd) {
            $dat = array();
            $dat[1]['title'] = '';
            jrCore_page_table_header($dat);
            $dat = array();
            $dat[1]['title'] = '<p>No results found to match your search</p>';
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
            jrCore_page_table_footer();
        }
    }
    jrCore_page_display();
}

//------------------------------
// license (magic)
//------------------------------
function view_jrCore_license($_post, $_user, $_conf)
{
    jrUser_master_only();
    // Check for license file
    if (!isset($_post['skin'])) {
        $_mta = jrCore_module_meta_data($_post['module']);
        jrCore_page_banner("{$_mta['name']}: license");
        $lic_file = APP_DIR . "/modules/{$_post['module']}/license.html";
    }
    else {
        $_mta = jrCore_skin_meta_data($_post['skin']);
        jrCore_page_banner("{$_mta['name']}: license");
        $lic_file = APP_DIR . "/skins/{$_post['skin']}/license.html";
    }
    if (is_file($lic_file)) {
        $temp = file_get_contents($lic_file);
        jrCore_page_custom($temp);
    }
    else {
        jrCore_set_form_notice('error', 'NO LICENSE FILE FOUND - contact developer');
        jrCore_get_form_notice();
    }
    jrCore_page_close_button();
    jrCore_page_set_meta_header_only();
    jrCore_page_display();
}

//------------------------------
// dashboard_panels
//------------------------------
function view_jrCore_dashboard_panels($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    list(, $row, $col) = explode('-', $_post['_1']);
    $one = (int) $row + 1;
    $two = (int) $col + 1;

    // See if we have an existing function
    $_cfg = array();
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
    }
    $func = false;
    if (isset($_cfg['_panels'][$row][$col]['f'])) {
        $func = "{$_cfg['_panels'][$row][$col]['f']}|{$_cfg['_panels'][$row][$col]['t']}";
    }

    jrCore_page_banner('Dashboard Panels', "row {$one}, column {$two}");
    $_fnc = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'dashboard_panel');
    if ($_tmp) {
        foreach ($_tmp as $mod => $_opts) {
            $nam = $_mods[$mod]['module_name'];
            if (!isset($_fnc[$nam])) {
                $_fnc[$nam] = array();
            }
            foreach ($_opts as $title => $fnc) {
                $key = "{$fnc}|{$title}";
                $_fnc[$nam][$key] = $title;
            }
        }
    }
    $_tmp = array(
        'name'     => 'panel',
        'label'    => 'available panels',
        'help'     => 'Select the panel you would like to appear in this dashboard location',
        'type'     => 'select',
        'options'  => $_fnc,
        'value'    => $func,
        'onchange' => "jrCore_set_dashboard_panel({$row}, {$col}, $(this).val());",
        'required' => true,
        'size'     => 8
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_close_button('$.modal.close();');
    jrCore_page_set_no_header_or_footer();
    jrCore_page_display();
}

//------------------------------
// set_dashboard_panel
//------------------------------
function view_jrCore_set_dashboard_panel($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_cfg = array();
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
    }
    if (!isset($_cfg['_panels'])) {
        $_cfg['_panels'] = array();
    }
    $row  = (int) $_post['row'];
    $col  = (int) $_post['col'];
    $_tmp = jrCore_get_registered_module_features('jrCore', 'dashboard_panel');
    $name = false;
    $func = false;
    if ($_tmp) {
        foreach ($_tmp as $mod => $_opts) {
            foreach ($_opts as $title => $fnc) {
                $key = "{$fnc}|{$title}";
                if ($key == $_post['opt']) {
                    $name = $title;
                    $func = $fnc;
                    break;
                }
            }
        }
    }
    if (!$func) {
        // Check for generic DS function
        if (strpos($_post['opt'], 'item count')) {
            list($func, $name) = explode('|', $_post['opt']);
        }
    }
    $_cfg['_panels'][$row][$col] = array('t' => $name, 'f' => $func);
    ksort($_cfg['_panels'], SORT_NUMERIC);
    jrCore_set_setting_value('jrCore', 'dashboard_config', json_encode($_cfg));
    jrCore_delete_config_cache();
    $_rp = array('success' => 'OK');
    jrCore_json_response($_rp);
}

//------------------------------
// dashboard_config
//------------------------------
function view_jrCore_dashboard_config($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_banner("Dashboard Config");

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard"
    );
    jrCore_form_create($_tmp);

    // See if we have our cols and rows
    $rows = 2;
    $cols = 4;
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_tmp = json_decode($_conf['jrCore_dashboard_config'], true);
        if ($_tmp && jrCore_checktype($_tmp['rows'], 'number_nz')) {
            $rows = (int) $_tmp['rows'];
        }
        if ($_tmp && jrCore_checktype($_tmp['cols'], 'number_nz')) {
            $cols = (int) $_tmp['cols'];
        }
    }

    // Rows
    $_opt = array(
        1 => '1 Row',
        2 => '2 Rows',
        3 => '3 Rows',
        4 => '4 Rows',
        5 => '5 Rows'
    );
    $_tmp = array(
        'name'     => 'dashboard_rows',
        'label'    => 'number of rows',
        'help'     => 'Select the number of rows you would like to appear in the dashboard',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 2,
        'value'    => $rows,
        'required' => true,
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Columns
    $_opt = array(
        2 => '2 Columns',
        3 => '3 Columns',
        4 => '4 Columns',
        5 => '5 Columns'
    );
    $_tmp = array(
        'name'     => 'dashboard_cols',
        'label'    => 'number of columns',
        'help'     => 'Select the number of columns you would like to appear in the dashboard',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 4,
        'value'    => $cols,
        'required' => true,
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// dashboard_config_save
//------------------------------
function view_jrCore_dashboard_config_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    $_cfg = array();
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
    }
    $_cfg['rows'] = (int) $_post['dashboard_rows'];
    $_cfg['cols'] = (int) $_post['dashboard_cols'];

    if (jrCore_set_setting_value('jrCore', 'dashboard_config', json_encode($_cfg))){
        jrCore_delete_config_cache();
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard");
    }
    jrCore_set_form_notice('error', 'Unable to save dashboard config - please try again');
    jrCore_form_result();
}

//------------------------------
// dashboard
//------------------------------
function view_jrCore_dashboard($_post, $_user, $_conf)
{
    jrUser_admin_only();
    // http://www.site.com/core/dashboard/online
    // http://www.site.com/core/dashboard/pending
    // http://www.site.com/core/dashboard/browser
    $title = '';
    if (!isset($_post['_1'])) {
        $_post['_1'] = 'bigview';
    }

    jrCore_page_dashboard_tabs($_post['_1']);
    switch ($_post['_1']) {

        //------------------------------
        // BIGVIEW
        //------------------------------
        case 'bigview':
            $title = 'Dashboard';
            // Setup timer
            $refresh = '';
            if (!jrCore_is_mobile_device()) {
                $refresh = jrCore_page_button('reload', '60', "jrCore_dashboard_reload_page(60,0);");
            }
            $refresh .= jrCore_page_button('refresh', 'refresh', "location.reload();");
            if (jrUser_is_master()) {
                $refresh .= jrCore_page_button('custom', 'customize', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard_config')");
            }

            // for reload timer
            if (isset($_COOKIE['dash_reload']) && $_COOKIE['dash_reload'] == 'on') {
                $_js = array('jrCore_dashboard_reload_page(60,1);');
            }
            else {
                $_js = array('$(\'#reload\').addClass(\'form_button_disabled\'); jrCore_dashboard_reload_page(60,1);');
            }
            jrCore_create_page_element('javascript_ready_function', $_js);

            jrCore_page_banner('dashboard', $refresh);
            jrCore_get_form_notice();
            jrCore_dashboard_bigview($_post, $_user, $_conf);

            break;

        //------------------------------
        // USERS ONLINE
        //------------------------------
        case 'online':
            $title = 'Users Online';
            $m_url = jrCore_get_module_url('jrUser');
            $nuser = jrCore_page_button('newuser', 'new user account', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$m_url}/create')");
            jrCore_page_banner('users online', $nuser);
            jrCore_get_form_notice();
            jrUser_online_users($_post, $_user, $_conf);
            break;

        //------------------------------
        // PENDING USERS
        //------------------------------
        case 'pending_users':
            $title = 'Pending Users';
            jrCore_page_banner('pending users');
            jrCore_get_form_notice();
            jrUser_dashboard_pending_users($_post, $_user, $_conf);
            break;

        //------------------------------
        // PENDING ITEMS
        //------------------------------
        case 'pending':
            $title = 'Pending Items';
            jrCore_page_banner('pending items');
            jrCore_get_form_notice();
            jrCore_dashboard_pending($_post, $_user, $_conf);
            break;

        //------------------------------
        // ACTIVITY LOG
        //------------------------------
        case 'activity':
            $title = 'Activity Log';
            jrCore_show_activity_log($_post, $_user, $_conf, 'dashboard');
            break;

        //------------------------------
        // DATA BROWSER
        //------------------------------
        case 'browser':
            $title = 'Data Browser';
            jrCore_dashboard_browser('dashboard', $_post, $_user, $_conf);
            break;
    }
    jrCore_page_title($title);
    jrCore_page_display();
}

//------------------------------
// form_designer (magic)
//------------------------------
function view_jrCore_form_designer($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_notice_page('error', 'invalid module');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_notice_page('error', 'invalid view');
    }
    $_fields = jrCore_get_designer_form_fields($_post['m'], $_post['v']);
    if (!isset($_fields) || !is_array($_fields)) {
        jrCore_notice_page('error', 'This form has not been setup properly to work with the custom form designer');
    }

    $mod = $_post['m'];
    $opt = $_post['v'];
    $url = jrCore_get_module_url('jrCore');

    jrUser_load_lang_strings();
    $_lang = jrCore_get_flag('jr_lang');

    // Show our table of options
    $subtitle = '';
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "SELECT `view` FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($mod) . "' GROUP BY `view` ORDER by `view` ASC";
    $_rt = jrCore_db_query($req, 'view', false, 'view');
    if (isset($_rt) && is_array($_rt)) {
        if (count($_rt) > 1) {
            $jump_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_designer/m={$_post['module']}/v=";
            // Create a Quick Jump list for custom forms for this module
            $subtitle .= '<select name="designer_form" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $jump_url . "'+ $(this).val())\">\n";
            foreach ($_rt as $option) {
                if ($option == $_post['v']) {
                    $subtitle .= '<option value="' . $option . '" selected="selected"> ' . $_post['module_url'] . '/' . $option . "</option>\n";
                }
                else {
                    $subtitle .= '<option value="' . $option . '"> ' . $_post['module_url'] . '/' . $option . "</option>\n";
                }
            }
            $subtitle .= '</select>';
        }
        else {
            $subtitle = "{$_post['module_url']}/{$_post['v']}";
        }
    }

    // Check for additional views that have been registered by this module, but have
    // not been setup for customization yet...
    $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form');
    foreach ($_rt as $option) {
        unset($_tmp[$mod][$option]);
    }
    if (isset($_tmp[$mod]) && count($_tmp[$mod]) > 0) {
        $text = "The following designer forms have not been setup yet for this module:<br><br>";
        foreach ($_tmp[$mod] as $view => $prefix) {
            $text .= "{$_post['module_url']}/{$view}<br>";
        }
        $text .= "<br>These forms will be initialized the first time they are viewed.  It is recommended that you view all forms for this module before using the Form Designer.";
        jrCore_set_form_notice('notice', $text, false);
    }

    // See if our module has a DS prefix, or has registered a designer prefix
    $pfx = jrCore_db_get_prefix($mod);
    if (!$pfx) {
        // Check for registered prefix
        $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form_prefix');
        if (isset($_tmp[$mod]) && is_array($_tmp[$mod])) {
            $pfx = array_keys($_tmp[$mod]);
            $pfx = reset($pfx);
        }
        else {
            jrCore_notice_page('error', 'This module is not setup with a DataStore prefix - unable to use form designer', 'referrer');
        }
    }

    jrCore_page_banner('form designer', $subtitle);
    jrCore_get_form_notice();

    $dat = array();
    $dat[1]['title'] = 'order';
    $dat[1]['width'] = '2%;';
    $dat[2]['title'] = 'label';
    $dat[2]['width'] = '38%;';
    $dat[3]['title'] = 'name';
    $dat[3]['width'] = '15%;';
    $dat[4]['title'] = 'type';
    $dat[4]['width'] = '15%;';
    $dat[5]['title'] = 'active';
    $dat[5]['width'] = '10%;';
    $dat[6]['title'] = 'required';
    $dat[6]['width'] = '10%;';
    $dat[7]['title'] = 'modify';
    $dat[7]['width'] = '5%;';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%;';
    jrCore_page_table_header($dat);

    foreach ($_fields as $_fld) {

        $dat = array();
        if ($_fld['order'] > 1) {
            $dat[1]['title'] = jrCore_page_button("o{$_fld['name']}", '^', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/form_field_order/m={$mod}/v={$opt}/n={$_fld['name']}/o={$_fld['order']}')");
        }
        else {
            $dat[1]['title'] = '';
        }
        $dat[2]['title'] = (is_numeric($_fld['label']) && isset($_lang[$mod]["{$_fld['label']}"])) ? '&nbsp;' . $_lang[$mod]["{$_fld['label']}"] : '&nbsp;' . $_fld['label'];
        $dat[3]['title'] = $_fld['name'];
        $dat[3]['class'] = 'center';
        $dat[4]['title'] = $_fld['type'];
        $dat[4]['class'] = 'center';
        $dat[5]['title'] = (isset($_fld['active']) && $_fld['active'] == '1') ? 'yes' : '<strong>no</strong>';
        $dat[5]['class'] = 'center';
        $dat[6]['title'] = (isset($_fld['required']) && $_fld['required'] == '1') ? 'yes' : 'no';
        $dat[6]['class'] = 'center';
        $dat[7]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_field_update/m={$mod}/v={$opt}/n={$_fld['name']}')");
        if (isset($_fld['locked']) && $_fld['locked'] == '1') {
            $dat[8]['title'] = jrCore_page_button("d{$_fld['name']}", 'delete', 'disabled');
        }
        else {
            $dat[8]['title'] = jrCore_page_button("d{$_fld['name']}", 'delete', "if (confirm('Are you sure you want to delete this form field?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/form_field_delete/m={$mod}/v={$opt}/n={$_fld['name']}')}");
        }
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // We need to record where we come in from
    $ckey = md5(json_encode($_post));
    if (!isset($_SESSION["form_designer_{$ckey}"])) {
        $_SESSION["form_designer_{$ckey}"] = jrCore_get_local_referrer();
    }
    $_tmp = array(
        'submit_value' => 'create new field',
        'cancel'       => $_SESSION["form_designer_{$ckey}"]
    );
    jrCore_form_create($_tmp);

    // Module
    $_tmp = array(
        'name'     => 'field_module',
        'type'     => 'hidden',
        'value'    => $mod,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // View
    $_tmp = array(
        'name'     => 'field_view',
        'type'     => 'hidden',
        'value'    => $opt,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // New Form Field
    $_tmp = array(
        'name'     => 'new_name',
        'label'    => 'new field name',
        'help'     => "If you would like to create a new field in this form, enter the field name here.<br><br>Note that the new field name must begin with <strong>{$pfx}_</strong>",
        'type'     => 'text',
        'value'    => "{$pfx}_",
        'validate' => 'core_string',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    if (isset($_post['v']) && ($_post['v'] == 'create' || $_post['v'] == 'update')) {
        $opp = ($_post['v'] == 'create') ? 'update' : 'create';
        // See if this module defines the opposite view
        require_once APP_DIR . "/modules/{$mod}/index.php";
        if (function_exists("view_{$mod}_{$opp}")) {
            if (isset($_rt[$opp])) {
                // Link to Update/Create
                $_tmp = array(
                    'name'     => "linked_form_field",
                    'label'    => "add to {$opp} form",
                    'help'     => "If you would like the same field name created for the &quot;{$opp}&quot; form view, check this option",
                    'type'     => 'checkbox',
                    'value'    => 'on',
                    'validate' => 'onoff'
                );
                jrCore_form_field_create($_tmp);
            }
        }
    }
    jrCore_page_display();
}

//------------------------------
// form_designer_save
//------------------------------
function view_jrCore_form_designer_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['field_module']) || !isset($_mods["{$_post['field_module']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result();
    }
    if (!isset($_post['field_view']) || strlen($_post['field_view']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result();
    }
    $mod = $_post['field_module'];
    $opt = $_post['field_view'];
    $_fields = jrCore_get_designer_form_fields($mod, $opt);
    if (!isset($_fields) || !is_array($_fields)) {
        jrCore_set_form_notice('error', 'This form has not been setup properly to work with the custom form designer');
        jrCore_form_result();
    }
    $nam = strtolower($_post['new_name']);
    // Make sure we don't already exist
    if (isset($_fields[$nam]) && is_array($_fields[$nam])) {
        jrCore_set_form_notice('error', 'The name you entered is already being used in this form - please enter a different name.');
        jrCore_form_field_hilight('new_name');
        jrCore_form_result();
    }
    // See if our module has a DS prefix, or has registered a designer prefix
    $pfx = jrCore_db_get_prefix($mod);
    if (!$pfx) {
        // Check for registered prefix
        $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form_prefix');
        if (!isset($_tmp[$mod]) || !is_array($_tmp[$mod])) {
            jrCore_set_form_notice('error', 'This module is not setup with a DataStore prefix - unable to use form designer');
            jrCore_form_result();
        }
    }
    $prfx = jrCore_db_get_prefix($mod);
    if (strpos($_post['new_name'], $prfx) !== 0) {
        jrCore_set_form_notice('error', "The new field name must begin with &quot;{$prfx}&quot;");
        jrCore_form_field_hilight('new_name');
        jrCore_form_result();
    }
    // We can't just use the prefix
    if ($_post['new_name'] == $prfx || $_post['new_name'] == "{$prfx}_") {
        jrCore_set_form_notice('error', "Please enter a valid field name beyond just the prefix");
        jrCore_form_field_hilight('new_name');
        jrCore_form_result();
    }
    // Looks good - create new form field
    $_field = array(
        'name'   => $_post['new_name'],
        'type'   => 'text',
        'label'  => $_post['new_name'],
        'locked' => '0'
    );
    jrCore_set_flag('jrcore_designer_create_custom_field', 1);
    $tmp = jrCore_verify_designer_form_field($mod, $opt, $_field);
    if ($tmp) {
        // See if we are also adding it to the create/update view
        if (isset($_post['linked_form_field']) && $_post['linked_form_field'] == 'on') {
            $opp = ($opt == 'create') ? 'update' : 'create';
            $tmp = jrCore_verify_designer_form_field($mod, $opp, $_field);
            if (!$tmp) {
                jrCore_set_form_notice('error', "An error was encountered inserting the new field into the {$opp} form - please try again");
                jrCore_form_result();
            }
        }
        $url = jrCore_get_module_url($mod);
        jrCore_form_delete_session();

        // Insert defaults into each existing record - note that this is required otherwise these records may not be searchable
        jrCore_db_create_default_key($_post['field_module'], $_post['new_name'], '');

        jrCore_form_result("{$_conf['jrCore_base_url']}/{$url}/form_field_update/m={$mod}/v={$opt}/n={$_post['new_name']}");
        return true;
    }
    jrCore_set_form_notice('error', 'An error was encountered saving the new for field to the database - please try again');
    jrCore_form_result();
    return true;
}

//------------------------------
// form_field_delete
//------------------------------
function view_jrCore_form_field_delete($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['n']) || strlen($_post['n']) === 0) {
        jrCore_set_form_notice('error', 'Invalid name');
        jrCore_form_result('referrer');
    }
    $mod = jrCore_db_escape($_post['m']);
    $opt = jrCore_db_escape($_post['v']);
    $nam = jrCore_db_escape($_post['n']);
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "SELECT * FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' and `name` = '{$nam}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid Field - not found in custom forms table');
        jrCore_form_result('referrer');
    }
    // Delete field
    $req = "DELETE FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' and `name` = '{$nam}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {

        // We need to remove any language strings for this custom field
        $_fnd = array();
        $_rem = array('label', 'sublabel', 'help');
        foreach ($_rem as $k) {
            if (isset($_rt[$k]) && jrCore_checktype($_rt[$k], 'number_nz')) {
                $_fnd[] = (int) $_rt[$k];
            }
        }
        if (count($_fnd) > 0) {
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $req = "DELETE FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_key IN(" . implode(',', $_fnd) . ")";
            jrCore_db_query($req);
        }

        // We need to reset any existing Form Sessions for this view
        jrCore_form_delete_session_view($_post['m'], $_post['v']);
        jrCore_set_form_notice('success', 'The form field was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered trying to delete the form field - please try again');
    }
    jrCore_form_result('referrer');
}

//------------------------------
// form_field_order
//------------------------------
function view_jrCore_form_field_order($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['n']) || strlen($_post['n']) === 0) {
        jrCore_set_form_notice('error', 'Invalid name');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid order');
        jrCore_form_result('referrer');
    }
    $ord = intval($_post['o'] - 1);
    // Okay - we need to MOVE UP the name we got, and MOVE DOWN the one above it
    jrCore_set_form_designer_field_order($_post['m'], $_post['v'], $_post['n'], $ord);
    jrCore_form_delete_session_view($_post['m'], $_post['v']);
    jrCore_form_result('referrer');
}

//------------------------------
// form_field_update (magic)
//------------------------------
function view_jrCore_form_field_update($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_notice_page('error', 'invalid module');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_notice_page('error', 'invalid view');
    }
    if (!isset($_post['n']) || strlen($_post['n']) === 0) {
        jrCore_notice_page('error', 'invalid name');
    }
    $mod = $_post['m'];
    $opt = $_post['v'];
    $_fields = jrCore_get_designer_form_fields($mod, $opt);
    if (!isset($_fields) || !is_array($_fields)) {
        jrCore_notice_page('error', 'This form has not been setup properly to work with the custom form designer');
    }
    $nam = $_post['n'];
    if (!isset($_fields[$nam]) || !is_array($_fields[$nam])) {
        jrCore_notice_page('error', 'This form field has not been setup properly to work with the custom form designer');
    }
    $_fld = $_fields[$nam];

    $_lang = jrUser_load_lang_strings(null, false);

    jrCore_page_banner("field: <span style=\"text-transform:lowercase;\">{$_fld['name']}</span>", "{$_post['module_url']}/{$_post['v']}");

    // Some fields will BREAK if they are changed - warn about this
    switch ($nam) {
        case 'user_passwd1':
        case 'user_passwd2':
            jrCore_set_form_notice('warning', 'This field is required for proper functionality - do not <strong>make inactive</strong> or change the field <strong>type</strong>, <strong>validation</strong> or <strong>group</strong> fields!', false);
            break;
    }
    jrCore_get_form_notice();

    // Show our table of options
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Module
    $_tmp = array(
        'name'     => 'field_module',
        'type'     => 'hidden',
        'value'    => $mod,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // View
    $_tmp = array(
        'name'     => 'field_view',
        'type'     => 'hidden',
        'value'    => $opt,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Name
    $_tmp = array(
        'name'     => 'name',
        'type'     => 'hidden',
        'value'    => $nam,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Fields can have the following attributes:
    // label
    // sublabel
    // help
    // name
    // type
    // validate
    // options
    // min
    // max
    // required

    // Field Label
    $_tmp = array(
        'name'     => 'label',
        'label'    => 'label',
        'help'     => 'This is the Label name that will appear to the left of the field.<br><br><strong>NOTE:</strong> If you see *change* in the field it means this text label has not been created yet - enter a label and save your changes.',
        'type'     => 'text',
        'value'    => (isset($_lang[$mod]["{$_fld['label']}"])) ? $_lang[$mod]["{$_fld['label']}"] : $_fld['label'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Field Sub Label
    $_tmp = array(
        'name'     => 'sublabel',
        'label'    => 'sub label',
        'help'     => 'This is the text that will be appear UNDER the Label in smaller type. Use this to let the user know about any restrictions in the field. This is an optional field - if left empty it will not show.',
        'type'     => 'text',
        'value'    => (isset($_lang[$mod]["{$_fld['sublabel']}"])) ? $_lang[$mod]["{$_fld['sublabel']}"] : $_fld['sublabel'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Field Help
    $_tmp = array(
        'name'     => 'help',
        'label'    => 'help',
        'help'     => 'The Help text will appear in the small drop down area when the user clicks on the Question button (like you are viewing right now). Leave this empty to not show a help drop down.',
        'type'     => 'text',
        'value'    => (isset($_lang[$mod]["{$_fld['help']}"])) ? $_lang[$mod]["{$_fld['help']}"] : $_fld['help'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Bring in any custom form fields
    $_opt = array();
    $_fdo = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'form_field');
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $mod => $_v) {
            foreach ($_v as $k => $v) {
                $_opt[$k] = $k;
                $fnc = "{$mod}_form_field_{$k}_form_designer_options";
                if (function_exists($fnc)) {
                    $_fdo[$k] = $fnc();
                }
            }
        }
        unset($_opt['hidden'], $_opt['custom'], $_opt['live_search']);
    }

    // Some field types have their own internal validation, so we "disable"
    // this field if those types are the selected one
    $_dis = array();
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['disable_validation']) && $_fo['disable_validation'] === true) {
            $_dis[] = $ft;
        }
    }

    // Disabled Options for some fields
    $_dop = array();
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['disable_options']) && $_fo['disable_options'] === true) {
            $_dop[] = $ft;
        }
    }

    // Disabled Default for some fields
    $_def = array();
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['disable_default']) && $_fo['disable_default'] === true) {
            $_def[] = $ft;
        }
    }

    // Disabled Min/Max for some fields
    $_dmx = array();
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['disable_min_and_max']) && $_fo['disable_min_and_max'] === true) {
            $_dmx[] = $ft;
        }
    }

    // Field Type
    $_tmp = array(
        'name'     => 'type',
        'label'    => 'type',
        'sublabel' => 'see <strong>help</strong> for field details',
        'help'     => 'The Field Type defines the type of form element that will be displayed for this field.',
        'type'     => 'select',
        'options'  => $_opt,
        'value'    => $_fld['type'],
        'validate' => 'core_string',
        'onchange' => "var a=this.options[this.selectedIndex].value;var b={'" . implode("':1,'", $_dis) . "':1};if(typeof b[a] !== 'undefined' && b[a] == 1){\$('.validate_element_right select').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.validate_element_right select').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')};var c={'" . implode("':1,'", $_dop) . "':1};if(typeof c[a] !== 'undefined' && c[a] == 1){\$('.options_element_right textarea').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.options_element_right textarea').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')};var d={'" . implode("':1,'", $_def) . "':1};if(typeof d[a] !== 'undefined' && d[a] == 1){\$('.default_element_right #default').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.default_element_right #default').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')};var e={'" . implode("':1,'", $_dmx) . "':1};if(typeof e[a] !== 'undefined' && e[a] == 1){\$('.min_element_right #min').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled');\$('.max_element_right #max').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.min_element_right #min').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled');\$('.max_element_right #max').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')}"

    );
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['type_help']{1})) {
            $_tmp['help'] .= "<br><br><strong>{$ft}</strong> - {$_fo['type_help']}";
        }
    }
    jrCore_form_field_create($_tmp);

    // Options
    $_opt = array();
    if (isset($_fld['options']) && strpos($_fld['options'], '{') === 0) {
        $_tmp = json_decode($_fld['options'], true);
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $k => $v) {
                $_opt[] = "{$k}|{$v}";
            }
            $_fld['options'] = implode("\n", $_opt);
        }
    }
    $_tmp = array(
        'name'     => 'options',
        'label'    => 'options',
        'sublabel' => 'see <strong>help</strong> for what is allowed here',
        'help'     => 'The Options value will vary depending on the selected field type:',
        'type'     => 'textarea',
        'value'    => $_fld['options'],
        'validate' => 'printable'
    );
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['options_help']{1})) {
            $_tmp['help'] .= "<br><br><strong>{$ft}</strong> - {$_fo['options_help']}";
        }
    }
    jrCore_form_field_create($_tmp);

    // Field Default
    $_tmp = array(
        'name'     => 'default',
        'label'    => 'default',
        'help'     => 'If you would like a default value to be used for this field, enter the default value here.',
        'type'     => 'text',
        'value'    => $_fld['default'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Validate
    $_opt = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'checktype');
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $mod => $_entries) {
            foreach ($_entries as $type => $ignore) {
                $func = $mod . '_checktype_' . $type;
                if (function_exists($func)) {
                    $check_type = jrCore_checktype('', $type, false, true);
                    $_opt[$type] = '(' . $check_type . ') ' . jrCore_checktype('', $type, true, true);
                }
            }
        }
    }
    $_tmp = array(
        'name'     => 'validate',
        'label'    => 'validation',
        'help'     => 'Select the type of field validation you would like to have for this field. The following field types:<br><br>optionlist<br>select<br>select_multiple<br>radio<br>image<br>file<br>audio<br>checkbox<br><br>are automatically validated internally, so the validation option will be grayed out if these field types are selected.',
        'type'     => 'select',
        'options'  => $_opt,
        'value'    => $_fld['validate'],
        'validate' => 'core_string'
    );
    // See if we have selected a disabled type
    if (in_array($_fld['type'], $_dis)) {
        $_js = array("$('.validate_element_right select').fadeTo(250,0.3).attr('disabled','disabled')");
        jrCore_create_page_element('javascript_ready_function', $_js);
    }
    jrCore_form_field_create($_tmp);

    // Field Min
    $_tmp = array(
        'name'     => 'min',
        'label'    => 'minimum',
        'help'     => 'The Field Minimum Value will validate that any entered value is greater than or equal to the minimum value.<br><br><strong>For (number) Fields:</strong> This is the minimum value accepted.<br><strong>For (string) Fields:</strong> This is the minimum <strong>character length</strong> for the string.<br><strong>For (date) Fields:</strong> This is the minimum accepted date (in YYYYMMDD[HHMMSS] format).',
        'type'     => 'text',
        'value'    => (isset($_fld['min']) && $_fld['min'] == '0') ? '' : (int) $_fld['min'],
        'validate' => 'number_nn'
    );
    jrCore_form_field_create($_tmp);

    // Field Max
    $_tmp = array(
        'name'     => 'max',
        'label'    => 'maximum',
        'help'     => 'The Field Maximum Value will validate that any entered value is less than or equal to the maximum value.<br><br><strong>For (number) Fields:</strong> This is the maximum value accepted.<br><strong>For (string) Fields:</strong> This is the maximum <strong>character length</strong> for the string.<br><strong>For (date) Fields:</strong> This is the maximum accepted date (in YYYYMMDD[HHMMSS] format).',
        'type'     => 'text',
        'value'    => (isset($_fld['max']) && $_fld['max'] == '0') ? '' : (int) $_fld['max'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Field Group
    $_opt = array(
        'all'     => '(group) All Users (including logged out)',
        'master'  => '(group) Master Admins',
        'admin'   => '(group) Profile Admins',
        'power'   => '(group) Power Users',
        'user'    => '(group) Normal Users',
        'visitor' => '(group) Logged Out Users'
    );
    $_qta = jrProfile_get_quotas();
    if (isset($_qta) && is_array($_qta)) {
        foreach ($_qta as $qid => $qname) {
            $_opt[$qid] = "(quota) {$qname}";
        }
    }
    $_tmp = array(
        'name'     => 'group',
        'label'    => 'display groups',
        'sublabel' => 'more than 1 group allowed',
        'help'     => 'If you would like this field to only be visible to Users in specific Profile Quotas, Profile Admins or Master Admins, select the group(s) here.',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'value'    => $_fld['group'],
        'default'  => 'user',
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Field Required
    $_tmp = array(
        'name'     => 'required',
        'label'    => 'required',
        'help'     => 'If you would like to ensure a valid value is always received for this field, check the Field Required option.',
        'type'     => 'checkbox',
        'value'    => (isset($_fld['required']) && $_fld['required'] == '1') ? 'on' : 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Field Active
    $_tmp = array(
        'name'     => 'active',
        'label'    => 'active',
        'help'     => 'If Field Active is not checked, this field will not appear in the form.',
        'type'     => 'checkbox',
        'value'    => (isset($_fld['active']) && $_fld['active'] == '1') ? 'on' : 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    if (isset($_post['v']) && ($_post['v'] == 'create' || $_post['v'] == 'update')) {

        // Make sure this module supplies the create/update view
        $opp = ($_post['v'] == 'create') ? 'update' : 'create';
        require_once APP_DIR . "/modules/{$mod}/index.php";
        if (function_exists("view_{$mod}_{$opp}")) {
            // Link to Update/Create
            $_tmp = array(
                'name'     => "linked_form_field",
                'label'    => "change {$opp} field",
                'help'     => "If you would like your changes to be saved to the same field in the &quot;{$opp}&quot; form, check here.",
                'type'     => 'checkbox',
                'value'    => 'on',
                'validate' => 'onoff'
            );
            jrCore_form_field_create($_tmp);
        }
    }

    jrCore_page_display();
}

//------------------------------
// form_field_update_save (magic)
//------------------------------
function view_jrCore_form_field_update_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['field_module']) || !isset($_mods["{$_post['field_module']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result();
    }
    if (!isset($_post['field_view']) || strlen($_post['field_view']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result();
    }
    if (isset($_post['required']) && $_post['required'] == 'on') {
        $_post['required'] = 1;
    }
    else {
        $_post['required'] = 0;
    }
    if (isset($_post['active']) && $_post['active'] == 'on') {
        $_post['active'] = 1;
    }
    else {
        $_post['active'] = 0;
    }
    $mod = $_post['field_module'];
    $opt = $_post['field_view'];
    $nam = $_post['name'];

    jrUser_load_lang_strings();
    $_lang = jrCore_get_flag('jr_lang');
    $_save = array();

    // Update Lang Strings
    $_tm = jrCore_get_designer_form_fields($mod, $opt);
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $lcd = jrCore_db_escape($_user['user_language']);
    if (isset($_tm[$nam]) && is_array($_tm[$nam])) {
        $_todo = array('label', 'sublabel', 'help');
        foreach ($_todo as $do) {
            $num = (isset($_tm[$nam][$do]) && jrCore_checktype($_tm[$nam][$do], 'number_nz')) ? (int) $_tm[$nam][$do] : 0;
            if (isset($num) && jrCore_checktype($num, 'number_nz')) {
                if (isset($_lang[$mod][$num])) {
                    if ($do === 'label') {
                        $_post[$do] = strtolower($_post[$do]);
                    }
                    $req = "UPDATE {$tbl} SET lang_text = '" . jrCore_db_escape($_post[$do]) . "' WHERE lang_module = '" . jrCore_db_escape($mod) . "' AND lang_key = '{$num}' AND (lang_code = '{$lcd}' OR lang_text LIKE '%change this%')";
                    jrCore_db_query($req);
                    $_save[$do] = $_post[$do];
                    $_post[$do] = $num;
                }
            }
        }
        jrCore_delete_all_cache_entries('jrUser');
    }

    // See if we are Create/Update Linked
    if (isset($_post['linked_form_field']) && $_post['linked_form_field'] == 'on') {
        $opp = ($_post['field_view'] == 'create') ? 'update' : 'create';
        $_tm = jrCore_get_designer_form_fields($mod, $opp);
        if (isset($_tm[$nam]) && is_array($_tm[$nam])) {
            $_todo = array('label', 'sublabel', 'help');
            foreach ($_todo as $do) {
                $num = (isset($_tm[$nam][$do]) && jrCore_checktype($_tm[$nam][$do], 'number_nz')) ? (int) $_tm[$nam][$do] : 0;
                if (isset($num) && jrCore_checktype($num, 'number_nz')) {
                    if (isset($_lang[$mod][$num])) {
                        $req = "UPDATE {$tbl} SET lang_text = '" . jrCore_db_escape($_save[$do]) . "' WHERE lang_module = '" . jrCore_db_escape($mod) . "' AND lang_key = '{$num}' AND (lang_code = '{$lcd}' OR lang_text LIKE '%change this%')";
                        jrCore_db_query($req);
                    }
                }
            }
        }
        jrCore_delete_all_cache_entries('jrUser');
    }

    // Check validation.  Some fields (such as checkbox) have specific validation
    // requirements - set this here so they cannot be set wrong.
    switch ($_post['type']) {
        case 'date':
        case 'datetime':
            $_post['validate'] = 'date';
            break;
        case 'select_date':
            $_post['validate'] = 'number_nz';
            break;
        case 'checkbox':
            $_post['validate'] = 'onoff';
            break;
        case 'select':
        case 'select_multiple':
        case 'radio':
        case 'optionlist':
            // For a select field, our OPTIONS will come in either as a FUNCTION or as individual options on each line
            if (isset($_post['options']) && strlen($_post['options']) > 0) {
                $cfunc = $_post['options'];
                if (!function_exists($cfunc)) {
                    // okay - we're not a function
                    $_tmp = explode("\n", $_post['options']);
                    if (!isset($_tmp) || !is_array($_tmp)) {
                        jrCore_set_form_notice('error', 'You have entered an invalid value for Options - must be a valid function or a set of options, one per line.');
                        jrCore_form_result();
                    }
                    $_post['options'] = array();
                    foreach ($_tmp as $v) {
                        $v = trim($v);
                        if (strpos($v, '|')) {
                            list($k, $v) = explode('|', $v, 2);
                        }
                        else {
                            $k = $v;
                        }
                        $_post['options'][$k] = $v;
                    }
                }
            }
            else {
                jrCore_set_form_notice('error', 'You must enter valid Options for a Select form field');
                jrCore_form_result();
            }
            break;
    }

    // First - get existing default value for use below
    $def = '';
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "SELECT `default` FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($_post['field_module']) . "' AND `name` = '" . jrCore_db_escape($_post['name']) . "' LIMIT 1";
    $_ev = jrCore_db_query($req, 'SINGLE');
    if (isset($_ev) && is_array($_ev) && isset($_ev['default']) && strlen($_ev['default']) > 0) {
        $def = jrCore_db_escape($_ev['default']);
    }

    $cnt = jrCore_verify_designer_form_field($_post['field_module'], $_post['field_view'], $_post);
    if (isset($cnt) && $cnt == '1') {
        if (isset($_post['linked_form_field']) && $_post['linked_form_field'] == 'on') {
            // The linked lang strings are handled above - don't change them here
            unset($_post['label'], $_post['sublabel'], $_post['help']);
            $opp = ($_post['field_view'] == 'create') ? 'update' : 'create';
            $cnt = jrCore_verify_designer_form_field($_post['field_module'], $opp, $_post);
            if (!isset($cnt) || $cnt != '1') {
                jrCore_set_form_notice('error', "An error was encountered updating the linked form field in the {$opp} form view - please try again");
                jrCore_form_result();
            }
        }
        jrCore_form_delete_session();
        jrCore_form_delete_session_view($_post['field_module'], $_post['field_view']);
        jrCore_set_form_notice('success', 'The field settings were successfully updated');

        // Next, we need to update any existing values in the DB
        // with the new default value, but only for those that have not
        // been set, or are still set to the previous default value (if set)
        $val = (isset($_post['default'])) ? jrCore_db_escape($_post['default']) : '';
        jrCore_db_update_default_key($_post['field_module'], $_post['name'], $val, $def);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the form field - please try again');
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_designer/m={$mod}/v={$opt}");
}

//------------------------------
// skin_admin (magic)
//------------------------------
function view_jrCore_skin_admin($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_create_media_directory(0);
    jrUser_load_lang_strings();
    $_lang = jrCore_get_flag('jr_lang');

    if (!isset($_post['_1'])) {
        $_post['_1'] = 'info';
    }
    if (!isset($_post['skin']{0})) {
        $_post['skin'] = $_conf['jrCore_active_skin'];
    }

    $admin = '';
    $title = '';
    // See if we are getting an INDEX page for this module.  The Index
    // Page will tell us what "view" for the module config they are showing.
    // This can be either a config page for the module (i.e. global settings,
    // quota settings, language, etc.) OR it can be a tool.
    // Our URL will be like:
    // http://www.site.com/core/config/global
    // http://www.site.com/core/config/quota
    // http://www.site.com/core/config/language
    // http://www.site.com/core/config/tools
    switch ($_post['_1']) {

        //------------------------------
        // GLOBAL SETTINGS
        //------------------------------
        case 'global':
            $title = 'Global Config';
            $admin = jrCore_show_global_settings('skin', $_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // STYLE
        //------------------------------
        case 'style':
            $title = 'Style';
            $admin = jrCore_show_skin_style($_post['skin'], $_post, $_user, $_conf);

            // Bring in our Color Picker if needed
            $_tmp = jrCore_get_flag('style_color_picker');
            if ($_tmp) {
                $_inc = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/js/jquery.colorpicker.js");
                jrCore_create_page_element('javascript_href', $_inc);
                foreach ($_tmp as $v) {
                    jrCore_create_page_element('javascript_ready_function', $v);
                }
            }
            break;

        //------------------------------
        // IMAGES
        //------------------------------
        case 'images':
            $title = 'Images';
            $admin = jrCore_show_skin_images('skin', $_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // LANGUAGE STRINGS
        //------------------------------
        case 'language':
            $title = 'Language Strings';
            $admin = jrUser_show_module_lang_strings('skin', $_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // TEMPLATES
        //------------------------------
        case 'templates':
            $title = 'Templates';
            $admin = jrCore_show_skin_templates($_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // INFO
        //------------------------------
        case 'info':
            $title = 'Info';
            $admin = jrCore_show_skin_info($_post['skin'], $_post, $_user, $_conf);
            break;
    }

    // Expand our skins
    $_rt = jrCore_get_skins();
    $_sk = array();
    foreach ($_rt as $skin_dir) {
        $func = "{$skin_dir}_skin_meta";
        if (!function_exists($func)) {
            require_once APP_DIR . "/skins/{$skin_dir}/include.php";
        }
        if (function_exists($func)) {
            $_sk[$skin_dir] = $func();
        }
    }

    // Process view
    $_rep = array(
        'active_tab'         => 'skins',
        'admin_page_content' => $admin,
        '_skins'             => $_sk
    );

    // We need to go through each module and get it's default page
    foreach ($_rep['_skins'] as $k => $_v) {
        if (is_file(APP_DIR . "/skins/{$k}/config.php")) {
            $_rep['_skins'][$k]['skin_index_page'] = 'global';
        }
        elseif (isset($_lang[$k])) {
            $_rep['_skins'][$k]['skin_index_page'] = 'language';
        }
        else {
            // info
            $_rep['_skins'][$k]['skin_index_page'] = 'info';
        }
    }

    // See if our skin is overriding our core admin template
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/admin.tpl")) {
        $html = jrCore_parse_template('admin.tpl', $_rep);
    }
    else {
        $html = jrCore_parse_template('admin.tpl', $_rep, 'jrCore');
    }

    // Output
    jrCore_page_title("{$title} - {$_sk["{$_post['skin']}"]['name']}");
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// skin_admin_save (magic)
//------------------------------
function view_jrCore_skin_admin_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure we get a good skin
    if (!isset($_post['skin'])) {
        $_post['skin'] = $_conf['jrCore_active_skin'];
    }
    // Make sure our skin config is properly loaded
    $_conf = jrCore_load_skin_config($_post['skin'], $_conf);

    // See what we are saving...
    switch ($_post['_1']) {

        case 'global':

            // See if this module is presenting us with a validate function
            if (is_file(APP_DIR . "/skins/{$_post['skin']}/config.php")) {
                $vfunc = "{$_post['skin']}_config_validate";
                if (!function_exists($vfunc)) {
                    require_once APP_DIR . "/skins/{$_post['skin']}/config.php";
                }
                if (function_exists($vfunc)) {
                    $_post = $vfunc($_post);
                }
            }
            // Update
            $show = false;
            foreach ($_post as $k => $v) {
                if (isset($_conf["{$_post['skin']}_{$k}"]) && $v != $_conf["{$_post['skin']}_{$k}"]) {
                    jrCore_set_setting_value($_post['skin'], $k, $v);
                    $show = true;
                }
            }
            jrCore_delete_all_cache_entries('jrCore', 0);
            $text = 'The settings have been successfully saved';
            if ($show) {
                $text .= "<br>Make sure you <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/cache_reset\"><u>Reset Caches</u></a> to activate your changes";
            }
            jrCore_set_form_notice('success', $text, false);
            break;

        case 'language':

            // Get all the lang strings for this module
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $mod = jrCore_db_escape($_post['skin']);
            $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '" . jrCore_db_escape($_post['lang_code']) . "'";
            $_rt = jrCore_db_query($req, 'lang_id');
            if (!isset($_rt) || !is_array($_rt)) {
                jrCore_set_form_notice('error', "Unable to retrieve skin language settings from language table - check debug_log errors");
                jrCore_form_result();
            }
            $req = "UPDATE {$tbl} SET lang_text = CASE lang_id\n";
            foreach ($_rt as $key => $_lng) {
                if (isset($_post["lang_{$key}"])) {
                    $req .= "WHEN {$key} THEN '" . jrCore_db_escape($_post["lang_{$key}"]) . "'\n";
                }
            }
            if (isset($req) && strpos($req, 'THEN')) {
                $req .= "ELSE lang_text END";
                jrCore_db_query($req, 'COUNT');
            }
            jrCore_delete_all_cache_entries('jrUser');
            jrCore_set_form_notice('success', 'The language strings have been successfully saved');
            jrCore_form_delete_session();
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/{$_post['_1']}/skin={$_post['skin']}/lang_code={$_post['lang_code']}/p={$_post['p']}");
            break;

        case 'images':

            jrCore_create_media_directory(0);
            // Get existing skin info to see what images we have customized
            $_im = array();
            if (isset($_conf["jrCore_{$_post['skin']}_custom_images"]{2})) {
                $_im = json_decode($_conf["jrCore_{$_post['skin']}_custom_images"], true);
            }
            // Check for new custom files being uploaded
            $_up = jrCore_get_uploaded_meter_files($_post['upload_token']);
            if (isset($_up) && is_array($_up)) {
                foreach ($_up as $_info) {
                    jrCore_copy_media_file(0, $_info['tmp_name'], "{$_post['skin']}_{$_info['name']}");
                    $_im["{$_info['name']}"] = array($_info['size'], 'on');
                }
            }
            // Go through and save our uploaded images (if any)
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $k => $_info) {
                    $num = (int) str_replace('file_', '', $k);
                    $nam = $_post["name_{$num}"];
                    if (isset($_info['size']) && jrCore_checktype($_info['size'], 'number_nz')) {
                        // Image extensions must match
                        $ext = jrCore_file_extension($_info['name']);
                        switch ($ext) {
                            case 'jpg':
                            case 'png':
                            case 'gif':
                                break;
                            default:
                                jrCore_set_form_notice('error', 'Invalid image type for ' . $_post["name_{$num}"] . ' - only JPG, PNG and GIF images are allowed');
                                jrCore_form_result();
                                break;
                        }
                        if (isset($_post["name_{$num}"]{0})) {
                            jrCore_copy_media_file(0, $_info['tmp_name'], "{$_post['skin']}_{$nam}");
                            $_im[$nam] = array($_info['size']);
                        }
                    }
                }
            }
            // Update setting with new values
            // [name_0_active] => on
            // [name_0] => bckgrd.png
            foreach ($_post as $k => $v) {
                if (strpos($k, 'name_') === 0 && strpos($k, '_active')) {
                    $num = (int) substr($k, 5, strrpos($k, '_'));
                    $nam = $_post["name_{$num}"];
                    if (isset($_im[$nam][0])) {
                        $_im[$nam][1] = $v;
                    }
                    else {
                        unset($_im[$nam]);
                    }
                }
            }
            jrCore_set_setting_value('jrCore', "{$_post['skin']}_custom_images", json_encode($_im));
            jrCore_delete_all_cache_entries();
            break;

        case 'style':

            // We need to save our updates to the database so they "override" the defaults...
            $_out = array();
            $_com = array();
            foreach ($_post as $k => $v) {
                // all of our custom style entries will start with "jrse"....
                if (strpos($k, 'jrse') === 0) {
                    // We have a style entry.  the key for this entry will in position 4
                    $key = $k;
                    if (strpos($key, '_')) {
                        list($key,) = explode('_', $k);
                    }
                    $key = (int) substr($key, 4);
                    if (!isset($_com[$key])) {
                        // Now we can get our Name, Selector and New Value - i.e.:
                        // [jrse3_s] => body~font-family
                        // [jrse3] => Open Sans,Tahoma,sans-serif
                        list($selector, $rule) = @explode('~', $_post["jrse{$key}_s"], 2);
                        // See if we have a color...
                        if (isset($_post["jrse{$key}_hex"])) {
                            $_out[$selector][$rule] = trim($_post["jrse{$key}_hex"]);
                        }
                        else {
                            $val = trim($_post["jrse{$key}"]);
                            switch ($rule) {
                                case 'font-family':
                                    if (strpos($val, ',')) {
                                        $_vl = array();
                                        foreach (explode(',', $val) as $vl) {
                                            if (strpos($vl, ' ')) {
                                                $vl = '"' . $vl . '"';
                                            }
                                            $_vl[] = $vl;
                                        }
                                        $val = implode(',', $_vl);
                                        unset($_vl);
                                    }
                                    break;
                            }
                            $_out[$selector][$rule] = $val;
                        }
                        // See if we are !important
                        if (isset($_post["jrse{$key}_add_important"]) && $_post["jrse{$key}_add_important"] == 'on') {
                            $_out[$selector][$rule] .= ' !important';
                        }
                        if (isset($_post["jrse{$key}_add_auto"]) && $_post["jrse{$key}_add_auto"] == 'on') {
                            $_out[$selector][$rule] .= ' auto';
                        }
                        $_com[$key] = 1;
                    }
                }
            }
            // Save out to database
            $tbl = jrCore_db_table_name('jrCore', 'skin');
            $req = "SELECT skin_custom_css, skin_custom_image FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if (isset($_rt) && is_array($_rt) && isset($_rt['skin_custom_css']{2})) {
                $_css = json_decode($_rt['skin_custom_css'], true);
                $_css = array_merge($_css, $_out);
                $cimg = $_rt['skin_custom_image'];
            }
            else {
                $_css = $_out;
                $cimg = '';
            }
            $_css = json_encode($_css);
            $skn = jrCore_db_escape($_post['skin']);
            $req = "INSERT INTO {$tbl} (skin_directory, skin_updated, skin_custom_css, skin_custom_image) VALUES ('{$skn}',UNIX_TIMESTAMP(),'" . jrCore_db_escape($_css) . "', '" . jrCore_db_escape($cimg) ."')
                    ON DUPLICATE KEY UPDATE skin_updated = UNIX_TIMESTAMP(), skin_custom_css = '" . jrCore_db_escape($_css) . "'";
            $cnt = jrCore_db_query($req, 'COUNT');
            if (!isset($cnt) || $cnt === 0) {
                jrCore_set_form_notice('error', 'An error was enountered saving the custom style to the database - please try again');
                jrCore_form_result();
            }
            // Recreate our site CSS
            jrCore_create_master_css($_post['skin']);
            jrCore_form_delete_session();
            switch ($_post['section']) {
                case 'simple':
                case 'padding':
                case 'advanced':
                case 'extra':
                    $section = $_post['section'];
                    break;
                default:
                    $section = 'simple';
                    break;
            }
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/{$_post['_1']}/skin={$_post['skin']}/file={$_post['file']}/section={$section}");
            break;

        case 'templates':

            //  [form_begin_template_active] => on
            $_act = array();
            $_off = array();
            $_all = array();
            foreach ($_post as $k => $v) {
                if (strpos($k, '_template_active')) {
                    $tpl = str_replace('_template_active', '.tpl', $k);
                    // See if we are turning this template on or off
                    if ($v == 'on') {
                        $_act[] = $tpl;
                        $_all[] = $tpl;
                    }
                    else {
                        $_off[] = $tpl;
                        $_all[] = $tpl;
                    }
                }
            }

            // Set active/inactive
            if (isset($_all) && is_array($_all) && count($_all) > 0) {
                $tbl = jrCore_db_table_name('jrCore', 'template');
                $mod = jrCore_db_escape($_post['skin']);
                if (isset($_act) && is_array($_act) && count($_act) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '1', template_updated = UNIX_TIMESTAMP() WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_act) . "')";
                    jrCore_db_query($req);
                }
                if (isset($_off) && is_array($_off) && count($_off) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '0', template_updated = UNIX_TIMESTAMP() WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_off) . "')";
                    jrCore_db_query($req);
                }
                // Reset cache for any that changed
                foreach ($_all as $tpl) {
                    jrCore_get_template_file($tpl, $_post['skin'], 'reset');
                }
            }
            jrCore_set_form_notice('success', 'The template settings have been successfully saved');
            break;

        case 'info':

            // Update
            if (isset($_post['skin_active']) && $_post['skin_active'] == 'on') {
                // config
                if (is_file(APP_DIR . "/skins/{$_post['skin']}/config.php")) {
                    require_once APP_DIR . "/skins/{$_post['skin']}/config.php";
                    $func = "{$_post['skin']}_skin_config";
                    if (function_exists($func)) {
                        $func();
                    }
                }
                // quota
                if (is_file(APP_DIR . "/skins/{$_post['skin']}/quota.php")) {
                    require_once APP_DIR . "/skins/{$_post['skin']}/quota.php";
                    $func = "{$_post['skin']}_skin_quota_config";
                    if (function_exists($func)) {
                        $func();
                    }
                }
                // lang strings
                if (is_dir(APP_DIR . "/skins/{$_post['skin']}/lang")) {
                    jrUser_install_lang_strings('skin', $_post['skin']);
                }

                // Make sure this skin is configured in the skins table
                $_mt = jrCore_skin_meta_data($_post['skin']);
                if (is_array($_mt)) {
                    $tbl = jrCore_db_table_name('jrCore', 'skin');
                    $skn = jrCore_db_escape($_post['skin']);
                    $req = "INSERT INTO {$tbl} (skin_directory, skin_updated, skin_custom_css, skin_custom_image) VALUES ('{$skn}', UNIX_TIMESTAMP(), '', '') ON DUPLICATE KEY UPDATE skin_updated = UNIX_TIMESTAMP()";
                    jrCore_db_query($req);
                }
                // Activate it
                jrCore_set_setting_value('jrCore', 'active_skin', $_post['skin']);
                $dir = jrCore_get_module_cache_dir('jrCore');
                jrCore_delete_dir_contents($dir);
                $dir = jrCore_get_module_cache_dir($_post['skin']);
                jrCore_delete_dir_contents($dir);
                jrCore_delete_all_cache_entries();

                // redirect so we reload
                jrCore_form_delete_session();
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/info/skin={$_post['skin']}");
            }
            elseif (isset($_post['skin_delete']) && $_post['skin_delete'] === 'on') {

                // Remove from skins
                $tbl = jrCore_db_table_name('jrCore', 'skin');
                $skn = jrCore_db_escape($_post['skin']);
                $req = "DELETE FROM {$tbl} WHERE skin_directory = '{$skn}' LIMIT 1";
                jrCore_db_query($req);

                // Cleanup cache dir
                $cdr = jrCore_get_module_cache_dir($_post['skin']);
                jrCore_delete_dir_contents($cdr);
                rmdir($cdr);

                // Cleanup all directories
                $_dirs = glob(APP_DIR ."/skins/{$_post['skin']}*");
                if ($_dirs && is_array($_dirs) && count($_dirs) > 0) {
                    foreach ($_dirs as $dir) {
                        if (is_link($dir)) {
                            unlink($dir);
                        }
                        elseif (is_dir($dir)) {
                            if (jrCore_delete_dir_contents($dir, false)) {
                                rmdir($dir);
                            }
                        }
                    }
                }

                // Remove custom lang entries
                $tbl = jrCore_db_table_name('jrUser', 'language');
                $req = "DELETE FROM {$tbl} WHERE `lang_module` = '{$skn}'";
                jrCore_db_query($req);

                // Remove custom templates
                $tbl = jrCore_db_table_name('jrCore', 'template');
                $req = "DELETE FROM {$tbl} WHERE `template_module` = '{$skn}'";
                jrCore_db_query($req);

                // Remove settings
                $tbl = jrCore_db_table_name('jrCore', 'setting');
                $req = "DELETE FROM {$tbl} WHERE `module` = '{$skn}'";
                jrCore_db_query($req);

                jrCore_set_form_notice('success', 'The skin was successfully deleted');
                jrCore_form_delete_session();
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin");
            }
            jrCore_set_form_notice('success', 'The settings have been successfully saved');
            break;

    }
    jrCore_form_delete_session();
    jrCore_form_result('referrer');
}

//------------------------------
// admin (magic)
//------------------------------
function view_jrCore_admin($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_install_new_modules();

    // Reset any saved location
    if (function_exists('jrUser_delete_saved_url_location')) {
        jrUser_delete_saved_url_location();
    }

    $admin = '';
    $title = '';
    // See if we are getting an INDEX page for this module.  The Index
    // Page will tell us what "view" for the module config they are showing.
    // This can be either a config page for the module (i.e. global settings,
    // quota settings, language, etc.) OR it can be a tool.
    // Our URL will be like:
    // http://www.site.com/core/config/global
    // http://www.site.com/core/config/quota
    // http://www.site.com/core/config/language
    // http://www.site.com/core/config/tools
    if (!isset($_post['_1'])) {
        $_post['_1'] = 'global';
    }
    switch ($_post['_1']) {

        //------------------------------
        // GLOBAL SETTINGS
        //------------------------------
        case 'global':
            if (is_file(APP_DIR ."/repair.php")) {
                jrCore_set_form_notice('error', "<p>Delete the <strong>repair.php</strong> script from your root directory or rename it to <strong>repair.php.html</strong>!</p>", false);
            }
            $title = 'Global Config';
            $admin = jrCore_show_global_settings('module', $_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // QUOTA SETTINGS
        //------------------------------
        case 'quota':
            $title = 'Quota Config';
            $admin = jrProfile_show_module_quota_settings($_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // TOOLS
        //------------------------------
        case 'tools':
            $title = 'Tools';
            $admin = jrCore_show_module_tools($_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // LANGUAGE STRINGS
        //------------------------------
        case 'language':
            $title = 'Language Strings';
            $admin = jrUser_show_module_lang_strings('module', $_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // TEMPLATES
        //------------------------------
        case 'templates':
            $title = 'Templates';
            $admin = jrCore_show_module_templates($_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // IMAGES
        //------------------------------
        case 'images':
            $title = 'Images';
            $admin = jrCore_show_skin_images('module', $_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // INFO
        //------------------------------
        case 'info':
            $title = 'Info';
            $admin = jrCore_show_module_info($_post['module'], $_post, $_user, $_conf);
            break;

    }

    // Process view
    $_rep = array(
        'active_tab'         => 'modules',
        'admin_page_content' => $admin
    );

    $_tmp = array();
    foreach ($_mods as $mod_dir => $_inf) {
        $_tmp["{$_inf['module_name']}"] = $mod_dir;
    }
    ksort($_tmp);

    $_out = array();
    foreach ($_tmp as $mod_dir) {
        if (!isset($_mods[$mod_dir]['module_category'])) {
            $_mods[$mod_dir]['module_category'] = 'tools';
        }
        $cat = $_mods[$mod_dir]['module_category'];
        if (!isset($_out[$cat])) {
            $_out[$cat] = array();
        }
        $_out[$cat][$mod_dir] = $_mods[$mod_dir];
    }
    $_rep['_modules']['core'] = $_out['core'];
    unset($_out['core']);
    $_rep['_modules'] = $_rep['_modules'] + $_out;
    ksort($_rep['_modules']);
    unset($_out);

    // See if our skin is overriding our core admin template
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/admin.tpl")) {
        $html = jrCore_parse_template('admin.tpl', $_rep);
    }
    else {
        $html = jrCore_parse_template('admin.tpl', $_rep, 'jrCore');
    }

    // Output
    $_mta = jrCore_module_meta_data($_post['module']);
    jrCore_page_title("{$title} - {$_mta['name']}");
    jrCore_admin_menu_accordion_js();
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// admin_save (magic)
//------------------------------
function view_jrCore_admin_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);

    // See what we are saving...
    switch ($_post['_1']) {

        case 'global':

            // See if this module is presenting us with a validate function
            if (is_file(APP_DIR . "/modules/{$_post['module']}/config.php")) {
                $vfunc = "{$_post['module']}_config_validate";
                if (!function_exists($vfunc)) {
                    require_once APP_DIR . "/modules/{$_post['module']}/config.php";
                }
                if (function_exists($vfunc)) {
                    $_temp = $vfunc($_post);
                    if (!$_temp) {
                        // Error in validation
                        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/global");
                        return true;
                    }
                    $_post = $_temp;
                    unset($_temp);
                }
            }
            // Update
            foreach ($_post as $k => $v) {
                if (isset($_conf["{$_post['module']}_{$k}"]) && $v != $_conf["{$_post['module']}_{$k}"]) {
                    jrCore_set_setting_value($_post['module'], $k, $v);
                }
            }
            jrCore_delete_all_cache_entries('jrCore', 0);
            jrCore_set_form_notice('success', 'The settings have been successfully saved');
            break;

        case 'quota':

            // See if this module is presenting us with a validate function
            if (is_file(APP_DIR . "/modules/{$_post['module']}/quota.php")) {
                $vfunc = "{$_post['module']}_quota_config_validate";
                if (!function_exists($vfunc)) {
                    require_once APP_DIR . "/modules/{$_post['module']}/quota.php";
                }
                if (function_exists($vfunc)) {
                    $_temp = $vfunc($_post);
                    if (!$_temp) {
                        // Error in validation
                        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}");
                        return true;
                    }
                    $_post = $_temp;
                    unset($_temp);
                }
            }

            // See if we are doing a single quota or ALL quotas
            if (isset($_post['apply_to_all_quotas']) && $_post['apply_to_all_quotas'] == 'on') {
                $_aq = jrProfile_get_quotas();
                foreach ($_aq as $qid => $qname) {
                    $_qt = jrProfile_get_quota($_post['id'], false);
                    foreach ($_post as $k => $v) {
                        if (isset($_qt["quota_{$_post['module']}_{$k}"])) {
                            jrProfile_set_quota_value($_post['module'], $qid, $k, $v);
                        }
                    }
                }
            }
            else {
                if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
                    jrCore_set_form_notice('error', 'Invalid quota_id');
                    jrCore_form_result();
                }
                // Get current settings for this Quota
                $_qt = jrProfile_get_quota($_post['id'], false);
                if (!isset($_qt) || !is_array($_qt)) {
                    jrCore_set_form_notice('error', 'Invalid quota_id - unable to retrieve settings');
                    jrCore_form_result();
                }
                // Update
                foreach ($_post as $k => $v) {
                    if (isset($_qt["quota_{$_post['module']}_{$k}"])) {
                        jrProfile_set_quota_value($_post['module'], $_post['id'], $k, $v);
                    }
                }
            }

            // Empty caches
            jrCore_delete_all_cache_entries();

            jrCore_form_delete_session();
            jrCore_set_form_notice('success', 'The settings have been successfully saved');
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}/id={$_post['id']}");
            break;

        case 'info':

            // Update
            $tbl = jrCore_db_table_name('jrCore', 'module');

            if (isset($_post['module_delete']) && $_post['module_delete'] === 'on') {

                // There are some modules we cannot delete
                switch ($_post['module']) {
                    case 'jrCore':
                    case 'jrUser':
                    case 'jrProfile':
                    case 'jrImage':
                        jrCore_set_form_notice('error', "The {$_post['module']} module cannot be deleted - it is a required core module");
                        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info");
                        break;
                }

                // Remove cache directory
                $cdr = jrCore_get_module_cache_dir($_post['module']);
                jrCore_delete_dir_contents($cdr);
                rmdir($cdr);

                // Delete from modules table
                $mod = jrCore_db_escape($_post['module']);
                $req = "DELETE FROM {$tbl} WHERE module_directory = '{$mod}' LIMIT 1";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt !== 1) {
                    jrCore_set_form_notice('error', 'An error was encountered deleting the module from the database - please try again');
                    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info");
                }

                // NOTE: We do not delete this module's datastore - if they decide to re-install then their data will be preserved

                // Cleanup all directories
                $_dirs = glob(APP_DIR ."/modules/{$_post['module']}*");
                if ($_dirs && is_array($_dirs) && count($_dirs) > 0) {
                    foreach ($_dirs as $dir) {
                        if (is_link($dir)) {
                            unlink($dir);
                        }
                        elseif (is_dir($dir)) {
                            if (jrCore_delete_dir_contents($dir, false)) {
                                rmdir($dir);
                            }
                        }
                    }
                }

                jrCore_set_form_notice('success', 'The module was successfully deleted');
                jrCore_form_delete_session();

                jrCore_delete_all_cache_entries('jrCore');
                $_mods["{$_post['module']}"]['module_active'] = 0;

                // Rebuild JS and CSS
                jrCore_create_master_css($_conf['jrCore_active_skin']);
                jrCore_create_master_javascript($_conf['jrCore_active_skin']);

                jrCore_form_delete_session();
                $url = jrCore_get_module_url('jrCore');
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$url}/admin/global");
            }
            else {
                $url = jrCore_db_escape($_post['module_url']);
                if (isset($_post['new_module_url']) && jrCore_checktype($_post['new_module_url'], 'url_name')) {
                    $url = jrCore_db_escape($_post['new_module_url']);
                    $_post['module_url'] = $_post['new_module_url'];
                }
                $cat = jrCore_db_escape($_post['new_module_category']);
                $act = (isset($_post['module_active']) && $_post['module_active'] == 'off') ? '0' : '1';
                $mod = jrCore_db_escape($_post['module']);
                $req = "UPDATE {$tbl} SET module_updated = UNIX_TIMESTAMP(), module_url = '{$url}', module_active = '{$act}', module_category = '{$cat}' WHERE module_directory = '{$mod}' LIMIT 1";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!isset($cnt) || $cnt !== 1) {
                    jrCore_set_form_notice('error', 'An error was encountered saving the module settings - please try again');
                    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info");
                }
                // Verify the module if we are turning it on
                if ((!isset($_mods[$mod]['module_active']) || $_mods[$mod]['module_active'] != '1') && $act == '1') {
                    jrCore_verify_module($mod);
                    jrCore_delete_all_cache_entries('jrUser'); // resets language caches when activating a new module
                }
                jrCore_delete_all_cache_entries('jrCore');
                $_mods["{$_post['module']}"]['module_active'] = $act;

                // Rebuild JS and CSS
                jrCore_create_master_css($_conf['jrCore_active_skin']);
                jrCore_create_master_javascript($_conf['jrCore_active_skin']);

                jrCore_form_delete_session();
                jrCore_set_form_notice('success', 'The settings have been successfully saved');
            }
            break;

        case 'language':

            // Get all the lang strings for this module
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $mod = jrCore_db_escape($_post['module']);
            $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '" . jrCore_db_escape($_post['lang_code']) . "'";
            $_rt = jrCore_db_query($req, 'lang_id');
            if (!isset($_rt) || !is_array($_rt)) {
                jrCore_set_form_notice('error', "Unable to retrieve language settings for module from language table - check debug_log errors");
                jrCore_form_result();
            }
            $req = "UPDATE {$tbl} SET lang_text = CASE lang_id\n";
            foreach ($_rt as $key => $_lng) {
                if (isset($_post["lang_{$key}"])) {
                    $req .= "WHEN {$key} THEN '" . jrCore_db_escape($_post["lang_{$key}"]) . "'\n";
                }
            }
            if (isset($req) && strpos($req, 'THEN')) {
                $req .= "ELSE lang_text END";
                jrCore_db_query($req);
            }
            jrCore_delete_all_cache_entries('jrUser');
            jrCore_set_form_notice('success', 'The language strings have been successfully saved');
            jrCore_form_delete_session();
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}/lang_code={$_post['lang_code']}/p={$_post['p']}");
            break;

        case 'images':

            jrCore_create_media_directory(0);
            // Get existing module info to see what images we have customized
            $_im = array();
            if (isset($_conf["jrCore_{$_post['module']}_custom_images"]{2})) {
                $_im = json_decode($_conf["jrCore_{$_post['module']}_custom_images"], true);
            }
            // Check for new custom files being uploaded
            $_up = jrCore_get_uploaded_meter_files($_post['upload_token']);
            if (isset($_up) && is_array($_up)) {
                foreach ($_up as $_info) {
                    jrCore_copy_media_file(0, $_info['tmp_name'], "mod_{$_post['module']}_{$_info['name']}");
                    $_im["{$_info['name']}"] = array($_info['size'], 'on');
                }
            }
            // Go through and save our uploaded images (if any)
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $k => $_info) {
                    $num = (int) str_replace('file_', '', $k);
                    $nam = $_post["name_{$num}"];
                    if (isset($_info['size']) && jrCore_checktype($_info['size'], 'number_nz')) {
                        // Image extensions must match
                        $ext = jrCore_file_extension($_info['name']);
                        switch ($ext) {
                            case 'jpg':
                            case 'png':
                            case 'gif':
                                break;
                            default:
                                jrCore_set_form_notice('error', 'Invalid image type for ' . $_post["name_{$num}"] . ' - only JPG, PNG and GIF images are allowed');
                                jrCore_form_result();
                                break;
                        }
                        if (isset($_post["name_{$num}"]{0})) {
                            jrCore_copy_media_file(0, $_info['tmp_name'], "mod_{$_post['module']}_{$nam}");
                            $_im[$nam] = array($_info['size']);
                        }
                    }
                }
            }
            // Update setting with new values
            // [name_0_active] => on
            // [name_0] => bckgrd.png
            foreach ($_post as $k => $v) {
                if (strpos($k, 'name_') === 0 && strpos($k, '_active')) {
                    $num = (int) substr($k, 5, strrpos($k, '_'));
                    $nam = $_post["name_{$num}"];
                    if (isset($_im[$nam][0])) {
                        $_im[$nam][1] = $v;
                    }
                    else {
                        unset($_im[$nam]);
                    }
                }
            }
            jrCore_set_setting_value('jrCore', "{$_post['module']}_custom_images", json_encode($_im));
            jrCore_delete_all_cache_entries('jrCore', 0);
            break;

        case 'templates':

            //  [form_begin_template_active] => on
            $_act = array();
            $_off = array();
            $_all = array();
            foreach ($_post as $k => $v) {
                if (strpos($k, '_template_active')) {
                    $tpl = str_replace('_template_active', '.tpl', $k);
                    // See if we are turning this template on or off
                    if ($v == 'on') {
                        $_act[] = $tpl;
                        $_all[] = $tpl;
                    }
                    else {
                        $_off[] = $tpl;
                        $_all[] = $tpl;
                    }
                }
            }

            // Set active/inactive
            if (isset($_all) && is_array($_all) && count($_all) > 0) {
                $mod = jrCore_db_escape($_post['module']);
                $tbl = jrCore_db_table_name('jrCore', 'template');
                if (isset($_act) && is_array($_act) && count($_act) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '1', template_updated = UNIX_TIMESTAMP() WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_act) . "')";
                    jrCore_db_query($req);
                }
                if (isset($_off) && is_array($_off) && count($_off) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '0', template_updated = UNIX_TIMESTAMP() WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_off) . "')";
                    jrCore_db_query($req);
                }

                // Reset cache for any that were changed
                foreach ($_all as $tpl) {
                    jrCore_get_template_file($tpl, $_post['module'], 'reset');
                }
            }
            jrCore_set_form_notice('success', 'The template settings have been successfully saved');
            break;
    }
    jrCore_form_delete_session();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}");
    return true;
}

//------------------------------
// template_replace
//------------------------------
function view_jrCore_template_replace($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || ($_post['_1'] != 'skin' && $_post['_1'] != 'module')) {
        jrCore_notice_page('error', 'invalid replace type - please try again');
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'md5')) {
        jrCore_notice_page('error', 'invalid replace information - please try again');
    }
    $nfile = false;
    $version = jrCore_get_temp_value('jrCore', $_post['_2']);
    if (!$version || strlen($version) === 0) {
        jrCore_notice_page('error', 'invalid replace information - please try again');
    }
    jrCore_delete_temp_value('jrCore', $_post['_2']);
    list($new, $mod, $tpl, $ver, $cid) = explode('/', $version);
    // Make sure file exists
    if ($_post['_1'] == 'skin') {
        $_mta = jrCore_skin_meta_data($mod);
        if (!$_mta) {
            jrCore_notice_page('error', 'invalid skin received - please try again');
        }
        if ($ver == $_mta['version']) {
            $file = APP_DIR ."/skins/{$mod}/{$tpl}";
        }
        else {
            $file = APP_DIR ."/skins/{$mod}-release-{$ver}/{$tpl}";
        }
        $nfile = APP_DIR ."/skins/{$new}/{$tpl}";
    }
    else {
        if (!isset($_mods[$mod])) {
            jrCore_notice_page('error', 'invalid module received - please try again');
        }
        if ($ver == $_mods[$mod]['module_version']) {
            $file = APP_DIR ."/modules/{$mod}/templates/{$tpl}";
        }
        else {
            $file = APP_DIR ."/modules/{$mod}-release-{$ver}/templates/{$tpl}";
        }
    }
    if (!is_file($file)) {
        jrCore_notice_page('error', 'invalid replace information - file does not exist');
    }

    if (jrCore_checktype($cid, 'number_nz')) {
        // Get our custom template
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$cid}'";
        $_tp = jrCore_db_query($req, 'SINGLE');
        if (!$_tp || !is_array($_tp)) {
            jrCore_notice_page('error', 'invalid custom template_id - please try again (2)');
        }
        // Get contents and update
        $upd = file_get_contents($file);
        $req = "UPDATE {$tbl} SET template_updated = UNIX_TIMESTAMP(), template_user = '" . jrCore_db_escape($_user['user_name']) . "', template_body = '" . jrCore_db_escape($upd) . "' WHERE template_id = '{$cid}' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt === 1) {
            jrCore_set_form_notice('success', 'The template has been successfully updated');
            jrCore_location('referrer');
        }
    }
    elseif ($nfile) {
        // Template to Template
        $tmp = file_get_contents($file);
        $tmp = str_replace($mod, $new, $tmp);
        if (jrCore_write_to_file($nfile, $tmp, 'overwrite')) {
            jrCore_set_form_notice('success', 'The template has been successfully updated');
        }
        else {
            jrCore_set_form_notice('error', 'Unable to copy template file to custom skin directory - check permissions');
        }
        jrCore_location('referrer');
    }
    jrCore_notice_page('error', 'error updating template in the database - please try again');
}

//------------------------------
// template_modify (magic)
//------------------------------
function view_jrCore_template_modify($_post, $_user, $_conf)
{
    jrUser_master_only();

    // Setup Code Mirror
    $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.css");
    jrCore_create_page_element('css_href', $_tmp);
    $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.js");
    jrCore_create_page_element('javascript_href', $_tmp);
    $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js");
    jrCore_create_page_element('javascript_href', $_tmp);
    $_tmp = array('var editor = CodeMirror.fromTextArea(document.getElementById("template_body"), { lineNumbers: true, matchBrackets: true, mode: \'smarty\' });');
    jrCore_create_page_element('javascript_ready_function', $_tmp);

    jrCore_page_include_admin_menu();

    if (isset($_post['skin'])) {
        jrCore_page_skin_tabs($_post['skin'], 'templates');
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$_post['skin']}";
        $t_type = 'skin';
    }
    else {
        jrCore_page_admin_tabs($_post['module'], 'templates');
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates";
        $t_type = 'module';
    }

    // our page banner
    jrCore_page_banner('Template Editor');

    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => $cancel_url,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Template ID
    $_tmp = array(
        'name'  => 'template_type',
        'type'  => 'hidden',
        'value' => $t_type
    );
    jrCore_form_field_create($_tmp);

    if (isset($_post['skin']{0})) {
        $_tmp = array(
            'name'  => 'skin',
            'type'  => 'hidden',
            'value' => $_post['skin']
        );
        jrCore_form_field_create($_tmp);
    }

    // Get info about this template...
    $tpl_body = '';
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        // Database template
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['id']}'";
        $_tp = jrCore_db_query($req, 'SINGLE');
        if (!isset($_tp) || !is_array($_tp)) {
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_location($cancel_url);
        }
        $tpl_body = $_tp['template_body'];

        // Template ID
        $_tmp = array(
            'name'  => 'template_id',
            'type'  => 'hidden',
            'value' => $_post['id']
        );
        jrCore_form_field_create($_tmp);
    }
    // From file
    elseif (isset($_post['template']{1}) && jrCore_checktype($_post['template'], 'printable')) {

        // Make sure this is a good file
        $_post['template'] = basename($_post['template']);
        if (isset($_post['skin']{0})) {
            $tpl_file = APP_DIR . "/skins/{$_post['skin']}/{$_post['template']}";
        }
        else {
            $tpl_file = APP_DIR . "/modules/{$_post['module']}/templates/{$_post['template']}";
        }
        if (!is_file($tpl_file)) {
            jrCore_set_form_notice('error', 'Template file not found - please try again');
            jrCore_location($cancel_url);
        }
        $tpl_body = file_get_contents($tpl_file);
        jrCore_page_custom(str_replace(APP_DIR .'/', '', $tpl_file), 'modifying:');

        $_tmp = array(
            'name'  => 'template_name',
            'type'  => 'hidden',
            'value' => $_post['template']
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        jrCore_set_form_notice('error', 'Invalid template - please try again');
        jrCore_location($cancel_url);
    }

    // Show template
    if (isset($_SESSION['template_body_save']) && strlen($_SESSION['template_body_save']) > 0) {
        $tpl_body = $_SESSION['template_body_save'];
        unset($_SESSION['template_body_save']);
    }
    $html = '<div class="form_template"><textarea id="template_body" name="template_body" class="form_template_editor">' . htmlspecialchars($tpl_body) . '</textarea></div>';
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// test_template
//------------------------------
function view_jrCore_test_template($_post, $_user, $_conf)
{
    global $_mods;
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        echo "error: invalid template";
        exit;
    }
    $cdr = jrCore_get_module_cache_dir('jrCore');
    $nam = $_post['_1'];
    if (!is_file("{$cdr}/{$nam}")) {
        echo "error : unable to open template file for testing";
        exit;
    }
    ini_set('display_errors', 1);
    ini_set('log_errors', 0);

    if (!class_exists('Smarty')) {
        require_once APP_DIR .'/modules/jrCore/contrib/smarty/libs/Smarty.class.php';
    }

    // Set our compile dir
    $temp = new Smarty;
    $temp->compile_id  = md5(APP_DIR);
    $temp->setCompileDir(APP_DIR .'/data/cache/'. $_conf['jrCore_active_skin']);

    // Get plugin directories
    $_dir = array(APP_DIR .'/modules/jrCore/contrib/smarty/libs/plugins');
    $temp->setPluginsDir($_dir);
    $temp->force_compile = true;

    $_data['page_title']  = jrCore_get_flag('jrcore_html_page_title');
    $_data['jamroom_dir'] = APP_DIR;
    $_data['jamroom_url'] = $_conf['jrCore_base_url'];
    $_data['_conf']       = $_conf;
    $_data['_post']       = $_post;
    $_data['_mods']       = $_mods;
    $_data['_user']       = $_SESSION;
    $_data['_items']      = array();

    // Remove User and MySQL info - we don't want this to ever leak into a template
    unset($_data['_user']['user_password'],$_data['_user']['user_old_password'],$_data['_user']['user_forgot_key']);
    unset($_data['_conf']['jrCore_db_host'],$_data['_conf']['jrCore_db_user'],$_data['_conf']['jrCore_db_pass'],$_data['_conf']['jrCore_db_name'],$_data['_conf']['jrCore_db_port']);

    $temp->assign($_data);
    ob_start();
    $temp->display("{$cdr}/{$nam}");
    $html = ob_get_contents();
    ob_end_clean();
    echo $html;
    exit;
}

//------------------------------
// template_modify_save (magic)
//------------------------------
function view_jrCore_template_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();

    // See if we are doing a skin or module
    $tid = false;
    $crt = false;
    $mod = (isset($_post['skin'])) ? $_post['skin'] : $_post['module'];

    // We need to test this template and make sure it does not cause any Smarty errors
    $cdr = jrCore_get_module_cache_dir('jrCore');
    $nam = time() . ".tpl";
    jrCore_write_to_file("{$cdr}/{$nam}", $_post['template_body']);
    $out = jrCore_load_url("{$_conf['jrCore_base_url']}/{$_post['module_url']}/test_template/{$nam}");
    if (isset($out) && strlen($out) > 1 && (strpos($out,'error:') === 0 || stristr($out,'fatal error'))) {
        $_SESSION['template_body_save'] = $_post['template_body'];
        unlink("{$cdr}/{$nam}");
        jrCore_set_form_notice('error', 'There is a syntax error in your template - please fix and try again');
        jrCore_form_result();
    }
    unlink("{$cdr}/{$nam}");

    $tbl = jrCore_db_table_name('jrCore', 'template');
    // See if we are updating a DB template or first time file
    if (isset($_post['template_id']) && jrCore_checktype($_post['template_id'], 'number_nz')) {
        // Make sure we have a valid template
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['template_id']}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!isset($_rt) || !is_array($_rt)) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_form_result();
        }
        $req = "UPDATE {$tbl} SET
                  template_updated = UNIX_TIMESTAMP(),
                  template_user    = '" . jrCore_db_escape($_user['user_name']) . "',
                  template_body    = '" . jrCore_db_escape($_post['template_body']) . "'
                 WHERE template_id = '{$_post['template_id']}'";
        $cnt = jrCore_db_query($req, 'COUNT');
        // Reset the template cache
        jrCore_get_template_file($_rt['template_name'], $mod, 'reset');
    }
    else {
        if (!isset($_post['template_name']{1})) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_name - please try again');
            jrCore_form_result();
        }
        // See if we already exist - this can happen when the user FIRST modifies the template
        // and does not leave the screen, and modifies again
        $nam = jrCore_db_escape($_post['template_name']);
        $mod = jrCore_db_escape($mod);
        $req = "INSERT INTO {$tbl} (template_created,template_updated,template_user,template_active,template_name,template_module,template_body)
                VALUES(UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'" . jrCore_db_escape($_user['user_name']) . "','0','{$nam}','{$mod}','" . jrCore_db_escape($_post['template_body']) . "')";
        $tid = jrCore_db_query($req, 'INSERT_ID');
        if (isset($tid) && jrCore_checktype($tid, 'number_nz')) {
            $cnt = 1;
            // Reset the template cache
            jrCore_get_template_file($_post['template_name'], $mod, 'reset');
        }
        $crt = true;
    }
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The template has been successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the template update - please try again');
    }
    jrCore_form_delete_session();
    // If we have just CREATED a new template, we must refresh on the ID
    if ($tid && $crt) {
        if (isset($_post['skin'])) {
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/skin={$_post['skin']}/id={$tid}");
        }
        else {
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/id={$tid}");
        }
    }
    jrCore_form_result();
}

//------------------------------
// cache_reset
//------------------------------
function view_jrCore_cache_reset($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_page_banner('Reset Caches');

    // Form init
    $_tmp = array(
        'submit_value' => 'reset selected caches',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools"
    );
    jrCore_form_create($_tmp);

    // Reset Smarty cache
    $_tmp = array(
        'name'     => 'reset_template_cache',
        'label'    => 'Reset Template Cache',
        'help'     => 'Check this box to delete the compiled skin templates, CSS and Javascript - these items will be rebuilt as needed.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Reset Database Cache
    $_tmp = array(
        'name'     => 'reset_database_cache',
        'label'    => 'Reset Database Cache',
        'help'     => 'Check this box to delete cached skin and profile pages in the database.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Reset Sprite Cache
    $_tmp = array(
        'name'     => 'reset_sprite_cache',
        'label'    => 'Reset Icon Cache',
        'help'     => 'Check this box to delete the cached sprite icon images so they are rebuilt.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// cache_reset_save
//------------------------------
function view_jrCore_cache_reset_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Reset cache directories
    if (isset($_post['reset_template_cache']) && $_post['reset_template_cache'] == 'on') {
        $_tmp = glob(APP_DIR ."/data/cache/*");
        if (is_array($_tmp)) {
            $_dirs = array();
            foreach ($_tmp as $path) {
                if (is_dir($path)) {
                    $_dirs[] = basename($path);
                }
            }
            $_dirs = jrCore_trigger_event('jrCore', 'template_cache_reset', $_dirs); // "template_cache_reset" event trigger
            foreach ($_dirs as $dir) {
                if ($dir == 'jrImage') {
                    // Image cache is handled by separate Image cache reset tool
                    continue;
                }
                if (is_dir(APP_DIR ."/data/cache/{$dir}")) {
                    jrCore_delete_dir_contents(APP_DIR ."/data/cache/{$dir}");
                }
            }
        }
    }

    // Reset database cache
    if (isset($_post['reset_database_cache']) && $_post['reset_database_cache'] == 'on') {
        jrCore_delete_all_cache_entries();
    }

    // Remove any generated Sprite images and Spire CSS files
    if (isset($_post['reset_sprite_cache']) && $_post['reset_sprite_cache'] == 'on') {
        $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
        $_fl = glob("{$dir}/*sprite*");
        if (isset($_fl) && is_array($_fl)) {
            foreach ($_fl as $file) {
                unlink($file);
            }
        }
    }

    jrCore_set_form_notice('success', 'The selected caches were successfully reset');
    jrCore_form_result();
}

//------------------------------
// skin_image_delete_save
//------------------------------
function view_jrCore_skin_image_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['skin']{0}) && !isset($_post['mod']{0})) {
        jrCore_set_form_notice('error', 'Invalid skin or module - please try again');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['name']{0})) {
        jrCore_set_form_notice('error', 'Invalid image name - please try again');
        jrCore_form_result('referrer');
    }
    if (isset($_post['mod']{0})) {
        $nam = $_post['mod'];
        $tag = 'mod_';
    }
    else {
        $nam = $_post['skin'];
        $tag = '';
    }
    // Remove from custom image info
    if (isset($_conf["jrCore_{$nam}_custom_images"]{2})) {
        $_im = json_decode($_conf["jrCore_{$nam}_custom_images"], true);
        unset($_im["{$_post['name']}"]);
        // Update setting with new values
        jrCore_set_setting_value('jrCore', "{$nam}_custom_images", json_encode($_im));
        jrCore_delete_all_cache_entries('jrCore', 0);
        jrCore_delete_media_file(0, "{$tag}{$nam}_{$_post['name']}");
    }
    jrCore_set_form_notice('success', 'The custom image was successfully deleted');
    jrCore_form_result('referrer');
}

//------------------------------
// template_reset_save
//------------------------------
function view_jrCore_template_reset_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // Reset smarty cache
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid template_id - please try again');
        jrCore_form_result('referrer');
    }
    // Get info about this template first so we can reset
    $tbl = jrCore_db_table_name('jrCore', 'template');
    $req = "SELECT template_name, template_module FROM {$tbl} WHERE template_id = '{$_post['id']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid template_id - please try again');
        jrCore_form_result('referrer');
    }
    $req = "DELETE FROM {$tbl} WHERE template_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_get_template_file($_rt['template_name'], $_rt['template_module'], 'reset');
        jrCore_set_form_notice('success', 'The template has been reset to use the default version');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the modified template from the database - please try again');
    }
    jrCore_form_result();
}

//------------------------------
// css_reset_save
//------------------------------
function view_jrCore_css_reset_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // Reset CSS elements
    if (!isset($_post['skin']{0})) {
        jrCore_set_form_notice('error', 'Invalid skin - please try again');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['tag']{0})) {
        jrCore_set_form_notice('error', 'Invalid element tag - please try again');
        jrCore_form_result('referrer');
    }
    $_post['tag'] = urldecode($_post['tag']);
    // Remove info about this element from the custom css
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && strlen($_rt['skin_custom_css']) > 3) {
        $_new = json_decode($_rt['skin_custom_css'], true);
        if (isset($_new) && is_array($_new)) {
            if (isset($_new["{$_post['tag']}"])) {
                unset($_new["{$_post['tag']}"]);
                $_new = json_encode($_new);
                $req = "UPDATE {$tbl} SET skin_updated = UNIX_TIMESTAMP(), skin_custom_css = '" . jrCore_db_escape($_new) . "' WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!isset($cnt) || $cnt === 0) {
                    jrCore_set_form_notice('error', 'An error was enountered saving the custom style to the database - please try again');
                    jrCore_form_result('referrer');
                }
            }
        }
    }
    jrCore_form_delete_session();
    // Cleanup any cached CSS files so it is rebuilt
    $cdir = jrCore_get_module_cache_dir($_post['skin']);
    $_tmp = glob("{$cdir}/*.css");
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $tmp_file) {
            unlink($tmp_file);
        }
    }
    jrCore_form_result('referrer');
}

//------------------------------
// integrity_check
//------------------------------
function view_jrCore_integrity_check($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_page_banner("Integrity Check");

    // Form init
    $_tmp = array(
        'submit_value'  => 'run integrity check',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Are you sure you want to run the Integrity Check? Please be patient - on large systems this could take some time.',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the Integrity Check runs'
    );
    jrCore_form_create($_tmp);

    // Validate Modules
    $_tmp = array(
        'name'     => 'validate_modules',
        'label'    => 'validate modules',
        'help'     => 'Check this box so the system will validate active modules and the structure of your database tables.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Validate Skins
    $_tmp = array(
        'name'     => 'validate_skins',
        'label'    => 'validate skins',
        'help'     => 'Check this box so the system will validate active skins and and skin config options.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Repair Tables
    $_tmp = array(
        'name'     => 'repair_tables',
        'label'    => 'repair tables',
        'help'     => 'If you suspect that some of your MySQL tables are corrupt, check this box and REPAIR TABLE will be run on each of your database tables.<br><br><strong>WARNING:</strong> While a repair is running on a table, access to that table will be locked. The repair operation could take several minutes for very large tables.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Optimize Tables
    $_tmp = array(
        'name'     => 'optimize_tables',
        'label'    => 'optimize tables',
        'help'     => 'Check this option to run OPTIMIZE TABLE on each database table.  This is helpful for sites that have been running a long time where the table data file can become &quot;fragmented&quot; and make data access a little bit slower.<br><br><strong>WARNING:</strong> While OPTIMIZE TABLE is running on a table, access to that table will be locked - the operation could take several minutes for very large tables.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Cache Reset
    $_tmp = array(
        'name'     => 'cache_reset',
        'label'    => 'clear caches',
        'help'     => 'Check this box to clear the stored template and database caches',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// integrity_check_save
//------------------------------
function view_jrCore_integrity_check_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    jrCore_logger('INF', 'integrity check started');

    // Check for Repair Tables first
    if (isset($_post['repair_tables']) && $_post['repair_tables'] == 'on') {
        $_rt = jrCore_db_query('SHOW TABLES','NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $tbl) {
                $tbl = reset($tbl);
                jrCore_form_modal_notice('update', "repairing table: {$tbl}");
                jrCore_db_query("REPAIR TABLE {$tbl}");
            }
        }
    }


    // Module install validation
    if (isset($_post['validate_modules']) && $_post['validate_modules'] == 'on') {

        // Make sure our Core schema is updated first
        require_once APP_DIR . '/modules/jrCore/schema.php';
        jrCore_db_schema();

        // Check for new modules
        jrCore_install_new_modules();

        //----------------------
        // MODULES
        //----------------------
        // Make sure module is setup
        foreach ($_mods as $mod_dir => $_inf) {
            if (!is_dir(APP_DIR . "/modules/{$mod_dir}") && !is_link(APP_DIR . "/modules/{$mod_dir}")) {
                // Looks like this module was removed from the filesystem - let's do a cleanup
                $tbl = jrCore_db_table_name('jrCore', 'module');
                $req = "DELETE FROM {$tbl} WHERE module_directory = '" . jrCore_db_escape($mod_dir) . "' LIMIT 1";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt !== 1) {
                    jrCore_form_modal_notice('error', "unable to cleanup deleted module: {$mod_dir}");
                }
                // Cleanup any cache
                $cdr = jrCore_get_module_cache_dir($mod_dir);
                if (is_dir($cdr)) {
                    jrCore_delete_dir_contents($cdr);
                    rmdir($cdr);
                }
            }
            jrCore_form_modal_notice('update', "verifying module: {$mod_dir}");
            jrCore_verify_module($mod_dir);
        }
    }

    // Skin install validation
    if (isset($_post['validate_skins']) && $_post['validate_skins'] == 'on') {

        //----------------------
        // SKINS
        //----------------------
        $_rt = jrCore_get_skins();
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $skin_dir) {

                jrCore_form_modal_notice('update', "verifying skin: {$skin_dir}");
                // config
                if (is_file(APP_DIR . "/skins/{$skin_dir}/config.php")) {
                    require_once APP_DIR . "/skins/{$skin_dir}/config.php";
                    $func = "{$skin_dir}_skin_config";
                    if (function_exists($func)) {
                        $func();
                    }
                }
                // quota
                if (is_file(APP_DIR . "/skins/{$skin_dir}/quota.php")) {
                    require_once APP_DIR . "/skins/{$skin_dir}/quota.php";
                    $func = "{$skin_dir}_skin_quota_config";
                    if (function_exists($func)) {
                        $func();
                    }
                }
                // lang strings
                if (is_dir(APP_DIR . "/skins/{$skin_dir}/lang")) {
                    jrUser_install_lang_strings('skin', $skin_dir);
                }

                // Make sure this skin is configured in the skins table
                $_mt = jrCore_skin_meta_data($skin_dir);
                if (is_array($_mt)) {
                    $tbl = jrCore_db_table_name('jrCore', 'skin');
                    $req = "INSERT INTO {$tbl} (skin_directory, skin_updated, skin_custom_css, skin_custom_image) VALUES ('{$skin_dir}', UNIX_TIMESTAMP(), '', '') ON DUPLICATE KEY UPDATE skin_updated = UNIX_TIMESTAMP()";
                    jrCore_db_query($req);
                }
            }
        }
    }

    // Cache reset
    if (isset($_post['cache_reset']) && $_post['cache_reset'] == 'on') {
        jrCore_form_modal_notice('update', "clearing template cache");
        // Reset cache directories
        $_tmp = glob(APP_DIR ."/data/cache/*");
        if (is_array($_tmp)) {
            $_dirs = array();
            foreach ($_tmp as $path) {
                if (is_dir($path)) {
                    $_dirs[] = basename($path);
                }
            }
            $_dirs = jrCore_trigger_event('jrCore', 'template_cache_reset', $_dirs); // "template_cache_reset" event trigger
            foreach ($_dirs as $dir) {
                if ($dir == 'jrImage') {
                    // Image cache is handled by separate Image cache reset tool
                    continue;
                }
                if (is_dir(APP_DIR ."/data/cache/{$dir}")) {
                    jrCore_delete_dir_contents(APP_DIR ."/data/cache/{$dir}");
                }
            }
        }

        // Reset database cache
        jrCore_form_modal_notice('update', "resetting template cache");
        jrCore_delete_all_cache_entries();

        // Remove any generated Sprite images and Spire CSS files
        $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
        $_fl = glob("{$dir}/*sprite*");
        if ($_fl && is_array($_fl)) {
            foreach ($_fl as $file) {
                unlink($file);
            }
        }
    }

    // Optimize Tables
    if (isset($_post['optimize_tables']) && $_post['optimize_tables'] == 'on') {
        $_rt = jrCore_db_query('SHOW TABLES','NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $tbl) {
                $tbl = reset($tbl);
                jrCore_form_modal_notice('update', "optimizing table: {$tbl}");
                jrCore_db_query("OPTIMIZE TABLE {$tbl}");
            }
        }
    }

    jrCore_form_delete_session();
    jrCore_logger('INF', 'integrity check completed');
    jrCore_form_modal_notice('complete', 'The integrity check options were successfully completed');
    exit;
}

//------------------------------
// activity_log
//------------------------------
function view_jrCore_activity_log($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_install_new_modules();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_master_log_tabs('activity');
    jrCore_show_activity_log($_post, $_user, $_conf);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// log_debug
//------------------------------
function view_jrCore_log_debug($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_page_set_meta_header_only();
    jrCore_page_banner('debug entry');
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid log_debug id');
    }
    $tbl = jrCore_db_table_name('jrCore', 'log');
    $tbd = jrCore_db_table_name('jrCore', 'log_debug');
    $req = "SELECT * FROM {$tbl} l LEFT JOIN {$tbd} d ON d.log_log_id = l.log_id WHERE log_id = '{$_post['_1']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_notice_page('error', 'invalid log_debug id - not found in db');
    }

    $dat = array();
    $dat[1]['title'] = 'Key';
    $dat[1]['width'] = '10%';
    $dat[2]['title'] = 'Value';
    $dat[2]['width'] = '90%';
    jrCore_page_table_header($dat);

    $dat = array();
    $dat[1]['title'] = 'Message';
    $dat[2]['title'] = jrCore_entity_string($_rt['log_text']);
    $dat[2]['class'] = "log-{$_rt['log_priority']}";
    jrCore_page_table_row($dat);

    $dat = array();
    $dat[1]['title'] = 'Date';
    $dat[2]['title'] = jrCore_format_time($_rt['log_created']);
    jrCore_page_table_row($dat);

    $dat = array();
    $dat[1]['title'] = 'IP&nbsp;Address';
    $dat[2]['title'] = $_rt['log_ip'];
    jrCore_page_table_row($dat);

    $dat = array();
    $dat[1]['title'] = 'URL';
    $dat[2]['title'] = $_rt['log_url'];
    jrCore_page_table_row($dat);

    $dat = array();
    $dat[1]['title'] = 'Memory';
    $dat[2]['title'] = jrCore_format_size($_rt['log_memory']);
    jrCore_page_table_row($dat);

    $dat = array();
    $dat[1]['title'] = 'Data';
    $dat[2]['title'] = '<div style="font-family:monospace;white-space:pre-wrap">'. str_replace(',', ', ', jrCore_entity_string($_rt['log_data'])) .'</div>';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();
    jrCore_page_close_button();
    jrCore_page_display();
}

//------------------------------
// activity_log_delete
//------------------------------
function view_jrCore_activity_log_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid log id received - verify usage');
        jrCore_form_result();
    }
    $tbl = jrCore_db_table_name('jrCore', 'log_debug');
    $req = "DELETE FROM {$tbl} WHERE log_log_id = '{$_post['id']}' LIMIT 1";
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrCore', 'log');
    $req = "DELETE FROM {$tbl} WHERE log_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_form_result();
    }
    jrCore_set_form_notice('error', 'An error was encountered deleting the log entry - please try again');
    jrCore_form_result();
}

//------------------------------
// activity_log_download
//------------------------------
function view_jrCore_activity_log_download($_post, $_user, $_conf)
{
    jrUser_master_only();
    $tbl = jrCore_db_table_name('jrCore', 'log');
    $req = "SELECT * FROM {$tbl} ORDER BY `log_id` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt[0]) && is_array($_rt[0])) {
        $today = date("Ymd");
        $fn = "Activity_Log_{$today}.csv";
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"{$fn}\"");
        $data = '"ID","Created","Priority","IP","Text"' . "\n";
        foreach ($_rt as $_x) {
            $_x['log_created'] = jrCore_format_time($_x['log_created']);
            $_x['log_text'] = str_replace('"', '', $_x['log_text']);
            $data .= '"' . $_x['log_id'] . '","' . $_x['log_created'] . '","' . $_x['log_priority'] . '","' . $_x['log_ip'] . '","' . $_x['log_text'] . '"' . "\n";
        }
        echo $data;
    }
    else {
        jrCore_notice_page('error', 'No activity logs to download');
    }
}

//------------------------------
// activity_log_delete_all
//------------------------------
function view_jrCore_activity_log_delete_all($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    $tbl = jrCore_db_table_name('jrCore', 'log');
    $req = "TRUNCATE {$tbl}";
    jrCore_db_query($req);
    jrCore_form_result();
}

//------------------------------
// browser (datastore)
//------------------------------
function view_jrCore_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs($_post['module']);

    // start our html output
    jrCore_dashboard_browser('master', $_post, $_user, $_conf);

    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// browser_item_update
//------------------------------
function view_jrCore_browser_item_update($_post, $_user, $_conf)
{
    jrUser_admin_only();
    // See if we are an admin or master user...
    $url = jrCore_get_local_referrer();
    if (jrUser_is_master() && !strpos($url, 'dashboard')) {
        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs($_post['module']);
    }
    else {
        jrCore_page_dashboard_tabs('browser');
    }
    jrCore_page_banner('modify datastore item', "item id: {$_post['id']}");
    jrCore_get_form_notice();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item($_post['module'], $_post['id'], SKIP_TRIGGERS);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to retrieve item from DataStore - please try again');
    }
    // Go through each field and show it on a form
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Item ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_rt['_item_id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    $pfx = jrCore_db_get_prefix($_post['module']);
    foreach ($_rt as $k => $v) {
        if (strpos($k, $pfx) !== 0) {
            continue;
        }
        switch ($k) {
            case 'user_group':
            case 'user_password':
            case 'user_old_password':
                break;
            default:
                if (strpos($v, '{') === 0) {
                    // JSON - skin
                    continue;
                }
                // New Form Field
                if (strlen($v) > 128 || strpos(' ' . $v, "\n")) {
                    $_tmp = array(
                        'name'  => "ds_key_{$k}",
                        'label' => '<span style="text-transform:lowercase">' . $k . '</span>',
                        'type'  => 'textarea',
                        'value' => $v
                    );
                }
                else {
                    $_tmp = array(
                        'name'  => "ds_key_{$k}",
                        'label' => '<span style="text-transform:lowercase">' . $k . '</span>',
                        'type'  => 'text',
                        'value' => $v
                    );
                }
                jrCore_form_field_create($_tmp);
                break;
        }
    }

    // New Field...
    $err = '';
    if (isset($_SESSION['jr_form_field_highlight']['ds_browser_new_key'])) {
        unset($_SESSION['jr_form_field_highlight']['ds_browser_new_key']);
        $err = ' field-hilight';
    }
    $text = '<input type="text" class="form_text' . $err . '" id="ds_browser_new_key" name="ds_browser_new_key" value="">';
    $html = '<input type="text" class="form_text" id="ds_browser_new_value" name="ds_browser_new_value" value="">';
    $_tmp = array(
        'type'     => 'page_link_cell',
        'label'    => $text,
        'url'      => $html,
        'module'   => 'jrCore',
        'template' => 'page_link_cell.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    jrCore_page_display();
}

//---------------------- -------
// browser_item_update_save
//---------------------- -------
function view_jrCore_browser_item_update_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item($_post['module'], $_post['id'], SKIP_TRIGGERS);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to retrieve item from DataStore - please try again');
        jrCore_form_result();
    }
    $refresh = false;
    $_upd = array();
    foreach ($_post as $k => $v) {
        if (strpos($k, 'ds_key_') === 0) {
            switch ($k) {
                // Only the Master Admin can change the user_group
                case 'ds_key_user_group':
                    if (!jrUser_is_master()) {
                        continue 2;
                    }
                    break;
                case 'ds_key_user_password':
                    continue 2;
                    break;
            }
            $k = substr($k, 7);
            if (isset($_rt[$k]) && ($_rt[$k] != $v || strlen($v) === 0)) {
                // See if we are removing fields....
                if (strlen($v) === 0) {
                    // Remove field
                    $refresh = true;
                    jrCore_db_delete_item_key($_post['module'], $_post['id'], $k);
                }
                else {
                    $_upd[$k] = $v;
                }
            }
        }
    }

    // Check for new Value..
    if (isset($_post['ds_browser_new_key']{0})) {
        // Make sure it begins with our DS prefix
        $pfx = jrCore_db_get_prefix($_post['module']);
        if (strpos($_post['ds_browser_new_key'], $pfx) !== 0) {
            jrCore_set_form_notice('error', "Invalid new key name - must begin with <strong>{$pfx}_</strong>", false);
            jrCore_form_field_hilight('ds_browser_new_key');
            jrCore_form_result();
        }
        elseif (!jrCore_checktype($_post['ds_browser_new_key'], 'core_string')) {
            $err = jrCore_checktype_core_string(null, true);
            jrCore_set_form_notice('error', "Invalid new key name - must contain {$err} only");
            jrCore_form_field_hilight('ds_browser_new_key');
            jrCore_form_result();
        }
        // Make sure it is NOT a restricted key
        switch ($_post['ds_browser_new_key']) {
            case 'user_group':
            case 'user_password':
                jrCore_set_form_notice('error', "Invalid new key name - {$_post['ds_browser_new_key']} cannot be set using the Data Browser");
                jrCore_form_field_hilight('ds_browser_new_key');
                jrCore_form_result();
                break;
        }
        $_upd["{$_post['ds_browser_new_key']}"] = $_post['ds_browser_new_value'];
        $refresh = true;
    }

    if (isset($_upd) && count($_upd) > 0) {
        if (!jrCore_db_update_item($_post['module'], $_post['id'], $_upd)) {
            jrCore_set_form_notice('error', 'An error was encountered saving the updates to the item - please try again');
            jrCore_form_result();
        }
    }
    jrCore_set_form_notice('success', 'The changes were successfully saved');
    if ($refresh) {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$_post['id']}");
    }
    else {
        jrCore_form_result();
    }
}

//------------------------------
// browser_item_delete
//------------------------------
function view_jrCore_browser_item_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result('referrer');
    }
    if (!jrCore_db_delete_item($_post['module'], $_post['id'])) {
        jrCore_set_form_notice('error', 'Unable to delete item from DataStore - please try again');
    }
    jrCore_delete_all_cache_entries($_post['module']);
    jrCore_form_result('referrer');
}

//------------------------------
// stream_file
//------------------------------
function view_jrCore_stream_file($_post, $_user, $_conf)
{
    // When a stream request comes in, it will look like:
    // http://www.site.com/song/stream/audio_file/5
    // so we have URL / module / option / _1 / _2
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id provided');
    }
    // Make sure this is a DataStore module
    if (!jrCore_db_get_prefix($_post['module'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid module - no datastore');
    }
    $key_check = true;
    // ALLOW_ALL_DOMAINS - disables key checking
    if (isset($_conf['jrCore_allowed_domains']) && strpos(' ' . $_conf['jrCore_allowed_domains'], 'ALLOW_ALL_DOMAINS')) {
        $key_check = false;
    }
    elseif (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_conf['jrCore_base_url']) !== 0) {
        if (!jrCore_media_is_allowed_referrer_domain()) {
            header('HTTP/1.0 403 Forbidden');
            jrCore_notice('Error', 'Media streams are blocked outside of players');
        }
        $key_check = false;
    }
    // Make sure we have a valid play key
    if ($key_check && !jrUser_is_admin() && (!isset($_post['key']) || !jrCore_media_get_play_key($_post['key']))) {
        header('HTTP/1.0 403 Forbidden');
        jrCore_notice('Error', 'Invalid play key');
    }

    // DO NOT CHANGE THIS to jrCore_db_get_item!  This needs to be
    // a db_search_item call so that Parameter Injection works!
    $_rt = array(
        'search' => array(
            "_item_id = {$_post['_2']}"
        ),
        'quota_check' => false,
        'limit' => 1
    );
    $_rt = jrCore_db_search_items($_post['module'], $_rt);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id - no data found');
    }
    $_rt = $_rt['_items'][0];
    if (!isset($_rt["{$_post['_1']}_size"]) || $_rt["{$_post['_1']}_size"] < 1) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id - no media item found');
    }
    // Privacy Checking for this profile
    if (!jrUser_is_admin() && isset($_rt['profile_private']) && $_rt['profile_private'] != '1') {
        // Privacy Check (Sub Select) - non admin users
        // 0 = Private
        // 1 = Global
        // 2 = Shared
        if ($_rt['profile_private'] == '0') {
            if (!jrProfile_is_profile_owner($_rt['_profile_id'])) {
                // We have a private profile and this is not the owner
                header('HTTP/1.0 403 Forbidden');
                header('Connection: close');
                jrCore_notice('Error', 'you do not have permission to stream this file');
                exit;
            }
        }
        // We're shared - viewer must be a follower of the profile
        elseif (jrCore_module_is_active('jrFollower')) {
            if (jrFollower_is_follower($_user['_user_id'], $_rt['_profile_id']) === false) {
                // We are not a follower of this profile - not allowed
                header('HTTP/1.0 403 Forbidden');
                header('Connection: close');
                jrCore_notice('Error', 'you do not have permission to stream this file');
                exit;
            }
        }
        else {
            // Shared by followers not enabled
            header('HTTP/1.0 403 Forbidden');
            header('Connection: close');
            jrCore_notice('Error', 'you do not have permission to stream this file');
            exit;
        }
    }

    // Check that file exists
    $nam = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}." . $_rt["{$_post['_1']}_extension"];
    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id - no file found');
    }
    // See if we have a SAMPLE for streaming - always overrides full stream
    if (isset($_rt["{$_post['_1']}_item_price"]) && $_rt["{$_post['_1']}_item_price"] > 0 && jrCore_media_file_exists($_rt['_profile_id'], "{$nam}.sample." . $_rt["{$_post['_1']}_extension"])) {
        $nam = "{$nam}.sample." . $_rt["{$_post['_1']}_extension"];
    }

    // "stream_file" event trigger
    $_args = array(
        'module'      => $_post['module'],
        'stream_file' => $nam,
        'file_name'   => $_post['_1'],
        'item_id'     => $_post['_2']
    );
    $_rt = jrCore_trigger_event('jrCore', 'stream_file', $_rt, $_args);
    if (isset($_rt['stream_file']) && $_rt['stream_file'] != $nam) {
        $nam = $_rt['stream_file'];
    }

    // Watch for browser scans
    if (!isset($_SERVER['HTTP_RANGE']) || strpos($_SERVER['HTTP_RANGE'], 'bytes=0-') !== 0) {
        jrCore_counter($_post['module'], $_post['_2'], "{$_post['_1']}_stream");
    }

    $fname = $nam;
    if (isset($_rt["{$_post['_1']}_original_name"]) && strlen($_rt["{$_post['_1']}_original_name"]) > 0) {
        $fname = $_rt["{$_post['_1']}_original_name"];
    }
    elseif (isset($_rt["{$_post['_1']}_name"]) && strlen($_rt["{$_post['_1']}_name"]) > 0) {
        $fname = $_rt["{$_post['_1']}_name"];
    }

    // Stream the file to the client
    jrCore_media_file_stream($_rt['_profile_id'], $nam, $fname);
    session_write_close();
    exit;
}

//------------------------------
// download_file
//------------------------------
function view_jrCore_download_file($_post, $_user, $_conf)
{
    // When a download request comes in, it will look like:
    // http://www.site.com/song/download/audio_file/5
    // so we have URL / module / option / _1 / _2
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id provided');
    }
    // Make sure this is a DataStore module
    if (!jrCore_db_get_prefix($_post['module'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid module - no datastore');
    }
    // Make sure referrer is allowed if we get one
    if (!jrCore_media_is_allowed_referrer_domain()) {
        header('HTTP/1.0 403 Forbidden');
        jrCore_notice('Error', 'Offsite media downloads are blocked');
    }

    // DO NOT CHANGE THIS to jrCore_db_get_item!  This needs to be
    // a db_search_item call so that Parameter Injection works!
    $_rt = array(
        'search' => array(
            "_item_id = {$_post['_2']}"
        ),
        'quota_check' => false,
        'limit' => 1
    );
    $_rt = jrCore_db_search_items($_post['module'], $_rt);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id - no data found');
    }
    $_rt = $_rt['_items'][0];

    if (!isset($_rt["{$_post['_1']}_size"]) || $_rt["{$_post['_1']}_size"] < 1) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id - no media item found');
    }

    // Non admin checks
    if (!jrUser_can_edit_item($_rt)) {

        // Make sure file is NOT for sale
        if (isset($_rt["{$_post['_1']}_item_price"]) && $_rt["{$_post['_1']}_item_price"] > 0) {
            header('HTTP/1.0 403 Forbidden');
            jrCore_notice('Error', 'Invalid media item - item must be purchased to be downloaded');
        }

        // Privacy Checking for this profile
        if (isset($_rt['profile_private']) && $_rt['profile_private'] != '1') {
            // Privacy Check (Sub Select) - non admin users
            // 0 = Private
            // 1 = Global
            // 2 = Shared
            if ($_rt['profile_private'] == '0') {
                if (!jrProfile_is_profile_owner($_rt['_profile_id'])) {
                    // We have a private profile and this is not the owner
                    header('HTTP/1.0 403 Forbidden');
                    header('Connection: close');
                    jrCore_notice('Error', 'you do not have permission to download this file');
                    exit;
                }
            }

            // We're shared - viewer must be a follower of the profile
            elseif (jrCore_module_is_active('jrFollower')) {
                if (jrFollower_is_follower($_user['_user_id'], $_rt['_profile_id']) === false) {
                    // We are not a follower of this profile - not allowed
                    header('HTTP/1.0 403 Forbidden');
                    header('Connection: close');
                    jrCore_notice('Error', 'you do not have permission to download this file');
                    exit;
                }
            }
            else {
                // Shared by followers not enabled
                header('HTTP/1.0 403 Forbidden');
                header('Connection: close');
                jrCore_notice('Error', 'you do not have permission to download this file');
                exit;
            }
        }
    }

    // Check that file exists
    $nam = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}." . $_rt["{$_post['_1']}_extension"];
    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'Invalid media id - no file found');
    }

    // "download_file" event trigger
    $_args = array(
        'module'    => $_post['module'],
        'file_name' => $_post['_1'],
        'item_id'   => $_post['_2']
    );
    $_rt = jrCore_trigger_event('jrCore', 'download_file', $_rt, $_args);

    // Increment our counter
    jrCore_counter($_post['module'], $_post['_2'], "{$_post['_1']}_download");

    $fname = $nam;
    if (isset($_rt["{$_post['_1']}_original_name"]) && strlen($_rt["{$_post['_1']}_original_name"]) > 0) {
        $fname = $_rt["{$_post['_1']}_original_name"];
    }
    elseif (isset($_rt["{$_post['_1']}_name"]) && strlen($_rt["{$_post['_1']}_name"]) > 0) {
        $fname = $_rt["{$_post['_1']}_name"];
    }

    // Download the file to the client
    jrCore_media_file_download($_rt['_profile_id'], $nam, $fname);
    session_write_close();
    exit;
}

//------------------------------
// upload_file
//------------------------------
function view_jrCore_upload_file($_post, $_user, $_conf)
{
    // Upload progress
    jrUser_session_require_login();
    if (!isset($_post['upload_token']) || !jrCore_checktype($_post['upload_token'],'md5')) {
        exit;
    }

    // Bring in meter backend
    require_once APP_DIR . '/modules/jrCore/contrib/meter/server.php';

    // Determine max allowed upload size
    $max = (isset($_user['quota_jrCore_max_upload_size'])) ? intval($_user['quota_jrCore_max_upload_size']) : 2097152;
    if (!isset($max) || $max < 2097152) {
        $max = 2097152;
    }
    $ext = explode(',', $_post['extensions']);
    $mtr = new qqFileUploader($ext, jrCore_get_max_allowed_upload($max));
    $dir = jrCore_get_module_cache_dir('jrCore') . '/' . $_post['upload_token'];
    @mkdir($dir, $_conf['jrCore_dir_perms'], true);
    $res = $mtr->handleUpload($dir . '/');
    echo htmlspecialchars(json_encode($res), ENT_NOQUOTES);
    exit;
}

//--------------------------------
// PHP Error Log
//--------------------------------
function view_jrCore_php_error_log($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_master_log_tabs('error');

    $clear = null;
    $out = "<div id=\"error_log\" class=\"center\"><p>No PHP Errors at this time</p></div>";
    if (is_file(APP_DIR . "/data/logs/error_log")) {
        $_er = file(APP_DIR . "/data/logs/error_log");
        $_nm = array();
        $_ln = array();
        if (isset($_er) && is_array($_er)) {
            $cnt = count($_er);
            $idx = 0;
            while ($cnt > 0) {
                $index = md5(substr($_er[$idx], 27));
                if (!isset($_ln[$index])) {
                    $level = str_replace(':', '', jrCore_string_field($_er[$idx], 5));
                    $_ln[$index] = "<span class=\"php_{$level}\">" . jrCore_entity_string($_er[$idx]);
                    $_nm[$index] = 1;
                }
                else {
                    $_nm[$index]++;
                }
                unset($_er[$idx]);
                $cnt--;
                $idx++;
            }
            $out = '<div id="error_log"><br>';
            foreach ($_ln as $k => $v) {
                $out .= $v . ' [x ' . $_nm[$k] . ']</span><br><br>';
            }
            $out .= '</div>';
            $clear = jrCore_page_button('clear', 'Delete Error Log', "if(confirm('really delete the error log?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/php_error_log_delete')}");
        }
    }
    jrCore_page_banner('PHP Error Log', $clear);
    jrCore_page_custom($out);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// php_error_log_delete
//------------------------------
function view_jrCore_php_error_log_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    unlink(APP_DIR . "/data/logs/error_log");
    jrCore_location('referrer');
}

//--------------------------------
// Debug Log
//--------------------------------
function view_jrCore_debug_log($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_master_log_tabs('debug');

    $clear = null;
    $out = "<div id=\"debug_log\" class=\"center\"><p>No Debug Log entries at this time</p></div>";
    if (is_file(APP_DIR . "/data/logs/debug_log")) {
        $out = '<div id="debug_log">' . jrCore_entity_string(file_get_contents(APP_DIR . "/data/logs/debug_log")) . '</div>';
        $clear = jrCore_page_button('clear', 'Delete Debug Log', "if(confirm('really delete the debug log?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/debug_log_delete')}");
    }
    jrCore_page_banner('Debug Log', $clear);
    jrCore_page_custom($out);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// debug_log_delete
//------------------------------
function view_jrCore_debug_log_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    unlink(APP_DIR . "/data/logs/debug_log");
    jrCore_location('referrer');
}

//------------------------------
// pending_item_approve
//------------------------------
function view_jrCore_pending_item_approve($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || strlen($_post['id']) === 0) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_location('referrer');
    }

    // See if we are doing ONE or multiple
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $_todo = array($_post['id']);
        $title = 'item has';
    }
    else {
        $_todo = explode(',', $_post['id']);
        $title = 'items have';
    }

    $tbl = jrCore_db_table_name('jrCore', 'pending');
    foreach ($_todo as $pid) {
        if (!jrCore_checktype($pid, 'number_nz')) {
            continue;
        }
        $pid = (int) $pid;
        if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
            $req = "SELECT * FROM {$tbl} WHERE pending_module = '" . jrCore_db_escape($_post['_1']) . "' AND pending_item_id = '{$pid}' LIMIT 1";
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE pending_id = '{$pid}' LIMIT 1";
        }
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!isset($_rt) || !is_array($_rt)) {
            jrCore_set_form_notice('error', 'Invalid pending id');
            jrCore_location('referrer');
        }
        // approve this item and remove the pending
        $pfx = jrCore_db_get_prefix($_rt['pending_module']);
        $_dt = array( "{$pfx}_pending" => '0' );
        jrCore_db_update_item($_rt['pending_module'], $_rt['pending_item_id'], $_dt);

        // Trigger approve pending event
        $_args = array(
            '_item_id' => $_rt['pending_item_id'],
            'module'   => $_rt['pending_module']
        );
        jrCore_trigger_event('jrCore', 'approve_pending_item', $_dt, $_args);

        // Cleanup pending entry
        $req = "DELETE FROM {$tbl} WHERE pending_id = '{$_rt['pending_id']}' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!isset($cnt) || $cnt !== 1) {
            jrCore_set_form_notice('error', "unable to delete pending entry for {$_rt['pending_module']} item_id {$_rt['pending_item_id']}");
            jrCore_location('referrer');
        }

        // Next, let's see if there is an associated ACTION that was created for
        // this item - of so, we want to approve it as well.
        $req = "SELECT * FROM {$tbl} WHERE pending_linked_item_module = '" . jrCore_db_escape($_rt['pending_module']) . "' AND pending_linked_item_id = '" . intval($_rt['pending_item_id']) . "'";
        $_pa = jrCore_db_query($req, 'SINGLE');
        if (isset($_pa) && is_array($_pa)) {
            // We've found a linked action - approve
            $pfx = jrCore_db_get_prefix('jrAction');
            $_dt = array( "{$pfx}_pending" => '0' );
            jrCore_db_update_item('jrAction', $_pa['pending_item_id'], $_dt);
        }

        jrCore_logger('INF', "pending item id {$pfx}/{$_rt['pending_item_id']} has been approved");
    }
    jrCore_set_form_notice('success', "The pending {$title} been approved");
    jrCore_location('referrer');
}

//------------------------------
// pending_item_reject
//------------------------------
function view_jrCore_pending_item_reject($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_admin_only();

    if (!isset($_post['_1']) || !isset($_mods["{$_post['_1']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT * FROM {$tbl} WHERE pending_module = '" . jrCore_db_escape($_post['_1']) . "' AND pending_item_id = '" . intval($_post['id']) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid pending id');
        jrCore_location('referrer');
    }
    // Get item
    $_it = jrCore_db_get_item($_rt['pending_module'], $_rt['pending_item_id']);
    if (!isset($_it) || !is_array($_it)) {
        jrCore_set_form_notice('error', 'Invalid item - unable to retrieve data from DataStore');
        jrCore_form_result();
    }

    // Show our tabs if we are from the dashboard
    $url = jrCore_get_local_referrer();
    if (strpos($url, 'dashboard') || strpos($url, 'pending')) {
        jrCore_page_dashboard_tabs('pending');
    }

    // Show reject notice page
    jrCore_page_banner('reject item');
    $pfx = jrCore_db_get_prefix($_rt['pending_module']);
    $seo = '';
    if (isset($_it["{$pfx}_title_url"])) {
        $seo = '/' . $_it["{$pfx}_title_url"];
    }
    $url = jrCore_get_module_url($_rt['pending_module']);

    // Form init
    $_tmp = array(
        'submit_value' => 'sending rejection email',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Module
    $_tmp = array(
        'name'  => 'pending_id',
        'type'  => 'hidden',
        'value' => $_rt['pending_id']
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_link_cell('rejected item url', "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}{$seo}");

    // Create an item list of our custom "quick reject" options
    $lbl = 'reject reason';
    $tbl = jrCore_db_table_name('jrCore', 'pending_reason');
    $req = "SELECT * FROM {$tbl} ORDER BY reason_text ASC";
    $_pr = jrCore_db_query($req, 'reason_key', false, 'reason_text');
    if (isset($_pr) && is_array($_pr)) {
        // Add in our delete button
        $_att = array('class' => '');
        foreach ($_pr as $k => $v) {
            $_pr[$k] .= '&nbsp;&nbsp;' . jrCore_page_button("d{$k}", 'X', "if(confirm('delete this reason?')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/pending_reason_delete/key={$k}')}", $_att);
        }
        $_tmp = array(
            'name'     => 'reject_reason',
            'label'    => 'reject reasons',
            'sublabel' => 'select all that apply',
            'help'     => 'Select predefined reasons for rejecting this item',
            'type'     => 'optionlist',
            'validate' => 'hex',
            'options'  => $_pr,
            'required' => false
        );
        jrCore_form_field_create($_tmp);
        $lbl = 'new reject reason';
    }

    $_tmp = array(
        'name'     => 'new_reject_reason',
        'label'    => $lbl,
        'help'     => 'Enter a reject reason here and it will be saved after it is submitted',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'reject_message',
        'label'    => 'reject message',
        'sublabel' => '(optional)',
        'help'     => 'Enter a custom message to send to the profile owner(s) that explains why this item has been rejected',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// pending_item_reject_save
//------------------------------
function view_jrCore_pending_item_reject_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    if (!isset($_post['pending_id']) || !jrCore_checktype($_post['pending_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid pending_id');
        jrCore_form_result();
    }
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT * FROM {$tbl} WHERE pending_id = '{$_post['pending_id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid pending_id');
        jrCore_form_result();
    }
    // Get item
    $_it = jrCore_db_get_item($_rt['pending_module'], $_rt['pending_item_id']);
    if (!isset($_it) || !is_array($_it)) {
        jrCore_set_form_notice('error', 'Invalid item - unable to retrieve data from DataStore');
        jrCore_form_result();
    }

    // Save any new reject message
    $_rs = array();
    if (isset($_post['new_reject_reason']) && strlen($_post['new_reject_reason']) > 0) {
        $tb2 = jrCore_db_table_name('jrCore', 'pending_reason');
        $req = "INSERT INTO {$tb2} (reason_key,reason_text) VALUES ('" . md5($_post['new_reject_reason']) . "','" . jrCore_db_escape($_post['new_reject_reason']) . "')";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!isset($cnt) || $cnt !== 1) {
            jrCore_set_form_notice('error', 'Unable to store new pending reason - please try again');
            jrCore_form_result();
        }
        $_rs[] = strip_tags($_post['new_reject_reason']);
    }

    // [pending_id] => 17
    // [reject_reason_d86c579c827fec297d69e58e4c06cfa2] => on
    // [reject_reason_e37bbbb8065ecdc1d34cf3e98f37e8a3] => on
    // [new_reject_reason] => NEW REASON
    // [reject_message] => MESSAGE

    // Send Reject email
    if (isset($_it['user_email']) && jrCore_checktype($_it['user_email'], 'email')) {

        $tb2 = jrCore_db_table_name('jrCore', 'pending_reason');
        $req = "SELECT * FROM {$tb2}";
        $_pr = jrCore_db_query($req, 'reason_key', false, 'reason_text');

        // See if we received any canned rejection notices
        foreach ($_post as $k => $v) {
            if (strpos($k, 'reject_reason_') === 0 && $v == 'on') {
                $key = substr($k, 14);
                if (isset($_pr[$key])) {
                    $_rs[] = $_pr[$key];
                }
            }
        }
        $reason  = implode("\n", $_rs);

        // If this is a COMMENT or an ACTION, they are deleted (since they cannot be edited)
        switch ($_rt['pending_module']) {
            case 'jrComment':
            case 'jrAction':
                $email_template = 'pending_reject_deleted';
                break;
            default:
                $email_template = 'pending_reject';
                break;
        }
        $url = jrCore_get_module_url($_rt['pending_module']);
        $pfx = jrCore_db_get_prefix($_rt['pending_module']);
        $ttl = '';
        if (isset($_it["{$pfx}_title_url"])) {
            $ttl = "/". $_it["{$pfx}_title_url"];
        }
        $_rp = array(
            'system_name'    => $_conf['jrCore_system_name'],
            'reject_reason'  => $reason,
            'reject_message' => strip_tags($_post['reject_message']),
            'reject_url'     =>"{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}{$ttl}"
        );
        list($sub, $msg) = jrCore_parse_email_templates('jrCore', $email_template, $_rp);

        // Get all email addresses associated with this profile
        jrCore_send_email($_it['user_email'], $sub, $msg);
    }

    // Cleanup pending entry
    $req = "DELETE FROM {$tbl} WHERE pending_id = '{$_rt['pending_id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!isset($cnt) || $cnt !== 1) {
        jrCore_set_form_notice('error', "unable to delete pending entry for {$_rt['pending_module']} item_id {$_rt['pending_item_id']}");
        jrCore_form_result();
    }

    // Delete item if needed
    switch ($_rt['pending_module']) {
        case 'jrComment':
        case 'jrAction':
            jrCore_db_delete_item($_rt['pending_module'], $_rt['pending_item_id']);
            break;
    }

    // Refresh
    $url = jrCore_get_module_url($_rt['pending_module']);
    jrCore_logger('INF', "{$_it['profile_url']}/{$url}/{$_it['_item_id']} has been rejected");
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/pending");
}

//------------------------------
// pending_item_delete
//------------------------------
function view_jrCore_pending_item_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || strlen($_post['id']) === 0) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_location('referrer');
    }
    // See if we are doing ONE or multiple
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $_todo = array($_post['id']);
        $title = 'item has';
    }
    else {
        $_todo = explode(',', $_post['id']);
        $title = 'items have';
    }

    $tbl = jrCore_db_table_name('jrCore', 'pending');
    foreach ($_todo as $pid) {
        if (!jrCore_checktype($pid, 'number_nz')) {
            continue;
        }
        $pid = (int) $pid;
        if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
            $req = "SELECT * FROM {$tbl} WHERE pending_module = '" . jrCore_db_escape($_post['_1']) . "' AND pending_item_id = '{$pid}' LIMIT 1";
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE pending_id = '{$pid}' LIMIT 1";
        }
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!isset($_rt) || !is_array($_rt)) {
            jrCore_set_form_notice('error', 'Invalid pending id');
            jrCore_location('referrer');
        }

        // delete this item
        jrCore_db_delete_item($_rt['pending_module'], $_rt['pending_item_id']);

        // Cleanup pending entry
        $req = "DELETE FROM {$tbl} WHERE pending_id = '{$_rt['pending_id']}' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!isset($cnt) || $cnt !== 1) {
            jrCore_set_form_notice('error', "unable to delete pending entry for {$_rt['pending_module']} item_id {$_rt['pending_item_id']}");
            jrCore_location('referrer');
        }

        // Next, let's see if there is an associated ACTION that was created for
        // this item - of so, we want to remove it as well.
        $req = "SELECT * FROM {$tbl} WHERE pending_linked_item_module = '" . jrCore_db_escape($_rt['pending_module']) . "' AND pending_linked_item_id = '" . intval($_rt['pending_item_id']) . "'";
        $_pa = jrCore_db_query($req, 'SINGLE');
        if (isset($_pa) && is_array($_pa)) {

            // We've found a linked action - delete
            if (!jrCore_db_delete_item('jrAction', $_pa['pending_item_id'], false, true)) {
                jrCore_logger('CRI', "unable to delete _item_id {$_pa['pending_item_id']} in the jrAction datastore");
            }

            // And remove the pending entry
            $req = "DELETE FROM {$tbl} WHERE pending_id = '{$_pa['pending_id']}' LIMIT 1";
            $cnt = jrCore_db_query($req, 'COUNT');
            if (!isset($cnt) || $cnt !== 1) {
                jrCore_logger('CRI', "unable to delete pending entry for {$_rt['pending_module']} item_id {$_rt['pending_item_id']}");
            }
        }
        $pfx = jrCore_db_get_prefix($_rt['pending_module']);
        jrCore_logger('INF', "pending item id {$pfx}/{$_rt['pending_item_id']} has been deleted");
    }
    // See if we are deleting from a media item's page or the dashboard
    $url = jrCore_get_local_referrer();
    if (strpos($url, 'dashboard')) {
        jrCore_set_form_notice('success', "The pending {$title} been deleted");
    }
    // We're coming in from an individual item's page.
    jrCore_location('referrer');
}

//------------------------------
// pending_reason_delete
//------------------------------
function view_jrCore_pending_reason_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    if (!isset($_post['key']) || !jrCore_checktype($_post['key'], 'md5')) {
        jrCore_set_form_notice('error', 'Invalid pending reason key');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'pending_reason');
    $req = "DELETE FROM {$tbl} WHERE reason_key = '" . jrCore_db_escape($_post['key']) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!isset($cnt) || $cnt !== 1) {
        jrCore_set_form_notice('error', 'unable to delete pending reason from database - please try again');
    }
    jrCore_location('referrer');
}

/**
 * Set display order for items on a profile
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function view_jrCore_item_display_order($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    // Make sure the requested module has a registered DS
    $pfx = jrCore_db_get_prefix($_post['module']);
    if (!$pfx) {
        jrCore_notice_page('error','Invalid module - module does not use a DataStore');
        return false;
    }
    // Make sure this module has registered for item_order
    $_md = jrCore_get_registered_module_features('jrCore','item_order_support');
    if (!isset($_md["{$_post['module']}"])) {
        jrCore_notice_page('error','Invalid module - module is not registered for item_order support');
        return false;
    }
    // Get all items of this type
    $_sc = array(
        'search'        => array("_profile_id = {$_user['user_active_profile_id']}"),
        'order_by'      => array("{$pfx}_display_order" => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 500
    );
    $_rt = jrCore_db_search_items($_post['module'],$_sc);
    if (!isset($_rt['_items']) || !is_array($_rt['_items'])) {
        jrCore_notice_page('notice','There are no items to set the order for!');
        return false;
    }
    jrCore_page_banner('set item order');

    // Let modules inspect our display order items
    $_rt = jrCore_trigger_event('jrCore', 'display_order', $_rt);

    $tmp = '<ul class="item_sortable list">';
    foreach ($_rt['_items'] as $_item) {
        $tmp .= "<li data-id=\"{$_item['_item_id']}\">". $_item["{$pfx}_title"] ."</li>";
    }
    $tmp .= '</ul>';
    jrCore_page_custom($tmp,'set order','drag and drop entries to set order');

    $url = "{$_conf['jrCore_base_url']}/". jrCore_get_module_url('jrCore') ."/item_display_order_update/m={$_post['module']}/__ajax=1";
    $tmp = array('$(function() {
           $(\'.item_sortable\').sortable().bind(\'sortupdate\', function(event,ui) {
               var o = $(\'ul.item_sortable li\').map(function(){ return $(this).data("id"); }).get();
               $.post(\''. $url .'\', { iid: o });
           });
       });');
    jrCore_create_page_element('javascript_footer_function', $tmp);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}",'continue');
    return jrCore_page_display(true);
}

/**
 * Update item order in Datastore
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function view_jrCore_item_display_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        return jrCore_json_response(array('error','Invalid module'));
    }
    // Make sure the requested module has a registered DS
    $pfx = jrCore_db_get_prefix($_post['m']);
    if (!$pfx) {
        return jrCore_json_response(array('error','Invalid module - module does not use a DataStore'));
    }
    // Make sure this module has registered for item_order
    $_md = jrCore_get_registered_module_features('jrCore','item_order_support');
    if (!isset($_md["{$_post['m']}"])) {
        return jrCore_json_response(array('error','Invalid module - module is not registered for item_order support'));
    }

    // Get our items that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items($_post['m'],$_post['iid']);
        if (!isset($_rt) || !is_array($_rt)) {
            return jrCore_json_response(array('error','unable to retrieve item entries from DataStore'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error','permission denied'));
            }
        }
    }
    // Looks good - set item order
    $_ids = array();
    foreach ($_post['iid'] as $ord => $iid) {
        $_ids[$iid] = $ord;
    }
    jrCore_db_set_display_order($_post['m'], $_ids);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success','item order successfully updated'));
}

/**
 * Update Item Action buttons for an index|list|detail
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function view_jrCore_item_action_buttons($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_notice_page('error', 'Invalid button type');
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_notice_page('error', 'Invalid module');
    }
    $type = false;
    $key  = false;
    switch ($_post['_1']) {
        case 'index':
        case 'list':
        case 'detail':
            $type = $_post['_1'];
            $key  = "{$_post['m']}_item_{$type}_buttons";
            break;
        default:
            jrCore_notice_page('error', 'Invalid button type');
            break;
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner($_mods["{$_post['m']}"]['module_name'] ." - {$type} buttons");
    jrCore_get_form_notice();

    // Get all registered features
    $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
    if (!$_rf || !is_array($_rf)) {
        jrCore_notice_page('notice', 'There are no modules in the system that provide Item Action Buttons');
    }
    $_rs = array();
    foreach ($_rf as $bmod => $_ft) {
        foreach ($_ft as $func => $_inf) {
            $_inf['module']   = $bmod;
            $_inf['function'] = $func;
            $_rs[] = $_inf;
        }
    }

    // The admin can:
    // set a specific button to not show
    // set the ORDER the buttons appear in (left to right)
    // Our config holds the info, ordered and by function => on|off
    if (isset($_conf[$key]{1})) {
        // admin has configured
        $_ord = json_decode($_conf[$key], true);
        // "new" modules may not be present in the order until the admin actually
        // re-orders things, so let's add any extra in at the end.
        if (isset($_ord) && is_array($_ord)) {
            foreach ($_rs as $_dat) {
                $found = false;
                foreach ($_ord as $_inf) {
                    if ($_inf['function'] == $_dat['function']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $_ord[] = $_dat;
                }
            }
        }
        else {
            $_ord = $_rs;
        }
    }
    else {
        $_ord = $_rs;
    }

    // See if they are active for this view
    foreach ($_ord as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (!$func($_post['m'], false, false, false, true)) {
                    unset($_ord[$k]);
                }
            }
        }
    }
    $_ord = array_values($_ord);

    $dat = array();
    $dat[1]['title'] = 'order';
    $dat[1]['width'] = '3%';
    $dat[2]['title'] = 'icon';
    $dat[2]['width'] = '3%';
    $dat[3]['title'] = 'module';
    $dat[3]['width'] = '27%';
    $dat[4]['title'] = 'button name';
    $dat[4]['width'] = '27%';
    $dat[5]['title'] = 'group';
    $dat[5]['width'] = '10%';
    $dat[6]['title'] = 'quota(s)';
    $dat[6]['width'] = '20%';
    $dat[7]['title'] = 'active';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'modify';
    $dat[8]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (count($_ord) > 0) {
        foreach ($_ord as $cnt => $_inf) {
            if (!jrCore_module_is_active($_inf['module'])) {
                continue;
            }
            $dat = array();
            if (!isset($first)) {
                $dat[1]['title'] = '';
                $first = true;
            }
            else {
                $dat[1]['title'] = jrCore_page_button("f{$cnt}", '^', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_action_button_order/t={$type}/m={$_post['m']}/o={$cnt}')");
            }
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = (isset($_inf['icon'])) ? jrCore_get_icon_html($_inf['icon']) : '';
            $dat[3]['title'] = $_mods["{$_inf['module']}"]['module_name'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_inf['title'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = (isset($_inf['group'])) ? $_inf['group'] : '';
            $dat[5]['class'] = 'center';
            if (isset($_inf['quota']) && strlen(trim($_inf['quota'])) > 0) {
                $_q = array();
                if (!isset($_qt)) {
                    $_qt = jrProfile_get_quotas();
                }
                foreach (explode(',', $_inf['quota']) as $qid) {
                    if (jrCore_checktype($qid, 'number_nz')) {
                        $_q[] = $_qt[$qid];
                    }
                }
                $dat[6]['title'] = implode('<br>', $_q);
            }
            else {
                $dat[6]['title'] = '-';
            }
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = (isset($_inf['active']) && $_inf['active'] == 'off') ? '<strong>off</strong>' : 'on';
            $dat[7]['class'] = 'center';
            $dat[8]['title'] = jrCore_page_button("m{$cnt}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_action_button_modify/t={$type}/m={$_post['m']}/o={$cnt}')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }
    jrCore_page_cancel_button(jrCore_is_profile_referrer(), 'continue');
    jrCore_page_display();
}

//------------------------------
// item_action_button_modify
//------------------------------
function view_jrCore_item_action_button_modify($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['t']) || strlen($_post['t']) === 0) {
        jrCore_set_form_notice('error', 'Invalid button type');
        jrCore_location('referrer');
    }
    $type = '';
    switch ($_post['t']) {
        case 'index':
        case 'list':
        case 'detail':
            $type = $_post['t'];
            break;
        default:
            jrCore_set_form_notice('error', 'Invalid button type');
            jrCore_location('referrer');
            break;
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    $mod = $_post['m'];
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid button offset');
        jrCore_location('referrer');
    }
    $idx = (int) $_post['o'];

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner("modify item {$type} button");
    jrCore_get_form_notice();

    // See if we have customized this button
    $opt = "{$mod}_item_{$type}_buttons";
    if (isset($_conf[$opt])) {
        $_rs = json_decode($_conf[$opt], true);
        if (is_array($_rs)) {
            $_dn = array();
            foreach ($_rs as $_ab) {
                $_dn["{$_ab['function']}"] = 1;
            }
            // We need to go through and see if any new action buttons have been added
            $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
            if ($_rf && is_array($_rf)) {
                foreach ($_rf as $bmod => $_ft) {
                    foreach ($_ft as $func => $_inf) {
                        if (!isset($_dn[$func])) {
                            $_inf['module']   = $bmod;
                            $_inf['function'] = $func;
                            $_rs[] = $_inf;
                        }
                    }
                }
            }
        }
    }
    else {
        // Get our existing (default) order
        $_rs = array();
        $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
        if ($_rf && is_array($_rf)) {
            foreach ($_rf as $bmod => $_ft) {
                foreach ($_ft as $func => $_inf) {
                    $_inf['module']   = $bmod;
                    $_inf['function'] = $func;
                    $_rs[] = $_inf;
                }
            }
        }
        else {
            jrCore_set_form_notice('error', "no registered {$type} buttons found");
            jrCore_location('referrer');
        }
        if (!isset($_rs[$idx])) {
            jrCore_set_form_notice('error', "invalid button offset");
            jrCore_location('referrer');
        }
    }

    // See if they are active for this view
    foreach ($_rs as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (!$func($mod, false, false, false, true)) {
                    unset($_rs[$k]);
                }
            }
        }
    }
    $_rs = array_values($_rs);
    $_rt = $_rs[$idx];


    $dat = array();
    $dat[1]['title'] = 'icon';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'provider';
    $dat[2]['width'] = '49%';
    $dat[3]['title'] = 'button name';
    $dat[3]['width'] = '49%';
    jrCore_page_table_header($dat);

    $dat = array();
    $dat[1]['title'] = (isset($_rt['icon'])) ? jrCore_get_icon_html($_rt['icon']) : '';
    $dat[2]['title'] = $_mods["{$_rt['module']}"]['module_name'];
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $_rt['title'];
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => 'referrer',
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // module
    $_tmp = array(
        'name'     => 'm',
        'type'     => 'hidden',
        'value'    => $mod
    );
    jrCore_form_field_create($_tmp);

    // type
    $_tmp = array(
        'name'     => 't',
        'type'     => 'hidden',
        'value'    => $type
    );
    jrCore_form_field_create($_tmp);

    // offset
    $_tmp = array(
        'name'     => 'o',
        'type'     => 'hidden',
        'value'    => $idx
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'      => 'active',
        'label'     => 'active',
        'help'      => 'Uncheck this option to disable this button from showing up in this location',
        'type'      => 'checkbox',
        'default'   => 'on',
        'validate'  => 'onoff',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(
        '0'       => '(no group restrictions)',
        'owner'   => 'profile owners',
        'master'  => 'master admins',
        'admin'   => 'profile admins',
        'power'   => 'power users',
        'multi'   => 'multi profile users',
        'user'    => 'logged in users',
        'visitor' => 'logged out users'
    );
    $_tmp = array(
        'name'      => 'group',
        'label'     => 'group',
        'sublabel'  => '(required)',
        'help'      => 'Select the group you would like this button to visible to',
        'type'      => 'select',
        'options'   => $_opt,
        'validate'  => 'core_string',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'      => 'quota',
        'label'     => 'quotas',
        'sublabel'  => '(optional)',
        'help'      => 'Select the group you would like this button to visible to',
        'type'      => 'optionlist',
        'options'   => 'jrProfile_get_quotas',
        'default'   => '',
        'validate'  => 'number_nz',
        'required'  => false
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// item_action_button_order
//------------------------------
function view_jrCore_item_action_button_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['t']) || strlen($_post['t']) === 0) {
        jrCore_set_form_notice('error', 'Invalid button type');
        jrCore_location('referrer');
    }
    $type = '';
    switch ($_post['t']) {
        case 'index':
        case 'list':
        case 'detail':
            $type = $_post['t'];
            break;
        default:
            jrCore_set_form_notice('error', 'Invalid button type');
            jrCore_location('referrer');
            break;
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    $mod = $_post['m'];
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid button offset');
        jrCore_location('referrer');
    }
    $idx = (int) $_post['o'];

    // See if we have customized this button
    $_rs = false;
    $_rt = false;
    $opt = "{$mod}_item_{$type}_buttons";
    if (isset($_conf[$opt])) {
        $_rs = json_decode($_conf[$opt], true);
        if (isset($_rs[$idx])) {
            $_rt = $_rs[$idx];
        }
    }
    else {
        // We've never set order for this module - create conf entry
        $_tmp = array(
            'name'     => "item_{$type}_buttons",
            'default'  => '',
            'type'     => 'hidden',
            'required' => 'off',
            'validate' => 'not_empty',
            'label'    => "item {$type} buttons",
            'help'     => "this hidden field keeps track of the item {$type} button information for {$_post['m']}/{$_post['t']} - do not modify"
        );
        jrCore_register_setting($mod, $_tmp);
    }

    // See if we fall through
    if (!$_rt) {
        // Get our existing (default) order
        $_rs = array();
        $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
        if ($_rf && is_array($_rf)) {
            foreach ($_rf as $bmod => $_ft) {
                foreach ($_ft as $func => $_inf) {
                    $_inf['module']   = $bmod;
                    $_inf['function'] = $func;
                    $_rs[] = $_inf;
                }
            }
        }
        else {
            jrCore_set_form_notice('error', "no registered {$type} buttons found");
            jrCore_location('referrer');
        }
        if (!isset($_rs[$idx])) {
            jrCore_set_form_notice('error', "invalid button offset");
            jrCore_location('referrer');
        }
    }

    // See if they are active for this view
    foreach ($_rs as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (!$func($mod, false, false, false, true)) {
                    unset($_rs[$k]);
                }
            }
        }
    }
    $_rs = array_values($_rs);

    // Update with new settings
    $_rs[$idx]['active'] = $_post['active'];
    $_rs[$idx]['quota']  = trim($_post['quota']);
    if ($_post['group'] != '0') {
        $_rs[$idx]['group']  = trim($_post['group']);
    }
    else {
        unset($_rs[$idx]['group']);
    }


    jrCore_form_delete_session();
    jrCore_set_setting_value($mod, "item_{$type}_buttons", json_encode($_rs));

    // Reset caches
    jrCore_delete_config_cache();
    jrCore_delete_all_cache_entries($mod);

    if (jrCore_checktype($_conf['jrCore_default_cache_seconds'], 'number_nz')) {
        jrCore_set_form_notice('success', 'The button settings have been updated.<br>Make sure and <a href="'. $_conf['jrCore_base_url'] .'/'. $_post['module_url'] .'/cache_reset"><u>Reset Caches</u></a> when complete for your changes to take effect',false);
    }
    else {
        jrCore_set_form_notice('success', 'The button settings have been updated');
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_action_buttons/{$type}/m={$mod}");
}

//------------------------------
// item_action_button_order
//------------------------------
function view_jrCore_item_action_button_order($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['t']) || strlen($_post['t']) === 0) {
        jrCore_notice_page('error', 'Invalid button type');
    }
    $type = '';
    switch ($_post['t']) {
        case 'index':
        case 'list':
        case 'detail':
            $type = $_post['t'];
            break;
        default:
            jrCore_set_form_notice('error', 'invalid button type');
            jrCore_location('referrer');
            break;
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid order');
        jrCore_location('referrer');
    }

    // Get our existing (default) order
    $_rs = array();
    $_ex = array();
    $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
    if ($_rf && is_array($_rf)) {
        foreach ($_rf as $mod => $_ft) {
            foreach ($_ft as $func => $_inf) {
                $_inf['module']   = $mod;
                $_inf['function'] = $func;
                $_rs[] = $_inf;
                $_ex[$func] = $_inf;
            }
        }
    }

    // Every module has it's own custom setting to store the order for
    $opt = "{$_post['m']}_item_{$type}_buttons";
    if (!isset($_conf[$opt])) {

        // We've never set order for this module - create conf entry
        $_tmp = array(
            'name'     => "item_{$type}_buttons",
            'default'  => '',
            'type'     => 'hidden',
            'required' => 'off',
            'validate' => 'not_empty',
            'label'    => "item {$type} buttons",
            'help'     => "this hidden field keeps track of the item {$type} button information for {$_post['m']}/{$_post['t']} - do not modify"
        );
        jrCore_register_setting($_post['m'], $_tmp);

    }
    else {
        // Get our existing order - we need to swap the one we got with the one above it
        $_rs = json_decode($_conf[$opt], true);
        if (!isset($_rs)) {
            $_rs = array();
        }
        // See if we have any new modules that were not part of our save - they go at the bottom
        foreach ($_ex as $func => $_dat) {
            $found = false;
            foreach ($_rs as $_inf) {
                if ($_inf['function'] == $func) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_rs[] = $_dat;
            }
        }
    }

    // See if they are active for this view
    foreach ($_rs as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (!$func($_post['m'], false, false, false, true)) {
                    unset($_rs[$k]);
                }
            }
        }
    }
    $_rs = array_values($_rs);

    $idx = (int) $_post['o'];
    foreach ($_rs as $k => $v) {
        $pre = intval($idx - 1);
        if ($k === $pre) {
            $_tm = $_rs[$idx];
            $_rs[$idx] = $v;
            $_rs[$pre] = $_tm;
            unset($_tm);
            break;
        }
    }
    jrCore_set_setting_value($_post['m'], "item_{$type}_buttons", json_encode($_rs));

    // Reset caches
    jrCore_delete_config_cache();
    jrCore_delete_all_cache_entries($_post['m']);

    if (jrCore_checktype($_conf['jrCore_default_cache_seconds'], 'number_nz')) {
        jrCore_set_form_notice('success', 'The button order has been updated.<br>Make sure and <a href="'. $_conf['jrCore_base_url'] .'/'. $_post['module_url'] .'/cache_reset"><u>Reset Caches</u></a> when complete for your changes to take effect',false);
    }
    else {
        jrCore_set_form_notice('success', 'The button order has been updated');
    }
    jrCore_location('referrer');
}

//------------------------------
// template_compare (magic)
//------------------------------
function view_jrCore_template_compare($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    if (isset($_post['skin'])) {
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$_post['skin']}";
        $t_type     = 'skin';
    }
    else {
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates";
        $t_type     = 'module';
    }

    // Get info about this template...
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        // Database template
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['id']}'";
        $_tp = jrCore_db_query($req, 'SINGLE');
        if (!isset($_tp) || !is_array($_tp)) {
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_location($cancel_url);
        }
        $tp1 = trim($_tp['template_body']);
        $mod = $_tp['template_module'];
        $nam = $_tp['template_name'];
        $tag = 'custom';
    }
    elseif ($t_type == 'skin') {
        // Skin template
        if (!is_file(APP_DIR . "/skins/{$_post['skin']}/{$_post['id']}")) {
            jrCore_set_form_notice('error', 'skin template file not found - please try again');
            jrCore_location($cancel_url);
        }
        $tp1 = trim(file_get_contents(APP_DIR . "/skins/{$_post['skin']}/{$_post['id']}"));
        $mod = $_post['skin'];
        $nam = $_post['id'];
    }
    else {
        // Module template
        if (!is_file(APP_DIR . "/modules/{$_post['module']}/templates/{$_post['id']}")) {
            jrCore_set_form_notice('error', 'module template file not found - please try again');
            jrCore_location($cancel_url);
        }
        $tp1 = trim(file_get_contents(APP_DIR . "/modules/{$_post['module']}/templates/{$_post['id']}"));
        $mod = $_post['module'];
        $nam = $_post['id'];
    }
    $omod = $mod;

    // Handle our incoming version, which can be in the following format
    // ModDir:Template:Version
    if (isset($_post['version'])) {
        $_post['version'] = jrCore_url_decode_string($_post['version']);
        list($dir, $tpl, $ver) = explode('/', $_post['version']);
        $_post['version'] = $ver;
        $mod              = $dir;
        $nam              = $tpl;
    }

    // Okay - we know this user has customized this template, so we compare to the
    // version located on the file system

    if ($t_type == 'skin') {
        if (isset($_post['version']) && strlen($_post['version']) > 0 && strlen($_post['version']) < 8) {
            $tpl_file = APP_DIR . "/skins/{$mod}-release-{$_post['version']}/{$nam}";
            if (!is_file($tpl_file)) {
                $tpl_file = APP_DIR . "/skins/{$mod}/{$nam}";
            }
        }
        else {
            $tpl_file = APP_DIR . "/skins/{$mod}/{$nam}";
        }
        $_v1    = glob(APP_DIR . "/skins/*-release-*/{$nam}");
        $_v2    = glob(APP_DIR . "/skins/*/{$nam}");
        $_vers  = array_merge($_v1, $_v2);
        $_vers  = array_unique($_vers);
        $_meta  = jrCore_skin_meta_data($mod);
        $active = $_meta['version'];
    }
    else {
        if (isset($_post['version']) && strlen($_post['version']) > 0 && strlen($_post['version']) < 8) {
            $tpl_file = APP_DIR . "/modules/{$mod}-release-{$_post['version']}/templates/{$nam}";
            if (!is_file($tpl_file)) {
                $tpl_file = APP_DIR . "/modules/{$mod}/templates/{$nam}";
            }
        }
        else {
            $tpl_file = APP_DIR . "/modules/{$mod}/templates/{$nam}";
        }
        $_vers  = glob(APP_DIR . "/modules/{$mod}-release*");
        $active = $_mods[$mod]['module_version'];
    }
    if (!isset($tag)) {
        $tag = $active;
    }

    $tp2 = false;
    if (isset($_post['version']) && strpos(' ' . $_post['version'], 'custom-')) {
        list($ver, $tid) = explode('-', $_post['version'], 2);
        $_post['version'] = $ver;
        // get the db version
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$tid}'";
        $_tp = jrCore_db_query($req, 'SINGLE');
        if (isset($_tp) && is_array($_tp)) {
            $tp2 = trim($_tp['template_body']);
        }
    }
    elseif (is_file($tpl_file)) {
        $tp2 = trim(file_get_contents($tpl_file));
    }
    if (!$tp2) {
        jrCore_set_form_notice('error', "{$t_type} template file not found - please try again");
        jrCore_location($cancel_url);
    }

    // Create jumper of previous versions if they exist
    if (is_array($_vers) && count($_vers) > 0) {
        if (isset($_post['version'])) {
            $sel = $_post['version'];
        }
        else {
            $sel = $active;
        }

        $selected = false;
        foreach ($_vers as $full_file) {
            // If this is a SKIN file, we are going to allow them to compare the file
            // to OTHER skins as will (since they may be using a custom skin based off an existing skin)
            $fln = basename($full_file);
            if ($t_type == 'skin') {
                $skn = explode('-', basename(dirname($full_file)));
                $ver = end($skn);
                $skn = reset($skn);
                $fnm = "{$skn}/{$nam}";
                $ttl = ($ver === $skn) ? 'default' : $ver;
            }
            else {
                $ver = explode('-', $fln);
                $ver = end($ver);
                $fnm = "{$mod}/{$nam}";
                $ttl = ($ver === $mod) ? 'default' : $ver;
            }
            $v = jrCore_url_encode_string("{$fnm}/{$ver}");
            if ($ver == $sel || (!isset($_post['version']) && $_post['skin'] == $ver)) {
                $selected = $v;
            }
            $_option[$v] = "{$fnm} - {$ttl}";
        }
        // add in any custom templates of the same name
        $skn = jrCore_get_skins();
        $skn = "'" . implode("','", array_keys($skn)) . "'";
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_name = '{$nam}' AND `template_module` IN ({$skn})";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if (isset($_rt) && is_array($_rt)) {
            foreach ($_rt as $_row) {
                $v = jrCore_url_encode_string("{$_row['template_module']}/{$_row['template_name']}/custom-{$_row['template_id']}");
                if ($_post['version'] == 'custom' && isset($tid) && $tid == $_row['template_id']) {
                    $selected = $v;
                }
                $_option[$v] = "{$_row['template_module']}/{$_row['template_name']} - custom";
            }
        }
        // ordering
        $sub = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$_post['module_url']}/template_compare/{$t_type}={$omod}/id={$_post['id']}/version='+ $(this).val())\">\n";
        natcasesort($_option);
        foreach ($_option as $v => $t) {
            if ($selected == $v) {
                $sub .= '<option value="' . $v . '" selected="selected"> ' . $t . "</option>\n";
            }
            else {
                $sub .= '<option value="' . $v . '"> ' . $t . "</option>\n";
            }
        }
        $sub .= '</select>';
    }
    else {
        $sub = $nam;
        $sel = $active;
    }

    jrCore_page_banner("template compare", $sub);
    jrCore_get_form_notice();

    if (isset($dir)) {
        $ttl = "{$dir}/{$nam}";
    }
    elseif (!isset($_post['version'])) {
        $ttl = "{$omod}/{$nam}";
    }
    else {
        $ttl = $nam;
    }

    // Setup Code Mirror
    jrCore_create_page_element('css_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.css"));
    jrCore_create_page_element('css_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/addon/merge/merge.css"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.js"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/addon/diff_match_patch.js"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/addon/merge/merge.js"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/htmlmixed/htmlmixed.js"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/xml/xml.js"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js"));

    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => $cancel_url,
        'onclick'          => 'update_textarea();',
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $html = "<div><table><tr><th class=\"diff_head\">{$omod}/{$nam} ({$tag})</th><th class=\"diff_head\">{$ttl} ({$sel})</th></tr>";
    $html .= "<tr><td colspan=\"2\">";

    $_rep = array(
        'code_left'    => json_encode($tp1),
        'code_right'   => json_encode($tp2),
        'succcess_url' => $cancel_url
    );
    if (isset($_post['skin'])) {
        $_rep['skin'] = $_post['skin'];
    }
    else {
        $_rep['module_url'] = $_post['module_url'];
    }

    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $_rep['template_id'] = $_post['id'];
    }
    else {
        $_rep['template_name'] = $_post['id'];
    }

    $html .= jrCore_parse_template('compare.tpl', $_rep, 'jrCore');
    $html .= "</td></tr></table></div>";
    jrCore_page_custom($html);

    jrCore_page_display();
}

//----------------------------------------------
// template_compare_save (magic)
//-----------------------------------------------
function view_jrCore_template_compare_save($_post, $_user, $_conf)
{
    jrUser_master_only();

    // See if we are doing a skin or module
    $mod = (isset($_post['skin'])) ? $_post['skin'] : $_post['module'];

    // We need to test this template and make sure it does not cause any Smarty errors
    $cdr = jrCore_get_module_cache_dir('jrCore');
    $nam = time() . ".tpl";
    jrCore_write_to_file("{$cdr}/{$nam}", $_post['template_body']);
    $out = jrCore_load_url("{$_conf['jrCore_base_url']}/{$_post['module_url']}/test_template/{$nam}");
    if (isset($out) && strlen($out) > 1 && (strpos($out, 'error:') === 0 || stristr($out, 'fatal error'))) {
        $_SESSION['template_body_save'] = $_post['template_body'];
        unlink("{$cdr}/{$nam}");
        jrCore_set_form_notice('error', 'There is a syntax error in your template - please fix and try again');
        jrCore_form_result();
    }
    unlink("{$cdr}/{$nam}");

    $tbl = jrCore_db_table_name('jrCore', 'template');
    // See if we are updating a DB template or first time file
    if (isset($_post['template_id']) && jrCore_checktype($_post['template_id'], 'number_nz')) {
        // Make sure we have a valid template
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['template_id']}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!isset($_rt) || !is_array($_rt)) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_form_result();
        }
        $req = "UPDATE {$tbl} SET
                  template_updated = UNIX_TIMESTAMP(),
                  template_user    = '" . jrCore_db_escape($_user['user_name']) . "',
                  template_body    = '" . jrCore_db_escape($_post['template_body']) . "'
                 WHERE template_id = '{$_post['template_id']}'";
        $cnt = jrCore_db_query($req, 'COUNT');
        // Reset the template cache
        jrCore_get_template_file($_rt['template_name'], $mod, 'reset');
        $hl = $_rt['template_name'];
    }
    else {
        if (!isset($_post['template_name']{1})) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_name - please try again');
            jrCore_form_result();
        }
        $hl = $_post['template_name'];
        // See if we already exist - this can happen when the user FIRST modifies the template
        // and does not leave the screen, and modifies again
        $nam = jrCore_db_escape($_post['template_name']);
        $mod = jrCore_db_escape($mod);
        $req = "INSERT INTO {$tbl} (template_created,template_updated,template_user,template_active,template_name,template_module,template_body)
                VALUES(UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'" . jrCore_db_escape($_user['user_name']) . "','0','{$nam}','{$mod}','" . jrCore_db_escape($_post['template_body']) . "')";
        $tid = jrCore_db_query($req, 'INSERT_ID');
        if (isset($tid) && jrCore_checktype($tid, 'number_nz')) {
            $cnt = 1;
            // Reset the template cache
            jrCore_get_template_file($_post['template_name'], $mod, 'reset');
        }
    }
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The template has been successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the template update - please try again');
    }
    jrCore_form_delete_session();
    if (isset($_post['skin'])) {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$_post['skin']}/hl={$hl}"); // skin
    }
    else {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates/hl={$hl}"); // module
    }

}
