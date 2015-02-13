{jrCore_include template="meta.tpl"}

<body{if isset($spt) && $spt == 'home'} class="loading"{/if}>

{if jrCore_is_mobile_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

{* TOP BAR *}
{if !jrCore_is_mobile_device()}
<div id="top-bar">
    <div class="top-bar-wrapper">
        <div class="container">
            <div class="row">
                <div class="col8">
                    <div class="welcome">

                    {if jrUser_is_logged_in()}
                        <span style="color:#999999;">{jrCore_lang skin=$_conf.jrCore_active_skin id="102" default="Welcome"}&nbsp;&nbsp;</span><span class="bold hl-1">{jrUser_home_profile_key key="profile_name"}</span>&nbsp;|&nbsp;
                        {if jrCore_module_is_active('jrPrivateNote')}
                            {if isset($_user.user_jrPrivateNote_unread_count) && $_user.user_jrPrivateNote_unread_count > 0}
                                <a href="{$jamroom_url}/note/notes" target="_top"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="103" default="In Box"}</span></a> <span class="hl-3">({$_user.user_jrPrivateNote_unread_count})</span> |
                            {else}
                                <a href="{$jamroom_url}/note/notes" target="_top"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="103" default="In Box"}</span></a> |
                            {/if}
                        {/if}
                        <a href="{$jamroom_url}/user/logout" target="_top" onclick="if (!confirm('Are you Sure you want to Log out?')) return false;"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="5" default="Logout"}</span></a>
                    {else}
                        <span style="color:#999999;">{jrCore_lang skin=$_conf.jrCore_active_skin id="7" default="Welcome Guest"}!</span> | <a href="{$jamroom_url}/user/login" target="_top"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="6" default="Login"}</span></a>
                    {/if}

                        {if jrUser_is_logged_in()}
                            | <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}" title="{jrCore_lang skin=$_conf.jrCore_active_skin id="102" default="Welcome"} {jrUser_home_profile_key key="profile_name"}"><span class="page-welcome" style="padding:2px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="104" default="Your Home"}</span></a>
                        {/if}

                    </div>
                </div>
                <div class="col4 last">
                    <div class="flags">
                        <a href="?set_user_language=en-US"><img src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/flags/us.png" alt="US" title="English US"></a>
                        <a href="?set_user_language=es-ES"><img src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/flags/es.png" alt="ES" title="Spanish"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}

{* LOGO AND AD HEADER *}
<div id="header">

    <div id="header_content">

        <div class="container">

            <div class="row">

                <div class="col6">
                    {* Logo *}
                    <div id="main_logo">
                        {if jrCore_is_mobile_device()}
                            {jrCore_image id="mmt" skin="jbTheBreak" image="menu.png" alt="menu"}
                            {jrCore_image image="logo.png" class="img_scale" alt=$_conf.jrCore_system_name title=$_conf.jrCore_system_name style="max-width:225px;max-height:48px;" custom="logo"}
                        {else}
                            <a href="{$jamroom_url}">{jrCore_image image="logo.png" class="img_scale" alt=$_conf.jrCore_system_name title=$_conf.jrCore_system_name style="max-width:375px;max-height:80px;" custom="logo"}</a>
                        {/if}
                    </div>
                </div>
                <div class="col6 last">
                    <div class="logo-ads">
                        {if $_conf.jbTheBreak_ads_off != 'on'}
                            {if isset($_conf.jbTheBreak_google_ads) && $_conf.jbTheBreak_google_ads == 'on'}
                                <script type="text/javascript"><!--
                                    google_ad_client = "{$_conf.jbTheBreak_google_id}";
                                    google_ad_width = 468;
                                    google_ad_height = 60;
                                    google_ad_format = "468x60_as";
                                    google_ad_type = "text_image";
                                    google_ad_channel ="";
                                    google_color_border = "CCCCCC";
                                    google_color_bg = "CCCCCC";
                                    google_color_link = "FF9900";
                                    google_color_text = "333333";
                                    google_color_url = "333333";
                                    //--></script>
                                <script type="text/javascript"
                                        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                                </script>
                            {elseif isset($_conf.jbTheBreak_top_ad) && strlen($_conf.jbTheBreak_top_ad) > 0}
                                {$_conf.jbTheBreak_top_ad}
                            {else}
                                <a href="http://www.jamroom.net/" target="_blank">{jrCore_image image="468x60_banner.png" alt="486x60 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:468px;max-height:60px;"}</a>
                            {/if}
                        {/if}
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

{* MAIN MENU *}
{if !jrCore_is_mobile_device()}
    {jrCore_include template="header_menu_desktop.tpl"}
{/if}


<div id="wrapper">

{* This is the search form - shows as a modal window when the search icon is clicked on *}
    <div id="searchform" class="search_box" style="display:none;">
        <div class="float-right ml10"><input type="button" class="simplemodal-close form_button" value="x"></div>
        <span class="media_title">{$_conf.jrCore_system_name} Site Search</span><br><br>
        {jrSearch_form class="form_text" value="Search Site" style="width:70%"}
        <div class="clear"></div>
    </div>

    {if $spt == 'home' ||  $spt == 'profiles'}
    {else}
        <div id="content">
    {/if}
        <!-- end header.tpl -->

        {* SEARCH PAGE HEADER *}
        {if isset($_post.module) && $_post.option != 'admin' && ($_post.module == 'jrRecommend' || $_post.module == 'jrSearch')}
        <div class="container">

            <div class="row">

                <div class="col9">
                    <div class="body_1 mr5">

                        <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="124" default="Search Results"}</div>
                            <div class="body_5">
        {/if}
