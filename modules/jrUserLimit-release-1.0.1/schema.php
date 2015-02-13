<?php
/**
 * Jamroom 5 User Daily Limits module
 *
 * copyright 2003 - 2014
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
 * jrUserLimit_db_schema
 */
function jrUserLimit_db_schema()
{
    // Counts
    $_tmp = array(
        "c_user_id INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_module VARCHAR(64) NOT NULL DEFAULT ''",
        "c_event VARCHAR(64) NOT NULL DEFAULT ''",
        "c_date INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_time INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "c_count INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "UNIQUE c_unique (c_user_id, c_module, c_event, c_date)",
        "INDEX c_module (c_module)",
        "INDEX c_event (c_event)",
        "INDEX c_date (c_date)",
        "INDEX c_time (c_time)"
    );
    jrCore_db_verify_table('jrUserLimit', 'counts', $_tmp);
    return true;
}
