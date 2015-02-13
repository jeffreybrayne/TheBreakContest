<?php
/**
 * Jamroom 5 Profile Blog module
 *
 * copyright 2003 - 2015
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
 * meta
 */
function jrBlog_meta()
{
    $_tmp = array(
        'name'        => 'Profile Blog',
        'url'         => 'blog',
        'version'     => '1.1.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add blogging capabilities to User profiles',
        'category'    => 'profiles',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrBlog_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrBlog', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrBlog', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrBlog', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'jrBlog', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'jrBlog', 'on');
    jrCore_register_module_feature('jrCore', 'item_order_support', 'jrBlog', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrBlog', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'jrBlog', 'update', 'item_action.tpl');

    // remove any blog posts that are set for a future date.
    jrCore_register_event_listener('jrCore', 'db_get_item', 'jrBlog_db_get_item_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrBlog_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrBlog_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'view_results', 'jrBlog_view_results_listener');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrBlog', 'blog_title', 29);

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'jrBlog', 'profile_jrBlog_item_count', 29);

    // When an action is shared via jrOneAll, we can provide the text of the shared item
    jrCore_register_event_listener('jrOneAll', 'network_share_text', 'jrBlog_network_share_text_listener');

    // Core item buttons
    $_tmp = array(
        'title'  => 'RSS feed button',
        'icon'   => 'rss',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrBlog', 'jrBlog_create_rss_button', $_tmp);

    return true;
}

//---------------------------------------------------------
// BLOG ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "RSS" button for audio index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrBlog_create_rss_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf;
    if ($module == 'jrBlog') {
        if ($test_only) {
            return true;
        }
        $url = jrCore_get_module_url('jrBlog');
        $_rt = array(
            'url'  => "{$_conf['jrCore_base_url']}/{$url}/feed/{$_args['profile_id']}",
            'icon' => 'rss',
            'alt'  => 31
        );
        return $_rt;
    }
    return false;
}

//---------------------------------------------------------
// BLOG EVENT LISTENERS
//---------------------------------------------------------

/**
 * Add share data to a jrOneAll network share
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrBlog_network_share_text_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data:
    // [providers] => twitter
    // [user_token] => ...
    // [user_id] => 1
    // [action_module] => jrBlog
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
    $url = jrCore_get_module_url('jrBlog');
    $txt = $_ln['jrBlog'][19];
    if ($_data['action_mode'] == 'update') {
        $txt = $_ln['jrBlog'][30];
    }
    $_out = array(
        'text' => "{$_data['profile_name']} {$txt}: \"{$_data['blog_title']}\" {$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['blog_title_url']}",
        'link' => array(
            'url'  => "{$_conf['jrCore_base_url']}/{$_data['profile_url']}/{$url}/{$_data['_item_id']}/{$_data['blog_title_url']}",
            'name' => $_data['blog_title']
        )
    );
    // See if they included a picture with the song
    if (isset($_data['blog_image_size']) && jrCore_checktype($_data['blog_image_size'], 'number_nz')) {
        $_out['picture'] = array(
            'url' => "{$_conf['jrCore_base_url']}/{$url}/image/blog_image/{$_data['_item_id']}/large"
        );
    }
    return $_out;
}

/**
 * Check publish time for a blog entry
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBlog_db_get_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // Make sure our publish date is passed
    if (!jrUser_is_admin() && isset($_args['module']) && $_args['module'] == 'jrBlog') {
        if (isset($_data['blog_publish_date']) && $_data['blog_publish_date'] > time()) {
            // leave it in if the user is the admin or the owner of the item.
            if ($_user['_profile_id'] != $_data['_profile_id']) {
                // unset all the blog stuff.
                $_data = array();
            }
        }
    }

    // Page count
    if (isset($_data['blog_text']) && strlen($_data['blog_text']) > 0) {
        $_data['blog_text_page_count'] = 1;
        if (strpos($_data['blog_text'], '<!-- pagebreak -->')) {
            $_data['blog_text_page_count'] = substr_count($_data['blog_text'], '<!-- pagebreak -->') + 1;
        }
    }

    // Check for pagination using <!-- pagebreak -->
    if (isset($_post['option']) && $_post['option'] == 'blog' && isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && isset($_data['blog_text']) && strpos($_data['blog_text'], '<!-- pagebreak -->')) {

        $page = 1;
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $page = (int) $_post['p'];
        }

        // We are paginating this blog entry
        $_temp = explode('<p><!-- pagebreak --></p>', $_data['blog_text']);
        if ($_temp && is_array($_temp)) {

            // Make sure we get a valid page or show first page
            $index = ($page - 1);
            if (!isset($_temp[$index])) {
                $index = 0;
                $page  = 1;
            }
            $_data['blog_text']  = $_temp[$index];

            // Setup our pager...
            $_page = array('info' => array());
            $_page['info']['total_pages']   = count($_temp);
            $_page['info']['next_page']     = ($_page['info']['total_pages'] > $page) ? intval($page + 1) : 0;
            $_page['info']['pagebreak']     = 1;
            $_page['info']['page']          = (int) $page;
            $_page['info']['this_page']     = (int) $page;
            $_page['info']['prev_page']     = ($page > 1) ? intval($page - 1) : 0;
            $_page['info']['page_base_url'] = jrCore_strip_url_params(jrCore_get_current_url(), array('p'));

            jrCore_set_flag('jrBlog_page_parameters', $_page);

            $_data['blog_text'] .= '%%jrBlog_pager%%';
        }
    }
    return $_data;
}

/**
 * Setup pagination on view results
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBlog_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_post['option']) && $_post['option'] == 'blog' && isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz')) {
        if (strpos($_data, '%%jrBlog_pager%%')) {
            // We have a pager.. see if we have the data from the same process
            if ($_page = jrCore_get_flag('jrBlog_page_parameters')) {
                $temp = jrCore_parse_template('list_pager.tpl', $_page, 'jrCore');
                if ($temp) {
                    jrCore_delete_flag('jrBlog_page_parameters');
                    return str_replace('%%jrBlog_pager%%', $temp, $_data);
                }
            }
            else {
                // We have to figure it out
                $txt = jrCore_db_get_item_key('jrBlog', $_post['_1'], 'blog_text');
                if ($txt && strlen($txt) > 0) {
                    $_temp = explode('<p><!-- pagebreak --></p>', $txt);
                    if ($_temp && is_array($_temp)) {

                        $page = 1;
                        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
                            $page = (int) $_post['p'];
                        }

                        // Make sure we get a valid page or show first page
                        $index = ($page - 1);
                        if (!isset($_temp[$index])) {
                            $page  = 1;
                        }

                        // Setup our pager...
                        $_page                          = array('info' => array());
                        $_page['info']['total_pages']   = count($_temp);
                        $_page['info']['next_page']     = ($_page['info']['total_pages'] > $page) ? intval($page + 1) : 0;
                        $_page['info']['pagebreak']     = 1;
                        $_page['info']['page']          = (int) $page;
                        $_page['info']['this_page']     = (int) $page;
                        $_page['info']['prev_page']     = ($page > 1) ? intval($page - 1) : 0;
                        $_page['info']['page_base_url'] = jrCore_strip_url_params(jrCore_get_current_url(), array('p'));

                        $temp = jrCore_parse_template('list_pager.tpl', $_page, 'jrCore');
                        if ($temp) {
                            return str_replace('%%jrBlog_pager%%', $temp, $_data);
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Add blog_text_page_count values to result array
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBlog_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['_items'])) {
        foreach ($_data['_items'] as $k => $v) {
            if (isset($v['blog_text']) && strlen($v['blog_text']) > 0) {
                $_data['_items'][$k]['blog_text_page_count'] = 1;
                if (strpos($v['blog_text'], '<!-- pagebreak -->')) {
                    $_data['_items'][$k]['blog_text_page_count'] = substr_count($v['blog_text'], '<!-- pagebreak -->') + 1;
                }
            }
        }
    }
    return $_data;
}

/**
 * Add blog_publish_date search criteria to blog searches
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrBlog_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_args['module']) && $_args['module'] == 'jrBlog') {
        // If this is NOT an admin user or the profile owner we
        // hide blog posts that have not been published yet
        if (!jrUser_is_admin() || !isset($_post['_profile_id']) || $_post['_profile_id'] != $_user['user_active_profile_id']) {
            $_data['search'][] = 'blog_publish_date < ' . time();
        }
    }
    return $_data;
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * Get initial portion of string up to "pagebreak" HTML comment
 * @param string $string String to format
 * @return string
 */
function smarty_modifier_jrBlog_readmore($string)
{
    // Return portion of string up to first <!-- pagebreak -->
    if (strpos($string, '<!-- pagebreak -->')) {
        list($before,) = explode('<!-- pagebreak -->', $string, 2);
        return jrCore_closetags($before);
    }
    return $string;
}

/**
 * {jrBlog_categories}
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrBlog_categories($params, $smarty)
{
    global $_conf;
    // Enabled?
    if (!jrCore_module_is_active('jrBlog')) {
        return '';
    }
    // Is it allowed in this quota?
    if (!jrProfile_is_allowed_by_quota('jrBlog', $smarty)) {
        return '';
    }
    // get all the categories for this users blog
    if (isset($params['profile_id']) && jrCore_checktype($params['profile_id'], 'number_nz')) {
        $_sp = array(
            'search'                       => array(
                "_profile_id = {$params['profile_id']}"
            ),
            'order_by'                     => array(
                'blog_category' => 'asc'
            ),
            'return_keys'                  => array('_profile_id', 'blog_category', 'blog_category_url', 'profile_url'),
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'limit'                        => 500
        );
        $_rt = jrCore_db_search_items('jrBlog', $_sp);
        if (isset($_rt['_items']) && is_array($_rt['_items'])) {
            $_ct = array();
            $url = jrCore_get_module_url('jrBlog');
            foreach ($_rt['_items'] as $_it) {
                if (!isset($_it['blog_category']) || strlen($_it['blog_category']) === 0) {
                    $_it['blog_category']     = 'default';
                    $_it['blog_category_url'] = 'default';
                }
                if (!isset($_ct["{$_it['blog_category_url']}"])) {
                    $_ct["{$_it['blog_category_url']}"] = array(
                        'url'        => "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/category/{$_it['blog_category_url']}",
                        'title'      => $_it['blog_category'],
                        'item_count' => 1
                    );
                }
                else {
                    $_ct["{$_it['blog_category_url']}"]['item_count']++;
                }
            }
            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $_ct);
            }
        }
    }
    return '';
}
