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
 * meta
 */
function jrGallery_meta()
{
    $_tmp = array(
        'name'        => 'Image Galleries',
        'url'         => 'gallery',
        'version'     => '1.5.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add Image Galleries to User Profiles',
        'category'    => 'media',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGallery_init()
{
    //listener for the jrEmbed module to add a tab to the popup tinymce editor
    jrCore_register_event_listener('jrEmbed', 'tinymce_popup', 'jrGallery_tinymce_popup_listener');

    // We listen for the jrUrlScan 'url_found' trigger and if its a gallery url, add appropriate data to its array
    jrCore_register_event_listener('jrUrlScan', 'url_found', 'jrGallery_url_found_listener');

    // We have some small custom CSS for our page
    jrCore_register_module_feature('jrCore', 'css', 'jrGallery', 'jrGallery.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGallery', 'jrGallery.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGallery', 'jquery.sortable.min.js');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGallery', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGallery', 'update');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrGallery', 'detail');

    // Let the core Action System know we are adding gallery Support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrGallery', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrGallery', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrGallery', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGallery', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrGallery', 'update', 'item_action.tpl');

    // Sales support
    jrCore_register_event_listener('jrFoxyCart', 'add_price_field', 'jrGallery_add_price_field_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'jrGallery_my_items_row_listener');

    // Bundle Support (selling an entire album (gallery))
    jrCore_register_module_feature('jrFoxyCartBundle', 'visible_support', 'jrGallery', true);
    jrCore_register_event_listener('jrFoxyCartBundle', 'get_album_field', 'jrGallery_get_bundle_field_listener');
    jrCore_register_event_listener('jrFoxyCartBundle', 'add_bundle_price_field', 'jrGallery_add_bundle_price_field_listener');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrGallery', 'gallery_image_title,gallery_image_name,gallery_caption', 24);

    // Fix up image titles in bundles
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrGallery_db_search_items_listener');

    // Make sure originals are not being downloaded
    jrCore_register_event_listener('jrCore', 'download_file', 'jrGallery_download_file_listener');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrGallery', 'profile_jrGallery_item_count', 38);

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Watch for original image downloads
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_download_file_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrGallery') {
        if (!isset($_conf['jrGallery_download']) || $_conf['jrGallery_download'] != 'on') {
            header('HTTP/1.0 403 Forbidden');
            header('Connection: close');
            jrCore_notice('Error', 'you do not have permission to download this file');
            exit;
        }
    }
    return $_data;
}

/**
 * Add in player code to the jrUrlScan array
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_url_found_listener($_data, $_user, $_conf, $_args, $event)
{
    // Is it a local gallery image url
    if (strpos($_args['url'], $_conf['jrCore_base_url']) == 0) {
        $_x = explode('/', substr($_args['url'], strlen($_conf['jrCore_base_url']) + 1));
        if ($_x && is_array($_x) && isset($_x[1]) && $_x[1] == jrCore_get_module_url('jrGallery')) {
            $idx = (int) $_args['i'];
            if (isset($_x[2]) && jrCore_checktype($_x[2], 'number_nz')) {
                $_item                             = jrCore_db_get_item('jrGallery', $_x[2], true);
                $title                             = (isset($_item['gallery_image_title']) && strlen($_item['gallery_image_title']) > 0) ? $_item['gallery_image_title'] : $_item['gallery_image_name'];
                $_data['_items'][$idx]['title']    = $title;
                $_data['_items'][$idx]['load_url'] = "{$_conf['jrCore_base_url']}/{$_x[1]}/parse/urlscan_player/{$_x[2]}/__ajax=1";
            }
            else {
                $_data['_items'][$idx]['title']    = ucfirst(str_replace('-', ' ', $_x[2]));
                $_data['_items'][$idx]['load_url'] = "{$_conf['jrCore_base_url']}/{$_x[1]}/parse/urlscan_player/0/{$_x[2]}/__ajax=1";
            }
            $_data['_items'][$idx]['url'] = $_args['url'];
        }
    }
    return $_data;
}

/**
 * Replace titles with image names
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrGallery' && isset($_data['_items'])) {
        foreach ($_data['_items'] as $k => $v) {

            // Figure title
            if (isset($v['gallery_image_title']) && strlen($v['gallery_image_title']) > 0) {
                $_data['_items'][$k]['gallery_alt_text'] = jrCore_entity_string($v['gallery_image_title']);
            }
            else {
                // use caption for alt|title if set
                if (isset($v['gallery_caption']) && strlen($v['gallery_caption']) > 0) {
                    $_data['_items'][$k]['gallery_alt_text'] = jrCore_entity_string(jrCore_strip_html($v['gallery_caption']));
                }
                else {
                    $_data['_items'][$k]['gallery_alt_text'] = jrCore_entity_string(jrGallery_title_name($v['gallery_image_name']));
                }
            }
        }
    }
    return $_data;
}

/**
 * Return gallery file field that a price can be added to
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_add_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module/View => File Field
    $_data['jrGallery/detail'] = 'gallery_image';
    return $_data;
}

/**
 * Return audio_album field for Bundle module
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_get_bundle_field_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrGallery'] = 'gallery_image';
    return $_data;
}

/**
 * Return gallery file bundle fields for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_add_bundle_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module/View => array(Bundle Title field, Bundle File field)
    $_data['jrGallery/create'] = array(
        'title' => 'gallery_title',
        'field' => 'gallery_image'
    );
    $_data['jrGallery/update'] = array(
        'title' => 'gallery_title',
        'field' => 'gallery_image'
    );
    return $_data;
}

/**
 * Add gallery image download row to My Items
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrGallery_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrGallery') {
        $url               = jrCore_get_module_url('jrGallery');
        $_data[2]['title'] = $_args['gallery_image_name'];
        $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/gallery_image/{$_args['_item_id']}')");
    }
    return $_data;
}

/**
 * for the jrEmbed module to add a gallery tab.
 * jrVideo_tinymce_popup_listener
 * Adds videos to the popup tinymce editor for insertion into pages
 *
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 *
 * @return array
 */
function jrGallery_tinymce_popup_listener($_data, $_user, $_conf, $_args, $event)
{
    $flag_found = false;
    //over-ride any existing jrVideo setting.
    foreach ($_data as $k => $tab) {
        if ($tab['module'] == 'jrGallery') {
            $_data[$k]['tab_location'] = 'jrGallery';
            $_data[$k]['tab_tpl']      = 'tab_jrGallery.tpl';
            $flag_found                = true;
        }
    }

    //this modules tab was not set, so set it.
    if (!$flag_found) {
        $_data[] = array(
            'module'       => 'jrGallery',
            'name'         => 'gallery',
            'tab_location' => 'jrGallery',
            'tab_tpl'      => 'tab_jrGallery.tpl',
        );
    }

    return $_data;
}

//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Create a clean URL from a Gallery Image name
 * @param $name string image name
 * @return string
 */
function jrGallery_url_name($name)
{
    // Note @ for "Detected an illegal character in input string"
    $str = @iconv('UTF-8', 'ASCII//TRANSLIT', substr(trim($name), 0, 128));
    $str = preg_replace("/[^a-zA-Z0-9\/\._| -]/", '', $str);
    $str = strtolower(trim($str, '-'));
    $str = trim(trim(preg_replace("/[\/_| -]+/", '-', $str)), '-');
    $str = preg_replace('/\\.[^.\\s]{3,4}$/', '', $str);
    if (strlen($str) === 0) {
        // We may have removed everything - rawurlencode
        $str = rawurlencode(jrCore_str_to_lower(str_replace(array('"', "'", ' ', '&', '@', '/', '[', ']', '(', ')'), '-', $name)));
    }
    return trim(preg_replace('/-+/', '-', $str), '-');
}

/**
 * Create a clean TITLE from a Gallery Image name
 * @param $name string image name
 * @return string
 */
function jrGallery_title_name($name)
{
    // Note @ for "Detected an illegal character in input string"
    $str = @iconv('UTF-8', 'ASCII//TRANSLIT', substr(trim($name), 0, 128));
    $str = preg_replace("/[^a-zA-Z0-9\/\._| -]/", '', $str);
    $str = strtolower(trim($str, '-'));
    $str = trim(trim(preg_replace("/[\/_| -]+/", ' ', $str)), ' ');
    $str = preg_replace('/\\.[^.\\s]{3,4}$/', '', $str);
    if (strlen($str) === 0) {
        // We may have removed everything - rawurlencode
        $str = rawurlencode(jrCore_str_to_lower(str_replace(array('"', "'", ' ', '&', '@', '/', '[', ']', '(', ')'), ' ', $name)));
    }
    return trim(preg_replace('/-+/', ' ', $str), ' ');
}

/**
 * Get unique array of gallery titles for specific profile_id
 * @param $profile_id
 * @return array
 */
function jrGallery_get_gallery_titles($profile_id)
{
    // Let's get other galleries this profile has created so we can allow the
    // image to be moved to a new gallery if they want
    $_params = array(
        'search'      => array(
            "_profile_id = {$profile_id}"
        ),
        'return_keys' => array(
            'gallery_title'
        ),
        'group_by'    => 'gallery_title',
        'limit'       => 200
    );
    $_gt     = jrCore_db_search_items('jrGallery', $_params);
    if (isset($_gt) && is_array($_gt) && isset($_gt['_items']) && is_array($_gt['_items'])) {
        $_og = array();
        foreach ($_gt['_items'] as $_itm) {
            $_og["{$_itm['gallery_title']}"] = $_itm['gallery_title'];
        }
        return $_og;
    }
    return false;
}

/**
 * Get a unique gallery image URL
 * @param $item array Gallery image data array
 * @return string
 */
function jrGallery_get_gallery_image_url($item)
{
    global $_conf;
    $mrl = jrCore_get_module_url('jrGallery');
    $url = "{$_conf['jrCore_base_url']}/{$item['profile_url']}/{$mrl}/{$item['_item_id']}/";
    if (isset($item['gallery_image_title_url']) && strlen($item['gallery_image_title_url']) > 0) {
        $url .= $item['gallery_image_title_url'];
    }
    elseif (isset($item['gallery_caption']) && strlen($item['gallery_caption']) > 0) {
        $url .= jrCore_url_string(substr(jrCore_strip_html($item['gallery_caption']), 0, 128));
    }
    else {
        $url .= jrGallery_url_name($item['gallery_image_name']);
    }
    return $url;
}

//---------------------------------------------------------
// SMARTY
//---------------------------------------------------------

/**
 * Get an image edit key for the aviary editor
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGallery_get_image_edit_key($params, $smarty)
{
    if (!isset($params['item_id'])) {
        return jrCore_smarty_missing_error('item_id');
    }
    if (!jrCore_checktype($params['item_id'], 'number_nz')) {
        return jrCore_smarty_invalid_error('item_id');
    }
    $key = mt_rand(0, 1000000000);
    jrCore_set_temp_value('jrGallery', "image_edit_key_{$key}", $params['item_id']);

    // cleanup old keys
    $tbl = jrCore_db_table_name('jrCore', 'temp');
    $req = "DELETE FROM {$tbl} WHERE temp_module = 'jrGallery' AND temp_updated < " . (time() - 600) . " AND temp_key LIKE 'image_edit_key_%'";
    jrCore_db_query($req);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $key);
        return '';
    }
    return $key;
}

/**
 * Get a unique gallery image URL
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGallery_get_gallery_image_url($params, $smarty)
{
    if (isset($params['item']) || !is_array($params['item'])) {
        jrCore_smarty_missing_error('item');
    }
    $out = jrGallery_get_gallery_image_url($params['item']);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show Download Image button
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrGallery_download_button($params, $smarty)
{
    global $_conf;
    if (!isset($params['item']) || !is_array($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    $out = '';
    $_it = $params['item'];
    if (jrCore_checktype($_it['gallery_image_size'], 'number_nz')) {

        // We have a valid audio file - check for allowed downloads
        if (isset($_conf['jrGallery_download']) && $_conf['jrGallery_download'] == 'on') {

            $allow = false;
            if (jrUser_can_edit_item($_it)) {
                // Admins and profile owners can always download
                $allow = true;
            }
            // NOTE: If an gallery item has NO PRICE, but is part of a BUNDLE, and is
            // not marked "Bundle Only" AND we allow downloads, show download button
            elseif ((!isset($_it['gallery_image_item_price']) || strlen($_it['gallery_image_item_price']) === 0 || $_it['gallery_image_item_price'] == 0) && (!isset($_it['gallery_bundle_only']) || $_it['gallery_bundle_only'] != 'on')) {
                $allow = true;
            }
            elseif (isset($_it['gallery_bundle_only']) && $_it['gallery_bundle_only'] == 'on') {
                $allow = false;
            }
            // NOTE: gallery_image_item_price is already checked in core download magic view
            // We just need to check to see if this gallery image is part of a paid bundle
            elseif (isset($_it['gallery_image_item_bundle']) && strlen($_it['gallery_image_item_bundle']) > 0) {
                $_id = array();
                foreach (explode(',', $_it['gallery_image_item_bundle']) as $bid) {
                    $_id[] = (int) $bid;
                }
                $_bi = jrCore_db_get_multiple_items('jrFoxyCartBundle', $_id, array('bundle_item_price'));
                if ($_bi && is_array($_bi)) {
                    $block = false;
                    foreach ($_bi as $_bun) {
                        if (isset($_bun['bundle_item_price']) && $_bun['bundle_item_price'] > 0) {
                            $block = true;
                            break;
                        }
                    }
                    if (!$block) {
                        $allow = true;
                    }
                }
            }
            else {
                $allow = true;
            }
            if ($allow) {
                $url = jrCore_get_module_url('jrGallery');
                $ttl = (isset($_it['gallery_image_title'])) ? $_it['gallery_image_title'] : $_it['gallery_image_name'];
                $ttl = jrGallery_url_name($ttl);
                $out = "<a href=\"{$_conf['jrCore_base_url']}/{$url}/download/gallery_image/{$_it['_item_id']}/{$ttl}\">" . jrCore_get_icon_html('download') .'</a>';
            }
        }
    }
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
