<?php
/**
 * Jamroom 5 jrElastic skin
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
 * Jamroom 5 Elastic skin
 * @copyright 2003 - 2014 by The Jamroom Network - All Rights Reserved
 * @author Brian Johnson - brian@jamroom.net
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * jrElastic_meta
 */
function jrElastic_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrElastic',
        'title'       => 'Elastic',
        'version'     => '1.2.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Elastic Skin for Jamroom 5 - clean and easy to expand',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrElastic_init
 * NOTE: unlike with a module, init() is NOT called on each page load, but is
 * called when the core needs to rebuild CSS or Javascript for the skin
 */
function jrElastic_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'override_tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic', 'override_mobile.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrElastic', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrElastic', 'jrElastic.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrElastic', APP_DIR .'/skins/jrElastic/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrElastic', 'black');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrElastic', 30);
    // Hide module icons
    jrCore_register_module_feature('jrCore', 'module_icons', 'jrElastic', 'show', false);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrElastic', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrElastic', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrElastic', 'jrPlaylist', 'jrPlaylist_player_dark');

    return true;
}
