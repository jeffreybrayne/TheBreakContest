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
 * meta
 */
function jrYouTube_meta()
{
    $_tmp = array(
        'name'        => 'YouTube Support',
        'url'         => 'youtube',
        'version'     => '1.0.18',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add YouTube videos to User profiles',
        'category'    => 'media',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrYouTube_init()
{
    // Event listeners
    jrCore_register_event_listener('jrEmbed', 'tinymce_popup', 'jrYouTube_tinymce_popup_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrYouTube_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrYouTube_verify_module_listener');

    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrYouTube', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrYouTube', 'update');

    // jrYouTube tool views
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrYouTube', 'integrity_check', array('YouTube Integrity Check', 'Checks the integrity of all uploaded YouTube videos'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrYouTube', 'mass_import', array('Mass Import', 'Imports multiple YouTube videos to a specified profile'));

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrYouTube', 'off');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrYouTube', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrYouTube', 'on');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrYouTube', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrYouTube', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrYouTube', 'update', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrYouTube', 'search', 'item_action.tpl');

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrYouTube_network_share_text_listener');

    // We listen for the jrUrlScan 'url_found' trigger and if its a youtube url, add appropriate data to its array
    jrCore_register_event_listener('jrUrlScan', 'url_found', 'jrYouTube_url_found_listener');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrYouTube', 'youtube_title', 41);

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrYouTube', 'profile_jrYouTube_item_count', 41);

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Fix bad count values for items
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrYouTube_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrYouTube', 'item_key');
    $req = "SELECT `_item_id` AS i, `value` AS v FROM {$tbl} WHERE `key` = 'youtube_file_stream_count_count'";
    $_rt = jrCore_db_query($req, 'i', false, 'v');
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        foreach ($_rt as $id => $cnt) {
            jrCore_db_increment_key('jrYouTube', $id, 'youtube_stream_count', $cnt);
        }
        $req = "DELETE FROM {$tbl} WHERE `key` = 'youtube_file_stream_count_count'";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {
            jrCore_logger('INF', "fixed {$cnt} invalid youtube stream count values");
        }
    }
    return $_data;
}

/**
 * Daily maintenance
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrYouTube_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_checktype($_conf['jrYouTube_daily_maintenance'], 'number_nz')) {
        // Get maintenance counter
        $tmp = jrCore_get_temp_value('jrYouTube', 'maintenance_count');
        if (!$tmp || !jrCore_checktype($tmp, 'number_nn')) {
            jrCore_set_temp_value('jrYouTube', 'maintenance_count', 0);
            $tmp = 0;
        }
        // Get items to check
        $iid = 0;
        $num = (isset($_conf['jrYouTube_daily_maintenance']) && jrCore_checktype($_conf['jrYouTube_daily_maintenance'], 'number_nz')) ? (int) $_conf['jrYouTube_daily_maintenance'] : 100;
        $_sp = array(
            "search"         => array(
                "_item_id > {$tmp}"
            ),
            "order_by"       => array(
                "_item_id" => "numerical_asc"
            ),
            'privacy_check'  => false,
            'ignore_pending' => true,
            'limit'          => $num
        );
        $_rt = jrCore_db_search_items('jrYouTube', $_sp);
        if ($_rt && is_array($_rt['_items']) && isset($_rt['_items'][0]) && is_array($_rt['_items'][0])) {
            // We have some checking to do
            $ctr = 0;
            $del = 0;
            foreach ($_rt['_items'] as $rt) {
                $_xt = jrYouTube_get_feed_data($rt['youtube_id']);
                if (isset($_xt) && $_xt == '404') {
                    // Not looking good for this item
                    jrCore_db_delete_item('jrYouTube', $rt['_item_id']);
                    jrCore_logger('MAJ', "Removed invalid YouTube video - '{$rt['youtube_title']}' owned by '{$rt['profile_name']}'");
                    $del++;
                }
                $iid = $rt['_item_id'];
                $ctr++;
            }
            // Log the counts
            jrCore_logger('INF', "jrYouTube daily maintenance - {$ctr} items checked, {$del} deleted");

            // Save where we are up to for next time
            if (count($_rt['_items']) < $_conf['jrYouTube_daily_maintenance']) {
                // Start over
                $iid = 0;
            }
        }
        jrCore_update_temp_value('jrYouTube', 'maintenance_count', $iid);
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
function jrYouTube_url_found_listener($_data, $_user, $_conf, $_args, $event)
{
    $murl = jrCore_get_module_url('jrYouTube');
    $uurl = jrCore_get_module_url('jrUrlScan');
    // Is it a local youtube url
    if (strpos($_args['url'], $_conf['jrCore_base_url']) == 0) {
        $_x = explode('/', substr($_args['url'], strlen($_conf['jrCore_base_url']) + 1));
    }
    if (isset($_x) && is_array($_x) && isset($_x[1]) && $_x[1] == $murl && jrCore_checktype($_x[2], 'number_nz')) {
        $title = jrCore_db_get_item_key('jrYouTube', $_x[2], 'youtube_title');
        if ($title != '') {
            // Yes
            $_data['_items'][$_args['i']]['title'] = $title;
            $_data['_items'][$_args['i']]['load_url'] = "{$_conf['jrCore_base_url']}/{$uurl}/parse/urlscan_player/{$_x[2]}/0/jrYouTube/__ajax=1";
            $_data['_items'][$_args['i']]['url'] = $_args['url'];
        }
    }
    // Is it a YouTube URL?
    elseif (isset($_args['url']) && stristr($_args['url'], 'youtu')) {
        if ($youtube_id = jrYouTube_extract_id($_args['url'])) {
            $_yt = jrYouTube_get_feed_data($youtube_id);
            if (is_array($_yt)) {
                // Yep - Its a good youtube
                $_data['_items'][$_args['i']]['title'] = $_yt['title'];
                $_data['_items'][$_args['i']]['load_url'] = "{$_conf['jrCore_base_url']}/{$uurl}/parse/urlscan_player/0/{$youtube_id}/jrYouTube/__ajax=1";
                $_data['_items'][$_args['i']]['url'] = $_args['url'];
            }
        }
    }
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
function jrYouTube_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => c6418e9a-b66e-4c6c-xxxx-cdea7e915d03
    // [user_id] => 1
    // [action_module] => jrYouTube
    // [action_data] => (JSON array of data for item initiating action)
    $_data = json_decode($_data['action_data'], true);
    if (!isset($_data) || !is_array($_data)) {
        // Not a youtube action...
        return $_data;
    }
    $_ln = jrUser_load_lang_strings($_data['user_language']);

    // We return an array:
    // 'text' => text to post (i.e. "tweet")
    // 'url'  => URL to media item,
    // 'name' => name if media item
    $url = jrCore_get_module_url('jrYouTube');
    $txt = $_ln['jrYouTube'][36];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrYouTube'][46];
    }
    $_out = array(
        'text' => "{$_conf['jrCore_base_url']}/{$_data['profile_url']} {$_data['profile_name']} {$txt}: \"{$_data['youtube_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['youtube_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['youtube_title_url']}",
            'name' => $_data['youtube_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['youtube_image_size']) && jrCore_checktype($_data['youtube_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/youtube_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

/**
 * Call YouTube feed URL to get JSONn results for YouTube video id
 * @param $id string YouTube video ID
 * @return bool
 */
function jrYouTube_get_feed_data($id)
{
    if (!$id || !preg_match('/[a-zA-Z0-9_-]/', $id)) {
        return false;
    }
    $temp = jrCore_load_url("https://gdata.youtube.com/feeds/api/videos/{$id}?v=2&alt=jsonc", null, 'GET', 443);
    if (!isset($temp) || strlen($temp) === 0) {
        // Curl has failed - lets make sure and try it with file_get_contents instead
        $temp = file_get_contents("https://gdata.youtube.com/feeds/api/videos/{$id}?v=2&alt=jsonc");
    }
    if (!isset($temp) || strlen($temp) === 0) {
        // YouTube did not respond right
        return false;
    }
    $_tmp = json_decode($temp, true);
    if (!$_tmp || !is_array($_tmp) || !isset($_tmp['data'])) {
        jrCore_logger('MAJ', "invalid youtube data returned for: {$id}", $temp);
        return false;
    }
    if ((isset($_tmp['error']['code']) && $_tmp['error']['code'] == '404')) {
        // NOT FOUND
        return '404';
    }
    return $_tmp['data'];
}

/**
 * Extract a YouTube ID from a string
 * @param $str string YouTube ID/URL
 * @return bool|string
 */
function jrYouTube_extract_id($str)
{
    if (strlen($str) === 11 && !preg_match('/[^a-zA-Z0-9_-]/', $str)) {
        return $str;
    }
    // http://youtu.be/VXWF_yi5WB0
    if (strpos($str, 'http://youtu.be/') === 0) {
        $id = trim(substr($str, 16));
        if (strlen($id) === 11) {
            return $id;
        }
    }
    // fall through
    parse_str(parse_url($str, PHP_URL_QUERY), $_tmp);
    if (strlen($_tmp['v']) === 11 && !preg_match('/[^a-zA-Z0-9_-]/', $_tmp['v'])) {
        return $_tmp['v'];
    }
    return false;
}

/**
 * Adds YouTube to the popup tinymce editor for insertion into pages
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrYouTube_tinymce_popup_listener($_data, $_user, $_conf, $_args, $event)
{
    $flag_found = false;
    //over-ride any existing jrYouTube setting.
    foreach ($_data as $k => $tab) {
        if ($tab['module'] == 'jrYouTube') {
            $_data[$k]['tab_location'] = 'jrYouTube';
            $_data[$k]['tab_tpl'] = 'tab_jrYouTube.tpl';
            $_data[$k]['onclick'] = 'loadYouTube()';
            $flag_found = true;
        }
    }
    //this modules tab was not set, so set it.
    if (!$flag_found) {
        $_data[] = array(
            'module'       => 'jrYouTube',
            'name'         => 'youtube',
            'tab_location' => 'jrYouTube',
            'tab_tpl'      => 'tab_jrYouTube.tpl',
            'onclick'      => 'loadYouTube();'
        );
    }
    return $_data;
}

/**
 * Embed a Youtube video into a template
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrYouTube_embed($params, $smarty)
{
    /**
     * In: item_id: required
     * In: width: optional - default 400
     * In: height: optional - default 300
     * In: auto_play: optional - default FALSE
     * In: assign: optional
     * Out: embed code
     */

    // datastore item
    if (isset($params['item_id'])  &&  jrCore_checktype($params['item_id'], 'number_nz')) {
        $_rt = jrCore_db_get_item('jrYouTube', $params['item_id']);
    }

    // direct embed
    if (isset($params['youtube_id']) && preg_match('/[a-zA-Z0-9_-]/', $params['youtube_id'])) {
        $_rt = array(
            'youtube_id' => $params['youtube_id']
        );
    }

    if (!isset($_rt) || !is_array($_rt) || !isset($_rt['youtube_id']) || !preg_match('/[a-zA-Z0-9_-]/', $_rt['youtube_id'])) {
        return 'jrYouTube_embed: invalid youtube_id. set "item_id" or "youtube_id".';
    }
    if (!isset($params['width'])) {
        $params['width'] = '100%';
    }
    if (!isset($params['height'])) {
        $params['height'] = 480;
    }
    if (isset($params['auto_play']) && $params['auto_play'] != 0 && $params['auto_play'] !== false && strtolower($params['auto_play']) != 'false') {
        $params['auto_play'] = '1';
    }
    else {
        $params['auto_play'] = '0';
    }
    $_rt['params'] = $params;
    $_rt['unique_id'] = jrCore_create_unique_string(6);

    $tpl = 'youtube_embed_iframe.tpl';
    if (isset($params['type']) && $params['type'] == 'object') {
        $tpl = 'youtube_embed_object.tpl';
    }
    $out = jrCore_parse_template($tpl, $_rt, 'jrYouTube');

    // Increment play count?
    jrCore_counter('jrYouTube', $params['item_id'], 'youtube_stream');

    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get latest feed data for a YouTube video
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrYouTube_get_feed_data($params, $smarty)
{
    /**
     * smarty call to decode the YouTube item array
     * In: assign: required
     * In: json: required
     * Out: array or nothing
     */
    if (!isset($params['item_id']) || !jrCore_checktype($params['item_id'], 'number_nz')) {
        return 'jrYouTube_get_data: invalid item_id';
    }
    if (!isset($params['assign']) || strlen($params['assign']) === 0) {
        return 'jrYouTube_get_data: invalid assign value';
    }
    $_tmp = jrYouTube_get_feed_data($params['item_id']);
    $smarty->assign($params['assign'], $_tmp);
    return '';
}
