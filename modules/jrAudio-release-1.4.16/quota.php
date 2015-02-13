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
 * quota_config
 */
function jrAudio_quota_config()
{
    // Allowed Audio Media Types
    $_tmp = array(
        'name'     => 'allowed_audio_types',
        'default'  => 'aac,flac,m4a,mp3,ogg,wav,wma',
        'type'     => 'optionlist',
        'options'  => 'jrAudio_get_audio_types',
        'required' => 'on',
        'label'    => 'allowed upload',
        'help'     => 'Select the audio file types you would like to allow Profiles in this Quota to upload.',
        'validate' => 'core_string',
        'order'    => 11
    );
    jrProfile_register_quota_setting('jrAudio',$_tmp);

    // Conversions
    $_tmp = array(
        'name'     => 'audio_conversions',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'convert to MP3',
        'help'     => 'If this option is checked, all uploaded media will have an MP3 version created that will be used to stream from the site.',
        'order'    => 12
    );
    jrProfile_register_quota_setting('jrAudio',$_tmp);

    // Conversion Bit Rate
    $_bit = array(
        32 => 32,
        40 => 40,
        48 => 48,
        56 => 56,
        64 => 64,
        80 => 80,
        96 => 96,
        112 => 112,
        128 => 128,
        160 => 160,
        192 => 192,
        224 => 224,
        256 => 256,
        320 => 320
    );
    $_tmp = array(
        'name'     => 'conversion_bitrate',
        'default'  => 128,
        'type'     => 'select',
        'options'  => $_bit,
        'required' => 'on',
        'label'    => 'MP3 bit rate',
        'help'     => 'Select the bit rate you would like to convert uploaded audio files to. If the bit rate of an uploaded file is GREATER than the value set here, it will be converted. If the bit rate of the uploaded audio file is LOWER than what is set here, it will be left as is to attempt to preserve the highest quality possible.',
        'validate' => 'number_nz',
        'min'      => 32,
        'max'      => 320,
        'order'    => 13
    );
    jrProfile_register_quota_setting('jrAudio',$_tmp);

    // ID3 Tagging
    $_tmp = array(
        'name'     => 'audio_tag',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Add MP3 Tags',
        'help'     => 'If this option is checked, uploaded and converted MP3 files will have their ID3 tags updated to match the stored audio information',
        'order'    => 14
    );
    jrProfile_register_quota_setting('jrAudio',$_tmp);

    // Remove old options no longer used
    jrProfile_delete_quota_setting('jrAudio','conversion_worker_count');

    return true;
}
