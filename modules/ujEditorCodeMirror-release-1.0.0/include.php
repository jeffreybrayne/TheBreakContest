<?php
/**
 * Jamroom 5 Editor Code Mirror module
 *
 * copyright 2003 - 2015
 * by Ultrajam
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * Ultrajam
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
 * Jamroom 5 EditorCodeMirror module
 *
 * copyright 2014 - 2015
 * by SteveX at ultrabubble
 *
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**

	In form_editor.tpl or its skin override jrCore_form_editor.tpl add the following:

    codemirror: {
    	path: '{$_conf.jrCore_base_url}/modules/ujEditorCodeMirror/contrib/codemirror/'
    },

    And replace the word "code" in both plugins: and toolbar: with the following:

    {if $ujeditorcodemirror}ujeditorcodemirror{else}code{/if}

    The ujEditorCodeMirror module uses its own instance of CodeMirror as it requires the latest version.
    https://github.com/codemirror/codemirror

    It implements the TinyMCE plugin by Arjan Haverkamp
    http://www.avoid.org/codemirror-for-tinymce4/

 */

/**
 * meta
 */
function ujEditorCodeMirror_meta()
{
    $_tmp = array(
        'name'        => 'Editor Code Mirror',
        'url'         => 'editorcodemirror',
        'version'     => '1.0.0',
        'developer'   => 'Ultrajam, &copy;' . strftime('%Y'),
        'description' => 'Provides CodeMirror highlighting to the TinyMCE Source Editor.',
        'category'    => 'site',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function ujEditorCodeMirror_init()
{
    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'ujEditorCodeMirror', 'off');

    // We have a custom editor button we provide
    jrCore_register_module_feature('jrCore', 'editor_button', 'ujEditorCodeMirror', 'on');

    return true;
}

