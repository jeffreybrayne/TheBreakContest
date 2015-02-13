<?php
/**
 * Jamroom 5 jrMediaPro skin
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
 * can be found in the "contrib" directory within this skin.
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
 * jrMediaPro_skin_config
 */
function jrMediaPro_skin_config()
{
    // Admin Forum URL Name
    $_tmp = array(
        'name'     => 'forum_profile_url',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Site Forum',
        'help'     => 'Set to the profile URL name of the Forum you want to use on the main nav menu.<br><span class="form_help_small">Note: If you do not have the jrForum module installed/activated, leave this field blank.</span>',
        'section'  => 'Main',
        'order'    => 1
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Admin Documentation URL Name
    $_tmp = array(
        'name'     => 'docs_profile_url',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Site Docs/FAQ',
        'help'     => 'Set to the profile URL name of the Documentation/FAQ you want to use on the main nav menu.<br><span class="form_help_small">Note: If you do not have the jrDocs module installed/activated, leave this field blank.</span>',
        'section'  => 'Main',
        'order'    => 2
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Admin Quota ID
    $_tmp = array(
        'name'     => 'admin_quota',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Admin Quota',
        'help'     => 'Enter the Admin quota ID.',
        'section'  => 'Main',
        'order'    => 3
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Artist Quota ID
    $_tmp = array(
        'name'     => 'artist_quota',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Artist Quota',
        'help'     => 'Enter the Artist quota ID. Set to 0 if there is no artist quota.',
        'section'  => 'Main',
        'order'    => 4
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Member Quota ID
    $_tmp = array(
        'name'     => 'member_quota',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Member Quota',
        'help'     => 'Enter the Member quota ID. Set to 0 if there is no member quota.',
        'section'  => 'Main',
        'order'    => 5
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Default character settings for letter searches
    $_tmp = array(
        'name'     => 'letter_alphabet',
        'type'     => 'text',
        'default'  => 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z',
        'validate' => 'not_empty',
        'label'    => 'Search Alphabet',
        'help'     => 'Default character settings for letter searches',
        'section'  => 'Main',
        'order'    => 6
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Featured Slider ID's
    $_tmp = array(
        'name'     => 'slider_profile_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Slider',
        'help'     => 'Enter up to 21 profile ID\'s you want to show in the Featured Image Slider on the index page.<br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3...</span>',
        'section'  => 'Featured',
        'order'    => 10
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Featured Profile ID's
    $_tmp = array(
        'name'     => 'profile_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Artists',
        'help'     => 'Enter up to 10 artist ID\'s you want to show in the Featured Artist Image Slider on the index page.<br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3...</span>',
        'section'  => 'Featured',
        'order'    => 11
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Today's Featured Profile ID
    $_tmp = array(
        'name'     => 'todays_featured',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Today\'s Featured Artist',
        'help'     => 'Enter the artist ID you want to show in the Today\'s Featured Artist on the side.',
        'section'  => 'Featured',
        'order'    => 12
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Today's Featured Member ID
    $_tmp = array(
        'name'     => 'featured_member',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Today\'s Featured Member',
        'help'     => 'Enter the member ID you want to show in the Today\'s Featured Member on the side of the Members page.',
        'section'  => 'Featured',
        'order'    => 13
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Featured Song ID
    $_tmp = array(
        'name'     => 'featured_song',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Song',
        'help'     => 'Enter the Song ID you want to show in the Featured Song section on the side of the Music page.',
        'section'  => 'Featured',
        'order'    => 14
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Featured Video ID
    $_tmp = array(
        'name'     => 'featured_video',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Video',
        'help'     => 'Enter the Video ID you want to show in the Featured Video section on the side of the Videos page.',
        'section'  => 'Featured',
        'order'    => 15
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Blog Profile ID
    $_tmp = array(
        'name'     => 'blog_profile',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Blog Profile',
        'help'     => 'By default the admin blog is used to show site news - set this profile_id to a valid profile_id if you want to use a different profile for blogs.',
        'section'  => 'Blog',
        'order'    => 20
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Index Blog Limit
    $_tmp = array(
        'name'     => 'index_blog_limit',
        'type'     => 'text',
        'default'  => '4',
        'validate' => 'not_empty',
        'label'    => 'Blog Limit',
        'help'     => 'Enter the number of blogs you would like to show on the home page.</span>',
        'section'  => 'Blog',
        'order'    => 21
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // House Radio
    $_tmp = array(
        'name'     => 'show_radio',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show House Radio',
        'help'     => 'Enabling this option will show the site radio section on the side section.<br><span class="form_help_small">Note: Disable this option to hide the site radio section.</span>',
        'section'  => 'Playlists',
        'order'    => 30
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // House Radio Title
    $_tmp = array(
        'name'     => 'radio_title',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Radio Title',
        'help'     => 'Enter the title of the playlist you want to use as the House Radio.',
        'section'  => 'Playlists',
        'order'    => 31
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Show House Channel
    $_tmp = array(
        'name'     => 'show_tv',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show House Channel',
        'help'     => 'Enabling this option will show the House Channel section.<br><span class="form_help_small">Note: Disable this option to hide the House Channel section.</span>',
        'section'  => 'Playlists',
        'order'    => 32
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // House Channel ID
    $_tmp = array(
        'name'     => 'tv_title',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'House Channel Title',
        'help'     => 'Enter the title of the playlist you want to use as the House Channel.',
        'section'  => 'Playlists',
        'order'    => 33
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Player Auto Play
    $_tmp = array(
        'name'     => 'auto_play',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Auto Play',
        'help'     => 'Enabling this option will turn on your players auto play feature.<br><span class="form_help_small">Note: This is for the following profile players only. Audio, Playlist and Video.</span>',
        'section'  => 'Playlists',
        'order'    => 34
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Ads Off
    $_tmp = array(
        'name'     => 'ads_off',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Hide Ads',
        'help'     => 'Check this checkbox to hide all the ads on the site.',
        'section'  => 'Ads',
        'order'    => 40
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Google Ads
    $_tmp = array(
        'name'     => 'google_ads',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Use Google Ads',
        'help'     => 'Enabling this option will show Google Ads on the site.<br><span class="form_help_small">Note: Disable this option to use the Ad fields below.</span>',
        'section'  => 'Ads',
        'order'    => 41
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Google ID
    $_tmp = array(
        'name'     => 'google_id',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Google ID',
        'help'     => 'Enter your Google Ads ID.',
        'section'  => 'Ads',
        'order'    => 42
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Top Ad 468x60
    $_tmp = array(
        'name'     => 'top_ad',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => '468x60 Top Ad',
        'help'     => 'Enter your Ad code here for the top 468x60 Ad.',
        'section'  => 'Ads',
        'order'    => 43
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Side Ad 180x150
    $_tmp = array(
        'name'     => 'side_ad',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => '180x150 Side Ad',
        'help'     => 'Enter your Ad code here for the side 180x150 Ad.',
        'section'  => 'Ads',
        'order'    => 44
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Bottom Ad 728x90
    $_tmp = array(
        'name'     => 'bottom_ad',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => '728x90 Bottom Ad',
        'help'     => 'Enter your Ad code here for the bottom 728x90 Ad.',
        'section'  => 'Ads',
        'order'    => 45
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Require Images
    $_tmp = array(
        'name'     => 'require_images',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Require Images',
        'help'     => 'Enabling this option will hide entries without an image associated.',
        'section'  => 'Extras',
        'order'    => 50
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Default Pagebreak
    $_tmp = array(
        'name'     => 'default_pagebreak',
        'type'     => 'text',
        'default'  => '10',
        'validate' => 'not_empty',
        'label'    => 'Default Pagebreak',
        'help'     => 'This is the default pagebreak for the Charts, Songs, Videos and Concerts pages.',
        'section'  => 'Extras',
        'order'    => 51
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Artist Page Pagebreak
    $_tmp = array(
        'name'     => 'default_artist_pagebreak',
        'type'     => 'text',
        'default'  => '24',
        'validate' => 'not_empty',
        'label'    => 'Artist Page Pagebreak',
        'help'     => 'This is the pagebreak for the Artist page.<br><span class="form_help_small">Note: The number should be divisible by 4!</span>',
        'section'  => 'Extras',
        'order'    => 52
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Gallery Page Pagebreak
    $_tmp = array(
        'name'     => 'default_gallery_pagebreak',
        'type'     => 'text',
        'default'  => '12',
        'validate' => 'not_empty',
        'label'    => 'Gallery Page Pagebreak',
        'help'     => 'This is the pagebreak for the Gallery page.<br><span class="form_help_small">Note: The number should be divisible by 4!</span>',
        'section'  => 'Extras',
        'order'    => 53
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Groups Page Pagebreak
    $_tmp = array(
        'name'     => 'default_group_pagebreak',
        'type'     => 'text',
        'default'  => '12',
        'validate' => 'not_empty',
        'label'    => 'Group Page Pagebreak',
        'help'     => 'This is the pagebreak for the Group page.',
        'section'  => 'Extras',
        'order'    => 54
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Discussuions Page Pagebreak
    $_tmp = array(
        'name'     => 'default_discuss_pagebreak',
        'type'     => 'text',
        'default'  => '12',
        'validate' => 'not_empty',
        'label'    => 'Discussions Page Pagebreak',
        'help'     => 'This is the pagebreak for the Discussions page.',
        'section'  => 'Extras',
        'order'    => 55
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Events Page Pagebreak
    $_tmp = array(
        'name'     => 'default_events_pagebreak',
        'type'     => 'text',
        'default'  => '6',
        'validate' => 'not_empty',
        'label'    => 'Gig/Events Page Pagebreak',
        'help'     => 'This is the pagebreak for the Gig/Events page.',
        'section'  => 'Extras',
        'order'    => 56
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Articles Page Pagebreak
    $_tmp = array(
        'name'     => 'default_articles_pagebreak',
        'type'     => 'text',
        'default'  => '8',
        'validate' => 'not_empty',
        'label'    => 'Articles Page Pagebreak',
        'help'     => 'This is the pagebreak for the Articles page.',
        'section'  => 'Extras',
        'order'    => 57
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Video Categories
    $_tmp = array(
        'name'     => 'v_category',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Video Category',
        'help'     => 'Enabling this option will show the video category search tab on the video page. Note: You must have a video category field created for this to work. Check your skin readme file for instructions.',
        'section'  => 'Profile',
        'order'    => 60
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Profile Comments
    $_tmp = array(
        'name'     => 'profile_comments',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Profile Comments',
        'help'     => 'Enabling this option will show profile comments on the profile homepage.',
        'section'  => 'Profile',
        'order'    => 61
    );
    jrCore_register_setting('jrMediaPro',$_tmp);

    // Social Media
    $num = 70;
    foreach (array('twitter', 'facebook', 'google', 'linkedin') as $network) {

        // App Store URL
        $_tmp = array(
            'name'     => "{$network}_name",
            'type'     => 'text',
            'default'  => '',
            'validate' => 'printable',
            'label'    => ucfirst($network) . " profile",
            'help'     => "If you have an account for your site on " . ucfirst(str_replace('_', ' ', $network)) .", enter the profile name and the network icon will show in your footer.  Leave blank to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrMediaPro', $_tmp);
    }

    return true;
}
?>
