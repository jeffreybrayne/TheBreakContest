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

/**
 * jrMailer_meta
 */
function jrMailer_meta()
{
    $_tmp = array(
        'name'        => 'Email Support',
        'url'         => 'mailer',
        'version'     => '1.2.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Core support for Sending Email via an SMTP Server',
        'category'    => 'communication',
        'priority'    => 1, // HIGHEST load priority
        'locked'      => true,
        'activate'    => true,
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrMailer_init
 */
function jrMailer_init()
{
    // Register our email plugin
    jrCore_register_system_plugin('jrMailer', 'email', 'smtp', 'Local Server SMTP (default)');

    // Test Email tab
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrMailer', 'test_email', 'Test Email');
    return true;
}

//-----------------------------------
// mailer module plugin function
//-----------------------------------

/**
 * @ignore
 * @param $_email_to mixed Email Addresses to send email to (single or array)
 * @param $_user array User info array
 * @param $_conf array Global Config
 * @param $_email_info array Extra email arguments
 * @return int
 */
function _jrMailer_smtp_send_email($_email_to, $_user, $_conf, $_email_info)
{
    // Bring in Swift Mailer
    require_once APP_DIR . '/modules/jrMailer/contrib/swiftmailer/swift_required.php';

    // $_email_to is an array containing all of the email addresses this message
    // is being sent to.
    //
    // $_email_info is an array of information about the email being sent, including:
    // required - 'subject'
    // required - 'message'
    // required - 'from'
    //
    // optional = 'from_name'
    // optional = 'priority'  (int 1 -> 5 = highest,high,normal,low,lowest)
    // optional = 'send_as_html' = true; Send as an HTML email
    //
    // Our module config also includes some items:
    // 'from' - specifies email address for bounces
    // 'return_email' - specifies email address for bounces

    // Init transport
    // See what type of transport we are using:
    // SMTP or Mail
    switch (strtolower($_conf['jrMailer_transport'])) {
        case 'smtp':
            if (function_exists('proc_open')) {
                if (isset($_conf['jrMailer_smtp_encryption']) && $_conf['jrMailer_smtp_encryption'] != 'none') {
                    $trs = Swift_SmtpTransport::newInstance($_conf['jrMailer_smtp_host'], intval($_conf['jrMailer_smtp_port']))
                        ->setUsername($_conf['jrMailer_smtp_user'])
                        ->setPassword($_conf['jrMailer_smtp_pass'])
                        ->setEncryption($_conf['jrMailer_smtp_encryption'])
                        ->setTimeout(5);
                }
                else {
                    $trs = Swift_SmtpTransport::newInstance($_conf['jrMailer_smtp_host'], intval($_conf['jrMailer_smtp_port']))
                        ->setUsername($_conf['jrMailer_smtp_user'])
                        ->setPassword($_conf['jrMailer_smtp_pass'])
                        ->setTimeout(5);
                }
            }
            else {
                if (!isset($GLOBALS['jrMailer_smtp_send_email_error'])) {
                    jrCore_logger('CRI', 'SMTP transport enabled but PHP proc_open function is disabled!');
                    $GLOBALS['jrMailer_smtp_send_email_error'] = 1;
                }
                $trs = Swift_MailTransport::newInstance();
            }
            break;
        default:
            $trs = Swift_MailTransport::newInstance();
            break;
    }

    // Create the message using the transport
    $mlr = Swift_Mailer::newInstance($trs);

    // Create a message
    $msg = Swift_Message::newInstance($_email_info['subject']);

    // Set From
    if (!isset($_email_info['from']) || !jrCore_checktype($_email_info['from'], 'email')) {
        if (isset($_SERVER['SERVER_ADMIN']) && jrCore_checktype($_SERVER['SERVER_ADMIN'], 'email')) {
            $_email_info['from'] = $_SERVER['SERVER_ADMIN'];
        }
        else {
            return 0;
        }
    }
    if (isset($_email_info['from_name']{0})) {
        $msg->setFrom(array($_email_info['from'] => $_email_info['from_name']));
    }
    else {
        $msg->setFrom(array($_email_info['from']));
    }
    // Check for return email
    if (isset($_conf['jrMailer_return_email']{0})) {
        $msg->setReturnPath($_conf['jrMailer_return_email']);
    }

    // Priority
    if (isset($_email_info['priority']) && jrCore_checktype($_email_info['priority'], 'number_nz')) {
        $msg->setPriority($_email_info['priority']);
    }

    // See if we have HTML in the body of the message - if we do, send as HTML OR
    if (strpos(' ' . trim($_email_info['message']), '<html') === 0) {
        $_email_info['send_as_html'] = true;
        if (strpos("\n", $_email_info['message'])) {
            $_email_info['message'] = nl2br($_email_info['message']);
        }
    }

    // Add Body of message (plain text)
    if (isset($_email_info['send_as_html']) && $_email_info['send_as_html'] !== false) {
        $msg->setBody($_email_info['message'], 'text/html');
        $msg->addPart(strip_tags($_email_info['message']), 'text/plain');
    }
    else {
        $msg->setBody(strip_tags($_email_info['message']));
    }

    // Send the message
    $bad = array();
    $num = 0;
    foreach ($_email_to as $address) {
        $msg->setTo($address);
        try {
            $num += $mlr->send($msg, $bad);
        }
        catch (Exception $e) {
            $_rp = array(
                'mailinfo' => $_email_info,
                'errormsg' => $e->getMessage()
            );
            jrCore_logger('CRI', 'Error sending email using configured transport', $_rp);
        }
    }
    return $num;
}
