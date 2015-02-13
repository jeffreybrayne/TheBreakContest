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
 * @copyright 2014 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * help tips
 */
function jrMarket_tips($_post, $_user, $_conf)
{
    $_out = array();
    $murl = jrCore_get_module_url('jrMarket');
    $_tmp = jrMarket_get_active_release_system();
    if (!$_tmp || !is_array($_tmp) || !jrCore_checktype($_tmp['system_email'], 'email')) {
        $_out[] = array(
            'view'       => "{$murl}/browse",
            'selector'   => '#admin_container',
            'title'      => 'The Marketplace',
            'text'       => 'The Marketplace is your one-stop-shop for all Jamroom related modules and skins.  You can easily install items, as well as keep your existing items up to date.<br><br>To get started, let\'s make sure you are setup properly.',
            'position'   => 'top center',
            'button'     => 'Continue',
            'button_url' => "{$_conf['jrCore_base_url']}/{$murl}/release_system_update/id=1/hl[]=system_email/hl[]=system_code",
            'pointer'    => false
        );
        $_out[] = array(
            'view'     => "{$murl}/release_system_update/id=1",
            'selector' => '#admin_container',
            'title'    => 'Marketplace Email and ID',
            'text'     => 'Your Marketplace Email and System ID are used to keep your system up to date and can be found in the <strong>Licenses</strong> section of your Jamroom.net account:<br><br><a href="http://www.jamroom.net/networklicense/licenses">View your Marketplace Settings</a><br><br>Make sure the your Marketplace Email and System ID are correct in the highlighted fields.',
            'position' => 'top center',
            'button'   => 'Close',
            'pointer'  => false
        );
    }
    if (!empty($_tmp['system_email'])) {
        $_out[] = array(
            'view'        => "{$murl}/browse",
            'selector'    => '#tmodule',
            'title'       => 'Installing Modules',
            'text'        => 'Installing modules is easy - simply click on the <strong>Install</strong> button in the marketplace.<br><br>Free modules are installed immediately, and paid modules can be purchased and installed without leaving your site.',
            'position'    => 'bottom center',
            'my_position' => 'top left',
            'button'      => 'Continue',
            'youtube_id'  => 'Fgng1i3dpXs'
        );
        $_out[] = array(
            'view'        => "{$murl}/browse",
            'selector'    => '#tskin',
            'title'       => 'Installing Skins',
            'text'        => 'Installing skins is the same - simply click install or purchase, and the skin is downloaded and installed immediately.',
            'position'    => 'bottom center',
            'my_position' => 'top left',
            'button'      => 'Continue'
        );
        $_out[] = array(
            'view'        => "{$murl}/browse",
            'selector'    => '#tbundle',
            'title'       => 'Save with Bundles',
            'text'        => 'Bundles are collections of modules and/or skins that can be purchased at significant savings over purchasing the items individually.<br><br>Some bundles are <strong>Free</strong> and can be installed just like a module or skin.',
            'position'    => 'bottom center',
            'my_position' => 'top left',
            'button'      => 'Continue'
        );
        $_out[] = array(
            'view'        => "{$murl}/browse",
            'selector'    => '#tsystem_update',
            'title'       => 'Staying up to Date',
            'text'        => 'Keeping your system up to date is just as easy - click on the <strong>System Updates</strong> tab at any time to see if there are new updates available for the modules and skins installed on your site.',
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'button'      => 'Continue'
        );
        $_out[] = array(
            'view'     => "{$murl}/browse",
            'selector' => '#admin_container',
            'title'    => 'Easy to Use',
            'text'     => 'The Marketplace is designed to easily install new modules and skins, as well as keep your system up to date - we hope you find it convenient and fun to use.<br><br>Thank you for using Jamroom!',
            'position' => 'top center',
            'button'   => 'Close',
            'pointer'  => false
        );
    }
    return $_out;
}
