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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Cache system unit tests
 */
function test_jrCore_cache()
{
    global $_conf;
    // Make sure we enable caching here for our test
    if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
        $_conf['jrDeveloper_developer_mode'] = 'off';
    }
    if (isset($_conf['jrCore_default_cache_seconds']) && $_conf['jrCore_default_cache_seconds'] == '0') {
        $_conf['jrCore_default_cache_seconds'] = 10;
    }

    $text = 'This is some text to be cached';
    $ckey = md5(microtime());

    $name = 'Set a TEMP Value';
    if (!jrCore_set_temp_value('jrCore', $ckey, $text)) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    jrUnitTest_success("success: {$name}");

    $name = 'Get a TEMP Value';
    if (!$tmp = jrCore_get_temp_value('jrCore', $ckey)) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    else {
        if ($tmp != $text) {
            jrUnitTest_exit_with_error("invalid result for: {$name} (2)");
        }
    }
    jrUnitTest_success("success: {$name}");

    $name = 'Update a TEMP Value';
    if (!jrCore_update_temp_value('jrCore', $ckey, "{$text} modified")) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    jrUnitTest_success("success: {$name}");

    $name = 'Get Updated TEMP Value';
    if (!$tmp = jrCore_get_temp_value('jrCore', $ckey)) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    else {
        if ($tmp != "{$text} modified") {
            jrUnitTest_exit_with_error("invalid result for: {$name} (2)");
        }
    }
    jrUnitTest_success("success: {$name}");

    $name = 'Delete TEMP Value';
    if (!jrCore_delete_temp_value('jrCore', $ckey)) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    jrUnitTest_success("success: {$name}");

    $name = 'Get Deleted TEMP Value';
    if ($tmp = jrCore_get_temp_value('jrCore', $ckey)) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    jrUnitTest_success("success: {$name}");

    $name = 'Save a text item to cache';
    if (!jrCore_add_to_cache('jrCore', $ckey, $text)) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    jrUnitTest_success("success: {$name}");

    $name = 'Retrieve text item just saved to cache';
    if (!$tmp = jrCore_is_cached('jrCore', $ckey)) {
        jrUnitTest_exit_with_error("invalid result for: {$name}");
    }
    else {
        if ($tmp != $text) {
            jrUnitTest_exit_with_error("retrieved cache results does not match: {$name}");
        }
    }
    jrUnitTest_success("success: {$name}");

}
