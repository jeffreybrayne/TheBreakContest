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
 * meta
 */
function jrAudio_meta()
{
    $_tmp = array(
        'name'        => 'Audio Support',
        'url'         => 'audio',
        'version'     => '1.4.16',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create and stream Audio files such as songs and audio books',
        'category'    => 'media',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrAudio_init()
{
    global $_conf;

    // Tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrAudio', 'import', array('Import Audio', 'Import ID3 tagged MP3 audio files from the filesystem'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrAudio', 'reconvert', array('Convert Audio', 'Run audio conversions for existing audio files based on Quota Settings'));

    // We provide support for the "audio" form field type
    jrCore_register_module_feature('jrCore', 'form_field', 'jrAudio', 'audio');

    // We have some small custom CSS for our update page
    jrCore_register_module_feature('jrCore', 'css', 'jrAudio', 'jrAudio.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrAudio', 'jquery.sortable.min.js');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrAudio', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrAudio', 'update');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrAudio', 'create_album');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrAudio', 'update_album');

    // Core module support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrAudio', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrAudio', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrAudio', 'on');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrAudio', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrAudio', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrAudio', 'update', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrAudio', 'create_album', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrAudio', 'update_album', 'item_action.tpl');

    // Core media player support
    jrCore_register_module_feature('jrCore', 'media_player', 'jrAudio', 'jrAudio_blue_monday', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrAudio', 'jrAudio_button', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrAudio', 'jrAudio_player_dark', 'audio');

    // We support audio conversions
    $max = (isset($_conf['jrAudio_conversion_worker_count'])) ? intval($_conf['jrAudio_conversion_worker_count']) : 1;
    jrCore_register_queue_worker('jrAudio', 'audio_conversions', 'jrAudio_convert_file', 0, $max);
    jrCore_register_queue_worker('jrAudio', 'audio_sample', 'jrAudio_create_audio_sample', 0, $max);

    // Event Listeners
    jrCore_register_event_listener('jrCore', 'system_check', 'jrAudio_system_check_listener');
    jrCore_register_event_listener('jrCore', 'stream_file', 'jrAudio_stream_file_listener');
    jrCore_register_event_listener('jrCore', 'download_file', 'jrAudio_download_file_listener');
    jrCore_register_event_listener('jrCore', 'get_save_data', 'jrAudio_get_save_data_listener');
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrAudio_db_search_params_listener');

    jrCore_register_event_listener('jrEmbed', 'tinymce_popup', 'jrAudio_tinymce_popup_listener');

    jrCore_register_event_listener('jrFoxyCart', 'add_price_field', 'jrAudio_add_price_field_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'jrAudio_my_items_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'jrAudio_adding_item_to_purchase_history_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'jrAudio_my_earnings_row_listener');

    jrCore_register_module_feature('jrFoxyCartBundle', 'visible_support', 'jrAudio', true);
    jrCore_register_event_listener('jrFoxyCartBundle', 'get_album_field', 'jrAudio_get_album_field_listener');
    jrCore_register_event_listener('jrFoxyCartBundle', 'add_bundle_price_field', 'jrAudio_add_bundle_price_field_listener');
    jrCore_register_event_listener('jrFoxyCartBundle', 'add_bundle_item', 'jrAudio_add_bundle_item_listener');
    jrCore_register_event_listener('jrFoxyCartBundle', 'delete_bundle_item', 'jrAudio_delete_bundle_item_listener');

    // We listen for the jrUrlScan 'url_found' trigger and if its an audio url, add appropriate data to its array
    jrCore_register_event_listener('jrUrlScan', 'url_found', 'jrAudio_url_found_listener');

    // We can be hidden but included in bundles
    jrCore_register_module_feature('jrFoxyCartBundle', 'bundle_only_support', 'jrAudio', 'create');
    jrCore_register_module_feature('jrFoxyCartBundle', 'bundle_only_support', 'jrAudio', 'update');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrAudio', 'audio_title,audio_genre,audio_album', 52);

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrAudio', 'profile_jrAudio_item_count', 52);

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrAudio_network_share_text_listener');

    // RSS Format
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrAudio_create_rss_feed_listener');

    // Core item buttons
    $_tmp = array(
        'title'  => 'create album button',
        'icon'   => 'star2',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrAudio', 'jrAudio_create_album_button', $_tmp);

    $_tmp = array(
        'title'  => 'download audio button',
        'icon'   => 'download',
        'active' => 'on',
        'group'  => 'user'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrAudio', 'jrAudio_item_download_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrAudio', 'jrAudio_item_download_button', $_tmp);

    return true;
}

//---------------------------------------------------------
// AUDIO ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "create album" button for audio index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrAudio_create_album_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrAudio') {
        if ($test_only) {
            return true;
        }
        if (jrProfile_is_profile_owner($_args['profile_id'])) {
            $url = jrCore_get_module_url('jrAudio');
            $_rt = array(
                'url'  => "{$_conf['jrCore_base_url']}/{$url}/create_album",
                'icon' => 'star2',
                'alt'  => 35
            );
            return $_rt;
        }
    }
    return false;
}

/**
 * Return "download" button for the audio item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrAudio_item_download_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrAudio') {
        if ($test_only) {
            return true;
        }
        if (jrCore_checktype($_item['audio_file_size'], 'number_nz')) {

            // We have a valid audio file - check for allowed downloads
            if (isset($_conf['jrAudio_block_download']) && $_conf['jrAudio_block_download'] == 'off') {

                $allow = false;
                if (jrUser_can_edit_item($_item)) {
                    // Admins and profile owners can always download
                    $allow = true;
                }
                // NOTE: If an audio item has NO PRICE, but is part of a BUNDLE, and is
                // not marked "Bundle Only" AND we allow downloads, show download button
                elseif ((!isset($_item['audio_file_item_price']) || strlen($_item['audio_file_item_price']) === 0 || $_item['audio_file_item_price'] == 0) && (!isset($_item['audio_bundle_only']) || $_item['audio_bundle_only'] != 'on')) {
                    $allow = true;
                }
                elseif (isset($_item['audio_bundle_only']) && $_item['audio_bundle_only'] == 'on') {
                    $allow = false;
                }
                // NOTE: audio_file_item_price is already checked in core download magic view
                // We just need to check to see if this audio item is part of a paid bundle
                elseif (isset($_item['audio_file_item_bundle']) && strlen($_item['audio_file_item_bundle']) > 0) {
                    $_id = array();
                    foreach (explode(',', $_item['audio_file_item_bundle']) as $bid) {
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
                    $url = jrCore_get_module_url('jrAudio');
                    $_rt = array(
                        'url'  => "{$_conf['jrCore_base_url']}/{$url}/download/audio_file/{$_item['_item_id']}/{$_item['audio_title_url']}",
                        'icon' => 'download',
                        'alt'  => 65
                    );
                    return $_rt;
                }
            }
        }
    }
    return false;
}

//---------------------------------------------------------
// AUDIO FORM FUNCTIONS
//---------------------------------------------------------

/**
 * Display an Audio File entry in a form
 * @param array $_field Form Field information array
 * @param array $_att Additional attributes for the field
 * @return bool
 */
function jrAudio_form_field_audio_display($_field, $_att = null)
{
    global $_user, $_post;
    // Get existing audio if we have one - the "value" we get will
    // be the unique id for the audio we are loading.
    $htm = '';
    if (!isset($_field['value']) || !is_array($_field['value'])) {
        // If we are doing an update - we need the full item
        $_field['value'] = jrCore_get_flag('jrcore_form_create_values');
    }
    if (isset($_field['value']) && is_array($_field['value'])) {
        // If we are not active, don't show the player
        if (isset($_field['value']['audio_active']) && $_field['value']['audio_active'] != 'on') {
            // We have not been converted yet
            return true;
        }
        $nam = $_field['name'];
        if (isset($_field['value']["{$nam}_size"]) && jrCore_checktype($_field['value']["{$nam}_size"], 'number_nz')) {
            $_key = array('name', 'original_name', 'type', 'size', 'time', 'extension', 'bitrate', 'smprate', 'length');
            $_rep = array(
                'item'       => $_field['value'],
                '_item_id'   => $_field['value']['_item_id'],
                'field_name' => $nam,
                'module'     => $_post['module']
            );
            foreach ($_key as $v) {
                $_rep['item'][$v] = (isset($_field['value']["{$nam}_{$v}"])) ? $_field['value']["{$nam}_{$v}"] : '';
            }
            $htm = jrCore_parse_template('audio_update.tpl', $_rep, 'jrAudio');
            // Next - we need to see if this is a MULTIPLE upload field - if it is, we need
            // need to show a audio box for EACH audio file stored for this item
            $i = 2;
            while (true) {
                if (isset($_field['value']["{$nam}_{$i}_size"]) && jrCore_checktype($_field['value']["{$nam}_{$i}_size"], 'number_nz')) {
                    $_rep = array(
                        'item' => array(
                            '_item_id'   => $_field['value']['_item_id'],
                            'field_name' => "{$nam}_{$i}"
                        )
                    );
                    foreach ($_key as $v) {
                        $_rep['item']["audio_file_{$v}"] = $_field['value']["{$nam}_{$i}_{$v}"];
                    }
                    $htm .= jrCore_parse_template('audio_update.tpl', $_rep, 'jrAudio');
                }
                else {
                    break;
                }
                $i++;
            }
        }
    }
    $_field['html']     = $htm;
    $_field['type']     = 'audio';
    $_field['template'] = 'form_field_elements.tpl';
    // We have a file upload - we need to turn on the progress meter if enabled
    $_field['multiple'] = (isset($_field['multiple'])) ? $_field['multiple'] : false;

    // Make sure we have a default
    if (!isset($_user['quota_jrAudio_allowed_audio_types']) || strlen($_user['quota_jrAudio_allowed_audio_types']) < 2) {
        $_user['quota_jrAudio_allowed_audio_types'] = 'mp3';
    }
    $_field = jrCore_enable_meter_support($_field, $_user['quota_jrAudio_allowed_audio_types'], jrCore_get_max_allowed_upload($_user['quota_jrCore_max_upload_size']), $_field['multiple']);
    // add to our page element
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return string
 */
function jrAudio_form_field_audio_form_designer_options()
{
    return array(
        'disable_validation'  => true,
        'disable_default'     => true,
        'disable_options'     => true,
        'disable_min_and_max' => true
    );
}

/**
 * Additional form field HTML attributes that can be passed in via the form
 */
function jrAudio_form_field_audio_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress');
}

/**
 * Check to be sure validation is on if field is required
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return bool
 */
function jrAudio_form_field_audio_params($_field, $_post)
{
    if (!isset($_field['validate'])) {
        $_field['validate'] = 'not_empty';
    }
    if (!isset($_field['error_msg'])) {
        $_lang               = jrUser_load_lang_strings();
        $_field['error_msg'] = $_lang['jrAudio'][58];
    }
    return $_field;
}

/**
 * jrAudio_form_field_audio_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return bool
 */
function jrAudio_form_field_audio_is_empty($_field, $_post)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        return true;
    }
    // Okay looks good
    return false;
}

/**
 * jrAudio_form_field_audio_validate
 * Verify we get an uploaded file if one is required in the form
 * @param $_field array Field Information
 * @param $_post array Parsed $_REQUEST
 * @param $e_msg string Error message for field if in error
 * @return bool
 */
function jrAudio_form_field_audio_validate($_field, $_post, $e_msg)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        jrCore_set_form_notice('error', $e_msg);
        return false;
    }
    // Okay looks good
    return $_post;
}

//---------------------------------------------------------
// AUDIO FUNCTIONS
//---------------------------------------------------------

/**
 * Create a Download Album button in a template
 * @param array $params function params
 * @param object $smarty Smarty Object
 * @return string
 */
function smarty_function_jrAudio_download_album_button($params, &$smarty)
{
    global $_conf;
    if (isset($_conf['jrAudio_block_album_download']) && $_conf['jrAudio_block_album_download'] == 'on' && !jrUser_is_admin()) {
        return '';
    }
    if (!isset($params['items']) || !is_array($params['items'])) {
        return 'jrAudio_download_album_button: items array required';
    }
    if (empty($params['icon'])) {
        $params['icon'] = 'download';
    }
    $url = false;
    $pid = false;
    foreach ($params['items'] as $_it) {
        if (isset($_it['audio_file_item_price']) && strlen($_it['audio_file_item_price']) > 0) {
            // If we have items in the album that are PRICED, we do not show download button
            return '';
        }
        if (!$url) {
            $url = $_it['audio_album_url'];
            $pid = $_it['_profile_id'];
        }
    }
    if (!$url) {
        // no allowed downloads
        return '';
    }
    $_ln = jrUser_load_lang_strings();
    $out = jrCore_get_icon_html($params['icon']);
    $out = "<a href=\"{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrAudio') ."/download_album/{$pid}/{$url}\" title=\"" . jrCore_entity_string($_ln['jrAudio'][64]) . "\">{$out}</a>";
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Delete an existing Album ZIP file
 * @param $profile_id
 * @param $album_url
 * @return bool
 */
function jrAudio_delete_album_zip_file($profile_id, $album_url)
{
    $dir = jrCore_get_media_directory($profile_id);
    $fil = "{$dir}/album_{$album_url}.zip";
    if (is_file($fil)) {
        unlink($fil);
    }
    return true;
}

/**
 * Checks to be sure ID3V2 is installed and working
 * @param bool $notice Set to FALSE to return boolean instead of show notice
 * @return bool
 */
function jrAudio_check_id3_install($notice = true)
{
    global $_conf;
    // Our audio module requires SOX - make sure it is executable
    $id3 = APP_DIR . "/modules/jrAudio/tools/id3v2";
    if (isset($_conf['jrAudio_id3v2_binary'])) {
        $id3 = $_conf['jrAudio_id3v2_binary'];
    }
    if (is_file($id3) && !is_executable($id3)) {
        // Try to set permissions if we can...
        @chmod($id3, 0755);
    }
    if (jrUser_is_master() && (!is_file($id3) || !is_executable($id3))) {
        if ($notice) {
            $show = htmlentities(str_replace(APP_DIR . '/', '', $id3));
            jrCore_set_form_notice('error', 'The ID3v2 binary: ' . $show . ' is not executable! Set permissions on the file to 755 or 555.');
        }
        return false;
    }
    return $id3;
}

/**
 * Checks to be sure SOX is installed and working
 * @param bool $notice Set to FALSE to return boolean instead of show notice
 * @return bool
 */
function jrAudio_check_sox_install($notice = true)
{
    global $_conf;
    // Our audio module requires SOX - make sure it is executable
    $sox = APP_DIR . "/modules/jrAudio/tools/sox";
    if (isset($_conf['jrAudio_sox_binary'])) {
        $sox = $_conf['jrAudio_sox_binary'];
    }
    if (is_file($sox) && !is_executable($sox)) {
        // Try to set permissions if we can...
        @chmod($sox, 0755);
    }
    if (jrUser_is_master() && (!is_file($sox) || !is_executable($sox))) {
        if ($notice) {
            $show = htmlentities(str_replace(APP_DIR . '/', '', $sox));
            jrCore_set_form_notice('error', 'The SOX binary: ' . $show . ' is not executable! Set permissions on the file to 755 or 555.');
        }
        return false;
    }
    return $sox;
}

/**
 * Add ID3v2 Tags to MP3 file
 * @param $input_file string MP3 file to write tags to
 * @param $_tags array Tags - Data array for Audio ID
 * @return bool
 */
function jrAudio_tag_audio_file($input_file, $_tags = null)
{
    // Make sure our id3v2 binary is available
    $id3 = jrAudio_check_id3_install();
    if (!is_file($id3)) {
        return false;
    }
    // Frames we support - see http://id3.org/id3v2.3.0
    if (is_null($_tags) || !is_array($_tags)) {
        return false;
    }
    // Must be an MP3
    if (jrCore_file_extension($input_file) !== 'mp3') {
        return true;
    }

    // Trigger tags event
    $_args = array(
        'input_file'   => $input_file,
        'id3v2_binary' => $id3
    );
    $_tags = jrCore_trigger_event('jrAudio', 'tag_audio_file', $_tags, $_args);
    if (!$_tags) {
        // Listener blocked our write
        return false;
    }

    // See if we are being passed a Genre
    if (isset($_tags['TCON']) && (!jrCore_checktype($_tags['TCON'], 'number_nz') || $_tags['TCON'] > 147)) {
        $_gen = array_flip(jrAudio_get_id3_genres());
        if (!isset($_gen["{$_tags['TCON']}"])) {
            $_tags['TCON'] = 255;
        }
    }

    // Delete any attached image
    ob_start();
    system("{$id3} --delete-all {$input_file} >/dev/null 2>/dev/null", $ret);
    ob_end_clean();

    // Error file
    $dir = jrCore_get_module_cache_dir('jrAudio');
    $tmp = tempnam($dir, 'id3_errors_');

    // Add Tags
    $cmd = $id3;
    foreach ($_tags as $tag => $txt) {
        if (function_exists('utf8_encode')) {
            $cmd .= " --{$tag} \"". utf8_encode($txt) ."\"";
        }
        else {
            $cmd .= " --{$tag} \"". jrCore_entity_string($txt) ."\"";
        }
    }
    ob_start();
    system("{$cmd} {$input_file} >/dev/null 2>{$tmp}", $ret);
    ob_end_clean();
    // See if we had errors
    if (is_file($tmp) && filesize($tmp) > 0) {
        if (stristr(file_get_contents($tmp), 'FAIL')) {
            jrCore_logger('MAJ', "errors encountered ID3 tagging audio file", $tmp);
        }
    }
    @unlink($tmp);
    return true;
}

/**
 * Create a "sample" of an existing MP3 file
 * @param $profile_id integer Unique Profile ID that audio file belongs to
 * @param $audio_id integer Unique Audio ID to create sample for
 * @param $field string Audio File field to create sample from
 * @param $_audio array Audio information array
 * @param $sample_length integer Length of sample (in seconds)
 * @return string
 */
function jrAudio_create_sample($profile_id, $audio_id, $field, $_audio, $sample_length = 60)
{
    global $_conf;
    // Make sure our sox binary is available
    $sox = jrAudio_check_sox_install();
    if (!is_file($sox)) {
        return false;
    }

    // Requested fade in length (in seconds)
    if (isset($_conf['jrAudio_sample_length']) && jrCore_checktype($_conf['jrAudio_sample_length'], 'number_nn')) {
        $sample_length = $_conf['jrAudio_sample_length'];
    }
    $sample_length = (int) $sample_length;
    if ($sample_length === 0) {
        // Nothing to do - return
        return false;
    }

    // Make sure this audio item exists
    if (!isset($_audio) || !is_array($_audio)) {
        return false;
    }

    // Make sure input MP3 file exists
    $input_file = jrCore_get_media_file_path('jrAudio', $field, $_audio);
    if (!jrCore_media_file_exists($profile_id, $input_file)) {
        return false;
    }

    // Our command to create our sample file
    // "q" for quarter of a sine wave
    // "h" for half a sine wave
    // "t" for linear (‘triangular’) slope
    // "l" for logarithmic
    // "p" for inverted parabola

    // See how long of a sample has been requested - we want to try and snip
    // a section out from the middle of the song for our sample.
    list($h, $m, $s) = explode(':', $_audio["{$field}_length"]);
    $total = (($h * 60) * 60) + ($m * 60) + $s;
    $begin = round($total / 2);

    // If our song is shorter than our requested sample length
    if ($total < ($sample_length * 2)) {
        $sample_length = round($total / 2);
        $begin         = 0;
    }
    elseif (($begin + $sample_length) > $total) {
        // Our sample would extend beyond the end of the file - slide it back
        $begin = 0;
    }

    // See how quickly we need to fade in/out
    $fade = 2;
    if ($sample_length < 5) {
        $sample_length = 5;
        $fade          = 1;
    }

    // Error file
    $dir = jrCore_get_module_cache_dir('jrAudio');
    $tmp = tempnam($dir, 'sox_errors_');

    // Convert MP3 sample file
    ob_start();
    system("{$sox} {$input_file} {$input_file}.sample.mp3 trim {$begin} {$sample_length} fade t 00:00:0{$fade}.0 00:00:{$sample_length}.0 00:00:0{$fade}.0 >/dev/null 2>{$tmp}", $ret);
    ob_end_clean();
    // See if we had errors
    if (is_file($tmp) && filesize($tmp) > 0) {
        if (stristr(file_get_contents($tmp), 'FAIL')) {
            jrCore_logger('MAJ', "errors encountered creating MP3 audio sample", $tmp);
        }
    }
    @unlink($tmp);
    if (isset($_conf['jrAudio_conversion_format']) && strpos($_conf['jrAudio_conversion_format'], 'ogg')) {
        // Convert OGG sample file
        $input_file = str_replace('.mp3', '.ogg', $input_file);
        if (jrCore_media_file_exists($_audio['_profile_id'], basename($input_file))) {
            ob_start();
            system("{$sox} {$input_file} {$input_file}.sample.ogg trim {$begin} {$sample_length} fade t 00:00:0{$fade}.0 00:00:{$sample_length}.0 00:00:0{$fade}.0 >/dev/null 2>{$tmp}", $ret);
            ob_end_clean();
            // See if we had errors
            if (is_file($tmp) && filesize($tmp) > 0) {
                $tmp = file_get_contents($tmp);
                if (stristr($tmp, 'FAIL')) {
                    jrCore_logger('MAJ', "errors encountered creating OGG audio sample", $tmp);
                }
            }
        }
    }
    @unlink($tmp);
    return $sample_length;
}

/**
 * Get an embedded APIC audio image
 * @param $audio_file string Audio File to get embedded image from
 * @return mixed
 */
function jrAudio_get_apic_image($audio_file)
{
    $ffmpeg = jrCore_check_ffmpeg_install();
    if (!is_file($ffmpeg)) {
        return false;
    }
    $dir = jrCore_get_module_cache_dir('jrAudio');
    $tmp = tempnam($dir, 'media_meta_');

    ob_start();
    $audio_file = str_replace('"', '\"', $audio_file);
    system("nice -n 9 {$ffmpeg} -threads 1 -i \"{$audio_file}\" >/dev/null 2>{$tmp}", $ret);
    ob_end_clean();

    if (!isset($tmp) || !is_file($tmp)) {
        return false;
    }
    $_out = array();
    $_tmp = file($tmp);
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $line) {
            // Stream #0:0: Audio: mp3, 44100 Hz, stereo, s16, 256 kb/s
            // Stream #0:1: Video: png, rgb24, 400x400, 90k tbr, 90k tbn, 90k tbc
            $line = trim($line);
            if (strpos($line, 'Video:')) {
                $_out['extension'] = trim(trim(jrCore_string_field($line, 4)), ',');
                // get stream for mapping
                $str_num = jrCore_string_field($line, 2);
                $str_num = trim(trim(trim($str_num), '#'), ':');
                ob_start();
                system("nice -n 9 {$ffmpeg} -threads 1 -i \"{$audio_file}\" -map {$str_num} \"{$audio_file}.{$_out['extension']}\" >/dev/null 2>/dev/null", $ret);
                ob_end_clean();
                if (is_file("{$audio_file}.{$_out['extension']}")) {
                    // If this is a PNG image, convert to JPG to save space
                    if (strtolower($_out['extension']) === 'png') {
                        $src = imagecreatefrompng("{$audio_file}.png");
                        imagejpeg($src, "{$audio_file}.jpg", 85);
                        imagedestroy($src);
                        unlink("{$audio_file}.png");
                        $_out['extension'] = 'jpg';
                    }
                    $_out['image_data'] = file_get_contents("{$audio_file}.{$_out['extension']}");
                    unlink("{$audio_file}.{$_out['extension']}");
                }
            }
        }
    }
    @unlink($tmp);
    if (isset($_out) && is_array($_out) && isset($_out['image_data']) && strlen($_out['image_data']) > 50) {
        return $_out;
    }
    return false;
}

/**
 * Create an Audio Sample for an item
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrAudio_create_audio_sample($_queue)
{
    $_it = jrCore_db_get_item('jrAudio', $_queue['item_id']);
    if ($_it && is_array($_it)) {
        $length = jrAudio_create_sample($_queue['profile_id'], $_queue['item_id'], $_queue['file_name'], $_it, 60);
        if ($length && $length > 0) {
            $_data = array(
                "{$_queue['file_name']}_sample_length" => $length
            );
            jrCore_db_update_item('jrAudio', $_queue['item_id'], $_data);
        }
    }
    return true;
}

/**
 * Convert an audio file from one format to another
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrAudio_convert_file($_queue)
{
    global $_conf;
    if (!isset($_queue) || !is_array($_queue)) {
        return false;
    }

    // Our queue entry will contain the item ID and the Quota ID
    // for the item being submitted for conversion
    $_qt = jrProfile_get_quota($_queue['quota_id']);
    if (!isset($_qt) || !is_array($_qt)) {
        jrCore_logger('CRI', "quota does not exist for quota_id in queue entry: {$_queue['quota_id']}", $_queue);
        return true; // Bad queue entry - remove it
    }

    // Make sure profile is valid
    $_pr = jrCore_db_get_item('jrProfile', $_queue['profile_id']);
    if (!isset($_pr) || !is_array($_pr)) {
        jrCore_logger('CRI', "profile does not exist for profile_id in queue entry: {$_queue['profile_id']}", $_queue);
        return true; // Bad queue entry - remove it
    }

    // Get the item
    $_it = jrCore_db_get_item('jrAudio', $_queue['item_id'], true);
    if (!isset($_it) || !is_array($_it)) {
        jrCore_logger('CRI', "item_id does not exist for queue entry: {$_queue['item_id']}", $_queue);
        return true; // Bad queue entry - remove it
    }

    // If this audio file is ALREADY an MP3 file, we need to find it's bitrate.  If it is LESS than
    // what we are encoding for, then we don't want to "up sample" the file or it sounds like crap.
    // But we need to check for embedded images and strip if needed
    if (isset($_it["{$_queue['file_name']}_extension"]) && $_it["{$_queue['file_name']}_extension"] == 'mp3') {
        // We have an MP3 - check bitrate
        if (isset($_it["{$_queue['file_name']}_bitrate"]) && $_it["{$_queue['file_name']}_bitrate"] < $_queue['bitrate']) {
            $_queue['bitrate'] = $_it["{$_queue['file_name']}_bitrate"]; // Don't up sample
        }
    }

    // Make sure the file we are converting exists
    $input_save = true;
    $input_file = jrCore_get_media_file_path('jrAudio', $_queue['file_name'], $_it);
    $input_orig = $input_file;
    if (isset($_queue['reconvert']) && $_queue['reconvert'] == '1') {
        // We are doing re-conversions - do not overwrite original file
        $input_save = false;
        $input_name = $_queue['file_name'];
        // See if have an original - always do our work off the original
        if (isset($_it["{$input_name}_original_size"]) && jrCore_checktype($_it["{$input_name}_original_size"], 'number_nz')) {
            $ext = $_it["{$input_name}_original_extension"];
            if (is_file("{$input_file}.original.{$ext}")) {
                $input_orig = "{$input_file}.original.{$ext}";
            }
        }
    }
    if (!jrCore_media_file_exists($_queue['profile_id'], $input_file)) {
        jrCore_logger('CRI', "invalid item_id received in queue entry: {$_queue['item_id']} - unable to open input file: {$_queue['file_name']} for reading");
        return true; // Bad queue entry - remove it
    }

    // Log start time
    $start = explode(' ', microtime());
    $start = $start[1] + $start[0];

    // See what type of file we are converting and if we support it
    $ext = jrCore_file_extension($input_orig);
    if (!is_file(APP_DIR . "/modules/jrAudio/plugins/{$ext}.php")) {
        // We don't support this format
        jrCore_logger('CRI', "invalid file type received for conversion: {$ext} - type is not supported");
        return true; // Bad queue entry - remove it
    }
    require_once APP_DIR . "/modules/jrAudio/plugins/{$ext}.php";

    // First - setup an error file we will use to watch for errors
    $cdr = jrCore_get_module_cache_dir('jrAudio');
    $err = tempnam($cdr, 'conversion');

    // See if we have a decoder
    $func = "jrAudio_{$ext}_decode";
    if (function_exists($func)) {
        $tmp = $func($input_orig, $_queue, $err);
        // If we encounter an error, the plugin will return false.  The
        // plugin is responsible for logging and error checking
        if (!$tmp) {
            unlink($err);
            return 1; // retry
        }
        else {
            // If we have decoded, we use the OUTPUT of the decode step
            // as the new INPUT to the encode step
            $input_orig = $tmp;
        }
    }

    // Next, encode to MP3 if needed
    $func = "jrAudio_mp3_encode";
    require_once APP_DIR . "/modules/jrAudio/plugins/mp3.php";
    if (function_exists($func)) {
        if ($ext != 'mp3' || (isset($_it["{$_queue['file_name']}_bitrate"]) && $_it["{$_queue['file_name']}_bitrate"] != $_queue['bitrate'])) {
            $tmp = $func($input_orig, $_queue, $err);
            // If we encounter an error, the plugin will return false.  The
            // plugin is responsible for logging and error checking
            if (!$tmp) {
                unlink($err);
                return 1; // retry
            }
            if (!is_file($tmp) || filesize($tmp) < 200) {
                jrCore_logger('CRI', "unable to convert mp3 file for: {$_queue['file_name']}/{$_queue['item_id']}", file_get_contents($err));
                return 1; // retry
            }
        }
        else {
            $tmp = "{$input_orig}.mp3";
            jrCore_copy_media_file($_queue['profile_id'], $input_orig, basename($tmp));
        }
        // $tmp is now the NEW (converted) file - we need to replace the
        // existing file with this new one, updated it, and save the old
        // as an "original" file
        if ($input_save) {
            if (!jrCore_rename_media_file($_queue['profile_id'], $input_file, basename($input_file) . '.original.' . $ext)) {
                jrCore_logger('CRI', "unable to save original media file for: {$_queue['file_name']}/{$_queue['item_id']}");
                return 1; // Count as a try
            }
            $input_orig = $input_file . '.original.' . $ext;
        }
        // Next, we need to rename the new MP3 file and update the item
        $input_file = preg_replace("/\\.[^.\\s]{3,4}$/", "", $input_file) . '.mp3';
        if (!jrCore_rename_media_file($_queue['profile_id'], $tmp, basename($input_file))) {
            jrCore_logger('CRI', "unable to rename converted mp3 file for: {$_queue['file_name']}/{$_queue['item_id']}");
            return 1; // Count as a try
        }
        // Update new entries
        $_data = array(
            "audio_active"                              => 'on',
            "{$_queue['file_name']}_name"               => basename($input_file),
            "{$_queue['file_name']}_time"               => time(),
            "{$_queue['file_name']}_size"               => filesize($input_file),
            "{$_queue['file_name']}_type"               => 'audio/mpeg',
            "{$_queue['file_name']}_extension"          => 'mp3',
            "{$_queue['file_name']}_bitrate"            => $_queue['bitrate'],
            "{$_queue['file_name']}_original_name"      => $_it["{$_queue['file_name']}_name"],
            "{$_queue['file_name']}_original_time"      => $_it["{$_queue['file_name']}_time"],
            "{$_queue['file_name']}_original_size"      => $_it["{$_queue['file_name']}_size"],
            "{$_queue['file_name']}_original_type"      => $_it["{$_queue['file_name']}_type"],
            "{$_queue['file_name']}_original_extension" => $_it["{$_queue['file_name']}_extension"],
            "{$_queue['file_name']}_original_bitrate"   => $_it["{$_queue['file_name']}_bitrate"],
        );
        jrCore_db_update_item('jrAudio', $_queue['item_id'], $_data);

        // Tag it
        if (isset($_pr['quota_jrAudio_audio_tag']) && $_pr['quota_jrAudio_audio_tag'] == 'on') {
            $a_url = jrCore_get_module_url('jrAudio');
            $_tags = array(
                'TPE1' => $_pr['profile_name'],
                'TPE2' => $_pr['profile_name'],
                'TIT2' => $_it['audio_title'],
                'TCOP' => 'Copyright ' . strftime('%Y') . " by {$_pr['profile_name']}",
                'TCON' => $_it['audio_genre'],
                'TALB' => $_it['audio_album'],
                'TRCK' => (int) $_it['audio_file_track'],
                'TYER' => strftime('%Y'),
                'COMM' => "Downloaded from {$_conf['jrCore_system_name']}",
                'WOAF' => "{$_conf['jrCore_base_url']}/{$_pr['profile_url']}/{$a_url}/{$_queue['item_id']}/{$_it['audio_title_url']}"
            );
            jrAudio_tag_audio_file($input_file, $_tags);
            if ($input_save && $input_orig) {
                jrAudio_tag_audio_file($input_orig, $_tags);
            }
        }

        // Next, encode OGG alternate if enabled
        if (isset($_conf['jrAudio_conversion_format']) && strpos($_conf['jrAudio_conversion_format'], 'ogg')) {
            $func = "jrAudio_ogg_encode";
            require_once APP_DIR . "/modules/jrAudio/plugins/ogg.php";
            if (function_exists($func)) {
                $tmp = $func($input_orig, $_queue, $err);
                // If we encounter an error, the plugin will return false.  The
                // plugin is responsible for logging and error checking
                if (!$tmp) {
                    unlink($err);
                    return 1; // Count as a try
                }
                if (!is_file($tmp) || filesize($tmp) < 200) {
                    jrCore_logger('CRI', "unable to convert ogg file for: {$_queue['file_name']}/{$_queue['item_id']}", file_get_contents($err));
                    return 1; // Count as a try
                }
                // Next, we need to rename the new MP3 file and update the item
                $input_file = preg_replace("/\\.[^.\\s]{3,4}$/", "", $input_file) . '.ogg';
                if (!jrCore_rename_media_file($_queue['profile_id'], $tmp, basename($input_file))) {
                    jrCore_logger('CRI', "unable to rename converted ogg file for: {$_queue['file_name']}/{$_queue['item_id']}");
                    return 1; // Count as a try
                }
            }
        }
        else {
            // Delete existing ogg file
            $del_file = preg_replace("/\\.[^.\\s]{3,4}$/", "", basename($input_file)) . '.ogg';
            if (jrCore_media_file_exists($_queue['profile_id'], $del_file)) {
                jrCore_delete_media_file($_queue['profile_id'], $del_file);
            }
        }

        // Create our Sample
        if ($_queue['sample']) {
            $length = jrAudio_create_sample($_queue['profile_id'], $_queue['item_id'], $_queue['file_name'], array_merge($_it, $_data), 60);
            if ($length && $length > 0) {
                $_data = array(
                    "{$_queue['file_name']}_sample_length" => $length
                );
                jrCore_db_update_item('jrAudio', $_queue['item_id'], $_data);
            }
        }
        else {
            // We're not creating a sample - make sure any old one is removed
            jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.mp3");
            jrCore_delete_media_file($_queue['profile_id'], "{$input_file}.sample.ogg");
            jrCore_db_delete_item_key('jrAudio', $_queue['item_id'], "{$_queue['file_name']}_sample_length");
        }

        $finish = explode(' ', microtime());
        $finish = $finish[1] + $finish[0];
        $total  = round(($finish - $start), 2);
        jrCore_logger('INF', "converted {$_queue['profile_id']}/{$_queue['item_id']}/{$_queue['file_name']} from {$ext} to MP3 in {$total} seconds");
        jrProfile_reset_cache($_queue['profile_id']);
    }
    // We're done - returning true tells the core to delete the queue entry
    unlink($err);
    return true;
}

/**
 * jrAudio_get_audio_types
 */
function jrAudio_get_audio_types()
{
    $_tmp = glob(APP_DIR . '/modules/jrAudio/plugins/*.php');
    if (!isset($_tmp) || !is_array($_tmp)) {
        return false;
    }
    $_out = array();
    foreach ($_tmp as $file) {
        $name        = str_replace('.php', '', basename($file));
        $_out[$name] = $name;
    }
    return $_out;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Get Meta data for uploaded audio files
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_get_save_data_listener($_data, $_user, $_conf, $_args, $event)
{
    $_fl = jrCore_get_uploaded_media_files('jrAudio');
    if (isset($_fl) && is_array($_fl)) {

        foreach ($_fl as $file) {
            // Make sure it is an audio file
            if (is_file("{$file}.tmp")) {
                $_ty = jrAudio_get_audio_types();
                $ext = jrCore_file_extension(trim(file_get_contents("{$file}.tmp")));
                if (isset($_ty[$ext])) {
                    $prfx = explode('_', basename($file), 2);
                    $prfx = trim(end($prfx));
                    $_tmp = jrCore_get_media_file_metadata($file, $prfx);
                    if (isset($_tmp) && is_array($_tmp)) {
                        if (!isset($_tmp["{$prfx}_track"]) || !jrCore_checktype($_tmp["{$prfx}_track"], 'number_nz')) {
                            $_tmp["{$prfx}_track"] = 1;
                        }
                        $_data = array_merge($_tmp, $_data);
                    }
                    unset($_tmp);
                }
            }
        }
    }
    return $_data;
}

/**
 * Switch to OGG file if requested
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_stream_file_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_conf['jrAudio_conversion_format']) && strstr($_conf['jrAudio_conversion_format'], 'ogg')) {
        // We support OGG files
        if (isset($_args['stream_file']) && strpos($_args['stream_file'], 'mp3') && strpos($_post['_uri'], '/file.ogg')) {
            $_data['stream_file'] = str_replace('.mp3', '.ogg', $_args['stream_file']);
        }
    }
    return $_data;
}

/**
 * Block downloads of MP3 files if configured
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_download_file_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrAudio_block_download']) && $_conf['jrAudio_block_download'] == 'on' && !jrUser_is_admin()) {
        // Check for extension
        switch ($_data["{$_args['file_name']}_extension"]) {
            case 'mp3':
                jrCore_notice('Error', $_data["{$_args['file_name']}_extension"] . " files are restricted to streaming only");
                break;
        }
    }
    return $_data;
}

/**
 * Add some items to the System Check
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    // Check for SOX binary
    $dat             = array();
    $dat[1]['title'] = 'Audio SOX binary';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'executable';
    $dat[2]['class'] = 'center';

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    $dir = jrCore_get_module_cache_dir('jrAudio');
    $tmp = tempnam($dir, 'system_check_');

    if ($sox = jrAudio_check_sox_install(false)) {
        ob_start();
        system("nice -n 9 {$sox} >{$tmp} 2>&1", $ret);
        ob_end_clean();
        if (is_file($tmp) && strpos(file_get_contents($tmp), 'Usage summary:')) {
            $dat[3]['title'] = $pass;
            $dat[4]['title'] = 'SOX binary is working properly';
        }
        else {
            $dat[3]['title'] = $fail;
            $dat[4]['title'] = "SOX binary is not working<br>" . str_replace(APP_DIR . '/', '', $sox);
        }
    }
    else {
        $dat[3]['title'] = $fail;
        $dat[4]['title'] = 'SOX binary is not executable<br>modules/jrAudio/tools/sox';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);

    $dat             = array();
    $dat[1]['title'] = 'Audio ID3v2 binary';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'executable';
    $dat[2]['class'] = 'center';

    if ($id3 = jrAudio_check_id3_install(false)) {
        ob_start();
        system("nice -n 9 {$id3} >{$tmp} 2>&1", $ret);
        ob_end_clean();
        if (is_file($tmp) && strpos(' ' . file_get_contents($tmp), 'Usage:')) {
            $dat[3]['title'] = $pass;
            $dat[4]['title'] = 'ID3v2 binary is working properly';
        }
        else {
            $dat[3]['title'] = $fail;
            $dat[4]['title'] = "ID3v2 binary is not working<br>" . str_replace(APP_DIR . '/', '', $id3);
        }
    }
    else {
        $dat[3]['title'] = $fail;
        $dat[4]['title'] = 'ID3v2 binary is not executable<br>modules/jrAudio/tools/id3v2';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
    unlink($tmp);
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
function jrAudio_get_album_field_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrAudio'] = 'audio_album';
    return $_data;
}

/**
 * Add share data to a jrOneAll network share
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrAudio
    // [action_data] => (JSON array of data for item initiating action)
    $_data = json_decode($_data['action_data'], true);
    if (!isset($_data) || !is_array($_data)) {
        return false;
    }
    $_ln = jrUser_load_lang_strings($_data['user_language']);

    // We return an array:
    // 'text' => text to post (i.e. "tweet")
    // 'url'  => URL to media item,
    // 'name' => name if media item
    $url = jrCore_get_module_url('jrAudio');
    $txt = $_ln['jrAudio'][33];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrAudio'][55];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['audio_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['audio_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['audio_title_url']}",
            'name' => $_data['audio_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['audio_image_size']) && jrCore_checktype($_data['audio_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/audio_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

/**
 * Return audio file field for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_add_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module/View => File Field
    $_data['jrAudio/create']       = 'audio_file';
    $_data['jrAudio/update']       = 'audio_file';
    $_data['jrAudio/create_album'] = 'audio_file';
    $_data['jrAudio/update_album'] = 'audio_file';
    $_data['jrAudio/import']       = 'audio_file';
    return $_data;
}

/**
 * Listen for audio being added to a paid bundle and create samples
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_add_bundle_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['bundle_module']) && $_args['bundle_module'] == 'jrAudio') {
        if (jrCore_checktype($_args['item_id'], 'number_nz') && isset($_data['bundle_item_price']) && $_data['bundle_item_price'] > 0) {
            // We have an audio file being added to a bundle - see
            // if this is a paid bundle - if it is, we need to create sample file
            // if the audio item being added does not have bundle restrictions
            $iid = (int) $_args['item_id'];
            if (isset($_data['bundle_list']['jrAudio'][$iid])) {
                // Make sure sample file exists...
                $_rt = jrCore_db_get_item('jrAudio', $iid, true);
                if ($_rt && is_array($_rt)) {
                    $fld = $_args['field'];
                    $nam = jrCore_get_media_file_path('jrAudio', $fld, $_rt);
                    if (!jrCore_media_file_exists($_rt['_profile_id'], "{$nam}.sample.mp3")) {
                        // Create Sample
                        $_queue = array(
                            'file_name'  => $fld,
                            'profile_id' => $_rt['_profile_id'],
                            'item_id'    => $iid
                        );
                        jrCore_queue_create('jrAudio', 'audio_sample', $_queue);
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Listen for audio being removed from a paid bundle
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_delete_bundle_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['bundle_module']) && $_args['bundle_module'] == 'jrAudio' && jrCore_checktype($_args['item_id'], 'number_nz')) {
        // We have an audio file being removed from a bundle.  If:
        // - the audio file no longer belongs to any bundles
        // - the audio file belongs to free bundles
        // - the audio file has no price
        // We remove the sample file
        if (!isset($_data['audio_file_item_bundle']) || strlen($_data['audio_file_item_bundle']) === 0) {
            if (!isset($_data['audio_file_item_price']) || $_data['audio_file_item_price'] == 0) {
                $nam = jrCore_get_media_file_path('jrAudio', 'audio_file', $_data);
                jrCore_delete_media_file($_data['_profile_id'], "{$nam}.sample.mp3");
                // Also - turn off bundle_only if on
                if (isset($_data['audio_bundle_only']) && $_data['audio_bundle_only'] = 'on') {
                    jrCore_db_delete_item_key('jrAudio', $_data['_item_id'], 'audio_bundle_only');
                }
            }
        }
        else {
            // See if we are only part of free bundles
            $_id = array();
            foreach (explode(',', $_data['audio_file_item_bundle']) as $bid) {
                $_id[] = (int) $bid;
            }
            $_bi = jrCore_db_get_multiple_items('jrFoxyCartBundle', $_id, array('bundle_item_price'));
            if ($_bi && is_array($_bi)) {
                foreach ($_bi as $_bun) {
                    if (isset($_bun['bundle_item_price']) && $_bun['bundle_item_price'] > 0) {
                        // We are paid - do not remove sample
                        return $_data;
                    }
                }
            }
            $nam = jrCore_get_media_file_path('jrAudio', 'audio_file', $_data);
            jrCore_delete_media_file($_data['_profile_id'], "{$nam}.sample.mp3");
        }
    }
    return $_data;
}

/**
 * Return audio file bundle fields for forms
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_add_bundle_price_field_listener($_data, $_user, $_conf, $_args, $event)
{
    // Module/View => array(Bundle Title field, Bundle File field)
    $_data['jrAudio/create_album'] = array(
        'title' => 'audio_album',
        'field' => 'audio_file'
    );
    $_data['jrAudio/update_album'] = array(
        'title' => 'audio_album',
        'field' => 'audio_file'
    );
    return $_data;
}

/**
 * Get information about an item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrAudio') {
        $url               = jrCore_get_module_url('jrAudio');
        $_data[2]['title'] = $_args['audio_title'];

        // See if this item has an original
        if (isset($_args['audio_file_original_extension'])) {
            $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/audio_file/{$_args['_item_id']}')");
        }
        else {
            $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/download/audio_file/{$_args['_item_id']}')");
        }
    }
    return $_data;
}

/**
 * display the sale info to the seller of the item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'jrAudio') {
        $_data[1]['title'] = $_args['audio_title'];
    }
    return $_data;
}

/**
 * fired when foxycart sends a confirmed order in the background back to our system.
 * If there are audio files in the order, those files need to be kept in the system vault
 * so they can be downloaded.  do that moving here.
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function jrAudio_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrAudio') {
        // a file has been sold, copy it to our system vault.
        $nam = jrCore_get_media_file_path($_args['module'], $_args['product_field'], $_data);
        if (!isset($nam) || !is_file($nam)) {
            // BAD FILE!
            jrCore_logger('CRI', "transaction received with no valid media file: {$_args['txn']['txn_id']}");
            return $_data;
        }
        $dir = APP_DIR . '/data/media/vault';
        $fil = $dir . '/' . basename($nam);
        if (!is_file($fil)) {
            if (!copy($nam, $fil)) {
                jrCore_logger('CRI', "unable to copy sold media file to system vault: {$_args['txn']['txn_id']}");
                return $_data;
            }
        }
    }
    return $_data;
}

/**
 * for the jrEmbed module to add a gallery tab.
 * Adds audio to the popup tinymce editor for insertion into pages
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 *
 * @return array
 */
function jrAudio_tinymce_popup_listener($_data, $_user, $_conf, $_args, $event)
{
    $flag_found = false;

    //over-ride any existing jrAudio setting.
    foreach ($_data as $k => $tab) {
        if ($tab['module'] == 'jrAudio') {
            $_data[$k]['tab_location'] = 'jrAudio';
            $_data[$k]['tab_tpl']      = 'tab_jrAudio.tpl';
            $_data[$k]['onclick']      = 'loadAudio();';
            $flag_found                = true;
        }
    }

    //this modules tab was not set, so set it.
    if (!$flag_found) {
        $_data[] = array(
            'module'       => 'jrAudio',
            'name'         => 'audio',
            'tab_location' => 'jrAudio',
            'tab_tpl'      => 'tab_jrAudio.tpl',
            'onclick'      => 'loadAudio();'
        );
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
function jrAudio_url_found_listener($_data, $_user, $_conf, $_args, $event)
{
    // Is it a local audio url
    if (isset($_args['url']) && strpos($_args['url'], $_conf['jrCore_base_url']) === 0) {
        $_x = explode('/', substr($_args['url'], strlen($_conf['jrCore_base_url']) + 1));
    }
    if (isset($_x) && is_array($_x) && isset($_x[1]) && $_x[1] == jrCore_get_module_url('jrAudio') && isset($_x[2]) && jrCore_checktype($_x[2], 'number_nz')) {
        $title = jrCore_db_get_item_key('jrAudio', $_x[2], 'audio_title');
        if ($title && strlen($title) > 0) {
            $uurl = jrCore_get_module_url('jrUrlScan');
            $_data['_items'][$_args['i']]['title']    = $title;
            $_data['_items'][$_args['i']]['load_url'] = "{$_conf['jrCore_base_url']}/{$uurl}/parse/urlscan_player/{$_x[2]}/0/jrAudio/__ajax=1";
            $_data['_items'][$_args['i']]['url']      = $_args['url'];
        }
    }
    return $_data;
}

/**
 * Format RSS entries
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrAudio_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    // We override the "description" and format it differently
    if (isset($_args['module']) && $_args['module'] == 'jrAudio') {
        $_lg = jrUser_load_lang_strings();
        $pfx = $_args['prefix'];
        foreach ($_data as $k => $_itm) {
            $_data[$k]['description'] = "{$_itm['profile_name']} {$_lg['jrAudio'][33]} - &quot;{$_itm["{$pfx}_title"]}&quot;";
        }
    }
    return $_data;
}

/**
 * Hide Audio items that are audio_active = off from everyone but admins and profile owners
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAudio_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'jrAudio' && !jrUser_is_admin()) {
        if ($pid = jrProfile_is_profile_view()) {
            if (!jrUser_is_logged_in() || !jrProfile_is_profile_owner($pid)) {
                if (!isset($_data['search'])) {
                    $_data['search'] = array();
                }
                $_data['search'][] = 'audio_active = on';
            }
        }
    }
    return $_data;
}

/**
 * Valid ID3v1 Genres
 * @return array
 */
function jrAudio_get_id3_genres()
{
    return array(
        0   => 'Blues',
        1   => 'Classic Rock',
        2   => 'Country',
        3   => 'Dance',
        4   => 'Disco',
        5   => 'Funk',
        6   => 'Grunge',
        7   => 'Hip-Hop',
        8   => 'Jazz',
        9   => 'Metal',
        10  => 'New Age',
        11  => 'Oldies',
        12  => 'Other',
        13  => 'Pop',
        14  => 'R&B',
        15  => 'Rap',
        16  => 'Reggae',
        17  => 'Rock',
        18  => 'Techno',
        19  => 'Industrial',
        20  => 'Alternative',
        21  => 'Ska',
        22  => 'Death Metal',
        23  => 'Pranks',
        24  => 'Soundtrack',
        25  => 'Euro-Techno',
        26  => 'Ambient',
        27  => 'Trip-Hop',
        28  => 'Vocal',
        29  => 'Jazz Funk',
        30  => 'Fusion',
        31  => 'Trance',
        32  => 'Classical',
        33  => 'Instrumental',
        34  => 'Acid',
        35  => 'House',
        36  => 'Game',
        37  => 'Sound Clip',
        38  => 'Gospel',
        39  => 'Noise',
        40  => 'Alternative Rock',
        41  => 'Bass',
        42  => 'Soul',
        43  => 'Punk',
        44  => 'Space',
        45  => 'Meditative',
        46  => 'Instrumental Pop',
        47  => 'Instrumental Rock',
        48  => 'Ethnic',
        49  => 'Gothic',
        50  => 'Darkwave',
        51  => 'Techno-Industrial',
        52  => 'Electronic',
        53  => 'Pop-Folk',
        54  => 'Eurodance',
        55  => 'Dream',
        56  => 'Southern Rock',
        57  => 'Comedy',
        58  => 'Cult',
        59  => 'Gangsta',
        60  => 'Top 40',
        61  => 'Christian Rap',
        62  => 'Pop/Funk',
        63  => 'Jungle',
        64  => 'Native US',
        65  => 'Cabaret',
        66  => 'New Wave',
        67  => 'Psychedelic',
        68  => 'Rave',
        69  => 'Showtunes',
        70  => 'Trailer',
        71  => 'Lo-Fi',
        72  => 'Tribal',
        73  => 'Acid Punk',
        74  => 'Acid Jazz',
        75  => 'Polka',
        76  => 'Retro',
        77  => 'Musical',
        78  => 'Rock & Roll',
        79  => 'Hard Rock',
        80  => 'Folk',
        81  => 'Folk-Rock',
        82  => 'National Folk',
        83  => 'Swing',
        84  => 'Fast Fusion',
        85  => 'Bebop',
        86  => 'Latin',
        87  => 'Revival',
        88  => 'Celtic',
        89  => 'Bluegrass',
        90  => 'Avantgarde',
        91  => 'Gothic Rock',
        92  => 'Progressive Rock',
        93  => 'Psychedelic Rock',
        94  => 'Symphonic Rock',
        95  => 'Slow Rock',
        96  => 'Big Band',
        97  => 'Chorus',
        98  => 'Easy Listening',
        99  => 'Acoustic',
        100 => 'Humour',
        101 => 'Speech',
        102 => 'Chanson',
        103 => 'Opera',
        104 => 'Chamber Music',
        105 => 'Sonata',
        106 => 'Symphony',
        107 => 'Booty Bass',
        108 => 'Primus',
        109 => 'Porn Groove',
        110 => 'Satire',
        111 => 'Slow Jam',
        112 => 'Club',
        113 => 'Tango',
        114 => 'Samba',
        115 => 'Folklore',
        116 => 'Ballad',
        117 => 'Power Ballad',
        118 => 'Rhythmic Soul',
        119 => 'Freestyle',
        120 => 'Duet',
        121 => 'Punk Rock',
        122 => 'Drum Solo',
        123 => 'Acapella',
        124 => 'Euro-House',
        125 => 'Dance Hall',
        126 => 'Goa',
        127 => 'Drum & Bass',
        128 => 'Club-House',
        129 => 'Hardcore',
        130 => 'Terror',
        131 => 'Indie',
        132 => 'BritPop',
        133 => 'Negerpunk',
        134 => 'Polsk Punk',
        135 => 'Beat',
        136 => 'Christian Gangsta Rap',
        137 => 'Heavy Metal',
        138 => 'Black Metal',
        139 => 'Crossover',
        140 => 'Contemporary Christian',
        141 => 'Christian Rock',
        142 => 'Merengue',
        143 => 'Salsa',
        144 => 'Trash Metal',
        145 => 'Anime',
        146 => 'Jpop',
        147 => 'Synthpop',
        255 => 'Unknown'
    );
}


