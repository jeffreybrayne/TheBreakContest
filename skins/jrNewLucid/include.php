<?php
/**
 * Jamroom 5 jrNewLucid skin
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
 * Jamroom 5 Elastic skin
 * @copyright 2003 - 2014 by The Jamroom Network - All Rights Reserved
 * @author Brian Johnson - brian@jamroom.net
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * jrNewLucid_meta
 */
function jrNewLucid_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrNewLucid',
        'title'       => 'Lucid',
        'version'     => '1.0.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Lucid - a clean, easy to use skin for creating a Blogging community',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrNewLucid_init
 * NOTE: unlike with a module, init() is NOT called on each page load, but is
 * called when the core needs to rebuild CSS or Javascript for the skin
 */
function jrNewLucid_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'slidebar.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'index.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'tablet_core.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNewLucid', 'mobile_core.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrNewLucid', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrNewLucid', 'jrNewLucid.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrNewLucid', APP_DIR .'/skins/jrNewLucid/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrNewLucid', 'black');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrNewLucid', 30);
    // Hide module icons
    jrCore_register_module_feature('jrCore', 'module_icons', 'jrNewLucid', 'show', false);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNewLucid', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNewLucid', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNewLucid', 'jrPlaylist', 'jrPlaylist_player_dark');

    return true;
}
