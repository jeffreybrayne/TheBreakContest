<?php
/**
 * Jamroom 5 User Profiles module
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
 * quota_config
 */
function jrProfile_quota_config()
{
    // Quota Name
    $_tmp = array(
        'name'     => 'name',
        'type'     => 'text',
        'validate' => 'printable',
        'label'    => 'quota name',
        'help'     => 'What name would you like to use for this quota?',
        'default'  => '',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrProfile', $_tmp);

    // Quota Admin Note
    $_tmp = array(
        'name'     => 'admin_note',
        'type'     => 'textarea',
        'validate' => 'printable',
        'label'    => 'quota admin note',
        'help'     => 'You can save a note about this quota',
        'default'  => '',
        'order'    => 2
    );
    jrProfile_register_quota_setting('jrProfile', $_tmp);

    // Default Profile Privacy
    $_tmp = array(
        'name'     => 'default_privacy',
        'type'     => 'select',
        'options'  => 'jrProfile_get_privacy_options',
        'validate' => 'number_nn',
        'label'    => 'default profile privacy',
        'help'     => 'Select the default profile privacy option - new profiles created in this Quota will have their profile privacy set to this value',
        'default'  => 1,
        'min'      => 0,
        'max'      => 2,
        'order'    => 3
    );
    jrProfile_register_quota_setting('jrProfile', $_tmp);

    // Allow Privacy Changes
    $_tmp = array(
        'name'     => 'privacy_changes',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'show privacy options',
        'help'     => 'If this option is checked, Profiles in this quota will be allowed to change their Profile Privacy.  If unchecked, the Default Profile Privacy will be used instead.',
        'default'  => 'on',
        'order'    => 4
    );
    jrProfile_register_quota_setting('jrProfile', $_tmp);

    // Profile Count
    $_tmp = array(
        'name'     => 'profile_count',
        'type'     => 'hidden',
        'validate' => 'number_nz',
        'label'    => 'profile count',
        'help'     => '@internal tracks number of profiles in quota',
        'default'  => '0'
    );
    jrProfile_register_quota_setting('jrProfile', $_tmp);
    return true;
}
