<?php
/**
 * Jamroom 5 Audio Support module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrAudio_config
 */
function jrAudio_config()
{
    // Block File Downloads
    $_tmp = array(
        'name'     => 'block_download',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'block file downloads',
        'help'     => 'By default audio files are blocked from being downloaded via the &quot;download&quot; URL, and are only playable in the site audio players. Uncheck this option to allow download of audio files.<br><br><strong>NOTE:</strong> If you are using the FoxyCart or PayPal eCommerce module, any audio files that have a price greater than 0, or are part of a paid bundle, cannot be downloaded.',
        'order'    => 1
    );
    jrCore_register_setting('jrAudio', $_tmp);

    // Block Album Downloads
    $_tmp = array(
        'name'     => 'block_album_download',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'block album downloads',
        'help'     => 'Check this option to block album downloads - this can save disk space on your server by preventing album ZIP files from being created.',
        'order'    => 2
    );
    jrCore_register_setting('jrAudio', $_tmp);

    $_cnt = array(
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5
    );
    $_tmp = array(
        'name'     => 'conversion_worker_count',
        'default'  => 1,
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'label'    => 'max conversion workers',
        'help'     => 'What is the maximum number of audio conversions that can be going on simultaneously? It is recommended to leave this value at 1 (one) unless you are on a dedicated server.',
        'validate' => 'number_nz',
        'min'      => 1,
        'max'      => 5,
        'section'  => 'audio conversion',
        'order'    => 10
    );
    jrCore_register_setting('jrAudio', $_tmp);

    $_cnt = array(
        0   => 'Disable Sample Creation',
        30  => '30 seconds',
        45  => '45 seconds',
        60  => '60 seconds (default)',
        90  => '90 seconds',
        120 => '120 seconds'
    );
    $_tmp = array(
        'name'     => 'sample_length',
        'default'  => 60,
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'label'    => 'audio sample length',
        'help'     => 'If you have the eCommerce package installed and are selling audio downloads, the Audio Module will create an &quot;audio sample&quot; of items that are for sale - how long should the audio sample be?',
        'validate' => 'number_nn',
        'min'      => 0,
        'max'      => 120,
        'section'  => 'audio conversion',
        'order'    => 11
    );
    jrCore_register_setting('jrAudio', $_tmp);

    $_cnt = array(
        'mp3'     => 'MP3',
        'mp3,ogg' => 'MP3 + OGG'
    );
    $_tmp = array(
        'name'     => 'conversion_format',
        'default'  => 'mp3',
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'label'    => 'conversion format',
        'help'     => 'Select the audio format(s) you would like to convert uploaded audio to. MP3 + OGG will have compatability with some older mobile devices, but will <strong>use more disk space</strong>, while MP3 Only (recommended) will use less disk space.',
        'section'  => 'audio conversion',
        'order'    => 12
    );
    jrCore_register_setting('jrAudio', $_tmp);

    return true;
}

?>
