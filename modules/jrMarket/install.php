<?php
/**
 * Jamroom 5 Marketplace module
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
 * jrMarket_install
 */
function jrMarket_install()
{
    // Make sure systems exist
    $tbl = jrCore_db_table_name('jrMarket', 'system');
    $nam = 'Jamroom Network';
    $url = jrCore_db_escape('http://www.jamroom.net');
    $req = "INSERT INTO {$tbl} (system_name, system_url, system_email, system_code, system_active, system_default) VALUES ('{$nam}', '{$url}', '', '', 'on', 'on')";
    $sid = jrCore_db_query($req, 'INSERT_ID');

    // Make sure our core release channels exist
    $tbl = jrCore_db_table_name('jrMarket', 'channel');
    $req = "SELECT * FROM {$tbl} WHERE channel_name IN('stable','beta')";
    $_rt = jrCore_db_query($req, 'channel_name');
    $_ch = array(
        'stable' => 1,
        'beta'   => 0
    );
    foreach ($_ch as $chn => $id) {
        if (!isset($_rt[$chn])) {
            $req = "INSERT INTO {$tbl} (channel_system_id, channel_created, channel_name, channel_active, channel_code) VALUES ('{$sid}', UNIX_TIMESTAMP(), '{$chn}', '{$id}', '') ON DUPLICATE KEY UPDATE channel_created = UNIX_TIMESTAMP()";
            jrCore_db_query($req);
        }
    }
    return true;
}
