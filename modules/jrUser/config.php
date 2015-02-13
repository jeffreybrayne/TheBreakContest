<?php
/**
 * Jamroom 5 User Accounts module
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
 * jrUser_config
 */
function jrUser_config()
{
    // Enable Signups
    $_tmp = array(
        'name'     => 'signup_on',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'User Signups',
        'help'     => 'Check this option to allow users to signup for your site.',
        'section'  => 'signup settings',
        'order'    => 1
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Signup Notification
    $_tmp = array(
        'name'     => 'signup_notify',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'Signup Notification',
        'help'     => 'If this option is checked the system will notify Admins when a new User Account is created.',
        'section'  => 'signup settings',
        'order'    => 2
    );
    jrCore_register_setting('jrUser', $_tmp);

    // authenticate
    $_tmp = array(
        'name'     => 'authenticate',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'Re-Authenticate',
        'help'     => 'If this option is checked, when a user attempts to change their <strong>email address</strong> or <strong>password</strong> they will have to enter their existing password to continue.',
        'section'  => 'user account settings',
        'order'    => 10
    );
    jrCore_register_setting('jrUser', $_tmp);

    // change notice
    $_tmp = array(
        'name'     => 'change_notice',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => 'email change notice',
        'help'     => 'If this option is checked, if a user changes their email address they will be sent a notification to their <strong>old</strong> email address letting them know that their address has been changed.',
        'section'  => 'user account settings',
        'order'    => 11
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Max Login Time
    $_tmp = array(
        'name'     => 'session_expire_min',
        'default'  => '360',
        'type'     => 'text',
        'validate' => 'number_nz',
        'required' => 'on',
        'min'      => 10,
        'max'      => 20160,
        'label'    => 'session expiration',
        'help'     => 'How many minutes of inactivity will cause a User session to be marked as expired?',
        'section'  => 'user account settings',
        'order'    => 13
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Auto Login
    $_als = array(
        '1'  => 'Every Login (auto login disabled)',
        '7'  => 'Every 7 days',
        '2'  => 'Every 14 days',
        '30' => 'Every 30 days',
        '60' => 'Every 60 days',
        '90' => 'Every 90 days',
        '3'  => 'Permanent (until user resets cookies)'
    );
    $_tmp = array(
        'name'     => 'autologin',
        'default'  => '2',
        'type'     => 'select',
        'options'  => $_als,
        'required' => 'on',
        'label'    => 'auto login reset',
        'help'     => 'How often should a user have to re-enter their login credentials? If the user does not visit the site for the number of days selected here, they will need to login again.',
        'section'  => 'user account settings',
        'order'    => 14
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Default Language
    $_tmp = array(
        'name'     => 'default_language',
        'default'  => 'en-US',
        'type'     => 'select',
        'options'  => 'jrUser_get_languages',
        'required' => 'on',
        'label'    => 'default language',
        'help'     => 'The Default language is the language that is setup for new user accounts by default.',
        'section'  => 'user account settings',
        'order'    => 15
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Enable SSL
    $_tmp = array(
        'name'     => 'force_ssl',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Create SSL URLs',
        'help'     => 'Checking this option will cause local non-SSL URLs that are embedded in text items to be shown as an SSL url for logged in users',
        'section'  => 'site settings',
        'order'    => 20
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Site privacy options
    $_priv = array(
        '1' => 'Public (all pages visible)',
        '2' => 'Limited (site index and log in / signup only)',
        '3' => 'Private (no pages visible)'
    );
    $_tmp = array(
        'name'     => 'site_privacy',
        'default'  => '1',
        'type'     => 'select',
        'options'  => $_priv,
        'required' => 'on',
        'label'    => 'Site Privacy',
        'help'     => 'Select which site pages visitors who are not logged in can see.<br><br><strong>NOTE:</strong> This setting only applies to users who are not logged in.',
        'section'  => 'site settings',
        'order'    => 21
    );
    jrCore_register_setting('jrUser', $_tmp);

    // Active Session System
    $_tmp = array(
        'name'     => 'active_session_system',
        'default'  => 'jrUser_mysql',
        'type'     => 'hidden',
        'required' => 'on',
        'validate' => 'not_empty',
        'label'    => 'active session system',
        'help'     => 'This hidden field holds the name of the active session sub system - do not modify by hand'
    );
    jrCore_register_setting('jrUser', $_tmp);

    return true;
}
