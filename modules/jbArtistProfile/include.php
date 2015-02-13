<?php
/**
 * Jamroom 5 ArtistProfile module
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
 * meta
 */
function jbArtistProfile_meta()
{
    $_tmp = array(
        'name'        => 'ArtistProfile',
        'url'         => 'artistprofile',
        'version'     => '1.0.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'A module created by ' . strrev('anrapA'),
        'license'     => 'mpl',
        'category'    => 'tools'
    );
    return $_tmp;
}

/**
 * init
 */
function jbArtistProfile_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jbArtistProfile', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jbArtistProfile', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jbArtistProfile', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jbArtistProfile', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jbArtistProfile', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jbArtistProfile', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jbArtistProfile', 'update', 'item_action.tpl');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jbArtistProfile', 'artistprofile_title', 1);

    // Integrate with payment system
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'jbArtistProfile_my_items_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'jbArtistProfile_adding_item_to_purchase_history_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'jbArtistProfile_my_earnings_row_listener');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jbArtistProfile', 'profile_jbArtistProfile_item_count', 1);

    return true;
}

/**
 * Get information about an item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_artistprofile()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jbArtistProfile_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jbArtistProfile') {
        $url = jrCore_get_module_url('jbArtistProfile');

        $_data[2]['title'] = $_args['artistprofile_title'];
        $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/artistprofile_file/{$_args['_item_id']}')");
    }
    return $_data;
}

/**
 * display the sale info to the seller of the item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jbArtistProfile_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jbArtistProfile') {
        $_data[1]['title'] = $_args['artistprofile_title'];
    }
    return $_data;
}

/**
 * fired when foxycart sends a confirmed order in the background back to our system.
 * If there are files in the order, those files need to be kept in the system vault
 * so they can be downloaded.  do that moving here.
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jbArtistProfile_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jbArtistProfile') {
        // a file has been sold, copy it to our system vault.
        // Make sure file is copied over to system vault
        $nam = jrCore_get_media_file_path($_args['module'], $_args['product_field'], $_data);
        if (!isset($nam) || !is_file($nam)) {
            // BAD FILE!
            jrCore_logger('CRI', "transaction received with no valid media file: {$_args['txn']['txn_id']}");
            return $_data;
        }
        $dir = APP_DIR . '/data/media/vault';
        $fil = $dir . '/' . basename($nam);
        if (!is_file($fil)) {
            if (!copy($nam, $fil)) {
                jrCore_logger('CRI', "unable to copy sold media file to system vault: {$_args['txn']['txn_id']}");
                return $_data;
            }
        }
    }
    return $_data;
}
