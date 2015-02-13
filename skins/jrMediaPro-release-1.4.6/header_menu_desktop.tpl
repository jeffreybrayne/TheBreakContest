{assign var="active_color" value="#99CC00"}
<div id="menu_content">

<nav id="menu-wrap">
<ul id="menu">

{if $_conf.jrCore_maintenance_mode != 'on' || jrUser_is_master() || jrUser_is_admin()}
    <li><a href="{$jamroom_url}"{if isset($selected) && $selected == 'home'} style="color:{$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</a></li>
    <li>
        <a href="{$jamroom_url}/profiles"{if isset($selected) && $selected == 'lists'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="profiles"}</a>
        {if $_conf.jrMediaPro_artist_quota > 0 || $_conf.jrMediaPro_member_quota > 0}
            <ul>
                {if $_conf.jrMediaPro_artist_quota > 0}
                    <li><a href="{$jamroom_url}/artists">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</a></li>
                {/if}
                {if $_conf.jrMediaPro_member_quota > 0}
                    <li><a href="{$jamroom_url}/members">{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="members"}</a></li>
                {/if}
            </ul>
        {/if}
    </li>

    {if jrCore_module_is_active('jrAudio')}
        <li>
            <a href="{$jamroom_url}/music"{if isset($selected) && ($selected == 'music' || $selected == 'stations')} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="music"}</a>
            <ul>
                <li><a href="{$jamroom_url}/music/by_album">{jrCore_lang skin=$_conf.jrCore_active_skin id="185" default="By Album"}</a></li>
                <li><a href="{$jamroom_url}/music/by_plays">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="By Plays"}</a></li>
                {if jrCore_module_is_active('jrRating')}
                    <li><a href="{$jamroom_url}/music/by_ratings">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="By Rating"}</a></li>
                {/if}
                {if jrCore_module_is_active('jrCharts')}
                    <li>
                        <a href="{$jamroom_url}/music_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="Music"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts"}</a>
                        <ul>
                            <li><a href="{$jamroom_url}/music_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="53" default="Weekly"}</a></li>
                            <li><a href="{$jamroom_url}/music_charts_monthly">{jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="Monthly"}</a></li>
                            <li><a href="{$jamroom_url}/music_charts_yearly">{jrCore_lang skin=$_conf.jrCore_active_skin id="55" default="Yearly"}</a></li>
                        </ul>
                    </li>
                {/if}
                <li><a href="{$jamroom_url}/stations">{jrCore_lang skin=$_conf.jrCore_active_skin id="138" default="Stations"}</a></li>
                {if jrCore_module_is_active('jrSoundCloud')}
                    <li><a href="{$jamroom_url}/sound_cloud">{jrCore_lang skin=$_conf.jrCore_active_skin id="154" default="SoundCloud"}</a></li>
                {/if}
            </ul>
        </li>
    {elseif jrCore_module_is_active('jrSoundCloud')}
        <li><a href="{$jamroom_url}/sound_cloud">{jrCore_lang skin=$_conf.jrCore_active_skin id="154" default="SoundCloud"}</a></li>
    {/if}

    {if jrCore_module_is_active('jrVideo')}
        <li>
            <a href="{$jamroom_url}/videos"{if isset($selected) && ($selected == 'videos' || $selected == 'channels')} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="videos"}</a>
            <ul>
                <li><a href="{$jamroom_url}/videos/by_album">{jrCore_lang skin=$_conf.jrCore_active_skin id="185" default="By Album"}</a></li>
                <li><a href="{$jamroom_url}/videos/by_plays">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="By Plays"}</a></li>
                {if jrCore_module_is_active('jrRating')}
                    <li><a href="{$jamroom_url}/videos/by_ratings">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="By Rating"}</a></li>
                {/if}
                {if jrCore_module_is_active('jrCharts')}
                    <li>
                        <a href="{$jamroom_url}/video_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="Video"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts"}</a>
                        <ul>
                            <li><a href="{$jamroom_url}/video_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="53" default="Weekly"}</a></li>
                            <li><a href="{$jamroom_url}/video_charts_monthly">{jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="Monthly"}</a></li>
                            <li><a href="{$jamroom_url}/video_charts_yearly">{jrCore_lang skin=$_conf.jrCore_active_skin id="55" default="Yearly"}</a></li>
                        </ul>
                    </li>
                {/if}
                <li><a href="{$jamroom_url}/channels">{jrCore_lang skin=$_conf.jrCore_active_skin id="139" default="Channels"}</a></li>
                {if jrCore_module_is_active('jrYouTube')}
                    <li><a href="{$jamroom_url}/youtube_videos">{jrCore_lang skin=$_conf.jrCore_active_skin id="73" default="YouTube"}</a></li>
                {/if}
                {if jrCore_module_is_active('jrVimeo')}
                    <li><a href="{$jamroom_url}/vimeo_videos">{jrCore_lang skin=$_conf.jrCore_active_skin id="72" default="Vimeo"}</a></li>
                {/if}
            </ul>
        </li>
    {elseif jrCore_module_is_active('jrYouTube')}
        <li>
            <a href="{$jamroom_url}/youtube_videos">{jrCore_lang skin=$_conf.jrCore_active_skin id="73" default="YouTube"}</a>
            <ul>
                {if jrCore_module_is_active('jrVimeo')}
                    <li><a href="{$jamroom_url}/vimeo_videos">{jrCore_lang skin=$_conf.jrCore_active_skin id="72" default="Vimeo"}</a></li>
                {/if}
            </ul>
        </li>
    {elseif jrCore_module_is_active('jrVimeo')}
        <li><a href="{$jamroom_url}/vimeo_videos">{jrCore_lang skin=$_conf.jrCore_active_skin id="72" default="Vimeo"}</a></li>
    {/if}

    {if jrCore_module_is_active('jrGallery')}
        <li><a href="{$jamroom_url}/galleries"{if isset($selected) && $selected == 'galleries'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="29" default="galleries"}</a></li>
    {/if}

    {if jrCore_module_is_active('jrEvent')}
        <li>
            <a href="{$jamroom_url}/events"{if isset($selected) && $selected == 'events'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="30" default="gigs/events"}</a>
            <ul>
                <li><a href="{$jamroom_url}/events/by_upcoming">{jrCore_lang skin=$_conf.jrCore_active_skin id="68" default="upcoming"}</a></li>
                {if jrCore_module_is_active('jrRating')}
                    <li><a href="{$jamroom_url}/events/by_ratings">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="By Rating"}</a></li>
                {/if}
            </ul>
        </li>
    {/if}

    {if jrCore_module_is_active('jrBlog')}
        <li>
            <a href="{$jamroom_url}/blogs"{if isset($selected) && $selected == 'ban'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="140" default="blogs"}/{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News"}</a>
            <ul>
                <li><a href="{$jamroom_url}/site_blogs">{jrCore_lang skin=$_conf.jrCore_active_skin id="188" default="User"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="140" default="blogs"}</a></li>
                {if jrCore_module_is_active('jrPage')}
                    <li><a href="{$jamroom_url}/articles">{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="articles"}</a></li>
                {/if}
                <li><a href="{$jamroom_url}/news">{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="news"}</a></li>
            </ul>
        </li>
    {elseif jrCore_module_is_active('jrPage')}
        <li><a href="{$jamroom_url}/articles">{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="articles"}</a></li>
    {/if}

    {if jrCore_module_is_active('jrGroup')}
        <li><a href="{$jamroom_url}/groups"{if isset($selected) && $selected == 'groups'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="195" default="Groups"}</a></li>
    {/if}
    {if jrCore_module_is_active('jrGroupDiscuss')}
        <li><a href="{$jamroom_url}/discussions"{if isset($selected) && $selected == 'discussions'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="196" default="Discussions"}</a></li>
    {/if}

    {if isset($_conf.jrMediaPro_forum_profile_url) && strlen($_conf.jrMediaPro_forum_profile_url) > 0}
        {assign var="forum_url" value=$_conf.jrMediaPro_forum_profile_url}
    {/if}
    {if jrCore_module_is_active('jrForum') && isset($forum_url)}
        {jrCore_module_url module="jrForum" assign="furl"}
        <li><a href="{$jamroom_url}/{$forum_url}/{$furl}"{if isset($_post.option) && $_post.option == 'forum' && $_post._uri == $check_forum_url} style="color: {$active_color};"{/if}>{jrCore_lang module="jrForum" id="36" default="Forum"}</a></li>
    {/if}

    {if isset($_conf.jrMediaPro_docs_profile_url) && strlen($_conf.jrMediaPro_docs_profile_url) > 0}
        {assign var="doc_url" value=$_conf.jrMediaPro_docs_profile_url}
    {/if}
    {if jrCore_module_is_active('jrDocs') && isset($doc_url)}
        {jrCore_module_url module="jrDocs" assign="durl"}
        <li><a href="{$jamroom_url}/{$doc_url}/{$durl}"{if isset($_post.option) && $_post.option == 'documentation' && $_post._uri == $check_doc_url} style="color: {$active_color};"{/if}>{jrCore_lang module="jrDocs" id="53" default="Documentation"}</a></li>
    {/if}

{/if}


{* Add additional menu categories here *}


{if jrUser_is_logged_in()}
    {if jrUser_is_master()}
        {jrCore_module_url module="jrCore" assign="core_url"}
        {jrCore_get_module_index module="jrCore" assign="url"}
        <li>
            {if $_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes'}
                {assign var="acp_menu_item" value="no"}
            {else}
                {assign var="acp_menu_item" value="yes"}
            {/if}
            <a href="{$jamroom_url}/{$core_url}/admin/global"{if !isset($from_profile) && !isset($selected) && $acp_menu_item == 'yes'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="16" default="ACP"}</a>
            <ul>
                <li>
                    <a href="{$jamroom_url}/{$core_url}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="75" default="System Tools"}</a>
                    <ul>
                        <li>
                            <a href="{$jamroom_url}/{$core_url}/{$url}">{jrCore_lang skin=$_conf.jrCore_active_skin id="91" default="Activity Logs"}</a>
                            <ul>
                                <li><a href="{$jamroom_url}/{$core_url}/debug_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="186" default="Debug Logs"}</a></li>
                                <li><a href="{$jamroom_url}/{$core_url}/php_error_log">{jrCore_lang skin=$_conf.jrCore_active_skin id="187" default="PHP Error Logs"}</a></li>
                            </ul>
                        </li>
                        <li><a href="{$jamroom_url}/{$core_url}/cache_reset">{jrCore_lang skin=$_conf.jrCore_active_skin id="92" default="Reset Cache"}</a></li>
                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrImage"}/cache_reset">{jrCore_lang skin=$_conf.jrCore_active_skin id="145" default="Reset Image Cache"}</a></li>
                        <li><a href="{$jamroom_url}/{$core_url}/integrity_check">{jrCore_lang skin=$_conf.jrCore_active_skin id="93" default="Integrity Check"}</a></li>
                        <li><a href="{$jamroom_url}/marketplace/system_update">{jrCore_lang skin=$_conf.jrCore_active_skin id="189" default="System Updates"}</a></li>
                        <li><a href="{$jamroom_url}/{$core_url}/system_check">{jrCore_lang skin=$_conf.jrCore_active_skin id="97" default="System Check"}</a></li>
                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrBanned"}/browse">{jrCore_lang skin=$_conf.jrCore_active_skin id="94" default="Banned Items"}</a></li>
                        <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang skin=$_conf.jrCore_active_skin id="95" default="Skin Menu Editor"}</a></li>
                        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrSitemap"}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="96" default="Create Sitemap"}</a></li>
                    </ul>
                </li>
                <li>
                    {jrCore_module_url module="jrProfile" assign="purl"}
                    {jrCore_module_url module="jrUser" assign="uurl"}
                    <a href="{$jamroom_url}/{$purl}/admin/tools">{jrCore_lang skin=$_conf.jrCore_active_skin id="163" default="Users"}</a>
                    <ul>
                        <li><a href="{$jamroom_url}/{$purl}/quota_browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="158" default="Quota Browser"}</a></li>
                        <li><a href="{$jamroom_url}/{$purl}/browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="161" default="Profile Browser"}</a></li>
                        <li><a href="{$jamroom_url}/{$uurl}/browser">{jrCore_lang skin=$_conf.jrCore_active_skin id="159" default="User Accounts"}</a></li>
                        <li><a href="{$jamroom_url}/{$uurl}/online">{jrCore_lang skin=$_conf.jrCore_active_skin id="162" default="Who's Online"}</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="74" default="Skin Settings"}</a>
                    <ul>
                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="190" default="Skin Styles"}</a></li>
                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="191" default="Skin Images"}</a></li>
                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/language/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="193" default="Skin Langauge"}</a></li>
                        <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">{jrCore_lang skin=$_conf.jrCore_active_skin id="192" default="Skin Templates"}</a></li>
                        <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',800,500,'yes');">{jrCore_lang skin=$_conf.jrCore_active_skin id="194" default="Skin Notes"}</a></li>
                        <li><a href="{$jamroom_url}/{$core_url}/skin_menu">{jrCore_lang skin=$_conf.jrCore_active_skin id="95" default="Skin Menu Editor"}</a></li>
                    </ul>
                </li>
                <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">{jrCore_lang skin=$_conf.jrCore_active_skin id="17" default="dashboard"}</a></li>
            </ul>
        </li>
    {elseif jrUser_is_admin()}
        {if $_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes'}
            {assign var="acp_menu_item" value="no"}
        {else}
            {assign var="acp_menu_item" value="yes"}
        {/if}
        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard"{if !isset($from_profile) && !isset($selected) && $acp_menu_item == 'yes'} style="color: {$active_color};"{/if}>{jrCore_lang skin=$_conf.jrCore_active_skin id="17" default="dashboard"}</a></li>
    {/if}
{else}
    {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
        <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup">{jrCore_lang skin=$_conf.jrCore_active_skin id="2" default="create"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="3" default="account"}</a></li>
    {/if}
    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrUser"}/login">{jrCore_lang skin=$_conf.jrCore_active_skin id="6" default="login"}</a></li>
{/if}

{if jrUser_is_logged_in()}
    <li>
        {if $_post._uri != $check_forum_url && $_post._uri != $check_doc_url && isset($from_profile) && $from_profile == 'yes' || ($_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes')}
            {assign var="artist_menu_item" value="yes"}
        {else}
            {assign var="artist_menu_item" value="no"}
        {/if}
        <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}"{if isset($artist_menu_item) && $artist_menu_item == 'yes'} style="color: {$active_color};"{/if}>{jrUser_home_profile_key key="profile_name"}</a>
        <ul>
            {jrCore_skin_menu template="menu.tpl" category="user"}
        </ul>
    </li>
{/if}

{* Add in Cart link if jrFoxyCart module is installed *}
<!-- Cart contents -->
{if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
    {if jrUser_is_logged_in()}
        <li>
            <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_image image="cart.png" width="24" height="24" alt="cart"}<span id="fc_minicart"><span id="fc_quantity" class="hl-4"></span></span></a>
        </li>
    {/if}
{/if}

{if jrCore_module_is_active('jrSearch')}
    <li><a onclick="jrSearch_modal_form();" title="Site Search">{jrCore_image image="magnifying_glass.png" width="24" height="24" alt="search"}</a></li>
{/if}
</ul>
</nav>

</div>
