<?php
/**
 * Jamroom 5 System Tips module
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

//------------------------------
// stop_tour
//------------------------------
function view_jrTips_stop_tour($_post, $_user, $_conf)
{
    if (!jrUser_is_logged_in()) {
        $_rp = array('error' => 'you must be logged in to use this function');
        jrCore_json_response($_rp);
    }
    $_ln = jrUser_load_lang_strings();
    $_up = array(
        'user_jrTips_enabled' => 'off'
    );
    if (jrCore_db_update_item('jrUser', $_user['_user_id'], $_up)) {
        jrUser_session_sync($_user['_user_id']);
        jrUser_reset_cache($_user['_user_id']);
        $_rp = array('success' => $_ln['jrTips'][1]);
    }
    else {
        $_rp = array('error' => $_ln['jrTips'][2]);
    }
    jrCore_json_response($_rp);
}
