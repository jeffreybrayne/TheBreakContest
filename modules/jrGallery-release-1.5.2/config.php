<?php
/**
 * Jamroom 5 Image Galleries module
 *
 * copyright 2003 - 2015
 * by The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
 * jrGallery_config
 *
 */
function jrGallery_config()
{
    // Adobe image manipulation developer key
    $_tmp = array(
        'name'     => 'api_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'string',
        'label'    => 'Adobe Client ID',
        'help'     => 'Enter your <strong>Client ID</strong> key that was created for your Web App in the Adobe &quot;My Apps&quot; section.<br><br>If you do not have an Adobe account, you can signup and get a free key here:<br><br><a href="https://creativesdk.adobe.com/"><u>https://creativesdk.adobe.com</u></a><br><br>After logging in, click on <strong>My Apps</strong>, then <strong>+New Application</strong>.<br><br>Enter the Application Name (your site name) and select <strong>Web</strong> for the Platform.<br><br>Enter a description and then click Add Application.<br><br>Your <strong>Client ID</strong> and <strong>Client Secret</strong> will be created and shown to you - enter the Client ID here and the Client Secret below.',
        'section'  => 'online image editing',
        'order'    => 1
    );
    jrCore_register_setting('jrGallery', $_tmp);

    // Adobe image manipulation developer key
    $_tmp = array(
        'name'     => 'aviary_key',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'string',
        'label'    => 'Adobe Client Secret',
        'help'     => 'Enter your <strong>Client Secret</strong> key that was created for your Web App in the Adobe &quot;My Apps&quot; section.',
        'order'    => 2
    );
    jrCore_register_setting('jrGallery', $_tmp);

    // Theme
    $_opt = array(
        'light' => 'Light Theme (default)',
        'dark'  => 'Dark Theme'
    );
    $_tmp = array(
        'name'    => 'theme',
        'type'    => 'select',
        'options' => $_opt,
        'default' => 'light',
        'label'   => 'Editor Theme',
        'help'    => 'Select the Editor UI Theme you would like to use in the Image Editor',
        'order'   => 3
    );
    jrCore_register_setting('jrGallery', $_tmp);

    // Original Image
    $_tmp = array(
        'name'     => 'original',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'label'    => 'High Resolution Editing',
        'help'     => 'If this option is checked, when a user edits an image in the Image Editor, the <strong>full resolution original image</strong> will be used, otherwise a max resolution of 1280 pixels will be used.<br><br><b>NOTE:</b> Contact Adobe and ensure your Application has been upgraded to High Resolution Output, or this will not work:<br><br><a href="https://creativesdk.adobe.com/docs/web/#/index.html"><u>https://creativesdk.adobe.com/docs/web/#/index.html</u></a>',
        'order'    => 5
    );
    jrCore_register_setting('jrGallery', $_tmp);

    // Original Downloads
    $_tmp = array(
        'name'     => 'download',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'label'    => 'Allow Original Download',
        'help'     => 'If this option is checked, then users will be able to download the Original Resolution image via a &quot;Download&quot; button when viewing individual images in a gallery - as long as the Gallery image is not for sale.',
        'order'    => 6
    );
    jrCore_register_setting('jrGallery', $_tmp);

    return true;
}
