<?php
/**
 * Jamroom 5 Image Support module
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
 * jrImage_meta
 */
function jrImage_meta()
{
    $_tmp = array(
        'name'        => 'Image Support',
        'url'         => 'image',
        'version'     => '1.2.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Core support for displaying, resizing and manipulating images',
        'category'    => 'media',
        'license'     => 'mpl',
        'priority'    => 1, // HIGHEST load priority
        'locked'      => true,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * jrImage_init
 */
function jrImage_init()
{
    // Our image module provides the "image" magic view
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrImage', 'image', 'jrImage_display_image');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrImage', 'default_img', 'jrImage_display_default_image');

    // Register our tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrImage', 'cache_reset', array('Reset Image Cache', 'Resets the resized image cache'));

    // We also provide support for the "image" form field type
    jrCore_register_module_feature('jrCore', 'form_field', 'jrImage', 'image');

    // We will check our own sessions
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrImage', 'image', 'magic_view');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrImage', 'img');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrImage', 'default_img');

    // We're going to listen to the "save_media_file" event
    // so we can add image specific fields to the item
    jrCore_register_event_listener('jrCore', 'save_media_file', 'jrImage_save_media_file_listener');

    // We also provide a "require_image" parameter to the jrCore_db_search_item function
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrImage_db_search_params_listener');

    // Once a day we cleanup old cache entries
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrImage_daily_maintenance_listener');

    // Triggers
    jrCore_register_event_trigger('jrImage', 'img_src', 'Fired before returning image src parameter in jrImage_display');
    jrCore_register_event_trigger('jrImage', 'module_image', 'Fired with img src when showing a module image via the image/img view');
    jrCore_register_event_trigger('jrImage', 'skin_image', 'Fired with img src when showing a skin image via the image/img view');

    // Register our CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrImage', 'jrImage.css');

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrImage', 'image cache size', 'jrImage_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrImage', 'cached image count', 'jrImage_dashboard_panels');

    return true;
}

//---------------------------------------------------------
// DASHBOARD
//---------------------------------------------------------

/**
 * User Profiles Dashboard Panels
 * @param $panel
 * @return bool|int
 */
function jrImage_dashboard_panels($panel)
{
    global $_conf;
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'image cache size':
            if (function_exists('system')) {
                $dir = jrCore_get_module_cache_dir('jrImage');
                $dir = "{$dir}/{$_conf['jrImage_active_cache_dir']}";
                if (is_dir($dir)) {
                    ob_start();
                    system("/usr/bin/du -sk {$dir} 2>/dev/null", $ret);
                    $out = ob_get_clean();
                    $out *= 1024;

                    $kb = 1024;
                    $mb = 1024 * $kb;
                    $gb = 1024 * $mb;
                    $tb = 1024 * $gb;

                    $mod = '';
                    $siz = 0;
                    if ($out < $kb) {
                        $mod = 'bytes';
                    }
                    elseif ($out < $mb) {
                        $mod = 'kilobytes';
                        $siz = round($out / $kb);
                    }
                    elseif ($out < $gb) {
                        $mod = 'megabytes';
                        $siz = round($out / $mb, 1);
                    }
                    elseif ($out < $tb) {
                        $mod = 'gigabytes';
                        $siz = round($out / $gb, 2);
                    }
                    $out = array(
                        'title' => "{$siz}<br><span>{$mod}</span>"
                    );
                }
            }
            break;

        case 'cached image count':
            if (function_exists('system')) {
                $dir = jrCore_get_module_cache_dir('jrImage');
                $dir = "{$dir}/{$_conf['jrImage_active_cache_dir']}";
                if (is_dir($dir)) {
                    ob_start();
                    system("/usr/bin/find {$dir} -type f | wc -l 2>/dev/null", $ret);
                    $out = ob_get_clean();
                    $out = array(
                        'title' => intval($out)
                    );
                }
            }
            break;

    }
    return ($out) ? $out : false;
}

//---------------------------------------------------------
// IMAGE EVENT LISTENERS
//---------------------------------------------------------

/**
 * Keeps image cache cleaned up
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrImage_clean_days']) && jrCore_checktype($_conf['jrImage_clean_days'], 'number_nz')) {
        $dif = (int) $_conf['jrImage_clean_days'];
        if ($dif > 2) {
            $tag = "{$dif} days";
        }
        else {
            $tag = ($dif * 24) . ' hours';
        }
        $dif = ($dif * 86400);
        // We will delete any cached image files that have not been accessed in the last day
        $old = (time() - $dif);
        $cdr = jrCore_get_module_cache_dir('jrImage') . "/{$_conf['jrImage_active_cache_dir']}";
        if (!is_dir($cdr)) {
            return true;
        }
        $c = 0;
        $s = 0;
        $f = opendir($cdr);
        if ($f) {
            while ($file = readdir($f)) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir("{$cdr}/{$file}")) {
                    $d = opendir("{$cdr}/{$file}");
                    if ($d) {
                        while ($img = readdir($d)) {
                            if (is_file("{$cdr}/{$file}/{$img}")) {
                                $_tmp = stat("{$cdr}/{$file}/{$img}");
                                if (isset($_tmp['atime']) && $_tmp['atime'] < $old) {
                                    unlink("{$cdr}/{$file}/{$img}");
                                    $c++;
                                    $s += $_tmp['size'];
                                }
                            }
                        }
                        closedir($d);
                    }
                }
            }
            closedir($f);
        }
        if (isset($c) && $c > 0) {
            jrCore_logger('INF', "deleted {$c} cached image files (total " . jrCore_format_size($s) . ") with no access in the last {$tag}");
        }
    }
    return $_data;
}

/**
 * Adds width/height keys to saved media info
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_save_media_file_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if we are getting an image file upload...
    if (!isset($_data["{$_args['file_name']}_extension"]) || !isset($_args['saved_file']) || !is_file($_args['saved_file'])) {
        return $_data;
    }
    switch ($_data["{$_args['file_name']}_extension"]) {
        // See if we are converting to JPG
        case 'png':
        case 'gif':
            $pfx = $_args['file_name'];
            if ($pfx && isset($_conf['jrImage_convert_to_jpg']) && $_conf['jrImage_convert_to_jpg'] == 'on') {

                $cnv = true;
                // If this is a transparent PNG, no conversion...
                if ($_data["{$_args['file_name']}_extension"] == 'png' && jrImage_is_alpha_png($_args['saved_file'])) {
                    $cnv = false;
                }
                elseif ($_data["{$_args['file_name']}_extension"] == 'gif' && jrImage_is_animated_gif($_args['saved_file'])) {
                    $cnv = false;
                }

                if ($cnv) {
                    $ext = $_data["{$pfx}_extension"];
                    $src = imagecreatefrompng($_args['saved_file']);
                    $new = str_replace(".{$ext}", '.jpg', $_args['saved_file']);
                    imagejpeg($src, $new, 85);
                    imagedestroy($src);
                    unlink($_args['saved_file']);
                    $_args['saved_file']       = $new;
                    $_data["{$pfx}_name"]      = str_replace(".{$ext}", '.jpg', $_data["{$pfx}image_name"]);
                    $_data["{$pfx}_size"]      = filesize($new);
                    $_data["{$pfx}_type"]      = 'image/jpg';
                    $_data["{$pfx}_extension"] = 'jpg';
                }
            }
            break;
    }
    switch ($_data["{$_args['file_name']}_extension"]) {
        case 'png':
        case 'gif':
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            $_tmp                                  = getimagesize($_args['saved_file']);
            $_data["{$_args['file_name']}_width"]  = (int) $_tmp[0];
            $_data["{$_args['file_name']}_height"] = (int) $_tmp[1];
            break;
    }
    return $_data;
}

/**
 * Adds support for "require_image" to jrCore_db_search_items()
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrImage_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    $done = false;
    // require_image_width="width"
    if (isset($_data['require_image']{0})) {
        if (isset($_data['require_image_width'])) {
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "{$_data['require_image']}_width >= " . intval($_data['require_image_width']);
            $done              = true;
        }
        // require_image_height="height"
        if (isset($_data['require_image_height'])) {
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "{$_data['require_image']}_height >= " . intval($_data['require_image_height']);
            $done              = true;
        }
        // require_image="profile_image"
        if (!$done) {
            // We need to add in the SQL "where" clause that the TYPE of image
            // received must be larger than 0 bytes
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            $_data['search'][] = "{$_data['require_image']}_size > 0";
        }
    }
    return $_data;
}

//---------------------------------------------------------
// IMAGE FUNCTIONS
//---------------------------------------------------------

/**
 * Return TRUE if file/string is an image file
 * @param $file string Image File name
 * @return string|bool
 */
function jrImage_is_image_file($file)
{
    $ext = jrCore_file_extension($file);
    switch ($ext) {
        case 'png':
        case 'gif':
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            return $ext;
            break;
    }
    return false;
}

/**
 * Returns true if PNG image contains transparency
 * @param $file string full path to PNG file
 * @return bool
 */
function jrImage_is_alpha_png($file)
{
    return (ord(@file_get_contents($file, null, null, 25, 1)) == 6);
}

/**
 * Returns true if a GIF image is an animated GIF
 * @param $file string Full path to GIF file
 * @return bool
 */
function jrImage_is_animated_gif($file)
{
    $file = file_get_contents($file);
    $sloc = 0;
    $scnt = 0;
    while ($scnt < 2) {
        $whr1 = strpos($file, "\x00\x21\xF9\x04", $sloc);
        if (!$whr1) {
            break;
        }
        else {
            $sloc = ($whr1 + 1);
            $whr2 = strpos($file, "\x00\x2C", $sloc);
            if (!$whr2) {
                break;
            }
            else {
                if (($whr1 + 8) == $whr2) {
                    $scnt++;
                }
                $sloc = ($whr2 + 1);
            }
        }
    }
    return ($scnt > 1) ? true : false;
}

/**
 * jrImage_form_field_image_display
 * @param $_field array Array of Field parameters
 * @param $_att array Additional HTML parameters
 * @return bool
 */
function jrImage_form_field_image_display($_field, $_att = null)
{
    global $_conf, $_user;
    $htm = '';
    if (!isset($_field['value']) || !is_array($_field['value'])) {
        if (isset($_field['value']) && $_field['value'] === false) {
            // weird..
        }
        else {
            // If we are doing an update - we need the full item - the 'jrcore_form_create_values'
            // flag is set in the jrCore_form_create call with the "values" key/array
            $_field['value'] = jrCore_get_flag('jrcore_form_create_values');
        }
    }
    // We need to show existing images to the user
    $_ln = jrUser_load_lang_strings();
    if (!isset($_field['no_image']) && isset($_field['value']) && is_array($_field['value']) && isset($_field['value']['_item_id']) && jrCore_checktype($_field['value']['_item_id'], 'number_nz')) {

        // See if we are allowing 1 or multiple images to be uploaded for this item
        $_im = array();
        if (!isset($_field['multiple']) || (jrCore_checktype($_field['multiple'], 'number_nz') && $_field['multiple'] > 1)) {
            // Get image fields
            foreach ($_field['value'] as $k => $v) {
                if (strpos($k, "{$_field['name']}_") === 0 && strpos($k, '_size') && jrCore_checktype($v, 'number_nz')) {
                    $_im[] = array(
                        'field'  => str_replace('_size', '', $k),
                        'unique' => (int) $_field['value']["{$_field['name']}_time"]
                    );
                }
            }
        }
        else {
            if (isset($_field['value']['_item_id']) && jrCore_checktype($_field['value']['_item_id'], 'number_nz') && isset($_field['value']["{$_field['name']}_size"]) && jrCore_checktype($_field['value']["{$_field['name']}_size"], 'number_nz')) {
                $_im[] = array(
                    'field'  => $_field['name'],
                    'unique' => $_field['value']["{$_field['name']}_time"]
                );
            }
        }
        if (count($_im) > 0) {
            $_fm = jrCore_form_get_session();
            if (!isset($_field['image_module'])) {
                $mod = $_fm['form_params']['module'];
                $iid = (int) $_field['value']['_item_id'];
            }
            else {
                $mod = $_field['image_module'];
                switch ($mod) {
                    case 'jrProfile':
                        $iid = (int) $_field['value']['_profile_id'];
                        break;
                    case 'jrUser':
                        $iid = (int) $_field['value']['_user_id'];
                        break;
                    default:
                        $iid = (int) $_field['value']['_item_id'];
                        break;
                }
            }
            $url = jrCore_get_module_url($mod);
            $iur = jrCore_get_module_url('jrImage');
            $_sz = jrImage_get_allowed_image_widths();
            $siz = (isset($_field['size'])) ? $_field['size'] : 'medium';


            foreach ($_im as $k => $_inf) {
                if (isset($_field['image_delete']) && $_field['image_delete'] !== false) {
                    $htm .= "<div class=\"image_update_display\" onmouseover=\"$('#d{$_inf['field']}').show()\" onmouseout=\"$('#d{$_inf['field']}').hide()\"><img src=\"{$_conf['jrCore_base_url']}/{$url}/image/{$_inf['field']}/{$iid}/{$siz}/_v={$_inf['unique']}\" width=\"" . intval($_sz[$siz]) . "\" alt=\"" . addslashes($_field['label']) . "\">";
                    $img = jrCore_get_sprite_html('close', 16);
                    $htm .= "<div id=\"d{$_inf['field']}\" class=\"image_delete\"><a href=\"{$_conf['jrCore_base_url']}/{$iur}/delete/{$mod}/{$_inf['field']}/{$iid}/_v={$_inf['unique']}\" title=\"" . $_ln['jrImage'][2] . "\" onclick=\"jrCore_set_csrf_cookie('{$_conf['jrCore_base_url']}/{$iur}/delete/{$mod}/{$_inf['field']}/{$iid}/_v={$_inf['unique']}'); if(!confirm('" . addslashes($_ln['jrImage'][3]) . "')){ return false; }\">{$img}</a></div>";
                }
                else {
                    $htm .= "<div class=\"image_update_display\"><img src=\"{$_conf['jrCore_base_url']}/{$url}/image/{$_inf['field']}/{$iid}/{$siz}/_v={$_inf['unique']}\" width=\"" . intval($_sz[$siz]) . "\" alt=\"" . addslashes($_field['label']) . "\">";
                }
                $htm .= '</div>';
            }
            $htm .= '<br><br>';
        }
    }
    if (!isset($_field['text']) && isset($_ln['jrImage'][4])) {
        $_field['text'] = $_ln['jrImage'][4];
    }
    $_field['html']     = $htm;
    $_field['type']     = 'image';
    $_field['template'] = 'form_field_elements.tpl';

    // We have a file upload - we need to turn on the progress meter if enabled
    $_field['multiple'] = (isset($_field['multiple'])) ? $_field['multiple'] : false;

    if (isset($_field['allowed'])) {
        $allowed = trim($_field['allowed']);
    }
    else {
        // Make sure we have some quota defaults
        if (!isset($_user['quota_jrImage_allowed_image_types']) || strlen($_user['quota_jrImage_allowed_image_types']) < 3) {
            $_user['quota_jrImage_allowed_image_types'] = 'png,gif,jpg,jpeg';
        }
        $allowed = trim($_user['quota_jrImage_allowed_image_types']);
    }
    if (!isset($_user['quota_jrImage_max_image_size']) || !jrCore_checktype($_user['quota_jrImage_max_image_size'], 'number_nz')) {
        $_user['quota_jrImage_max_image_size'] = 2097152;
    }
    $_field = jrCore_enable_meter_support($_field, $allowed, jrCore_get_max_allowed_upload($_user['quota_jrImage_max_image_size']), $_field['multiple']);

    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return string
 */
function jrImage_form_field_image_form_designer_options()
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
function jrImage_form_field_image_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress');
}

/**
 * Check to be sure validation is on if field is required
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return bool
 */
function jrImage_form_field_image_params($_field, $_post)
{
    if (!isset($_field['validate'])) {
        $_field['validate'] = 'not_empty';
    }
    if (!isset($_field['error_msg'])) {
        $_lang               = jrUser_load_lang_strings();
        $_field['error_msg'] = $_lang['jrImage'][1];
    }
    return $_field;
}

/**
 * Checks to see if we received data on our post in the form validator
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return bool
 */
function jrImage_form_field_image_is_empty($_field, $_post)
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
 * Verify we get an uploaded file if one is required in the form
 * @param $_field array Field Information
 * @param $_post array Parsed $_REQUEST
 * @param $e_msg string Error message for field if in error
 * @return bool
 */
function jrImage_form_field_image_validate($_field, $_post, $e_msg)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        if (!$_field['required']) {
            // file does not exist, but is not required
            return $_post;
        }
        jrCore_set_form_notice('error', $e_msg);
        return false;
    }
    // Okay looks good
    return $_post;
}

/**
 * jrImage_get_allowed_image_widths()
 * @return array Returns array of allowed image sizes
 */
function jrImage_get_allowed_image_widths()
{
    $_sz = array(
        '24'       => 24,
        'xxsmall'  => 24,
        '40'       => 40,
        'xsmall'   => 40,
        '56'       => 56,
        '72'       => 72,
        'small'    => 72,
        '96'       => 96,
        'icon96'   => 96,
        '128'      => 128,
        'icon'     => 128,
        '196'      => 196,
        'medium'   => 196,
        '256'      => 256,
        'large'    => 256,
        '320'      => 320,
        'larger'   => 320,
        '384'      => 384,
        'xlarge'   => 384,
        '512'      => 512,
        'xxlarge'  => 512,
        '800'      => 800,
        'xxxlarge' => 800,
        '1280'     => 1280
    );
    return $_sz;
}

/**
 * Return an Image SRC URL
 * @param $module string Module name
 * @param $field string DS field
 * @param $item_id int Item ID
 * @param $size string Size
 * @param $_args array Additional params
 * @return string
 */
function jrImage_get_image_src($module, $field, $item_id, $size, $_args = null)
{
    $params = array();
    if (!is_null($_args)) {
        $params = $_args;
    }
    $params['module']  = $module;
    $params['type']    = $field;
    $params['item_id'] = (int) $item_id;
    $params['size']    = $size;
    $smarty            = new stdClass();
    return smarty_function_jrImage_display($params, $smarty);
}

/**
 * Display an image for a DataStore item
 * @param $_post array Params from jrCore_parse_url();
 * @param $_user array User information
 * @param $_conf array Global config
 * @return bool Returns true
 */
function jrImage_display_image($_post, $_user, $_conf)
{
    jrUser_ignore_action();
    // our URL will look like:
    // http://www.site.com/module/image/image/5/small
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        if (isset($_post['debug'])) {
            jrCore_notice('CRI', "invalid media_id - must be valid media id");
        }
        jrImage_display_default_image($_post, $_conf);
    }
    $_rt = jrCore_db_get_item($_post['module'], intval($_post['_2']));
    if (!isset($_rt) || !is_array($_rt)) {
        if (isset($_post['debug'])) {
            jrCore_notice('CRI', "invalid media data  - data for id not found in DataStore");
        }
        jrImage_display_default_image($_post, $_conf);
    }

    // Privacy Checking for this profile
    if (isset($_rt['profile_private'])) {
        switch ($_rt['profile_private']) {
            case '0':
            case '2':
                // Don't change this - this needs to be global here
                global $_user;
                $_user = jrUser_session_start(false); // DO NOT REMOVE
                if (!jrProfile_privacy_check($_rt['_profile_id'], $_rt['profile_private'])) {
                    // We do not have access to this profile
                    jrImage_display_default_image($_post, $_conf);
                }
                break;
        }
    }

    // Check for not modified
    $ifs = (function_exists('getenv')) ? getenv('HTTP_IF_MODIFIED_SINCE') : false;
    if (!$ifs && isset($_SERVER['HTTP_IF_MODIFIED_SINCE']{1})) {
        $ifs = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
    }
    if ($ifs && isset($_rt["{$_post['_1']}_time"]) && strtotime($ifs) == $_rt["{$_post['_1']}_time"]) {
        $_tmp = jrCore_get_flag('jrcore_set_custom_header');
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $header) {
                header($header);
            }
        }
        header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', $_rt["{$_post['_1']}_time"]));
        header('Content-Disposition: inline; filename="' . $_rt["{$_post['_1']}_name"] . '"');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
        header('HTTP/1.1 304 Not Modified');
        exit;
    }

    // Make sure database is correct
    if (!isset($_rt["{$_post['_1']}_size"]) || $_rt["{$_post['_1']}_size"] < 1) {
        if (isset($_post['debug'])) {
            jrCore_notice('CRI', "invalid media data - size of media in DataStore is 0 bytes");
        }
        jrImage_display_default_image($_post, $_conf);
    }
    // Check that file exists
    $nam = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}.{$_rt["{$_post['_1']}_extension"]}";
    // Make sure file is actually there...
    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        if (isset($_post['debug'])) {
            jrCore_notice('CRI', "invalid media file - not found");
        }
        jrImage_display_default_image($_post, $_conf);
    }

    // See what size we are getting
    if (!isset($_post['_3'])) {
        $_post['_3'] = 'icon';
    }
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$_post['_3']}"])) {
        if (isset($_post['debug'])) {
            jrCore_notice('CRI', "invalid image size - must be one of: " . implode(',', array_keys($_sz)));
        }
        jrImage_display_default_image($_post, $_conf);
    }
    $_post['width'] = $_sz["{$_post['_3']}"];

    // So now we know our WIDTH of the image.  This means the HEIGHT of the image
    // will vary, but can be controlled by the "crop" parameter:
    // - "height": image will be cropped from center in height to match width

    // get resized/cached image for display
    $_im = array(
        'image_time'      => $_rt["{$_post['_1']}_time"],
        'image_name'      => $_rt["{$_post['_1']}_name"],
        'image_size'      => $_rt["{$_post['_1']}_size"],
        'image_type'      => $_rt["{$_post['_1']}_type"],
        'image_extension' => $_rt["{$_post['_1']}_extension"]
    );
    if (isset($_rt["{$_post['_1']}_width"])) {
        $_im['image_width'] = $_rt["{$_post['_1']}_width"];
    }
    if (isset($_rt["{$_post['_1']}_height"])) {
        $_im['image_height'] = $_rt["{$_post['_1']}_height"];
    }

    // Directory to profile's files
    $dir = jrCore_get_media_directory($_rt['_profile_id']);
    $img = jrImage_create_image("{$dir}/{$nam}", $_im, $_post, $_conf, $_rt);

    // Custom headers set by other modules
    $_tmp = jrCore_get_flag('jrcore_set_custom_header');
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $header) {
            header($header);
        }
    }

    // Get right mime type - sometimes it can be wrong when PHP is wrong
    switch ($_im['image_extension']) {
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
            header("Content-type: {$_im['image_type']}");
            break;
    }
    header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', $_im['image_time']));
    header('Content-Disposition: inline; filename="' . $_im['image_name'] . '"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
    echo file_get_contents($img);
    session_write_close();
    exit();
}

/**
 * Resize and image, maintaining aspect ratio
 * @param $input_file string Input file to resize
 * @param $output_file string Output file to create
 * @param $width int Width (in pixels) for new image
 * @return mixed
 */
function jrImage_resize_image($input_file, $output_file, $width)
{
    global $_conf;
    // Some resize options can use a lot of memory
    @ini_set('memory_limit', '256M');
    $_im = getimagesize($input_file);
    switch ($_im['mime']) {
        case 'image/jpeg':
            $ext = 'jpg';
            $src = imagecreatefromjpeg($input_file);
            break;
        case 'image/png':
            $ext = 'png';
            $src = imagecreatefrompng($input_file);
            break;
        case 'image/gif':
            $ext = 'gif';
            $src = imagecreatefromgif($input_file);
            break;
        default:
            return 'ERROR: invalid image extension';
            break;
    }

    // make sure we get a valid resource
    if (!is_resource($src)) {
        // See if we can get it via imagecreatefromstring
        if (function_exists('imagecreatefromstring')) {
            $tmp = file_get_contents($input_file);
            $src = imagecreatefromstring($tmp);
            unset($tmp);
        }
        if (!is_resource($src)) {
            return 'ERROR: unable to create image resource from input file';
        }
    }

    // Resize Image
    $src_y_offset = 0;
    $src_x_offset = 0;
    $src_width    = $_im[0];
    $src_height   = $_im[1];

    // maintain aspect ratio of original image
    $height = (int) (($src_height / $src_width) * $width);

    // create resource
    if ($ext != 'gif') {
        $new = imagecreatetruecolor($width, $height);
        if (!$new) {
            imagedestroy($src);
            return 'ERROR: unable to create new resized image resource';
        }
        // Maintain alpha transparency on PNG
        imagealphablending($new, false);
        imagesavealpha($new, true);
    }
    else {
        $new = imagecreate($width, $height);
        if (!$new) {
            imagedestroy($src);
            return 'ERROR: unable to create new resized image resource';
        }
    }
    // resize image
    if (!imagecopyresampled($new, $src, 0, 0, $src_x_offset, $src_y_offset, $width, $height, $src_width, $src_height)) {
        if (!imagecopyresized($new, $src, 0, 0, $src_x_offset, $src_y_offset, $width, $height, $src_width, $src_height)) {
            imagedestroy($src);
            return 'ERROR: unable to create new resized image';
        }
    }

    // Create new image
    switch ($ext) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            imagejpeg($new, $output_file, 85);
            break;
        case 'png':
            imagepng($new, $output_file);
            break;
        case 'gif':
            if (function_exists('imagegif')) {
                imagecolortransparent($new);
                imagegif($new, $output_file);
            }
            else {
                imagepng($new, $output_file);
            }
            break;
    }
    imagedestroy($src);
    chmod($output_file, $_conf['jrCore_file_perms']);
    return $output_file;
}

/**
 * Create a new image thumbnail from an existing master image
 * @param $input_file string Input file to resize
 * @param $_image array Image information
 * @param $_post array Params from jrCore_parse_url();
 * @param $_conf array Global config
 * @param $_data array full image information
 * @return bool
 */
function jrImage_create_image($input_file, $_image, $_post, $_conf, $_data = null)
{
    global $_conf;
    // Some resize options can use a lot of memory
    @ini_set('memory_limit', '256M');
    // NOTE: $_image contains info about the ORIGINAL IMAGE
    // $_post contains our info about the NEW (resizing) image
    $ext = false;
    switch ($_image['image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            $ext = 'jpg';
            break;
        case 'png':
        case 'gif':
            if (jrImage_is_animated_gif($input_file)) {
                // We don't resize animated GIFs
                return $input_file;
            }
            $ext = 'png';
            break;
        default:
            if (isset($_post['debug'])) {
                jrCore_notice('CRI', "invalid image extension: {$_image['image_extension']}");
            }
            jrImage_display_default_image($_post, $_conf);
            break;
    }

    // Prep filters
    $_filter = false;
    foreach ($_post as $k => $v) {
        if (strpos($k, 'filter') === 0) {
            if (!isset($_filter)) {
                $_filter = array();
            }
            if (strpos($v, ',')) {
                foreach (explode(',', $v) as $vv) {
                    $_filter[] = trim($vv);
                }
            }
            else {
                $_filter[] = $v;
            }
        }
    }
    $key = '';
    if (isset($_data['quota_jrImage_watermark'])) {
        $key = $_data['quota_jrImage_watermark'] . '-' . $_data['quota_jrImage_watermark_x_offset'] . '-' . $_data['quota_jrImage_watermark_y_offset'];
    }
    // Check for cache
    $pky = json_encode($_post) . $key;
    $cid = md5($input_file . "-{$pky}-" . json_encode($_image));
    $cdr = jrCore_get_module_cache_dir('jrImage') . "/{$_conf['jrImage_active_cache_dir']}/" . substr($cid, 0, 2);
    if (is_file("{$cdr}/{$cid}.{$ext}")) {
        // If this is NOT a png image, and we have a filter, our filter
        // could have changed the format to PNG so check for that here
        if ($ext != 'png' && is_array($_filter)) {
            // see if filter has changed extension
            foreach ($_filter as $filt) {
                $_flt = explode(':', $filt);
                $func = "jrImage_filter_{$_flt[0]}_extension";
                if (function_exists($func)) {
                    $ext = $func();
                    break;
                }
            }
        }
        return "{$cdr}/{$cid}.{$ext}";
    }

    // Make sure cache directory exists
    if (!is_dir($cdr)) {
        mkdir($cdr, $_conf['jrCore_dir_perms'], true);
    }
    $cache_file = "{$cdr}/{$cid}.{$ext}";

    //----------------------------------
    // Load source image
    //----------------------------------
    $image = null;
    switch ($_image['image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            $image['source'] = @imagecreatefromjpeg($input_file);
            $image['quality'] = 85;
            break;
        case 'png':
            $image['source'] = @imagecreatefrompng($input_file);
            break;
        case 'gif':
            $image['source'] = @imagecreatefromgif($input_file);
            break;
    }
    // make sure we get a valid resource
    if (!is_resource($image['source'])) {
        // See if we can get it via imagecreatefromstring
        if (function_exists('imagecreatefromstring')) {
            $tmp             = file_get_contents($input_file);
            $image['source'] = @imagecreatefromstring($tmp);
            unset($tmp);
        }
        if (!is_resource($image['source'])) {
            return $input_file;
        }
    }

    // interlace
    switch ($_image['image_extension']) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            imageinterlace($image['source'], true);
            break;
    }

    //----------------------------------
    // Resize Image
    //----------------------------------
    $hnd_y_offset = 0;
    $hnd_x_offset = 0;
    $src_y_offset = 0;
    $src_x_offset = 0;
    $new_width    = $_post['width'];
    $new_height   = $_post['width'];
    list($src_width, $src_height,) = getimagesize($input_file);

    //----------------------------------
    // Cropping
    //----------------------------------
    if (isset($_post['crop'])) {
        switch ($_post['crop']) {

            case 'fill':
                // With a "fill" crop, we place black borders on top/sides of
                // image so the image is square, but no cropping/resizing
                // of the image happens.
                if ($src_width > $src_height) {
                    $diff         = ($src_width - $src_height);
                    $diff         = round(($diff / $src_width) * $_post['width']);
                    $hnd_y_offset = round($diff / 2);
                    $new_height   = ($new_width - $diff);
                }
                else {
                    $diff         = ($src_height - $src_width);
                    $diff         = round(($diff / $src_height) * $_post['width']);
                    $hnd_x_offset = round($diff / 2);
                    $new_width    = ($new_height - $diff);
                }
                $_post['height'] = $_post['width'];
                break;

            case 'portrait':
                // With a portrait crop, we expect the images to be of people,
                // with their head in the upper 3rd of the picture
                if ($src_width > $src_height) {
                    $diff         = ($src_width - $src_height);
                    $src_x_offset = round($diff / 2);
                    $src_width    = $src_height;
                }
                else {
                    $diff         = ($src_height - $src_width);
                    $src_y_offset = round($diff / 4);
                    $src_height   = $src_width;
                }
                $_post['height'] = $_post['width'];
                $new_height      = $new_width;
                break;

            // With crop set to "height" we will crop the height to the given
            // size, but maintain the aspect ratio for the width
            // NOTE: $_post['width'] here is set to allowed size as passed in
            case 'height':
                $_post['height'] = $_post['width'];
                $new_height      = $new_width;
                // Now we figure our width based on ratio of height
                $new_width      = (int) (($src_width / $src_height) * $new_height);
                $_post['width'] = $new_width;
                break;

            case 'width':
                // Now we figure our height based on ratio of width
                $new_height      = (int) (($src_height / $src_width) * $new_width);
                $_post['height'] = $new_height;
                break;

            // No crop
            case 'none':
            case 'false':
                // maintain aspect ratio of original image
                $new_height      = (int) (($src_height / $src_width) * $new_width);
                $_post['height'] = $new_height;
                break;


            // With crop set to "auto" we will crop the height OR width
            // depending on original aspect ratio of the image
            // 'auto'
            // 'square'
            default:

                // Check for an aspect ratio crop
                if (strpos($_post['crop'], ':')) {

                    // Our aspect ratio crop will come in like 4x3 or 16x9, etc.
                    list($w, $h) = explode(':', $_post['crop'], 2);
                    $w = (int) $w;
                    $h = (int) $h;
                    if ($w > 0 && $h > 0) {
                        $ratio = array(0 => $src_width / $src_height, 1 => $w / $h);
                        if ($w > $h) {
                            if ($ratio[0] > $ratio[1]) {
                                $width        = $src_height * ($w / $h);
                                $src_x_offset = ($src_width - $width) / 2;
                                $src_width    = $width;
                            }
                            else {
                                if ($ratio[0] < $ratio[1]) {
                                    $height       = $src_width / ($w / $h);
                                    $src_y_offset = ($src_height - $height) / 2;
                                    $src_height   = $height;
                                }
                            }
                            $new_width       = $_post['width'];
                            $new_height      = round(($_post['width'] / $w) * $h);
                            $_post['height'] = $new_height;
                        }
                        else {
                            if ($ratio[0] > $ratio[1]) {
                                $width        = $src_height * ($w / $h);
                                $src_x_offset = ($src_width - $width) / 2;
                                $src_width    = $width;
                            }
                            else {
                                if ($ratio[0] < $ratio[1]) {
                                    $height       = $src_width / ($w / $h);
                                    $src_y_offset = ($src_height - $height) / 2;
                                    $src_height   = $height;
                                }
                            }
                            $new_height      = $_post['width'];
                            $new_width       = round(($_post['width'] / $h) * $w);
                            $_post['width']  = $new_width;
                            $_post['height'] = $new_height;
                        }
                    }
                    else {
                        // fall through - maintain aspect ratio of original image
                        $new_height      = (int) (($src_height / $src_width) * $new_width);
                        $_post['height'] = $new_height;
                    }
                }
                else {
                    // Default auto height/width
                    if ($src_width > $src_height) {
                        $diff         = ($src_width - $src_height);
                        $src_x_offset = round($diff / 2);
                        $src_width    = $src_height;
                    }
                    else {
                        $diff         = ($src_height - $src_width);
                        $src_y_offset = round($diff / 2);
                        $src_height   = $src_width;
                    }
                    $_post['height'] = $_post['width'];
                    $new_height      = $new_width;
                }
                break;
        }
    }
    else {
        // maintain aspect ratio of original image
        $new_height      = (int) (($src_height / $src_width) * $new_width);
        $_post['height'] = $new_height;
    }

    //----------------------------------
    // create resource
    //----------------------------------
    if ($_image['image_extension'] != 'gif') {
        $image['handle'] = imagecreatetruecolor($_post['width'], $_post['height']);
        if (!$image['handle']) {
            imagedestroy($image['source']);
            return $input_file;
        }
        imagealphablending($image['handle'], false);
        imagesavealpha($image['handle'], true);
    }
    else {
        $image['handle'] = imagecreate($_post['width'], $_post['height']);
        if (!$image['handle']) {
            imagedestroy($image['source']);
            return $input_file;
        }
    }

    // If crop=fill we need to fill the image in first
    if (isset($_post['crop']) && $_post['crop'] == 'fill') {
        $color = imagecolorallocate($image['handle'], 0, 0, 0);
        imagefilledrectangle($image['handle'], 0, 0, $_post['width'], $_post['height'], $color);
    }

    // resize image
    if (!imagecopyresampled($image['handle'], $image['source'], $hnd_x_offset, $hnd_y_offset, $src_x_offset, $src_y_offset, $new_width, $new_height, $src_width, $src_height)) {
        if (!imagecopyresized($image['handle'], $image['source'], $hnd_x_offset, $hnd_y_offset, $src_x_offset, $src_y_offset, $new_width, $new_height, $src_width, $src_height)) {
            imagedestroy($image['source']);
            return $input_file;
        }
    }

    //----------------------------------
    // Watermarking
    //----------------------------------
    // [quota_jrImage_watermark] => on
    // [quota_jrImage_watermark_x_offset] => -5
    // [quota_jrImage_watermark_y_offset] => -5
    if (isset($_data['quota_jrImage_watermark']) && $_data['quota_jrImage_watermark'] == 'on') {
        // See if we are using the default
        $mark_file = APP_DIR . '/modules/jrImage/img/watermark.png';
        if (is_file(APP_DIR . '/data/media/0/0/mod_jrImage_watermark.png')) {
            $mark_file = APP_DIR . '/data/media/0/0/mod_jrImage_watermark.png';
        }
        $wmark = imagecreatefrompng($mark_file);
        if (is_resource($wmark)) {
            $wtr_x = imagesx($wmark);
            $wtr_y = imagesy($wmark);
            // We will make sure our watermark is no larger than HALF the width of the source
            if ($wtr_x > ($_post['width'] / 2)) {
                // We need to resize our watermark
                $tmp_width  = round($_post['width'] / 2);
                $tmp_height = round(($tmp_width / $wtr_x) * $wtr_y);
                $tmp_mark   = imagecreatetruecolor($tmp_width, $tmp_height);
                if (is_resource($tmp_mark)) {
                    imagealphablending($tmp_mark, false);
                    imagesavealpha($tmp_mark, true);
                    imagecopyresampled($tmp_mark, $wmark, 0, 0, 0, 0, $tmp_width, $tmp_height, $wtr_x, $wtr_y);
                    $wmark = $tmp_mark;
                }
                $wtr_x = $tmp_width;
                $wtr_y = $tmp_height;
                unset($tmp_mark);
            }
            // Positioning
            if (isset($_data['quota_jrImage_watermark_x_offset']) && $_data['quota_jrImage_watermark_x_offset'] >= 0) {
                $img_x = (int) $_data['quota_jrImage_watermark_x_offset'];
                if ($_post['width'] < 100) {
                    $img_x = round($img_x / 2);
                }
            }
            else {
                $img_x = (($_post['width'] - $wtr_x) - intval($_data['quota_jrImage_watermark_x_offset'] * -1));
                if ($_post['width'] < 100) {
                    $img_x -= round(intval($_data['quota_jrImage_watermark_x_offset'] * -1) / 2);
                }
            }
            if (isset($_data['quota_jrImage_watermark_y_offset']) && $_data['quota_jrImage_watermark_y_offset'] >= 0) {
                $img_y = (int) $_data['quota_jrImage_watermark_y_offset'];
                if ($_post['width'] < 100) {
                    $img_y = round($img_y / 2);
                }
            }
            else {
                $img_y = (($_post['height'] - $wtr_y) - intval($_data['quota_jrImage_watermark_y_offset'] * -1));
                if ($_post['width'] < 100) {
                    $img_y += round(intval($_data['quota_jrImage_watermark_y_offset'] * -1) / 2);
                }
            }
            jrImage_imagecopymerge_alpha($image['handle'], $wmark, $img_x, $img_y, 0, 0, $wtr_x, $wtr_y, 100);
        }
        imagedestroy($wmark);
    }

    //----------------------------------
    // Check for filters
    //----------------------------------
    if (is_array($_filter)) {
        // run our filters
        foreach ($_filter as $filt) {
            $_flt = explode(':', $filt);
            $func = "jrImage_filter_{$_flt[0]}";
            if (function_exists($func)) {
                $ftmp = $func($image['handle'], $_flt, $new_height, $new_width);
                if (isset($ftmp) && is_resource($ftmp)) {
                    $image['handle'] = $ftmp;
                    $new_height      = imagesy($image['handle']);
                    $new_width       = imagesx($image['handle']);
                    unset($ftmp);
                    $func = "jrImage_filter_{$_flt[0]}_extension";
                    if (function_exists($func)) {
                        $ext = $func();
                        if ($ext) {
                            $_image['image_extension'] = $ext;
                            $cache_file                = "{$cdr}/{$cid}.{$ext}";
                        }
                    }
                }
            }
        }
    }

    //----------------------------------
    // Create Cached image
    //----------------------------------
    switch (strtolower($_image['image_extension'])) {
        case 'jpg':
        case 'jpe':
        case 'jpeg':
        case 'jfif':
            imagejpeg($image['handle'], $cache_file, $image['quality']);
            break;
        case 'png':
            imagepng($image['handle'], $cache_file);
            break;
        case 'gif':
            if (function_exists('imagegif')) {
                imagecolortransparent($image['handle']);
                imagegif($image['handle'], $cache_file);
            }
            else {
                imagepng($image['handle'], $cache_file);
            }
            break;
    }
    imagedestroy($image['source']);
    chmod($cache_file, $_conf['jrCore_file_perms']);
    return $cache_file;
}

/**
 * jrImage_display_default_image
 * Display the "default" image when no image is available
 * @param $_post array incoming $_post
 * @param $_conf array System Config
 * @return null
 */
function jrImage_display_default_image($_post, $_conf)
{
    // Make sure we get a valid image width
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$_post['_3']}"])) {
        jrCore_notice('CRI', "invalid image size - must be one of: " . implode(',', array_keys($_sz)));
    }
    $_post['width'] = $_sz["{$_post['_3']}"];

    // Check for default image over ride
    $img = APP_DIR . "/modules/jrImage/img/default.png";
    if (is_file(APP_DIR . "/data/media/0/0/mod_jrImage_default.png")) {
        $img = APP_DIR . "/data/media/0/0/mod_jrImage_default.png";
    }
    elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$_post['module']}_default.png")) {
        $img = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/{$_post['module']}_default.png";
    }
    elseif (is_file(APP_DIR . "/modules/{$_post['module']}/img/default.png")) {
        $img = APP_DIR . "/modules/{$_post['module']}/img/default.png";
    }
    elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/default.png")) {
        $img = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/default.png";
    }

    // get sized/cached image for display
    $_im                    = getimagesize($img);
    $_rt                    = array(
        'image_name'      => 'default.png',
        'image_type'      => 'image/png',
        'image_size'      => filesize($img),
        'image_width'     => $_im[0],
        'image_height'    => $_im[1],
        'image_extension' => 'png'
    );
    $_post['default_image'] = true;
    $img                    = jrImage_create_image($img, $_rt, $_post, $_conf);

    // Custom headers set by other modules
    $_tmp = jrCore_get_flag('jrcore_set_custom_header');
    if (isset($_tmp) && is_array($_tmp)) {
        foreach ($_tmp as $header) {
            header($header);
        }
    }
    header("Content-type: {$_rt['image_type']}");
    header('Content-Disposition: inline; filename="' . $_rt['image_name'] . '"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
    header('Content-length: ' . filesize($img));
    readfile($img);
    exit;
}

/**
 * jrImage_get_allowed_image_sizes
 */
function jrImage_get_allowed_image_sizes()
{
    $_todo = array(
        131072, 262144, 393216, 524288, 655360, 786432, 1048576, 1572864, 2097152, 2621440, 3145728, 3670016, 4194304, 4718592, 5242880, 6291456, 7340032, 8388608, 9437184, 10485760
    );
    $_out  = array();
    $cmax  = jrCore_get_max_allowed_upload();
    foreach ($_todo as $size) {
        if ($size <= $cmax) {
            $_out[$size] = jrCore_format_size($size);
        }
    }
    return $_out;
}

//---------------------------------------------------------
// IMAGE FILTERS
//---------------------------------------------------------

/**
 * blur
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_blur($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_GAUSSIAN_BLUR);
    return $handle;
}

/**
 * border
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_border($handle, $_args, $height, $width)
{
    // Border width in pixels is first
    if (!is_numeric($_args[1])) {
        $_args[1] = 1;
    }
    $_args[1] = intval($_args[1]);
    // Make sure we get good values, or default to 127
    foreach (array(2, 3, 4) as $num) {
        if (!isset($_args[$num]) || !is_numeric($_args[$num])) {
            $_args[$num] = 0;
        }
        $_args[$num] = intval($_args[$num]);
    }
    $color = imagecolorallocate($handle, $_args[2], $_args[3], $_args[4]);
    $brd_x = 0;
    $brd_y = 0;
    $img_x = (imagesx($handle) - 1);
    $img_y = (imagesy($handle) - 1);
    for ($i = 0; $i < $_args[1]; $i++) {
        imagerectangle($handle, $brd_x++, $brd_y++, $img_x--, $img_y--, $color);
    }
    return $handle;
}

/**
 * brightness
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_brightness($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_BRIGHTNESS, $_args[1]);
    return $handle;
}

/**
 * colorize
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_colorize($handle, $_args, $height, $width)
{
    // Make sure we get good values, or default to 127
    foreach (array(1, 2, 3, 4) as $num) {
        if (!isset($_args[$num]) || !is_numeric($_args[$num])) {
            $_args[$num] = 127;
        }
    }
    imagefilter($handle, IMG_FILTER_COLORIZE, $_args[1], $_args[2], $_args[3], $_args[4]);
    return $handle;
}

/**
 * contrast
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_contrast($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_CONTRAST, $_args[1]);
    return $handle;
}

/**
 * edgedetect
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_edgedetect($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_EDGEDETECT);
    return $handle;
}

/**
 * emboss
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_emboss($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_EMBOSS);
    return $handle;
}

/**
 * grayscale
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_grayscale($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_GRAYSCALE);
    return $handle;
}

/**
 * negative
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_negative($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_NEGATE);
    return $handle;
}

/**
 * pixelate
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_pixelate($handle, $_args, $height, $width)
{
    if (!isset($_args[1]) || !is_numeric($_args[1])) {
        $_args[1] = 5;
    }
    if (!isset($_args[2])) {
        $_args[2] = true;
    }
    imagefilter($handle, IMG_FILTER_PIXELATE, $_args[1], $_args[2]);
    return $handle;
}

/**
 * rotate
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_rotate($handle, $_args, $height, $width)
{
    if (!is_numeric($_args[1]) || $_args[1] <= 0 || $_args[1] >= 360) {
        return $handle;
    }
    if (!isset($_args[2])) {
        $_args[2] = 0;
    }
    $handle = imagerotate($handle, $_args[1], intval($_args[2]));
    imagealphablending($handle, true);
    imagesavealpha($handle, true);

    // See if our rotation affected the size of the image - if so, we need to resize
    $new_h = imagesy($handle);
    $new_w = imagesx($handle);
    if ($new_h != $height || $new_w != $width) {
        $new_resource = imagecreatetruecolor($width, $height);
        if (!imagecopyresampled($new_resource, $handle, 0, 0, 0, 0, $width, $height, $new_w, $new_h)) {
            if (!imagecopyresized($new_resource, $handle, 0, 0, 0, 0, $width, $height, $new_w, $new_h)) {
                return 'unable to create new source image from rotate resulting in new image size';
            }
        }
        $handle = $new_resource;
    }
    return $handle;
}

/**
 * sepia
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_sepia($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_GRAYSCALE);
    imagefilter($handle, IMG_FILTER_BRIGHTNESS, -30);
    imagefilter($handle, IMG_FILTER_COLORIZE, 90, 55, 30);
    return $handle;
}

/**
 * sketch
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_sketch($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_MEAN_REMOVAL);
    return $handle;
}

/**
 * smooth
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_smooth($handle, $_args, $height, $width)
{
    imagefilter($handle, IMG_FILTER_SMOOTH, $_args[1]);
    return $handle;
}

/**
 * rounded corners
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_rounded($handle, $_args, $height, $width)
{
    $_arg = array();
    if (!isset($_args[2]) && is_numeric($_args[1]) && $_args[1] > 1) {
        $_arg[1] = $_arg[2] = $_arg[3] = $_arg[4] = $_args[1];
    }
    elseif (count($_args) === 5) {
        foreach ($_args as $k => $v) {
            if ($k < 1) {
                continue;
            }
            if (!is_numeric($v)) {
                return false;
            }
            $_arg[$k] = (int) $v;
        }
    }
    else {
        return false;
    }
    if ($tp_col = imagecolorallocatealpha($handle, 0, 0, 0, 127)) {
        $_r = $_c = array();
        // radius (ellipse width and height)
        foreach ($_arg as $k => $v) {
            switch ($v) {
                case 2:
                case 4:
                case 6:
                case 8:
                case 10:
                    $_r[$k] = round($v * 3.3);
                    break;
                default:
                    $_r[$k] = round($v * 3);
                    break;
            }
            // Center of ellipse
            $_c[$k] = round($v * .45);
        }
        // Upper Left
        if ($_arg[1] > 0) {
            $x = ($_arg[1] + $_c[1]); // x offset
            $y = ($_arg[1] + $_c[1]); // y offset
            imagearc($handle, $x, $y, $_r[1], $_r[1], 180, 270, $tp_col);
            imagefilltoborder($handle, 0, 0, $tp_col, $tp_col);
        }
        // Lower Left
        if ($_arg[4] > 0) {
            $x = ($_arg[4] + $_c[4]); // x offset
            $y = (($height - 1) - ($_arg[4] + $_c[4])); // y offset
            imagearc($handle, $x, $y, $_r[4], $_r[4], 90, 180, $tp_col);
            imagefilltoborder($handle, 1, ($height - 1), $tp_col, $tp_col);
        }
        // Upper Right
        if ($_arg[2] > 0) {
            $x = (($width - 1) - ($_arg[2] + $_c[2])); // x offset
            $y = ($_arg[2] + $_c[2]); // y offset
            imagearc($handle, $x, $y, $_r[2], $_r[2], 270, 0, $tp_col);
            imagefilltoborder($handle, ($width - 1), 1, $tp_col, $tp_col);
        }
        // Lower Right
        if ($_arg[3] > 0) {
            $x = (($width - 1) - ($_arg[3] + $_c[3])); // x offset
            $y = (($height - 1) - ($_arg[3] + $_c[3])); // x offset
            imagearc($handle, $x, $y, $_r[3], $_r[3], 0, 90, $tp_col);
            imagefilltoborder($handle, ($width - 1), ($height - 1), $tp_col, $tp_col);
        }
        return $handle;
    }
    return false;
}

/**
 * rounded corners - extension (must be png)
 * @return string
 */
function jrImage_filter_rounded_extension()
{
    return 'png';
}

/**
 * cut corners
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_cut($handle, $_args, $height, $width)
{
    $_cc = array();
    if (!isset($_args[2]) && is_numeric($_args[1])) {
        $_cc[1] = $_cc[2] = $_cc[3] = $_cc[4] = $_args[1];
    }
    elseif (count($_args) === 5) {
        foreach ($_args as $k => $v) {
            if ($k < 1) {
                continue;
            }
            if (!is_numeric($v)) {
                return false;
            }
            $_cc[$k] = (int) $v;
        }
    }
    else {
        return false;
    }
    if ($tp_col = imagecolorallocatealpha($handle, 0, 0, 0, 127)) {
        // Upper Left
        if ($_cc[1] > 0) {
            imageline($handle, 0, $_cc[1], $_cc[1], 0, $tp_col);
            imagefilltoborder($handle, 0, 0, $tp_col, $tp_col);
        }
        // Lower Left
        if ($_cc[4] > 0) {
            imageline($handle, 0, (($height - 1) - $_cc[4]), $_cc[4], ($height - 1), $tp_col);
            imagefilltoborder($handle, 0, ($height - 1), $tp_col, $tp_col);
        }
        // Upper Right
        if ($_cc[2] > 0) {
            imageline($handle, (($width - 1) - $_cc[2]), 0, ($width - 1), $_cc[2], $tp_col);
            imagefilltoborder($handle, ($width - 1), 0, $tp_col, $tp_col);
        }
        // Lower Right
        if ($_cc[3] > 0) {
            imageline($handle, ($width - 1), (($height - 1) - $_cc[3]), (($width - 1) - $_cc[3]), ($height - 1), $tp_col);
            imagefilltoborder($handle, ($width - 1), ($height - 1), $tp_col, $tp_col);
        }
        return $handle;
    }
    return false;
}

/**
 * cut corners - extension (must be png)
 * @return string
 */
function jrImage_filter_cut_extension()
{
    return 'png';
}

/**
 * reflection
 * @param $handle resource Incoming Image resource
 * @param $_args array Filter params (0 = filter name)
 * @param $height int Output height
 * @param $width int Output width
 * @return resource
 */
function jrImage_filter_reflection($handle, $_args, $height, $width)
{
    if (!function_exists('imagelayereffect')) {
        return false;
    }
    $rf_h = round($height * ($_args[1] / 100));
    // Buffer to hold our reflection
    if ($buff = imagecreatetruecolor($width, $rf_h)) {

        imagesavealpha($handle, true);
        imagealphablending($handle, false);
        imagesavealpha($buff, true);
        imagealphablending($buff, false);

        // We need to "squish" the reflection into a bit smaller space to give it the
        // appearance that it is coming "forward" of the image
        $rf_h = round($rf_h * .50);

        for ($y = 0; $y < $rf_h; $y++) {
            imagecopy($buff, $handle, 0, $y, 0, ($height - $y - 1), $width, 1);
        }
        $alpha_s = 80;
        $alpha_e = 0;
        $alpha_l = abs($alpha_s - $alpha_e);

        $new = imagecreatetruecolor($width, ($height + $rf_h));
        imagesavealpha($new, true);
        imagealphablending($new, false);

        imagecopy($new, $handle, 0, 0, 0, 0, $width, $height);
        imagecopy($new, $buff, 0, $height, 0, 0, $width, $rf_h);

        imagelayereffect($new, IMG_EFFECT_OVERLAY);

        for ($y = 0; $y <= $rf_h; $y++) {
            //  Get % of reflection height
            $pct   = ($y / $rf_h);
            $alpha = (int) ($alpha_s - ($pct * $alpha_l));
            $alpha = (127 - $alpha);
            imagefilledrectangle($new, 0, ($height + $y), $width, ($height + $y), imagecolorallocatealpha($new, 127, 127, 127, $alpha));
        }
        imagedestroy($buff);
        return $new;
    }
    return false;
}

/**
 * reflection - extension (must be png)
 * @return string
 */
function jrImage_filter_reflection_extension()
{
    return 'png';
}


//---------------------------------------------------------
// Smarty template functions
//---------------------------------------------------------

/**
 * Display a "stacked" image setup
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrImage_stacked_image($params, $smarty)
{
    // Make sure we get what we need
    if (!isset($params['module']{0})) {
        return 'jrImage_stacked_image: module parameter required';
    }
    if (!isset($params['type']{0})) {
        return 'jrImage_stacked_image: image type parameter required';
    }
    if (!isset($params['item_id']) || strlen($params['item_id']) === 0) {
        return 'jrImage_stacked_image: image item_id parameter required';
    }
    if (!isset($params['size']{0})) {
        return 'jrImage_stacked_image: image size parameter required';
    }
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$params['size']}"])) {
        return 'jrImage_stacked_image: invalid size parameter';
    }

    $bw = 2;
    if (isset($params['border_width'])) {
        $bw = (int) $params['border_width'];
    }
    $bs = 'solid';
    if (isset($params['border_style'])) {
        $bs = $params['border_style'];
    }
    $bc = '#FFF';
    if (isset($params['border_color'])) {
        $bc = $params['border_color'];
    }

    $_md = explode(',', $params['module']);
    $_ty = explode(',', $params['type']);
    $_im = explode(',', $params['item_id']);
    // figure our height and width  of the holding div.
    // NOTE: you cannot do this in CSS for absolutely positioned elements within a DIV
    $off = round($_sz["{$params['size']}"] / 6);
    $isz = intval(count($_im) - 1);
    $p_h = $_sz["{$params['size']}"] + ($off * $isz) + ((count($_im) + 1) * $bw);
    $p_w = $_sz["{$params['size']}"] + ($off * $isz) + ($bw * 2);
    $out = '<div class="image_stack" style="display:inline-block;position:relative;height:' . $p_h . 'px;width:' . $p_w . 'px">' . "\n";
    foreach ($_im as $k => $iid) {
        if (!jrCore_checktype($iid, 'number_nz')) {
            continue;
        }
        $_tm            = $params;
        $_tm['item_id'] = $iid;
        // See if we were already given a class
        if (isset($_tm['class']{0})) {
            $_tm['class'] .= " image_stack{$k}";
        }
        else {
            $_tm['class'] = "image_stack{$k}";
        }
        $_tm['style'] = "position:absolute;z-index:" . ($k * 10) . ";border-width:{$bw}px;border-style:{$bs};border-color:{$bc}";
        if ($k > 0) {
            $t_off = round($_sz["{$params['size']}"] / 6) * $k;
            $l_off = $t_off * 2;
            $_tm['style'] .= ";top:{$t_off}px;left:{$l_off}px";
        }
        // Check for module
        $_tm['module'] = $_md[0];
        if (isset($_md[$k])) {
            $_tm['module'] = $_md[$k];
        }
        $_tm['type'] = $_ty[0];
        if (isset($_ty[$k])) {
            $_tm['type'] = $_ty[$k];
        }
        $_tm['crop'] = 'auto';
        $out .= smarty_function_jrImage_display($_tm, $smarty) . "\n";
    }
    $out .= '</div>';
    return $out;
}

/**
 * Display an image for a module
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrImage_display($params, $smarty)
{
    global $_conf;
    // Make sure we get what we need
    if (!isset($params['module']{0})) {
        return 'jrImage_display: module parameter required';
    }
    if (!isset($params['type']{0})) {
        return 'jrImage_display: image type parameter required';
    }
    if (!isset($params['item_id']) || !jrCore_checktype($params['item_id'], 'number_nz')) {
        return 'jrImage_display: image item_id parameter required';
    }
    if (!isset($params['size']{0})) {
        return 'jrImage_display: image size parameter required';
    }
    $_sz = jrImage_get_allowed_image_widths();
    if (!isset($_sz["{$params['size']}"])) {
        return 'jrImage_display: invalid size parameter';
    }

    // Unique URL tag
    switch ($params['module']) {
        case 'jrUser':
            $key = '_user_id';
            break;
        case 'jrProfile':
            $key = '_profile_id';
            break;
        default:
            $key = '_item_id';
            break;
    }

    // Check for height and width.  Note that if our height or width are passed
    // in as false, we do NOT set them (for CSS media queries).

    // Width
    if (isset($params['width']) && jrCore_checktype($params['width'], 'number_nz')) {
        $wid = " width=\"" . $params['width'] . "\"";
    }
    elseif (isset($params['width']) && ($params['width'] === false || $params['width'] == 'false')) {
        $wid = '';
    }
    else {
        $wid = " width=\"" . $_sz["{$params['size']}"] . "\"";
    }

    // Height
    $hgt = '';
    if (isset($params['height']) && jrCore_checktype($params['height'], 'number_nz')) {
        $hgt = " height=\"" . $params['height'] . "\"";
    }
    if (strlen($hgt) === 0 && (isset($params['height']) && $params['height'] !== false)) {
        $hgt = " height=\"" . $_sz["{$params['size']}"] . "\"";
    }

    // Get active media system
    $plg = jrCore_get_active_media_system();
    $fnc = "_{$plg}_media_get_image_url";
    if (function_exists($fnc)) {
        // if we have not created this version yet, create at access time and store in S3
        // need to maintain a hash DB of everything already created
        $url = $fnc($params);
    }
    else {
        $url = jrCore_get_module_url($params['module']);
        $url = "{$_conf['jrCore_base_url']}/{$url}/image/{$params['type']}/{$params['item_id']}/{$params['size']}";

        // Check for cropping and filters
        if (isset($params['crop'])) {
            $url .= "/crop={$params['crop']}";
        }
        if (isset($params['filter'])) {
            $url .= "/filter={$params['filter']}";
        }

        if (isset($params['_v']) && strlen($params['_v']) > 0) {
            $url .= '/_v=' . (int) $params['_v'];
        }
        elseif (isset($smarty->tpl_vars['item']->value[$key]) && $smarty->tpl_vars['item']->value[$key] == $params['item_id']) {
            if (isset($smarty->tpl_vars['item']->value["{$params['type']}_time"])) {
                $url .= '/_v=' . (int) $smarty->tpl_vars['item']->value["{$params['type']}_time"];
                $params['_item'] = $smarty->tpl_vars['item']->value;
            }
        }
        elseif ($key == '_profile_id' && isset($smarty->tpl_vars['_profile_id']) && $smarty->tpl_vars['_profile_id']->value == $params['item_id']) {
            if (isset($smarty->tpl_vars['profile_image_time']->value)) {
                $url .= '/_v=' . (int) $smarty->tpl_vars['profile_image_time']->value;
                $params['_item'] = $smarty->tpl_vars;
            }
        }
        elseif (isset($smarty->tpl_vars['_items']) && is_array($smarty->tpl_vars['_items']->value)) {
            foreach ($smarty->tpl_vars['_items']->value as $v) {
                if (isset($v[$key]) && $v[$key] == $params['item_id'] && isset($v["{$params['type']}_time"])) {
                    $url .= '/_v=' . (int) $v["{$params['type']}_time"];
                    $params['_item'] = $v;
                    break;
                }
            }
        }
    }

    // Additional tags
    if (!isset($params['alt'])) {
        $params['alt'] = '';
    }
    $_chck = array('alt', 'class', 'style', 'id', 'title');
    $attrs = '';
    foreach ($_chck as $attr) {
        if (isset($params[$attr])) {
            $attrs .= " {$attr}=\"" . jrCore_entity_string($params[$attr]) . "\"";
        }
    }
    // Our final img source URL
    $_rs = array(
        'src' => "<img src=\"{$url}\"" . $wid . $hgt . $attrs . '>'
    );
    // Trigger display event
    $_rs = jrCore_trigger_event('jrImage', 'img_src', $_rs, $params);
    // Assign?
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_rs['src']);
        return '';
    }
    return $_rs['src'];
}

/**
 * Copy image to another image preserving transparency
 */
function jrImage_imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}
