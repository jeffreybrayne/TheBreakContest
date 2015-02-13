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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrMarket_config()
{
    // Check for updates
    $_tmp = array(
        'name'     => 'update_check',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'check for updates',
        'help'     => 'During daily maintenance the system will check if there are available Marketplace updates - if there are, an email will be sent to master admins letting them know updates are available.',
        'order'    => 2
    );
    jrCore_register_setting('jrMarket', $_tmp);

    // Quick Purchases
    $_tmp = array(
        'name'     => 'quick_purchase',
        'default'  => 'on',
        'type'     => 'checkbox',
        'required' => 'on',
        'validate' => 'onoff',
        'label'    => 'quick purchase',
        'sublabel' => 'uncheck to force new credit card',
        'help'     => 'Once a purchase has been made through the Marketplace, future purchases can bypass the Credit Card checkout step and instead use the same information from your last purchase.  Uncheck this option to require a full checkout for each purchase.',
        'order'    => 4
    );
    jrCore_register_setting('jrMarket', $_tmp);

    // FTP Host
    $_tmp = array(
        'name'     => 'ftp_host',
        'default'  => 'localhost',
        'type'     => 'text',
        'required' => 'off',
        'validate' => 'printable',
        'label'    => 'FTP Host',
        'help'     => 'If the system is unable to write directly to the module and skin directories, you can enter your FTP Information here and the system will attempt to install items via FTP.<br><br><b>NOTE:</b> If you are not sure of the hostname for your FTP server, you may need to contact your hosting provider for assistance.',
        'order'    => 10,
        'section'  => 'FTP settings'
    );
    jrCore_register_setting('jrMarket', $_tmp);

    // FTP Port
    $_tmp = array(
        'name'     => 'ftp_port',
        'default'  => '21',
        'type'     => 'text',
        'required' => 'off',
        'validate' => 'number_nz',
        'label'    => 'FTP Port',
        'help'     => 'If you are using FTP to install modules and skins, enter the FTP Port here. FTP servers normally run on port 21.<br><br><b>NOTE:</b> If you are not sure of the port number for your FTP server, you may need to contact your hosting provider for assistance.',
        'order'    => 11
    );
    jrCore_register_setting('jrMarket', $_tmp);

    // FTP User
    $_tmp = array(
        'name'     => 'ftp_user',
        'default'  => '',
        'type'     => 'text',
        'required' => 'off',
        'validate' => 'printable',
        'label'    => 'FTP User',
        'help'     => 'If you are using FTP to install modules and skins, enter the FTP Username',
        'order'    => 11
    );
    jrCore_register_setting('jrMarket', $_tmp);

    // FTP Password
    $_tmp = array(
        'name'     => 'ftp_pass',
        'default'  => '',
        'type'     => 'password',
        'required' => 'off',
        'validate' => 'printable',
        'label'    => 'FTP Password',
        'help'     => 'If you are using FTP to install modules and skins, enter the FTP Password',
        'order'    => 12
    );
    jrCore_register_setting('jrMarket', $_tmp);

    return true;
}

/**
 * config_display
 */
function jrMarket_config_display($_post, $_user, $_conf)
{
    // FTP Check - make sure we have FTP information if we need it
    if (isset($_conf['jrMarket_ftp_user']) && strlen($_conf['jrMarket_ftp_user']) > 0) {
        return true;
    }
    // Make sure FTP Functions are installed...
    if (!function_exists('ftp_rename')) {
        jrCore_set_form_notice('error', 'FTP Functions are not installed in your PHP, but are required to install modules and skins.<br>Contact your hosting provider and have them install FTP functions in your PHP.');
        return false;
    }
    $_dirs = array();
    // Check to see if our directories are writable
    if (!is_writable(APP_DIR ."/modules")) {
        $_dirs[] = APP_DIR .'/modules';
    }
    if (!is_writable(APP_DIR ."/skins")) {
        $_dirs[] = APP_DIR .'/skins';
    }
    if (count($_dirs) > 0) {
        jrCore_set_form_notice('error', 'The system is unable to write to the following directories:<br><br>'. implode('<br>', $_dirs) .'<br><br>Enter your FTP details below so the system can install modules and skins.', false);
        jrCore_form_field_hilight('ftp_host');
        jrCore_form_field_hilight('ftp_user');
        jrCore_form_field_hilight('ftp_pass');
        return false;
    }
    return true;
}

/**
 * config_validate
 */
function jrMarket_config_validate($_post)
{
    // Validate FTP Settings
    if (isset($_post['ftp_host']{1}) && isset($_post['ftp_user']) && strlen($_post['ftp_user']) > 0 && isset($_post['ftp_pass']) && strlen($_post['ftp_pass']) > 0) {
        if (!jrMarket_ftp_connect($_post['ftp_host'], $_post['ftp_port'], $_post['ftp_user'], $_post['ftp_pass'])) {
            jrCore_set_form_notice('error', 'Unable to log in via FTP using the settings provided - please double check the FTP settings');
            return false;
        }
    }
    return $_post;
}
