<?php
/**
 * Jamroom 5 Profile Blog module
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
 * Custom Form Install
 */
function jrBlog_install()
{
    $txt = <<<EOT

    Thank you for installing Jamroom!

    Jamroom is an easy to use, and easy to customize CMS that centers around <b>User Profiles</b>.  With excellent media support, thousands of sites have been using Jamroom to power their social media needs for over 10 years.

    When logged in to your new site, click on the &quot;Tour&quot; tabs available in many of the modules - these small tips and tours will help you quickly get up to speed on how Jamroom works and how it can be customized to suit your needs.

    Make sure and join us on Jamroom.net:

    https://www.jamroom.net

    and let us know how you're using Jamroom - we love getting feedback on how we can make Jamroom better.

    Thanks again for installing Jamroom - we hope you enjoy using it and hope to see you online!

    - The Jamroom Team

EOT;

    if (jrCore_db_number_rows('jrBlog', 'item') === 0) {

        // Create first blog entry
        $_dt = array(
            'blog_title'        => 'Welcome to Jamroom',
            'blog_title_url'    => jrCore_url_string('Welcome to Jamroom'),
            'blog_publish_date' => time(),
            'blog_text'         => $txt,
            'blog_category'     => 'welcome',
            'blog_category_url' => 'welcome',
            'blog_readmore'     => 0
        );
        $_cr = array(
            '_user_id'    => 1,
            '_profile_id' => 1
        );
        jrCore_db_create_item('jrBlog', $_dt, $_cr);
    }
    return true;
}

