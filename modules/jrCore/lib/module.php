<?php
/**
 * Jamroom 5 System Core module
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
 * @package Module
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Get meta data for a module
 * @param string $module module string module name
 * @return mixed returns metadata/key if found, false if not
 */
function jrCore_module_meta_data($module)
{
    $func = "{$module}_meta";
    if (!function_exists($func)) {
        if (is_file(APP_DIR ."/modules/{$module}/include.php")) {
            require_once APP_DIR . "/modules/{$module}/include.php";
            if (!function_exists($func)) {
                return false;
            }
        }
        else {
            return false;
        }
    }
    $_tmp = $func();
    if (isset($_tmp) && is_array($_tmp)) {
        return $_tmp;
    }
    return false;
}

/**
 * Returns true if a module is installed and active
 * @param string $module module string module name
 * @return bool true if module is installed and active, else false
 */
function jrCore_module_is_active($module)
{
    global $_mods;
    return (isset($_mods[$module]) && $_mods[$module]['module_active'] == '1') ? true : false;
}

/**
 * Run a module function and return the results
 * @param string $func function to run
 * @return mixed returns function results or bool false on bad function
 */
function jrCore_run_module_function($func)
{
    if (function_exists($func)) {
        if (func_num_args() > 0) {
            return call_user_func_array($func,array_slice(func_get_args(),1));
        }
    }
    return false;
}

/**
 * Run a module view function and return the results
 * @param string $func function to run
 * @return mixed returns function results or bool false on bad function
 */
function jrCore_run_module_view_function($func)
{
    global $_post, $_user, $_conf;
    if (function_exists($func)) {
        ob_start();

        // Fire off trigger to let modules know we're about to run a view function
        $_args = array(
            'module' => $_post['module'],
            'view'   => (isset($_post['option'])) ? $_post['option'] : false
        );
        $_post = jrCore_trigger_event('jrCore','run_view_function',$_post,$_args);

        // Run module function and get output
        $out = $func($_post,$_user,$_conf);
        if (isset($out) && strlen($out) > 0) {
            ob_end_clean();
            return $out;
        }
        return ob_get_clean();
    }
    return false;
}

/**
 * Return URL name for a given module directory
 * @param string $module Module to get URL for
 * @return string
 */
function jrCore_get_module_url($module)
{
    global $_mods;
    if (isset($_mods[$module]) && isset($_mods[$module]['module_url'])) {
        return $_mods[$module]['module_url'];
    }
    // Check for non-active module
    $_mta = jrCore_module_meta_data($module);
    if ($_mta && isset($_mta['url'])) {
        return $_mta['url'];
    }
    return false;
}

/**
 * Check for and install new modules
 * @return bool Returns true
 */
function jrCore_install_new_modules()
{
    global $_mods, $_urls;
    // We need to also go through the file system and look for modules
    // that are not installed and set them up in inactive state
    $reload = false;
    if ($h = opendir(APP_DIR .'/modules')) {
        while (($file = readdir($h)) !== false) {
            if ($file == '.' || $file == '..' || strpos($file,'-release-')) {
                continue;
            }
            if (!isset($_mods[$file]) && is_dir(APP_DIR ."/modules/{$file}") && is_file(APP_DIR ."/modules/{$file}/include.php")) {
                jrCore_verify_module($file);
                $reload = true;
            }
        }
    }
    if ($reload) {
        $tbl = jrCore_db_table_name('jrCore','module');
        $req = "SELECT * FROM {$tbl} ORDER BY FIELD('module_directory','jrCore') ASC, module_priority ASC";
        $_rt = jrCore_db_query($req,'NUMERIC');
        if (isset($_rt) && is_array($_rt)) {
            // Include...
            foreach ($_rt as $_md) {
                $_mods["{$_md['module_directory']}"] = $_md;
                $_urls["{$_md['module_url']}"] = $_md['module_directory'];
            }
        }
        // Reset cache
        jrCore_delete_config_cache();
    }
    return true;
}

/**
 * Verify a module is installed properly
 * @param string $module Module to verify
 * @param string $version Version to verify
 * @return bool Returns true
 */
function jrCore_verify_module($module, $version = null)
{
    global $_mods;
    @ini_set('memory_limit', '512M');
    // Check for meta
    $func = "{$module}_meta";
    $file = APP_DIR ."/modules/{$module}/include.php";
    if (!is_null($version) && substr_count($version, '.') === 2) {
        if (is_file(APP_DIR . "/modules/{$module}-release-{$version}/include.php")) {
            $file = APP_DIR . "/modules/{$module}-release-{$version}/include.php";
        }
    }
    if (!function_exists($func)) {
        if (!is_file($file)) {
            jrCore_logger('CRI',"invalid module: {$module} - unable to read modules/{$module}/include.php");
            return false;
        }
        ob_start();
        require_once $file;
        ob_end_clean();
    }
    if (!function_exists($func)) {
        jrCore_logger('CRI',"invalid module: {$module} - required meta function does not exist");
        return false;
    }

    // Make sure our Core schema is updated first if not done yet
    require_once APP_DIR .'/modules/jrCore/schema.php';
    jrCore_db_schema();

    $_mod = $func();
    if (!is_null($version) && substr_count($version, '.') === 2) {
        $_mod['version'] = $version;
    }

    // Check for init
    $func = "{$module}_init";
    if (!function_exists($func)) {
        jrCore_logger('CRI',"invalid module: {$module} - required init function does not exist");
    }
    if ($module != 'jrCore' && !isset($_mods[$module]['module_initialized'])) {
        $func();
        $_mods[$module]['module_initialized'] = 1;
    }

    // Setup module in module table
    $pri = (isset($_mod['priority'])) ? intval($_mod['priority']) : 100;
    $mod = jrCore_db_escape($module);
    $ver = jrCore_db_escape($_mod['version']);
    $dev = jrCore_db_escape(substr($_mod['developer'], 0, 128));
    $nam = jrCore_db_escape(substr($_mod['name'], 0, 256));
    $dsc = jrCore_db_escape(substr($_mod['description'], 0, 1024));
    $cat = (isset($_mod['category']) && jrCore_checktype($_mod['category'], 'printable')) ? jrCore_db_escape(substr($_mod['category'], 0, 64)) : 'tools';
    $url = (isset($_mod['url']) && jrCore_checktype($_mod['url'],'core_string')) ? jrCore_db_escape($_mod['url']) : jrCore_url_string($module);
    $mrq = (isset($_mod['requires']{0})) ? jrCore_db_escape($_mod['requires']) : '';
    $pfx = jrCore_db_get_prefix($module);
    $tbl = jrCore_db_table_name('jrCore','module');

    // We need to see if we are installing a module that is defining a module
    // url that is already being used - if so, we must set the priority lower
    $req = "SELECT module_priority FROM {$tbl} WHERE module_url = '{$url}' AND module_directory != '{$mod}'";
    $_rt = jrCore_db_query($req,'SINGLE');
    if (isset($_rt) && is_array($_rt)) {
        $pri = 255;
    }
    // Setup this module in our module table
    $mex = true;
    $req = "SELECT module_id FROM {$tbl} WHERE module_directory = '{$mod}'";
    $_rt = jrCore_db_query($req,'SINGLE');
    if (isset($_rt) && is_array($_rt)) {
        // Make sure our module entry is updated with the latest meta data
        $req = "UPDATE {$tbl} SET
                  module_updated     = UNIX_TIMESTAMP(),
                  module_priority    = '{$pri}',
                  module_version     = '{$ver}',
                  module_name        = '{$nam}',
                  module_prefix      = '{$pfx}',
                  module_description = '{$dsc}',
                  module_developer   = '{$dev}',
                  module_requires    = '{$mrq}'
                 WHERE module_directory = '{$mod}'";
        $cnt = jrCore_db_query($req,'COUNT');
        if (!isset($cnt) || $cnt !== 1) {
            jrCore_logger('CRI',"error updating module: {$module} - check PHP error log");
        }
    }
    else {

        // New Module Install - not in module table
        $mex = false;

        // See if our module is defining it's priority
        if (isset($_mod['priority']) && jrCore_checktype($_mod['priority'],'number_nz') && $_mod['priority'] < 256 && $_mod['priority'] > 0) {
            $pri = $_mod['priority'];
        }

        // See if our module is a "locked" module (cannot be deleted, usually only for Core modules)
        $lck = 0;
        if (isset($_mod['locked']) && $_mod['locked'] === true) {
            $lck = 1;
        }

        // See if our module is an "active" module - i.e. installs directly into an active state
        // NOTE: by default modules are inactive, and SHOULD be - this flag should only used by the
        // Core modules to know what modules to make active on install!
        $act = 0;
        if (isset($_mod['activate']) && $_mod['activate'] === true) {
            $act = 1;
        }

        // Make sure our jrCore module gets highest load priority (0)
        if (isset($module) && $module == 'jrCore') {
            $act = 1;
            $pri = 0; // highest load priority
            $lck = 1;
        }

        $req = "INSERT INTO {$tbl} (module_created,module_updated,module_priority,module_directory,module_url,module_version,module_name,module_prefix,module_description,module_category,module_developer,module_active,module_locked,module_requires)
                VALUES (UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'{$pri}','{$mod}','{$url}','{$ver}','{$nam}','{$pfx}','{$dsc}','{$cat}','{$dev}','{$act}','{$lck}','{$mrq}')";
        $mid = jrCore_db_query($req,'INSERT_ID');
        if (!isset($mid) || $mid < 1) {
            jrCore_logger('CRI',"error creating new module: {$module} - check PHP error log");
        }
    }

    // schema
    if (is_file(APP_DIR ."/modules/{$module}/schema.php")) {
        require_once APP_DIR ."/modules/{$module}/schema.php";
        $func = "{$module}_db_schema";
        if (function_exists($func)) {
            $func();
        }
    }

    // config
    if (is_file(APP_DIR ."/modules/{$module}/config.php")) {
        require_once APP_DIR ."/modules/{$module}/config.php";
        $func = "{$module}_config";
        if (function_exists($func)) {
            $func();
        }
    }

    // quota
    $_quota = jrCore_get_registered_module_features('jrCore','quota_support');
    if (isset($_quota[$module]) || is_file(APP_DIR ."/modules/{$module}/quota.php")) {
        if (is_file(APP_DIR ."/modules/{$module}/quota.php")) {
            require_once APP_DIR ."/modules/{$module}/quota.php";
            $func = "{$module}_quota_config";
            if (function_exists($func)) {
                $func();
            }
        }

        // Ok this module is setup for Quota options - make sure the
        // "allowed" setting is setup if it is supported
        $_pnd = jrCore_get_registered_module_features('jrCore','quota_support');
        if ($_pnd && isset($_pnd[$module])) {
            // Access
            $_tmp = array(
                'name'     => 'allowed',
                'label'    => 'allowed on profile',
                'default'  => (jrCore_checktype($_pnd[$module],'onoff')) ? $_pnd[$module] : 'off',
                'type'     => 'checkbox',
                'required' => 'on',
                'help'     => 'Should this Quota be allowed access to this module?',
                'validate' => 'onoff',
                'section'  => 'permissions',
                'order'    => 1
            );
            jrProfile_register_quota_setting($module,$_tmp);
        }

        // Max Items
        $_pnd = jrCore_get_registered_module_features('jrCore','max_item_support');
        if ($_pnd && isset($_pnd[$module])) {
            $_tmp = array(
                'name'     => 'max_items',
                'label'    => 'max items',
                'default'  => '0',
                'type'     => 'text',
                'required' => 'on',
                'help'     => 'How many items of this type can a profile create in this Quota?  Set to zero (0) to allow an unlimited number of items to be created.',
                'validate' => 'number_nn',
                'section'  => 'permissions',
                'order'    => 2
            );
            jrProfile_register_quota_setting($module,$_tmp);
        }

        // Item Display Order
        $_pnd = jrCore_get_registered_module_features('jrCore','item_order_support');
        if ($_pnd && isset($_pnd[$module])) {
            // This module is registered for item_order_support - this means ALL DS entries
            // for this module need to have a "display_order" field that is used to do the
            // actual sorting.   Make sure each entry has that here.
            $pfx = jrCore_db_get_prefix($module);
            $_mk = jrCore_db_get_items_missing_key($module,"{$pfx}_display_order");
            if (isset($_mk) && is_array($_mk)) {
                $_ids = array();
                foreach ($_mk as $id) {
                    $_ids[$id] = 1000;
                }
                jrCore_db_set_display_order($module, $_ids);
                unset($_ids, $_mk);
            }
        }

        // Pending Support
        $_pnd = jrCore_get_registered_module_features('jrCore','pending_support');
        if ($_pnd && isset($_pnd[$module])) {
            $_opt = array(
                '0' => 'No Approval Needed',
                '1' => 'Approval on Create',
                '2' => 'Approval on Create and Update'
            );
            $_tmp = array(
                'name'     => 'pending',
                'label'    => 'Item Approval',
                'default'  => 0,
                'type'     => 'select',
                'options'  => $_opt,
                'required' => 'on',
                'help'     => 'When a new Item is created by a Profile in this Quota, should the item be placed in a pending state before being visible in the system?<br><br><b>No Approval Needed:</b> item is immediately visible.<br><b>Approval on Create:</b> item will need to be approved by an admin after being created.<br><b>Approval on Create and Update:</b> item will need to be approved by an admin after being created or updated.',
                'validate' => 'number_nz',
                'section'  => 'permissions',
                'order'    => 3
            );
            jrProfile_register_quota_setting($module,$_tmp);
        }

        // See if we have other modules registering settings for this module's Quota settings
        foreach ($_mods as $qmod => $_inf) {
            if (is_file(APP_DIR ."/modules/{$qmod}/quota.php")) {
                $func = "{$qmod}_global_quota_config";
                if (!function_exists($func)) {
                    require_once APP_DIR ."/modules/{$qmod}/quota.php";
                }
                if (function_exists($func)) {
                    $func();
                }
            }
        }
        // See if we got anything...
        $_tmp = jrCore_get_flag('jrprofile_register_global_quota_setting');
        if ($_tmp) {
            foreach ($_tmp as $_fields) {
                foreach ($_fields as $k => $_fld) {
                    $_fld['order'] = ($k + 3);
                    jrProfile_register_quota_setting($module,$_fld);
                }
            }
        }
    }

    // lang strings
    if (is_dir(APP_DIR ."/modules/{$module}/lang")) {
        jrUser_install_lang_strings('module',$module);
    }

    // run custom installer if we are in our installer
    if (!$mex || defined('IN_JAMROOM_INSTALLER') && IN_JAMROOM_INSTALLER === 1) {

        // If this module has a registered Text Formatter, let's turn it on by default
        $_sf = jrCore_get_registered_module_features('jrCore', 'format_string');
        if (isset($_sf[$module]) && $module != 'jrCore') {
            $_qt = jrProfile_get_quotas();
            // This module provides at least one text formatter
            foreach ($_sf[$module] as $fnc => $_desc) {
                // Activate this formatter
                foreach ($_qt as $qid => $_qs) {
                    $_qi = jrProfile_get_quota($qid, false);
                    if (isset($_qi['quota_jrCore_active_formatters']) && !strpos(' '. $_qi['quota_jrCore_active_formatters'], $fnc)) {
                        // add it in
                        $val = trim($_qi['quota_jrCore_active_formatters']) . ",{$fnc}";
                        jrProfile_set_quota_value('jrCore', $qid, 'active_formatters', $val);
                    }
                }
            }
        }

        // See if this module has a custom installer
        if (is_file(APP_DIR ."/modules/{$module}/install.php")) {
            require_once APP_DIR ."/modules/{$module}/install.php";
            $func = "{$module}_install";
            if (function_exists($func)) {
                $func();
            }
        }
    }
    // Not doing an install - verify
    elseif (is_file(APP_DIR ."/modules/{$module}/verify.php")) {
        require_once APP_DIR ."/modules/{$module}/verify.php";
        $func = "{$module}_verify";
        if (function_exists($func)) {
            $func();
        }
    }

   // check for exported form fields in the custom_form_fields.json file.
    if (is_file(APP_DIR . "/modules/{$module}/custom_form_fields.json")) {
        $_txt = file_get_contents(APP_DIR . "/modules/{$module}/custom_form_fields.json");
        if (isset($_txt) && jrCore_checktype($_txt, 'json')) {
            $_fields = json_decode($_txt, true);
            // add Form Designer fields to db
            if (isset($_fields) && is_array($_fields)) {
                $tbl = jrCore_db_table_name('jrCore', 'form');
                foreach ($_fields as $view => $_row) {
                    if (isset($_row) && is_array($_row)) {
                        foreach ($_row as $_col) {
                            // update create
                            $req1 = "SELECT * FROM {$tbl} WHERE `module` = '{$module}' AND `view` = '{$view}' AND `name` = '{$_col['name']}'";
                            $_rt = jrCore_db_query($req1, 'SINGLE');
                            if (!is_array($_rt)) {
                                // insert
                                $fields = false;
                                $values = false;
                                foreach ($_col as $k => $v) {
                                    $fields[] = "`" . jrCore_db_escape($k) . "`";
                                    $values[] = "'" . jrCore_db_escape($v) . "'";
                                }
                                $fields = implode(",", $fields);
                                $values = implode(",", $values);
                                if (isset($fields) && isset($values)) {
                                    $req = "INSERT INTO {$tbl} ({$fields}) VALUES ({$values})";
                                    jrCore_db_query($req);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Fire off our verify_module trigger for anything the module might want to do
    jrCore_trigger_event('jrCore', 'verify_module', array(), array() , $module);

    return true;
}

/**
 * Get unique Cache directory for a module
 * @param string $module Module to get Cache Directory for
 * @return string Returns a unique, persisted cache directory for a module
 */
function jrCore_get_module_cache_dir($module)
{
    global $_conf;
    $cdir = APP_DIR ."/data/cache/{$module}";
    if (!is_dir($cdir)) {
        @mkdir($cdir,$_conf['jrCore_dir_perms']) or jrCore_logger('CRI',"jrCore_get_module_cache_dir: unable to create cache directory: {$module}");
        @chmod($cdir,$_conf['jrCore_dir_perms']);
    }
    return $cdir;
}

/**
 * Get unique Support directory for a module
 * @param string $module Module to get Cache Directory for
 * @return string Returns a unique, persisted cache directory for a module
 */
function jrCore_get_module_support_dir($module)
{
    global $_conf;
    $cdir = APP_DIR ."/data/support/{$module}";
    if (!is_dir($cdir)) {
        @mkdir($cdir,$_conf['jrCore_dir_perms']) or jrCore_logger('CRI',"jrCore_get_module_support_dir: unable to create cache directory: {$module}");
        @chmod($cdir,$_conf['jrCore_dir_perms']);
    }
    return $cdir;
}

/**
 * @ignore
 * @param string $module Module
 * @param string $index Unique Registered module index
 * @param string $_options Javascript file
 * @return array
 */
function jrCore_enable_external_css($module, $index, $_options)
{
    if (jrCore_is_view_request()) {
        if (strpos($index, 'http') === 0 || strpos($index, '//') === 0) {
            $xtra = '';
            if (is_array($_options) && count($_options) > 0) {
                $xtra = '"';
                foreach ($_options as $opt) {
                    $xtra .= ' ' . $opt;
                }
            }
            $_tmp = array('source' => "{$index}{$xtra}");
            jrCore_create_page_element('css_href', $_tmp);
        }
    }
    return $_options;
}

/**
 * @ignore
 * @param string $module Module
 * @param string $index Unique Registered module index
 * @param string $_options Javascript file
 * @return array
 */
function jrCore_enable_external_javascript($module, $index, $_options)
{
    if (jrCore_is_view_request()) {
        if (strpos($index, 'http') === 0 || strpos($index, '//') === 0) {
            $xtra = '';
            if (is_array($_options) && count($_options) > 0) {
                $xtra = '"';
                foreach ($_options as $opt) {
                    $xtra .= ' ' . $opt;
                }
            }
            $_tmp = array('source' => "{$index}{$xtra}");
            jrCore_create_page_element('javascript_footer_href', $_tmp);
        }
    }
    return $_options;
}
