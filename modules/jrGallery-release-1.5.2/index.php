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
// original_image
//------------------------------
function view_jrGallery_original_image($_post, $_user, $_conf)
{
    // Valid item id
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice('CRI', "Invalid Item ID");
    }
    // Check for valid edit key
    if (!isset($_post['edit_key']) || !jrCore_checktype($_post['edit_key'], 'number_nz')) {
        jrCore_notice('CRI', "Invalid Edit Key");
    }
    if (!$key = jrCore_get_temp_value('jrGallery', "image_edit_key_{$_post['edit_key']}")) {
        jrCore_notice('CRI', "Invalid Edit Key - not found");
    }
    // Get image
    $_rt = jrCore_db_get_item('jrGallery', $_post['_1']);
    if (!$_rt) {
        jrCore_notice_page('error', 'invalid image - image data not found');
    }
    // Check that file exists
    $dir = jrCore_get_media_directory($_rt['_profile_id']);
    $nam = "jrGallery_{$_post['_1']}_gallery_image.{$_rt['gallery_image_extension']}";
    // Make sure file is actually there...
    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        jrCore_notice_page('error', 'invalid image - image file not found');
    }
    // Get right mime type - sometimes it can be wrong when PHP is wrong
    switch ($_rt['gallery_image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            header("Content-type: image/jpeg");
            break;
        case 'png':
            header("Content-type: image/png");
            break;
        case 'gif':
            header("Content-type: image/gif");
            break;
        default:
            header("Content-type: " . $_rt['gallery_image_type']);
            break;
    }
    header('Content-Disposition: inline; filename="' . $_rt['gallery_image_name'] . '"');
    echo file_get_contents("{$dir}/{$nam}");
    session_write_close();
    exit();
}


//------------------------------
// slider_images
//------------------------------
function view_jrGallery_slider_images($_post, $_user, $_conf)
{
    if (!isset($_post['pid']) || !jrCore_checktype($_post['pid'], 'number_nz')) {
        $_rs = array('error' => 'invalid profile_id');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['gallery']) || strlen($_post['gallery']) === 0) {
        $_rs = array('error' => 'invalid gallery');
        jrCore_json_response($_rs);
    }
    $page = 1;
    if (isset($_post['page']) && jrCore_checktype($_post['page'], 'number_nz')) {
        $page = (int) $_post['page'];
    }
    $pagebreak = 12;
    if (isset($_post['pagebreak']) && jrCore_checktype($_post['pagebreak'], 'number_nz')) {
        $pagebreak = (int) $_post['pagebreak'];
    }
    $_sc = array(
        'search'                       => array(
            "_profile_id = {$_post['pid']}",
            "gallery_title_url = {$_post['gallery']}"
        ),
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'pagebreak'                    => $pagebreak,
        'page'                         => $page
    );
    $_rt = jrCore_db_search_items('jrGallery', $_sc);
    if (!$_rt || !is_array($_rt) || !is_array($_rt['_items'])) {
        $_rs = array('error' => 'no gallery images found');
        jrCore_json_response($_rs);
    }
    $key = md5("{$_post['pid']}-{$_post['gallery']}");
    if (!isset($_SESSION['jrGallery_active_gallery'])) {
        $_SESSION['jrGallery_active_gallery'] = $key;
    }
    elseif ($_SESSION['jrGallery_active_gallery'] != $key) {
        // We've changed galleries - reset
        unset($_SESSION['jrGallery_page_num']);
        $_SESSION['jrGallery_active_gallery'] = $key;
    }
    $_SESSION['jrGallery_page_num'] = $page;
    return jrCore_parse_template('item_slider.tpl', $_rt, 'jrGallery');
}

//------------------------------
// create
//------------------------------
function view_jrGallery_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new gallery file
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrProfile_check_disk_usage();

    // Start our create form
    jrCore_page_banner(1);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Gallery Title
    $_tmp = array(
        'name'       => 'gallery_title',
        'label'      => 3,
        'help'       => 4,
        'type'       => 'text',
        'validate'   => 'not_empty',
        'ban_check'  => 'word',
        'required'   => false,
        'unique'     => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Gallery Images
    $_tmp = array(
        'name'     => 'gallery_image',
        'label'    => 1,
        'help'     => 5,
        'text'     => 'select images to upload',
        'type'     => 'image',
        'multiple' => true,
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrGallery_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrCore_form_validate($_post);

    // For our Gallery Images, we are going to create a UNIQUE DataStore entry
    // for each file that is uploaded
    $_files = jrCore_get_uploaded_media_files('jrGallery', 'gallery_image', $_user['user_active_profile_id']);
    if (!$_files || !is_array($_files)) {
        jrCore_set_form_notice('error', 6);
        jrCore_form_result();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrGallery', 'create', $_post);

    // If we do NOT get a gallery title, we default to profile images...
    if (!isset($_rt['gallery_title']) || strlen($_rt['gallery_title']) === 0) {
        $_ln = jrUser_load_lang_strings();
        $_rt['gallery_title'] = $_ln['jrGallery'][10];
    }

    // Add in our Gallery Title (for SEO URL use)
    $_rt['gallery_title_url'] = jrCore_url_string($_rt['gallery_title']);

    $i = 0;
    foreach ($_files as $file_name) {
        $_rt['gallery_order'] = ++$i;
        $aid                  = jrCore_db_create_item('jrGallery', $_rt);
        if (!$aid) {
            jrCore_set_form_notice('error', 7);
            jrCore_form_result();
        }
        // Now that we have our DataStore Item created, link up the file with it
        // We have to tell jrCore_save_media_file the file we want to link with this item,
        // so we pass in the FULL PATH $_file_name as arg #2 to jrCore_save_media_file
        jrCore_save_media_file('jrGallery', $file_name, $_user['user_active_profile_id'], $aid);

        // Add our FIRST IMAGE to our actions...
        if (!isset($action_saved)) {
            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'create', 'jrGallery', $aid);
            $action_saved = true;
        }
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_rt['gallery_title_url']}/all");
}

//------------------------------
// update
//------------------------------
function view_jrGallery_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 14);
    }
    $_it = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!$_it) {
        jrCore_notice_page('error', 14);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_it)) {
        jrUser_not_authorized();
    }

    // Get our info
    $_params = array(
        'search'   => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "gallery_title_url = {$_it['gallery_title_url']}"
        ),
        "order_by" => array(
            '_item_id' => 'DESC'
        ),
        "limit"    => 500
    );
    $_rt     = jrCore_db_search_items('jrGallery', $_params);

    // Start our create form
    jrCore_page_banner($_it['gallery_title']);

    // Form init
    $_tmp = array(
        'submit_value' => 16,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_it
    );
    jrCore_form_create($_tmp);

    // Gallery Title
    $_tmp = array(
        'name'     => 'existing_gallery_title',
        'type'     => 'hidden',
        'validate' => 'printable',
        'value'    => $_it['gallery_title_url']
    );
    jrCore_form_field_create($_tmp);

    // Gallery Title
    $_tmp = array(
        'name'      => 'gallery_title',
        'label'     => 3,
        'help'      => 4,
        'type'      => 'text',
        'validate'  => 'not_empty',
        'ban_check' => 'word',
        'required'  => true,
        'unique'    => $_it['gallery_title']
    );
    jrCore_form_field_create($_tmp);

    $htm = jrCore_parse_template('gallery_update.tpl', $_rt, 'jrGallery');
    jrCore_page_custom($htm, 10);

    // Gallery Images
    $_tmp = array(
        'name'     => 'gallery_image',
        'label'    => 11,
        'help'     => 5,
        'text'     => 'select images to upload',
        'type'     => 'image',
        'multiple' => true,
        'required' => false,
        'value'    => false,
        'no_image' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrGallery_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrCore_form_validate($_post);

    // For our Gallery Images, we are going to create a UNIQUE DataStore entry
    // for each file that is uploaded
    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_up                      = jrCore_form_get_save_data('jrGallery', 'update', $_post);
    $_up['gallery_title_url'] = jrCore_url_string($_post['gallery_title']);

    // Any _new_ uploaded files
    $_files = jrCore_get_uploaded_media_files('jrGallery', 'gallery_image', $_user['user_active_profile_id']);

    // Update all existing gallery entries with new title
    $ord = 0;
    $cnt = count($_files);
    if ($_post['gallery_title_url'] != $_post['existing_gallery_title']) {
        $_rt = array(
            'search'         => array(
                "_profile_id = {$_user['user_active_profile_id']}",
                "gallery_title_url = {$_post['existing_gallery_title']}"
            ),
            'return_keys'    => array('_item_id', '_updated', 'gallery_order'),
            'skip_triggers'  => true,
            'ignore_pending' => true,
            'limit'          => 25000
        );
        $_rt = jrCore_db_search_items('jrGallery', $_rt);
        if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
            jrCore_set_form_notice('error', 12);
            jrCore_form_result();
        }
        $tot = count($_rt['_items']);
        $_dt = array();
        $_cr = array();
        foreach ($_rt['_items'] as $item) {
            // Setup new gallery order
            if (!isset($item['gallery_order'])) {
                // Old one without order - fall to end
                $ord++;
                $_up['gallery_order'] = ($tot + $cnt + $ord);
            }
            else {
                $_up['gallery_order'] = ($item['gallery_order'] + $cnt);
            }
            $_dt["{$item['_item_id']}"] = $_up;
            $_cr["{$item['_item_id']}"] = array('_updated' => $item['_updated']);
        }
        jrCore_db_update_multiple_items('jrGallery', $_dt, $_cr);
    }

    // Get new uploaded files
    if ($_files && is_array($_files)) {

        $_up['gallery_order'] = 0;
        foreach ($_files as $file_name) {
            // $aid will be the INSERT_ID (_item_id) of the created item
            $_up['gallery_order']++;
            $aid = jrCore_db_create_item('jrGallery', $_up);
            if (!$aid) {
                jrCore_set_form_notice('error', 7);
                jrCore_form_result();
            }
            // Now that we have our DataStore Item created, link up the file with it
            // We have to tell jrCore_save_media_file the file we want to link with this item,
            // so we pass in the FULL PATH of $_file_name as arg #2 to jrCore_save_media_file
            jrCore_save_media_file('jrGallery', $file_name, $_user['user_active_profile_id'], $aid);

            // Add our FIRST IMAGE to our actions...
            if (!isset($action_saved)) {
                // Add to Actions...
                jrCore_run_module_function('jrAction_save', 'update', 'jrGallery', $aid);
                $action_saved = true;
            }
        }
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_up['gallery_title_url']}/all");
}

//------------------------------
// delete_save
//------------------------------
function view_jrGallery_delete_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrGallery');
    // We should get our gallery_title_url as $_post['_2'] ...
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_notice_page('error', 9);
    }
    // Get all gallery images that are part of this collection
    $_params = array(
        'search'        => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "gallery_title_url = {$_post['_2']}"
        ),
        "order_by"      => array(
            '_item_id' => 'DESC'
        ),
        "limit"         => 500,
        "skip_triggers" => true
    );
    $_rt     = jrCore_db_search_items('jrGallery', $_params);

    if (!jrUser_can_edit_item($_rt['_items'][0])) {
        jrUser_not_authorized();
    }
    // Delete each image
    foreach ($_rt['_items'] as $_g) {
        jrCore_db_delete_item('jrGallery', $_g['_item_id']);
    }
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// detail
//------------------------------
function view_jrGallery_detail($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start our create form
    jrCore_page_banner(15);

    $canc = jrCore_is_profile_referrer("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/{$_rt['gallery_title_url']}");
    if (strpos(jrCore_get_local_referrer(), '/update/id=')) {
        $canc = 'referrer';
    }

    // Form init
    $_tmp = array(
        'submit_value' => 16,
        'cancel'       => $canc,
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // Gallery ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'validate' => 'number_nz',
        'value'    => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    $no_image = false;
    if (isset($_conf['jrGallery_aviary_key']) && strlen($_conf['jrGallery_aviary_key']) > 0 && (!isset($_user['quota_jrGallery_image_editor']) || $_user['quota_jrGallery_image_editor'] != 'off')) {
        // See if we using HI RES IMAGES
        if (isset($_conf['jrGallery_original']) && $_conf['jrGallery_original'] == 'on' && isset($_conf['jrGallery_api_key']) && strlen($_conf['jrGallery_api_key']) > 0) {
            // signature = md5(api_key+api_secret+timestamp+salt)
            $_rt['timestamp'] = time();
            $_rt['salt']      = md5(microtime());
            $_rt['signature'] = sha1($_conf['jrGallery_api_key'] . $_conf['jrGallery_aviary_key'] . $_rt['timestamp'] . $_rt['salt']);
        }
        $htm = jrCore_parse_template('gallery_manipulate.tpl', $_rt, 'jrGallery');
        jrCore_page_custom($htm, 'Gallery Image');
        $no_image = true;
    }

    // New Image (replace existing)
    $_tmp = array(
        'name'     => 'gallery_image',
        'label'    => 41,
        'help'     => 42,
        'text'     => 43,
        'type'     => 'image',
        'value'    => $_rt,
        'required' => false,
        'no_image' => $no_image
    );
    // 'value'    => $_rt,
    // 'no_image' => true
    jrCore_form_field_create($_tmp);

    // Let's get other galleries this profile has created so we can allow the
    // image to be moved to a new gallery if they want
    $_og = jrGallery_get_gallery_titles($_user['user_active_profile_id']);
    if ($_og) {
        // Gallery Title
        $_tmp = array(
            'name'          => 'gallery_title',
            'label'         => 25,
            'help'          => 36,
            'type'          => 'select_and_text',
            'options'       => $_og,
            'validate'      => 'not_empty',
            'required'      => true,
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }

    // Gallery Image Title
    $_tmp = array(
        'name'     => 'gallery_image_title',
        'label'    => 46,
        'help'     => 47,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Gallery Caption
    $_tmp = array(
        'name'     => 'gallery_caption',
        'label'    => 17,
        'help'     => 18,
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// detail_save
//------------------------------
function view_jrGallery_detail_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrGallery');
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result();
    }
    // Get data
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 14);
        jrCore_form_result();
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv                            = jrCore_form_get_save_data('jrGallery', 'detail', $_post);
    $_sv['gallery_title_url']       = jrCore_url_string($_sv['gallery_title']);
    if (isset($_sv['gallery_image_title']) && strlen($_sv['gallery_image_title']) > 0) {
        $_sv['gallery_image_title_url'] = jrCore_url_string($_sv['gallery_image_title']);
    }
    else {
        unset($_sv['gallery_image_title']);
    }

    // get the just edited remote image file from http://aviary.com if it has been edited using the aviary image editor.
    if (jrCore_file_extension($_post['gallery_alt_img']) === 'png') {

        $file_contents = file_get_contents($_post['gallery_alt_img']);
        $fname         = 'gallery_image';
        $nam           = 'jrGallery_' . $_post['id'] . '_' . $fname . '.png';
        if (!jrCore_write_media_file($_rt['_profile_id'], $nam, $file_contents, 'public')) {
            jrCore_logger('CRI', "error saving media file: {$_rt['_profile_id']}/{$nam}");
        }
        $file = jrCore_get_media_directory($_rt['_profile_id']) . '/' . $nam;

        // Okay we've save it.  Next, we need to update the datastore
        // entry with the info from the file
        $_tmp                      = getimagesize($file);
        $_sv["{$fname}_time"]      = time();
        $_sv["{$fname}_name"]      = $_rt['gallery_image_name'];
        $_sv["{$fname}_size"]      = filesize($file);
        $_sv["{$fname}_type"]      = jrCore_mime_type($file);
        $_sv["{$fname}_extension"] = 'png';
        $_sv["{$fname}_width"]     = (int) $_tmp[0];
        $_sv["{$fname}_height"]    = (int) $_tmp[1];
        $_sv["{$fname}_access"]    = '1'; // 0 = creator only, 1 = private view/stream only, 2 = private view/stream/download, 3 = public view/stream only, 4 = public view/stream/download
    }

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrGallery', $_post['id'], $_sv);

    // Save any NEW gallery image (overwriting existing)
    jrCore_save_all_media_files('jrGallery', 'detail', $_user['user_active_profile_id'], $_post['id']);

    // jrCore_form_delete_session();
    jrProfile_reset_cache();
    $_rt = array_merge($_rt, $_sv);
    $url = jrGallery_get_gallery_image_url($_rt);
    jrCore_form_result($url);
}

//------------------------------
// delete_image
//------------------------------
function view_jrGallery_delete_image($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_check_quota_access('jrGallery');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrGallery', $_post['id']);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    jrCore_db_delete_item('jrGallery', $_post['id']);
    jrProfile_reset_cache();

    // See if we have images left in the gallery
    $_sc = array(
        'search'         => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "gallery_title_url = {$_rt['gallery_title_url']}"
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 1
    );
    $_ex = jrCore_db_search_items('jrGallery', $_sc);
    if ($_ex && isset($_ex['_items']) && is_array($_ex['_items'])) {
        // We still have more gallery images
        $url = jrCore_get_local_referrer();
        if (strpos($url, "/{$_rt['_item_id']}/")) {
            // Deleted from detail
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}/{$_rt['gallery_title_url']}/all");
        }
        elseif (strpos($url, "/{$_rt['profile_url']}/{$_post['module_url']}/{$_rt['gallery_title_url']}")) {
            // Deleted from profile
            jrCore_form_result($url);
        }
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_ex['_items'][0]['_item_id']}");
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// jrEmbed tab (loaded via ajax)
//------------------------------
function view_jrGallery_tab($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $ss = array();
    $default = true;

    // search string
    if (isset($_post['ss']) && $_post['ss'] !== "false" && $_post['ss'] !== "undefined" && $_post['ss'] !== "") {
        $ss[] = "gallery_% LIKE %{$_post['ss']}%";
        $default = false;
    }
    // profile
    if (isset($_post['profile_url']) && $_post['profile_url'] !== "false" && $_post['profile_url'] !== "undefined" && $_post['profile_url'] !== "") {
        $ss[] = "profile_url = {$_post['profile_url']}";
        $default = false;
    }
    // default list of items
    if ($default) {
        $ss[] = "_profile_id = {$_user['user_active_profile_id']}";
    }
    // Create search params from $_post
    $_sp = array(
        'search'              => $ss,
        'pagebreak'           => 8,
        'page'                => $_post['p'],
        'exclude_jrUser_keys' => true
    );
    $_rt     = jrCore_db_search_items('jrGallery', $_sp);

    // Get images sizes
    $_rt['image_sizes'] = array();
    $_sz                = jrImage_get_allowed_image_widths();
    if (isset($_sz) && is_array($_sz)) {
        foreach ($_sz as $desc => $pixels) {
            if (!is_numeric($desc)) {
                $_rt['image_sizes'][$pixels] = "{$desc} ({$pixels}px)";
            }
        }
    }
    return jrCore_parse_template('tab_ajax_gallery.tpl', $_rt, 'jrGallery');
}

//----------------------------------
// update the order of an gallery
//----------------------------------
function view_jrGallery_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['gallery_order']) || !is_array($_post['gallery_order'])) {
        return jrCore_json_response(array('error', 'invalid gallery_order array received'));
    }

    // Get our gallery files that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items('jrGallery', $_post['gallery_order']);
        if (!isset($_rt) || !is_array($_rt)) {
            return jrCore_json_response(array('error', 'unable to retrieve audio entries from database'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error', 'permission denied'));
            }
        }
    }
    // Looks good - set album order
    $tbl = jrCore_db_table_name('jrGallery', 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_post['gallery_order'] as $ord => $iid) {
        $ord = (int) $ord;
        $iid = (int) $iid;
        $req .= "('{$iid}','gallery_order',0,'{$ord}'),";
    }
    $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    jrCore_db_query($req);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'gallery_order successfully updated'));
}

//----------------------------------
// parse a given template
// $_post['_1'] - template
// $_post['_2'] - _item_id
// $_post['_3'] - gallery_title_url
//----------------------------------
function view_jrGallery_parse($_post, $_user, $_conf)
{
    if (isset($_post['_2']) && jrCore_checktype($_post['_2'], 'number_nz')) {
        $_tmp = array(
            'item' => jrCore_db_get_item('jrGallery', $_post['_2'])
        );
    }
    elseif (isset($_post['_3']) && strlen($_post['_3']) > 0) {
        $_s   = array(
            "search" => array(
                "gallery_title_url = {$_post['_3']}"
            ),
            "limit"  => 100
        );
        $_tmp = jrCore_db_search_items('jrGallery', $_s);
    }
    else {
        return 'invalid parameters received';
    }
    return jrCore_parse_template("{$_post['_1']}.tpl", $_tmp, 'jrGallery');
}
