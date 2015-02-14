<?php
/**
 * Jamroom 5 Activity Timeline module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrAction_meta()
{
    $_tmp = array(
        'name'        => 'Activity Timeline',
        'url'         => 'timeline',
        'version'     => '1.6.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Users can enter updates and log activity to their Timeline',
        'category'    => 'users',
        'priority'    => 250, // LOW load priority (we want other listeners to run first)
        'activate'    => true,
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrAction_init()
{
    // Let other modules affect actions
    jrCore_register_event_trigger('jrAction', 'action_save', 'Fired before action is saved to override');
    jrCore_register_event_trigger('jrAction', 'action_stats', 'Fired when the {jrAction_stats} function is called from the templates.');

    // register our custom JS/CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrAction', 'char_count.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrAction', 'jrAction.css');

    // Core options
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrAction', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrAction', true);

    // Add additional search params
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrAction_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrAction_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrAction_db_get_item_listener');

    // "add to time line" option
    jrCore_register_event_listener('jrCore', 'form_display', 'jrAction_form_display_listener');

    // Cleanup any bad events
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrAction_verify_module_listener');

    // RSS Feed
    jrCore_register_event_listener('jrFeed', 'create_rss_feed', 'jrAction_create_rss_feed_listener');

    // notifications
    $_tmp = array(
        'label' => 12, // 'mentioned in an activity stream'
        'help'  => 16 // 'If your profile name is mentioned in an Activity Stream do you want to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrAction', 'mention', $_tmp);

    $_tmp = array(
        'wl'    => 'hash_tags',
        'label' => 'Convert # Tags',
        'help'  => 'If active, hash tags written as #tag will be linked up to a tag search.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrAction', 'jrAction_format_string_convert_hash_tags', $_tmp);

    return true;
}

//----------------------
// STRING FORMATTER
//----------------------

/**
 * Registered core string formatter - Convert # tags
 * @param string $string String to format
 * @param int $quota_id Quota ID for Profile ID
 * @return string
 */
function jrAction_format_string_convert_hash_tags($string, $quota_id = 0)
{
    if (!strpos(' ' . $string, ' #')) {
        return $string;
    }
    // NOTE: We don't want to mess with any embedded Javascript or CSS
    if (stripos(' ' . $string, '<script')) {
        $out = '';
        $_sv = array();
        foreach (explode('<script', $string) as $k => $part) {
            if (stripos($part, '</script>')) {
                // We have found the actual code portion
                list($beg, $end) = explode('</script>', $part, 2);
                $_sv[$k] = "<script{$beg}</script>";
                $out .= "~~!~~{$k}~~!~~{$end}";
            }
            else {
                $out .= $part;
            }
        }
        $string = $out;
    }
    $string = preg_replace_callback("/(<([^.]+)>)([^<]+)(<\\/\\2>)/s",
        function($_m) {
            global $_conf, $_user;
            $url = jrCore_get_module_url('jrAction');
            return $_m[1] . preg_replace('/(#([_a-z0-9\-]+))/si', '<a href="' . $_conf['jrCore_base_url'] . '/' . $_user['profile_url'] . '/' . $url . '/search/ss=%23$2"><span class="hash_link">$1</span></a>', $_m[3]) . $_m[4];
        },
        $string);

    // If we plucked any JS out earlier, stick it back in
    if (isset($_sv) && is_array($_sv) && count($_sv) > 0) {
        foreach ($_sv as $k => $v) {
            $string = str_replace("~~!~~{$k}~~!~~", $v, $string);
        }
    }
    return $string;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Cleanup any bad action events with empty data
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $_sc = array(
        'search'         => array(
            "action_data = []"
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'no_cache'       => true,
        'limit'          => 50
    );
    $_rt = jrCore_db_search_items('jrAction', $_sc);
    if ($_rt && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $_act) {
            $_tmp = jrCore_db_get_item($_act['action_module'], $_act['action_item_id']);
            if (!$_tmp || !is_array($_tmp)) {
                // This item no longer exists - remove this empty action
                jrCore_db_delete_item('jrAction', $_act['_item_id']);
            }
            else {
                // See if we can clean it up
                foreach ($_tmp as $k => $v) {
                    if (strpos($k, 'quota_') === 0 ||
                        strpos($k, '_item_count') ||
                        strpos($k, '_exif_') ||
                        strpos($k, 'jrStore_first_message') ||
                        $k == 'playlist_items' ||
                        strpos(' ' . $k, 'pending') ||
                        strpos($k, 'user_valid') === 0 ||
                        strpos($k, 'notification') ||
                        strpos($k, '_settings') ||
                        strpos($k, '_payout') ||
                        strpos($k, '_file_t') ||
                        strpos($k, '_file_ext') ||
                        strpos($k, '_bio')
                    ) {
                        unset($_tmp[$k]);
                    }
                }
                if (count($_tmp) > 0) {
                    $_dat = array('action_data' => json_encode($_tmp));
                    jrCore_db_update_item('jrAction', $_act['_item_id'], $_dat);
                }
                else {
                    // Still coming back empty
                    jrCore_db_delete_item('jrAction', $_act['_item_id']);
                }
            }
        }
    }
    return $_data;
}

/**
 * Save an Action to the Time line
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    // See if this module supports actions
    if (isset($_user['quota_jrAction_allowed']) && $_user['quota_jrAction_allowed'] == 'on') {
        list($mod, $view) = explode('/', $_data['form_view']);
        $_as = jrCore_get_registered_module_features('jrCore', 'action_support');
        if ($_as && isset($_as[$mod][$view]) && jrCore_is_profile_referrer(false)) {
            if (!isset($_user['quota_jrAction_show_add']) || $_user['quota_jrAction_show_add'] == 'on') {
                $_lng = jrUser_load_lang_strings();
                $_tmp = array(
                    'name'          => "jraction_add_to_timeline",
                    'label'         => $_lng['jrAction'][13],
                    'help'          => $_lng['jrAction'][14],
                    'type'          => 'checkbox',
                    'default'       => 'on',
                    'required'      => false,
                    'form_designer' => false
                );
                jrCore_form_field_create($_tmp);
            }
            else {
                $_tmp = array(
                    'name'  => "jraction_add_to_timeline",
                    'type'  => 'hidden',
                    'value' => 'on'
                );
                jrCore_form_field_create($_tmp);
            }
        }
    }
    return $_data;
}

/**
 * jrAction_create_rss_feed_listener
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_create_rss_feed_listener($_data, $_user, $_conf, $_args, $event)
{
    // Format latest actions
    if (isset($_args['module']) && $_args['module'] == 'jrAction') {
        foreach ($_data as $k => $_itm) {
            // We set "title", "url" and "description"
            $_ln = jrUser_load_lang_strings();
            $url = jrCore_get_module_url($_itm['action_module']);
            $pfx = jrCore_db_get_prefix($_itm['action_module']);
            if (isset($_itm['action_text'])) {
                $ttl = $_ln['jrAction'][2];
                $_data[$k]['description'] = jrCore_strip_html($_itm['action_text']);
            }
            else {
                $ttl = (isset($_itm['action_item']["{$pfx}_title"])) ? $_itm['action_item']["{$pfx}_title"] : jrCore_strip_html($_itm['action_data']);
                $_data[$k]['description'] = jrCore_strip_html($_itm['action_data']);
            }
            $_data[$k]['title'] = "@{$_itm['profile_name']} - {$ttl}";
            if (isset($_itm['action_item']["{$pfx}_title_url"])) {
                $_data[$k]['url'] = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$url}/{$_itm['_item_id']}/" . $_itm['action_item']["{$pfx}_title_url"];
            }
            else {
                $_data[$k]['url'] = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$url}/{$_itm['_item_id']}";
            }
        }
    }
    return $_data;
}

/**
 * jrAction_db_search_params_listener
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_args['module']) || $_args['module'] != 'jrAction') {
        return $_data;
    }

    // Make sure only registered actions for enabled modules come back
    $_ram = jrCore_get_registered_module_features('jrCore', 'action_support');
    if ($_ram) {
        if (!isset($_data['search']) || !is_array($_data['search'])) {
            $_data['search'] = array();
        }
        $_data['search'][] = "action_module in jrAction," . implode(',', array_keys($_ram));
    }
    unset($_ram);

    // If we are viewing our OWN time line, we don't want to see items we have shared
    // with our followers, otherwise we get double entries in the time line
    if (isset($_data['include_followed']) && ($_data['include_followed'] == 'true' || $_data['include_followed'] === true)) {
        if (!isset($_data['search']) || !is_array($_data['search'])) {
            $_data['search'] = array();
        }
        $_data['search'][] = "action_original_shared_by != {$_user['_user_id']}";
    }
    return $_data;
}

/**
 * jrAction_db_search_items_listener
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_args['module']) || $_args['module'] != 'jrAction' || !is_array($_data['_items'])) {
        return $_data;
    }
    // If we do not need the parsed template, we can skip it
    if (isset($_args['exclude_jrAction_parse_template']) && ($_args['exclude_jrAction_parse_template'] == 'true' || $_args['exclude_jrAction_parse_template'] === true)) {
        return $_data;
    }
    $_ram = jrCore_get_registered_module_features('jrCore', 'action_support');
    foreach ($_data['_items'] as $k => $_v) {
        if (isset($_ram["{$_v['action_module']}"]) && isset($_v['action_data']{3})) {

            $tpl = $_ram["{$_v['action_module']}"]["{$_v['action_mode']}"];
            if (empty($tpl)) {
                // No template to handle this action - note that this should never happen
                // since we've restricted our search modules in the function above
                unset($_data['_items'][$k]);
                continue;
            }
            $pfx = jrCore_db_get_prefix($_v['action_module']);
            $_rp = array('item' => $_v);
            $_rp['item']['action_data'] = json_decode($_v['action_data'], true);

            if ($pfx) {
                // Check for "album" action
                if (strpos($_v['action_mode'], '_album')) {
                    $_data['_items'][$k]['album_title']     = $_rp['item']['action_data']["{$pfx}_album"];
                    $_data['_items'][$k]['album_title_url'] = $_rp['item']['action_data']["{$pfx}_album_url"];
                }
                // Get action URL
                if (isset($_rp['item']['action_data']["{$pfx}_title"])) {
                    $_data['_items'][$k]['action_title']     = $_rp['item']['action_data']["{$pfx}_title"];
                    $_data['_items'][$k]['action_title_url'] = $_rp['item']['action_data']["{$pfx}_title_url"];
                }
            }
            $_data['_items'][$k]['action_item'] = $_rp['item']['action_data'];
            $_data['_items'][$k]['action_data'] = jrCore_parse_template($tpl, $_rp, $_v['action_module']);

        }
        $_data['_items'][$k]['action_shared_by_user'] = 0;
        if (isset($_v['action_shared_by']) && strlen($_v['action_shared_by']) > 0) {
            // How many times an action item has been shared
            $_ids = explode(',', $_v['action_shared_by']);
            $_data['_items'][$k]['action_shared_by_ids']   = array_flip($_ids);
            $_data['_items'][$k]['action_shared_by_count'] = count($_data['_items'][$k]['action_shared_by_ids']);
            if (isset($_data['_items'][$k]['action_shared_by_ids']["{$_user['_user_id']}"])) {
                $_data['_items'][$k]['action_shared_by_user'] = 1;
            }
        }
        else {
            $_data['_items'][$k]['action_shared_by_count'] = 0;
        }
    }
    return $_data;
}

/**
 * Return action_date JSON decoded
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAction_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (!isset($_args['module']) || $_args['module'] != 'jrAction' || !is_array($_data)) {
        return $_data;
    }
    // If we do not need the parsed template, we can skip it
    if (isset($_args['exclude_jrAction_parse_template']) && $_args['exclude_jrAction_parse_template'] === true) {
        return $_data;
    }
    if (isset($_data['action_data']) && strlen($_data['action_data']) > 0) {
        $_data['action_data'] = json_decode($_data['action_data'], true);
        if (!isset($_post['option']) || $_post['option'] != 'share') {
            $pfx = jrCore_db_get_prefix($_data['action_module']);
            if ($pfx) {
                // Check for "album" action
                if (strpos($_data['action_mode'], '_album')) {
                    $_data['album_title']     = $_data['action_data']["{$pfx}_album"];
                    $_data['album_title_url'] = $_data['action_data']["{$pfx}_album_url"];
                }
                // Get action URL
                if (isset($_data['action_data']["{$pfx}_title"])) {
                    $_data['action_title']     = $_data['action_data']["{$pfx}_title"];
                    $_data['action_title_url'] = $_data['action_data']["{$pfx}_title_url"];
                }
            }
        }
        $_data['action_item'] = $_data['action_data'];
        $_rp = array(
            'item' => $_data
        );
        $_data['action_data'] = jrCore_parse_template('item_action.tpl', $_rp, $_data['action_module']);
    }

    // Add in shared by info
    if (!isset($_post['option']) || $_post['option'] != 'share') {
        $_data['action_shared_by_user'] = 0;
        if (isset($_data['action_shared_by']) && strlen($_data['action_shared_by']) > 0) {
            // How many times an action item has been shared
            $_ids = explode(',', $_data['action_shared_by']);
            $_data['action_shared_by_ids']   = array_flip($_ids);
            $_data['action_shared_by_count'] = count($_ids);
            if (isset($_data['action_shared_by_ids']["{$_user['_user_id']}"])) {
                $_data['action_shared_by_user'] = 1;
            }
            // If we are on the item detail page for this item, get info about who shared
            $_sc = array(
                'search' => array(
                    '_user_id in '. implode(',', $_ids)
                ),
                'include_jrProfile_keys' => true,
                'ignore_pending' => true,
                'limit' => 100
            );
            $_rt = jrCore_db_search_items('jrUser', $_sc);
            if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
                $_data['action_shared_by_user_info'] = $_rt['_items'];
            }
            unset($_rt, $_sc);
        }
        else {
            $_data['action_shared_by_count'] = 0;
        }
    }
    $_data['item'] = $_data;
    return $_data;
}

/**
 * Check an Action text for '@' mentions
 * @param $text string Action Text
 * @return bool
 */
function jrAction_check_for_mentions($text)
{
    global $_user;
    // We need to grab any "mentions" our of the text and notify users that
    // they have been mentioned in an activity stream post
    if (!strpos(' ' . $text, '@')) {
        // No mentions of any kind in this text
        return true;
    }
    // We have mentions
    $_words = explode(' ', $text);
    if (isset($_words) && is_array($_words)) {
        $_pr = array();
        foreach ($_words as $word) {
            if (strlen($word) > 0 && strpos($word, '@') === 0) {
                // We have a mention - get profile_id for this profile name
                $_pr[] = substr($word, 1);
            }
        }
        if (isset($_pr) && is_array($_pr)) {
            $_sc = array(
                'search' => array(
                    'profile_url in '. implode(',', $_pr)
                ),
                'return_item_id_only' => true,
                'skip_triggers'       => true,
                'ignore_pending'      => true,
                'limit'               => count($_pr)
            );
            $_rt = jrCore_db_search_items('jrProfile', $_sc);
            if ($_rt && is_array($_rt)) {
                foreach ($_rt as $pid) {
                    $_owners = jrProfile_get_owner_info($pid);
                    if (isset($_owners) && is_array($_owners)) {
                        $_rp = array(
                            'action_user' => $_user,
                            'action_url'  => jrCore_get_local_referrer()
                        );
                        list($sub, $msg) = jrCore_parse_email_templates('jrAction', 'mention', $_rp);
                        foreach ($_owners as $_o) {
                            // NOTE: "0" is from_user_id - 0 is the "system user"
                            if ($_o['_user_id'] != $_user['_user_id']) {
                                jrUser_notify($_o['_user_id'], 0, 'jrAction', 'mention', $sub, $msg);
                            }
                        }
                    }
                }
            }
        }
    }
    return true;
}

/**
 * jrAction_save
 * @param $mode string Mode (create/update/delete/etc)
 * @param $module string Module creating action
 * @param $item_id integer Unique Item ID in module DataStore
 * @param $_data array Array of item-specific key pairs
 * @param $profile_check bool whether to create actions if admin is creating item on another users profile
 * @param $profile_id int By default will use user_active_profile_id - set to alternate
 * @return bool
 */
function jrAction_save($mode, $module, $item_id, $_data = null, $profile_check = true, $profile_id = 0)
{
    global $_post, $_user;
    // See if we are turned on for this module
    if (!isset($_user['quota_jrAction_allowed']) || $_user['quota_jrAction_allowed'] != 'on') {
        return true;
    }
    if (isset($_post['jraction_add_to_timeline']) && $_post['jraction_add_to_timeline'] != 'on') {
        return true;
    }
    elseif (isset($_data['jraction_add_to_timeline']) && $_data['jraction_add_to_timeline'] != 'on') {
        return true;
    }
    // Make sure module is active
    if (!jrCore_module_is_active($module)) {
        return true;
    }
    // Make sure we get a valid $item_id...
    if (!jrCore_checktype($item_id, 'number_nz')) {
        return false;
    }
    $pid = $_user['user_active_profile_id'];
    if (isset($profile_id) && jrCore_checktype($profile_id, 'number_nz')) {
        $pid = (int) $profile_id;
    }

    // If we are an ADMIN USER that is creating something for a profile
    // that is NOT our home profile, we do not record the action.
    $key = jrUser_get_profile_home_key('_profile_id');
    if ($profile_check) {
        if (jrUser_is_admin() && $pid != $key) {
            return true;
        }
    }
    elseif ($module == 'jrRating' && jrUser_is_admin() && $pid != $key) {
        $pid = $key;
    }

    // See if we have been given data straight away or need to grab it
    if (is_null($_data) || !is_array($_data) || count($_data) === 0) {
        $_data = jrCore_db_get_item($module, $item_id);
        if (!$_data || !is_array($_data) || count($_data) === 0) {
            jrCore_logger('MAJ', "jrAction_save() invalid module or item_id - data not found for: {$module}/{$item_id}", $_data);
            return true;
        }
    }

    // Let other modules cancel our action if needed
    $_data = jrCore_trigger_event('jrAction', 'action_save', $_data, $_post);
    if (!$_data || (isset($_data['jraction_add_to_timeline']) && $_data['jraction_add_to_timeline'] == 'off')) {
        // Cancelled by listener
        return true;
    }

    // There are some fields we don't store
    // Try to get rid of some fields that are not needed
    $_temp = $_data;
    unset($_data['user_password']);
    foreach ($_data as $k => $v) {
        if (strpos($k, 'quota_') === 0 ||
            strpos($k, '_item_count') ||
            strpos($k, 'rofile_jrStore') ||
            $k == 'playlist_items' ||
            strpos(' ' . $k, 'pending') ||
            strpos($k, 'user_valid') === 0 ||
            strpos($k, 'notification') ||
            strpos($k, '_settings') ||
            strpos($k, '_payout') ||
            strpos($k, '_file_t') ||
            strpos($k, '_file_ext') ||
            strpos($k, '_bio')
        ) {
            unset($_data[$k]);
        }
    }

    // Make sure we come out of it with something...
    if (count($_data) === 0) {
        // Nothing left to save
        jrCore_logger('MAJ', 'jrAction_save() after pruning, empty result!', array('save' => $_temp, 'post' => $_post));
        return true;
    }

    // Store our action...
    $_save = array(
        'action_mode'     => $mode,
        'action_quota_id' => $_user['profile_quota_id'],
        'action_module'   => $module,
        'action_item_id'  => (int) $item_id,
        'action_data'     => json_encode($_data)
    );

    // See if items being created in this module are pending
    // If so, set pending on action as well.
    $_pnd = jrCore_get_registered_module_features('jrCore', 'pending_support');
    if ($_pnd && isset($_pnd[$module]) && isset($_user["quota_{$module}_pending"]) && intval($_user["quota_{$module}_pending"]) > 0) {
        $_save['action_pending'] = '1';
        $_save['action_pending_linked_item_module'] = $module;
        $_save['action_pending_linked_item_id'] = (int) $item_id;
    }
    $_core = array(
        '_profile_id' => $pid
    );
    $aid = jrCore_db_create_item('jrAction', $_save, $_core);

    // Send out our Action Created trigger
    $_args = array(
        '_user_id' => $_user['_user_id'],
        '_item_id' => $aid,
    );
    jrCore_trigger_event('jrAction', 'create', $_save, $_args);

    jrProfile_reset_cache($profile_id);
    return true;
}

/**
 * {jrAction_form}
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrAction_form($params, $smarty)
{
    global $_user, $_conf, $_mods;
    // Enabled?
    if (!jrCore_module_is_active('jrAction')) {
        return '';
    }
    // Is it allowed in this quota?
    if (!jrProfile_is_allowed_by_quota('jrAction', $smarty)) {
        return '';
    }

    $_lang = jrUser_load_lang_strings();

    // Setup options
    $murl = jrCore_get_module_url('jrAction');
    if (!isset($_conf['jrAction_editor']) || $_conf['jrAction_editor'] != 'on') {

        $_tmp = array('$("#action_update").charCount({allowed: ' . intval($_conf['jrAction_max_length']) . ', warning: 20});');
        jrCore_create_page_element('javascript_ready_function', $_tmp);
        $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrAction/contrib/underscore/underscore-min.js?v={$_mods['jrAction']['module_version']}");
        jrCore_create_page_element('javascript_href', $_tmp);
        $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrAction/contrib/mentions/jquery.mentionsInput.js?v={$_mods['jrAction']['module_version']}");
        jrCore_create_page_element('javascript_href', $_tmp);
        $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrAction/contrib/mentions/lib/jquery.elastic.js?v={$_mods['jrAction']['module_version']}");
        jrCore_create_page_element('javascript_href', $_tmp);
        $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrAction/contrib/mentions/lib/jquery.events.input.js?v={$_mods['jrAction']['module_version']}");
        jrCore_create_page_element('javascript_href', $_tmp);
        $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrAction/contrib/mentions/jquery.mentionsInput.css?v={$_mods['jrAction']['module_version']}");
        jrCore_create_page_element('css_href', $_tmp);
        $_tmp = array("$('#action_update').mentionsInput({
        onDataRequest: function(mode, query, callback) {
            var d = 'q=' + query;
            $.getJSON('{$_conf['jrCore_base_url']}/{$murl}/mention_profiles', d, function(r) {
                r = _.filter(r, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
                callback.call(this, r);
            });
        } });");
        jrCore_create_page_element('javascript_ready_function', $_tmp);
    }

    // If the jrOneAll module is installed, see if the user is linked up to any networks
    $add = '';
    if (jrCore_module_is_active('jrOneAll')) {
        if (isset($_user["quota_jrOneAll_allowed"]) && $_user["quota_jrOneAll_allowed"] == 'on') {
            $tbl = jrCore_db_table_name('jrOneAll', 'link');
            $req = "SELECT provider FROM {$tbl} WHERE user_id = '{$_user['_user_id']}' AND shared = '1'";
            $_rt = jrCore_db_query($req, 'NUMERIC');
            if ($_rt && is_array($_rt)) {
                $add = '<span id="action_networks"><input type="hidden" name="oneall_share_active" value="off"><input type="checkbox" name="oneall_share_active" class="form_checkbox share_checkbox" checked="checked">
                &nbsp;' . $_lang['jrAction'][20] . '&nbsp;<a href="' . $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url('jrOneAll') . '/networks">';
                $url = jrCore_get_module_url('jrImage');
                foreach ($_rt as $_pr) {
                    $add .= "<img src=\"{$_conf['jrCore_base_url']}/{$url}/img/module/jrOneAll/{$_pr['provider']}.png\" width=\"24\" height=\"24\" alt=\"{$_pr['provider']}\">";
                }
                $add .= '</a></span>';
            }
        }
    }

    $url = $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url('jrAction') . '/create_save';
    $tkn = jrCore_form_token_create();
    $img = jrCore_get_module_url('jrImage');
    $img = "{$_conf['jrCore_base_url']}/{$img}/img/skin/{$_conf['jrCore_active_skin']}/form_spinner.gif";
    $out = '<form id="action_form" method="post" action="' . $url . '"><input type="hidden" name="jr_html_form_token" value="' . $tkn . '">';

    if (!isset($_conf['jrAction_editor']) || $_conf['jrAction_editor'] != 'on') {
        $out .= '<textarea id="action_update" name="action_text"></textarea><br>
        <img id="action_submit_indicator" src="' . $img . '" width="24" height="24" alt="'. jrCore_entity_string($_lang['jrCore'][73]) .'"><input id="action_submit" type="button" class="form_button" value="' . str_replace('"', '\"', $_lang['jrAction'][5]) . '" onclick="$(\'#action_submit_indicator\').show(300, function() { ;var t=$(\'#action_update\').val();if (t.length < 1){return false;} else {$(this).attr(\'disabled\',\'disabled\').addClass(\'form_button_disabled\');$(\'#action_form\').submit()} });">' .
        $add . '</form>' .
        '<span id="action_text_counter" class="action_warning">' . $_lang['jrAction'][6] . ': <span id="action_text_num">' . intval($_conf['jrAction_max_length']) . '</span></span>';
    }
    else {
        $out .= '<style type="text/css">.form_editor_holder { width: 100% !important }</style>';
        $tmp  = new StdClass();
        $_pm = array('name' => 'action_text');
        $out .= smarty_function_jrCore_editor_field($_pm, $tmp);
        $out .= '<br><img id="action_submit_indicator" src="' . $img . '" width="24" height="24" alt="'. jrCore_entity_string($_lang['jrCore'][73]) .'"><input id="action_submit" type="button" class="form_button" value="' . str_replace('"', '\"', $_lang['jrAction'][5]) . '" onclick="$(\'#action_submit_indicator\').show(300, function() { ;var t=$(\'#action_text_editor_contents\').val(tinyMCE.get(\'eaction_text\').getContent());if (t.length < 1){return false;} else {$(this).attr(\'disabled\',\'disabled\').addClass(\'form_button_disabled\');$(\'#action_form\').submit()} });">' . $add . '</form>';
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Convert # tags into links to profiles
 * @param string $text String to convert at tags in
 * @return string
 */
function smarty_modifier_jrAction_convert_hash_tags($text)
{
    return jrAction_format_string_convert_hash_tags($text, 0);
}

/**
 * returns an array of stats for actions 'actions' 'following' 'followers' for a profile id
 * called from the templates like this {jrAction_stats assign="action_stats" profile_id=$_profile_id}
 *
 * Will return an array of stats that can be formatted
 * <ul>
 *    <li>{$action_stats.actions} Tweets</li>
 *    <li>{$action_stats.followers} Following</li>
 *    <li>{$action_stats.following} Followers</li>
 * </ul>
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_jrAction_stats($params, $smarty)
{
    // Enabled?
    if (!jrCore_module_is_active('jrAction')) {
        return '';
    }
    // Is it allowed in this quota?
    if (!jrProfile_is_allowed_by_quota('jrAction', $smarty)) {
        return '';
    }
    $out = array();

    if (jrCore_checktype($params['profile_id'], 'number_nz')) {
        //actions
        $_sp = array(
            'search'        => array(
                "_profile_id = {$params['profile_id']}"
            ),
            'skip_triggers' => true,
            'limit'         => 1000000
        );
        $_rt = jrCore_db_search_items('jrAction', $_sp);
        $out['actions'] = $_rt['info']['total_items'];
    }

    // Trigger our action_stats event  (jrFollowers adds in 'following' and 'followers')
    $out = jrCore_trigger_event('jrAction', 'action_stats', $out, $params);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
