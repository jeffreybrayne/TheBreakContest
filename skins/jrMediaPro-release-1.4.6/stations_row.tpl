{jrCore_module_url module="jrPlaylist" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
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
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="149" default="Owner"}:</span> <span class="hl-4 capital">{if $item.profile_id == '1'}{jrCore_lang skin=$_conf.jrCore_active_skin id="8" default="Site"} {jrCore_lang skin=$_conf.jrCore_active_skin id="150" default="Station"}</span>{else}<span class="capital bold"><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>{/if}<br>
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
                        <div class="block_config">
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="148" default="Play" assign="title1"}
                            {assign var="plybttn_title" value="`$title1` `$item.playlist_title`"}
                            <a href="{$jamroom_url}/stations/{$item.playlist_title_url}/autoplay=true">{jrCore_image image="button_player_play.png" alt=$plybttn_title title=$plybttn_title onmouseover="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/button_player_play_hover.png');" onmouseout="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/button_player_play.png');"}</a>

                            {jrCore_lang skin=$_conf.jrCore_active_skin id="69" default="Launch" assign="popup_title1"}
                            {assign var="chnnl_pop_title" value="`popup_title1` `$item.playlist_title`"}
                            <a onclick="popwin('{$jamroom_url}/radio_player/playlist_id={$item._item_id}/title={$item.playlist_title}/autoplay=true','radio_player',345,240,true);">{jrCore_image image="launch_button.png" alt=$plybttn_title title=$plybttn_title onmouseover="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/launch_button_hover.png');" onmouseout="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/launch_button.png');"}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
    {if $info.total_pages > 1}
        <div class="block">
            <table style="width:100%;">
                <tr>

                    <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                        {if isset($info.prev_page) && $info.prev_page > 0}
                            <a onclick="jrLoad('#newest_station_div','{$jamroom_url}/stations_list/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#newstation').offset().top });return false;"><span class="button-arrow-previous">&nbsp;</span></a>
                        {else}
                            <span class="button-arrow-previous-off">&nbsp;</span>
                        {/if}
                    </td>

                    <td class="body_4 p5 middle" style="width:50%;text-align:center;border:1px solid #282828;">
                        {if $info.total_pages <= 5 || $info.total_pages > 500}
                            {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#newest_station_div','{$jamroom_url}/stations_list/p=' +sel);$('html, body').animate({ scrollTop: $('#newstation').offset().top });return false;">
                                    {for $pages=1 to $info.total_pages}
                                        {if $info.page == $pages}
                                            <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                        {else}
                                            <option value="{$pages}"> {$pages}</option>
                                        {/if}
                                    {/for}
                                </select>&nbsp;/&nbsp;{$info.total_pages}
                            </form>
                        {/if}
                    </td>

                    <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                        {if isset($info.next_page) && $info.next_page > 1}
                            <a onclick="jrLoad('#newest_station_div','{$jamroom_url}/stations_list/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#newstation').offset().top });return false;"><span class="button-arrow-next">&nbsp;</span></a>
                        {else}
                            <span class="button-arrow-next-off">&nbsp;</span>
                        {/if}
                    </td>

                </tr>
            </table>
        </div>
    {/if}
{/if}
