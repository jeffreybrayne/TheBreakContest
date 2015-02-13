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
 * DataStore unit tests
 */
function test_jrCore_datastore()
{
    global $_user;

    // Init datastore
    jrUnitTest_reset_datastore();

    // Setup
    jrUnitTest_success("creating DataStore (will take 10 seconds)");
    foreach (range(1, 9) as $num) {
        $mod = ($num % 2);
        $_dt = array(
            'ut_num' => $num,
            'ut_title'  => "Object {$num} Title",
            'ut_title2' => "Object {$num} Title2",
            'ut_string' => "Object {$num} String",
            'ut_number' => intval("{$num}0"),
            'ut_float'  => floatval("{$num}.{$num}"),
            'ut_set'    => $mod
        );
        if ($mod == 1) {
            $_dt['ut_one'] = 1;
        }
        if ($num == 2) {
            $_dt['ut_exists'] = 1;
        }
        if ($num == 3) {
            $_dt['ut_exists'] = 2;
        }
        $uid = jrCore_db_create_item('jrUnitTest', $_dt, null, false);
        if (!$uid) {
            jrUnitTest_exit_with_error("unable to create ut item #{$num}");
        }
        sleep(1);
    }

    $nam = 'Retrieve 1 item from DS (title asc)';
    $_sc = array(
        'order_by'      => array('ut_title' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 1 item from DS (title desc)';
    $_sc = array(
        'order_by'      => array('ut_title' => 'desc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '9') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 1 item from DS (number asc)';
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 1 item from DS (number desc)';
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_desc'),
        'skip_triggers' => true,
        'limit'         => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '9') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 5 items from DS (number asc)';
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1' || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != '5') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 5 items from DS (number desc)';
    $_sc = array(
        'order_by'      => array('ut_number' => 'numerical_desc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '9' || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != '5') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 5 items from DS (float asc)';
    $_sc = array(
        'order_by'      => array('ut_float' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1' || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != '5') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 5 items from DS (float desc)';
    $_sc = array(
        'order_by'      => array('ut_float' => 'numerical_desc'),
        'skip_triggers' => true,
        'limit'         => 5
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '9' || !isset($_rt['_items'][4]) || $_rt['_items'][4]['_item_id'] != '5') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 9 items from DS (_item_id desc)';
    $_sc = array(
        'order_by'      => array('_item_id' => 'desc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '9' || !isset($_rt['_items'][8]) || $_rt['_items'][8]['_item_id'] != '1') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Retrieve 9 items from DS (_created desc)';
    $_sc = array(
        'order_by'      => array('_created' => 'desc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '9' || !isset($_rt['_items'][8]) || $_rt['_items'][8]['_item_id'] != '1') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Group by Set title asc (2 sets)';
    $_sc = array(
        'order_by'      => array('ut_title' => 'asc'),
        'group_by'      => 'ut_set',
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) != 2) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Group by Set title desc (2 sets)';
    $_sc = array(
        'order_by'      => array('ut_title' => 'desc'),
        'group_by'      => 'ut_set',
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) != 2) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search Title EQUALS (1 item)';
    $_sc = array(
        'search'        => array('ut_title = Object 5 Title'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '5' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search Title NOT EQUALS (8 items)';
    $_sc = array(
        'search'        => array('ut_title != Object 5 Title'),
        'skip_triggers' => true,
        'limit'         => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 8) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search Title LIKE (1 item)';
    $_sc = array(
        'search'        => array('ut_title like %Object 5 T%'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || $_rt['_items'][0]['_item_id'] != '5' || count($_rt['_items']) > 1) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search Title NOT EQUALS (8 items)';
    $_sc = array(
        'search'        => array('ut_title != Object 5 Title'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 8) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search Title NOT LIKE (8 items)';
    $_sc = array(
        'search'        => array('ut_title not_like %Object 5 T%'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 8) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search _item_id IN (3 items)';
    $_sc = array(
        'search'        => array('_item_id in 1,5,9'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search _item_id IN order_by _item_id (3 items)';
    $_sc = array(
        'search'        => array('_item_id in 1,5,9'),
        'order_by'      => array('_item_id' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1' || $_rt['_items'][2]['_item_id'] != '9') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search _item_id IN order_by _created (3 items)';
    $_sc = array(
        'search'        => array('_item_id in 1,5,9'),
        'order_by'      => array('_item_id' => 'asc'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != '1' || $_rt['_items'][2]['_item_id'] != '9') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search _item_id NOT IN (6 items)';
    $_sc = array(
        'search'        => array('_item_id not_in 1,5,9'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 6) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search _item_id OR EQUALS (3 items)';
    $_sc = array(
        'search'        => array('_item_id = 1 || _item_id = 5 || _item_id = 9'),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search _item_id OR GREATER THAN (5 items)';
    $_sc = array(
        'search'        => array('_item_id = 1 || _item_id > 5 '),
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 5) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search title with pagebreak 3, page 2 (3 items)';
    $_sc = array(
        'search'        => array('ut_title like %Object%'),
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'pagebreak'     => 3,
        'page'          => 2,
        'skip_triggers' => true,
        'limit'         => 9
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '4') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'Search title with limit 3 (3 items)';
    $_sc = array(
        'search'        => array('ut_title like %Object%'),
        'order_by'      => array('ut_number' => 'numerical_asc'),
        'skip_triggers' => true,
        'limit'         => 3
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '1') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'number LESS THAN (3 items)';
    $_sc = array(
        'search'        => array('ut_number < 40'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][2]['_item_id'] != '3') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'number LESS THAN OR EQUAL TO (3 items)';
    $_sc = array(
        'search'        => array('ut_number <= 30'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][2]['_item_id'] != '3') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'number GREATER THAN (3 items)';
    $_sc = array(
        'search'        => array('ut_number > 60'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][2]['_item_id'] != '9') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'number GREATER THAN OR EQUAL TO (3 items)';
    $_sc = array(
        'search'        => array('ut_number >= 70'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3 || $_rt['_items'][2]['_item_id'] != '9') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'title REGEXP (3 items)';
    $_sc = array(
        'search'        => array('ut_title regexp Object [1-3]'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 3) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'order by title RANDOM (pass 1)';
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    $pass_one = json_encode($_rt['_items']);
    jrUnitTest_success("success: {$nam}");

    $nam = 'order by title RANDOM (pass 2)';
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    $pass_two = json_encode($_rt['_items']);
    if ($pass_one == $pass_two) {
        jrUnitTest_exit_with_error("non-random results for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'key DOES NOT EXIST - NOT EQUAL (4 items)';
    $_sc = array(
        'search'        => array('ut_one != 1'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 4) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'key DOES NOT EXIST - NOT EQUAL (9 items)';
    $_sc = array(
        'search'        => array('ut_non_existing != 1', 'ut_non_existing2 != 2'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'key DOES NOT EXIST - NOT LIKE (4 items)';
    $_sc = array(
        'search'        => array('ut_one not_like %1%'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 4) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'key DOES NOT EXIST - NOT LIKE (9 items)';
    $_sc = array(
        'search'        => array('ut_non_existing not_like %1%'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'key DOES NOT EXIST - NOT IN (4 items)';
    $_sc = array(
        'search'        => array('ut_one not_in 1'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 4) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'key DOES NOT EXIST - NOT IN (9 items)';
    $_sc = array(
        'search'        => array('ut_non_existing not_in 1'),
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !isset($_rt['_items'][0]) || !is_array($_rt['_items']) || count($_rt['_items']) !== 9) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'key OR CONDITION with pagebreak (page 2)';
    $_sc = array(
        'search'        => array('ut_title like %Object% || ut_string like %Object%'),
        'pagebreak'     => 3,
        'page'          => 2,
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 3 || $_rt['_items'][0]['_item_id'] != '4') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    unset($pass_one, $pass_two);
    jrUnitTest_success("success: {$nam}");

    $nam = 'ORDER BY key that does not exist in all entries WITHOUT SEARCH';
    $_sc = array(
        'order_by'       => array('ut_exists' => 'numerical_desc'),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'limit'          => 6
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 6 || $_rt['_items'][0]['_item_id'] != '3') {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'ORDER BY RAND with GROUP BY (pass 1)';
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'group_by'       => 'ut_number',
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 2
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    $one = $_rt['_items'][0]['ut_title'];
    $two = $_rt['_items'][1]['ut_title'];
    jrUnitTest_success("success: {$nam}");

    $nam = 'ORDER BY RAND with GROUP BY (pass 2)';
    $_sc = array(
        'order_by'       => array('ut_title' => 'random'),
        'group_by'       => 'ut_number',
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 2
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error("invalid result for: {$nam}");
    }
    if ($_rt['_items'][0]['ut_title'] == $one && $_rt['_items'][1]['ut_title'] == $two) {
        jrUnitTest_exit_with_error("results not random for: {$nam}");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'GROUP BY with UNIQUE';
    $_sc = array(
        'order_by'       => array('_item_id' => 'asc'),
        'group_by'       => 'ut_one UNIQUE',
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 1
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || $_rt['_items'][0]['_item_id'] != 9) {
        jrUnitTest_exit_with_error("invalid result for: {$nam} (" . $_rt['_items'][0]['_item_id'] .")");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'key OR condition on USER keys only';
    $_sc = array(
        'search'         => array(
            "ut_num = 1 || ut_num = 2",
            "user_email = {$_user['user_email']} || user_name like %{$_user['user_name']}%"
        ),
        'order_by'       => array('_item_id' => 'asc'),
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error("invalid result for: {$nam} (" . count($_rt['_items']) . " results instead of 2)");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'key OR condition on PROFILE keys only';
    $_sc = array(
        'search'         => array(
            "ut_num = 2 || ut_num = 5",
            "profile_name = {$_user['profile_name']} || profile_url = {$_user['profile_url']}",
        ),
        'order_by'       => array('_item_id' => 'asc'),
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error("invalid result for: {$nam} (" . count($_rt['_items']) . " results instead of 2)");
    }
    jrUnitTest_success("success: {$nam}");

    $nam = 'key OR condition on USER and PROFILE keys';
    $_sc = array(
        'search'         => array(
            "ut_num = 1 || ut_num = 2",
            "profile_active = 1",
            "user_email = {$_user['user_email']} || user_name like %{$_user['user_name']}%"
        ),
        'order_by'       => array('_item_id' => 'asc'),
        'ignore_pending' => true,
        'no_cache'       => true,
        'limit'          => 10
    );
    $_rt = jrCore_db_search_items('jrUnitTest', $_sc);
    if (!$_rt || !is_array($_rt['_items']) || !isset($_rt['_items'][0]) || count($_rt['_items']) !== 2) {
        jrUnitTest_exit_with_error("invalid result for: {$nam} (" . count($_rt['_items']) . " results instead of 2)");
    }
    jrUnitTest_success("success: {$nam}");

    jrUnitTest_success("all datastore unit tests completed");
}
