<?php
/**
 * Jamroom 5 Marketplace module
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
// config_check
//------------------------------
function view_jrMarket_config_check($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_mkt = jrMarket_get_active_release_system();
    // Must have a valid system email address
    if (!isset($_mkt['system_email']) || !jrCore_checktype($_mkt['system_email'], 'email') || !isset($_mkt['system_code']) || !jrCore_checktype($_mkt['system_code'], 'md5')) {
        jrCore_set_form_notice('error', 'A valid Marketplace Email and System ID is required to install Marketplace Items', false);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/release_system_update/id={$_mkt['system_id']}");
    }
    jrCore_location('referrer');
}

//------------------------------
// payment_methods
//------------------------------
function view_jrMarket_payment_methods($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'browse');
    jrCore_page_banner('Payment Methods');
    jrCore_get_form_notice();

    jrMarket_browse_tabs('methods');
    $_inf = jrMarket_get_market_info();

    $note = '<div id="secure_notice" style="padding:0 18px"><div class="item success rounded"><b>' . $_inf['system_name'] . '</b> uses <a href="https://stripe.com/" target="_blank"><b>Stripe</b></a> for all direct Marketplace purchases, which accepts all major credit cards.<br><br>If you would prefer to use an alternate payment provider you can do so by purchasing the module, skin or bundle directly by <b>clicking the &quot;more info...&quot; link for any item</b>.  You will taken directly to the item\'s detail page, where you can add it to your cart.  Upon checking out you will be able to select an alternative payment provider from the following:<br><ul>';
    if (isset($_inf['providers']) && is_array($_inf['providers'])) {
        foreach ($_inf['providers'] as $provider) {
            $note .= "<li>" . strip_tags($provider) . "</li>";
        }
    }
    $note .= '</ul>If you would like to use a Payment Provider not listed here, or have any questions, please don\'t hesitate to contact us.<br><br>Thanks!</div></div>';
    jrCore_page_custom($note);
    jrCore_page_display();
}

//------------------------------
// promo
//------------------------------
function view_jrMarket_promo($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'browse');
    jrCore_page_banner('Enter Promo Code', jrMarket_system_jumper());
    jrCore_get_form_notice();

    jrMarket_browse_tabs('promo');

    jrCore_page_note('If you have received a Promo Code, enter it here.<br>Promo Codes can be used on individual Module and Skin purchases.', false);

    // Form init
    $_tmp = array(
        'submit_value' => 'redeem promo code',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse"
    );
    jrCore_form_create($_tmp);

    // Promo Code
    $_tmp = array(
        'name'     => 'promo_code',
        'label'    => 'promo code',
        'help'     => 'Enter the promo code exactly as it appears in your email (or where you received the promo code from).<br><br><b>NOTE:</b> Promo Codes are only applied to modules and skins - bundles are already discounted so cannot be purchased with a promo code.',
        'type'     => 'text',
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// promo_save
//------------------------------
function view_jrMarket_promo_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure we have a good release system
    if (!$_mkt = jrMarket_get_active_release_system()) {
        jrCore_set_form_notice('error', 'There are no active Marketplace Systems configured where you can download items or get system updates!<br>Enter a new Marketplace System below to enable the Marketplace.', false);
        jrCore_form_result();
    }

    // Redeem code
    $_si = array(
        "sysid" => (isset($_mkt['system_code']) && jrCore_checktype($_mkt['system_code'], 'md5')) ? $_mkt['system_code'] : '',
        'host'  => jrCore_url_encode_string($_conf['jrCore_base_url']),
        'code'  => $_post['promo_code']
    );
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/redeem", $_si, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (isset($_rs) && strpos($_rs, '{') === 0) {
        $_rs = json_decode($_rs, true);
        if (isset($_rs['error'])) {
            jrCore_set_form_notice('error', $_rs['error']);
            jrCore_form_result();
        }
    }
    // Fall through - we have redeemed.
    // If this was a promo for a specific item, redirect to it now
    jrCore_form_delete_session();
    jrCore_delete_all_cache_entries('jrMarket');
    jrCore_set_form_notice('success', $_rs['success']);
    if (isset($_rs['market_name'])) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/{$_rs['market_type']}/sn={$_rs['market_name']}");
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// history
//------------------------------
function view_jrMarket_history($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'tools');
    jrCore_page_banner('Install History');
    jrCore_get_form_notice();

    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }

    $tbl = jrCore_db_table_name('jrMarket', 'install');
    $req = "SELECT * FROM {$tbl} ORDER BY install_time DESC";
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    $dat             = array();
    $dat[1]['title'] = 'date';
    $dat[1]['width'] = '20%';
    $dat[2]['title'] = 'installed items';
    $dat[2]['width'] = '75%';
    $dat[3]['title'] = 'details';
    $dat[3]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $k => $_itm) {
            $dat             = array();
            $dat[1]['title'] = jrCore_format_time($_itm['install_time']);
            $dat[1]['class'] = 'center';
            $_it             = json_decode($_itm['install_data'], true);
            $itm             = array();
            if (isset($_it['modules']) && count($_it['modules']) > 0) {
                $itm[] = "<span style=\"display:inline-block;width:60px;text-align:right\"><b>Modules:</b></span> " . implode(', ', array_keys($_it['modules']));
            }
            if (isset($_it['skins']) && count($_it['skins']) > 0) {
                $itm[] = "<span style=\"display:inline-block;width:60px;text-align:right\"><b>Skins:</b></span> " . implode(', ', array_keys($_it['skins']));
            }
            $dat[2]['title'] = implode('<br>', $itm);
            $dat[3]['title'] = jrCore_page_button("v{$k}", "details", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/install_result/{$_itm['install_id']}')");
            $dat[3]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There is no installation history to show</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_pager($_rt);
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// browse
//------------------------------
function view_jrMarket_browse($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'browse');
    $_skins = jrCore_get_skins();

    // Make sure we have a good release system
    if (!$_mkt = jrMarket_get_active_release_system()) {
        jrCore_set_form_notice('error', 'There are no active Marketplace Systems configured where you can download items or get system updates!<br>Enter a new Marketplace System below to enable the Marketplace.', false);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/systems");
    }

    // delete any old update tmp info
    jrCore_delete_temp_value('jrMarket', 'jrupdate_results');

    // Make sure we are subscribed to at least 1 channel
    $tbl = jrCore_db_table_name('jrMarket', 'channel');
    $req = "SELECT * FROM {$tbl} WHERE channel_system_id = '{$_mkt['system_id']}' AND channel_active = '1'";
    $_rt = jrCore_db_query($req, 'channel_id');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'You have not subscribed to any Release Channels - no updates or installs can be provided.<br><br>You can activate a Release Channel from the <a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/release_channels"><b><u>Release Channels</u></b></a> tool.', false);
        jrCore_page_banner('Marketplace');
        jrCore_get_form_notice();
        jrCore_page_display();
        exit;
    }
    $sec = jrMarket_system_jumper() . '&nbsp' . '<img src="' . $_conf['jrCore_base_url'] . '/image/img/module/jrMarket/secure.png" alt="Your purchase is secured via SSL - click for info" title="Your purchase is secured via SSL - click for info" width="32" height="32" style="cursor:pointer" onclick="$(\'#secure_notice\').slideToggle(300);">';

    // Send out our call to get modules and skins
    // The TYPE we are browsing will come in as _1
    // i.e. market/browse/modules
    if (!isset($_post['_1'])) {
        $_post['_1'] = 'module';
    }
    switch ($_post['_1']) {
        case 'skin':
        case 'module':
        case 'bundle':
        case 'installed':
            break;
        default:
            $_post['_1'] = 'module';
            break;
    }

    // Subscribed Channels
    $_ch = array();
    foreach ($_rt as $_chan) {
        switch ($_chan['channel_name']) {
            case 'stable':
            case 'beta':
                $_ch[] = "c[]={$_chan['channel_name']}";
                break;
            default:
                if (isset($_chan['channel_code']{1})) {
                    $_ch[] = "c[]={$_chan['channel_code']}";
                }
                break;
        }
    }

    // System Info
    $_si      = jrMarket_get_active_system_info();
    $_si['p'] = 1;

    // Page number
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz') && $_post['p'] > 1) {
        $_si['p'] = (int) $_post['p'];
    }
    // Search string
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_si['ss'] = jrCore_url_encode_string($_post['search_string']);
    }
    // Search Name
    if (isset($_post['sn']) && strlen($_post['sn']) > 0) {
        $_si['sn'] = $_post['sn'];
    }
    // Installed
    $_ins = array_keys($_mods);
    // See if we are searching for a specific module directory name
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0 && isset($_mods["{$_post['search_string']}"])) {
        foreach ($_ins as $k => $v) {
            if ($v == $_post['search_string']) {
                unset($_ins[$k]);
            }
        }
    }
    foreach ($_skins as $skin) {
        $_ins[] = $skin;
    }
    $_si['installed'] = jrCore_url_encode_string(implode(',', $_ins));

    // Get marketplace items
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/browse?type={$_post['_1']}&" . implode('&', $_ch), $_si, 'POST', jrMarket_get_port(), null, null, true, 12);
    if (isset($_rs) && strpos($_rs, '{') === 0) {
        $_rs = json_decode($_rs, true);
        if (isset($_rs['error'])) {
            jrCore_set_form_notice('error', $_rs['error'], false);
        }
    }
    else {
        jrCore_page_banner('Marketplace', $sec);
        if (strpos($_rs, 'system maintenance') || strpos($_rs, 'jrLaunch')) {
            jrCore_set_form_notice('notice', 'The Active Marketplace is currently down for maintenance - please try again shortly');
        }
        else {
            jrCore_set_form_notice('error', 'An error was encountered trying to communicate with the Active Marketplace<br>make sure the Marketplace URL is set correctly in Tools -> Marketplace Systems.', false);
        }
        jrCore_get_form_notice();
        jrCore_page_display();
        exit;
    }

    $_ds = jrCore_get_disk_usage();
    if ($_ds['percent_used'] > 90 && $_ds['disk_free'] < 100000000) {
        jrCore_set_form_notice('warning', "Your site is low on disk space (currently using {$_ds['percent_used']}%)<br>Try to free up disk space before installing or updating modules or skins.", false);
    }

    if (isset($_rs['_items']) && is_array($_rs['_items'])) {
        foreach ($_rs['_items'] as $k => $_v) {
            if (isset($_mods["{$_v['market_name']}"]) || isset($_skins["{$_v['market_name']}"])) {
                $_rs['_items'][$k]['market_already_installed'] = 1;
            }
            else {
                $_rs['_items'][$k]['market_already_installed'] = 0;
            }
        }
    }

    $active = 'module';
    switch ($_post['_1']) {
        case 'module':
        case 'skin':
        case 'bundle':
        case 'installed':
            $active = $_post['_1'];
            break;
    }
    jrCore_page_banner('Marketplace', $sec);
    jrCore_get_form_notice();

    // Our security note
    $_inf = jrMarket_get_market_info();
    $note = '<div id="secure_notice" style="padding:0 18px;display:none"><div class="item success rounded">Your Purchase is <b>100% secure</b>.  ' . $_inf['system_name'] . ' uses <a href="https://stripe.com/" target="_blank"><b>Stripe</b></a> for all Marketplace purchases - your Credit Card information is never sent or stored anywhere on the ' . $_inf['system_name'] . ' servers. Your payment information is encrypted and sent directly to Stripe (and in turn Stripe lets our server know if your purchase was successful, and what you purchased).</div></div>';
    jrCore_page_custom($note);

    jrMarket_browse_tabs($active);

    if (isset($_rs['success']) && $_rs['success'] == 'no results') {
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            jrCore_set_form_notice('notice', "There are no items in the Marketplace that match your search condition");
        }
        else {
            jrCore_set_form_notice('notice', "There are no new {$_post['_1']}s in the Marketplace that you have not already installed");
        }
    }
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/{$_post['_1']}");
    jrCore_get_form_notice();

    $_rs['type']             = ucfirst($_post['_1']);
    $_rs['show_item_status'] = 0;
    if (count($_rt) > 1) {
        $_rs['show_item_status'] = 1;
    }
    $_rs['browse_base_url'] = jrCore_strip_url_params(jrCore_get_current_url(), array('p'));

    // If we are showing bundles, figure out the savings
    if ($_post['_1'] == 'bundle') {
        if (isset($_rs['_items']) && is_array($_rs['_items'])) {
            foreach ($_rs['_items'] as $k => $_v) {
                if (isset($_v['bundle_items']) && is_array($_v['bundle_items'])) {
                    $tot = 0;
                    foreach ($_v['bundle_items'] as $bv) {
                        if (isset($bv['market_file_item_price']) && strlen($bv['market_file_item_price']) > 0 && $bv['market_file_item_price'] > 0) {
                            $tot += $bv['market_file_item_price'];
                        }
                    }
                    if ($tot > 0 && $tot > $_v['bundle_item_price']) {
                        $_rs['_items'][$k]['bundle_savings'] = ($tot - $_v['bundle_item_price']);
                    }
                }
            }
        }
    }
    elseif ($_post['_1'] == 'module' || $_post['_1'] == 'installed') {
        if (isset($_rs['_items']) && is_array($_rs['_items'])) {
            // See if any of these are paid and if we have a license
            foreach ($_rs['_items'] as $k => $_v) {
                if (isset($_v['market_file_item_price']) && $_v['market_file_item_price'] > 0 && (!isset($_v['market_allow_license_install']) || $_v['market_allow_license_install'] != '1') && (!isset($_mods["{$_v['market_name']}"]['module_license']) || strlen($_mods["{$_v['market_name']}"]['module_license']) === 0)) {
                    $_rs['_items'][$k]['market_allow_license_install'] = '0';
                    $_rs['_items'][$k]['market_already_installed']     = '0';
                }
            }
        }
    }

    $_rs['active_market'] = $_mkt;

    // Page Jumper
    $_rs['pages'] = array(1);
    if (isset($_rs['info'])) {
        $i = 1;
        while ($i < $_rs['info']['total_pages']) {
            $_rs['pages'][] = ++$i;
        }
    }
    $out = jrCore_parse_template('browse.tpl', $_rs, 'jrMarket');
    jrCore_page_custom($out);
    jrCore_page_display();
}

//------------------------------
// system_archive
//------------------------------
function view_jrMarket_system_archive($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'tools');
    jrCore_page_banner('release archive');
    jrCore_get_form_notice();

    // First - get all old module releases
    $show = false;
    $_old = array();
    $_fil = glob(APP_DIR . '/modules/*-release-*');
    if (isset($_fil) && is_array($_fil)) {

        foreach ($_fil as $file) {
            $mod = basename($file);
            $mod = substr($mod, 0, strpos($mod, '-release-'));
            $ver = end(explode('-', $file));
            if (isset($ver{4}) && isset($_mods[$mod]['module_version']) && $ver != $_mods[$mod]['module_version']) {
                $_old[$mod] = $ver;
            }
        }

        if (count($_old) > 0) {
            $dat             = array();
            $dat[1]['title'] = '';
            $dat[1]['width'] = '2%';
            $dat[2]['title'] = 'module';
            $dat[2]['width'] = '48%';
            $dat[3]['title'] = 'your version';
            $dat[3]['width'] = '15%';
            $dat[4]['title'] = 'archive version';
            $dat[4]['width'] = '15%';
            $dat[5]['title'] = 'restore';
            $dat[5]['width'] = '5%';
            $dat[6]['title'] = 'remove archive';
            $dat[6]['width'] = '5%';
            jrCore_page_table_header($dat);

            foreach ($_old as $mod => $ver) {
                $dat             = array();
                $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/{$mod}-release-{$ver}/icon.png\" width=\"32\" alt=\"{$mod}\">";
                $dat[2]['title'] = $_mods[$mod]['module_name'];
                $dat[3]['title'] = $_mods[$mod]['module_version'];
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = $ver;
                $dat[4]['class'] = 'center';
                $dat[5]['title'] = jrCore_page_button("r{$mod}{$ver}", 'restore', "if (confirm('WARNING! Are you sure you want to restore this old version? Doing so could cause your system to no longer function properly!')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/restore_item/module/{$mod}/{$ver}') }");
                $dat[6]['title'] = jrCore_page_button("d{$mod}{$ver}", 'remove archive', "if (confirm('Delete this archived version?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/remove_archive_item/module/{$mod}/{$ver}') }");
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
            $show = true;
        }
    }

    // Next - the skins
    $_old = array();
    $_fil = glob(APP_DIR . '/skins/*-release-*');
    $_mta = array();
    if (isset($_fil) && is_array($_fil)) {

        foreach ($_fil as $file) {
            $mod = basename($file);
            $mod = substr($mod, 0, strpos($mod, '-release-'));
            $ver = end(explode('-', $file));
            if (!isset($_mta[$mod])) {
                $_mta[$mod] = jrCore_skin_meta_data($mod);
            }
            if (isset($ver{4}) && isset($_mta[$mod]['version']) && $ver != $_mta[$mod]['version']) {
                $_old[$mod] = $ver;
            }
        }

        if (count($_old) > 0) {
            $dat             = array();
            $dat[1]['title'] = '';
            $dat[1]['width'] = '2%';
            $dat[2]['title'] = 'skin';
            $dat[2]['width'] = '48%';
            $dat[3]['title'] = 'your version';
            $dat[3]['width'] = '15%';
            $dat[4]['title'] = 'archive version';
            $dat[4]['width'] = '15%';
            $dat[5]['title'] = 'restore';
            $dat[5]['width'] = '5%';
            $dat[6]['title'] = 'remove';
            $dat[6]['width'] = '5%';
            jrCore_page_table_header($dat);

            foreach ($_old as $mod => $ver) {
                $dat             = array();
                $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/skins/{$mod}-release-{$ver}/icon.png\" width=\"32\" alt=\"jrCore\">";
                $dat[2]['title'] = $_mta[$mod]['name'];
                $dat[3]['title'] = $_mta[$mod]['version'];
                $dat[3]['class'] = 'center';
                $dat[4]['title'] = $ver;
                $dat[4]['class'] = 'center';
                $dat[5]['title'] = jrCore_page_button("r[$mod}{$ver}", 'restore', "if (confirm('WARNING! Are you sure you want to restore this old version? Doing so could cause your system to no longer function properly!')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/restore_item/skin/{$mod}/{$ver}') }");
                $dat[6]['title'] = jrCore_page_button("d[$mod}{$ver}", 'remove archive', "if (confirm('Delete this archived version?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/remove_archive_item/skin/{$mod}/{$ver}') }");
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
            $show = true;
        }
    }
    if (!$show) {
        jrCore_set_form_notice('success', 'There are no archived versions of modules or skins');
        jrCore_get_form_notice();
    }
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// restore_item
//------------------------------
function view_jrMarket_restore_item($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || ($_post['_1'] != 'skin' && $_post['_1'] != 'module')) {
        jrCore_set_form_notice('error', 'invalid item type');
        jrCore_location('referrer');
    }
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_set_form_notice('error', 'invalid item name');
        jrCore_location('referrer');
    }
    if (!isset($_post['_3']) || strlen($_post['_3']) !== 5) {
        jrCore_set_form_notice('error', 'invalid item version');
        jrCore_location('referrer');
    }
    if (is_dir(APP_DIR . "/{$_post['_1']}s/{$_post['_2']}-release-{$_post['_3']}")) {
        // We're moving to a different release
        clearstatcache();
        $old_dir = getcwd();
        if (chdir(APP_DIR . "/{$_post['_1']}s")) {
            if (is_link($_post['_2'])) {
                unlink($_post['_2']);
            }
            symlink("{$_post['_2']}-release-{$_post['_3']}", $_post['_2']);
        }
        chdir($old_dir);
        // Verify Module (separate view)
        sleep(1);
        jrCore_set_temp_value('jrMarket', 'marketplace_restore_referrer', jrCore_get_local_referrer());
        jrCore_delete_all_cache_entries();
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/restore_item_update_save/{$_post['_2']}/{$_post['_3']}");
    }
    jrCore_set_form_notice('error', "Invalid release directory - no version found");
    jrCore_location('referrer');

}
//------------------------------
// restore_item_update
//------------------------------
function view_jrMarket_restore_item_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_verify_module($_post['_1'], $_post['_2']);
    $url = jrCore_get_temp_value('jrMarket', 'marketplace_restore_referrer');
    jrCore_delete_temp_value('jrMarket', 'marketplace_restore_referrer');
    jrCore_set_form_notice('success', "The archive version has been restored");
    jrCore_delete_all_cache_entries();
    jrCore_location($url);
}

//------------------------------
// remove_archive_item
//------------------------------
function view_jrMarket_remove_archive_item($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['_1']) || ($_post['_1'] != 'skin' && $_post['_1'] != 'module')) {
        jrCore_set_form_notice('error', 'invalid item type');
        jrCore_location('referrer');
    }
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_set_form_notice('error', 'invalid item name');
        jrCore_location('referrer');
    }
    if (!isset($_post['_3']) || strlen($_post['_3']) < 5 || strlen($_post['_3']) > 6) {
        jrCore_set_form_notice('error', 'invalid item version');
        jrCore_location('referrer');
    }
    if (is_dir(APP_DIR . "/{$_post['_1']}s/{$_post['_2']}-release-{$_post['_3']}")) {
        // Our version directory exists - make sure it is NOT the active version
        if (is_link(APP_DIR . "/{$_post['_1']}s/{$_post['_2']}") && readlink(APP_DIR . "/{$_post['_1']}s/{$_post['_2']}") === "{$_post['_2']}-release-{$_post['_3']}") {
            jrCore_set_form_notice('error', "You cannot delete the active version of the {$_post['_1']}!");
            jrCore_location('referrer');
        }
        jrCore_delete_dir_contents(APP_DIR . "/{$_post['_1']}s/{$_post['_2']}-release-{$_post['_3']}", false);
        @rmdir(APP_DIR . "/{$_post['_1']}s/{$_post['_2']}-release-{$_post['_3']}");
        jrCore_set_form_notice('success', "The archived release version was successully deleted");
    }
    else {
        jrCore_set_form_notice('error', "Invalid release directory - no version found");
    }
    jrCore_location('referrer');
}

//------------------------------
// system_update
//------------------------------
function view_jrMarket_system_update($_post, $_user, $_conf)
{
    global $_mods;
    $_skins = jrCore_get_skins();
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    if (isset($_post['all'])) {
        jrCore_page_admin_tabs('jrMarket', 'tools');
        $title = 'reload modules or skins';
    }
    else {
        jrCore_page_admin_tabs('jrMarket', 'system_update');
        $title = 'system update';
    }

    // delete any old update tmp info
    jrCore_delete_temp_value('jrMarket', 'jrupdate_results');

    if (!$_mkt = jrMarket_get_active_release_system()) {
        jrCore_set_form_notice('error', 'There are no active Marketplace Systems configured where you can download items or get system updates!<br>Enter a new Marketplace System below to enable the Marketplace.', false);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/systems");
    }
    if (!isset($_mkt['system_email']) || !jrCore_checktype($_mkt['system_email'], 'email')) {
        jrCore_set_form_notice('error', 'The Active Marketplace is missing some information required for System Updates');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/release_system_update/id={$_mkt['system_id']}");
    }

    // Make sure we are subscribed to at least 1 channel
    $tbl = jrCore_db_table_name('jrMarket', 'channel');
    $req = "SELECT * FROM {$tbl} WHERE channel_active = '1'";
    $_rt = jrCore_db_query($req, 'channel_id');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'You have not subscribed to any Update Channels - no updates can be provided.<br><br>You can activate an Update Channel from the <a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/release_channels"><b><u>Update Channels</u></b> tool.', false);
        jrCore_page_banner($title, jrMarket_system_jumper());
        jrCore_get_form_notice();
        jrCore_page_display();
        exit;
    }

    // pre-update checks
    $upd = true;
    if (!is_writable(APP_DIR . '/modules')) {
        // See if we are configured for FTP...
        if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
            jrCore_set_form_notice('error', 'Your modules directory is not writable - make sure you have entered FTP settings into the Global Config so the system can install and update modules.');
            jrCore_get_form_notice();
            $upd = false;
        }
    }
    if (!is_writable(APP_DIR . '/skins')) {
        if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
            jrCore_set_form_notice('error', 'Your skins directory is not writable - make sure you have entered FTP settings into the Global Config so the system can install and update skins.');
            jrCore_get_form_notice();
            $upd = false;
        }
    }

    $_ds = jrCore_get_disk_usage();
    if ($_ds['percent_used'] > 90 && $_ds['disk_free'] < 100000000) {
        jrCore_set_form_notice('warning', "Your site is low on disk space (currently using {$_ds['percent_used']}%)<br>Try to free up disk space before installing or updating modules or skins.", false);
    }

    $shw  = false;
    $temp = false;
    if ($upd) {

        $_rs = jrMarket_get_system_updates();
        if ($_rs && is_array($_rs)) {
            if (isset($_rs['error'])) {
                if (isset($_post['all'])) {
                    $shw = true;
                    jrCore_set_form_notice('error', $_rs['error']);
                }
            }
        }
        else {
            jrCore_page_banner($title, jrMarket_system_jumper());
            if (strpos($_rs, 'system maintenance') || strpos($_rs, 'jrLaunch')) {
                jrCore_set_form_notice('notice', 'The Active Marketplace is currently down for maintenance - please try again shortly');
            }
            else {
                jrCore_set_form_notice('error', 'An error was encountered trying to communicate with the Active Marketplace - please try again');
            }
            jrCore_get_form_notice();
            jrCore_page_display();
            exit;
        }

        $_up = array(
            'module' => array(),
            'skin'   => array(),
        );
        $_rl = array();
        $_mu = array();
        $_mr = array();
        $all = false;

        //----------------------------------------
        // First all the modules that have updates
        //----------------------------------------
        foreach ($_mods as $mod => $_inf) {
            if (isset($_rs['module'][$mod]['v']) && version_compare($_inf['module_version'], $_rs['module'][$mod]['v']) === -1) {
                // See if we have requires
                if (!isset($_post['all']) && isset($_rs['module'][$mod]['r']) && strlen($_rs['module'][$mod]['r']) > 0) {
                    // We have requires
                    foreach (explode(',', $_rs['module'][$mod]['r']) as $rmod) {
                        $rmod = trim($rmod);
                        $rver = false;
                        if (strpos($rmod, ':')) {
                            // We have a MINIMUM version for this requires
                            list($rmod, $rver) = explode(':', $rmod);
                        }
                        // Make sure we have it..
                        if (!isset($_mods[$rmod]) || version_compare($_mods[$rmod]['module_version'], $rver) === -1) {
                            // We'll pick it up after the required module is updated
                            continue;
                        }
                    }
                }
                $_up['module'][$mod]      = $_inf;
                $_up['module'][$mod]['d'] = intval($_rs['module'][$mod]['d']);
                $_mr[$mod]                = $_inf['module_name'];
                if (isset($_rs['module'][$mod]['l']) && strlen($_rs['module'][$mod]['l']) === 16) {
                    $all = true;
                }
            }
            elseif (isset($_rs['module'][$mod])) {
                $_rl[$mod] = $_inf;
                $_mr[$mod] = $_inf['module_name'];
            }
            // See if we need to add in the license for this module
            if (isset($_rs['module'][$mod]) && isset($_rs['module'][$mod]['l']{0}) && !isset($_inf['module_license']{0})) {
                $_mu[$mod] = $_rs['module'][$mod]['l'];
                $_mr[$mod] = $_inf['module_name'];
            }
        }
        if (isset($_mu) && is_array($_mu) && count($_mu) > 0) {
            // We have licenses to update in modules
            $tbl = jrCore_db_table_name('jrCore', 'module');
            $req = "UPDATE {$tbl} SET module_license = CASE module_directory\n";
            foreach ($_mu as $mod => $license) {
                $req .= "WHEN '" . jrCore_db_escape($mod) . "' THEN '" . jrCore_db_escape($license) . "'\n";
            }
            $req .= "ELSE module_license END";
            jrCore_db_query($req);
        }
        unset($_mu);

        $ups  = false;
        $_srl = array();
        $_sr  = array();

        //----------------------------------------
        // Next all skins that have updates
        //----------------------------------------
        foreach ($_skins as $s => $sn) {
            $_sk        = jrCore_skin_meta_data($s);
            $_skins[$s] = $_sk;
        }
        foreach ($_skins as $skin => $_inf) {
            if (isset($_rs['skin'][$skin]['v']) && version_compare($_inf['version'], $_rs['skin'][$skin]['v']) === -1) {
                // See if we have requires
                if (!isset($_post['all']) && isset($_rs['skin'][$skin]['r']) && strlen($_rs['skin'][$skin]['r']) > 0) {
                    // We have requires
                    foreach (explode(',', $_rs['skin'][$skin]['r']) as $rmod) {
                        $rmod = trim($rmod);
                        $rver = false;
                        if (strpos($rmod, ':')) {
                            // We have a MINIMUM version for this requires
                            list($rmod, $rver) = explode(':', $rmod);
                        }
                        // Make sure we have it..
                        if (!isset($_mods[$rmod])) {
                            // We'll pick it up after the required module is updated
                            continue;
                        }
                        if (version_compare($_inf['version'], $rver) === -1) {
                            // We'll pick it up after the required module is updated
                            continue;
                        }
                    }
                }
                $_up['skin'][$skin]      = $_inf;
                $_up['skin'][$skin]['d'] = intval($_rs['skin'][$skin]['d']);
                $_sr[$skin]              = $_inf['title'];
                if (isset($_rs['skin'][$skin]['l']) && strlen($_rs['skin'][$skin]['l']) === 16) {
                    $all = true;
                }
            }
            elseif (isset($_rs['skin'][$skin])) {
                $_srl[$skin] = $_inf;
                $_sr[$skin]  = $_inf['title'];
            }
            // See if we need to add in the license for this module
            if (isset($_rs['skin'][$skin]) && isset($_rs['skin'][$skin]['l']{0})) {
                // See if we have an existing license field for this skin
                if (isset($_conf["{$skin}_license"])) {
                    jrCore_set_setting_value($skin, 'license', $_rs['skin'][$skin]['l']);
                }
                else {
                    // Create it
                    $_fld = array(
                        'name'    => 'license',
                        'value'   => $_rs['skin'][$skin]['l'],
                        'default' => $_rs['skin'][$skin]['l'],
                        'type'    => 'hidden'
                    );
                    jrCore_update_setting($skin, $_fld);
                }
                $ups        = true;
                $_sr[$skin] = $_inf['title'];
            }
        }
        if ($ups) {
            // Reset settings so they reload
            jrCore_delete_config_cache();
        }

        natcasesort($_mr);
        natcasesort($_sr);

        // We need to save our UPDATES to the DB so we can access them
        // during an actual update event (to check for dependencies)
        jrCore_set_temp_value('jrMarket', 'jrupdate_results', $_up);

        $pass = jrCore_get_option_image('pass');
        $fail = jrCore_get_option_image('fail');
        $blk  = false;

        $temp = '';
        if (!isset($_post['all']) && (count($_up['module']) > 0 || count($_up['skin'])) > 0) {
            $mkey = md5(microtime());
            jrCore_delete_temp_value('jrMarket', 'modal_update_key');
            jrCore_set_temp_value('jrMarket', 'modal_update_key', $mkey);
            if ($all) {
                $temp = jrCore_page_button("uall", 'update all items', "if (confirm('Install all available updates?')){ jrMarket_update_all_items('{$mkey}'); }");
            }
            else {
                $temp = jrCore_page_button("uall", 'update all items', 'disabled');
            }
            $_args = array(
                'modal_width'   => 600,
                'modal_height'  => 400,
                'modal_note'    => 'Installing all available updates',
                'modal_onclick' => "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/validate_modules/bundle')"
            );
            jrCore_form_modal_window('jrMarket', $_args);
        }

        if (count($_up['module']) > 0 || count($_up['skin']) > 0 || isset($_post['all'])) {
            jrCore_page_banner($title, jrMarket_system_jumper() . '&nbsp;' . $temp);
            jrCore_get_form_notice();
        }

        // Our progress indicator for an individual item
        $pi = "<img src=\"{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/submit.gif\" width=\"24\" height=\"24\" style=\"display:none\" alt=\"working...\">";

        if (isset($_rs['module']['jrCore']) && (isset($_up['module']['jrCore']) || isset($_post['all']))) {

            $dat             = array();
            $dat[1]['title'] = '';
            $dat[1]['width'] = '2%';
            $dat[2]['title'] = 'module';
            $dat[2]['width'] = '43%';
            $dat[3]['title'] = 'installed';
            $dat[3]['width'] = '10%';
            $dat[4]['title'] = 'available';
            $dat[4]['width'] = '10%';
            $dat[5]['title'] = 'changes';
            $dat[5]['width'] = '10%';
            $dat[6]['title'] = 'channel';
            $dat[6]['width'] = '10%';
            $dat[7]['title'] = 'status';
            $dat[7]['width'] = '10%';
            $dat[8]['title'] = 'action';
            $dat[8]['width'] = '5%';
            jrCore_page_table_header($dat);

            // Check for core first - if there is a core update, it must be done before
            // other modules are going to be updated
            $blk             = false;
            $dat             = array();
            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/jrCore/icon.png\" width=\"32\" alt=\"jrCore\">";
            $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_mods['jrCore']['module_url']}/admin/info\">{$_mods['jrCore']['module_name']}</a>";
            $dat[3]['title'] = $_mods['jrCore']['module_version'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = (isset($_rs['module']['jrCore']['v'])) ? $_rs['module']['jrCore']['v'] : '-';
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = ($_rs['module']['jrCore']['o'] == '1') ? "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/changelog/jrCore','changelog',800,500,'yes');return false\"><span style=\"text-decoration:underline;\"> changes</span></a>" : '-';
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = (isset($_rs['module']['jrCore']['c'])) ? $_rs['module']['jrCore']['c'] : 'stable';
            $dat[6]['class'] = 'center';
            if (isset($_rs['module']['jrCore']['v']) && version_compare($_mods['jrCore']['module_version'], $_rs['module']['jrCore']['v']) === -1) {
                $dat[7]['title'] = $fail;
                $dat[8]['title'] = $pi . jrCore_page_button("ujrCore", 'update', "jrMarket_update_item('module','jrCore','update'," . intval($_rs['module']['jrCore']['d']) . ");");
                $blk             = true;
            }
            else {
                $dat[7]['title'] = $pass;
                $dat[8]['title'] = $pi . jrCore_page_button("ujrCore", 'reload', "if (confirm('Reload this module from the Marketplace?')) { jrMarket_update_item('module','jrCore','reload'," . intval($_rs['module']['jrCore']['d']) . "); }");
            }
            $dat[7]['class'] = 'center';
            $dat[8]['class'] = 'center';
            jrCore_page_table_row($dat);
            jrCore_page_table_footer();
            $shw = true;
        }

        // Go through installed modules
        if (!$blk) {

            if (count($_up['module']) > 0 || (isset($_post['all']) && count($_rs['module']) > 0)) {
                $dat             = array();
                $dat[1]['title'] = '';
                $dat[1]['width'] = '2%';
                $dat[2]['title'] = 'module';
                $dat[2]['width'] = '43%';
                $dat[3]['title'] = 'installed';
                $dat[3]['width'] = '10%';
                $dat[4]['title'] = 'available';
                $dat[4]['width'] = '10%';
                $dat[5]['title'] = 'changes';
                $dat[5]['width'] = '10%';
                $dat[6]['title'] = 'channel';
                $dat[6]['width'] = '10%';
                $dat[7]['title'] = 'status';
                $dat[7]['width'] = '10%';
                $dat[8]['title'] = 'action';
                $dat[8]['width'] = '5%';
                jrCore_page_table_header($dat);
            }

            // Updates
            if (count($_up['module']) > 0) {
                foreach ($_up['module'] as $mod => $_inf) {
                    $dat             = array();
                    $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/{$mod}/icon.png\" width=\"32\" alt=\"{$mod}\">";
                    $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_inf['module_url']}/admin/info\">{$_inf['module_name']}</a>";
                    $dat[3]['title'] = $_inf['module_version'];
                    $dat[3]['class'] = 'center';
                    $dat[4]['title'] = (isset($_rs['module'][$mod]['v'])) ? $_rs['module'][$mod]['v'] : '-';
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = ($_rs['module'][$mod]['o'] == '1') ? "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/changelog/{$mod}','changelog',800,500,'yes');return false\"><span style=\"text-decoration:underline;\"> changes</span></a>" : '-';
                    $dat[5]['class'] = 'center';
                    $dat[6]['title'] = (isset($_rs['module'][$mod]['c'])) ? $_rs['module'][$mod]['c'] : 'stable';
                    $dat[6]['class'] = 'center';
                    // We must have a license to update
                    if (!isset($_rs['module'][$mod]['l']) || strlen($_rs['module'][$mod]['l']) !== 16) {
                        $dat[7]['title'] = 'no<br>license';
                        $dat[7]['class'] = 'center error';
                        $dat[8]['title'] = jrCore_page_button("p{$mod}", 'purchase', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/module/search_string={$mod}/sli=1');");
                        $dat[8]['class'] = 'error';
                    }
                    elseif (isset($_rs['module'][$mod]['v']) && version_compare($_inf['module_version'], $_rs['module'][$mod]['v']) === -1) {
                        $dat[7]['title'] = $fail;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$mod}", 'update', "jrMarket_update_item('module','{$mod}','update'," . intval($_rs['module'][$mod]['d']) . ");");
                        $dat[8]['class'] = 'center';
                    }
                    else {
                        $dat[7]['title'] = $pass;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$mod}", 'reload', "if (confirm('Reload this module from the Marketplace?')) { jrMarket_update_item('module','{$mod}','reload'," . intval($_rs['module'][$mod]['d']) . "); }");
                        $dat[8]['class'] = 'center';
                    }
                    jrCore_page_table_row($dat);
                }
                $shw = true;
            }

            // Reloads
            if (count($_rl) > 0 && isset($_post['all'])) {
                foreach ($_mr as $mod => $mnam) {
                    if (!isset($_rl[$mod]) || $mod == 'jrCore') {
                        continue;
                    }
                    $_inf            = $_rl[$mod];
                    $dat             = array();
                    $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/{$mod}/icon.png\" width=\"32\" alt=\"{$mod}\">";
                    $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_inf['module_url']}/admin/info\">{$_inf['module_name']}</a>";
                    $dat[3]['title'] = $_inf['module_version'];
                    $dat[3]['class'] = 'center';
                    $dat[4]['title'] = (isset($_rs['module'][$mod]['v'])) ? $_rs['module'][$mod]['v'] : '-';
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = ($_rs['module'][$mod]['o'] == '1') ? "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/changelog/{$mod}','changelog',800,500,'yes');return false\"><span style=\"text-decoration:underline;\"> changes</span></a>" : '-';
                    $dat[5]['class'] = 'center';
                    $dat[6]['title'] = (isset($_rs['module'][$mod]['c'])) ? $_rs['module'][$mod]['c'] : 'stable';
                    $dat[6]['class'] = 'center';
                    // We must have a license to update
                    if (!isset($_rs['module'][$mod]['l']) || strlen($_rs['module'][$mod]['l']) !== 16) {
                        $dat[7]['title'] = 'no<br>license';
                        $dat[7]['class'] = 'center error';
                        $dat[8]['title'] = jrCore_page_button("p{$mod}", 'purchase', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/module/search_string={$mod}/sli=1');");
                        $dat[8]['class'] = 'error';
                    }
                    elseif (isset($_rs['module'][$mod]['v']) && version_compare($_inf['module_version'], $_rs['module'][$mod]['v']) === -1) {
                        $dat[7]['title'] = $fail;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$mod}", 'update', "jrMarket_update_item('module','{$mod}','update'," . intval($_rs['module'][$mod]['d']) . ");");
                        $dat[8]['class'] = 'center';
                    }
                    else {
                        $dat[7]['title'] = $pass;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$mod}", 'reload', "if (confirm('Reload this module from the Marketplace?')) { jrMarket_update_item('module','{$mod}','reload'," . intval($_rs['module'][$mod]['d']) . "); }");
                        $dat[8]['class'] = 'center';
                    }
                    jrCore_page_table_row($dat);
                }
                $shw = true;
            }
            if (count($_up['module']) > 0 || isset($_post['all'])) {
                jrCore_page_table_footer();
            }
        }
        elseif (isset($_up['module']['jrCore'])) {
            $dat             = array();
            $dat[1]['title'] = '<p>You must first update the ' . $_mods['jrCore']['module_name'] . ' module before updating other modules.</p>';
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
        }

        // Go through installed skins
        if (!$blk) {

            // Skins
            if (count($_up['skin']) > 0 || (isset($_post['all']) && count($_rs['skin']) > 0)) {
                $dat             = array();
                $dat[1]['title'] = '';
                $dat[1]['width'] = '2%';
                $dat[2]['title'] = 'skin';
                $dat[2]['width'] = '43%';
                $dat[3]['title'] = 'installed';
                $dat[3]['width'] = '10%';
                $dat[4]['title'] = 'available';
                $dat[4]['width'] = '10%';
                $dat[5]['title'] = 'changes';
                $dat[5]['width'] = '10%';
                $dat[6]['title'] = 'channel';
                $dat[6]['width'] = '10%';
                $dat[7]['title'] = 'status';
                $dat[7]['width'] = '10%';
                $dat[8]['title'] = 'action';
                $dat[8]['width'] = '5%';
                jrCore_page_table_header($dat);
            }

            // Updates
            if (count($_up['skin']) > 0) {
                foreach ($_up['skin'] as $skin => $_inf) {
                    $dat             = array();
                    $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/skins/{$skin}/icon.png\" width=\"32\" alt=\"{$skin}\">";
                    $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/info/skin={$_inf['name']}\">" . ((isset($_inf['title'])) ? $_inf['title'] : $_inf['name']) . "</a>";
                    $dat[3]['title'] = $_inf['version'];
                    $dat[3]['class'] = 'center';
                    $dat[4]['title'] = (isset($_rs['skin'][$skin]['v'])) ? $_rs['skin'][$skin]['v'] : '-';
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = ($_rs['skin'][$skin]['o'] == '1') ? "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/changelog/{$skin}','changelog',800,500,'yes');return false\"><span style=\"text-decoration:underline;\"> changes</span></a>" : '-';
                    $dat[5]['class'] = 'center';
                    $dat[6]['title'] = (isset($_rs['skin'][$skin]['c'])) ? $_rs['skin'][$skin]['c'] : 'stable';
                    $dat[6]['class'] = 'center';
                    // We must have a license to update
                    if (!isset($_rs['skin'][$skin]['l']) || strlen($_rs['skin'][$skin]['l']) !== 16) {
                        $dat[7]['title'] = 'no<br>license';
                        $dat[7]['class'] = 'center error';
                        $dat[8]['title'] = jrCore_page_button("p{$skin}", 'purchase', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/skin/search_string={$skin}/sli=1');");
                        $dat[8]['class'] = 'error';
                    }
                    elseif (isset($_rs['skin'][$skin]['v']) && version_compare($_inf['version'], $_rs['skin'][$skin]['v']) === -1) {
                        $dat[7]['title'] = $fail;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$skin}", 'update', "jrMarket_update_item('skin','{$skin}','update'," . intval($_rs['skin'][$skin]['d']) . ");");
                        $dat[8]['class'] = 'center';
                    }
                    else {
                        $dat[7]['title'] = $pass;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$skin}", 'reload', "if (confirm('Reload this skin from the Marketplace?')) { jrMarket_update_item('skin','{$skin}','reload'," . intval($_rs['skin'][$skin]['d']) . "); }");
                        $dat[8]['class'] = 'center';
                    }
                    jrCore_page_table_row($dat);
                }
                $shw = true;
            }

            // Reloads
            if (count($_srl) > 0 && isset($_post['all'])) {
                foreach ($_sr as $skin => $snam) {
                    if (!isset($_srl[$skin])) {
                        continue;
                    }
                    $_inf            = $_srl[$skin];
                    $dat             = array();
                    $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/skins/{$skin}/icon.png\" width=\"32\" alt=\"{$skin}\">";
                    $dat[2]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/info/skin={$_inf['name']}\">" . ((isset($_inf['title'])) ? $_inf['title'] : $_inf['name']) . "</a>";
                    $dat[3]['title'] = $_inf['version'];
                    $dat[3]['class'] = 'center';
                    $dat[4]['title'] = (isset($_rs['skin'][$skin]['v'])) ? $_rs['skin'][$skin]['v'] : '-';
                    $dat[4]['class'] = 'center';
                    $dat[5]['title'] = ($_rs['skin'][$skin]['o'] == '1') ? "<a onclick=\"popwin('{$_conf['jrCore_base_url']}/{$_post['module_url']}/changelog/{$skin}','changelog',800,500,'yes');return false\"><span style=\"text-decoration:underline;\"> changes</span></a>" : '-';
                    $dat[5]['class'] = 'center';
                    $dat[6]['title'] = (isset($_rs['skin'][$skin]['c'])) ? $_rs['skin'][$skin]['c'] : 'stable';
                    $dat[6]['class'] = 'center';
                    // We must have a license to update
                    if (!isset($_rs['skin'][$skin]['l']) || strlen($_rs['skin'][$skin]['l']) !== 16) {
                        $dat[7]['title'] = 'no<br>license';
                        $dat[7]['class'] = 'center error';
                        $dat[8]['title'] = jrCore_page_button("p{$skin}", 'purchase', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/skin/search_string={$skin}/sli=1');");
                        $dat[8]['class'] = 'error';
                    }
                    elseif (isset($_rs['skin'][$skin]['v']) && version_compare($_inf['version'], $_rs['skin'][$skin]['v']) === -1) {
                        $dat[7]['title'] = $fail;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$skin}", 'update', "jrMarket_update_item('skin','{$skin}','update'," . intval($_rs['skin'][$skin]['d']) . ");");
                        $dat[8]['class'] = 'center';
                    }
                    else {
                        $dat[7]['title'] = $pass;
                        $dat[7]['class'] = 'center';
                        $dat[8]['title'] = $pi . jrCore_page_button("u{$skin}", 'reload', "if (confirm('Reload this skin from the Marketplace?')) { jrMarket_update_item('skin','{$skin}','reload'," . intval($_rs['skin'][$skin]['d']) . "); }");
                        $dat[8]['class'] = 'center';
                    }
                    jrCore_page_table_row($dat);
                }
                $shw = true;
            }
            if (count($_up['skin']) > 0 || isset($_post['all'])) {
                jrCore_page_table_footer();
            }
        }
    }
    if (!$shw) {
        jrCore_set_form_notice('success', 'All modules and skins are up to date');
        jrCore_page_banner($title, jrMarket_system_jumper() . '&nbsp;' . $temp);
        jrCore_get_form_notice();
    }
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// changelog
//------------------------------
function view_jrMarket_changelog($_post, $_user, $_conf)
{
    jrUser_master_only();
    // Check for item
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_set_form_notice('error', 'Invalid marketplace item - please try again');
        jrCore_get_form_notice();
    }
    $_dt = array(
        'item' => $_post['_1']
    );
    if (!$_mkt = jrMarket_get_active_release_system()) {
        jrCore_set_form_notice('error', 'There are no active Marketplace Systems configured where you can download items or get system updates!<br>Enter a new Marketplace System below to enable the Marketplace.', false);
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/systems");
    }
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/changelog", $_dt, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (!isset($_rs) || strpos($_rs, '{') !== 0) {
        jrCore_set_form_notice('error', 'Error communicating with update server - please try again');
        jrCore_get_form_notice();
    }
    $_rs = json_decode($_rs, true);
    if (!isset($_rs['changes'])) {
        jrCore_set_form_notice('error', 'Unable to retrieve changelog for item');
        jrCore_get_form_notice();
    }
    jrCore_page_banner('Change Log', htmlentities($_post['_1']));
    jrCore_page_custom('<div class="p10 normal" style="font-family:monospace;white-space:pre-wrap">' . $_rs['changes'] . '</div>');
    jrCore_page_close_button();
    jrCore_page_set_meta_header_only();
    jrCore_page_display();
}

//------------------------------
// update_all_items
//------------------------------
function view_jrMarket_update_all_items($_post, $_user, $_conf)
{
    global $_post;
    jrUser_master_only();
    $_post['jr_html_modal_token'] = jrCore_get_temp_value('jrMarket', 'modal_update_key');
    $_up                          = jrCore_get_temp_value('jrMarket', 'jrupdate_results');
    if (!isset($_up) || !is_array($_up)) {
        jrCore_form_modal_notice('error', 'Unable to retrieve update data - please try again');
        jrCore_form_modal_notice('complete', 'Errors were encountered updating the items');
        exit;
    }
    jrCore_logger('INF', 'installing all marketplace updates');

    // Make sure we have time to run
    ignore_user_abort(true);
    ini_set('max_execution_time', 3600);

    $_md = array();
    foreach ($_up as $type => $_todo) {
        foreach ($_todo as $item => $_info) {
            switch ($type) {
                case 'module':
                    if (!isset($_info['module_license']) || strlen($_info['module_license']) !== 16) {
                        jrCore_form_modal_notice('update', "error updating module: {$item} - no license");
                        jrCore_logger('CRI', "No license found for {$item} - reload System Update so the backend will generate a license if this item is free.");
                        continue 2;
                    }
                    jrCore_form_modal_notice('update', "installing module update for: {$item}");
                    if (jrMarket_update_module($item, $_up['module'], false, true, $_info['d'])) {
                        jrCore_form_modal_notice('update', "installed module update: {$item}");
                        $_md[] = $item;
                    }
                    break;
                case 'skin':
                    jrCore_form_modal_notice('update', "installing skin update for: {$item}");
                    if (jrMarket_update_skin($item, $_up['skin'], false, true, $_info['d'])) {
                        jrCore_form_modal_notice('update', "installed skin update: {$item}");
                    }
                    break;
                default:
                    jrCore_form_modal_notice('error', 'Invalid update type received - please try again');
                    jrCore_form_modal_notice('complete', 'Errors were encountered updating the items');
                    exit;
                    break;
            }
            sleep(1); // Gives time for update to show
        }
    }
    jrCore_logger('INF', 'all marketplace updates successfully installed');
    $_post['jr_html_modal_token'] = jrCore_get_temp_value('jrMarket', 'modal_update_key');
    jrCore_form_modal_notice('complete', 'All items have been successfully updated - click close to validate the update');
    jrMarket_reset_opcode_caches();
    sleep(1);
    jrCore_delete_temp_value('jrMarket', 'modal_update_key');

    // Validate modules
    if (count($_md) > 0) {
        jrCore_set_temp_value('jrMarket', 'bundle_update', $_md);
    }
    exit;
}

//------------------------------
// update_item
//------------------------------
function view_jrMarket_update_item($_post, $_user, $_conf)
{
    global $_mods;
    $_skins = jrCore_get_skins();
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['_2']) || (!isset($_mods["{$_post['_2']}"]) && !isset($_skins["{$_post['_2']}"]))) {
        $_rs = array('error' => 'Invalid update item received - please try again');
        jrCore_json_response($_rs);
    }

    // Make sure we have time to run
    ignore_user_abort(true);
    ini_set('max_execution_time', 1800);

    $atype = 'updated';
    if (isset($_post['_3']) && $_post['_3'] == 'reload') {
        $atype = 'reloaded';
    }

    // Our Marketplace ID comes in as id
    $mid = intval($_post['id']);

    $_set = jrCore_get_temp_value('jrMarket', 'jrupdate_results');
    if (!isset($_set) || !is_array($_set)) {
        $_rs = array('error' => 'Invalid update set received - please try again');
        jrCore_json_response($_rs);
    }
    switch ($_post['_1']) {
        case 'module':
            if (jrMarket_update_module($_post['_2'], $_set['module'], true, false, $mid)) {
                jrMarket_reset_opcode_caches();
                jrCore_set_form_notice('success', "The {$_post['_1']} was successfully {$atype}");
                $_rs = array(
                    'success' => "The {$_post['_1']} was successfully {$atype}",
                    'url'     => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/validate_modules/{$_post['_2']}"
                );
                if (isset($_post['_3']) && $_post['_3'] == 'reload') {
                    $_rs['url'] .= '/reload';
                }
                jrCore_json_response($_rs);
            }
            break;
        case 'skin':
            if (jrMarket_update_skin($_post['_2'], $_set['skin'], true, false, $mid)) {
                jrMarket_reset_opcode_caches();
                jrCore_set_form_notice('success', "The {$_post['_1']} was successfully {$atype}");
                $_rs = array(
                    'success' => "The {$_post['_1']} was successfully {$atype}",
                    'url'     => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_update"
                );
                jrCore_json_response($_rs);
            }
            break;
        default:
            $_rs = array('error' => 'Invalid update type received - please try again');
            jrCore_json_response($_rs);
            break;
    }
    $_rs = array('error' => 'an error was encountered updating the item - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// validate_modules
//------------------------------
function view_jrMarket_validate_modules($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (isset($_post['_1']) && $_post['_1'] == 'bundle') {
        $_bn = jrCore_get_temp_value('jrMarket', 'bundle_update');
        jrCore_delete_temp_value('jrMarket', 'bundle_update');
        if (!$_bn || !is_array($_bn)) {
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_update");
        }
        foreach ($_bn as $mod) {
            if (is_file(APP_DIR . "/modules/{$mod}/include.php")) {
                jrCore_verify_module($mod);
            }
        }
    }
    else {
        $i = 1;
        $d = false;
        while (!$d) {
            if (isset($_post["_{$i}"]) && strlen($_post["_{$i}"]) > 0) {
                if (is_file(APP_DIR . '/modules/' . $_post["_{$i}"] . '/include.php')) {
                    jrCore_verify_module($_post["_{$i}"]);
                }
                $i++;
            }
            else {
                break;
            }
        }
    }
    // Force reload of new module on next load
    jrCore_delete_config_cache();
    if (isset($_post['_2']) && $_post['_2'] == 'reload') {
        $murl = jrCore_get_module_url($_post['_1']);
        jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/admin/info");
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_update");
}

//------------------------------
// purchase (paid items)
//------------------------------
function view_jrMarket_purchase($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    $_to_check = array('price', 'market_id');
    foreach ($_to_check as $chk) {
        if (!isset($_post[$chk])) {
            $_rs = array('error' => 'Invalid post data received - please try again');
            jrCore_json_response($_rs);
        }
    }
    switch ($_post['type']) {
        case 'module':
        case 'skin':
        case 'bundle':
            break;
        default:
            $_rs = array('error' => 'Invalid Type received - must be one of module,skin,bundle');
            jrCore_json_response($_rs);
            break;
    }
    if (!$_mkt = jrMarket_get_active_release_system()) {
        $_rs = array('error' => 'There are no active Marketplace Systems configured where you can purchase Marketplace items');
        jrCore_json_response($_rs);
    }
    $_dt = array(
        'token'     => (isset($_post['token'])) ? $_post['token'] : '',  // Only on first time purchase
        'sysid'     => $_mkt['system_code'],
        'type'      => $_post['type'],
        'item'      => $_post['item'],
        'market_id' => $_post['market_id'],
        'price'     => $_post['price'],
        'host'      => jrCore_url_encode_string($_conf['jrCore_base_url'])
    );
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/purchase", $_dt, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (isset($_rs) && strpos($_rs, '{') === 0) {
        $_rs = json_decode($_rs, true);
        if (isset($_rs['error'])) {
            $_rs = array('error' => $_rs['error']);
        }
        if ($_post['type'] == 'bundle') {
            // The license string can come in REALLY long here - we need to save
            // it off to a temp key and pass that key as the license
            $_tm = jrCore_url_decode_string($_rs['license']);
            $key = md5(microtime());
            jrCore_set_temp_value('jrMarket', $key, $_tm);
            $_rs['license'] = $key;
        }
        jrCore_json_response($_rs);
    }
    $_rs = array('error' => 'Unable to complete purchase - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// license_item (free items)
//------------------------------
function view_jrMarket_license_item($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        $_rs = array('error' => 'Invalid license item received - please try again');
        jrCore_json_response($_rs);
    }
    switch ($_post['_1']) {
        case 'module':
        case 'skin':
        case 'bundle':
            break;
        default:
            $_rs = array('error' => 'Invalid item type received - must be one of module,skin,bundle');
            jrCore_json_response($_rs);
            break;
    }
    if (!$_mkt = jrMarket_get_active_release_system()) {
        $_rs = array('error' => 'There are no active Marketplace Systems configured where you can license Marketplace items');
        jrCore_json_response($_rs);
    }
    // Request a license for this item
    $_dt = array(
        'sysid' => $_mkt['system_code'],
        'type'  => $_post['_1'],
        'item'  => $_post['_2'],
        'mid'   => intval($_post['_3']),
        'host'  => jrCore_url_encode_string($_conf['jrCore_base_url'])
    );
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/license", $_dt, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (isset($_rs) && strpos($_rs, '{') === 0) {
        $_rs = json_decode($_rs, true);
    }
    if (isset($_rs['error'])) {
        $_rs = array('error' => $_rs['error']);
        jrCore_json_response($_rs);
    }
    // Make sure we get a valid license back
    if (!isset($_rs['license']) || strlen($_rs['license']) < 16) {
        $_rs = array('error' => 'Invalid license received from marketplace - please try again');
        jrCore_json_response($_rs);
    }
    $_rs = array(
        'success' => 'item licensed',
        'license' => $_rs['license']
    );

    // If this is a bundle we need to so some setup for installation
    if ($_post['_1'] == 'bundle') {
        $key = md5($_rs['license']);
        jrCore_set_temp_value('jrMarket', $key, jrCore_url_decode_string($_rs['license']));
        $_rs['license'] = $key;
    }
    jrCore_json_response($_rs);
}

//------------------------------
// install_item
//------------------------------
function view_jrMarket_install_item($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        $_rs = array('error' => 'Invalid install item received - please try again');
        jrCore_json_response($_rs);
    }

    // Make sure we have time to run
    ignore_user_abort(true);
    ini_set('max_execution_time', 1800);

    switch ($_post['_1']) {

        case 'module':
            if (!isset($_post['license']) || strlen($_post['license']) !== 16) {
                $_rs = array('error' => 'Invalid module license received - please try again');
                jrCore_json_response($_rs);
            }
            if (jrMarket_install_module($_post['_2'], $_post['license'], $_post['_3'])) {
                $_mta = jrCore_module_meta_data($_post['_2']);

                $old = false;
                if (isset($_mods["{$_post['_2']}"]['module_version'])) {
                    $old = true;
                }
                $_tmp                              = array(
                    'modules' => array(),
                    'skins'   => array()
                );
                $_tmp['modules']["{$_post['_2']}"] = array(
                    'old_version' => ($old) ? $_mods["{$_post['_2']}"]['module_version'] : '-',
                    'action'      => ($old) ? 'updated' : 'installed'
                );
                // Add this item into our history
                $tbl = jrCore_db_table_name('jrMarket', 'install');
                $req = "INSERT INTO {$tbl} (install_time, install_data) VALUES (UNIX_TIMESTAMP(), '" . jrCore_db_escape(json_encode($_tmp)) . "')";
                $iid = jrCore_db_query($req, 'INSERT_ID');
                if (!jrCore_checktype($iid, 'number_nz')) {
                    jrCore_logger('CRI', "Unable to save install history for module: {$_post['_2']}");
                }

                $_rs = array(
                    'success' => 'The new module was successfully installed',
                    'url'     => "{$_conf['jrCore_base_url']}/{$_mta['url']}/admin/info"
                );
                jrCore_set_form_notice('success', $_rs['success']);
                jrCore_json_response($_rs);
            }
            break;

        case 'skin':
            if (!isset($_post['license']) || strlen($_post['license']) !== 16) {
                $_rs = array('error' => 'Invalid skin license received - please try again');
                jrCore_json_response($_rs);
            }
            if (jrMarket_install_skin($_post['_2'], $_post['license'], $_post['_3'])) {

                $_mt = jrCore_skin_meta_data($_post['_2']);
                $old = false;
                if (isset($_mt['version'])) {
                    $old = true;
                }
                $_tmp                            = array(
                    'modules' => array(),
                    'skins'   => array()
                );
                $_tmp['skins']["{$_post['_2']}"] = array(
                    'old_version' => ($old) ? $_mt['version'] : '-',
                    'action'      => ($old) ? 'updated' : 'installed'
                );
                // Add this item into our history
                $tbl = jrCore_db_table_name('jrMarket', 'install');
                $req = "INSERT INTO {$tbl} (install_time, install_data) VALUES (UNIX_TIMESTAMP(), '" . jrCore_db_escape(json_encode($_tmp)) . "')";
                $iid = jrCore_db_query($req, 'INSERT_ID');
                if (!jrCore_checktype($iid, 'number_nz')) {
                    jrCore_logger('CRI', "Unable to save install history for skin: {$_post['_2']}");
                }

                $url = jrCore_get_module_url('jrCore');
                $_rs = array(
                    'success' => 'The new skin was successfully installed',
                    'url'     => "{$_conf['jrCore_base_url']}/{$url}/skin_admin/info/skin=" . $_post['_2']
                );
                jrCore_set_form_notice('success', $_rs['success']);
                jrCore_json_response($_rs);
            }
            break;

        case 'bundle':

            if (!isset($_post['license']) || strlen($_post['license']) < 16) {
                $_rs = array('error' => 'Invalid bundle license received - please try again');
                jrCore_json_response($_rs);
            }
            // For a bundle install our returned license will be the items
            // we are installing - i.e.
            // jrYouTube:module:<license>,jrProfileTweaks:module:<license>[,...]
            $_lic = explode(',', jrCore_get_temp_value('jrMarket', $_post['license']));
            jrCore_delete_temp_value('jrMarket', $_post['license']);
            if (!is_array($_lic)) {
                $_rs = array('error' => 'Invalid bundle license received - please try again (2)');
                jrCore_json_response($_rs);
            }
            $_tmp = array(
                'modules' => array(),
                'skins'   => array()
            );
            foreach ($_lic as $item) {
                list($name, $type, $license) = explode(':', $item, 3);
                switch ($type) {
                    case 'module':
                        if (jrMarket_install_module($name, $license)) {
                            $old = false;
                            if (isset($_mods[$name]['module_version'])) {
                                $old = true;
                            }
                            $_tmp['modules'][$name] = array(
                                'old_version' => ($old) ? $_mods[$name]['module_version'] : '-',
                                'action'      => ($old) ? 'updated' : 'installed'
                            );
                        }
                        break;
                    case 'skin':
                        if (jrMarket_install_skin($name, $license)) {
                            if (!isset($_skins)) {
                                $_skins = jrCore_get_skins();
                            }
                            $_mt = false;
                            $old = false;
                            if (isset($_skins[$name])) {
                                $_mt = jrCore_skin_meta_data($name);
                                $old = true;
                            }
                            $_tmp['skins'][$name] = array(
                                'old_version' => ($old) ? $_mt['version'] : '-',
                                'action'      => ($old) ? 'updated' : 'installed'
                            );
                        }
                        break;
                    default:
                        // We should _never_ fall through here
                        $_rs = array('error' => 'Invalid item type returned in license result: ' . htmlentities($type));
                        jrCore_json_response($_rs);
                        break;
                }
            }
            // Make sure we installed something
            if (count($_tmp['modules']) > 0 || count($_tmp['skins']) > 0) {
                // We installed stuff
                $tbl = jrCore_db_table_name('jrMarket', 'install');
                $req = "INSERT INTO {$tbl} (install_time, install_data) VALUES (UNIX_TIMESTAMP(), '" . jrCore_db_escape(json_encode($_tmp)) . "')";
                $iid = jrCore_db_query($req, 'INSERT_ID');
                $_rs = array(
                    'success' => 'bundle installed',
                    'url'     => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/install_result/{$iid}"
                );
                jrCore_json_response($_rs);
            }
            else {
                $_rs = array('error' => 'Unable to install any of the bundle items - please try again');
                jrCore_json_response($_rs);
            }
            break;
    }

    // Fall through from case
    $_rs = array('error' => 'Invalid install type received - please try again');
    jrCore_json_response($_rs);
}

//------------------------------
// install_result
//------------------------------
function view_jrMarket_install_result($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'system_update');
    jrCore_page_banner('install results');

    // We need a valid install id
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid install_id');
    }
    $tbl = jrCore_db_table_name('jrMarket', 'install');
    $req = "SELECT * FROM {$tbl} WHERE install_id = '{$_post['_1']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_notice_page('error', 'Invalid install_id');
    }
    $_rt = json_decode($_rt['install_data'], true);

    if (isset($_rt['modules']) && is_array($_rt['modules']) && count($_rt['modules']) > 0) {

        $dat             = array();
        $dat[1]['title'] = '';
        $dat[1]['width'] = '2%';
        $dat[2]['title'] = 'module';
        $dat[2]['width'] = '63%';
        $dat[3]['title'] = 'old version';
        $dat[3]['width'] = '10%';
        $dat[4]['title'] = 'version';
        $dat[4]['width'] = '10%';
        $dat[5]['title'] = 'action';
        $dat[5]['width'] = '10%';
        $dat[6]['title'] = 'info';
        $dat[6]['width'] = '5%';
        jrCore_page_table_header($dat);

        foreach ($_rt['modules'] as $name => $_inf) {
            $dat             = array();
            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/modules/{$name}/icon.png\" alt=\"{$name}\" width=\"32\">";
            $dat[1]['width'] = '2%';
            $dat[2]['title'] = $_mods[$name]['module_name'];
            $dat[3]['title'] = $_inf['old_version'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_mods[$name]['module_version'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_inf['action'];
            $dat[5]['class'] = 'center';
            $url             = jrCore_get_module_url($name);
            $dat[6]['title'] = jrCore_page_button($name, 'info', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/admin/info')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }

    if (isset($_rt['skins']) && is_array($_rt['skins']) && count($_rt['skins']) > 0) {

        $dat             = array();
        $dat[1]['title'] = '';
        $dat[1]['width'] = '2%';
        $dat[2]['title'] = 'skin';
        $dat[2]['width'] = '63%';
        $dat[3]['title'] = 'old version';
        $dat[3]['width'] = '10%';
        $dat[4]['title'] = 'version';
        $dat[4]['width'] = '10%';
        $dat[5]['title'] = 'action';
        $dat[5]['width'] = '10%';
        $dat[6]['title'] = 'info';
        $dat[6]['width'] = '5%';
        jrCore_page_table_header($dat);

        $url = jrCore_get_module_url('jrCore');
        foreach ($_rt['skins'] as $name => $_inf) {
            $_mta            = jrCore_skin_meta_data($name);
            $dat             = array();
            $dat[1]['title'] = "<img src=\"{$_conf['jrCore_base_url']}/skins/{$name}/icon.png\" alt=\"{$name}\" width=\"32\">";
            $dat[1]['width'] = '2%';
            $dat[2]['title'] = $_mta['name'];
            $dat[3]['title'] = $_inf['old_version'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_mta['version'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = $_inf['action'];
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrCore_page_button($name, 'info', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/skin_admin/info/skin={$name}')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }

    if (strpos(jrCore_get_local_referrer(), 'history')) {
        jrCore_page_cancel_button('referrer', 'cancel');
    }
    else {
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse", 'continue');
    }
    jrCore_page_display();
}

//------------------------------
// systems
//------------------------------
function view_jrMarket_systems($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'tools');
    jrCore_page_banner('Marketplaces');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'marketplace system';
    $dat[1]['width'] = '50%';
    $dat[2]['title'] = 'credentials';
    $dat[2]['width'] = '35%';
    $dat[3]['title'] = 'active';
    $dat[3]['width'] = '5%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    $tbl = jrCore_db_table_name('jrMarket', 'system');
    $req = "SELECT * FROM {$tbl} ORDER BY system_id ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt) && is_array($_rt)) {
        foreach ($_rt as $k => $_sys) {
            $dat             = array();
            $dat[1]['title'] = "{$_sys['system_name']}<br>{$_sys['system_url']}";
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = "{$_sys['system_email']}<br>{$_sys['system_code']}";
            $dat[2]['class'] = 'center';
            if ($_sys['system_active'] == 'on') {
                $dat[3]['title'] = $pass;
            }
            else {
                $dat[3]['title'] = $fail;
            }
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("m{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/release_system_update/id={$_sys['system_id']}')");
            if ($_sys['system_id'] > 1) {
                $dat[5]['title'] = jrCore_page_button("d{$k}", 'delete', "if (confirm('Are you sure you delete this system?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_delete_save/id={$_sys['system_id']}') }");
            }
            else {
                $dat[5]['title'] = jrCore_page_button("d{$k}", 'delete', 'disabled');
            }
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No Marketplace systems found - add a new System below</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value'     => 'add marketplace system',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // System Name
    $_tmp = array(
        'name'     => 'system_name',
        'label'    => 'Marketplace Name',
        'help'     => 'Enter a unique Marketplace Name for this new Marketplace system',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true,
        'section'  => 'add a new marketplace'
    );
    jrCore_form_field_create($_tmp);

    // System URL
    $_tmp = array(
        'name'     => 'system_url',
        'label'    => 'Marketplace URL',
        'help'     => 'Enter the Marketplace URL for this new Marketplace system',
        'type'     => 'text',
        'validate' => 'url',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // System Email
    $_tmp = array(
        'name'     => 'system_email',
        'label'    => 'Marketplace Email',
        'help'     => 'Enter the Email Address used for your Account on this new Marketplace system',
        'type'     => 'text',
        'validate' => 'email',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // System Code
    $_tmp = array(
        'name'     => 'system_code',
        'label'    => 'Marketplace System ID',
        'help'     => 'Enter the Unique System ID from your Account on this new Marketplace system',
        'type'     => 'text',
        'validate' => 'md5',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// systems_save
//------------------------------
function view_jrMarket_systems_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    // Make sure this does not already exist
    $url = jrCore_db_escape(trim($_post['system_url']));
    $tbl = jrCore_db_table_name('jrMarket', 'system');
    $req = "SELECT * FROM {$tbl} WHERE system_url = '{$url}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (is_array($_rt)) {
        jrCore_set_form_notice('error', 'A system using that URL is already configured!');
        jrCore_form_result();
    }
    $nam = jrCore_db_escape($_post['system_name']);
    $eml = jrCore_db_escape($_post['system_email']);
    $cod = jrCore_db_escape($_post['system_code']);
    $req = "INSERT INTO {$tbl} (system_name, system_url, system_email, system_code, system_active) VALUES ('{$nam}', '{$url}', '{$eml}', '{$cod}', 'on')";
    $sid = jrCore_db_query($req, 'INSERT_ID');
    if (isset($sid) && jrCore_checktype($sid, 'number_nz')) {

        // Add in default stable/beta channels for this new system
        $_chan = array(
            'stable' => 1,
            'beta'   => 0
        );
        $tbl   = jrCore_db_table_name('jrMarket', 'channel');
        foreach ($_chan as $chn => $id) {
            $req = "INSERT INTO {$tbl} (channel_system_id, channel_created, channel_name, channel_active, channel_code) VALUES ('{$sid}', UNIX_TIMESTAMP(), '{$chn}', '{$id}', '') ON DUPLICATE KEY UPDATE channel_created = UNIX_TIMESTAMP()";
            jrCore_db_query($req);
        }
        jrCore_set_form_notice('success', "The new System has been successfully created");
        jrCore_form_delete_session();
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered trying to create the System - please try again');
    }
    jrCore_form_result('referrer');
}

//------------------------------
// set_active_system
//------------------------------
function view_jrMarket_set_active_system($_post, $_user, $_conf)
{
    jrUser_master_only();
    // Make sure this is a valid release system
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid system_id - please try again');
        jrCore_location('referrer');
    }
    jrMarket_set_active_release_system($_post['id']);
    jrCore_location('referrer');
}

//------------------------------
// release_system_update
//------------------------------
function view_jrMarket_release_system_update($_post, $_user, $_conf)
{
    jrUser_master_only();
    // Make sure this is a valid release system
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid system_id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrMarket', 'system');
    $req = "SELECT * FROM {$tbl} WHERE system_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid system_id - not found in DB - please try again');
        jrCore_form_result();
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'tools');
    jrCore_page_banner("update marketplace");
    jrCore_set_form_notice('success', 'Your <b>Marketplace Email</b> and <b>Marketplace System ID</b> can be found by logging into<br>your <a href="' . $_rt['system_url'] . '/user/login" target="_blank"><u>' . $_rt['system_name'] . ' User Account</u></a> and viewing your <a href="' . $_rt['system_url'] . '/networklicense/licenses" target="_blank"><u>Licenses Section</u></a>.<br>If you do not have an account, <a href="' . $_rt['system_url'] . '/user/signup" target="_blank"><u>Click here to create a new account</u></a>.', false);
    jrCore_get_form_notice();

    if ($_post['id'] == 1) {
        if (empty($_rt['system_email'])) {
            jrCore_form_field_hilight('system_email');
        }
        if (empty($_rt['system_code'])) {
            jrCore_form_field_hilight('system_code');
        }
    }

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/systems",
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // System ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'validate' => 'number_nz',
        'value'    => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // System Name
    $_tmp = array(
        'name'     => 'system_name',
        'label'    => 'Marketplace Name',
        'help'     => 'This is the unique Marketplace Name for this Marketplace system.',
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // System URL
    $_tmp = array(
        'name'     => 'system_url',
        'label'    => 'Marketplace URL',
        'help'     => 'This is the Marketplace URL for this Marketplace system - it is where new modules and skins can be installed from.',
        'type'     => 'text',
        'validate' => 'url',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // System Email
    $_tmp = array(
        'name'     => 'system_email',
        'label'    => 'Marketplace Email',
        'help'     => "This is the Email Address that is used on your account on this Marketplace: <strong>{$_rt['system_name']}</strong>",
        'type'     => 'text',
        'validate' => 'email',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // System Code
    $_tmp = array(
        'name'     => 'system_code',
        'label'    => 'Marketplace System ID',
        'help'     => "This is the unique System ID that can be found in your &quot;Licenses&quot; section of your account on this Marketplace: <strong>{$_rt['system_name']}</strong>",
        'type'     => 'text',
        'validate' => 'md5',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // System Active
    $_tmp = array(
        'name'     => 'system_active',
        'label'    => 'Active',
        'help'     => 'Check this option to mark this Release System as active - it will appear as a selection in the Marketplace browser',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'on',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // System Default
    $_tmp = array(
        'name'     => 'system_default',
        'label'    => 'Default',
        'help'     => 'Check this option to mark this Release System as the default Marketplace - when loading the Marketplace Browser it will be the default Marketplace selected.',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// release_system_update_save
//------------------------------
function view_jrMarket_release_system_update_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    // Make sure this does not already exist
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid system_id - please try again');
        jrCore_form_result();
    }
    $tbl = jrCore_db_table_name('jrMarket', 'system');
    $req = "SELECT * FROM {$tbl} WHERE system_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid system_id - not found in DB - please try again');
        jrCore_form_result();
    }
    // Update
    $url = jrCore_db_escape($_post['system_url']);
    $nam = jrCore_db_escape($_post['system_name']);
    $eml = jrCore_db_escape($_post['system_email']);
    $cod = jrCore_db_escape($_post['system_code']);
    $req = "UPDATE {$tbl} SET system_name = '{$nam}', system_url = '{$url}', system_email = '{$eml}', system_code = '{$cod}', system_active = '{$_post['system_active']}', system_default = '{$_post['system_default']}' WHERE system_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        // Make sure any other system is no longer the default if changed
        if ($_post['system_default'] == 'on') {
            $req = "UPDATE {$tbl} SET system_default = 'off' WHERE system_id != '{$_post['id']}'";
            jrCore_db_query($req);
        }
        unset($_SESSION['JRMARKET_RELEASE_SYSTEM'], $_SESSION['JRMARKET_INFO']);
        jrCore_delete_all_cache_entries();
        jrCore_set_form_notice('success', "The System has been successfully updated");
        jrCore_form_delete_session();
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered updating the System - please try again');
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// system_delete_save
//------------------------------
function view_jrMarket_system_delete_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // Make sure this does not already exist
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid system_id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrMarket', 'system');
    $req = "DELETE FROM {$tbl} WHERE system_id = '{$_post['id']}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt > 0) {
        // Delete any channels associated with this system
        $tbl = jrCore_db_table_name('jrMarket', 'channel');
        $req = "DELETE FROM {$tbl} WHERE channel_system_id = '{$_post['id']}'";
        jrCore_db_query($req);
        jrCore_set_form_notice('success', "The System has been successfully deleted");
        unset($_SESSION['JRMARKET_RELEASE_SYSTEM']);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the System - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// release_channels
//------------------------------
function view_jrMarket_release_channels($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMarket', 'tools');
    jrCore_page_banner('release channels');
    jrCore_get_form_notice();

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    $tbl = jrCore_db_table_name('jrMarket', 'channel');
    $tbs = jrCore_db_table_name('jrMarket', 'system');
    $req = "SELECT c.*, s.system_name FROM {$tbl} c LEFT JOIN {$tbs} s ON s.system_id = c.channel_system_id ORDER BY s.system_name ASC, c.channel_id ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt) && is_array($_rt)) {
        $_dn = array();
        foreach ($_rt as $k => $_chan) {

            if (!isset($_dn["{$_chan['channel_system_id']}"])) {
                if (count($_dn) > 0) {
                    jrCore_page_table_footer();
                }

                jrCore_page_section_header($_chan['system_name']);
                $dat             = array();
                $dat[1]['title'] = 'channel name';
                $dat[1]['width'] = '50%';
                $dat[2]['title'] = 'type';
                $dat[2]['width'] = '20%';
                $dat[3]['title'] = 'status';
                $dat[3]['width'] = '20%';
                $dat[4]['title'] = 'action';
                $dat[4]['width'] = '10%';
                jrCore_page_table_header($dat);
                $_dn["{$_chan['channel_system_id']}"] = 1;
            }

            $dat = array();
            if ($_chan['channel_name'] == 'stable' || $_chan['channel_name'] == 'beta') {
                $dat[1]['title'] = '<b>' . strtoupper($_chan['channel_name']) . '</b>';
                $dat[2]['title'] = 'public';
            }
            else {
                $dat[1]['title'] = '<b>' . $_chan['channel_name'] . '</b>';
                $dat[2]['title'] = 'private';
            }
            $dat[1]['class'] = 'center';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = (isset($_chan['channel_active']) && $_chan['channel_active'] == '1') ? $pass : $fail;
            $dat[3]['class'] = 'center';
            if ($_chan['channel_active'] == '1') {
                $dat[4]['title'] = jrCore_page_button("a{$k}", 'De-Activate', "if (confirm('Are you sure you no longer wish to subscribe to this channel?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/channel_active/id={$_chan['channel_id']}/status=0') }");
            }
            else {
                $dat[4]['title'] = jrCore_page_button("a{$k}", 'Make Active', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/channel_active/id={$_chan['channel_id']}/status=1')");
            }
            $dat[4]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = 'channel name';
        $dat[1]['width'] = '50%';
        $dat[2]['title'] = 'type';
        $dat[2]['width'] = '20%';
        $dat[3]['title'] = 'status';
        $dat[3]['width'] = '20%';
        $dat[4]['title'] = 'action';
        $dat[4]['width'] = '10%';
        jrCore_page_table_header($dat);

        $dat             = array();
        $dat[1]['title'] = '<p>No channels found</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    jrCore_set_form_notice('notice', 'If you have received a private channel invitation, enter the invite code below');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'add channel',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Channel System ID
    $_opt = jrMarket_get_active_systems();
    if ($_opt && is_array($_opt)) {
        if (count($_opt) > 1) {
            $_tmp = array(
                'name'     => 'channel_system_id',
                'label'    => 'Channel System',
                'help'     => 'Select the Marketplace System you want to add this channel for',
                'type'     => 'select',
                'options'  => $_opt,
                'validate' => 'number_nz',
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }
        else {
            $_tmp = array(
                'name'  => 'channel_system_id',
                'type'  => 'hidden',
                'value' => reset(array_keys($_opt))
            );
            jrCore_form_field_create($_tmp);
        }
    }

    // Channel Name
    $_tmp = array(
        'name'     => 'channel_code',
        'label'    => 'Channel Invite Code',
        'help'     => 'Enter the Channel Invite Code you received by email to subscribe to the private channel.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// release_channels_save
//------------------------------
function view_jrMarket_release_channels_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    // Validate Channel
    $_mk = jrMarket_set_active_release_system($_post['channel_system_id']);
    $_ps = array(
        'code'  => $_post['channel_code'],
        'host'  => jrCore_url_encode_string($_conf['jrCore_base_url']),
        'email' => $_mk['system_email']
    );
    $_rs = jrCore_load_url("{$_mk['system_url']}/networkmarket/validate_channel", $_ps, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (isset($_rs) && strpos($_rs, '{') === 0) {
        $_rs = json_decode($_rs, true);
        if (isset($_rs['error'])) {
            jrCore_set_form_notice('error', $_rs['error']);
            jrCore_form_result();
        }
    }
    $sid = intval($_post['channel_system_id']);
    $tbl = jrCore_db_table_name('jrMarket', 'channel');
    $req = "INSERT INTO {$tbl} (channel_system_id, channel_created, channel_name, channel_active, channel_code)
            VALUES ('{$sid}', UNIX_TIMESTAMP(), '" . jrCore_db_escape($_rs['channel_name']) . "', 1, '" . jrCore_db_escape($_post['channel_code']) . "')";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', "You have successfully subscribed to the &quot;" . strip_tags($_rs['channel_name']) . "&quot; channel");
        jrCore_form_delete_session();
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered trying to create the channel subscription - please try again');
    }
    jrCore_form_result('referrer');
}

//------------------------------
// channel_active
//------------------------------
function view_jrMarket_channel_active($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid channel_id - please try again');
        jrCore_location('referrer');
    }
    if (!isset($_post['status']) || ($_post['status'] != '1' && $_post['status'] != '0')) {
        jrCore_set_form_notice('error', 'invalid channel status - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrMarket', 'channel');
    $req = "UPDATE {$tbl} SET channel_active = '{$_post['status']}' WHERE channel_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'Successfully modified the channel status');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to update the channel status - please try again');
    }
    jrCore_location('referrer');
}
