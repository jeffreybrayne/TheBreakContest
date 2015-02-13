{* STATS | ONLINE *}
<table class="menu_tab">
    <tr>
        {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
            <td>
                <div id="site_stats" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/stats');jrSetActive('#site_stats');">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
            </td>
            <td class="spacer">&nbsp;</td>
            <td>
                <div id="online" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/online');jrSetActive('#online');">{jrCore_lang skin=$_conf.jrCore_active_skin id="113" default="online"}</div>
            </td>
            <td class="spacer">&nbsp;</td>
            <td>
                <div id="default" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/music_genres');jrSetActive('#default');">{jrCore_lang skin=$_conf.jrCore_active_skin id="182" default="Genres"}</div>
            </td>
        {else}
            <td>
                <div id="default" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/stats');jrSetActive('#default');">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
            </td>
            <td class="spacer">&nbsp;</td>
            <td>
                <div id="online" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/online');jrSetActive('#online');">{jrCore_lang skin=$_conf.jrCore_active_skin id="113" default="online"}</div>
            </td>
            <td class="spacer">&nbsp;</td>
            <td>
                <div id="genre_search" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/music_genres');jrSetActive('#genre_search');">{jrCore_lang skin=$_conf.jrCore_active_skin id="182" default="Genres"}</div>
            </td>
        {/if}
    </tr>
</table>

<div id="stats" class="body_2 mb20">
    {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
        <!-- Search Song Genre-->
        <h3>{jrCore_lang module="jrAudio" id="12" default="Genre"} {jrCore_lang skin=$_conf.jrCore_active_skin id="24" default="Search"}</h3>
        <br />
        <form class="margin" method="post" action="{$jamroom_url}/music">
            <input type="hidden" name="search_area" value="audio_genre">
            <select class="form_select" name="search_string" style="width:100%; font-size:13px;" onchange="this.form.submit()">
                {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                    <option value="{$_post.search_string}">{$_post.search_string}</option>
                {else}
                    <option value="">{jrCore_lang skin=$_conf.jrCore_active_skin id="183" default="Select A Genre"}</option>
                {/if}
                {jrCore_list module="jrAudio" order_by="audio_genre asc" group_by="audio_genre" limit="200" template="music_genres_row.tpl"}
            </select>
        </form>
    {else}
        <div style="width:90%;display:table;margin:0 auto;">

            {capture name="template" assign="stats_tpl"}
            {literal}
                {foreach $_stats as $title => $_stat}
                <div style="display:table-row">
                    <div class="capital bold" style="display:table-cell">{$title}</div>
                    <div class="hl-3" style="width:5%;display:table-cell;text-align:right;">{$_stat.count}</div>
                </div>
                {/foreach}
            {/literal}
            {/capture}

            {jrCore_stats template=$stats_tpl}

        </div>
    {/if}
</div>

<h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="111" default="Song"}</h3>
<div class="mb20 pt10">
    {* FEATURED SONG ROW *}
    {capture name="row_template" assign="featured_song"}
    {literal}
        {if isset($_items)}
        {jrCore_module_url module="jrAudio" assign="murl"}
        {foreach from=$_items item="row"}
        <div class="center p5">
            <a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$row._item_id size="medium" crop="auto" class="iloutline img_shadow" alt=$row.audio_title}</a><br>
            <div class="spacer10"></div>
            <div class="table_div" style="max-width: 200px; margin: 0 auto;">
                <div class="table_div_row">
                    <div class="table_div_cell" style="text-align: right; vertical-align: middle;">
                        {if $row.audio_file_extension == 'mp3'}
                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$row image="button_player"}&nbsp;
                        {else}
                        {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                        <a href="{$jamroom_url}/{$murl}/download/audio_file/{$row._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                        {/if}
                    </div>
                    <div class="table_div_cell" style="text-align: left; padding-left: 10px;">
                        <h3><a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.audio_title_url}" title="{$row.audio_title}">{$row.audio_title|truncate:20:"...":false}</a></h3><br>
                        <h4>By&nbsp;<a href="{$jamroom_url}/{$row.profile_url}">{$row.profile_name}</a></h4><br>
                    </div>
                </div>
            </div>
        </div>
        {/foreach}
        {/if}
    {/literal}
    {/capture}
    {* FEATURED SONG FUNCTION *}
    {if isset($_conf.jrMediaPro_featured_song) && strlen($_conf.jrMediaPro_featured_song) > 0}
        {jrCore_list module="jrAudio" order_by="_item_id desc" limit="1" search1="profile_active = 1" search2="_item_id = `$_conf.jrMediaPro_featured_song`" template=$featured_song}
    {else}
        {jrCore_list module="jrAudio" order_by="audio_file_stream_count numerical_desc" limit="1" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template=$featured_song}
    {/if}
</div>

{* HOUSE STATION *}
{if isset($spt) && ($spt == 'music' || $spt == 'galleries' || $spt == 'home' || $spt == 'artist' || $spt == 'member' || $spt == 'profiles')}
    {if isset($_conf.jrMediaPro_show_radio) && $_conf.jrMediaPro_show_radio == 'on'}
        {if isset($_conf.jrMediaPro_radio_title) && strlen($_conf.jrMediaPro_radio_title) > 0}
            {jrCore_list module="jrPlaylist" profile_id="0" order_by="_created desc" search1="playlist_title = `$_conf.jrMediaPro_radio_title`" limit="1" template="index_radio.tpl"}
        {else}
            {if jrUser_is_logged_in()}
                {if jrUser_is_master()}
                    <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="43" default="radio"}</h3>
                    <div class="body_2b normal p20 mb20">
                        Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin={$_conf.jrCore_active_skin}">Settings</a> <b>"Radio Title"</b> is not set!
                    </div>
                {/if}
            {/if}
        {/if}
    {/if}
{/if}

{* SITE WIDE TAG CLOUD *}
{jrTags_cloud height="300" assign="tag_cloud"}
{if strlen($tag_cloud) > 0}
    <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">Tag</span> Cloud</h3>
    <div class="border-1px block_content">
        <div class="item">
            {$tag_cloud}
        </div>
    </div>
{/if}
