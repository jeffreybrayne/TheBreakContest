{jrCore_include template="meta.tpl"}

<body>

{if jrCore_is_mobile_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

<div id="header">
    <div id="header_content">

        {* Logo *}
        {if jrCore_is_mobile_device()}
            <div id="main_logo">
                {jrCore_image id="mmt" skin="jrNewLucid" image="menu.png" alt="menu"}
                <a href="{$jamroom_url}">{jrCore_image image="logo.png" width="170" height="56" class="jlogo" alt=$_conf.jrCore_system_name custom="logo"}</a>
            </div>
        {else}
            <div id="main_logo">
                <a href="{$jamroom_url}">{jrCore_image image="logo.png" width="170" height="56" alt=$_conf.jrCore_system_name custom="logo"}</a>
            </div>
            {jrCore_include template="header_menu_desktop.tpl"}
        {/if}

    </div>
</div>

{if $show_bg == 1}
    <div class="banner"></div>
{else}
    <div class="spacer"></div>
{/if}

<div id="wrapper">
    <div id="content">

        <noscript>
            <div class="item error center" style="margin:12px">
                This site requires Javascript to function properly - please enable Javascript in your browser
            </div>
        </noscript>

        <!-- end header.tpl -->
