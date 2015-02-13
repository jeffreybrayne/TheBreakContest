<?php
/**
 * Jamroom 5 Audio Support module
 *
 * copyright 2003 - 2015
 * by The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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

//------------------------------
// download_album
//------------------------------
function view_jrAudio_download_album($_post, $_user, $_conf)
{
    if (isset($_conf['jrAudio_block_album_download']) && $_conf['jrAudio_block_album_download'] == 'on' && !jrUser_is_admin()) {
        jrCore_notice('error', 'Audio album downloads are blocked for non-admin users');
    }
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice('error', 'invalid _profile_id - verify usage');
    }
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_notice('error', 'invalid album name - verify usage');
    }

    // See if we are already being created...
    ignore_user_abort(true);
    $fil = jrCore_get_media_directory($_post['_1']);
    $lck = "{$fil}/album_{$_post['_2']}.lock";
    if (is_file($lck)) {
        // We are already being created - wait a bit
        $i = 0;
        while (file_exists($lck)) {
            sleep(2);
            $i += 2;
            if ($i > 10) {
                unlink($lck);
                break;
            }
        }
    }

    // See if we have already created this album ZIP file
    $fil = "{$fil}/album_{$_post['_2']}.zip";
    if (!is_file($fil)) {
        touch($lck);
        $_sp = array(
            'search'      => array(
                "_profile_id = {$_post['_1']}",
                "audio_album_url = {$_post['_2']}"
            ),
            'return_keys' => array('_item_id', '_profile_id', 'audio_file_name', 'audio_file_original_name', 'audio_file_size', 'audio_file_extension', 'audio_file_original_extension'),
            'order_by'    => array('audio_file_track' => 'numerical_asc'),
            'limit'       => 50
        );
        $_rt = jrCore_db_search_items('jrAudio', $_sp);
        if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
            $_fl = array();
            foreach ($_rt['_items'] as $_ai) {
                if (isset($_ai['audio_file_item_price']) && $_ai['audio_file_item_price'] > 0) {
                    // We are for sale...
                    continue;
                }
                // jrAudio_499_audio_file.wma.original.wma
                $tmp = jrCore_get_media_directory($_ai['_profile_id']);
                if (isset($_ai['audio_file_original_extension'])) {
                    $tmp = "{$tmp}/jrAudio_{$_ai['_item_id']}_audio_file.{$_ai['audio_file_original_extension']}";
                    if (is_file("{$tmp}.original.{$_ai['audio_file_original_extension']}")) {
                        $_fl["{$_ai['audio_file_original_name']}"] = "{$tmp}.original.{$_ai['audio_file_original_extension']}";
                    }
                    elseif (is_file($tmp)) {
                        $_fl["{$_ai['audio_file_name']}"] = $tmp;
                    }
                }
                else {
                    $_fl["{$_ai['audio_file_name']}"] = $tmp;
                }
            }
            if (count($_fl) > 0) {
                jrCore_create_zip_file($fil, $_fl);
                unlink($lck);
                if (!is_file($fil)) {
                    jrCore_notice('error', 'unable to create album download ZIP file');
                }
            }
            else {
                unlink($lck);
                jrCore_notice('error', 'unable to create album download ZIP file (2)');
            }
        }
        else {
            // no entries in this album!
            unlink($lck);
            jrCore_notice('error', 'There were no audio files found to download for this album');
        }
    }
    jrCore_media_file_download($_post['_1'], basename($fil), "{$_post['_2']}.zip");
    session_write_close();
    exit();
}

//------------------------------
// import
//------------------------------
function view_jrAudio_import($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrAudio');
    jrCore_page_banner("Import Audio");
    jrCore_page_note("The Import Audio tool will import ID3 tagged files, creating profiles and audio items as necessary.<br>If a profile already exists for an audio file, it will be added to the existing profile.<br><br><strong>Conversion Workers: {$_conf['jrAudio_conversion_worker_count']}");

    // Form init
    $url = jrCore_get_module_url('jrCore');
    $url = "{$_conf['jrCore_base_url']}/{$url}/queue_view/queue_name=audio_conversions";
    $_tmp = array(
        'submit_value'  => 'import audio files',
        'submit_prompt' => 'Are you sure you want to Import the audio files? Please be patient - depending on the number of audio files, this could take some time.',
        'cancel'        => 'referrer',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Importing Audio Files',
        'modal_close'   => 'view conversion queue',
        'modal_onclick' => "jrCore_window_location('{$url}')"
    );
    jrCore_form_create($_tmp);

    // Import Directory
    $_tmp = array(
        'name'     => 'import_dir',
        'label'    => 'Import Directory',
        'help'     => 'Enter the directory on the server where the ID3 tagged audio files are located.<br><br><strong>Note:</strong> The web user must have read access to this directory!',
        'type'     => 'text',
        'default'  => $_conf['jrCore_base_dir'],
        'validate' => 'not_empty'
    );
    jrCore_form_field_create($_tmp);

    // Import Quota
    $_tmp = array(
        'name'     => 'import_quota_id',
        'label'    => 'Import Quota',
        'help'     => 'Select the Quota the import profiles will be created under',
        'type'     => 'select',
        'options'  => jrProfile_get_quotas(),
        'default'  => $_conf['jrProfile_default_quota_id'],
        'validate' => 'number_nn'
    );
    jrCore_form_field_create($_tmp);

    // Overwrite
    $_tmp = array(
        'name'     => 'import_del',
        'label'    => 'Overwrite on Import',
        'help'     => 'Check this option if you would like to overwrite existing audio entries with the imported audio entries.<br><br>An audio file will match if the <strong>Artist</strong>, <strong>Album</strong>, AND <strong>Title</strong> all match exactly to an existing entry.<br><br><strong>Warning!</strong> If an audio entry for a profile already exists, it will be <strong>deleted</strong> before it is added from the import!',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// import_save
//------------------------------
function view_jrAudio_import_save($_post, &$_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_modal_notice('update', "Importing Audio Files - please be patient");
    @ini_set('max_execution_time', 1978000); // 23 hours max
    @ini_set('memory_limit', '512M');

    if (!is_dir($_post['import_dir']) || !is_readable($_post['import_dir'])) {
        jrCore_form_modal_notice('error', 'import directory is not readable');
        jrCore_form_modal_notice('complete', 'Errors were encountered importing the audio files');
        exit;
    }
    $_fl = jrCore_get_directory_files($_post['import_dir']);
    if (!$_fl || !is_array($_fl)) {
        jrCore_form_modal_notice('error', 'no audio files found to import in directory');
        jrCore_form_modal_notice('complete', 'Errors were encountered importing the audio files');
        exit;
    }
    $nfl = count($_fl);
    jrCore_form_modal_notice('update', "found {$nfl} files - checking for audio files");

    // Go through and get files we can use
    $_ex = jrAudio_get_audio_types();
    $_mt = array();
    $_bd = array();
    $cnt = 0;
    foreach ($_fl as $full_file => $file_name) {
        $ext = jrCore_file_extension($file_name);
        if (isset($_ex[$ext])) {
            $_tm = jrCore_get_media_file_metadata($full_file, 'audio_file');
            if ($_tm && is_array($_tm) && isset($_tm['audio_file_artist'])) {
                $_tm['audio_file_name'] = $file_name;
                $_mt[$full_file] = $_tm;
            }
            else {
                $_bd[] = $file_name;
            }
        }
        $cnt++;
        if (($cnt % 10) === 0 || $cnt >= $nfl) {
            jrCore_form_modal_notice('update', "checked {$cnt} files");
        }
    }
    // Log bad audio entries
    if (count($_bd) > 0) {
        jrCore_logger('MAJ', "Audio files found missing audio tags - view debug for list of files", implode("\n", $_bd));
        jrCore_form_modal_notice('update', 'found ' . count($_bd) . ' audio files missing tags - check activity log');
    }
    unset($_bd, $_fl);
    $num = count($_mt);
    if ($num === 0) {
        jrCore_form_modal_notice('error', 'no audio files found to import in directory');
        jrCore_form_modal_notice('complete', 'Errors were encountered importing the audio files');
        exit;
    }
    jrCore_form_modal_notice('update', "found {$num} audio files with valid tags - importing");

    // Get Quota
    $_qt = jrProfile_get_quota($_post['import_quota_id']);

    // Import
    $num = 0;
    $_pc = array(); // profile cache
    foreach ($_mt as $full_file => $_tmp) {

        $file_name = $_tmp['audio_file_name'];
        $ext = jrCore_file_extension($file_name);

        // What fields from the meta data are we overriding?
        $_def = array(
            'audio_genre'          => 'no-genre',
            'audio_title'          => '',
            'audio_file_publisher' => '',
            'audio_file_composer'  => '',
            'audio_file_date'      => '',
            'audio_file_track'     => 1
        );

        foreach ($_def as $k => $v) {
            if (!isset($_tmp[$k])) {
                $_tmp[$k] = $v;
            }
        }
        // If we do not have a title, use the file name
        if (!isset($_tmp['audio_file_title']) || strlen($_tmp['audio_file_title']) === 0) {
            $_tmp['audio_file_title'] = substr($file_name, 0, strrpos(basename($file_name), '.'));
            $_tmp['audio_file_title'] = str_replace(array('-', '_'), ' ', $_tmp['audio_file_title']);
        }
        $_tmp['audio_title'] = $_tmp['audio_file_title'];
        $_tmp['audio_album'] = $_tmp['audio_file_album'];
        $_tmp['audio_genre'] = $_tmp['audio_file_genre'];
        $_tmp['audio_display_order'] = $_tmp['audio_file_track'];
        // Add in our SEO URL names if we get them
        foreach (array('audio_title', 'audio_album', 'audio_genre') as $k) {
            if (isset($_tmp[$k])) {
                $_tmp["{$k}_url"] = jrCore_url_string($_tmp[$k]);
            }
        }
        // Cleanup any fields that are empty...
        foreach ($_tmp as $k => $v) {
            if (strlen($v) === 0) {
                unset($_tmp[$k]);
            }
        }

        // Check for price
        if (isset($_post['audio_file_item_price']) && $_post['audio_file_item_price'] > 0) {
            $_tmp['audio_file_item_price'] = $_post['audio_file_item_price'];
        }

        // See if we need to create a profile...
        $pnm = jrCore_url_string($_tmp['audio_file_artist']);

        if (!isset($_pc[$pnm])) {
            $_pr = jrCore_db_get_item_by_key('jrProfile', 'profile_url', $pnm);
            if ($_pr && is_array($_pr)) {
                $pid = (int) $_pr['_profile_id'];
                // See if we have already imported this song from a previous run
                $_sc = array(
                    'search'         => array(
                        "_profile_id = {$pid}",
                        "audio_album_url = {$_tmp['audio_album_url']}",
                        "audio_title_url = {$_tmp['audio_title_url']}"
                    ),
                    'skip_triggers'  => true,
                    'privacy_check'  => false,
                    'ignore_pending' => true,
                    'limit'          => 1
                );
                $_ex = jrCore_db_search_items('jrAudio', $_sc);
                if ($_ex && is_array($_ex['_items'])) {
                    // We've got a match - see if we are deleting or skipping
                    if (isset($_post['import_del']) && $_post['import_del'] == 'on') {
                        jrCore_db_delete_item('jrAudio', $_ex['_items'][0]['_item_id']);
                    }
                    else {
                        jrCore_form_modal_notice('update', "skipping item exists: " . basename($file_name));
                    }
                    continue;
                }
                $_pc[$pnm] = $pid;

                // Delete existing album ZIP file...
                jrAudio_delete_album_zip_file($pid, $_tmp['audio_album_url']);
            }
            else {
                // We do not have this artist yet - create
                $_dt = array(
                    'profile_name'     => $_tmp['audio_file_artist'],
                    'profile_url'      => $pnm,
                    'profile_quota_id' => $_post['import_quota_id'],
                    'profile_private'  => (isset($_qt['jrProfile_default_privacy'])) ? $_qt['jrProfile_default_privacy'] : '1'
                );
                $_cr = array(
                    '_user_id' => 0
                );
                $pid = jrCore_db_create_item('jrProfile', $_dt, $_cr);
                if ($pid) {
                    $_cr = array(
                        '_profile_id' => $pid,
                        '_user_id'    => 0
                    );
                    jrCore_db_update_item('jrProfile', $pid, $_dt, $_cr);
                    $_pc[$pnm] = $pid;
                }
                else {
                    jrCore_form_modal_notice('error', "unable to create new artist: {$_tmp['audio_file_artist']}");
                    continue;
                }
            }
        }
        else {
            // Cached profile info
            $pid = $_pc[$pnm];
        }

        // We don't want to show this audio file in lists and on the site if
        // it is being converted - set our active flag to 0 if we're converting
        $_tmp['audio_active'] = 'on';
        if ($ext != 'mp3' && isset($_qt['quota_jrAudio_audio_conversions']) && $_qt['quota_jrAudio_audio_conversions'] == 'on') {
            $_tmp['audio_active'] = 'off';
        }

        // $aid will be the INSERT_ID (_item_id) of the created item
        $_cr = array('_profile_id' => $pid);
        $aid = jrCore_db_create_item('jrAudio', $_tmp, $_cr);
        if (!$aid) {
            jrCore_form_modal_notice('error', "error importing: " . basename($file_name));
            continue;
        }

        // Try to grab an embedded image if we have one
        $_img = jrAudio_get_apic_image($full_file);
        if (isset($_img) && is_array($_img)) {
            $dir = jrCore_get_media_directory($pid);
            jrCore_write_to_file("{$dir}/{$aid}_audio_image", $_img['image_data']);
            jrCore_write_to_file("{$dir}/{$aid}_audio_image.tmp", "audio_image.{$_img['extension']}");
            jrCore_save_media_file('jrAudio', "{$dir}/{$aid}_audio_image", $pid, $aid, 'audio_image');
            unlink("{$dir}/{$aid}_audio_image");
            unlink("{$dir}/{$aid}_audio_image.tmp");
        }

        // Now that we have our DataStore Item created, link up the file with it
        // We have to tell jrCore_save_media_file the file we want to link with this item,
        // so we pass in the FULL PATH $_file_name as arg #2 to jrCore_save_media_file
        jrCore_save_media_file('jrAudio', $full_file, $pid, $aid, 'audio_file');

        $sample = false;
        if (isset($_post['audio_file_item_price']) && $_post['audio_file_item_price'] > 0) {
            $sample = true;
        }

        // Lastly, check if audio conversions are enabled.
        // If so, we need to add this item into the conversion queue
        if (isset($_qt['quota_jrAudio_audio_conversions']) && $_qt['quota_jrAudio_audio_conversions'] == 'on') {
            $_queue = array(
                'file_name'   => 'audio_file',
                'quota_id'    => $_post['import_quota_id'],
                'profile_id'  => $pid,
                'item_id'     => $aid,
                'sample'      => $sample,
                'bitrate'     => intval($_qt['quota_jrAudio_conversion_bitrate']),
                'max_workers' => (isset($_conf['jrAudio_conversion_worker_count'])) ? intval($_conf['jrAudio_conversion_worker_count']) : 1
            );
            jrCore_queue_create('jrAudio', 'audio_conversions', $_queue);
        }

        jrCore_form_modal_notice('update', "imported: " . basename($file_name));
        $num++;
    }
    jrCore_form_delete_session();
    jrCore_form_modal_notice('complete', "{$num} audio files were successfully imported");
    exit;
}

//------------------------------
// reconvert
//------------------------------
function view_jrAudio_reconvert($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrAudio');
    jrCore_page_banner("Convert Audio");

    if (!isset($_post['quota_id']) || !jrCore_checktype($_post['quota_id'], 'number_nz')) {
        $num = jrCore_db_number_rows('jrAudio', 'item');
        $qid = 0;
    }
    else {
        $_sc = array(
            'quota_id'       => (int) $_post['quota_id'],
            'return_count'   => true,
            'ignore_pending' => true,
            'privacy_check'  => false,
            'limit'          => 1000000
        );
        $num = jrCore_db_search_items('jrAudio', $_sc);
        $qid = (int) $_post['quota_id'];
    }

    jrCore_page_note("The Convert Audio tool will create new audio conversion queue entries for the audio files specified.<br>It is recommended to run this during a low usage time - it can place a large load on your server while in process.<br><br><strong>Conversion Workers: {$_conf['jrAudio_conversion_worker_count']} &nbsp; &nbsp; Total Audio Files Selected: {$num}</strong>");

    // Form init
    $url = jrCore_get_module_url('jrCore');
    $url = "{$_conf['jrCore_base_url']}/{$url}/queue_view/queue_name=audio_conversions";
    $_tmp = array(
        'submit_value'  => 'submit audio for conversion',
        'submit_prompt' => 'Are you sure you want to Convert the audio files? Please be patient - on large systems this could take some time.',
        'cancel'        => 'referrer',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Creating Audio File Conversion Queue Entries',
        'modal_close'   => 'view conversion queue',
        'modal_onclick' => "jrCore_window_location('{$url}')"
    );
    jrCore_form_create($_tmp);

    // Select Quota
    $_qt = array(
        '0' => 'All Quotas'
    );
    $_qt = array_merge($_qt, jrProfile_get_quotas());
    $_tmp = array(
        'name'     => 'convert_quotas',
        'label'    => 'Conversion Quota',
        'help'     => 'If you want to reconvert the audio files for a specific quota, select the quota here.',
        'type'     => 'select',
        'options'  => $_qt,
        'value'    => $qid,
        'default'  => '0',
        'validate' => 'number_nn',
        'onchange' => "var v=this.options[this.selectedIndex].value; jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/reconvert/quota_id='+ v)"
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'convert_all',
        'label'    => 'Force Reconversion',
        'help'     => 'Normally if a media file already exists at the correct bitrate, it will be skipped - check this option to force reconversion of all media files.',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// reconvert_save
//------------------------------
function view_jrAudio_reconvert_save($_post, &$_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_modal_notice('update', "Converting Audio Files - please be patient");
    ini_set('max_execution_time', 1978000); // 23 hours max

    // Get Audio files and create queue entries
    $num = 0;
    $_sp = array(
        'return_keys'    => array(
            '_item_id',
            '_profile_id',
            'audio_file_item_price',
            'audio_file_length',
            'audio_file_size',
            'audio_file_extension',
            'audio_file_bitrate',
            'audio_file_sample_length',
            'profile_quota_id',
            'quota_jrAudio_conversion_bitrate'
        ),
        'ignore_pending' => true,
        'limit'          => 1000000
    );
    if (isset($_post['convert_quotas']) && jrCore_checktype($_post['convert_quotas'], 'number_nz')) {
        $_sp['search'] = array("profile_quota_id = {$_post['convert_quotas']}");
    }
    $_sg = jrCore_db_search_items('jrAudio', $_sp);
    if ($_sg && is_array($_sg) && isset($_sg['_items'])) {
        foreach ($_sg['_items'] as $v) {

            // See if we have to convert this audio file
            if (isset($_post['convert_all']) && $_post['convert_all'] == 'off') {
                if (isset($v['audio_file_bitrate']) && $v['audio_file_bitrate'] <= $v['quota_jrAudio_conversion_bitrate']) {
                    // We're good on bitrate - check sample length and OGG file
                    $input_file = jrCore_get_media_file_path('jrAudio', 'audio_file', $v);
                    $ogg_file = str_replace('.mp3', '.ogg', $input_file);
                    if (isset($_conf['jrAudio_conversion_format']) && ((strstr($_conf['jrAudio_conversion_format'], 'ogg') && is_file($ogg_file)) || !strstr($_conf['jrAudio_conversion_format'], 'ogg'))) {
                        if (isset($v['audio_file_item_price']) && $v['audio_file_item_price'] > 0) {
                            // We've got a price
                            if (isset($v['audio_file_sample_length']) && isset($_conf['jrAudio_sample_length']) && jrCore_checktype($_conf['jrAudio_sample_length'], 'number_nz') && $v['audio_file_sample_length'] == $_conf['jrAudio_sample_length']) {
                            }
                            else {
                                // Sample length has changed - rebuild
                                $_queue = array(
                                    'file_name'  => 'audio_file',
                                    'profile_id' => $v['_profile_id'],
                                    'item_id'    => $v['_item_id']
                                );
                                jrCore_queue_create('jrAudio', 'audio_sample', $_queue);
                            }
                            continue;
                        }
                        // No price set - remove any sample file
                        if (is_file("{$input_file}.sample.mp3")) {
                            jrCore_delete_media_file($v['_profile_id'], "{$input_file}.sample.mp3");
                            jrCore_delete_media_file($v['_profile_id'], str_replace('.mp3', '.ogg', $input_file) . ".sample.ogg");
                            jrCore_db_delete_item_key('jrAudio', $v['_item_id'], 'audio_file_sample_length');
                        }
                        continue;
                    }
                }
            }

            $sample = false;
            if (isset($v['audio_file_item_price']) && $v['audio_file_item_price'] > 0) {
                $sample = true;
            }
            $_queue = array(
                'file_name'   => 'audio_file',
                'quota_id'    => $v['profile_quota_id'],
                'profile_id'  => $v['_profile_id'],
                'item_id'     => $v['_item_id'],
                'sample'      => $sample,
                'reconvert'   => 1,
                'bitrate'     => intval($v['quota_jrAudio_conversion_bitrate']),
                'max_workers' => (isset($_conf['jrAudio_conversion_worker_count'])) ? intval($_conf['jrAudio_conversion_worker_count']) : 1
            );
            jrCore_queue_create('jrAudio', 'audio_conversions', $_queue);
            $num++;
            if (($num % 100) === 0 || isset($_sg['info']['total_items']) && $num >= $_sg['info']['total_items']) {
                jrCore_form_modal_notice('update', "Submitted {$num} conversion queue entries");
            }
        }
    }
    jrCore_form_delete_session();
    jrCore_form_modal_notice('complete', "{$num} audio files were submitted for conversion");
    exit;
}

//------------------------------
// create_album
//------------------------------
function view_jrAudio_create_album($_post, $_user, $_conf)
{
    // Must be logged in to create a new audio file
    jrUser_session_require_login();
    jrCore_check_ffmpeg_install();
    jrUser_check_quota_access('jrAudio');
    jrProfile_check_disk_usage();

    jrCore_page_banner(35);

    // Form init
    $_tmp = array(
        'submit_value' => 35,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Audio Album
    $_tmp = array(
        'name'      => 'audio_album',
        'label'     => 31,
        'help'      => 32,
        'type'      => 'text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Genre
    $_tmp = array(
        'name'      => 'audio_genre',
        'label'     => 12,
        'help'      => 13,
        'type'      => 'select_and_text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => false
    );
    jrCore_form_field_create($_tmp);

    // Audio File
    $_tmp = array(
        'name'     => 'audio_file',
        'label'    => 36,
        'help'     => 37,
        'text'     => 38,
        'type'     => 'audio',
        'required' => true,
        'multiple' => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Image
    $_tmp = array(
        'name'     => 'audio_image',
        'label'    => 16,
        'help'     => 17,
        'text'     => 39,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_album_save
//------------------------------
function view_jrAudio_create_album_save($_post, &$_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrAudio');

    $_files = jrCore_get_uploaded_media_files('jrAudio', 'audio_file');
    if (!isset($_files) || !is_array($_files)) {
        jrCore_set_form_notice('error', 'You must upload some audio files!');
        jrCore_form_result();
    }

    // See if we have an image
    $_image = jrCore_get_uploaded_media_files('jrAudio', 'audio_image', $_user['user_active_profile_id']);

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrAudio', 'create', $_post);

    // If we have been given a PRICE for the individual audio items, we create a sample
    $sample = false;
    if (isset($_rt['audio_file_item_price']) && strlen($_rt['audio_file_item_price']) > 0) {
        $sample = true;
    }
    elseif (isset($_rt['audio_album_bundle_price']) && strlen($_rt['audio_album_bundle_price']) > 0) {
        $sample = true;
    }

    foreach ($_files as $file_name) {

        // Grab meta data from this file
        $_tmp = array();

        // What fields from the meta data are we overriding?
        $_def = array(
            'audio_genre'          => 'no-genre',
            'audio_title'          => '',
            'audio_file_publisher' => '',
            'audio_file_composer'  => '',
            'audio_file_date'      => '',
            'audio_file_track'     => 1
        );
        foreach ($_def as $k => $v) {
            if (isset($_rt[$k]) && strlen($_rt[$k]) > 0) {
                $_tmp[$k] = $_rt[$k];
            }
            else {
                $_tmp[$k] = $v;
            }
        }

        // Merge in meta data
        $_met = jrCore_get_media_file_metadata($file_name, 'audio_file');
        if (isset($_met) && is_array($_met)) {
            foreach ($_met as $k => $v) {
                if (strlen($v) > 0) {
                    $_tmp[$k] = $v;
                }
            }
        }

        // Add in any additional custom fields that come in
        foreach ($_rt as $k => $v) {
            if (!isset($_tmp[$k])) {
                $_tmp[$k] = $v;
            }
        }

        // If we do not have a title, use the file name
        if (!isset($_tmp['audio_title']) || strlen($_tmp['audio_title']) === 0) {
            $tmp = trim(file_get_contents("{$file_name}.tmp"));
            $_tmp['audio_title'] = substr($tmp, 0, strrpos($tmp, '.'));
            $_tmp['audio_title'] = str_replace(array('-', '_'), ' ', $_tmp['audio_title']);
        }

        // Add in our SEO URL names if we get them
        foreach (array('audio_title', 'audio_album', 'audio_genre') as $k) {
            if (isset($_tmp[$k])) {
                $_tmp["{$k}_url"] = jrCore_url_string($_tmp[$k]);
            }
        }

        // Cleanup any fields that are empty...
        foreach ($_tmp as $k => $v) {
            if (strlen($v) === 0) {
                unset($_tmp[$k]);
            }
        }

        // Or that we don't want...
        if (isset($_tmp['audio_file_resolution'])) {
            unset($_tmp['audio_file_resolution']);
        }

        // We don't want to show this audio file in lists and on the site if
        // it is being converted - set our active flag to 0 if we're converting
        $ext = '';
        if (is_file("{$file_name}.tmp")) {
            $ext = jrCore_file_extension(trim(file_get_contents("{$file_name}.tmp")));
        }
        $_tmp['audio_active'] = 'on';
        if ($ext != 'mp3' && isset($_user['quota_jrAudio_audio_conversions']) && $_user['quota_jrAudio_audio_conversions'] == 'on') {
            $_tmp['audio_active'] = 'off';
        }

        // $aid will be the INSERT_ID (_item_id) of the created item
        $aid = jrCore_db_create_item('jrAudio', $_tmp);
        if (!$aid) {
            jrCore_set_form_notice('error', 'unable to create new audio file in DataStore!');
            jrCore_form_result();
        }

        // Try to grab an embedded image if we have one
        if (!$_image) {
            if (isset($file_name) && is_file($file_name)) {
                $_img = jrAudio_get_apic_image($file_name);
                if (isset($_img) && is_array($_img)) {
                    $dir = jrCore_get_media_directory($_user['user_active_profile_id']);
                    jrCore_write_to_file("{$dir}/{$aid}_audio_image", $_img['image_data']);
                    jrCore_write_to_file("{$dir}/{$aid}_audio_image.tmp", "audio_image.{$_img['extension']}");
                    jrCore_save_media_file('jrAudio', "{$dir}/{$aid}_audio_image", $_user['user_active_profile_id'], $aid);
                    unlink("{$dir}/{$aid}_audio_image");
                    unlink("{$dir}/{$aid}_audio_image.tmp");
                }
            }
        }

        // Now that we have our DataStore Item created, link up the file with it
        // We have to tell jrCore_save_media_file the file we want to link with this item,
        // so we pass in the FULL PATH $_file_name as arg #2 to jrCore_save_media_file
        jrCore_save_media_file('jrAudio', $file_name, $_user['user_active_profile_id'], $aid);

        // Add album image if supplied
        if (isset($_image) && is_array($_image) && isset($_image[0])) {
            jrCore_save_media_file('jrAudio', $_image[0], $_user['user_active_profile_id'], $aid);
        }

        // Lastly, check if audio conversions are enabled.
        // If so, we need to add this item into the conversion queue
        if (isset($_user['quota_jrAudio_audio_conversions']) && $_user['quota_jrAudio_audio_conversions'] == 'on') {
            $_queue = array(
                'file_name'   => 'audio_file',
                'quota_id'    => $_user['profile_quota_id'],
                'profile_id'  => $_user['user_active_profile_id'],
                'item_id'     => $aid,
                'sample'      => $sample,
                'bitrate'     => intval($_user['quota_jrAudio_conversion_bitrate']),
                'max_workers' => (isset($_conf['jrAudio_conversion_worker_count'])) ? intval($_conf['jrAudio_conversion_worker_count']) : 1
            );
            jrCore_queue_create('jrAudio', 'audio_conversions', $_queue);
        }

        // Add the FIRST AUDIO to our actions...
        if (!isset($action_saved)) {
            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'create_album', 'jrAudio', $aid);
            $action_saved = true;
        }
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/albums");
}

//------------------------------
// update_album
//------------------------------
function view_jrAudio_update_album($_post, $_user, $_conf)
{
    // Must be logged in to create a new audio file
    jrUser_session_require_login();
    jrCore_check_ffmpeg_install();
    jrUser_check_quota_access('jrAudio');
    jrProfile_check_disk_usage();

    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_notice_page('error', 60);
    }

    // get our first audio entry that uses this album
    $_sc = array(
        'search'         => array(
            "audio_album_url = {$_post['_1']}",
            "_profile_id = {$_user['user_active_profile_id']}"
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 1
    );
    $_rt = jrCore_db_search_items('jrAudio', $_sc);
    if (!$_rt || !is_array($_rt['_items'])) {
        jrCore_notice_page('error', 60);
    }
    jrCore_page_banner(60);

    // Form init
    $_tmp = array(
        'submit_value' => 60,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt['_items'][0]
    );
    jrCore_form_create($_tmp);

    // Audio Album URL
    $_tmp = array(
        'type'  => 'hidden',
        'name'  => 'existing_url',
        'value' => $_rt['_items'][0]['audio_album_url'],
    );
    jrCore_form_field_create($_tmp);

    // Audio Album
    $_tmp = array(
        'name'      => 'audio_album',
        'label'     => 31,
        'help'      => 32,
        'type'      => 'text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Genre
    $_tmp = array(
        'name'      => 'audio_genre',
        'label'     => 12,
        'help'      => 13,
        'type'      => 'select_and_text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => false
    );
    jrCore_form_field_create($_tmp);

    // Audio Image
    $_tmp = array(
        'name'     => 'audio_image',
        'label'    => 16,
        'help'     => 17,
        'text'     => 39,
        'type'     => 'image',
        'value'    => $_rt['_items'][0],
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// update_album_save
//------------------------------
function view_jrAudio_update_album_save($_post, &$_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrAudio');

    // get all audio entries in this album
    $_sc = array(
        'search'         => array(
            "audio_album_url = {$_post['existing_url']}",
            "_profile_id = {$_user['user_active_profile_id']}"
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'privacy_check'  => false,
        'limit'          => 1000
    );
    $_rt = jrCore_db_search_items('jrAudio', $_sc);
    if (!is_array($_rt) || !is_array($_rt['_items'])) {
        jrCore_set_form_notice('error', 62);
        jrCore_form_result();
    }

    // Delete existing album ZIP file...
    jrAudio_delete_album_zip_file($_user['user_active_profile_id'], $_post['existing_url']);

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrAudio', 'update_album', $_post);
    $_sv['audio_album_url'] = jrCore_url_string($_post['audio_album']);
    $_sv['audio_genre_url'] = jrCore_url_string($_post['audio_genre']);

    // See if we have an image
    $_image = jrCore_get_uploaded_media_files('jrAudio', 'audio_image', $_user['user_active_profile_id']);

    // If we have been given a PRICE for the individual audio items, we create a sample
    $sample = false;
    if (isset($_sv['audio_file_item_price']) && strlen($_sv['audio_file_item_price']) > 0) {
        $sample = true;
    }
    elseif (isset($_sv['audio_album_bundle_price']) && strlen($_sv['audio_album_bundle_price']) > 0) {
        $sample = true;
    }

    foreach ($_rt['_items'] as $_af) {

        if (!jrCore_db_update_item('jrAudio', $_af['_item_id'], $_sv)) {
            jrCore_set_form_notice('error', 'unable to update audio file in DataStore!');
            jrCore_form_result();
        }

        // Add album image if supplied
        if (is_array($_image) && isset($_image[0])) {
            jrCore_save_media_file('jrAudio', $_image[0], $_user['user_active_profile_id'], $_af['_item_id']);
        }

        // Create sample if needed
        $input_file = jrCore_get_media_file_path('jrAudio', 'audio_file', $_af);
        if ($sample) {
            if (!is_file("{$input_file}.sample.mp3")) {
                // Sample length has changed - rebuild
                $_queue = array(
                    'file_name'  => 'audio_file',
                    'profile_id' => $_user['user_active_profile_id'],
                    'item_id'    => $_af['_item_id']
                );
                jrCore_queue_create('jrAudio', 'audio_sample', $_queue);
            }
        }
        elseif (is_file("{$input_file}.sample.mp3")) {
            jrCore_delete_media_file($_user['user_active_profile_id'], "{$input_file}.sample.mp3");
            jrCore_delete_media_file($_user['user_active_profile_id'], str_replace('.mp3', '.ogg', $input_file) . ".sample.ogg");
            jrCore_db_delete_item_key('jrAudio', $_af['_item_id'], 'audio_file_sample_length');
        }

        // Add the FIRST AUDIO to our actions...
        if (!isset($action_saved)) {
            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'update_album', 'jrAudio', $_af['_item_id']);
            $action_saved = true;
        }
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/albums/{$_sv['audio_album_url']}");
}

//------------------------------
// create
//------------------------------
function view_jrAudio_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new audio file
    jrUser_session_require_login();
    jrCore_check_ffmpeg_install();
    jrUser_check_quota_access('jrAudio');
    jrProfile_check_disk_usage();

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrAudio', 'audio_title', $_sr, 'create', 'update');
    jrCore_page_banner(22, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 9,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Audio Title
    $_tmp = array(
        'name'      => 'audio_title',
        'label'     => 10,
        'help'      => 11,
        'type'      => 'text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Genre
    $_tmp = array(
        'name'      => 'audio_genre',
        'label'     => 12,
        'help'      => 13,
        'type'      => 'select_and_text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Album
    $_tmp = array(
        'name'      => 'audio_album',
        'label'     => 31,
        'help'      => 32,
        'type'      => 'select_and_text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => false
    );
    jrCore_form_field_create($_tmp);

    // Audio File
    $_tmp = array(
        'name'     => 'audio_file',
        'label'    => 14,
        'help'     => 15,
        'text'     => 29,
        'type'     => 'audio',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Image
    $_tmp = array(
        'name'     => 'audio_image',
        'label'    => 16,
        'help'     => 17,
        'text'     => 30,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrAudio_create_save($_post, &$_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrAudio');

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrAudio', 'create', $_post);

    // Add in our SEO URL names
    $_to_check = array('audio_title', 'audio_album', 'audio_genre');
    foreach ($_to_check as $check) {
        if (isset($_rt[$check])) {
            $_rt["{$check}_url"] = jrCore_url_string($_rt[$check]);
        }
    }

    // We don't want to show this audio file in lists and on the site if
    // it is being converted - set our active flag to 0 if we're converting
    $_rt['audio_active'] = 'on';
    if (isset($_user['quota_jrAudio_audio_conversions']) && $_user['quota_jrAudio_audio_conversions'] == 'on') {
        $_rt['audio_active'] = 'off';
    }

    // $aid will be the INSERT_ID (_item_id) of the created item
    $aid = jrCore_db_create_item('jrAudio', $_rt);
    if (!$aid) {
        jrCore_set_form_notice('error', 18);
        jrCore_form_result();
    }

    // Save any uploaded media files
    jrCore_save_all_media_files('jrAudio', 'create', $_user['user_active_profile_id'], $aid);

    // Delete existing album ZIP file...
    jrAudio_delete_album_zip_file($_user['user_active_profile_id'], $_rt['audio_album_url']);

    // Check for uploaded files and convert
    if (isset($_user['quota_jrAudio_audio_conversions']) && $_user['quota_jrAudio_audio_conversions'] == 'on') {

        foreach ($_rt as $k => $v) {
            if (strpos($k, '_bitrate')) {

                // We have a file field
                $fld = str_replace('_bitrate', '', $k);

                // If we have been given a PRICE for this audio item, we create a sample
                $sample = false;
                if (isset($_rt["{$fld}_item_price"]) && strlen($_rt["{$fld}_item_price"]) > 0) {
                    $sample = true;
                }
                $_queue = array(
                    'file_name'   => $fld,
                    'quota_id'    => $_user['profile_quota_id'],
                    'profile_id'  => $_user['user_active_profile_id'],
                    'item_id'     => $aid,
                    'sample'      => $sample,
                    'bitrate'     => intval($_user['quota_jrAudio_conversion_bitrate']),
                    'max_workers' => intval($_conf['jrAudio_conversion_worker_count'])
                );
                jrCore_queue_create('jrAudio', 'audio_conversions', $_queue);
            }
        }
    }
    else {

        // If we are ADDING a price, we must create our sample as well as TAG
        foreach ($_rt as $k => $v) {
            if (strpos($k, '_bitrate')) {
                $fld = str_replace('_bitrate', '', $k);
                $input_file = jrCore_get_media_file_path('jrAudio', $fld, $_rt);
                if (isset($_rt["{$fld}_item_price"]) && strlen($_rt["{$fld}_item_price"]) > 0 && !is_file("{$input_file}.sample.mp3")) {
                    // Create Sample
                    $_queue = array(
                        'file_name'  => $fld,
                        'profile_id' => $_user['user_active_profile_id'],
                        'item_id'    => $aid
                    );
                    jrCore_queue_create('jrAudio', 'audio_sample', $_queue);
                }
                if (isset($_user['quota_jrAudio_audio_tag']) && $_user['quota_jrAudio_audio_tag'] == 'on') {
                    $_temp = jrCore_db_get_item('jrAudio', $aid);
                    $_tags = array(
                        'TPE1' => $_temp['profile_name'],
                        'TPE2' => $_temp['profile_name'],
                        'TIT2' => $_temp['audio_title'],
                        'TCOP' => 'Copyright ' . strftime('%Y') . " by {$_temp['profile_name']}",
                        'TCON' => $_temp['audio_genre'],
                        'TALB' => $_temp['audio_album'],
                        'TRCK' => (int) $_temp['audio_file_track'],
                        'TYER' => strftime('%Y'),
                        'COMM' => "Downloaded from {$_conf['jrCore_system_name']}",
                        'WOAF' => "{$_conf['jrCore_base_url']}/{$_temp['profile_url']}/{$_post['module_url']}/{$aid}/{$_temp['audio_title_url']}"
                    );
                    jrAudio_tag_audio_file($input_file, $_tags);
                }
            }
        }
    }

    // See if we got an audio image - if we did not, let's try to read any embedded APIC image in the audio file
    $_im = jrCore_get_uploaded_media_files('jrAudio', 'audio_image', $_user['user_active_profile_id']);
    if (!$_im) {
        $_fl = jrCore_get_uploaded_media_files('jrAudio');
        if ($_fl && is_array($_fl)) {
            foreach ($_fl as $file) {
                $_img = jrAudio_get_apic_image($file);
                if (isset($_img) && is_array($_img)) {
                    $dir = jrCore_get_media_directory($_user['user_active_profile_id']);
                    jrCore_write_to_file("{$dir}/{$aid}_audio_image", $_img['image_data']);
                    jrCore_write_to_file("{$dir}/{$aid}_audio_image.tmp", "audio_image.{$_img['extension']}");
                    jrCore_save_media_file('jrAudio', "{$dir}/{$aid}_audio_image", $_user['user_active_profile_id'], $aid);
                    unlink("{$dir}/{$aid}_audio_image");
                    unlink("{$dir}/{$aid}_audio_image.tmp");
                }
            }
        }
    }

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrAudio', $aid);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$aid}/{$_rt['audio_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrAudio_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_check_ffmpeg_install();
    jrUser_check_quota_access('jrAudio');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 20);
    }
    $_rt = jrCore_db_get_item('jrAudio', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 21);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrAudio', 'audio_title', $_sr, 'create', 'update');
    jrCore_page_banner(23, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 24,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Audio Title
    $_tmp = array(
        'name'      => 'audio_title',
        'label'     => 10,
        'help'      => 11,
        'type'      => 'text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Genre
    $_tmp = array(
        'name'      => 'audio_genre',
        'label'     => 12,
        'help'      => 13,
        'type'      => 'select_and_text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Audio Album
    $_tmp = array(
        'name'      => 'audio_album',
        'label'     => 31,
        'help'      => 32,
        'type'      => 'select_and_text',
        'validate'  => 'printable',
        'ban_check' => 'word',
        'required'  => false
    );
    jrCore_form_field_create($_tmp);

    // Audio File
    $_tmp = array(
        'name'     => 'audio_file',
        'label'    => 14,
        'help'     => 15,
        'text'     => 29,
        'type'     => 'audio',
        'value'    => $_rt,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Audio Image
    $_tmp = array(
        'name'     => 'audio_image',
        'label'    => 16,
        'help'     => 17,
        'text'     => 30,
        'type'     => 'image',
        'value'    => $_rt,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrAudio_update_save($_post, &$_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrAudio');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrAudio', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 20);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrAudio', 'update', $_post);

    // Add in our SEO URL names
    $_sv['audio_title_url'] = jrCore_url_string($_sv['audio_title']);
    $_sv['audio_album_url'] = jrCore_url_string($_sv['audio_album']);
    $_sv['audio_genre_url'] = jrCore_url_string($_sv['audio_genre']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrAudio', $_post['id'], $_sv);

    // Save any uploaded media files
    jrCore_save_all_media_files('jrAudio', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Delete existing album ZIP file...
    jrAudio_delete_album_zip_file($_user['user_active_profile_id'], $_rt['audio_album_url']);

    // Check for uploaded files and convert
    if (isset($_user['quota_jrAudio_audio_conversions']) && $_user['quota_jrAudio_audio_conversions'] == 'on') {

        $_don = array();
        foreach ($_sv as $k => $v) {
            if (strpos($k, '_bitrate')) {

                // We have a file field
                $fld = str_replace('_bitrate', '', $k);
                $_don[$fld] = 1;

                // If we have been given a PRICE for this audio item, we create a sample
                $sample = false;
                if (isset($_sv["{$fld}_item_price"]) && strlen($_sv["{$fld}_item_price"]) > 0) {
                    $sample = true;
                }
                $_queue = array(
                    'file_name'   => $fld,
                    'quota_id'    => $_user['profile_quota_id'],
                    'profile_id'  => $_user['user_active_profile_id'],
                    'item_id'     => $_post['id'],
                    'sample'      => $sample,
                    'bitrate'     => intval($_user['quota_jrAudio_conversion_bitrate']),
                    'max_workers' => intval($_conf['jrAudio_conversion_worker_count'])
                );
                jrCore_queue_create('jrAudio', 'audio_conversions', $_queue);
            }
        }
    }

    // Check for pricing changes
    foreach ($_sv as $k => $v) {
        if (strpos($k, '_item_price')) {
            $fld = str_replace('_item_price', '', $k);
            if (!isset($_don[$fld])) {

                $input_file = jrCore_get_media_file_path('jrAudio', $fld, $_rt);
                if (isset($_sv["{$fld}_item_price"]) && strlen($_sv["{$fld}_item_price"]) > 0 && !is_file("{$input_file}.sample.mp3")) {
                    // Create Sample
                    $_queue = array(
                        'file_name'  => $fld,
                        'profile_id' => $_user['user_active_profile_id'],
                        'item_id'    => $_post['id']
                    );
                    jrCore_queue_create('jrAudio', 'audio_sample', $_queue);
                }
                // See if we are removing a price - delete sample
                elseif (isset($_rt["{$fld}_item_price"]) && strlen($_rt["{$fld}_item_price"]) > 0 && (!isset($_sv["{$fld}_item_price"]) || strlen($_sv["{$fld}_item_price"]) === 0)) {

                    // jrAudio_383_audio_file.mp3.sample.mp3
                    // We have removed our individual price - see if we are still part
                    // of a bundle that has a price on it
                    $remove = true;
                    if (isset($_rt['audio_file_item_bundle']) && strlen($_rt['audio_file_item_bundle']) > 0) {
                        $_id = array();
                        foreach (explode(',', $_rt['audio_file_item_bundle']) as $bid) {
                            $_id[] = (int) $bid;
                        }
                        $_bi = jrCore_db_get_multiple_items('jrFoxyCartBundle', $_id, array('bundle_item_price'));
                        if ($_bi && is_array($_bi)) {
                            foreach ($_bi as $_bun) {
                                if (isset($_bun['bundle_item_price']) && $_bun['bundle_item_price'] > 0) {
                                    $remove = false;
                                    break;
                                }
                            }
                        }
                    }
                    if ($remove) {
                        jrCore_delete_media_file($_user['user_active_profile_id'], "{$input_file}.sample.mp3");
                        jrCore_delete_media_file($_user['user_active_profile_id'], str_replace('.mp3', '.ogg', $input_file) . ".sample.ogg");
                        jrCore_db_delete_item_key('jrAudio', $_post['id'], 'audio_file_sample_length');
                    }
                }

                // Tagging
                if (jrCore_file_extension($input_file) === 'mp3' && isset($_user['quota_jrAudio_audio_tag']) && $_user['quota_jrAudio_audio_tag'] == 'on') {
                    $_temp = jrCore_db_get_item('jrAudio', $_post['id']);
                    $_tags = array(
                        'TPE1' => $_temp['profile_name'],
                        'TPE2' => $_temp['profile_name'],
                        'TIT2' => $_temp['audio_title'],
                        'TCOP' => 'Copyright ' . strftime('%Y') . " by {$_temp['profile_name']}",
                        'TCON' => $_temp['audio_genre'],
                        'TALB' => $_temp['audio_album'],
                        'TRCK' => (int) $_temp['audio_file_track'],
                        'TYER' => strftime('%Y'),
                        'COMM' => "Downloaded from {$_conf['jrCore_system_name']}",
                        'WOAF' => "{$_conf['jrCore_base_url']}/{$_temp['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_temp['audio_title_url']}"
                    );
                    jrAudio_tag_audio_file($input_file, $_tags);
                    if (jrCore_media_file_exists($_user['user_active_profile_id'], "{$input_file}.original.mp3")) {
                        jrAudio_tag_audio_file("{$input_file}.original.mp3", $_tags);
                    }
                }
            }
        }
    }

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrAudio', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['audio_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrAudio_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrAudio');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 20);
    }
    $_rt = jrCore_db_get_item('jrAudio', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice_page('error', 20);
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Delete item and any associated files
    jrCore_db_delete_item('jrAudio', $_post['id']);
    jrCore_queue_delete_by_item_id('jrAudio', $_post['id']);

    // Delete existing album ZIP file...
    jrAudio_delete_album_zip_file($_user['user_active_profile_id'], $_rt['audio_album_url']);

    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// delete_album
//------------------------------
function view_jrAudio_delete_album($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrAudio');

    // Make sure we get a good id
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_notice_page('error', 20);
    }
    $_rt = jrCore_db_get_item_by_key('jrAudio', 'audio_album_url', $_post['_1']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice_page('error', 20);
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Delete all items that match
    $_id = jrCore_db_get_multiple_items_by_key('jrAudio', 'audio_album_url', $_post['_1'], true);
    if (isset($_id) && is_array($_id)) {
        jrCore_db_delete_multiple_items('jrAudio', $_id);
    }

    // Delete existing album ZIP file...
    jrAudio_delete_album_zip_file($_user['user_active_profile_id'], $_rt['audio_album_url']);

    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// jrEmbed tab (loaded via ajax)
//------------------------------
function view_jrAudio_tab($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }

    $ss = array();
    $default = true;

    // search string
    if (isset($_post['ss']) && $_post['ss'] !== "false" && $_post['ss'] !== "undefined" && $_post['ss'] !== "") {
        $ss[] = "audio_% LIKE %{$_post['ss']}%";
        $default = false;
    }
    // profile
    if (isset($_post['profile_url']) && $_post['profile_url'] !== "false" && $_post['profile_url'] !== "undefined" && $_post['profile_url'] !== "") {
        $ss[] = "profile_url = {$_post['profile_url']}";
        $default = false;
    }
    // genre
    if (isset($_post['genre_url']) && $_post['genre_url'] !== "false" && $_post['genre_url'] !== "undefined" && $_post['genre_url'] !== "") {
        $genre_url = jrCore_url_string($_post['genre_url']);
        $ss[] = "audio_genre_url = $genre_url";
        $default = false;
    }
    // album
    if (isset($_post['album_url']) && $_post['album_url'] !== "false" && $_post['album_url'] !== "undefined" && $_post['album_url'] !== "") {
        $album_url = jrCore_url_string($_post['album_url']);
        $ss[] = "audio_album_url = $album_url";
        $default = false;
    }
    // default list of items
    if ($default) {
        $ss[] = "_profile_id = {$_user['user_active_profile_id']}";
    }

    // Create search params from $_post
    $_sp = array(
        'search'              => $ss,
        'pagebreak'           => 8,
        'page'                => $_post['p'],
        'exclude_jrUser_keys' => true
    );

    $_rt = jrCore_db_search_items('jrAudio', $_sp);
    return jrCore_parse_template('tab_ajax_audio.tpl', $_rt, 'jrAudio');
}

//----------------------------------
// update the order of an album.
//----------------------------------
function view_jrAudio_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['audio_file_track']) || !is_array($_post['audio_file_track'])) {
        return jrCore_json_response(array('error', 'invalid audio_file_track array received'));
    }

    // Get our audio files that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items('jrAudio', $_post['audio_file_track']);
        if (!isset($_rt) || !is_array($_rt)) {
            return jrCore_json_response(array('error', 'unable to retrieve audio entries from database'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error', 'permission denied'));
            }
        }
    }
    $_up = array();
    foreach ($_post['audio_file_track'] as $ord => $aid) {
        $_up[$aid] = array('audio_file_track' => $ord);
    }
    jrCore_db_update_multiple_items('jrAudio', $_up);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'audio_file_track successfully updated'));
}
