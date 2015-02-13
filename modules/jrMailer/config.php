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
 * config
 */
function jrMailer_config()
{
    // Active Email System
    $_tmp = array(
        'name'     => 'active_email_system',
        'default'  => 'jrMailer_smtp',
        'type'     => 'select',
        'options'  => 'jrCore_get_email_system_plugins',
        'validate' => 'core_string',
        'required' => 'on',
        'label'    => 'active email system',
        'help'     => 'What Email system should be the active email system?',
        'section'  => 'general settings',
        'order'    => 1
    );
    jrCore_register_setting('jrMailer', $_tmp);

    // From Address
    $_tmp = array(
        'name'     => 'from_email',
        'label'    => 'from email address',
        'default'  => (isset($_SERVER['SERVER_ADMIN']) && strpos($_SERVER['SERVER_ADMIN'], '@')) ? $_SERVER['SERVER_ADMIN'] : 'changeme@example.com',
        'type'     => 'text',
        'validate' => 'email',
        'help'     => 'When the system sends an automated / system message, what email address should the email be sent from? Note that this should be a real email address that will be checked with an email client.',
        'section'  => 'general settings',
        'order'    => 2
    );
    jrCore_register_setting('jrMailer', $_tmp);

    // Throttling
    $_opt = array(
        '0'   => 'disabled',
        '5'   => '5',
        '10'  => '10',
        '15'  => '15',
        '20'  => '20',
        '25'  => '25',
        '30'  => '30',
        '35'  => '35',
        '40'  => '40',
        '50'  => '50',
        '60'  => '60',
        '75'  => '75',
        '90'  => '90',
        '120' => '120',
        '150' => '150',
        '200' => '200'
    );
    $_tmp = array(
        'name'     => 'throttle',
        'label'    => 'max send rate',
        'sublabel' => '(per minute)',
        'default'  => '0',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nn',
        'help'     => 'What is the maximum number of emails that will be sent in any minute?<br><br><strong>NOTE:</strong> For large systems it is recommended that this setting be <strong>disabled</strong>. This setting uses database counters that may cause additional load on the system.',
        'section'  => 'general settings',
        'order'    => 3
    );
    jrCore_register_setting('jrMailer', $_tmp);

    $_trs = array(
        'mail' => 'PHP mail function (default)',
        'smtp' => 'External SMTP Server (configured below)'
    );
    // Delivery Method
    $_tmp = array(
        'name'    => 'transport',
        'label'   => 'delivery method',
        'type'    => 'select',
        'options' => $_trs,
        'default' => 'mail',
        'help'    => 'Select the delivery method you would like to use for outbound email.',
        'section' => 'delivery settings (Local Server SMTP only)',
        'order'   => 10
    );
    jrCore_register_setting('jrMailer', $_tmp);

    // SMTP server
    $_tmp = array(
        'name'     => 'smtp_host',
        'label'    => 'SMTP host',
        'type'     => 'text',
        'validate' => 'false',
        'default'  => 'localhost.com',
        'help'     => 'If you would like to use an external SMTP Server for sending email, enter the hostname or IP address here.<br><br><strong>NOTE:</strong> if you are setting up Gmail as your SMTP server, make sure you have allowed 3rd party applications to send by visiting the following URL:<br><br><a href="https://accounts.google.com/UnlockCaptcha" target="_blank">https://accounts.google.com/UnlockCaptcha</a>',
        'section' => 'delivery settings (Local Server SMTP only)',
        'order'    => 11
    );
    jrCore_register_setting('jrMailer', $_tmp);

    // SMTP port
    $_tmp = array(
        'name'     => 'smtp_port',
        'label'    => 'SMTP port number',
        'type'     => 'text',
        'validate' => 'number_nz',
        'default'  => '25',
        'help'     => 'If you have specified an SMTP host, enter the port that the SMTP server is running on.',
        'section' => 'delivery settings (Local Server SMTP only)',
        'order'    => 12
    );
    jrCore_register_setting('jrMailer', $_tmp);

    // SMTP user
    $_tmp = array(
        'name'     => 'smtp_user',
        'label'    => 'SMTP user name',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'false',
        'help'     => 'If you have specified an SMTP host, enter the user name that is used to connect to the SMTP server.',
        'section' => 'delivery settings (Local Server SMTP only)',
        'order'    => 13
    );
    jrCore_register_setting('jrMailer', $_tmp);

    // SMTP pass
    $_tmp = array(
        'name'     => 'smtp_pass',
        'label'    => 'SMTP password',
        'type'     => 'password',
        'default'  => '',
        'validate' => 'false',
        'help'     => 'If you have specified an SMTP user name, enter the password that is used to connect to the SMTP server.',
        'section' => 'delivery settings (Local Server SMTP only)',
        'order'    => 14
    );
    jrCore_register_setting('jrMailer', $_tmp);

    // Enabled SSL
    $_opt = array(
        'none' => 'no encryption',
        'ssl'  => 'SSL',
        'tls'  => 'TLS'
    );
    $_tmp = array(
        'name'     => 'smtp_encryption',
        'label'    => 'SMTP Encryption',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 'none',
        'validate' => 'not_empty',
        'help'     => 'If your SMTP server supports SSL or TLS, you can enable encryption by selecting the option your SMTP server supports',
        'section' => 'delivery settings (Local Server SMTP only)',
        'order'    => 15
    );
    jrCore_register_setting('jrMailer', $_tmp);

    return true;
}

/**
 * Validate Config settings
 */
function jrMailer_config_validate($_post)
{
    // If we are being configured for SSL/TLS, make sure we are supported
    switch ($_post['smtp_encryption']) {
        case 'ssl':
        case 'tls':
            if (function_exists('stream_get_transports')) {
                $found = false;
                $_tmp  = stream_get_transports();
                if (is_array($_tmp)) {
                    foreach ($_tmp as $transport) {
                        if ($transport == $_post['smtp_encryption']) {
                            $found = true;
                            break;
                        }
                    }
                }
                if (!$found) {
                    jrCore_set_form_notice('error', 'Your PHP does not appear to have OpenSSL support, which is required for SSL or TLS to function.<br>Contact your hosting provider to be sure OpenSSL support is enabled in your PHP', false);
                    return false;
                }
            }
            else {
                jrCore_set_form_notice('notice', 'Your PHP does not have the stream_get_transports() function enabled - unable to determine if your PHP supports SSL.<br>Email delivery over SMTP may not work if OpenSSL is not enabled in your PHP.', false);
                return false;
            }
            break;
    }
    return $_post;
}

?>
