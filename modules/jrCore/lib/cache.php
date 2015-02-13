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
 * @package Temp and Cache
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Temp: Save a Temp Value to the DB
 *
 * The jrCore_set_temp_value function will store a "value" for a "key"
 * for a given module.  It is guaranteed to NOT conflict with any other
 * module on the system (including the core).  This value can be retrieved
 * at a later point using jrCore_get_temp_value.
 *
 * @subpackage Temp Functions
 *
 * @param string $module Module to store temp value for
 * @param string $key Key unique key for temp value
 * @param mixed $value Value to store - can be string or array
 * @param string $tag Additional tag for temp storage
 *
 * @return mixed Returns value on success, bool false on key does not exist
 */
function jrCore_set_temp_value($module, $key, $value, $tag = 'jrCore')
{
    $tbl = jrCore_db_table_name('jrCore', 'temp');
    $req = "INSERT INTO {$tbl} (temp_module,temp_updated,temp_key,temp_tag,temp_value)
            VALUES ('" . jrCore_db_escape($module) . "',UNIX_TIMESTAMP(),'" . jrCore_db_escape($key) . "','" . jrCore_db_escape($tag) . "','" . jrCore_db_escape(json_encode($value)) . "')";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Temp: Update a Temp Value in the DB
 *
 * The jrCore_update_temp_value function will return a "value" that has been
 * stored in the Temp Table, when given the KEY.  This is guaranteed to
 * be unique to the calling module.
 *
 * @param string $module Module that saved the temp value
 * @param string $key Key existing key to update
 * @param mixed $value New Value to store - can be string or array
 * @param string $tag Additional tag used when value was saved
 *
 * @return mixed Returns value (string or array) on success, bool false on key does not exist
 */
function jrCore_update_temp_value($module, $key, $value, $tag = 'jrCore')
{
    $tbl = jrCore_db_table_name('jrCore', 'temp');
    $req = "UPDATE {$tbl} SET
              temp_updated = UNIX_TIMESTAMP(),
              temp_rand = '" . mt_rand(0, 65535) . "',
              temp_value = '" . jrCore_db_escape(json_encode($value)) . "'
             WHERE temp_module = '" . jrCore_db_escape($module) . "'
               AND temp_key = '" . jrCore_db_escape($key) . "'
               AND temp_tag = '" . jrCore_db_escape($tag) . "'
             LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Temp: Get a Temp Value from the DB
 *
 * The jrCore_get_temp_value function will return a "value" that has been
 * stored in the Temp Table, when given the KEY.  This is guaranteed to
 * be unique to the calling module.
 *
 * @param string $module Module that saved the temp value
 * @param string $key Key to retrieve
 * @param string $tag Additional tag used when value was saved
 *
 * @return mixed Returns value (string or array) on success, bool false on key does not exist
 */
function jrCore_get_temp_value($module, $key, $tag = 'jrCore')
{
    $tbl = jrCore_db_table_name('jrCore', 'temp');
    $req = "SELECT temp_value
              FROM {$tbl}
             WHERE temp_module = '" . jrCore_db_escape($module) . "'
               AND temp_key = '" . jrCore_db_escape($key) . "'
               AND temp_tag = '" . jrCore_db_escape($tag) . "'
             LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt['temp_value']{0})) {
        return json_decode($_rt['temp_value'], true);
    }
    return false;
}

/**
 * Temp: Delete an Existing Temp Value in the DB
 *
 * The jrCore_delete_temp_value function will delete a temp value that was
 * previously set by the jrCore_set_temp_value function.
 *
 * @param string $module Module that saved the temp value
 * @param string $key Key to delete
 * @param string $tag Additional tag used when value was saved
 * @return bool returns true if key is deleted, false if key is not found
 */
function jrCore_delete_temp_value($module, $key, $tag = 'jrCore')
{
    $tbl = jrCore_db_table_name('jrCore', 'temp');
    $req = "DELETE FROM {$tbl}
             WHERE temp_module = '" . jrCore_db_escape($module) . "'
               AND temp_key = '" . jrCore_db_escape($key) . "'
               AND temp_tag = '" . jrCore_db_escape($tag) . "'
             LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Temp: Cleanup old Temp Values in the DB
 *
 * jrCore_clean_temp - delete old temp entries for a module
 *
 * @param string $module Module that saved the temp value
 * @param int $length number of seconds old entry must be
 * @param string $tag Option Module tag
 * @return int Returns number of Temp entries deleted
 */
function jrCore_clean_temp($module, $length, $tag = 'jrCore')
{
    $tbl = jrCore_db_table_name('jrCore', 'temp');
    $req = "DELETE FROM {$tbl}
             WHERE temp_module = '" . jrCore_db_escape($module) . "'
               AND temp_tag = '" . jrCore_db_escape($tag) . "'
               AND temp_updated < (UNIX_TIMESTAMP() - {$length})";
    return jrCore_db_query($req, 'COUNT');
}

//-------------------------------------------
// Cache System
//-------------------------------------------

/**
 * @ignore
 * jrCore_get_active_cache_system
 * @return string
 */
function jrCore_get_active_cache_system()
{
    global $_conf;
    if (isset($_conf['jrCore_active_cache_system']{1})) {
        // Make sure it is valid...
        $func = "_{$_conf['jrCore_active_cache_system']}_is_cached";
        if (function_exists($func)) {
            return $_conf['jrCore_active_cache_system'];
        }
    }
    return 'jrCore_mysql';
}

/**
 * Deletes the core Module and Config cache
 * @return bool
 */
function jrCore_delete_config_cache()
{
    $key = jrCore_get_flag('jrcore_config_and_modules_key');
    return jrCore_delete_cache('jrCore', $key, false);
}

/**
 * Delete all cache entries
 * @param $module string Optionally delete all cache entries for a specific module
 * @param $user_id int Optionally delete all cache entries for specific User ID
 * @return bool
 */
function jrCore_delete_all_cache_entries($module = null, $user_id = null)
{
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_delete_all_cache_entries";
    if (function_exists($func)) {
        return $func($module, $user_id);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Delete all cache entries for a specific profile
 * @param $profile_id int Optionally delete all cache entries for specific User ID
 * @param $module string Optionally delete all cache entries for a specific module
 * @return bool
 */
function jrCore_delete_profile_cache_entries($profile_id = null, $module = null)
{
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_delete_profile_cache_entries";
    if (function_exists($func)) {
        return $func($profile_id, $module);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Cache: Delete cache for a given key
 * @param string $module  Module to save cache for
 * @param string $key Key to save cache for
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @return bool
 */
function jrCore_delete_cache($module, $key, $add_user = true)
{
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_delete_cache";
    if (function_exists($func)) {
        $key = jrCore_get_cache_key($module, $key, $add_user);
        return $func($module, $key, $add_user);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Cache: Delete multiple cache entries in 1 shot
 * @param $_items array consisting of 'module' (0), 'key' (1) and 'add_user' (2) params
 * @return bool
 */
function jrCore_delete_multiple_cache_entries($_items)
{
    if (!is_array($_items) || count($_items) === 0) {
        // Nothing to delete
        return false;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_delete_multiple_cache_entries";
    if (function_exists($func)) {
        foreach ($_items as $k => $i) {
            if (!isset($i[2])) {
                $i[2] = true;
            }
            // 0 = module, 1 = key, 2 = $add_user
            $_items[$k][1] = jrCore_get_cache_key($i[0], $i[1], $i[2]);
        }
        return $func($_items);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Cache: Check if a given key is cached
 * @param string $module Module to save cache for
 * @param string $key Key to save cache for
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @return mixed returns string on success, bool false on not cached
 */
function jrCore_is_cached($module, $key, $add_user = true)
{
    global $_conf;
    // Check to see if we are enabled
    if (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') {
        return false;
    }
    // Check for developer mode
    if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
        return false;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_is_cached";
    if (function_exists($func)) {
        $key = jrCore_get_cache_key($module, $key, $add_user);
        return $func($module, $key, $add_user);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Cache: Cache a string for a given key
 * @param string $module         Module doing the caching
 * @param string $key            Unique key for cache item
 * @param mixed $value          Value to cache
 * @param int $expire_seconds How long key will be cached for (in seconds)
 * @param int $profile_id     Profile ID cache item belongs to
 * @param bool $add_user       By default each cache entry is for a specific User ID - set to false to override
 * @return mixed returns string on success, bool false on not cached
 */
function jrCore_add_to_cache($module, $key, $value, $expire_seconds = 0, $profile_id = 0, $add_user = true)
{
    global $_conf;
    // Check to see if we are enabled
    if (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') {
        return true;
    }
    // Check for developer mode
    if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
        return true;
    }
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_add_to_cache";
    if (function_exists($func)) {
        $uniq = null;
        if ($_ci = jrCore_get_flag('datastore_cache_profile_ids')) {
            $uniq = implode(',', $_ci);
            unset($_ci);
        }
        $key = jrCore_get_cache_key($module, $key, $add_user);
        return $func($module, $key, $value, $expire_seconds, $profile_id, $add_user, $uniq);
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

/**
 * Generate an Internal MD5 Cache key from params
 * @param $module string Module cache key is being created for
 * @param $key string Key to create new key from
 * @param $add_user bool make key unique for calling user
 * @return string
 */
function jrCore_get_cache_key($module, $key, $add_user = true)
{
    global $_conf;
    $key = trim($key);
    // See if we are adding unique User items to the key
    if ($add_user) {

        // Get unique user id
        $uid = (jrUser_is_logged_in()) ? (int) $_SESSION['_user_id'] : 0;

        // Add our language specific modifier in
        $lng = (isset($_SESSION['user_language'])) ? $_SESSION['user_language'] : 'en-US';

        if (!isset($_SESSION['jrdtype']{2})) {
            if (jrCore_is_mobile_device()) {
                $_SESSION['jrdtype'] = 'mobile';
            }
            elseif (jrCore_is_tablet_device()) {
                $_SESSION['jrdtype'] = 'tablet';
            }
            else {
                $_SESSION['jrdtype'] = 'desktop';
            }
        }
        $key = "{$module}-{$lng}-{$key}-{$uid}-{$_SESSION['jrdtype']}";
    }
    return md5(jrCore_get_server_protocol() . "-{$_conf['jrCore_base_url']}-{$_conf['jrCore_active_skin']}-". $key);
}

/**
 * Perform maintenance on the cache system
 * @return bool
 */
function jrCore_cache_maintenance()
{
    $temp = jrCore_get_active_cache_system();
    $func = "_{$temp}_cache_maintenance";
    if (function_exists($func)) {
        return $func();
    }
    jrCore_logger('CRI', "active cache system function: {$func} is not defined");
    return false;
}

//-------------------------------------------
// MySQL Cache Plugins
//-------------------------------------------

/**
 * Internal jrCore_delete_all_cache_entries() plugin
 * @ignore
 * @param $module string Optionally delete all cache entries for specific module
 * @param $user_id int Optionally delete all cache entries for a specific user_id
 * @return bool
 */
function _jrCore_mysql_delete_all_cache_entries($module = null, $user_id = null)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    if (is_null($module) && is_null($user_id)) {
        $req = "TRUNCATE TABLE {$tbl}";
    }
    else {
        $req = "DELETE FROM {$tbl} ";
        if (!is_null($module)) {
            $req .= "WHERE cache_module = '" . jrCore_db_escape($module) . "'";
            if (!is_null($user_id)) {
                $req .= " AND cache_user_id = '" . intval($user_id) . "'";
            }
        }
        else {
            $req .= "WHERE cache_user_id = '" . intval($user_id) . "'";
        }
    }
    jrCore_db_query($req);
    return true;
}

/**
 * Internal jrCore_delete_profile_cache_entries() plugin
 * @ignore
 * @param $profile_id int delete all cache entries for the specified profile_id
 * @param $module string Optionally delete only cache entries for specific module
 * @return bool
 */
function _jrCore_mysql_delete_profile_cache_entries($profile_id = null, $module = null)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    if (!is_null($module)) {
        $req = "DELETE FROM {$tbl} WHERE (cache_profile_id = '{$profile_id}' OR cache_item_id LIKE '%,{$profile_id},%') AND cache_module = '" . jrCore_db_escape($module) . "'";
    }
    else {
        $req = "DELETE FROM {$tbl} WHERE (cache_profile_id = '{$profile_id}' OR cache_item_id LIKE '%,{$profile_id},%')";
    }
    jrCore_db_query($req);
    return true;
}

/**
 * Cache: Delete cache for a given key
 * @param string $module  Module to save cache for
 * @param string $key Key to save cache for
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @return bool
 */
function _jrCore_mysql_delete_cache($module, $key, $add_user = true)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $req = "DELETE FROM {$tbl} WHERE cache_key = '{$key}'";
    jrCore_db_query($req);
    return true;
}

/**
 * Cache: Delete cache for multiple module/keys
 * @param array $_items Cache Items to delete
 * @return bool
 */
function _jrCore_mysql_delete_multiple_cache_entries($_items)
{
    $_ky = array();
    foreach ($_items as $v) {
        $_ky[] = "(cache_module = '". jrCore_db_escape($v[0]) ."' AND cache_key = '". $v[1] ."')";
    }
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $req = "DELETE FROM {$tbl} WHERE " . implode(' OR ', $_ky);
    jrCore_db_query($req);
    return true;
}

/**
 * Cache: Check if a given key is cached
 * @param string $module Module to save cache for
 * @param string $key Key to save cache for
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @return mixed returns string on success, bool false on not cached
 */
function _jrCore_mysql_is_cached($module, $key, $add_user = true)
{
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $req = "SELECT cache_expires, cache_encoded, cache_value FROM {$tbl} WHERE cache_key = '{$key}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt['cache_value'])) {
        // See if we have expired...
        if (isset($_rt['cache_expires']) && $_rt['cache_expires'] < time()) {
            // return false so we rebuild
            return false;
        }
        switch ($_rt['cache_encoded']) {
            case '1':
                // Array
                $_rt['cache_value'] = json_decode($_rt['cache_value'], true);
                break;
            case '2':
                // Object
                $_rt['cache_value'] = json_decode($_rt['cache_value']);
                break;
        }
        return $_rt['cache_value'];
    }
    return false;
}

/**
 * MySQL Cache Maintenance function
 */
function _jrCore_mysql_cache_maintenance()
{
    // Delete expired cache entries
    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $req = "DELETE FROM {$tbl} WHERE cache_expires < " . time();
    jrCore_db_query($req);
}

/**
 * Cache: Cache a string for a given key
 * @param string $module Module doing the caching
 * @param string $key Unique key for cache item
 * @param mixed $value Value to cache
 * @param int $expire_seconds How long key will be cached for (in seconds)
 * @param int $profile_id Profile ID cache item belongs to
 * @param bool $add_user By default each cache entry is for a specific User ID - set to false to override
 * @param string $unique Unique Module-Item_IDs (set in DataStore)
 * @return mixed returns string on success, bool false on not cached
 */
function _jrCore_mysql_add_to_cache($module, $key, $value, $expire_seconds = 0, $profile_id = 0, $add_user = true, $unique = null)
{
    global $_post, $_conf;
    if (!$expire_seconds || $expire_seconds === 0) {
        $expire_seconds = $_conf['jrCore_default_cache_seconds'];
    }
    $expire_seconds = intval($expire_seconds);
    if (isset($expire_seconds) && $expire_seconds === 0) {
        return true;
    }

    $tbl = jrCore_db_table_name('jrCore', 'cache');
    $tim = (time() + $expire_seconds);
    // Check if we are encoding this in the DB
    $enc = 0;
    if (isset($value) && is_array($value)) {
        $value = json_encode($value);
        $enc = 1;
    }
    elseif (isset($value) && is_object($value)) {
        $value = json_encode($value);
        $enc = 2;
    }
    $unq = '';
    if (isset($unique) && strlen($unique) > 0) {
        $unq = jrCore_db_escape(",{$unique},");
    }
    if ($profile_id === 0 && isset($_post['_profile_id'])) {
        $pid = (int) $_post['_profile_id'];
    }
    else {
        $pid = (int) $profile_id;
    }
    $uid = (isset($_SESSION['_user_id'])) ? (int) $_SESSION['_user_id'] : 0;
    $req = "INSERT INTO {$tbl} (cache_key,cache_expires,cache_module,cache_profile_id,cache_user_id,cache_item_id,cache_encoded,cache_value)
            VALUES ('{$key}','{$tim}','{$module}','{$pid}','{$uid}','{$unq}','{$enc}','" . jrCore_db_escape($value) . "')
            ON DUPLICATE KEY UPDATE cache_expires = '{$tim}', cache_encoded = '{$enc}', cache_value = '" . jrCore_db_escape($value) . "', cache_item_id = '{$unq}'";
    $cnt = jrCore_db_query($req, 'COUNT', false, null, false);
    if ($cnt < 1 || $cnt > 2) {
        return false;
    }
    return true;
}
