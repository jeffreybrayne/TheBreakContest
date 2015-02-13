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
 * jrGallery_verify
 */
function jrGallery_verify()
{
    // We need to fix up any gallery images that may have been created
    // without a gallery_order set
    $_rt = jrCore_db_get_items_missing_key('jrGallery','gallery_order');
    if (isset($_rt) && is_array($_rt)) {
        $tbl = jrCore_db_table_name('jrGallery','item_key');
        $_id = array();
        foreach ($_rt as $id) {
            $_id[] = "('{$id}','gallery_order',100)";
        }
        $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`value`) VALUES ". implode(',',$_id) ." ON DUPLICATE KEY UPDATE `value` = 100";
        $cnt = jrCore_db_query($req,'COUNT');
        if (isset($cnt) && $cnt > 0) {
            jrCore_logger('INF',"fixed {$cnt} gallery images missing gallery_order key");
        }
    }
    return true;
}
