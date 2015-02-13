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
 * jrAudio_verify
 */
function jrAudio_verify()
{
    // We need to fix up any items created without an audio_file_track set
    $_rt = jrCore_db_get_items_missing_key('jrAudio', 'audio_file_track');
    if (isset($_rt) && is_array($_rt)) {
        $_up = array();
        foreach ($_rt as $id) {
            $_up[$id] = array('audio_file_track' => 1);
        }
        jrCore_db_update_multiple_items('jrAudio', $_up);
        jrCore_logger('INF', "fixed " . count($_up) . " audio items missing audio_file_track key");
    }

    // add bit rate in for items that were not set
    $_rt = jrCore_db_get_items_missing_key('jrAudio', 'audio_file_original_bitrate');

    return true;
}
