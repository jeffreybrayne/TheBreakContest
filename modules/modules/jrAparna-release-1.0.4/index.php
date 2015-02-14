<?php
/**
 * Jamroom 5 Aparna module
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
// clone_module
//------------------------------
function view_jrAparna_clone($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrAparna');

    // Make sure the module directory is writable by the web user
    if (!is_writable(APP_DIR . '/modules')) {
        jrCore_set_form_notice('error', 'The modules directory is not writable by the web user - unable to clone Aparna');
        jrCore_page_banner('Create');
        jrCore_get_form_notice();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    elseif ($_post['module_url'] != strrev('anrapa')) {
        jrCore_set_form_notice('error', 'Please use the _jrAparna_ module to do any further cloned modules');
        jrCore_page_banner('Clone Aparna');
        jrCore_get_form_notice();
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    }
    else {
        jrCore_page_banner('Clone Aparna');

        // Form init
        $_tmp = array(
            'submit_value'  => 'Clone',
            'cancel'        => 'referrer',
            'submit_prompt' => 'Are you sure you want to create a new module?',
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'       => 'module_name',
            'label'      => 'New Module Name',
            'help'       => "Enter the new module name. Be sure that it's in the Jamroom format of xxNewModule with the two character prefix.<br><br><b>NOTE:</b> Only letters, numbers and underscores are allowed in the name.<br><br><b>NOTE:</b> After cloning run the integrity check and your new module will appear in the TOOLS section of the ACP.",
            'type'       => 'text',
            'value'      => '',
            'min'        => 3,
            'validate'   => 'core_string',
            'onkeypress' => 'if (event && event.keyCode == 13) return false;'
        );
        jrCore_form_field_create($_tmp);
    }

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// clone_module_save
//------------------------------
function view_jrAparna_clone_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $_rt = jrCore_get_datastore_modules();
    if (isset($_post['module_name']) && array_key_exists($_post['module_name'], $_rt)) {
        jrCore_set_form_notice('error', 'New module already exists');
        jrCore_form_result();
    }
    // Clone Aparna
    $_rp = array(
        'jrAparna' => $_post['module_name'],
        'Aparna'   => substr($_post['module_name'], 2),
        'aparna'   => strtolower(substr($_post['module_name'], 2))
    );
    $res = jrCore_copy_dir_recursive(APP_DIR . "/modules/jrAparna/clone_files", APP_DIR . "/modules/{$_post['module_name']}", $_rp);
    if (!$res) {
        jrCore_set_form_notice('error', "An error was encountered trying to copy the module directory - check Error Log");
    }
    else {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The ' . $_post['module_name'] . ' module has been created. Now run the <a href="' . $_conf['jrCore_base_url'] . '/core/integrity_check" style="text-decoration:underline">integrity check</a>.', false);
    }
    jrCore_form_result();
}
