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
 * @package DataStore
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

// Constants
define('SKIP_TRIGGERS', true);
define('NO_CACHE', true);

/**
 * An array of modules that have a datastore enabled
 */
function jrCore_get_datastore_modules()
{
    global $_mods;
    $_out = array();
    foreach ($_mods as $module => $_inf) {
        if (isset($_inf['module_prefix']) && strlen($_inf['module_prefix']) > 0) {
            $_out[$module] = $_inf['module_prefix'];
        }
    }
    return $_out;
}

/**
 * Returns DataStore Prefix for a module
 * @param string $module Module to return prefix for
 * @return mixed
 */
function jrCore_db_get_prefix($module)
{
    global $_mods;
    if (isset($_mods[$module]['module_prefix']) && strlen($_mods[$module]['module_prefix']) > 0) {
        return $_mods[$module]['module_prefix'];
    }
    elseif ($_tmp = jrCore_get_flag('jrcore_db_create_datastore_prefixes')) {
        if (isset($_tmp[$module])) {
            return $_tmp[$module];
        }
    }
    return false;
}

/**
 * Creates a new module DataStore
 * @param string $module Module to create DataStore for
 * @param string $prefix Key Prefix in DataStore
 * @return bool
 */
function jrCore_db_create_datastore($module, $prefix)
{
    if (!isset($prefix) || strlen($prefix) === 0) {
        jrCore_logger('CRI', "Invalid datastore module_prefix for module: {$module}");
        return false;
    }
    // Items
    $_tmp = array(
        "`_item_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY"
    );
    jrCore_db_verify_table($module, 'item', $_tmp, 'MyISAM');

    // Item
    $_tmp = array(
        "`_item_id` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "`key` VARCHAR(128) NOT NULL DEFAULT ''",
        "`index` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'",
        "`value` VARCHAR(512) NOT NULL DEFAULT ''",
        "PRIMARY KEY (`key`,`_item_id`,`index`)",
        "INDEX `_item_id` (`_item_id`)",
        "INDEX `index` (`index`)",
        "INDEX `value` (`value`(64))",
    );
    jrCore_db_verify_table($module, 'item_key', $_tmp, 'InnoDB');

    // Make sure our DataStore prefix is stored with the module info
    $efx = jrCore_db_get_prefix($module);
    if (!$efx || $efx != $prefix || strlen($efx) === 0) {
        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "UPDATE {$tbl} SET
                  module_prefix = '" . jrCore_db_escape($prefix) . "'
                 WHERE module_directory = '" . jrCore_db_escape($module) . "'
                 LIMIT 1";
        jrCore_db_query($req, 'COUNT');
    }

    // Lastly, if this DS is being created in a jrCore_verify_module, and the
    // module has an install.php script, the prefix won't be available in $_mods
    // until cache is reset and the page reloaded, so put it in a tmp place
    if ($_tmp = jrCore_get_flag('jrcore_db_create_datastore_prefixes')) {
        $_tmp[$module] = $prefix;
    }
    else {
        $_tmp = array( $module => $prefix );
    }
    jrCore_set_flag('jrcore_db_create_datastore_prefixes', $_tmp);

    // Let modules know we are creating/validating a DataStore
    $_args = array(
        'module' => $module,
        'prefix' => $prefix
    );
    $_data = array();
    jrCore_trigger_event('jrCore', 'db_create_datastore', $_data, $_args);

    return true;
}

/**
 * Truncate and reset a module DataStore
 * @param $module string Module DataStore to truncate
 * @return bool
 */
function jrCore_db_truncate_datastore($module)
{
    $tbl = jrCore_db_table_name($module, 'item');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "TRUNCATE TABLE {$tbl}";
    jrCore_db_query($req);
    return true;
}

/**
 * Get number of items in a module DataStore
 * @param $module string Module to get number of items for
 * @return int
 */
function jrCore_db_get_datastore_item_count($module)
{
    return jrCore_db_number_rows($module, 'item');
}

/**
 * Run a key "function" in matching values
 * @param $module string Module DataStore
 * @param $key string Key to match
 * @param $match string Value to match - '*' for all
 * @param $function string function to run on key values (sum, avg, min, max, std, count)
 * @return bool
 */
function jrCore_db_run_key_function($module, $key, $match, $function)
{
    switch (strtolower($function)) {
        case 'sum':
        case 'avg':
        case 'min':
        case 'max':
        case 'std':
            $fnc = strtoupper($function) . '(`value`)';
            break;
        case 'count':
            $fnc = 'COUNT(`_item_id`)';
            break;
        default:
            return false;
            break;
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    if ($match == '*') {
        $req = "SELECT {$fnc} AS tc FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "'";
    }
    elseif (strpos($match, '%')) {
        $req = "SELECT {$fnc} AS tc FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` LIKE '" . jrCore_db_escape($match) ."'";
    }
    else {
        $req = "SELECT {$fnc} AS tc FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` = '" . jrCore_db_escape($match) ."'";
    }
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return $_rt['tc'];
    }
    return false;
}

/**
 * Set the special "display_order" keys for items in a DataStore
 * @param $module string Module DataStore to set values in
 * @param $_ids array Array of id => value entries
 * @return bool
 */
function jrCore_db_set_display_order($module, $_ids)
{
    $pfx = jrCore_db_get_prefix($module);
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_ids as $iid => $ord) {
        $ord = (int) $ord;
        $iid = (int) $iid;
        $req .= "('{$iid}','{$pfx}_display_order',0,'{$ord}'),";
    }
    $req = substr($req,0,strlen($req) - 1) ." ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    jrCore_db_query($req);
    return true;
}

/**
 * Create a new key for all entries in a DataStore and set it to a default value
 * @param $module string Module DataStore to create new key in
 * @param $key string Key to create
 * @param $value mixed initial value
 * @return bool
 */
function jrCore_db_create_default_key($module, $key, $value)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $key = jrCore_db_escape($key);
    $val = jrCore_db_escape($value);
    $req = "INSERT IGNORE INTO {$tbl} (`_item_id`,`key`,`value`) SELECT DISTINCT(`_item_id`),'{$key}','{$val}' FROM {$tbl} WHERE `_item_id` > 0";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Create a new key for all entries in a DataStore and set it to a default value
 * @param $module string Module DataStore to create new key in
 * @param $key string Key to create
 * @param $value mixed value to set keys to
 * @param $default mixed if a value is set set to $default, it will be changed to $value
 * @return bool
 */
function jrCore_db_update_default_key($module, $key, $value, $default)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $key = jrCore_db_escape($key);
    $val = jrCore_db_escape($value);
    $def = jrCore_db_escape($default);
    $req = "UPDATE {$tbl} SET `value` = '{$val}' WHERE `key` = '{$key}' AND (`value` IS NULL OR `value` = '' OR `value` = '{$def}')";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Increment a DataStore key for an Item ID or Array of Item IDs by a given value
 * @param $module string Module Name
 * @param $id mixed Unique Item ID OR Array of Item IDs
 * @param $key string Key to increment
 * @param $value number Integer/Float to increment by
 * @return bool
 */
function jrCore_db_increment_key($module, $id, $key, $value)
{
    if (!is_numeric($value)) {
        return false;
    }
    if (!is_array($id)) {
        $id = array(intval($id));
    }
    else {
        foreach ($id as $k => $iid) {
            $id[$k] = (int) $iid;
        }
    }
    $_in = array();
    foreach ($id as $uid) {
        $_in[] = "('{$uid}','" . jrCore_db_escape($key) . "',0,'{$value}')";
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES " . implode(',', $_in) . " ON DUPLICATE KEY UPDATE `value` = (`value` + {$value})";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt < 1) {
        return false;
    }
    // We need to reset the caches for these items
    $_ch = array();
    foreach ($id as $uid) {
        $_ch[] = array($module, "{$module}-{$uid}-0", false);
        $_ch[] = array($module, "{$module}-{$uid}-1", false);
    }
    jrCore_delete_multiple_cache_entries($_ch);
    return true;
}

/**
 * Decrement a DataStore key for an Item ID or Array of Item IDs by a given value
 * @param $module string Module Name
 * @param $id mixed Unique Item ID OR Array of Item IDs
 * @param $key string Key to decrement
 * @param $value number Integer/Float to decrement by
 * @param $min_value number Lowest Value allowed for Key (default 0)
 * @return bool
 */
function jrCore_db_decrement_key($module, $id, $key, $value, $min_value = null)
{
    if (!is_numeric($value)) {
        return false;
    }
    if (is_null($min_value) || !is_numeric($min_value)) {
        $min_value = 0;
    }
    if (!is_array($id)) {
        $id = array(intval($id));
    }
    else {
        foreach ($id as $k => $iid) {
            $id[$k] = (int) $iid;
        }
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    $val = ($min_value + $value);
    $req = "UPDATE {$tbl} SET `value` = (`value` - {$value}) WHERE `_item_id` IN(" . implode(',', $id) . ") AND `key` = '" . jrCore_db_escape($key) . "' AND `value` >= {$val}";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt < 1) {
        return false;
    }
    // We need to reset the caches for these items
    $_ch = array();
    foreach ($id as $uid) {
        $_ch[] = array($module, "{$module}-{$uid}-0", false);
        $_ch[] = array($module, "{$module}-{$uid}-1", false);
    }
    jrCore_delete_multiple_cache_entries($_ch);
    return true;
}

/**
 * Return an array of _item_id's that do NOT have a specified key set
 * @param $module string Module DataStore to search through
 * @param $key string Key Name that should not be set
 * @return array|bool
 */
function jrCore_db_get_items_missing_key($module, $key)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "') GROUP BY `_item_id`";
    $_rt = jrCore_db_query($req, '_item_id');
    if ($_rt && is_array($_rt)) {
        return array_keys($_rt);
    }
    return false;
}

/**
 * Deletes multiple keys from an item
 * @param string $module Module the DataStore belongs to
 * @param int $id Item ID
 * @param array $_keys Keys to delete
 * @param bool $core_check by default you cannot delete keys that begin with _
 * @param bool $cache_reset by default cache is reset
 * @return bool
 */
function jrCore_db_delete_multiple_item_keys($module, $id, $_keys, $core_check = true, $cache_reset = true)
{
    if (!is_array($_keys)) {
        return false;
    }
    foreach ($_keys as $k => $key) {
        // Some things we cannot remove
        if ($core_check && strpos($key, '_') === 0) {
            // internally used - cannot remove
            unset($_keys[$k]);
            continue;
        }
        $_keys[$k] = jrCore_db_escape($key);
    }
    // Delete keys
    if (count($_keys) > 0) {
        $uid   = intval($id);
        $_args = array( 'module' => $module, '_item_id' => $uid );
        $_keys = jrCore_trigger_event('jrCore', 'db_delete_keys', $_keys, $_args);
        if (count($_keys) > 0) {
            $tbl = jrCore_db_table_name($module, 'item_key');
            $req = "DELETE FROM {$tbl} WHERE `_item_id` = '{$uid}' AND `key` IN('". implode("','", $_keys) ."')";
            $cnt = jrCore_db_query($req, 'COUNT');
            if ($cnt && $cnt > 0) {
                // We need to reset the cache for this item
                if ($cache_reset) {
                    $_ch = array(
                        array($module, "{$module}-{$id}-0", false),
                        array($module, "{$module}-{$id}-1", false)
                    );
                    jrCore_delete_multiple_cache_entries($_ch);
                }
                return true;
            }
        }
    }
    return false;
}

/**
 * Deletes a single key from an item
 * @param string $module Module the DataStore belongs to
 * @param int $id Item ID
 * @param string $key Key to delete
 * @param bool $core_check by default you cannot delete keys that begin with _
 * @param bool $cache_reset by default cache is reset
 * @return mixed INSERT_ID on success, false on error
 */
function jrCore_db_delete_item_key($module, $id, $key, $core_check = true, $cache_reset = true)
{
    return jrCore_db_delete_multiple_item_keys($module, $id, array($key), $core_check, $cache_reset);
}

/**
 * Delete DataStore Key(s) from Multiple Items
 * @param string $module Module the DataStore belongs to
 * @param array $_ids IDs of items to delete keys from
 * @param mixed $key key name or array of key names
 * @return bool
 */
function jrCore_db_delete_key_from_multiple_items($module, $_ids, $key)
{
    $_cch = array();
    foreach ($_ids as $k => $id) {
        $_ids[$k] = (int) $id;
        $_cch[] = array($module, "{$module}-{$_ids[$k]}-0", false);
        $_cch[] = array($module, "{$module}-{$_ids[$k]}-1", false);
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    if (is_array($key)) {
        $_ky = array();
        foreach ($key as $k) {
            $_ky[] = jrCore_db_escape($k);
        }
        $req = "DELETE FROM {$tbl} WHERE `key` IN('" . implode("','", $_ky) . "') AND `_item_id` IN(" . implode(',', $_ids) . ")";
    }
    else {
        $req = "DELETE FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `_item_id` IN(" . implode(',', $_ids) . ")";
    }
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        jrCore_delete_multiple_cache_entries($_cch);
    }
    return true;
}

/**
 * Validates DataStore key names are allowed and correct
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs to check
 * @return mixed true on success, exits on error
 */
function jrCore_db_get_allowed_item_keys($module, $_data)
{
    if (!$_data || !is_array($_data)) {
        return false;
    }
    $pfx = jrCore_db_get_prefix($module);
    $_rt = array();
    foreach ($_data as $k => $v) {
        if (strpos($k, '_') === 0) {
            jrCore_notice_page('CRI', "invalid key name: {$k} - key names cannot start with an underscore");
        }
        elseif (strpos($k, $pfx) !== 0) {
            jrCore_notice_page('CRI', "invalid key name: {$k} - key name must begin with module prefix: {$pfx}_");
        }
        $_rt[$k] = $v;
    }
    return $_rt;
}

/**
 * Creates a new item in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @param bool $profile_count If set to true, profile_count will be incremented for given _profile_id
 * @param bool $skip_trigger bool Set to TRUE to skip sending out create_item trigger
 * @return mixed INSERT_ID on success, false on error
 */
function jrCore_db_create_item($module, $_data, $_core = null, $profile_count = true, $skip_trigger = false)
{
    global $_user;

    // See if we are limiting the number of items that can be created by a profile in this quota
    if (isset($_user["quota_{$module}_max_items"]) && $_user["quota_{$module}_max_items"] > 0 && isset($_user["profile_{$module}_item_count"]) && $_user["profile_{$module}_item_count"] >= $_user["quota_{$module}_max_items"]) {
        // We've hit the limit for this quota
        return false;
    }

    // Validate incoming data
    $_data = jrCore_db_get_allowed_item_keys($module, $_data);

    // Check for additional core fields being added in
    if ($_core && is_array($_core)) {
        foreach ($_core as $k => $v) {
            if (strpos($k, '_') === 0) {
                $_data[$k] = $_core[$k];
            }
        }
        unset($_core);
    }

    // Internal defaults
    $now = time();
    $_check = array(
        '_created'    => $now,
        '_updated'    => $now,
        '_profile_id' => 0,
        '_user_id'    => 0
    );
    // If user is logged in, defaults to their account
    if (jrUser_is_logged_in()) {
        $_check['_profile_id'] = (int) $_user['user_active_profile_id'];
        $_check['_user_id']    = (int) $_user['_user_id'];
    }
    foreach ($_check as $k => $v) {
        // Any of our _check values can be removed by setting it to false
        if (isset($_data[$k]) && $_data[$k] === false) {
            unset($_data[$k]);
        }
        elseif (!isset($_data[$k])) {
            $_data[$k] = $_check[$k];
        }
    }

    // Get our unique item id
    $tbl = jrCore_db_table_name($module, 'item');
    $req = "INSERT INTO {$tbl} (`_item_id`) VALUES (0)";
    $iid = jrCore_db_query($req, 'INSERT_ID');
    if ($iid && $iid > 0) {

        // Our module prefix
        $pfx = jrCore_db_get_prefix($module);

        // Check for Pending Support for this module
        // NOTE: Items created by master/admin users bypass pending
        $pnd = false;
        $_pn = jrCore_get_registered_module_features('jrCore', 'pending_support');
        if ($_pn && isset($_pn[$module])) {
            $_data["{$pfx}_pending"] = '0';
            if (!jrUser_is_admin()) {
                // Pending support is on for this module - check quota
                // 0 = immediately active
                // 1 = review needed on CREATE
                // 2 = review needed on CREATE and UPDATE
                if (isset($_user["quota_{$module}_pending"]) && intval($_user["quota_{$module}_pending"]) > 0) {
                    $_data["{$pfx}_pending"] = '1';
                    $pnd = true;
                    jrCore_set_flag("jrcore_created_pending_item_{$iid}", 1);
                }
            }
        }

        // Check for item_order_support
        $_pn = jrCore_get_registered_module_features('jrCore', 'item_order_support');
        if ($_pn && isset($_pn[$module]) && !isset($_data["{$pfx}_display_order"])) {
            // New entries at top
            $_data["{$pfx}_display_order"] = 0;
        }

        // Trigger create event
        if (!$skip_trigger) {
            $_args = array(
                '_item_id' => $iid,
                'module'   => $module
            );
            $_data = jrCore_trigger_event('jrCore', 'db_create_item', $_data, $_args);
        }

        // Listeners could set this item to pending
        if (isset($_data["{$pfx}_pending"]) && $_data["{$pfx}_pending"] == '1') {
            $pnd = true;
            jrCore_set_flag("jrcore_created_pending_item_{$iid}", 1);
        }

        // Check for actions that are linking to pending items
        $lid = 0;
        $lmd = '';
        if (isset($_data['action_pending_linked_item_id']) && jrCore_checktype($_data['action_pending_linked_item_id'], 'number_nz')) {
            $lid = (int) $_data['action_pending_linked_item_id'];
            $lmd = jrCore_db_escape($_data['action_pending_linked_item_module']);
            unset($_data['action_pending_linked_item_id']);
            unset($_data['action_pending_linked_item_module']);
        }

        $tbl = jrCore_db_table_name($module, 'item_key');
        $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
        foreach ($_data as $k => $v) {
            // If our value is longer than 508 bytes we split it up
            $len = strlen($v);
            if ($len > 508) {
                $_tm = array();
                while ($len) {
                    $_tm[] = mb_strcut($v, 0, 508, "UTF-8");
                    $v     = mb_strcut($v, 508, $len, "UTF-8");
                    $len   = strlen($v);
                }
                foreach ($_tm as $idx => $part) {
                    $req .= "('{$iid}','" . jrCore_db_escape($k) . "','" . ($idx + 1) . "','" . jrCore_db_escape($part) . "'),";
                }
            }
            elseif ($v === 'UNIX_TIMESTAMP()') {
                $req .= "('{$iid}','" . jrCore_db_escape($k) . "','0',UNIX_TIMESTAMP()),";
            }
            else {
                $req .= "('{$iid}','" . jrCore_db_escape($k) . "','0','" . jrCore_db_escape($v) . "'),";
            }
        }
        $req = substr($req, 0, strlen($req) - 1);
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {
            // Increment profile counts for this item
            if ($profile_count) {
                switch ($module) {
                    // Some modules we do not store counts for
                    case 'jrProfile':
                    case 'jrUser':
                        break;
                    default:

                        $pid = $_data['_profile_id'];
                        if (isset($profile_count) && jrCore_checktype($profile_count, 'number_nz')) {
                            $pid = (int) $profile_count;
                        }

                        // Update PROFILE counts for module items
                        $ptb = jrCore_db_table_name('jrProfile', 'item_key');
                        $req = "UPDATE {$ptb} SET `value` = (SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_profile_id' AND `value` = '{$pid}') WHERE `key` = 'profile_{$module}_item_count' AND `_item_id` = '{$pid}' LIMIT 1";
                        $cnt = jrCore_db_query($req, 'COUNT');
                        if (!isset($cnt) || $cnt === 0) {
                            // The first entry for a new module item
                            $req = "INSERT INTO {$ptb} (`_item_id`,`key`,`index`,`value`) VALUES ({$pid},'profile_{$module}_item_count',0,1) ON DUPLICATE KEY UPDATE `value` = (`value` + 1)";
                            jrCore_db_query($req);
                        }

                        // Update USER counts for module items (i.e. user has created X songs, X comments)
                        if (is_numeric($_data['_user_id'])) {
                            $ptb = jrCore_db_table_name('jrUser', 'item_key');
                            $req = "UPDATE {$ptb} SET `value` = (SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_user_id' AND `value` = '{$_data['_user_id']}') WHERE `key` = 'user_{$module}_item_count' AND `_item_id` = '{$_data['_user_id']}' LIMIT 1";
                            $cnt = jrCore_db_query($req, 'COUNT');
                            if (!isset($cnt) || $cnt === 0) {
                                // The first entry for a new user module item counter
                                $req = "INSERT INTO {$ptb} (`_item_id`,`key`,`index`,`value`) VALUES ('{$_data['_user_id']}','user_{$module}_item_count',0,1) ON DUPLICATE KEY UPDATE `value` = (`value` + 1)";
                                jrCore_db_query($req);
                            }
                        }

                        break;
                }
            }
            if ($pnd) {
                // Add pending entry to Pending table...
                $_pd = array(
                    'module' => $module,
                    'item'   => $_data,
                    'user'   => $_user
                );
                $dat = jrCore_db_escape(json_encode($_pd));
                $pnd = jrCore_db_table_name('jrCore', 'pending');
                $req = "INSERT INTO {$pnd} (pending_created,pending_module,pending_item_id,pending_linked_item_module,pending_linked_item_id,pending_data)
                        VALUES (UNIX_TIMESTAMP(),'" . jrCore_db_escape($module) . "','{$iid}','{$lmd}','{$lid}','{$dat}')
                        ON DUPLICATE KEY UPDATE pending_created = UNIX_TIMESTAMP()";
                jrCore_db_query($req, 'INSERT_ID');
                unset($_pd);

                // Notify admins of new pending item
                $_sp = array(
                    'search'        => array(
                        "user_group IN master,admin",
                    ),
                    'limit'         => 100,
                    'return_keys'   => array('_user_id', 'user_name'),
                    'skip_triggers' => true,
                    'pending_check' => false,
                    'privacy_check' => false
                );
                $_rt = jrCore_db_search_items('jrUser', $_sp);
                if (isset($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
                    list($sub, $msg) = jrCore_parse_email_templates('jrCore', 'pending_item', $_data);
                    foreach ($_rt['_items'] as $_v) {
                        jrUser_notify($_v['_user_id'], 0, 'jrCore', 'pending_item', $sub, $msg);
                    }
                }
            }
            return $iid;
        }
    }
    return false;
}

/**
 * Create multiple items in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @param bool $skip_trigger bool Set to TRUE to skip sending out create_item trigger
 * @return mixed array of INSERT_ID's on success, false on error
 */
function jrCore_db_create_multiple_items($module, $_data, $_core = null, $skip_trigger = false)
{
    global $_user;

    // Validate incoming data
    foreach ($_data as $k => $_dt) {
        if (!is_array($_dt)) {
            // bad data
            return false;
        }
        $_data[$k] = jrCore_db_get_allowed_item_keys($module, $_dt);
    }

    // Check for additional core fields being added in
    if (is_array($_core)) {
        foreach ($_core as $ck => $_cr) {
            foreach ($_cr as $k => $v) {
                if (strpos($k, '_') === 0) {
                    $_data[$ck][$k] = $v;
                }
            }
        }
        unset($_core);
    }

    // Internal defaults
    $now = time();
    $_check = array(
        '_created'    => $now,
        '_updated'    => $now,
        '_profile_id' => 0,
        '_user_id'    => 0
    );
    // If user is logged in, defaults to their account
    if (jrUser_is_logged_in()) {
        $_check['_profile_id'] = (int) $_user['user_active_profile_id'];
        $_check['_user_id']    = (int) $_user['_user_id'];
    }
    foreach ($_data as $k => $_dt) {
        foreach ($_check as $ck => $v) {
            // Any of our _check values can be removed by setting it to false
            if (isset($_data[$k][$ck]) && $_data[$k][$ck] === false) {
                unset($_data[$k][$ck]);
            }
            elseif (!isset($_data[$k][$ck])) {
                $_data[$k][$ck] = $v;
            }
        }
    }

    // Get our unique item id - this will get us the FIRST ID, but all
    // the following are guaranteed to be sequential
    $tbl = jrCore_db_table_name($module, 'item');
    $num = count($_data);
    $ins = str_repeat('(0),', $num);
    $req = "INSERT INTO {$tbl} (`_item_id`) VALUES " . substr($ins, 0, strlen($ins) - 1);
    $iid = jrCore_db_query($req, 'INSERT_ID');
    if (isset($iid) && $iid > 0) {

        // Trigger create event
        if (!$skip_trigger) {
            $uid = $iid;
            foreach ($_data as $k => $_dt) {
                $_args     = array(
                    '_item_id' => $uid,
                    'module'   => $module
                );
                $_data[$k] = jrCore_trigger_event('jrCore', 'db_create_item', $_dt, $_args);
                $uid++;
            }
        }

        $tbl = jrCore_db_table_name($module, 'item_key');
        $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
        $uid = $iid;
        foreach ($_data as $dk => $_dt) {
            foreach ($_dt as $k => $v) {
                // If our value is longer than 508 bytes we split it up
                $len = strlen($v);
                if ($len > 508) {
                    $_tm = array();
                    while ($len) {
                        $_tm[] = mb_strcut($v, 0, 508, "UTF-8");
                        $v     = mb_strcut($v, 508, $len, "UTF-8");
                        $len   = strlen($v);
                    }
                    foreach ($_tm as $idx => $part) {
                        $req .= "('{$uid}','" . jrCore_db_escape($k) . "','" . ($idx + 1) . "','" . jrCore_db_escape($part) . "'),";
                    }
                }
                elseif ($v === 'UNIX_TIMESTAMP()') {
                    $req .= "('{$uid}','" . jrCore_db_escape($k) . "','0',UNIX_TIMESTAMP()),";
                }
                else {
                    $req .= "('{$uid}','" . jrCore_db_escape($k) . "','0','" . jrCore_db_escape($v) . "'),";
                }
            }
            $uid++;
        }
        $req = substr($req, 0, strlen($req) - 1);
        $cnt = jrCore_db_query($req, 'COUNT');
        if (isset($cnt) && $cnt > 0) {
            $_id = array();
            while ($iid < $uid) {
                $_id[] = $iid++;
            }
            return $_id;
        }
    }
    return false;
}

/**
 * Gets all items from a module datastore matching a key and value
 * @param string $module Module the item belongs to
 * @param string $key Key name to find
 * @param mixed $value Value to find
 * @param bool $keys_only if set to TRUE returns array of id's
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_multiple_items_by_key($module, $key, $value, $keys_only = false)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` = '" . jrCore_db_escape($value) . "'";
    $_rt = jrCore_db_query($req, '_item_id');
    if (!isset($_rt) || !is_array($_rt)) {
        return false;
    }
    if ($keys_only) {
        return array_keys($_rt);
    }
    return jrCore_db_get_multiple_items($module, array_keys($_rt));
}

/**
 * Gets a single item from a module datastore by key name and value
 * @param string $module Module the item belongs to
 * @param string $key Key name to find
 * @param mixed $value Value to find
 * @param bool $skip_trigger By default the db_get_item event trigger is sent out to allow additional modules to add data to the item.  Set to TRUE to just return the item from the item datastore.
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_item_by_key($module, $key, $value, $skip_trigger = false, $skip_caching = false)
{
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "' AND `value` = '" . jrCore_db_escape($value) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        return false;
    }
    return jrCore_db_get_item($module, $_rt['_item_id'], $skip_trigger, $skip_caching);
}

/**
 * Gets an item from a module datastore
 * @param string $module Module the item belongs to
 * @param int $id Item ID to retrieve
 * @param bool $skip_trigger By default the db_get_item event trigger is sent out to allow additional modules to add data to the item.  Set to TRUE to just return the item from the item datastore.
 * @param bool $skip_caching Set to true to force item reload (skip caching)
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_item($module, $id, $skip_trigger = false, $skip_caching = false)
{
    if (!is_numeric($id)) {
        return false;
    }

    // See if we are cached - note that this is a GLOBAL cache
    // since it will be the same for any viewing user
    $key = ($skip_trigger) ? 1 : 0;
    $key = "{$module}-{$id}-{$key}";
    if (!$skip_caching && $_rt = jrCore_is_cached($module, $key, false)) {
        return $_rt;
    }

    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `key`,`value` FROM {$tbl} WHERE `_item_id` = '" . intval($id) . "' ORDER BY `index` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt) && is_array($_rt)) {

        // Construct item
        $_ot = array();
        foreach ($_rt as $_v) {
            if (!isset($_ot["{$_v['key']}"])) {
                $_ot["{$_v['key']}"] = $_v['value'];
            }
            else {
                $_ot["{$_v['key']}"] .= $_v['value'];
            }
        }
        unset($_rt);
        $_ot['_item_id'] = intval($id);

        if (!$skip_trigger) {

            // Every item always gets User, Profile and Quota information added in.
            switch ($module) {

                // The one exception is "jrProfile" - since a profile can have
                // more than one User Account associated with it, we let the
                // developer handle that themselves.
                case 'jrProfile':
                    // We only add in Quota info (below)
                    break;

                // For Users we always add in their ACTIVE profile info
                case 'jrUser':
                    $_tm = jrCore_db_get_item('jrProfile', $_ot['_profile_id'], true);
                    if (isset($_tm) && is_array($_tm)) {
                        unset($_tm['_item_id']);
                        $_ot = $_ot + $_tm;
                    }
                    break;

                // Everything else gets both
                default:
                    $pid = $_ot['_profile_id'];
                    // Add in User Info
                    $_tm = jrCore_db_get_item('jrUser', $_ot['_user_id'], true);
                    if (isset($_tm) && is_array($_tm)) {
                        // We do not return passwords
                        unset($_tm['_item_id'], $_tm['user_password'], $_tm['user_old_password']);
                        $_ot = $_ot + $_tm;
                    }
                    // Add in Profile Info
                    $_tm = jrCore_db_get_item('jrProfile', $pid, true);
                    if (isset($_tm) && is_array($_tm)) {
                        unset($_tm['_item_id']);
                        $_ot = $_ot + $_tm;
                    }
                    break;
            }

            // Add in Quota info to item
            if (isset($_ot['profile_quota_id'])) {
                $_tm = jrProfile_get_quota($_ot['profile_quota_id']);
                if ($_tm) {
                    unset($_tm['_item_id']);
                    $_ot = $_ot + $_tm;
                }
            }
            unset($_tm);

            // Trigger db_get_item event
            $_md = array('module' => $module);
            $_ot = jrCore_trigger_event('jrCore', 'db_get_item', $_ot, $_md);

            // Make sure listeners did not change our _item_id
            $_ot['_item_id'] = intval($id);
        }

        // Save to cache
        jrCore_set_flag('datastore_cache_profile_ids', array($_ot['_profile_id']));
        jrCore_add_to_cache($module, $key, $_ot, 86400, $_ot['_profile_id'], false);
        return $_ot;
    }
    return false;
}

/**
 * Get multiple items by _item_id from a module datastore
 *
 * NOTE: This function does NOT send out a trigger to add User/Profile information.  If you need
 * User and Profile information in the returned array of items, make sure and use jrCore_db_search_items
 * With an "in" search for your items ids - i.e. _item_id IN 1,5,7,9,12
 *
 * @param string $module Module the item belongs to
 * @param array $_ids array array of _item_id's to get
 * @param array $_keys Array of key names to get, default is all keys for each item
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_multiple_items($module, $_ids, $_keys = null)
{
    if (!isset($_ids) || !is_array($_ids)) {
        return false;
    }
    // validate id's
    foreach ($_ids as $k => $id) {
        if (!jrCore_checktype($id, 'number_nz')) {
            unset($_ids[$k]);
        }
    }
    if (count($_ids) === 0) {
        return false;
    }
    $ink = false;
    $tbl = jrCore_db_table_name($module, 'item_key');
    if (isset($_keys) && is_array($_keys) && count($_keys) > 0) {
        $_ky = array();
        foreach ($_keys as $k) {
            if ($k == '_item_id') {
                // We handle _item_id down below...
                if (!in_array('_created', $_keys)) {
                    $_ky[] = '_created';
                    $ink = true;
                }
            }
            else {
                $_ky[] = jrCore_db_escape($k);
            }
        }
    }
    $req = "SELECT `_item_id` AS i,`key` AS k,`index` AS x,`value` AS v FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
    if (isset($_ky) && is_array($_ky) && count($_ky) > 0) {
        $req .= " AND `key` IN('" . implode("','", $_ky) . "')";
    }
    $req .= " ORDER BY FIELD(`_item_id`," . implode(',', $_ids) . "), `index` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt) && is_array($_rt)) {

        // First - get results indexed by _item_id
        $_nw = array();
        foreach ($_rt as $k => $v) {
            if ($v['x'] < 2) {
                $_nw["{$v['i']}"]["{$v['k']}"] = $v['v'];
            }
            else {
                $_nw["{$v['i']}"]["{$v['k']}"] .= $v['v'];
            }
            unset($_rt[$k]);
        }

        $i   = 0;
        $_rs = array();
        foreach ($_nw as $id => $_dt) {
            $_rs[$i] = $_dt;
            if ($module != 'jrUser' && $module != 'jrProfile') {
                $_rs[$i]['_item_id'] = $id;
            }
            if ($ink && isset($_rs[$i]['_created'])) {
                unset($_rs[$i]['_created']);
            }
            $i++;
        }
        unset($_nw, $_rt);
        return $_rs;
    }
    return false;
}

/**
 * Gets a single item attribute from a module datastore
 * @param string $module Module the item belongs to
 * @param int $id Item ID to retrieve
 * @param string $key Key value to return
 * @return mixed array on success, bool false on failure
 */
function jrCore_db_get_item_key($module, $id, $key)
{
    if (!jrCore_checktype($id, 'number_nz')) {
        return false;
    }
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "SELECT `value` FROM {$tbl} WHERE `_item_id` = '" . intval($id) . "' AND `key` = '" . jrCore_db_escape($key) . "' ORDER BY `index` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt) && is_array($_rt)) {
        if (!isset($_rt[1])) {
            return $_rt[0]['value'];
        }
        $out = '';
        foreach ($_rt as $_v) {
            $out .= $_v['value'];
        }
        return $out;
    }
    return false;
}

/**
 * Updates multiple Item in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @return bool true on success, false on error
 */
function jrCore_db_update_multiple_items($module, $_data = null, $_core = null)
{
    global $_post, $_user;
    if (!$_data || is_null($_data) || !is_array($_data)) {
        return false;
    }
    foreach ($_data as $id => $_up) {
        if (!jrCore_checktype($id, 'number_nz')) {
            return false;
        }
        // Validate incoming array
        if (isset($_up) && is_array($_up)) {
            $_data[$id] = jrCore_db_get_allowed_item_keys($module, $_up);
        }
        else {
            $_data[$id] = array();
        }
        // We're being updated
        $_data[$id]['_updated'] = time();

        // Check for additional core fields being overridden
        if (isset($_core[$id]) && is_array($_core[$id])) {
            foreach ($_core[$id] as $k => $v) {
                if (strpos($k, '_') === 0) {
                    $_data[$id][$k] = $v;
                }
            }
            unset($_core[$id]);
        }
    }

    $pfx = jrCore_db_get_prefix($module);
    // Check for Pending Support for this module
    // NOTE: We must check for this function being called as part of another (usually save)
    // routine - we don't want to change the value if this is an update that is part of a create process
    // and we don't want to change it if the update is being done by a different module (rating, comment, etc.)
    if (isset($_post['module']) && $_post['module'] == $module && !jrCore_is_magic_view()) {
        $key = md5(json_encode($_data));
        $tmp = jrCore_get_flag("jrcore_created_pending_item_{$key}");
        if (!$tmp) {
            $_pnd = jrCore_get_registered_module_features('jrCore', 'pending_support');
            if ($_pnd && isset($_pnd[$module])) {
                // Pending support is on for this module - check quota
                // 0 = immediately active
                // 1 = review needed on CREATE
                // 2 = review needed on CREATE and UPDATE
                $val = '0';
                if (!jrUser_is_admin() && isset($_user["quota_{$module}_pending"]) && $_user["quota_{$module}_pending"] == '2') {
                    $val = '1';
                }
                foreach ($_data as $id => $_v) {
                    $_data[$id]["{$pfx}_pending"] = $val;
                }
            }
        }
    }

    // Trigger update event
    $_li = array();
    $_lm = array();
    foreach ($_data as $id => $_v) {
        $_args = array(
            '_item_id' => $id,
            'module'   => $module
        );
        $_data[$id] = jrCore_trigger_event('jrCore', 'db_update_item', $_v, $_args);

        // Check for actions that are linking to pending items
        $_li[$id] = 0;
        $_lm[$id] = '';
        if (isset($_v['action_pending_linked_item_id']) && jrCore_checktype($_v['action_pending_linked_item_id'], 'number_nz')) {
            $_li[$id] = (int) $_v['action_pending_linked_item_id'];
            $_lm[$id] = jrCore_db_escape($_v['action_pending_linked_item_module']);
            unset($_data[$id]['action_pending_linked_item_id']);
            unset($_data[$id]['action_pending_linked_item_module']);
        }
    }

    // Update
    $_mx = array();
    $_zo = array();
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "INSERT INTO {$tbl} (`_item_id`,`key`,`index`,`value`) VALUES ";
    foreach ($_data as $id => $_vals) {
        $_mx[$id] = array();
        $_zo[$id] = array();
        foreach ($_vals as $k => $v) {
            // If our value is longer than 500 bytes we split it up
            $len = strlen($v);
            if ($len > 508) {
                $_tm = array();
                while ($len) {
                    $_tm[] = mb_strcut($v, 0, 508, "UTF-8");
                    $v     = mb_strcut($v, 508, $len, "UTF-8");
                    $len   = strlen($v);
                }
                $idx = 0;
                foreach ($_tm as $i => $part) {
                    $idx = ($i + 1);
                    $req .= "('{$id}','" . jrCore_db_escape($k) . "','{$idx}','" . jrCore_db_escape($part) . "'),";
                }
                $_mx[$id][$k] = $idx;
                // We have to also delete any previous 0 index
                $_zo[$id][] = $k;
            }
            elseif ($v === 'UNIX_TIMESTAMP()') {
                $req .= "('{$id}','" . jrCore_db_escape($k) . "',0,UNIX_TIMESTAMP()),";
                $_mx[$id][$k] = '0';
            }
            else {
                $req .= "('{$id}','" . jrCore_db_escape($k) . "',0,'" . jrCore_db_escape($v) . "'),";
                $_mx[$id][$k] = '0';
            }
        }
    }
    $req = substr($req, 0, strlen($req) - 1) . " ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
    jrCore_db_query($req);

    // Cleanup
    $_tm = array();
    foreach ($_mx as $id => $_vals) {
        foreach ($_vals as $fld => $max) {
            $_tm[] = "(`_item_id` = '{$id}' AND `key` = '" . jrCore_db_escape($fld) . "' AND `index` > {$max})";
        }
    }
    if (isset($_zo) && is_array($_zo) && count($_zo) > 0) {
        foreach ($_zo as $id => $_vals) {
            foreach ($_vals as $fld) {
                $_tm[] = "(`_item_id` = '{$id}' AND `key` = '" . jrCore_db_escape($fld) . "' AND `index` = '0')";
            }
        }
    }
    $req = "DELETE FROM {$tbl} WHERE (" . implode(' OR ', $_tm) . ')';
    jrCore_db_query($req);
    unset($_mx, $idx);

    // Check for pending
    $_rq = array();
    $pnd   = jrCore_db_table_name('jrCore', 'pending');
    foreach ($_data as $id => $_vals) {
        if (isset($_vals["{$pfx}_pending"]) && $_vals["{$pfx}_pending"] == '1') {
            // Add pending entry to Pending table...
            $_pd = array(
                'module' => $module,
                'item'   => $_vals,
                'user'   => $_user
            );
            $dat   = jrCore_db_escape(json_encode($_pd));
            $_rq[] = "(UNIX_TIMESTAMP(),'" . jrCore_db_escape($module) . "','{$id}','{$_lm[$id]}','{$_li[$id]}','{$dat}')";
            unset($_pd);
        }
    }
    if (count($_rq) > 0) {
        $req = "INSERT INTO {$pnd} (pending_created,pending_module,pending_item_id,pending_linked_item_module,pending_linked_item_id,pending_data) VALUES ". implode(',', $_rq) ." ON DUPLICATE KEY UPDATE pending_created = UNIX_TIMESTAMP()";
        jrCore_db_query($req);

        // Notify admins of new pending item
        $_sp = array(
            'search'        => array(
                "user_group IN master,admin",
            ),
            'limit'         => 100,
            'return_keys'   => array('_user_id', 'user_name'),
            'skip_triggers' => true,
            'pending_check' => false,
            'privacy_check' => false
        );
        $_rt = jrCore_db_search_items('jrUser', $_sp);
        if (isset($_rt) && isset($_rt['_items']) && is_array($_rt['_items'])) {
            $_rp = reset($_data);
            list($sub, $msg) = jrCore_parse_email_templates('jrCore', 'pending_item', $_rp);
            foreach ($_rt['_items'] as $_v) {
                jrUser_notify($_v['_user_id'], 0, 'jrCore', 'pending_item', $sub, $msg);
            }
        }
    }

    // We need to reset the caches for these items
    $_ch = array();
    foreach ($_data as $id => $_vals) {
        $_ch[] = array($module, "{$module}-{$id}-0", false);
        $_ch[] = array($module, "{$module}-{$id}-1", false);
    }
    jrCore_delete_multiple_cache_entries($_ch);
    unset($_ch);
    return true;
}

/**
 * Updates an Item in a module datastore
 * @param string $module Module the DataStore belongs to
 * @param int $id Unique ID to update
 * @param array $_data Array of Key => Value pairs for insertion
 * @param array $_core Array of Key => Value pairs for insertion - skips jrCore_db_get_allowed_item_keys()
 * @return bool true on success, false on error
 */
function jrCore_db_update_item($module, $id, $_data = null, $_core = null)
{
    $_dt = array(
        $id => $_data
    );
    $_cr = null;
    if (!is_null($_core)) {
        $_cr = array(
            $id => $_core
        );
    }
    return jrCore_db_update_multiple_items($module, $_dt, $_cr);
}

/**
 * Delete multiple items from a module DataStore
 * @param $module string Module DataStore belongs to
 * @param $_ids array Array of _item_id's to delete
 * @param bool $delete_media Set to false to NOT delete associated media files
 * @param mixed $profile_count If set to true, profile_count will be decremented by 1 for given _profile_id.  If set to an integer, it will be used as the profile_id for the counts
 * @return bool
 */
function jrCore_db_delete_multiple_items($module, $_ids, $delete_media = true, $profile_count = true)
{
    if (!isset($_ids) || !is_array($_ids) || count($_ids) === 0) {
        return false;
    }
    // validate id's
    foreach ($_ids as $id) {
        if (!jrCore_checktype($id, 'number_nz')) {
            return false;
        }
    }

    // First, get all items so we can check for attached media
    $_it = jrCore_db_get_multiple_items($module, $_ids);
    if (!isset($_it) || !is_array($_it)) {
        // no items matching
        return true;
    }

    // Delete item
    $tbl = jrCore_db_table_name($module, 'item');
    $req = "DELETE FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
    jrCore_db_query($req);

    // Delete keys
    $tbl = jrCore_db_table_name($module, 'item_key');
    $req = "DELETE FROM {$tbl} WHERE `_item_id` IN(" . implode(',', $_ids) . ")";
    jrCore_db_query($req);

    // Take care of media
    if ($delete_media) {
        foreach ($_it as $_item) {
            foreach ($_item as $k => $v) {
                if (strpos($k, '_extension')) {
                    $field = str_replace('_extension', '', $k);
                    jrCore_delete_item_media_file($module, $field, $_item['_profile_id'], $_item['_item_id']);
                }
            }
        }
    }

    // Take care of profile counts
    if ($profile_count) {
        switch ($module) {
            case 'jrProfile':
            case 'jrUser':
                break;
            default:

                // Update counts for profiles and users affected by the deletion of these items
                $_pi = array();
                $_ui = array();
                foreach ($_it as $_item) {
                    if (isset($_item['_profile_id'])) {
                        $_pi["{$_item['_profile_id']}"] = $_item['_profile_id'];
                    }
                    if (isset($_item['_user_id'])) {
                        $_ui["{$_item['_user_id']}"] = $_item['_user_id'];
                    }
                }
                if (count($_pi) > 0) {
                    $ptb = jrCore_db_table_name('jrProfile', 'item_key');
                    foreach ($_pi as $pid) {
                        // Update counts for module items
                        $req = "UPDATE {$ptb} SET `value` = (SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_profile_id' AND `value` = '{$pid}') WHERE `key` = 'profile_{$module}_item_count' AND `_item_id` = '{$pid}' LIMIT 1";
                        jrCore_db_query($req);
                    }
                }
                if (count($_ui) > 0) {
                    $ptb = jrCore_db_table_name('jrUser', 'item_key');
                    foreach ($_ui as $uid) {
                        // Update counts for module items
                        $req = "UPDATE {$ptb} SET `value` = (SELECT COUNT(`_item_id`) FROM {$tbl} WHERE `key` = '_user_id' AND `value` = '{$uid}') WHERE `key` = 'user_{$module}_item_count' AND `_item_id` = '{$uid}' LIMIT 1";
                        jrCore_db_query($req);
                    }
                }
                unset($_pi, $_ui);
                break;
        }
    }


    // Lastly, trigger
    foreach ($_it as $_item) {

        switch ($module) {
            case 'jrProfile':
                $iid = (int) $_item['_profile_id'];
                break;
            case 'jrUser':
                $iid = (int) $_item['_user_id'];
                break;
            default:
                $iid = (int) $_item['_item_id'];
                break;
        }

        // reset caches
        $_ch = array(
            array($module, "{$module}-{$iid}-0", false),
            array($module, "{$module}-{$iid}-1", false)
        );
        jrCore_delete_multiple_cache_entries($_ch);

        $_args = array(
            '_item_id' => $iid,
            'module'   => $module
        );
        jrCore_trigger_event('jrCore', 'db_delete_item', $_item, $_args);
    }
    return true;
}

/**
 * Deletes an Item in the module DataStore
 *
 * <b>NOTE:</b> By default this function will also delete any media files that are associated with the item id!
 *
 * @param string $module Module the DataStore belongs to
 * @param int $id Item ID to delete
 * @param bool $delete_media Set to false to NOT delete associated media files
 * @param mixed $profile_count If set to true, profile_count will be decremented by 1 for given _profile_id.  If set to an integer, it will be used as the profile_id for the counts
 * @return bool
 */
function jrCore_db_delete_item($module, $id, $delete_media = true, $profile_count = true)
{
    $id = array($id);
    return jrCore_db_delete_multiple_items($module, $id, $delete_media, $profile_count);
}

/**
 * Search a module DataStore and return matching items
 *
 * $_params is an array that contains all the function parameters - i.e.:
 *
 * <code>
 * $_params = array(
 *     'search' => array(
 *         'user_name = brian',
 *         'user_height > 72'
 *     ),
 *     'order_by' => array(
 *         'user_name' => 'asc',
 *         'user_height' => 'desc'
 *     ),
 *     'group_by' => '_user_id',
 *     'return_keys' => array(
 *         'user_email',
 *         'username'
 *      ),
 *     'return_count' => true|false,
 *     'limit' => 50
 * );
 *
 * wildcard searches use a % in the key name:
 * 'search' => array(
 *     'user_% = brian',
 *     '% like brian%'
 * );
 * </code>
 *
 * "no_cache" - by default search results are cached - this will disable caching if set to true
 *
 * "cache_seconds" - set length of time result set is cached
 *
 * "return_keys" - only return the matching keys
 *
 * "return_count" - If the "return_count" parameter is set to TRUE, then only the COUNT of matching
 * entries will be returned.
 *
 * "privacy_check" - by default only items that are viewable to the calling user will be returned -
 * set "privacy_check" to FALSE to disable privacy settings checking.
 *
 * "ignore_pending" - by default only items that are NOT pending are shown - set ignore_pending to
 * TRUE to skip the pending item check
 *
 * "exclude_(module)_keys" - some modules (such as jrUser and jrProfile) add extra keys into the returned
 * results - you can skip adding these extra keys in by disable the module(s) you do not want keys for.
 *
 * Valid Search conditions are:
 * <code>
 *  =        - "equals"
 *  !=       - "not equals"
 *  >        - greater than
 *  >=       - greater than or equal to
 *  <        - less than
 *  <=       - less than or equal to
 *  like     - wildcard text search - i.e. "user_name like %ob%" would find "robert" and "bob". % is wildcard character.
 *  not_like - wildcard text negated search - same format as "like"
 *  in       - "in list" of values - i.e. "user_name in brian,douglas,paul,michael" would find all 4 matches
 *  not_in   - negated "in least" search - same format as "in"
 * </code>
 * @param string $module Module the DataStore belongs to
 * @param array $_params Search Parameters
 * @return mixed Array on success, Bool on error
 */
function jrCore_db_search_items($module, $_params)
{
    global $_user, $_conf;
    if (!$_params || !is_array($_params)) {
        return false;
    }
    $_params['module'] = $module;
    // Backup copy of original params
    $_backup = $_params;

    // Other modules can provide supported parameters for searching - send
    // our trigger so those events can be added in.
    if (!isset($_params['skip_triggers'])) {
        $_params = jrCore_trigger_event('jrCore', 'db_search_params', $_params, array('module' => $module));
        // See if a listener switched modules on us
        $_change = jrCore_get_flag('jrcore_active_trigger_args');
        if (isset($_change['module']) && $_change['module'] != $module) {
            $module = $_change['module'];
            $_params['module'] = $module;
        }
        unset($_change);
    }

    // See if we are cached
    $cky = json_encode($_params);
    if ((!isset($_params['no_cache']) || $_params['no_cache'] === false) && $tmp = jrCore_is_cached($module, $cky)) {
        return $tmp;
    }

    // We allow searching on both USER and PROFILE keys for all modules - check for those here
    if (isset($_params['search']) && is_array($_params['search'])) {
        switch ($module) {
            case 'jrProfile':
                $_ck = array(
                    'user' => 'jrUser'
                );
                break;
            case 'jrUser':
                $_ck = array(
                    'profile' => 'jrProfile'
                );
                break;
            default:
                $_ck = array(
                    'user'    => 'jrUser',
                    'profile' => 'jrProfile'
                );
                break;
        }
        foreach ($_ck as $pfx => $mod) {
            foreach ($_params['search'] as $k => $cond) {
                $_c = array();
                if (strpos($cond, '||')) {
                    $tbl = jrCore_db_table_name($mod, 'item_key');
                    foreach (explode('||', $cond) as $part) {
                        if (strpos(trim($part), "{$pfx}_") === 0) {
                            if ($_sc = jrCore_db_check_for_supported_operator($part)) {
                                // There are keys in this condition we need to handle
                                $_c[] = "`key` = '" . jrCore_db_escape($_sc[0]) . "' AND `value` {$_sc[1]} {$_sc[2]}";
                                unset($_params['search'][$k]);
                            }
                            else {
                                jrCore_logger('MAJ', 'invalid OR search operator in jrCore_db_search_items parameters', array($module, $_params));
                                return false;
                            }
                        }
                    }
                    if (count($_c) > 0) {
                        $_params['search'][] = "_{$pfx}_id IN (SELECT `_item_id` FROM {$tbl} WHERE (" . implode(' OR ', $_c) . '))';
                    }
                }
                elseif (strpos(trim($cond), "{$pfx}_") === 0) {
                    $tbl = jrCore_db_table_name($mod, 'item_key');
                    if ($_sc = jrCore_db_check_for_supported_operator($cond)) {
                        $_params['search'][] = "_{$pfx}_id IN (SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($_sc[0]) . "' AND `value` {$_sc[1]} {$_sc[2]})";
                        unset($_params['search'][$k]);
                    }
                    else {
                        jrCore_logger('MAJ', 'invalid OR search operator in jrCore_db_search_items parameters', array($module, $_params));
                        return false;
                    }
                }
            }
            unset($_c);
        }
    }

    $dob = '_created';
    $_ob = array();
    $_sc = array();
    $_ky = array();
    $ino = false;
    $_so = false;
    if (isset($_params['search']) && count($_params['search']) > 0) {

        // Pre check for OR search conditions
        if (strpos(json_encode($_params['search']), '||')) {
            $_so = array();
            foreach ($_params['search'] as $k => $v) {
                if (strpos($v, '||')) {
                    foreach (explode('||', $v) as $cond) {
                        if (!$tmp = jrCore_db_check_for_supported_operator($cond)) {
                            jrCore_logger('MAJ', 'invalid OR search operator in jrCore_db_search_items parameters', array($module, $_params));
                            return false;
                        }
                        $_so[$k][] = $tmp;
                    }
                    if (count($_so) > 0) {
                        $_params['search'][$k] = "{$k} OR COND";
                    }
                }
            }
        }

        foreach ($_params['search'] as $v) {
            list($key, $opt, $val) = @explode(' ', $v, 3);
            if (!isset($val) || strlen($val) === 0 || !isset($opt) || strlen($opt) === 0) {
                // Bad Search
                jrCore_logger('MAJ', 'invalid search criteria in jrCore_db_search_items parameters', array($module, $_params));
                return false;
            }
            $key = jrCore_str_to_lower($key);
            if (!strpos(' ' . $key, '%')) {
                $_ky[$key] = 1;
            }
            if (strpos($val, '(SELECT ') === 0) {
                // We have a sub query as our match condition
                switch ($opt) {
                    case 'not_in':
                        $_sc[] = array($key, 'NOT IN', $val, 'no_quotes');
                        break;
                    case 'not_like':
                        $_sc[] = array($key, 'NOT LIKE', $val, 'no_quotes');
                        break;
                    default:
                        $_sc[] = array($key, $opt, $val, 'no_quotes');
                        break;
                }
                continue;
            }
            elseif ($opt == 'OR' && $val == 'COND') {
                // We have an OR condition as our match condition
                $_sc[] = array($key, $opt, $val, 'parens');
                continue;
            }
            // Check for OR conditions (||)
            switch (jrCore_str_to_lower($opt)) {
                case '>':
                case '>=':
                case '<':
                case '<=':
                    if (strpos($val, '.')) {
                        $_sc[] = array($key, strtoupper($opt), floatval($val), 'no_quotes');
                    }
                    else {
                        $_sc[] = array($key, strtoupper($opt), intval($val), 'no_quotes');
                    }
                    break;
                case '!=':
                    // With a NOT EQUAL operator on non _item_id, we also need to include items where the key may be MISSING or NULL
                    if ($key == '_item_id') {
                        $_sc[] = array($key, $opt, intval($val), 'no_quotes');
                    }
                    else {
                        $tbl = jrCore_db_table_name($module, 'item_key');
                        $vrq = "(SELECT `_item_id` FROM {$tbl} WHERE ((`key` = '{$key}' AND `value` != '" . jrCore_db_escape($val) . "') OR (`key` = '_created' AND `_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "'))))";
                        $_sc[] = array('_item_id', 'IN', $vrq, 'no_quotes', $key);
                        unset($_ky[$key]);
                    }
                    break;
                case '=':
                    $_sc[] = array($key, strtoupper($opt), jrCore_db_escape($val));
                    break;
                case 'like':
                case 'regexp':
                    $_sc[] = array($key, strtoupper($opt), jrCore_db_escape($val));
                    if (!$_so) {
                        $_so = array();
                    }
                    break;
                case 'not_like':
                    $tbl = jrCore_db_table_name($module, 'item_key');
                    $vrq = "(SELECT `_item_id` FROM {$tbl} WHERE ((`key` = '{$key}' AND `value` NOT LIKE '". jrCore_db_escape($val) ."') OR (`key` = '_created' AND `_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "'))))";
                    $_sc[] = array('_item_id', 'IN', $vrq, 'no_quotes', $key);
                    unset($_ky[$key]);
                    if (!$_so) {
                        $_so = array();
                    }
                    break;
                case 'in':
                    $_vl = array();
                    foreach (explode(',', $val) as $iv) {
                        if (ctype_digit($iv)) {
                            $_vl[] = (int) $iv;
                        }
                        else {
                            $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                        }
                    }
                    $val = "(" . implode(',', $_vl) . ')';
                    $_sc[] = array($key, strtoupper($opt), $val, 'no_quotes');
                    // By default if we do NOT get an ORDER BY clause on an IN, order by FIELD unless specifically set NOT to
                    if (!isset($_params['order_by']) && !isset($_params['return_item_id_only'])) {
                        $ino = $key;
                        $_do = $_vl;
                    }
                    unset($_vl);
                    break;
                case 'not_in':
                    $_vl = array();
                    foreach (explode(',', $val) as $iv) {
                        if (ctype_digit($iv)) {
                            $_vl[] = (int) $iv;
                        }
                        else {
                            $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                        }
                    }
                    $val = "(" . implode(',', $_vl) . ')';
                    if ($key == '_item_id') {
                        $_sc[] = array($key, 'NOT IN', $val, 'no_quotes');
                    }
                    else {
                        $tbl = jrCore_db_table_name($module, 'item_key');
                        $vrq = "(SELECT `_item_id` FROM {$tbl} WHERE ((`key` = '{$key}' AND `value` NOT IN{$val}) OR (`key` = '_created' AND `_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($key) . "'))))";
                        $_sc[] = array('_item_id', 'IN', $vrq, 'no_quotes', $key);
                        unset($_ky[$key]);
                    }
                    unset($_vl);
                    break;
                default:
                    jrCore_logger('MAJ', "invalid search operator in jrCore_db_search_items search: {$opt}", array($module, $_params));
                    return false;
                    break;
            }

        }
    }

    // Module prefix
    $pfx = jrCore_db_get_prefix($module);

    // Check for Pending Support
    $_pn = jrCore_get_registered_module_features('jrCore', 'pending_support');
    if (!jrUser_is_admin() && isset($_pn) && isset($_pn[$module]) && !isset($_params['ignore_pending'])) {
        // Pending support is on for this module - check status
        // 0 = immediately active
        // 1 = review needed
        // Let's see if anything is pending
        $_pq = jrCore_get_flag('jrcore_db_search_items_pending_modules');
        if (!$_pq) {
            $ptb = jrCore_db_table_name('jrCore', 'pending');
            $prq = "SELECT pending_module FROM {$ptb} GROUP BY pending_module";
            $_pq = jrCore_db_query($prq, 'pending_module', false, 'pending_module');
            jrCore_set_flag('jrcore_db_search_items_pending_modules', $_pq);
        }
        if (isset($_pq) && is_array($_pq) && isset($_pq[$module])) {
            $_sc[] = array("{$pfx}_pending", '!=', '1');
            $_ky["{$pfx}_pending"] = 1;
        }
    }

    // in order to properly ORDER BY, we must be including the key we are
    // ordering by in our JOIN - thus, if the user specifies an ORDER BY on
    // a key that they did not search on, then we must add in an IS NOT NULL
    // condition for the order by key
    $custom_order_by = false;
    if (isset($_params['order_by']) && is_array($_params['order_by'])) {
        // Check for special "display" order_by
        if (isset($_params['order_by']["{$pfx}_display_order"]) && count($_params['order_by']) === 1) {
            // Sort by display order, _created desc default
            $_params['order_by']['_item_id'] = 'numerical_desc';
        }
        foreach ($_params['order_by'] as $k => $v) {
            if ($k == 0 && $k != '_item_id') {
                $dob = $k;
            }
            // Check for random order - no need to join
            if (!isset($_ky[$k]) && $k != '_item_id' && $v != 'random') {

                // See if we have existing search queries.  This sub query is needed
                // since if there are NO search conditions, then the table will NOT
                // have been joined in order to do the order - but if we join on a
                // not exists condition (!=, not_in, not_like) we have to include items
                // that do NOT have the DS key set
                if (count($_sc) === 0 && $k != '_created' && $k != '_updated') {
                    $tbl = jrCore_db_table_name($module, 'item_key');
                    $vrq = "(a.`key` = '" . jrCore_db_escape($k) . "') OR (a.`key` = '_created' AND a.`_item_id` IN (SELECT `_item_id` FROM {$tbl} WHERE `_item_id` NOT IN(SELECT `_item_id` FROM {$tbl} WHERE `key` = '" . jrCore_db_escape($k) . "')))";
                    $_sc[] = array($k, 'CUSTOM', $vrq);
                    $custom_order_by = true;
                }
                else {
                    // (e.`value` IS NOT NULL OR e.`value` IS NULL)
                    $_sc[] = array($k, 'IS OR IS NOT', 'NULL');
                }
                $_ky[$k] = 1;
            }
        }
    }

    // Lastly - if we get a group_by parameter, we have to make sure the field
    // that is being grouped on is joined to the query so it can be grouped
    $_gb = array();
    if (isset($_params['group_by']) && strlen($_params['group_by']) > 0 && !strpos(' ' . $_params['group_by'], '_item_id')) {
        // Check for special UNIQUE in our group_by
        if (strpos($_params['group_by'], ' UNIQUE')) {
            list($gfd,) = explode(' ', $_params['group_by']);
            $gfd = trim($gfd);
            $gtb = jrCore_db_table_name($module, 'item_key');
            $grq = "SELECT MAX(`_item_id`) AS iid FROM {$gtb} WHERE `key` = '" . jrCore_db_escape($gfd) ."' GROUP BY `value`";
            $_gr = jrCore_db_query($grq, 'iid', false, 'iid');
            if ($_gr && is_array($_gr)) {
                $_sc[] = array('_item_id', 'IN', '(' . implode(',', $_gr) . ')', 'no_quotes');
            }
            unset($_params['group_by']);
        }
        else {
            foreach (explode(',', $_params['group_by']) as $k => $gby) {
                $gby = trim($gby);
                if (!isset($_ky[$gby])) {
                    $_sc[] = array($gby, 'IS NOT', 'NULL');
                }
                $_gb[$gby] = $k;
            }
        }
    }
    // Make sure we got something
    if (!isset($_sc) || !is_array($_sc) || count($_sc) === 0) {
        $_sc[] = array('_item_id', '>', 0);
    }

    // To try and avoiding creating temp tables, we need to make sure if we have
    // an ORDER BY clause, the table that is being ordered on needs to be the
    // first table in the query
    // https://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html
    if (isset($_params['order_by']) && is_array($_params['order_by'])) {
        $o_key = array_keys($_params['order_by']);
        $o_key = reset($o_key);
        $_stmp = array();
        $found = false;
        foreach ($_sc as $k => $v) {
            if (!$found && $v[0] == $o_key) {
                $_stmp[0] = $v;
                $found = true;
            }
            else {
                $t_key = ($k + 1);
                $_stmp[$t_key] = $v;
            }
        }
        ksort($_stmp, SORT_NUMERIC);
        $_sc = array_values($_stmp);
        unset($_stmp, $o_key, $found, $t_key);
    }

    // Build query and get data
    $idx = true;
    $tba = jrCore_db_table_name($module, 'item_key');
    $_al = range('a', 'z');
    $req = ''; // Main data Query
    foreach ($_sc as $k => $v) {
        $als = $_al[$k];
        if ($k == 0) {
            if (isset($_so) && is_array($_so)) {
                // With an OR condition we have to group on the item_id or we can
                // get multiple results for the same key
                $req .= "SELECT DISTINCT(a.`_item_id`) AS _item_id FROM {$tba} a\n";
                $idx = false;
            }
            else {
                $req .= "SELECT a.`_item_id` AS _item_id FROM {$tba} a\n";
            }
        }
        // wildcard
        elseif (strpos(' ' . $v[0], '%') || $v[1] == 'OR') {
            if (!$idx) {
                // We're already doing a DISTINCT so no need for index requirement
                $req .= "LEFT JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id`)\n";
            }
            else {
                $req .= "LEFT JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`index` < 2)\n";
            }
        }
        elseif ($v[0] !== '_item_id') {
            if (!$idx) {
                // We're already doing a DISTINCT so no need for index requirement
                $req .= "LEFT JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}')\n";
            }
            else {
                switch ($v[0]) {
                    case '_item_id':
                    case '_profile_id':
                    case '_created':
                    case '_updated':
                        // No index needed on keys we know cannot be longer than 512
                        $req .= "LEFT JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}')\n";
                        break;
                    default:
                        $req .= "LEFT JOIN {$tba} {$als} ON ({$als}.`_item_id` = a.`_item_id` AND {$als}.`key` = '{$v[0]}' AND {$als}.`index` < 2)\n";
                        break;
                }
            }
        }
        // Save for our "order by" below - we must be searching on a column to order by it
        $_ob["{$v[0]}"] = $als;
        // See if this is our group_by column
        if ((!isset($_params['return_count']) || $_params['return_count'] === false) && isset($_gb["{$v[0]}"])) {
            if (!isset($group_by)) {
                $group_by = " GROUP BY {$als}.`value`";
            }
            else {
                $group_by .= ",{$als}.`value`";
            }
        }
    }

    // Privacy Check - non admin users
    // 0 = Private
    // 1 = Global
    // 2 = Shared
    $add = '';
    if (!jrUser_is_admin() && (!isset($_params['privacy_check']) || $_params['privacy_check'] !== false)) {

        $tbp = jrCore_db_table_name('jrProfile', 'item_key');
        $req .= "LEFT JOIN {$tba} pr ON (pr.`_item_id` = a.`_item_id` AND pr.`key` = '_profile_id')\n";
        $req .= "LEFT JOIN {$tbp} pp ON (pp.`_item_id` = pr.`value` AND pp.`key` = 'profile_private')\n";

        // Users that are not logged in only see global profiles
        if (!jrUser_is_logged_in()) {
            $add = "AND pp.`value` = '1'\n";
        }
        else {
            $_pr = array();
            $hid = (int) jrUser_get_profile_home_key('_profile_id');
            if ($hid > 0) {
                $_pr[] = $hid;
            }
            if (isset($_user['user_active_profile_id']) && jrCore_checktype($_user['user_active_profile_id'], 'number_nz') && $_user['user_active_profile_id'] != $hid) {
                $_pr[] = (int) $_user['user_active_profile_id'];
            }
            if (jrCore_module_is_active('jrFollower') && jrUser_is_logged_in()) {
                // If we are logged in, we can see GLOBAL profiles as well as profiles we are followers of
                if ($_ff = jrFollower_get_profiles_followed($_user['_user_id'])) {
                    $_pr = array_merge($_ff, $_pr);
                    unset($_ff);
                }
            }
            if (jrUser_is_power_user() || jrUser_is_multi_user()) {
                // Power/Multi users can always see the profiles they manage
                if (isset($_user['user_linked_profile_ids']) && strlen($_user['user_linked_profile_ids']) > 0) {
                    $_tm = explode(',', $_user['user_linked_profile_ids']);
                    if (is_array($_tm)) {
                        $_pr = array_merge($_pr, $_tm);
                        unset($_tm);
                    }
                }
            }
            if (count($_pr) > 0) {
                $add = "AND (pp.`value` = '1' OR pr.`value` IN(0," . implode(',', $_pr) . "))\n";
            }
            else {
                $add = "AND pp.`value` = '1'\n";
            }
        }
    }

    $req .= 'WHERE ';
    foreach ($_sc as $k => $v) {
        if ($k == 0) {
            if ($v[0] == '_item_id') {
                if ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                    if ($v[1] == '>' && $v[2] == '0') {
                        $req .= "a.`key` = '{$dob}'\n";
                    }
                    else {
                        $req .= "(a.`key` = '{$dob}' AND a.`_item_id` {$v[1]} {$v[2]})\n";
                    }
                }
                else {
                    $req .= "(a.`key` = '{$dob}' AND a.`_item_id` {$v[1]} '{$v[2]}')\n";
                }
            }
            elseif ($v[1] == 'CUSTOM') {
                $req .= "{$v[2]}\n";
            }
            elseif ($v[1] == 'IS OR IS NOT') {
                $req .= "a.`key` = '{$v[0]}'\n";
            }
            elseif ($v[0] != '_item_id' && isset($v[3]) && $v[3] == 'add_null') {
                $req .= "a.`key` = '{$v[0]}' AND (a.`value` {$v[1]} {$v[2]} OR a.`value` IS NULL)\n";
            }
            elseif (isset($v[3]) && $v[3] == 'parens' && isset($_so["{$v[0]}"])) {
                $_bd = array();
                // ((a.key = 'something' AND value = 1) OR (a.key = 'other' AND value = 2))
                $req .= '(';
                foreach ($_so["{$v[0]}"] as $_part) {
                    if ($_part[0] == '_item_id') {
                        $_bd[] = "(a.`key` = '_created' AND a.`_item_id` {$_part[1]} {$_part[2]})";
                    }
                    else {
                        $_bd[] = "(a.`key` = '{$_part[0]}' AND a.`value` {$_part[1]} {$_part[2]})";
                    }
                }
                $req .= implode(' OR ', $_bd) .")\n";
            }
            elseif ($v[0] == "{$pfx}_visible") {
                $req .= "a.`key` = '{$v[0]}' AND (a.`value` IS NULL OR a.`value` != 'off')\n";
            }
            // wildcard (all keys)
            elseif ($v[0] == '%') {
                if (isset($v[3]) && $v[3] == 'no_quotes') {
                    $req .= "a.`value` {$v[1]} {$v[2]}\n";
                }
                else {
                    $req .= "a.`value` {$v[1]} '{$v[2]}'\n";
                }
            }
            // wildcard match on key
            elseif (strpos(' ' . $v[0], '%')) {
                if (isset($v[3]) && $v[3] == 'no_quotes') {
                    $req .= "a.`key` LIKE '{$v[0]}' AND a.`value` {$v[1]} {$v[2]}\n";
                }
                else {
                    $req .= "a.`key` LIKE '{$v[0]}' AND a.`value` {$v[1]} '{$v[2]}'\n";
                }
            }
            // IN / NOT IN (no quotes) or NULL
            elseif ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                $req .= "a.`key` = '{$v[0]}' AND a.`value` {$v[1]} {$v[2]}\n";
            }
            else {
                $req .= "a.`key` = '{$v[0]}' AND a.`value` {$v[1]} '{$v[2]}'\n";
            }
        }
        else {
            // If we are searching by _item_id we always use "a" for our prefix
            if ($v[0] == '_item_id') {
                if ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                    $req .= "AND a.`_item_id` {$v[1]} {$v[2]}\n";
                }
                else {
                    $req .= "AND a.`_item_id` {$v[1]} '{$v[2]}'\n";
                }
            }
            else {
                $als = $_al[$k];
                // Special is or is not condition
                // (e.`value` IS NOT NULL OR e.`value` IS NULL)
                // This allows an ORDER_BY on a column that may not be set in all DS entries
                if ($v[1] == 'IS OR IS NOT') {
                    $req .= "AND ({$als}.`value` > '' OR {$als}.`value` IS NULL)\n";
                }
                elseif (isset($v[3]) && $v[3] == 'add_null') {
                    $req .= "AND ({$als}.`value` {$v[1]} {$v[2]} OR {$als}.`value` IS NULL)\n";
                }
                elseif (isset($v[3]) && $v[3] == 'parens' && isset($_so["{$v[0]}"])) {
                    $_bd = array();
                    // ((a.key = 'something' AND value = 1) OR (a.key = 'other' AND value = 2))
                    $req .= 'AND (';
                    foreach ($_so["{$v[0]}"] as $_part) {
                        if ($_part[0] == '_item_id') {
                            $_bd[] = "(a.`_item_id` {$_part[1]} {$_part[2]})";
                        }
                        else {
                            $_bd[] = "({$als}.`key` = '{$_part[0]}' AND {$als}.`value` {$_part[1]} {$_part[2]})";
                        }
                    }
                    $req .= implode(' OR ', $_bd) .")\n";
                }
                // wildcard (all keys)
                elseif ($v[0] == '%') {
                    $req .= "AND {$als}.`value` {$v[1]} '{$v[2]}'\n";
                }
                // wildcard match on key
                elseif (strpos(' ' . $v[0], '%')) {
                    $req .= "AND {$als}.`key` LIKE '{$v[0]}' AND {$als}.`value` {$v[1]} '{$v[2]}'\n";
                }
                elseif ($v[2] == 'NULL' || (isset($v[3]) && $v[3] == 'no_quotes')) {
                    $req .= "AND {$als}.`value` {$v[1]} {$v[2]}\n";
                }
                else {
                    $req .= "AND {$als}.`value` {$v[1]} '{$v[2]}'\n";
                }
            }
        }
    }

    // Bring in privacy additions if set...
    if (isset($add{1})) {
        $req .= $add;
    }

    // For our counting query
    $re2 = $req;

    // Some items are not needed in our counting query
    if (!isset($_params['return_count']) || $_params['return_count'] === false) {

        // Group by
        if (isset($group_by{0})) {
            $req .= $group_by .' ';
            $re2 .= $group_by .' ';
        }
        elseif (!strpos($req, 'RAND()')) {
            // Default - group by item_id
            if (isset($ino) && $ino == '_item_id') {
                $req .= "GROUP BY a._item_id ";
                $re2 .= "GROUP BY a._item_id ";
            }
        }

        // Order by
        if (isset($_params['order_by']) && is_array($_params['order_by']) && count($_params['order_by']) > 0) {
            $_ov = array();
            $oby = 'ORDER BY ';

            foreach ($_params['order_by'] as $k => $v) {
                $v = strtoupper($v);
                switch ($v) {

                    case 'RAND':
                    case 'RANDOM':
                        if (isset($_params['limit']) && intval($_params['limit']) === 1) {
                            $req .= "AND a.`_item_id` >= FLOOR(1 + RAND() * (SELECT MAX(_item_id) FROM " . jrCore_db_table_name($module, 'item') . ")) ";
                            $oby = false;
                        }
                        else {
                            $_ov[] = 'RAND()';
                        }
                        // With random ordering we ignore all other orders...
                        continue 2;
                        break;

                    case 'ASC':
                    case 'DESC':
                        if (!isset($_ob[$k]) && $k != '_item_id') {
                            return "error: you must include the {$k} field in your search criteria to order_by it";
                        }
                        // If we are ordering by _item_id, we do not order by value...
                        if ($custom_order_by) {
                            $_ov[] = "a.`value` {$v}";
                        }
                        elseif ($k == '_item_id') {
                            $_ov[] = "a.`_item_id` {$v}";
                        }
                        else {
                            $_ov[] = $_ob[$k] . ".`value` {$v}";
                        }
                        break;

                    case 'NUMERICAL_ASC':
                        if (!isset($_ob[$k]) && $k != '_item_id') {
                            return "error: you must include the {$k} field in your search criteria to order_by it";
                        }
                        if ($custom_order_by) {
                            $_ov[] = "a.`value` ASC";
                        }
                        else {
                            switch ($k) {
                                case '_item_id':
                                case '_user_id':
                                case '_profile_id':
                                    $_ov[] = "a.`_item_id` ASC";
                                    break;
                                case '_created':
                                case '_updated':
                                    $_ov[] = $_ob[$k] . ".`value` ASC";
                                    break;
                                default:
                                    $_ov[] = '(' . $_ob[$k] . ".`value` + 0) ASC";
                                    break;
                            }
                        }
                        break;

                    case 'NUMERICAL_DESC':
                        if (!isset($_ob[$k]) && $k != '_item_id') {
                            return "error: you must include the {$k} field in your search criteria to order_by it";
                        }
                        if ($custom_order_by) {
                            $_ov[] = "a.`value` DESC";
                        }
                        else {
                            switch ($k) {
                                case '_item_id':
                                case '_user_id':
                                case '_profile_id':
                                    $_ov[] = "a.`_item_id` DESC";
                                    break;
                                case '_created':
                                case '_updated':
                                    $_ov[] = $_ob[$k] . ".`value` DESC";
                                    break;
                                default:
                                    $_ov[] = '(' . $_ob[$k] . ".`value` + 0) DESC";
                                    break;
                            }
                        }
                        break;

                    default:
                        return "error: invalid order direction: {$v} received for {$k} - must be one of: ASC, DESC, NUMERICAL_ASC, NUMERICAL_DESC, RANDOM";
                        break;
                }
            }
            if (isset($oby) && strlen($oby) > 0) {
                $req .= $oby . implode(', ', $_ov) . ' ';
            }
        }

        // If we get a LIST of items, we (by default) order by that list unless we get a different order by
        elseif ($ino && isset($_do)) {
            if ($ino == '_item_id') {
                $field = "a.`_item_id`";
            }
            else {
                $field = $_ob[$ino] . ".`_item_id`";
            }
            if (isset($_params['limit'])) {
                $req .= "ORDER BY FIELD({$field}," . implode(',', array_reverse(array_slice($_do, 0, $_params['limit'], true))) . ") DESC ";
            }
            elseif (isset($_params['pagebreak']) && jrCore_checktype($_params['pagebreak'], 'number_nz')) {
                // Check for good page num
                if (!isset($_params['page']) || !jrCore_checktype($_params['page'], 'number_nz')) {
                    $_params['page'] = 1;
                }
                $req .= "ORDER BY FIELD({$field}," . implode(',', array_reverse(array_slice($_do, 0, ($_params['page'] * $_params['pagebreak'])))) . ") DESC ";
            }
            else {
                $req .= "ORDER BY FIELD({$field}," . implode(',', $_do) . ") ";
            }
            unset($_do);
        }
    }

    // Start our result set.  When doing a search an array with 2 keys is returned:
    // "_items" - contains the actual search results numerically indexed
    // "info" - contains meta information about the result set
    $_rs = array(
        '_items' => false,
        'info'   => array()
    );

    // Limit
    if (isset($_params['limit']) && !isset($_params['pagebreak'])) {
        if (!jrCore_checktype($_params['limit'], 'number_nz')) {
            return "error: invalid limit value - must be a number greater than 0";
        }
        $req .= 'LIMIT ' . intval($_params['limit']) . ' ';
        $_rs['info']['limit'] = intval($_params['limit']);
    }

    // Pagebreak
    elseif ((!isset($_params['return_count']) || $_params['return_count'] === false) && isset($_params['pagebreak']) && jrCore_checktype($_params['pagebreak'], 'number_nz')) {

        // Check for good page num
        if (!isset($_params['page']) || !jrCore_checktype($_params['page'], 'number_nz')) {
            $_params['page'] = 1;
        }
        if (isset($_so) && is_array($_so)) {
            $re2 = str_replace('SELECT DISTINCT(a.`_item_id`) AS _item_id', 'SELECT COUNT(DISTINCT(a.`_item_id`)) AS tc', $re2);
        }
        else {
            $re2 = str_replace('SELECT a.`_item_id` AS _item_id', 'SELECT COUNT(a.`_item_id`) AS tc', $re2);
        }

        $beg = explode(' ', microtime());
        $beg = $beg[1] + $beg[0];

        if (strpos($req, 'GROUP BY')) {
            $_ct = array(
                'tc' => jrCore_db_query($re2, 'NUM_ROWS')
            );
        }
        else {
            $_ct = jrCore_db_query($re2, 'SINGLE');
        }

        $end = explode(' ', microtime());
        $end = $end[1] + $end[0];
        $end = round(($end - $beg), 2);
        if ($end > 2 && isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
            jrCore_logger('MAJ', "Slow jrCore_db_search_items() count query: {$end} seconds", $re2);
        }

        if (is_array($_ct) && isset($_ct['tc'])) {

            // Check if we also have a limit - this is going to limit the total
            // result set to a specific size, but still allow pagination
            if (isset($_params['limit'])) {
                // We need to see WHERE we are in the requested set
                $_rs['info']['total_items'] = (isset($_ct['tc']) && jrCore_checktype($_ct['tc'], 'number_nz')) ? intval($_ct['tc']) : 0;
                if ($_rs['info']['total_items'] > $_params['limit']) {
                    $_rs['info']['total_items'] = $_params['limit'];
                }
                // Find out how many we are returning on this query...
                $pnum = $_params['pagebreak'];
                if (($_params['page'] * $_params['pagebreak']) > $_params['limit']) {
                    $pnum = (int) ($_params['limit'] % $_params['pagebreak']);
                    // See if the request range is completely outside the last page
                    if ($_params['pagebreak'] < $_params['limit'] && $_params['page'] > ceil($_params['limit'] / $_params['pagebreak'])) {
                        // invalid set
                        return false;
                    }
                }
                $req .= "LIMIT " . intval(($_params['page'] - 1) * $_params['pagebreak']) . ",{$pnum}";
            }
            else {
                $_rs['info']['total_items'] = (isset($_ct['tc']) && jrCore_checktype($_ct['tc'], 'number_nz')) ? intval($_ct['tc']) : 0;
                $req .= "LIMIT " . intval(($_params['page'] - 1) * $_params['pagebreak']) . ",{$_params['pagebreak']}";
            }
            $_rs['info']['total_pages'] = (int) ceil($_rs['info']['total_items'] / $_params['pagebreak']);
            $_rs['info']['next_page'] = ($_rs['info']['total_pages'] > $_params['page']) ? intval($_params['page'] + 1) : 0;
            $_rs['info']['pagebreak'] = (int) $_params['pagebreak'];
            $_rs['info']['page'] = (int) $_params['page'];
            $_rs['info']['this_page'] = $_params['page'];
            $_rs['info']['prev_page'] = ($_params['page'] > 1) ? intval($_params['page'] - 1) : 0;
            $_rs['info']['page_base_url'] = jrCore_strip_url_params(jrCore_get_current_url(), array('p'));
        }
        else {
            // No items
            return false;
        }
    }
    else {
        // Default limit of 10
        $req .= 'LIMIT 10';
    }

    $beg = explode(' ', microtime());
    $beg = $beg[1] + $beg[0];

    $_rt = jrCore_db_query($req, 'NUMERIC');

    // Check for fdebug logging
    if (isset($_params['fdebug']) && $_params['fdebug']) {
        if (isset($_ct)) {
            fdebug($_params, "(PAGINATION QUERY): {$re2}", $req, $_rs, $_rt); // OK
        }
        else {
            fdebug($_params, $req, $_rs, $_rt); // OK
        }
    }

    $end = explode(' ', microtime());
    $end = $end[1] + $end[0];
    $end = round(($end - $beg), 2);
    if ($end > 2 && isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
        jrCore_logger('MAJ', "Slow jrCore_db_search_items() query: {$end} seconds", $req);
    }

    if (isset($_rt) && is_array($_rt)) {

        // See if we are only providing a count...
        // NOTE: No need for triggers here
        if (isset($_params['return_count']) && $_params['return_count'] !== false) {
            return count($_rt);
        }

        $_id = array();
        foreach ($_rt as $v) {
            $_id[] = $v['_item_id'];
        }

        // We can ask to just get the item_id's for our own use.
        // NOTE: No need for triggers here
        if (isset($_params['return_item_id_only']) && $_params['return_item_id_only'] === true) {
            return $_id;
        }

        $_ky = null;
        if (isset($_params['return_keys']) && is_array($_params['return_keys']) && count($_params['return_keys']) > 0) {
            $_params['return_keys'][] = '_user_id'; // We must include _user_id or jrUser search items trigger does not know the user to include
            $_params['return_keys'][] = '_profile_id'; // We must include _profile_id or jrProfile search items trigger does not know the profile to include
            $_ky = $_params['return_keys'];
        }
        $_rs['_items'] = jrCore_db_get_multiple_items($module, $_id, $_ky);
        if (isset($_rs['_items']) && is_array($_rs['_items'])) {
            // Add in some meta data
            if (!isset($_rs['info']['total_items'])) {
                $_rs['info']['total_items'] = count($_rs['_items']);
            }
            // Trigger search event
            if (!isset($_params['skip_triggers'])) {
                $_rs = jrCore_trigger_event('jrCore', 'db_search_items', $_rs, $_params);
            }

            $_ci = array();
            foreach ($_rs['_items'] as $v) {
                if (isset($v['_profile_id'])) {
                    $_ci["{$v['_profile_id']}"] = $v['_profile_id'];
                }
            }
            jrCore_set_flag('datastore_cache_profile_ids', $_ci);
            unset($_ci);

            // Check for return keys
            if ($_ky) {
                $_ky = array_flip($_ky);
                foreach ($_rs['_items'] as $k => $v) {
                    foreach ($v as $ky => $kv) {
                        if (!isset($_ky[$ky])) {
                            unset($_rs['_items'][$k][$ky]);
                        }
                    }
                }
            }
            $_rs['_params'] = $_backup;
            $_rs['_params']['module'] = $module;
            $_rs['_params']['module_url'] = jrCore_get_module_url($module);
            unset($_params);
            if (!isset($_params['cache_seconds'])) {
                jrCore_add_to_cache($module, $cky, $_rs);
            }
            elseif (jrCore_checktype($_params['cache_seconds'], 'number_nz')) {
                jrCore_add_to_cache($module, $cky, $_rs, $_params['cache_seconds']);
            }
            return $_rs;
        }
    }
    return false;
}

/**
 * Check if a given search operator is valid
 * @param $search string Search Condition
 * @return array
 */
function jrCore_db_check_for_supported_operator($search)
{
    $cd = false;
    list($key, $opt, $val) = explode(' ', trim($search), 3);
    switch (jrCore_str_to_lower($opt)) {
        case '>':
        case '>=':
        case '<':
        case '<=':
            if (strpos($val, '.')) {
                $cd = array($key, $opt, floatval($val));
            }
            else {
                $cd = array($key, $opt, intval($val));
            }
            break;
        case '!=':
        case '=':
        case 'like':
        case 'regexp':
            $cd = array($key, $opt, "'" . jrCore_db_escape($val) . "'");
            break;
        case 'not_like':
            $cd = array($key, 'not like', "'" . jrCore_db_escape($val) . "'");
            break;
        case 'in':
            $_vl = array();
            foreach (explode(',', $val) as $iv) {
                if (ctype_digit($iv)) {
                    $_vl[] = (int) $iv;
                }
                else {
                    $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                }
            }
            $val = "(" . implode(',', $_vl) . ") ";
            $cd  = array($key, 'IN', $val);
            break;
        case 'not_in':
            $_vl = array();
            foreach (explode(',', $val) as $iv) {
                if (ctype_digit($iv)) {
                    $_vl[] = (int) $iv;
                }
                else {
                    $_vl[] = "'" . jrCore_db_escape($iv) . "'";
                }
            }
            $val = "(" . implode(',', $_vl) . ") ";
            $cd  = array($key, 'NOT IN', $val);
            break;
    }
    return $cd;
}
