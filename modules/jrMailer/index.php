<?php
/**
 * Jamroom 5 Email Support module
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
// test email
//------------------------------
function view_jrMailer_test_email($_post,$_user,$_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMailer', 'test_email');
    jrCore_page_banner('Send a Test Email');

    // Form init
    $_tmp = array(
        'submit_value' => 'send test email',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools"
    );
    jrCore_form_create($_tmp);

    // Email Address
    $_tmp = array(
        'name'      => 'email',
        'label'     => 'email address',
        'help'      => 'Enter a valid email address you would like to send a test message to',
        'type'      => 'text',
        'validate'  => 'email',
        'default'   => $_user['user_email'],
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Email Subject
    $_tmp = array(
        'name'      => 'subject',
        'label'     => 'email subject',
        'help'      => 'Enter a subject for the test email',
        'type'      => 'text',
        'validate'  => 'not_empty',
        'default'   => 'this is a test email subject',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Email Message
    $_tmp = array(
        'name'      => 'message',
        'label'     => 'email message',
        'help'      => 'Enter a message for the test email',
        'type'      => 'textarea',
        'validate'  => 'not_empty',
        'default'   => 'this is the test email message',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// test email_save
//------------------------------
function view_jrMailer_test_email_save($_post,$_user,$_conf)
{
    jrUser_session_require_login();
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (jrCore_send_email($_post['email'], $_post['subject'], $_post['message'])) {
        jrCore_set_form_notice('success', 'The test email was successfully sent');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered sending the test email - check activity log');
    }
    jrCore_form_result();
}
