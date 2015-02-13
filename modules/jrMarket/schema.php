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
 * jrMarket_db_schema
 */
function jrMarket_db_schema()
{
    global $_conf;
    // Update Systems
    $_tmp = array(
        "system_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "system_name VARCHAR(128) NOT NULL DEFAULT ''",
        "system_url VARCHAR(128) NOT NULL DEFAULT ''",
        "system_email VARCHAR(128) NOT NULL DEFAULT ''",
        "system_code VARCHAR(32) NOT NULL DEFAULT ''",
        "system_active CHAR(3) NOT NULL DEFAULT 'off'",
        "system_default CHAR(3) NOT NULL DEFAULT 'off'",
        "INDEX system_default (system_default)"
    );
    jrCore_db_verify_table('jrMarket', 'system', $_tmp);

    // Update Channels
    $_tmp = array(
        "channel_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "channel_system_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "channel_created INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "channel_name VARCHAR(64) NOT NULL DEFAULT ''",
        "channel_active TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
        "channel_code VARCHAR(16) NOT NULL DEFAULT ''",
        "INDEX channel_code (channel_code)"
    );
    jrCore_db_verify_table('jrMarket', 'channel', $_tmp);

    // Install Results
    $_tmp = array(
        "install_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
        "install_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "install_data TEXT NOT NULL"
    );
    jrCore_db_verify_table('jrMarket', 'install', $_tmp);

    // Convert global marketplace config to new release system
    if (isset($_conf['jrMarket_system_id']{0})) {
        $tbl = jrCore_db_table_name('jrMarket', 'system');
        $nam = 'Jamroom Network';
        $url = jrCore_db_escape($_conf['jrMarket_update_url']);
        $eml = jrCore_db_escape($_conf['jrMarket_system_email']);
        $cod = jrCore_db_escape($_conf['jrMarket_system_id']);
        $req = "INSERT INTO {$tbl} (system_name, system_url, system_email, system_code, system_active, system_default) VALUES ('{$nam}', '{$url}', '{$eml}', '{$cod}', 'on', 'on')";
        $sid = jrCore_db_query($req, 'INSERT_ID');
        if ($sid && $sid > 0) {

            // Update channel with new System ID
            $tbl = jrCore_db_table_name('jrMarket', 'channel');
            $req = "UPDATE {$tbl} SET channel_system_id = '{$sid}'";
            jrCore_db_query($req);

            jrCore_delete_setting('jrMarket', 'update_url');
            jrCore_delete_setting('jrMarket', 'system_email');
            jrCore_delete_setting('jrMarket', 'system_id');
        }
    }
    return true;
}
