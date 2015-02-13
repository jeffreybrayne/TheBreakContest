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

//------------------------------
// profile_default
//------------------------------
function profile_view_jrGallery_default($_profile, $_post, $_user, $_conf)
{
    // Make sure we get a valid title to show
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return false;
    }

    $_lng = jrUser_load_lang_strings();

    // View a specific image in a gallery
    if (jrCore_checktype($_post['_1'], 'number_nz') && (!isset($_post['_2']) || $_post['_2'] != 'all')) {
        $_rt = jrCore_db_get_item('jrGallery', $_post['_1']);
        if (isset($_rt) && is_array($_rt) && $_rt['_profile_id'] == $_profile['_profile_id']) {

            // Check for pending
            if (isset($_rt['gallery_pending']) && $_rt['gallery_pending'] == '1' && !jrUser_is_profile_owner($_rt['_profile_id'])) {
                jrCore_page_not_found();
            }

            if (isset($_rt['gallery_image_title']) && strlen($_rt['gallery_image_title']) > 0) {
                $title = $_rt['gallery_image_title'];
            }
            else {
                $title = jrGallery_title_name($_rt['gallery_image_name']);
            }
            jrCore_page_title("{$title} - {$_lng['jrGallery']['menu']} - {$_profile['profile_name']}");
            $_profile['item'] = $_rt;
            $key              = md5("{$_rt['_profile_id']}-{$_rt['gallery_title_url']}");
            if (!isset($_SESSION['jrGallery_active_gallery'])) {
                $_SESSION['jrGallery_active_gallery'] = $key;
            }
            elseif ($_SESSION['jrGallery_active_gallery'] != $key) {
                // We've changed galleries - reset
                $_SESSION['jrGallery_active_gallery'] = $key;
                $_SESSION['jrGallery_page_num']       = 1;
            }
            // get the NEXT and PREV ids so the user can move to the next item.
            $_sp = array(
                'search'                       => array(
                    "_profile_id = {$_rt['_profile_id']}",
                    "gallery_title_url = {$_rt['gallery_title_url']}",
                ),
                'return_keys'                  => array('_item_id', 'gallery_order', 'gallery_image_name', 'gallery_image_title_url', 'profile_url'),
                'order_by'                     => "gallery_order numerical_asc",
                'exclude_jrUser_keys'          => true,
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => 10000
            );
            $_im = jrCore_db_search_items('jrGallery', $_sp);
            if (isset($_im) && is_array($_im['_items'])) {
                $prev = 0;
                $next = 0;
                foreach ($_im['_items'] as $k => $i) {
                    if ($i['_item_id'] == $_rt['_item_id']) {
                        $nxt = ($k + 1);
                        if (isset($_im['_items'][$nxt])) {
                            $next = $_im['_items'][$nxt];
                        }
                        $prv = ($k - 1);
                        if (isset($_im['_items'][$prv])) {
                            $prev = $_im['_items'][$prv];
                        }
                        break;
                    }
                }
                $_profile['prev'] = $prev;
                $_profile['next'] = $next;
            }
            return jrCore_parse_template('item_detail.tpl', $_profile, 'jrGallery');
        }
        // Fall through for gallery named as a number
    }

    // View a specific Gallery
    $_sp = array(
        'search'   => array(
            "_profile_id = {$_profile['_profile_id']}",
        ),
        'order_by' => array('gallery_order' => 'numerical_asc'),
        'limit'    => 500
    );
    if (isset($_post['_1']) && $_post['_1'] != 'all') {
        $_sp['search'][] = "gallery_title_url = " . rawurlencode($_post['_1']);
    }

    // Get results
    $_it = jrCore_db_search_items('jrGallery', $_sp);

    if ($_it && is_array($_it) && isset($_it['_items'])) {

        if ($_post['_1'] == 'all') {
            $_profile['show_all_galleries'] = true;
        }

        $_profile['_items'] = $_it['_items'];
        jrCore_page_title("{$_profile['_items'][0]['gallery_title']} - {$_lng['jrGallery']['menu']} - {$_profile['profile_name']}");
        unset($_it);
        return jrCore_parse_template('item_gallery.tpl', $_profile, 'jrGallery');
    }

    jrCore_page_not_found();
    return false;
}
