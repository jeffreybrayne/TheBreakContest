<div id="menu_content">
    <nav id="menu-wrap">
        <ul id="menu">

            {* Add in Cart link if jrFoxyCart module is installed *}
            {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                <li>
                    {jrCore_lang skin=$_conf.jrCore_active_skin id="2" default="cart" assign="cart"}
                    <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view">{jrCore_image image="cart24.png" width="24" height="24" alt=$cart}</a>
                    <span id="fc_minicart"><span id="fc_quantity"></span></span>
                </li>
            {/if}

            {if jrCore_module_is_active('jrSearch')}
                {jrCore_lang skin=$_conf.jrCore_active_skin id="3" default="search" assign="st"}
                <li><a onclick="jrSearch_modal_form();" title="{$st}">{$st}</a></li>
            {/if}

            {if jrUser_is_logged_in()}
                {if jrUser_is_admin()}
                    <li><a href="{$jamroom_url}/{jrCore_module_url module="jrCore"}/dashboard">dashboard</a></li>
                {/if}
                <li>
                    <a href="{$jamroom_url}/{jrUser_home_profile_key key="profile_url"}">{jrUser_home_profile_key key="profile_name"}</a>
                    <ul>
                        {jrCore_skin_menu template="menu.tpl" category="user"}
                    </ul>
                </li>
            {/if}


            {* Add additional menu categories here *}

            {if jrUser_is_logged_in()}
                {if jrUser_is_master()}
                    {jrCore_module_url module="jrCore" assign="core_url"}
                    {jrCore_module_url module="jrMarket" assign="murl"}
                    {jrCore_get_module_index module="jrCore" assign="url"}
                    <li>
                        <a href="{$jamroom_url}/{$core_url}/admin/global">ACP</a>
                        <ul>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/admin/tools">system tools</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/{$core_url}/{$url}">activity logs</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/cache_reset">reset caches</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/integrity_check">integrity check</a></li>
                                    <li><a href="{$jamroom_url}/{$murl}/system_update">system updates</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/system_check">system check</a></li>
                                </ul>
                            </li>
                            <li>
                                {jrCore_module_url module="jrProfile" assign="purl"}
                                {jrCore_module_url module="jrUser" assign="uurl"}
                                <a href="{$jamroom_url}/{$purl}/admin/tools">users</a>
                                <ul>
                                    <li><a href="{$jamroom_url}/{$purl}/quota_browser">quota browser</a></li>
                                    <li><a href="{$jamroom_url}/{$purl}/browser">profile browser</a></li>
                                    <li><a href="{$jamroom_url}/{$uurl}/browser">user account browser</a></li>
                                    <li><a href="{$jamroom_url}/{$uurl}/online">users online</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}">skin settings</a>
                                <ul>
                                    <li><a onclick="popwin('{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/readme.html','readme',600,500,'yes');">skin notes</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_menu">user menu editor</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/images/skin={$_conf.jrCore_active_skin}">skin images</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/style/skin={$_conf.jrCore_active_skin}">skin style</a></li>
                                    <li><a href="{$jamroom_url}/{$core_url}/skin_admin/templates/skin={$_conf.jrCore_active_skin}">skin templates</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                {/if}
            {else}
                {jrCore_module_url module="jrUser" assign="uurl"}
                {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
                    <li><a href="{$jamroom_url}/{$uurl}/signup">{jrCore_lang skin=$_conf.jrCore_active_skin id="4" default="create account"}</a></li>
                {/if}
                <li><a href="{$jamroom_url}/{$uurl}/login">{jrCore_lang skin=$_conf.jrCore_active_skin id="5" default="login"}</a></li>
            {/if}


        </ul>
    </nav>

</div>

{if jrCore_module_is_active('jrSearch')}
{* This is the search form - shows as a modal window when the search icon is clicked on *}
<div id="searchform" class="search_box" style="display:none;">
    {jrSearch_form class="form_text" value="Search Site" style="width:70%"}
    <div style="float:right;clear:both;margin-top:3px;">
        <a class="simplemodal-close">{jrCore_icon icon="close" size="16"}</a>
    </div>
    <div class="clear"></div>
</div>
{/if}