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
 * Decode an MP3 file to a WAV file
 *
 * @param string $input_file Input Audio File
 * @param array $_options Conversion options array
 * @param string $error_file Conversion errors save file
 * @return string
 */
function jrAudio_mp3_decode($input_file,$_options,$error_file)
{
    return $input_file;
}

/**
 * Encode an MP3 file
 *
 * @param string $input_file Input Audio File
 * @param array $_options Conversion options array
 * @param string $error_file Conversion errors save file
 * @return bool
 */
function jrAudio_mp3_encode($input_file,$_options,$error_file)
{
    // Get ffmpeg location
    $ffmpeg = jrCore_check_ffmpeg_install();

    // Audio Bit Rate
    $bitrate = 128;
    if (isset($_options['bitrate'])) {
        switch ($_options['bitrate']) {
            case '32':
            case '40':
            case '48':
            case '56':
            case '64':
            case '80':
            case '96':
            case '112':
            case '128':
            case '160':
            case '192':
            case '224':
            case '256':
            case '320':
                $bitrate = (int) $_options['bitrate'];
                break;
        }
    }

    // Extra decode options
    $d_options = '';
    if (isset($_options['decode_options']) && strlen($_options['decode_options']) > 0) {
        $d_options = trim($_options['decode_options']);
    }
    // Extra encode options
    $e_options = '';
    if (isset($_options['encode_options']) && strlen($_options['encode_options']) > 0) {
        $e_options = trim($_options['encode_options']);
    }

    ob_start();
    system("nice -n 9 {$ffmpeg} -analyzeduration 30000000 -probesize 30000000 -y {$d_options} -i \"{$input_file}\" -map 0:a -threads 1 {$e_options} -acodec libmp3lame -ar 44100 -ab {$bitrate}k \"{$input_file}.mp3\" >/dev/null 2>{$error_file}",$ret);
    ob_end_clean();
    return "{$input_file}.mp3";
}
