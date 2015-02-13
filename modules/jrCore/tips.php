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
 * @copyright 2014 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * help tips
 */
function jrCore_tips($_post, $_user, $_conf)
{
    $murl = jrCore_get_module_url('jrCore');
    $_out = array(
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => '#content > table',
            'title'    => 'Welcome to the ACP!',
            'text'     => 'Please take a few moments to follow this small introduction tour and get to know how the Admin Control Panel (ACP) works.<br><br>Click on the &quot;Start&quot; button to get started.',
            'position' => 'top center',
            'button'   => 'Start',
            'pointer'  => false
        ),
        array(
            'view'        => "{$murl}/admin/global",
            'selector'    => '#mtab',
            'title'       => 'Modules',
            'text'        => "You can view installed modules in the modules tab.<br><br>Modules provide <strong>specific functionality</strong> for your site.<br><br>New modules can be installed using the <a href=\"{$_conf['jrCore_base_url']}/market/browse\"><strong>Marketplace</strong></a>.",
            'position'    => 'bottom center',
            'video_url'   => 'https://www.youtube.com/watch?v=K9BTURpfFWU',
            'video_title' => 'Installing modules using the Marketplace',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/getting-started/1767/keeping-jamroom-up-to-date',
            'doc_title'   => 'Keeping Jamroom Up-To-Date'
        ),
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => 'dt:first',
            'title'    => 'Module Categories',
            'text'     => 'Modules are divided into categories to help keep things organized.<br><br>Click on a category header to expand the category and view the modules within.',
            'position' => 'bottom center'
        ),
        array(
            'view'      => "{$murl}/admin/global",
            'selector'  => '#stab',
            'title'     => 'Skins',
            'text'      => 'You can view installed Skins by clicking on the &quot;Skins&quot; tab.<br><br>Skins define the <strong>look and feel</strong> of your site, and can be customized to suit your needs.',
            'position'  => 'bottom center',
            'doc_url'   => 'https://www.jamroom.net/the-jamroom-network/documentation/skins/709/changing-the-site-skin',
            'doc_title' => 'Changing your Jamroom Skin'
        ),
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => '#tglobal',
            'title'    => 'Module Tabs',
            'text'     => 'After you have selected a module, the <strong>options</strong> provided by the module can be accessed by clicking on the desired tab.<br><br>Different modules provide different features, and not all tabs will be available for all modules.<br><br><strong>Global Config</strong> contains module settings that affect the module behavior system wide.',
            'position' => 'bottom center'
        ),
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => '#tquota',
            'title'    => 'Profile Quotas',
            'text'     => 'All User Profiles belong to a <strong>Profile Quota</strong>.  Profile quotas define the features and options that are available to your users.<br><br>The <strong>Quota Config</strong> section allows you to change quota settings for the selected module.',
            'position' => 'bottom center'
        ),
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => '#ttools',
            'title'    => 'Module Tools',
            'text'     => 'Some modules provide <strong>Tools</strong> that help you administer your system - module tools can always be found in the &quot;Tools&quot; tab.',
            'position' => 'bottom center'
        ),
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => '#tlanguage',
            'title'    => 'Language Strings',
            'text'     => 'User-facing language strings can be customized in the &quot;Language&quot; tab.<br><br><strong>Note:</strong> some modules may not provide user-facing functionality - those modules will not have language strings that can be customized.',
            'position' => 'bottom center'
        ),
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => '#timages',
            'title'    => 'Module Images',
            'text'     => 'If a module provides images, you can override the default images by uploading images of your own in the &quot;Images&quot; tab.',
            'position' => 'bottom center'
        ),
        array(
            'view'        => "{$murl}/admin/global",
            'selector'    => '#ttemplates',
            'title'       => 'Module Templates',
            'text'        => 'Many modules provide templates that allow you to customize how the module output will appear.  In the &quot;Templates&quot; tab you can customize the module templates to suit your needs.<br><br><strong>Note:</strong> all template modifications are stored in the database and are not overwritten when you upgrade a module.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'        => "{$murl}/admin/global",
            'selector'    => '#tinfo',
            'title'       => 'Module Info',
            'text'        => 'Every module has an Info tab - inside you can view information about the module, any requirements the module needs, view module notes, as well as disable and enable the module.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'        => "{$murl}/admin/global",
            'selector'    => '.form_select_item_jumper',
            'title'       => 'Jump between Modules',
            'text'        => 'You can quickly switch to any other module using the <strong>module jumper</strong>.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'        => "{$murl}/admin/global",
            'selector'    => '.form_admin_search',
            'title'       => 'Find what you\'re looking for',
            'text'        => 'Quickly find any Global Setting, Quota Config or Tool by using the <strong>admin quick search</strong> field.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'        => "{$murl}/admin/global",
            'selector'    => '.system_name_element_right > .form_help_button',
            'title'       => 'Get Help for Form Fields',
            'text'        => 'Most form fields have a &quot;help&quot; button to the right of the form field - click on it to get detailed help for the field including valid options, last updated by and reset options.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'     => "{$murl}/admin/global",
            'selector' => '#admin_container',
            'title'    => 'Thank you for using Jamroom',
            'text'     => 'Once you\'ve spent a few minutes in the ACP you\'ll find moving around is quick and easy - hopefully this small introduction helps you to understand how things are organized.<br><br><strong>Tip:</strong> You can restart this tour at any time by clicking on the &quot;Tour&quot; tab in the module.',
            'position' => 'top center',
            'button'   => 'Close',
            'pointer'  => false
        ),
        array(
            'view'     => "{$murl}/system_check",
            'selector' => '#admin_container',
            'title'    => 'System Check',
            'text'     => 'The System Check tool validates your server to ensure it is setup properly to run Jamroom.<br><br>Entries marked with a <strong>red</strong> result indicate a possible problem with that entry - check out the <strong>Note</strong> section to find help that addresses the issue.',
            'position' => 'top center',
            'button'   => 'Close',
            'cookie'   => false,
            'pointer'  => false
        )
    );

    // If we have a new install, let's show a small tip to create account on the front page
    if (!jrUser_is_logged_in() && jrCore_db_get_datastore_item_count('jrUser') === 0) {
        $_out[] = array(
            'view'        => $_conf['jrCore_base_url'] . '$',
            'selector'    => '#user-create-account',
            'title'       => 'Create your User Account',
            'text'        => 'Click on the <strong>Create Account</strong> to create your User Account.<br><br>The first User Account created is created as a <strong>Master Admin</strong>.',
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'group'       => 'visitor',
            'cookie'      => false,
        );
    }

    return $_out;
}
