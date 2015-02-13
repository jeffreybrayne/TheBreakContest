<?php
/**
 * Jamroom 5 Support Center module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrSupport_config()
{
    // Marketplace Email
    $_tmp = array(
        'name'     => 'support_email',
        'default'  => '',
        'type'     => 'text',
        'required' => 'on',
        'validate' => 'email',
        'label'    => 'support email',
        'help'     => 'Enter your Jamroom Network user email address - this enables the Support Center to show you all available support options for your account.',
        'order'    => 1
    );
    jrCore_register_setting('jrSupport', $_tmp);

    // Support URL
    $_tmp = array(
        'name'     => 'support_url',
        'default'  => 'http://www.jamroom.net',
        'type'     => 'text',
        'required' => 'on',
        'validate' => 'url',
        'label'    => 'Support URL',
        'help'     => 'This is the URL the system will use to search for support documentation - do not change unless you have been told to change this value.',
        'order'    => 2
    );
    jrCore_register_setting('jrSupport', $_tmp);
    return true;
}
