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

//------------------------------
// Search results
// In: $_post['search_string']
// In: $_post['_1'] = module
// In: $_post['_2'] = page
// In: $_post['_3'] = pagebreak
//------------------------------
function view_jrSearch_results($_post, $_user, $_conf)
{
    global $_mods;
    if (empty($_post['search_string'])) {
        if (isset($_SESSION['jrsearch_last_search_string'])) {
            $_post['search_string'] = $_SESSION['jrsearch_last_search_string'];
        }
        else {
            jrCore_page_not_found();
        }
    }
    $_SESSION['jrsearch_last_search_string'] = jrCore_entity_string(strip_tags($_post['search_string']));

    // Do search and get results
    $out = jrCore_parse_template('header.tpl');

    // First - find modules we are going to be searching
    $_rm = jrCore_get_registered_module_features('jrSearch', 'search_fields');

    // Allow other modules to inject into search
    $_rm = jrCore_trigger_event('jrSearch', 'search_fields', $_rm);

    // Specific modules
    if (!empty($_post['_1']) && $_post['_1'] != 'all' && isset($_mods["{$_post['_1']}"])) {
        $_tm = explode(',', $_post['_1']);
        if ($_tm && is_array($_tm)) {
            $_at = array();
            foreach ($_tm as $mod) {
                if (isset($_rm[$mod])) {
                    $_at[$mod] = $_rm[$mod];
                }
            }
            $_rm = $_at;
        }
    }

    // Check for custom/additional search fields
    if (isset($_conf['jrSearch_search_fields']) && strlen($_conf['jrSearch_search_fields']) > 0) {
        $_af = explode("\n", $_conf['jrSearch_search_fields']);
        if ($_af && is_array($_af)) {
            $_pf = array();
            foreach ($_mods as $dir => $_in) {
                if (isset($_rm[$dir])) {
                    if (isset($_in['module_prefix']) && strlen($_in['module_prefix']) > 0) {
                        $_pf["{$_in['module_prefix']}"] = $dir;
                    }
                }
            }
            foreach ($_af as $fld) {
                $fld = trim($fld);
                // See if we have a lang string
                if (strpos($fld, ':')) {
                    list($fld, $lng) = explode(':', $fld, 2);
                    $lng = intval($lng);
                }
                else {
                    $lng = $fld;
                }
                list($pfx,) = explode('_', $fld, 2);
                if (isset($_pf[$pfx])) {
                    $smod = $_pf[$pfx];
                    if (!isset($_rm[$smod])) {
                        $_rm[$smod] = array();
                        $_rm[$smod][$fld] = $lng;
                    }
                    else {
                        $tkey = array_keys($_rm[$smod]);
                        $tkey = reset($tkey);
                        $fval = $_rm[$smod][$tkey];
                        unset($_rm[$smod]);
                        $_rm[$smod]["{$tkey},{$fld}"] = $fval;
                    }
                }
            }
        }
    }

    // Make sure profiles show up first
    if (isset($_rm['jrProfile'])) {
        $_tm = array(
            'jrProfile' => $_rm['jrProfile']
        );
        unset($_rm['jrProfile']);
        $_rm = array_merge($_tm, $_rm);
    }

    if (isset($_rm) && is_array($_rm)) {

        // figure pagebreak
        $page = 1;
        if (!empty($_post['_2'])) {
            $page = (int) $_post['_2'];
        }
        $pbrk = 4;
        if (!empty($_post['_3'])) {
            $pbrk = (int) $_post['_3'];
        }
        $_fn = array(
            'titles'  => array(),
            'results' => array()
        );
        $_ln = jrUser_load_lang_strings();
        $ltl = '';
        $ttl = 0;
        foreach ($_rm as $mod => $_mod) {
            if (!jrCore_module_is_active($mod)) {
                continue;
            }
            $pfx = jrCore_db_get_prefix($mod);
            if ($pfx) {
                $_sc = array(
                    'search'                       => array(),
                    'pagebreak'                    => $pbrk,
                    'page'                         => $page,
                    'exclude_jrProfile_quota_keys' => true
                );
                $fnc = false;
                foreach ($_mod as $fields => $title) {

                    // A module can give us a custom search function
                    if (function_exists($fields)) {
                        $fnc = $fields;
                    }
                    else {
                        if (strpos($fields, ',')) {
                            $_fl = array();
                            foreach (explode(',', $fields) as $field) {
                                $field = trim($field);
                                $_fl[] = "{$field} LIKE %{$_post['search_string']}%";
                            }
                            $_sc['search'][] = implode(' || ', $_fl);
                        }
                        else {
                            $_sc['search'][] = "{$fields} LIKE %{$_post['search_string']}%";
                        }
                    }
                    $_fn['titles'][$mod] = (!empty($_ln[$mod][$title])) ? $_ln[$mod][$title] : $_mods[$mod]['module_name'];
                }
                if ($fnc) {
                    // Custom module function for results
                    $_rt = $fnc($_post['search_string'], $pbrk, $page);
                }
                else {
                    $_rt = jrCore_db_search_items($mod, $_sc);
                }
                if (is_array($_rt) && is_array($_rt['_items'])) {
                    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$mod}_item_search.tpl")) {
                        $_fn['results'][$mod] = jrCore_parse_template("{$mod}_item_search.tpl", $_rt);
                    }
                    elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$mod}_item_list.tpl")) {
                        $_fn['results'][$mod] = jrCore_parse_template("{$mod}_item_list.tpl", $_rt);
                    }
                    elseif (is_file(APP_DIR . "/modules/{$mod}/templates/item_search.tpl")) {
                        $_fn['results'][$mod] = jrCore_parse_template('item_search.tpl', $_rt, $mod);
                    }
                    elseif (is_file(APP_DIR . "/modules/{$mod}/templates/item_list.tpl")) {
                        $_fn['results'][$mod] = jrCore_parse_template('item_list.tpl', $_rt, $mod);
                    }
                    $_fn['info'][$mod] = $_rt['info'];
                    $ttl += count($_rt['_items']);
                    $ltl = $_fn['titles'][$mod];
                }
            }
        }
        $_fn['search_string'] = jrCore_entity_string(strip_tags($_post['search_string']));
        $_fn['pagebreak']     = $pbrk;
        $_fn['page']          = $page;
        $_fn['modules']       = (isset($_post['_1']) && isset($_mods["{$_post['_1']}"])) ? $_post['_1'] : 'all';
        $_fn['module_count']  = count($_fn['results']);
        if ($_fn['module_count'] === 1) {
            $_fn['titles']['all'] = $ltl;
        }
        $out .= jrCore_parse_template('search_results.tpl', $_fn, 'jrSearch');

        // Save search details
        if (jrUser_is_logged_in()) {
            $_data = array(
                'search_string'  => $_post['search_string'],
                'search_module'  => (isset($_post['_1']) && isset($_mods["{$_post['_1']}"])) ? $_post['_1'] : 'all',
                'search_results' => $ttl
            );
            jrCore_db_create_item('jrSearch', $_data, null, false);
        }
    }
    $out .= jrCore_parse_template('footer.tpl');
    ini_set('session.cache_limiter', 'private');
    return $out;
}
