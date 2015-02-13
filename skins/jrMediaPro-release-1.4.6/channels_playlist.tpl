{jrCore_module_url module="jrPlaylist" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="container">

            <div class="row">
                <div class=" col12 last">
                    <div class="p5">
                        {jrPlaylist_util mode="embed_playlist" playlist_id=$item._item_id template="channel_playlist.tpl"}
                    </div>
                </div>
            </div>
        </div>

        <div class="body_5 page" style="margin-top:10px;margin-right:auto;">
            <div class="container">
                <div class="row">
                    <div class="col2">
                        <div class="center p5 middle" style="margin:0 auto;">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="large" crop="auto" width="72" height="72" alt=$item.playlist_title title=$item.playlist_title class="iloutline"}</a>
                        </div>
                    </div>
                    <div class="col4">
                        <div class="left" style="padding-top:15px;padding-left:5px;">
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="120" default="Title"}:</span> <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{$item.playlist_title}</a></h3><br>
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="149" default="Owner"}:</span> <span class="hl-4 capital">{if $item.profile_id == '1'}{jrCore_lang skin=$_conf.jrCore_active_skin id="8" default="Site"} {jrCore_lang skin=$_conf.jrCore_active_skin id="152" default="Channel"}</span>{else}<span class="capital bold"><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>{/if}<br>
                            <span class="capital bold">{jrCore_lang module="jrPlaylist" id="10" default="Tracks"}:</span> <span class="hl-3">{$item.playlist_count}</span><br>
                        </div>
                    </div>
                    <div class="col3">
                        {if jrCore_module_is_active('jrRating')}
                        <div class="left" style="padding-top:15px;padding-left:5px;">
                            <div style="display:table;">
                                <div style="display:table-row;">
                                    <div style="display:table-cell;width:10%;padding:5px;text-align:left;">
                                        <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="147" default="Rating"}:&nbsp;</span>
                                    </div>
                                    <div style="display:table-cell;width:90%;vertical-align:middle;">
                                        {jrCore_module_function function="jrRating_form" type="star" module="jrPlaylist" index="1" item_id=$item._item_id current=$item.playlist_rating_1_average_count|default:0 votes=$item.playlist_rating_1_count|default:0 }
                                    </div>
                                </div>
                            </div>
                        </div>
                        {else}
                        &nbsp;
                        {/if}
                    </div>
                    <div class=" col3 last">
                        <div class="nowrap float-right middle">
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="69" default="Launch" assign="popup_title1"}
                            {assign var="chnnl_pop_title" value="`popup_title1` `$item.playlist_title`"}
                            <a onclick="popwin('{$jamroom_url}/channel_player/playlist_id={$item._item_id}/title={$item.playlist_title}/autoplay=true','channel_player',480,490,true);">{jrCore_image image="launch_button.png" alt=$chnnl_pop_title title=$chnnl_pop_title onmouseover="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/launch_button_hover.png');" onmouseout="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/launch_button.png');"}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{/if}
