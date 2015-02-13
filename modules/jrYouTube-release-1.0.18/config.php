<?php
/**
 * Jamroom 5 YouTube Support module
 *
 * copyright 2003 - 2015
 * by paul
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * paul
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
 * jrYouTube_config
 */
function jrYouTube_config()
{
     // Daily Maintenance
    $_tmp = array(
        'name'     => 'daily_maintenance',
        'type'     => 'text',
        'default'  => 0,
        'validate' => 'number_nn',
        'label'    => 'Daily Maintenance',
        'help'     => 'If greater than zero, the specified number of created YouTube videos will be checked sequentially on a daily basis, and removed if they are no longer active on YouTube. Removed items will be logged.',
        'order'    => 1
    );
    jrCore_register_setting('jrYouTube', $_tmp);

    return true;
}

/**
 * Display number of uploaded YouTube videos to master
 */
function jrYouTube_config_display($_post, $_user, $_conf)
{
    $cnt = jrCore_db_number_rows('jrYouTube', 'item');
    jrCore_set_form_notice('notice', "There are {$cnt} YouTube videos uploaded");
    return true;
}
