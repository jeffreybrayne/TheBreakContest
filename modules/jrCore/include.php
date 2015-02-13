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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Information about the Jamroom Core
 * @return array
 */
function jrCore_meta()
{
    $_tmp = array(
        'name'        => 'System Core',
        'url'         => 'core',
        'version'     => '5.2.22',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Provides low level functionality for all system operations',
        'category'    => 'core',
        'license'     => 'mpl',
        'locked'      => true,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * Core Initialization
 * @return bool
 */
function jrCore_init()
{
    global $_conf, $_urls, $_mods;

    ob_start();
    if (function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
    }
    else {
        jrCore_notice('CRI', 'Required PHP Multibyte String function (mb_internal_encoding) not found - enable in PHPcoding)');
    }

    // Some core config
    $_conf['jrCore_base_url'] = (isset($_conf['jrCore_base_url']{0})) ? $_conf['jrCore_base_url'] : jrCore_get_base_url();
    $_conf['jrCore_base_dir'] = APP_DIR;

    // Bring in local config
    if (!@include_once APP_DIR . '/data/config/config.php') {
        header("Location: {$_conf['jrCore_base_url']}/install.php");
        exit;
    }

    // Check for SSL...
    if (strpos($_conf['jrCore_base_url'], 'http:') === 0 && !empty($_SERVER['HTTPS'])) {
        $_conf['jrCore_base_url'] = 'https://' . substr($_conf['jrCore_base_url'], 7);
    }

    // Core magic views
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'admin', 'view_jrCore_admin');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'admin_save', 'view_jrCore_admin_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'skin_admin', 'view_jrCore_skin_admin');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'skin_admin_save', 'view_jrCore_skin_admin_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'stream', 'view_jrCore_stream_file');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'download', 'view_jrCore_download_file');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_compare', 'view_jrCore_template_compare');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_compare_save', 'view_jrCore_template_compare_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_modify', 'view_jrCore_template_modify');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'template_modify_save', 'view_jrCore_template_modify_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser', 'view_jrCore_browser');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser_item_update', 'view_jrCore_browser_item_update');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser_item_update_save', 'view_jrCore_browser_item_update_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'browser_item_delete', 'view_jrCore_browser_item_delete');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'license', 'view_jrCore_license');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_designer', 'view_jrCore_form_designer');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_designer_save', 'view_jrCore_form_designer_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_field_update', 'view_jrCore_form_field_update');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'form_field_update_save', 'view_jrCore_form_field_update_save');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'dashboard', 'view_jrCore_dashboard');
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrCore', 'item_display_order', 'view_jrCore_item_display_order');

    // Core tool views
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'activity_log', array('Activity Logs', 'Browse the system Activity, Debug and Error Logs'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'cache_reset', array('Reset Caches', 'Reset database and filesystem caches'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'integrity_check', array('Integrity Check', 'Validate, Optimize and Repair module and skin installs'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'skin_menu', array('Skin Menu Editor', 'Customize the items and options that appear in the main Skin Menu'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'system_check', array('System Check', 'Display information about your System and installed modules'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'performance_check', array('Performance Check', 'Run a performance test on your server and optionally share the results'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'queue_view', array('Queue Viewer', 'View active worker queue information'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCore', 'module_detail_features', array('Item Detail Features', 'Set the Order of Item Detail Features provided by modules'));

    // Our default view for admins
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrCore', 'activity_log');

    // Core checktype plugins
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'allowed_html');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'core_string');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'user_name');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'date');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'domain');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'email');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'float');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'hex');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'ip_address');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'is_true');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'md5');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'multi_word');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'not_empty');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'signed');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'number');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'number_nn');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'number_nz');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'onoff');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'price');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'printable');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'sha1');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'string');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'url');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'url_name');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'file_name');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'yesno');
    jrCore_register_module_feature('jrCore', 'checktype', 'jrCore', 'json');

    // Core form fields supported
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'hidden');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'checkbox');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'checkbox_spambot');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'date');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'datetime');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'file');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'editor');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'optionlist');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'password');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'radio');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'select');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'select_and_text');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'select_multiple');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'text');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'textarea');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'custom');
    jrCore_register_module_feature('jrCore', 'form_field', 'jrCore', 'live_search');

    jrCore_register_module_feature('jrTips', 'tip', 'jrCore', 'tip');

    // Bring in core javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jquery-1.11.1.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jquery.simplemodal.1.4.4.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'lightbox-2.6.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jquery.livesearch.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'fileuploader.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', APP_DIR . "/modules/jrCore/contrib/jplayer/jquery.jplayer.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', APP_DIR . "/modules/jrCore/contrib/jplayer/jquery.jplayer.playlist.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', "jquery.sortable.min.js");
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCore', 'jrCore.js');

    // When javascript is registered, we have a function that is called
    jrCore_register_module_feature_function('jrCore', 'javascript', 'jrCore_enable_external_javascript');

    // Register our core CSS
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jrCore.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jrCore_tinymce.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jrCore_dashboard.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'fileuploader.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jquery.lightbox.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'lightbox.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrCore', 'jquery.livesearch.css');

    // When CSS is registered, we have a function that is called
    jrCore_register_module_feature_function('jrCore', 'css', 'jrCore_enable_external_css');

    // We have some core string formatting functions
    $_tmp = array(
        'wl'    => 'html',
        'label' => 'Allow HTML',
        'help'  => 'If active, any HTML tags defined in the Allowed HTML Tags setting will be allowed in the text.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrCore', 'jrCore_format_string_allowed_html', $_tmp);
    $_tmp = array(
        'wl'    => 'at_tags',
        'label' => 'Convert @ Tags',
        'help'  => 'If active, links to User Profiles written as @profile_name will be linked to the actual User Profile.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrCore', 'jrCore_format_string_convert_at_tags', $_tmp);
    $_tmp = array(
        'wl'    => 'click_urls',
        'label' => 'Make URLs Clickable',
        'help'  => 'If active, URLs entered into the text will be hyperlinked so they are clickable.'
    );
    jrCore_register_module_feature('jrCore', 'format_string', 'jrCore', 'jrCore_format_string_clickable_urls', $_tmp);

    // We don't need sessions on a couple views
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'css');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'icon_css');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrCore', 'icon_sprite');

    // No Play Key replacement on some views
    jrCore_register_module_feature('jrCore', 'skip_play_keys', 'jrCore', 'template_compare', 'magic_view');
    jrCore_register_module_feature('jrCore', 'skip_play_keys', 'jrCore', 'template_modify', 'magic_view');

    // Core plugins
    jrCore_register_system_plugin('jrCore', 'email', 'debug', 'Log Sent Email to debug log');
    jrCore_register_system_plugin('jrCore', 'media', 'local', 'Local File System');

    // Core event triggers
    jrCore_register_event_trigger('jrCore', 'allowed_html_tags', 'Fired when validating posted HTML in the editor');
    jrCore_register_event_trigger('jrCore', 'approve_pending_item', 'Fired when a pending item is approved by an admin');
    jrCore_register_event_trigger('jrCore', 'daily_maintenance', 'Fired once a day after midnight server time');
    jrCore_register_event_trigger('jrCore', 'db_create_datastore', 'Fired when a new DataStore is initialized - check module');
    jrCore_register_event_trigger('jrCore', 'db_create_item', 'Fired when creating a new DataStore item - check module');
    jrCore_register_event_trigger('jrCore', 'db_update_item', 'Fired when a DataStore item is updated - check module');
    jrCore_register_event_trigger('jrCore', 'db_get_item', 'Fired when a DataStore item is retrieved - check module');
    jrCore_register_event_trigger('jrCore', 'db_delete_item', 'Fired when a DataStore item is deleted - check module');
    jrCore_register_event_trigger('jrCore', 'db_delete_keys', 'Fired when specific keys are deleted from a DataStore item - check module');
    jrCore_register_event_trigger('jrCore', 'db_search_items', 'Fired with an array of DataStore items that matched the search criteria  - check _items array');
    jrCore_register_event_trigger('jrCore', 'db_search_params', 'Fired when doing a DataStore search for search params');
    jrCore_register_event_trigger('jrCore', 'db_query_init', 'Fired in jrCore_db_query() with query to be run');
    jrCore_register_event_trigger('jrCore', 'db_query_exit', 'Fired in jrCore_db_query() with query results');
    jrCore_register_event_trigger('jrCore', 'display_order', 'Fired with entries during the display_order magic view');
    jrCore_register_event_trigger('jrCore', 'download_file', 'Fired when a DataStore file is downloaded');
    jrCore_register_event_trigger('jrCore', 'form_validate_init', 'Fired at the beginning of jrCore_form_validate()');
    jrCore_register_event_trigger('jrCore', 'form_validate_exit', 'Fired at the end of jrCore_form_validate()');
    jrCore_register_event_trigger('jrCore', 'form_field_create', 'Fired when a form_field is added to a form session');
    jrCore_register_event_trigger('jrCore', 'form_display', 'Fired when a form is displayed (receives form data)');
    jrCore_register_event_trigger('jrCore', 'form_result', 'Fired when a form target view has completed');
    jrCore_register_event_trigger('jrCore', 'get_save_data', 'Fired on exit of jrCore_form_get_save_data()');
    jrCore_register_event_trigger('jrCore', 'html_purifier', 'Fired during HTMLPurifier config setup');
    jrCore_register_event_trigger('jrCore', 'index_template', 'Fired when the skin index template is displayed');
    jrCore_register_event_trigger('jrCore', 'log_message', 'Fired when a message is logged to the Activity Log');
    jrCore_register_event_trigger('jrCore', 'media_playlist', 'Fired when a playlist is assembled in {jrCore_media_player}');
    jrCore_register_event_trigger('jrCore', 'module_view', 'Fired when a module view is going to be processed');
    jrCore_register_event_trigger('jrCore', 'parse_url', 'Fired when the current URL has been parsed into $_url');
    jrCore_register_event_trigger('jrCore', 'process_init', 'Fired when the core has initialized');
    jrCore_register_event_trigger('jrCore', 'process_exit', 'Fired when process exits');
    jrCore_register_event_trigger('jrCore', 'profile_template', 'Fired when a profile template is displayed');
    jrCore_register_event_trigger('jrCore', 'run_view_function', 'Fired before a view function is run for a module');
    jrCore_register_event_trigger('jrCore', 'save_media_file', 'Fired when a media file has been saved for a profile');
    jrCore_register_event_trigger('jrCore', 'skin_template', 'Fired when a skin template is displayed');
    jrCore_register_event_trigger('jrCore', 'stream_file', 'Fired when a DataStore file is streamed');
    jrCore_register_event_trigger('jrCore', 'stream_url_error', 'Fired when Media Player encounters a URL error');
    jrCore_register_event_trigger('jrCore', 'system_check', 'Fired in System Check so modules can run own checks');
    jrCore_register_event_trigger('jrCore', 'template_cache_reset', 'Fired when Reset Template Cache is fired');
    jrCore_register_event_trigger('jrCore', 'template_variables', 'Fired for replacement variables when parsing a template');
    jrCore_register_event_trigger('jrCore', 'template_file', 'Fired with template file info when a template is being parsed');
    jrCore_register_event_trigger('jrCore', 'verify_module', 'Fired when a module is verified during the Integrity Check');
    jrCore_register_event_trigger('jrCore', 'view_results', 'Fired when results from a module view are displayed');
    jrCore_register_event_trigger('jrCore', '404_not_found', 'Fired when a URL results in a 404 not found.');
    jrCore_register_event_trigger('jrCore', 'tpl_404', 'Fired when a template can not be found.');
    jrCore_register_event_trigger('jrCore', 'create_queue_entry', 'Fired when a process tries to create a queue entry');
    jrCore_register_event_trigger('jrCore', 'queue_entry_created', 'Fired after a queue entry has been created with queue_id');
    jrCore_register_event_trigger('jrCore', 'get_queue_entry', 'Fired when a worker tries to get a queue entry');
    jrCore_register_event_trigger('jrCore', 'release_queue_entry', 'Fired when a worker releases a queue entry back to the stack');
    jrCore_register_event_trigger('jrCore', 'sleep_queue_entry', 'Fired when a worker adjust the sleep of an existing queue entry');
    jrCore_register_event_trigger('jrCore', 'delete_queue_entry', 'Fired when a worker tries to delete a queue entry');
    jrCore_register_event_trigger('jrCore', 'delete_queue_by_item_id', 'Fired when deleting a queue by module/item_id');
    jrCore_register_event_trigger('jrCore', 'get_queue_info', 'Fired in the Queue Viewer tool to get queue info');
    jrCore_register_event_trigger('jrCore', 'check_queues_ready', 'Fired when checking Queue State');
    jrCore_register_event_trigger('jrCore', 'set_queue_status', 'Fired when setting Queue Status');
    jrCore_register_event_trigger('jrCore', 'get_queue_worker_count', 'Fired when getting number of queue workers for a queue');
    jrCore_register_event_trigger('jrCore', 'all_events', 'Fired when any other event trigger is fired');

    // If the tracer module is installed, we have a few events for it
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrCore', 'download_file', 'A user downloads a file');
    jrCore_register_module_feature('jrTrace', 'trace_event', 'jrCore', 'stream_file', 'A user streams a file');

    // Set core directory and file permissions
    if (!isset($_conf['jrCore_dir_perms'])) {
        $umask = (int) sprintf('%03o', umask());
        $_conf['jrCore_dir_perms'] = octdec(0 . (777 - $umask));
        $_conf['jrCore_file_perms'] = octdec(0 . (666 - $umask));
    }

    // Check for install routine
    if (defined('IN_JAMROOM_INSTALLER')) {
        return true;
    }

    // We have to set a default cache seconds here as $_conf is NOT loaded yet!
    $_conf['jrCore_default_cache_seconds'] = 3600;

    // See if our master config has defined the active skin - it needs to be part of our cache key if it has
    $key = 'jrcore_config_and_modules';
    if (isset($_conf['jrCore_active_skin']{1})) {
        $key = 'jrcore_config_and_modules_' . $_conf['jrCore_active_skin'];
    }
    jrCore_set_flag('jrcore_config_and_modules_key', $key);
    jrCore_set_flag('jrcore_in_module_init', 1);
    $_rt = jrCore_is_cached('jrCore', $key, false);
    if (!$_rt) {

        // Get modules
        $tbl   = jrCore_db_table_name('jrCore', 'module');
        $req   = "SELECT * FROM {$tbl} ORDER BY module_priority ASC";
        $_mods = jrCore_db_query($req, 'module_directory');
        if (!is_array($_mods)) {
            jrCore_notice('CRI', "unable to initialize modules - verify installation");
        }

        // Get settings
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        if (isset($_conf['jrCore_active_skin']{1})) {
            $add = "`module` = '{$_conf['jrCore_active_skin']}'";
        }
        else {
            $add = "`module` = (SELECT `value` FROM {$tbl} WHERE `module` = 'jrCore' AND `name` = 'active_skin')";
        }
        $req = "SELECT CONCAT_WS('_', `module`, `name`) AS k, `value` AS v FROM {$tbl} WHERE (`module` IN('" . implode("','", array_keys($_mods)) ."') OR {$add})";
        $_cf = jrCore_db_query($req, 'k', false, 'v');
        if (!is_array($_cf)) {
            jrCore_notice('CRI', "unable to initialize settings - verify installation");
        }
        $_conf = array_merge($_cf, $_conf);
        $_conf['jrCore_default_cache_seconds'] = $_cf['jrCore_default_cache_seconds'];
        unset($_cf);

        $_ina = array();
        foreach ($_mods as $k => $v) {
            $_urls["{$v['module_url']}"] = $k;
            if ($k != 'jrCore') {
                // jrCore is already included ;)
                // If this module is NOT active, we add it to our inactive list of modules
                // so we can check in the next loop down any module dependencies
                if ($v['module_active'] != '1') {
                    $_ina[$k] = 1;
                }
                else {
                    // NOTE: error redirect here for users that simply try to delete a module
                    // by removing the module directory BEFORE removing the module from the DB!
                    if ((@include_once APP_DIR . "/modules/{$k}/include.php") === false) {
                        // Bad module
                        unset($_mods[$k], $_urls["{$v['module_url']}"]);
                    }
                }
            }
        }

        // init active modules
        foreach ($_mods as $k => $v) {
            if ($k != 'jrCore' && $v['module_active'] == '1') {
                if (isset($v['requires']{0})) {
                    // We have a module that depends on another module to be active
                    foreach (explode(',', trim($v['requires'])) as $req_mod) {
                        if (isset($_ina[$req_mod])) {
                            continue 2;
                        }
                    }
                }
                $func = "{$k}_init";
                if (function_exists($func)) {
                    $func();
                }
                $_mods[$k]['module_initialized'] = 1;
            }
        }
        unset($_ina);

        $_rt = array(
            '_conf' => $_conf,
            '_mods' => $_mods,
            '_urls' => $_urls
        );
        jrCore_add_to_cache('jrCore', $key, json_encode($_rt), 0, 0, false);
    }
    else {
        // We are cached
        $_rt = json_decode($_rt, true);
        $_conf = $_rt['_conf'];
        $_mods = $_rt['_mods'];
        $_urls = $_rt['_urls'];
        // Module setup
        foreach ($_mods as $_md) {
            if ($_md['module_directory'] != 'jrCore') {
                // jrCore is already included ;)
                // NOTE: error redirect here for users that simply try to delete a module
                // by removing the module directory BEFORE removing the module from the DB!
                @include APP_DIR . "/modules/{$_md['module_directory']}/include.php";
            }
        }
        // .. and init
        foreach ($_mods as $k => $_md) {
            if ($_md['module_directory'] != 'jrCore' && $_md['module_active'] == '1') {
                $func = "{$_md['module_directory']}_init";
                if (function_exists($func)) {
                    $func();
                }
                $_mods[$k]['module_initialized'] = 1;
            }
        }
    }
    jrCore_delete_flag('jrcore_in_module_init');

    // Set our timezone...
    date_default_timezone_set($_conf['jrCore_system_timezone']);

    // Initialize active skin...
    $func = "{$_conf['jrCore_active_skin']}_skin_init";
    if (!function_exists($func)) {
        require APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/include.php";
        if (function_exists($func)) {
            $func();
        }
    }
    ob_end_clean();

    // Core event listeners - must come after $_mods
    jrCore_register_event_listener('jrCore', 'view_results', 'jrCore_view_results_listener');
    jrCore_register_event_listener('jrCore', 'process_exit', 'jrCore_process_exit_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrCore_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrCore_daily_maintenance_listener');

    $_tmp = array(
        'label' => 'pending item',
        'help'  => 'When a new Item is created and is pending review, how do you want to be notified?',
        'group' => 'admin'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrCore', 'pending_item', $_tmp);

    // Core item buttons
    $_tmp = array(
        'title'  => 'item order button',
        'icon'   => 'refresh',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrCore', 'jrCore_item_order_button', $_tmp);

    $_tmp = array(
        'title'  => 'item create button',
        'icon'   => 'plus',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrCore', 'jrCore_item_create_button', $_tmp);
    $_tmp['active'] = 'off';
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrCore', 'jrCore_item_create_button', $_tmp);

    $_tmp = array(
        'title'  => 'item update button',
        'icon'   => 'gear',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrCore', 'jrCore_item_update_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrCore', 'jrCore_item_update_button', $_tmp);

    $_tmp = array(
        'title'  => 'item delete button',
        'icon'   => 'trash',
        'active' => 'on',
        'group'  => 'owner'
    );
    jrCore_register_module_feature('jrCore', 'item_list_button', 'jrCore', 'jrCore_item_delete_button', $_tmp);
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrCore', 'jrCore_item_delete_button', $_tmp);

    // We provide some dashboard panels
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'queue depth', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'memory used', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'disk usage', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'CPU count', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'installed modules', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'installed skins', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', '1 minute load', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', '5 minute load', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', '15 minute load', 'jrCore_dashboard_panels');
    jrCore_register_module_feature('jrCore', 'dashboard_panel', 'jrCore', 'pending items', 'jrCore_dashboard_panels');

    // We run the core email queue
    if (isset($_conf['jrMailer_throttle']) && $_conf['jrMailer_throttle'] > 0) {
        jrCore_register_queue_worker('jrCore', 'send_email', 'jrCore_send_email_queue_worker', intval($_conf['jrMailer_throttle']), 1);
    }
    else {
        jrCore_register_queue_worker('jrCore', 'send_email', 'jrCore_send_email_queue_worker', 0, 4);
    }

    // Trigger our process_init event
    jrCore_trigger_event('jrCore', 'process_init', array());
    return true;
}

// Include Library
require APP_DIR . '/modules/jrCore/lib/mysql.php';
require APP_DIR . '/modules/jrCore/lib/datastore.php';
require APP_DIR . '/modules/jrCore/lib/module.php';
require APP_DIR . '/modules/jrCore/lib/media.php';
require APP_DIR . '/modules/jrCore/lib/checktype.php';
require APP_DIR . '/modules/jrCore/lib/smarty.php';
require APP_DIR . '/modules/jrCore/lib/cache.php';
require APP_DIR . '/modules/jrCore/lib/page.php';
require APP_DIR . '/modules/jrCore/lib/form.php';
require APP_DIR . '/modules/jrCore/lib/skin.php';
require APP_DIR . '/modules/jrCore/lib/util.php';
require APP_DIR . '/modules/jrCore/lib/misc.php';

//---------------------------------------------------------
// DASHBOARD
//---------------------------------------------------------

/**
 * User Profiles Dashboard Panels
 * @param $panel
 * @return bool|int
 */
function jrCore_dashboard_panels($panel)
{
    global $_mods;
    // The panel being asked for will come in as $panel
    $out = false;
    switch ($panel) {

        case 'pending items':
            $out = array(
                'title' => number_format(jrCore_db_number_rows('jrCore', 'pending'))
            );
            break;

        case 'installed modules':
            $out = array(
                'title' => count($_mods)
            );
            break;

        case 'installed skins':
            $out = array(
                'title' => count(jrCore_get_skins())
            );
            break;

        case 'queue depth':
            $out = array(
                'title' => number_format(jrCore_db_number_rows('jrCore', 'queue'))
            );
            break;

        case 'memory used':
            $_rm = jrCore_get_system_memory();
            if (isset($_rm['percent_used']) && is_numeric($_rm['percent_used'])) {
                $out = array(
                    'title' => $_rm['percent_used'] . "%<br><span>" . jrCore_format_size($_rm['memory_used'])  . " of " . jrCore_format_size($_rm['memory_total']) .'</span>',
                    'class' => (isset($_rm['class']) ? $_rm['class'] : 'bigsystem-inf')
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        case 'disk usage':
            $_ds = jrCore_get_disk_usage();
            if (isset($_ds['percent_used']) && is_numeric($_ds['percent_used'])) {
                $out = array(
                    'title' => $_ds['percent_used'] . "%<br><span>" . jrCore_format_size($_ds['disk_used']) . " of " . jrCore_format_size($_ds['disk_total']) .'</span>',
                    'class' => (isset($_ds['class']) ? $_ds['class'] : 'bigsystem-inf')
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        case 'CPU count':
            $_pc = jrCore_get_proc_info();
            if (isset($_pc) && is_array($_pc)) {
                $num = count($_pc);
                jrCore_set_flag('jrCore_dashboard_cpu_num', $num);
                $out = array(
                    'title' => "{$num}<span>@ {$_pc[1]['mhz']}</span>",
                    'class' => 'bigsystem-inf'
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        case '1 minute load':
        case '5 minute load':
        case '15 minute load':
            $min = (int) jrCore_string_field($panel, 1);
            if (!$num = jrCore_get_flag('jrCore_dashboard_cpu_num')) {
                $num = jrCore_get_proc_info();
                if ($num && is_array($num)) {
                    $num = count($num);
                }
            }
            $_ll = jrCore_get_system_load($num);
            if (isset($_ll) && is_array($_ll)) {
                $out = array(
                    'title' => "{$_ll[$min]['level']}<br><span>{$_ll[1]['level']}, {$_ll[5]['level']}, {$_ll[15]['level']}</span>",
                    'class' => $_ll[$min]['class']
                );
            }
            else {
                $out = array(
                    'title' => '?',
                    'class' => 'bigsystem-inf'
                );
            }
            break;

        default:

            // All other "DS" Counts
            if (strpos($panel, 'item count')) {
                $mod = trim(jrCore_string_field($panel, 1));
                $out = array(
                    'title' => jrCore_db_get_datastore_item_count($mod),
                    'graph' => "{$mod}|ds_items_by_day"
                );
            }
            break;

    }
    return ($out) ? $out : false;
}

//---------------------------------------------------------
// ITEM BUTTONS
//---------------------------------------------------------

/**
 * Return "order" button for item index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_order_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // See if this module has registered for item order support
    $_tm = jrCore_get_registered_module_features('jrCore', 'item_order_support');
    if (!isset($_tm[$module])) {
        return false;
    }
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    return smarty_function_jrCore_item_order_button($_args, $smarty);
}

/**
 * Return "create" button for an item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_create_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module']     = $module;
    if (!isset($_args['profile_id'])) {
        $_args['profile_id'] = $_item['_profile_id'];
    }
    return smarty_function_jrCore_item_create_button($_args, $smarty);
}

/**
 * Return "update" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_update_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module']     = $module;
    $_args['profile_id'] = $_item['_profile_id'];
    $_args['item_id']    = $_item['_item_id'];
    return smarty_function_jrCore_item_update_button($_args, $smarty);
}

/**
 * Return "delete" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_delete_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module']     = $module;
    $_args['profile_id'] = $_item['_profile_id'];
    $_args['item_id']    = $_item['_item_id'];
    return smarty_function_jrCore_item_delete_button($_args, $smarty);
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Verify Module items
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Make sure our tools are executable
    foreach (array('diff', 'ffmpeg') as $file) {
        $file = APP_DIR ."/modules/jrCore/tools/{$file}";
        if (is_file($file) && !is_executable($file)) {
            @chmod($file, 0755);
        }
    }
    return $_data;
}

/**
 * Set Media Play Keys in HTML pages
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    $_SESSION['session_updated'] = time();  // Updates user location
    return jrCore_media_set_play_key($_data);
}

/**
 * Run on process exit and used for cleanup/inserting
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_process_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // Our core process exit listener handles core level cleanup
    // and tasks that should happen after a process shutdown
    // NOTE: The Client has disconnected at this point!
    if (jrCore_is_view_request()) {

        // Check for daily maintenance run
        $now = (time() + date_offset_get(new DateTime));
        $now = gmstrftime('%Y%m%d', $now);
        if (!isset($_conf['jrCore_last_daily_maint_run']) || $_conf['jrCore_last_daily_maint_run'] < $now) {

            // Time to run Maintenance - we use the Play Key table for our daily lock
            $tbl = jrCore_db_table_name('jrCore', 'play_key');
            $req = "INSERT IGNORE INTO {$tbl} (key_time, key_code) VALUES (UNIX_TIMESTAMP(), 'MNT{$now}')";
            if (jrCore_db_query($req, 'INSERT_ID') > 0) {

                jrCore_set_temp_value('jrCore', 'daily_maintenance_active', $now);
                ini_set('max_execution_time', 3600);

                jrCore_set_flag('jr_daily_maintenance_is_active', 1);
                jrCore_logger('INF', 'daily_maintenance started', null, false);

                jrCore_set_setting_value('jrCore', 'last_daily_maint_run', $now);
                jrCore_delete_all_cache_entries('jrCore', 0);
                jrCore_trigger_event('jrCore', 'daily_maintenance', $_post);

                // Cleanup
                $req = "DELETE FROM {$tbl} WHERE key_code = 'MNT{$now}'";
                jrCore_db_query($req);
                jrCore_logger('INF', 'daily_maintenance completed', null, false);

            }
        }

        // Cleanup hit counter ip table (5% chance)
        if (mt_rand(1, 20) === 3) {
            $tbl = jrCore_db_table_name('jrCore', 'count_ip');
            $req = "DELETE FROM {$tbl} WHERE count_time < (UNIX_TIMESTAMP() - 86400)";
            jrCore_db_query($req, 'COUNT');
        }

        // Cleanup old form sessions (older than 8 hours) - 5% chance
        if (mt_rand(1, 20) === 4) {
            $old = (time() - 28800);
            $tbl = jrCore_db_table_name('jrCore', 'form_session');
            $req = "DELETE FROM {$tbl} WHERE form_updated > 0 AND form_updated < {$old}";
            jrCore_db_query($req);
        }

        // Run cache maintenance (5% chance)
        if (mt_rand(1, 20) === 5) {
            jrCore_cache_maintenance();
        }

        // Check for Queue Workers
        if (!isset($_conf['jrCore_queues_active']) || $_conf['jrCore_queues_active'] == 'on') {

            if ($_tmp = jrCore_get_flag('jrcore_register_queue_worker')) {

                // see if we have any queue entries
                if ($_qn = jrCore_queues_are_ready()) {

                    // Conversions and other queue-based work can take a long time to run
                    set_time_limit(0);

                    foreach ($_tmp as $mod => $_queue) {
                        foreach ($_queue as $qname => $qdat) {

                            // Only process entries for queue we actually have
                            if (!isset($_qn[$qname])) {
                                continue;
                            }

                            $func = $qdat[0]; // Queue Function that is going to be run
                            $qcnt = intval($qdat[1]); // Number of Queue Entries to process before exiting (set to 0 for worker to process all queue entries)
                            if (!function_exists($func)) {
                                jrCore_logger('MAJ', "registered queue worker function: {$func} for module: {$mod} does not exist");
                                continue;
                            }
                            // See if we have a queue entry
                            if ($qcnt === 0) {
                                $qcnt = 1000000; // high enough for one process
                            }
                            while ($qcnt > 0) {
                                if (jrCore_queues_are_active()) {
                                    // Maximum number of workers
                                    $maxw = (isset($qdat[2])) ? $qdat[2] : 1;
                                    // Worker Timeout
                                    $tout = (isset($qdat[3]) && is_numeric($qdat[3])) ? intval($qdat[3]) : 3600;
                                    if (jrCore_queue_worker_count($qname, $tout, $maxw) < $maxw) {
                                        // We are under the max allowed workers for this queue
                                        $_tmp = jrCore_queue_get($mod, $qname);
                                        if ($_tmp && isset($_tmp['queue_id'])) {
                                            // We found a queue entry - pass it on to the worker
                                            $ret = $func($_tmp['queue_data']);
                                            // Our queue workers can return:
                                            // 1) TRUE - everything is good, delete queue entry
                                            // 2) # - indicates we should "hide" the queue entry for # number of seconds before allowing another worker to pick it up
                                            // 3) FALSE - an issue was encountered processing the queue - no delete or increment
                                            if ($ret === true) {
                                                // We successfully processed our queue entry - delete it
                                                jrCore_queue_delete($_tmp['queue_id']);
                                            }
                                            elseif ($ret === 'EXIT') {
                                                // Forced exit by worker
                                                break;
                                            }
                                            else {
                                                $sec = 0;
                                                if (isset($ret) && jrCore_checktype($ret, 'number_nn')) {
                                                    $sec = (int) $ret;
                                                }
                                                jrCore_queue_release($_tmp['queue_id'], $sec);
                                            }
                                            $qcnt--;
                                            jrCore_db_close();
                                        }
                                        else {
                                            // We were unable to grab a queue entry - return
                                            break;
                                        }
                                    }
                                    else {
                                        // We are over our max allowed worker processes for this queue - return
                                        break;
                                    }
                                }
                                else {
                                    // Queues are NOT active - do not start another worker - return
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Keep jrCore cache directory clean during daily maintenance
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrCore_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // We will delete any old upload directories not accessed in 24 hours
    $old = (time() - 86400);
    $cdr = jrCore_get_module_cache_dir('jrCore');
    if (!is_dir($cdr)) {
        jrCore_logger('CRI', 'Unable to open jrCore cache dir for cleaning');
        return true;
    }
    $c = 0;
    $f = opendir($cdr);
    if ($f) {
        while ($file = readdir($f)) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir("{$cdr}/{$file}")) {
                $_tmp = stat("{$cdr}/{$file}");
                if (isset($_tmp['mtime']) && $_tmp['mtime'] < $old) {
                    jrCore_delete_dir_contents("{$cdr}/{$file}");
                    $c++;
                }
            }
        }
        closedir($f);
    }
    if ($c > 0) {
        jrCore_logger('INF', "deleted {$c} temp upload directories created more than 24 hours ago");
    }
    return true;
}
