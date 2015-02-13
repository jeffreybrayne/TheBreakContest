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

/**
 * meta
 */
function jrPage_meta()
{
    $_tmp = array(
        'name'        => 'Page Creator',
        'url'         => 'page',
        'version'     => '1.1.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create new pages for your site',
        'category'    => 'site',
        'requires'    => 'jrCore:5.1.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrPage_init()
{
    global $_post;
    // We have some small custom CSS for our page
    jrCore_register_module_feature('jrCore', 'css', 'jrPage', 'jrPage.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrPage', 'jquery.sortable.min.js');

    // Let the master admins create pages on the back end
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPage', 'create', array('create a new page', 'create a new page for your site'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrPage', 'browse', array('browse existing pages', 'browse existing site pages'));

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrPage', 'off');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrPage', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrPage', 'on');

    jrCore_register_module_feature('jrCore', 'action_support', 'jrPage', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrPage', 'update', 'item_action.tpl');

    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrPage', 'on');

    // Our default view for admins
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrPage', 'admin/tools');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrPage', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrPage', 'update');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrPage', 'profile_jrPage_item_count', 19);

    // display_order listener
    jrCore_register_event_listener('jrCore', 'display_order', 'jrPage_display_order_listener');

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Remove "site" level pages from profile page display order
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPage_display_order_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['_params']['module']) && $_data['_params']['module'] == 'jrPage' && isset($_data['_items'])) {
        foreach ($_data['_items'] as $k => $v) {
            if (isset($v['page_location']) && $v['page_location'] == '0') {
                // This page is a MAIN site page - unset
                unset($_data['_items'][$k]);
            }
        }
    }
    return $_data;
}
