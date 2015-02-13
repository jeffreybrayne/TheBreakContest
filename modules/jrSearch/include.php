<?php
/**
 * Jamroom 5 Search module
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
 * meta
 */
function jrSearch_meta()
{
    $_tmp = array(
        'name'        => 'Search',
        'url'         => 'search',
        'version'     => '1.2.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Search for items in modules that have registered with the system',
        'category'    => 'listing',
        'requires'    => 'jrCore:5.1.0',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSearch_init()
{
    jrCore_register_module_feature('jrCore', 'css', 'jrSearch', 'jrSearch.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSearch', 'jrSearch.js');
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrSearch_db_search_params_listener');
    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Add support for custom list parameters
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrSearch_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_urls, $_post;
    if (isset($_post['module_url']) && (!isset($_post['option']) || strlen($_post['option']) == 0 || !isset($_urls["{$_post['module_url']}"])) && isset($_post['ss']) && strlen($_post['ss']) > 0 && isset($_SESSION['jr-search-fields'])) {
        // See if this is from a profile or the site
        if (!isset($_urls["{$_post['module_url']}"])) {
            // Profile...
            $pfx = jrCore_db_get_prefix($_urls["{$_post['option']}"]);
        }
        else {
            // Module
            $pfx = jrCore_db_get_prefix($_post['module']);
        }
        if ($pfx) {
            // We are adding a search condition
            if (!isset($_data['search'])) {
                $_data['search'] = array();
            }
            switch ($_SESSION['jr-search-fields']) {
                case 'all':
                    $_data['search'] = "{$pfx}_% like %{$_post['ss']}%";
                    break;
                default:
                    $_fld = explode(',', $_SESSION['jr-search-fields']);
                    if ($_fld && is_array($_fld)) {
                        $_tmp = array();
                        foreach ($_fld as $field) {
                            if (strpos($field, $pfx) === 0) {
                                $_tmp[] = "{$field} like %{$_post['ss']}%";
                            }
                        }
                        if ($_tmp && count($_tmp) > 0) {
                            $_data['search'][] = implode(' || ', $_tmp);
                        }
                    }
                    break;
            }
        }

    }
    return $_data;
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * Build a search form
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_form($params, $smarty)
{
    global $_conf;

    // In: module="ModuleName" or module="all" for a global search (default: all)
    // In: page (default:1)
    // In: pagebreak (default:10)
    // In: template (default: html_search_form.tpl)
    // In: class (optional)
    // In: style (optional)
    // In: assign (optional)

    if (!jrCore_module_is_active('jrSearch')) {
        return '';
    }

    // Check the incoming parameters
    if (empty($params['module'])) {
        $params['module'] = 'all';
    }
    if (!isset($params['page']) || !jrCore_checktype($params['page'], 'number_nz')) {
        $params['page'] = 1;
    }

    if (!isset($params['pagebreak']) || !jrCore_checktype($params['pagebreak'], 'number_nz')) {
        $params['pagebreak'] = 4;
    }

    if (empty($params['value'])) {
        $_lang = jrUser_load_lang_strings();
        $params['value'] = $_lang['jrSearch'][1];
    }

    if (empty($params['style'])) {
        $params['style'] = '';
    }

    if (empty($params['class'])) {
        $params['class'] = '';
    }

    if (!empty($params['template'])) {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = 'html_search_form.tpl';
        $params['tpl_dir']  = 'jrSearch';
    }
    if (!isset($params['method'])) {
        $params['method'] = 'post';
    }
    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrSearch'][$k] = $v;
    }

    // Call the appropriate template and return
    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && strlen($params['assign']) > 0) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * jrSearch_recent
 * Show most recent searches
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_recent($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrSearch')) {
        return '';
    }
    // Check the incoming parameters
    $_s = array();
    if (isset($params['user_id']) && jrCore_checktype($params['user_id'], 'number_nz')) {
        $_s[] = "_user_id = {$params['user_id']}";
    }
    if (isset($params['module']) && $params['module'] != '') {
        $_s[] = "search_module = {$params['module']}";
    }
    if (!isset($params['limit']) || jrCore_checktype($params['limit'], 'number_nz') || $params['limit'] > 100) {
        $params['limit'] = 5;
    }
    if (!isset($params['style']) || strlen($params['style']) === 0) {
        $params['style'] = '';
    }
    if (!isset($params['class']) || strlen($params['class']) === 0) {
        $params['class'] = '';
    }
    if (isset($params['template']) && $params['template'] != '') {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "search_recent.tpl";
        $params['tpl_dir']  = 'jrSearch';
    }

    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrSearch'][$k] = $v;
    }

    // Get most recent
    $_s  = array(
        'search'        => $_s,
        'order_by'      => array("_created" => "desc"),
        'return_keys'   => array('search_module', 'search_string'),
        'skip_triggers' => true,
        'limit'         => $params['limit']
    );
    $_rt = jrCore_db_search_items('jrSearch', $_s);
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $k => $rt) {
            $_tmp['jrSearchRecent'][$k]['module'] = $rt['search_module'];
            $_tmp['jrSearchRecent'][$k]['string'] = jrCore_entity_string($rt['search_string']);
        }
    }

    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show most popular searches
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_popular($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrSearch')) {
        return '';
    }
    // Check the incoming parameters
    if (!isset($params['limit']) || !jrCore_checktype($params['limit'], 'number_nz') || $params['limit'] > 100) {
        $params['limit'] = 5;
    }
    if (!isset($params['style']) || strlen($params['style']) === 0) {
        $params['style'] = '';
    }
    if (!isset($params['class']) || strlen($params['class']) === 0) {
        $params['class'] = '';
    }
    if (isset($params['template']) && $params['template'] != '') {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "search_popular.tpl";
        $params['tpl_dir']  = 'jrSearch';
    }
    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrSearch'][$k] = $v;
    }

    // Get most popular
    $tbl = jrCore_db_table_name('jrSearch', 'item_key');
    $req = "SELECT `_item_id`, COUNT(*) AS cnt FROM {$tbl} WHERE `key` = 'search_string' GROUP BY `value` ORDER BY `count` DESC LIMIT {$params['limit']}";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt[0]) && is_array($_rt[0])) {
        foreach ($_rt as $k => $rt) {
            $_item = jrCore_db_get_item('jrSearch', $rt['_item_id']);
            $_tmp['jrSearchPopular'][$k]['module'] = $_item['search_module'];
            $_tmp['jrSearchPopular'][$k]['string'] = jrCore_entity_string($_item['search_string']);
            $_tmp['jrSearchPopular'][$k]['count']  = (int) $rt['cnt'];
        }
    }

    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && strlen($params['assign']) > 0) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Show search area for a module index
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSearch_module_form($params, $smarty)
{
    global $_urls, $_post;
    if (!isset($params['module'])) {
        if (!isset($_post['module'])) {
            jrCore_smarty_missing_error('module');
        }
        $params['module'] = $_post['module'];
    }
    if (!isset($params['template'])) {
        $params['template'] = 'search_module_form.tpl';
    }
    if (!isset($params['fields'])) {
        $params['fields'] = 'all';
    }
    if (!isset($_post['ss']) || strlen($_post['ss']) === 0) {
        $_SESSION['jr-search-fields'] = $params['fields'];
    }

    // See if we are on a SITE module index (index.tpl) OR
    // on a profile module index (item_index.tpl)
    if (isset($_post['module_url'])) {
        $params['search_url'] = $_post['module_url'];
        if (isset($_post['module_url']) && !isset($_urls["{$_post['module_url']}"])) {
            // We're on a profile...
            $params['search_url'] = "{$_post['module_url']}/{$_post['option']}";
        }
    }
    $out = jrCore_parse_template($params['template'], $params, 'jrSearch');
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
