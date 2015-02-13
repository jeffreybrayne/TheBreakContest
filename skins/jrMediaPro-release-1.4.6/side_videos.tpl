{* STATS | ONLINE *}
<table class="menu_tab">
    <tr>
        {if isset($_conf.jrMediaPro_v_category) && $_conf.jrMediaPro_v_category == 'on'}
            {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                <td>
                    <div id="site_stats" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/stats');jrSetActive('#site_stats');">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="online" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/online');jrSetActive('#online');">{jrCore_lang skin=$_conf.jrCore_active_skin id="113" default="online"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="default" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/video_categories');jrSetActive('#default');">{jrCore_lang skin=$_conf.jrCore_active_skin id="132" default="Categories"}</div>
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
                    <div id="video_genre" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/video_categories');jrSetActive('#video_genre');">{jrCore_lang skin=$_conf.jrCore_active_skin id="132" default="Categories"}</div>
                </td>
            {/if}
        {else}
            <td>
                <div id="default" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/stats');jrSetActive('#default');">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
            </td>
            <td class="spacer">&nbsp;</td>
            <td>
                <div id="rss_feeds" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/site_feeds');jrSetActive('#rss_feeds');">{jrCore_lang skin=$_conf.jrCore_active_skin id="134" default="Feeds"}</div>
            </td>
            <td class="spacer">&nbsp;</td>
            <td>
                <div id="online" class="p_choice" onclick="jrLoad('#stats','{$jamroom_url}/online');jrSetActive('#online');">{jrCore_lang skin=$_conf.jrCore_active_skin id="113" default="online"}</div>
            </td>
        {/if}
    </tr>
</table>

<div id="stats" class="body_2 mb20">
    {if isset($_post.search_area) && $_post.search_area == 'video_category'}
        <!-- Search Song Genre-->
        <h3>{jrCore_lang module="jrVideo" id="12" default="Category"} {jrCore_lang skin=$_conf.jrCore_active_skin id="24" default="Search"}</h3>
        <br />
        <form class="margin" method="post" action="{$jamroom_url}/videos">
            <input type="hidden" name="search_area" value="video_category">
            <select class="form_select" name="search_string" style="width:100%; font-size:13px;" onchange="this.form.submit()">
                {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                    <option value="{$_post.search_string}">{$_post.search_string}</option>
                {else}
                    <option value="">{jrCore_lang skin=$_conf.jrCore_active_skin id="184" default="Select A Cateogry"}</option>
                {/if}
                {jrCore_list module="jrVideo" order_by="video_category asc" group_by="video_category" limit="200" template="video_categories_row.tpl"}
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

{* FEATURED VIDEO *}
<h3>
    <span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="Video"}
</h3>
<div class="page mb20 pt10">
    {* FEATURED VIDEO ROW TEMPLATE *}
    {capture name="row_template" assign="featured_video"}
        {literal}
            {if isset($_items)}
            {jrCore_module_url module="jrVideo" assign="vurl"}
            {foreach from=$_items item="row"}
            <div class="center p5">
                <a href="{$jamroom_url}/{$row.profile_url}/{$vurl}/{$row._item_id}/{$row.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$row._item_id size="medium" crop="auto" class="iloutline img_shadow" alt=$row.video_title title=$row.video_title}</a>
                <div class="spacer10"></div>
                <h3><a href="{$jamroom_url}/{$row.profile_url}/{$vurl}/{$row._item_id}/{$row.video_title|jrCore_url_string}">{$row.video_title}</a></h3><br>
                <h4>By&nbsp;<a href="{$jamroom_url}/{$row.profile_url}">{$row.profile_name}</a></h4><br>
            </div>
            {/foreach}
            {/if}
        {/literal}
    {/capture}
    {* FEATURED VIDEO FUNCTION *}
    {if isset($_conf.jrMediaPro_featured_video) && strlen($_conf.jrMediaPro_featured_video) > 0}
        {jrCore_list module="jrVideo" order_by="_item_id desc" limit="1" search1="profile_active = 1" search2="_item_id = `$_conf.jrMediaPro_featured_video`" template=$featured_video}
    {else}
        {jrCore_list module="jrVideo" order_by="video_file_stream_count numerical_desc" limit="1" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template=$featured_video}
    {/if}
</div>

{* LATEST COMMENTS *}
{if $selected == 'channels'}

    {if jrCore_module_is_active('jrComment')}
        {jrCore_list module="jrComment" order_by="_created desc" limit="10" template="side_comments.tpl" assign="SIDE_COMMENTS"}
    {/if}
    {if isset($SIDE_COMMENTS) && strlen($SIDE_COMMENTS) > 0}
        {$SIDE_COMMENTS}
    {elseif jrCore_module_is_active('jrFeed')}
        {if jrCore_module_is_active('jrDisqus')}
            {jrFeed_list name="all disqus site comments" tpl_dir="skin" skin=$_conf.jrCore_active_skin template="rss_list.tpl"}
        {else}
            {jrFeed_list name="jamroom facebook page" tpl_dir="skin" skin=$_conf.jrCore_active_skin template="rss_list.tpl"}
        {/if}
    {/if}

{/if}

{* HOUSE CHANNEL *}
{if isset($_conf.jrMediaPro_show_tv) && $_conf.jrMediaPro_show_tv == 'on'}
    {if isset($_conf.jrMediaPro_tv_title) && strlen($_conf.jrMediaPro_tv_title) > 0}
        {jrCore_list module="jrPlaylist" profile_id="0" order_by="_created desc" search1="playlist_title = `$_conf.jrMediaPro_tv_title`" tpl_dir="jrMediaPro" template="index_channel.tpl"}
    {else}
        {if jrUser_is_logged_in()}
            {if jrUser_is_master()}
                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"} {jrCore_lang skin=$_conf.jrCore_active_skin id="44" default="channel"}</div>
                <div class="normal p20 mb20">
                    Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrMediaPro">Settings</a> <b>"Radio Title"</b> is not set!
                </div>
            {/if}
        {/if}
    {/if}
{/if}

{* TOP 10 CHARTS *}
{if jrCore_module_is_active('jrCharts')}
    {if $selected == 'channels'}
        <a id="ttvcharts" name="ttvcharts"></a>

        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="365" template="side_video_charts_row.tpl" pagebreak="10" page=$_post.p assign="TOP_TEN_VIDEO_CHARTS"}
        {if isset($TOP_TEN_VIDEO_CHARTS) && strlen($TOP_TEN_VIDEO_CHARTS) > 0}
            <h3>
                <span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="Video"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="Charts"}
            </h3>
            <br>
            <br>
            <div id="side_video_charts" style="max-height:560px;">
                {$TOP_TEN_VIDEO_CHARTS}
            </div>
            <div class="clear"> </div>
        {/if}
    {/if}
{/if}

{* OUR SPONSORS *}
{if $selected == 'channels'}
    {if $_conf.jrMediaPro_ads_off != 'on'}
        <br>
        <div class="body_1 center mt20 mb20">
            {if isset($_conf.jrMediaPro_google_ads) && $_conf.jrMediaPro_google_ads == 'on'}
                <script type="text/javascript"><!--
                    google_ad_client = "{$_conf.jrMediaPro_google_id}";
                    google_ad_width = 180;
                    google_ad_height = 150;
                    google_ad_format = "180x150_as";
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

            {elseif isset($_conf.jrMediaPro_side_ad) && strlen($_conf.jrMediaPro_side_ad) > 0}
                {$_conf.jrMediaPro_side_ad}
            {else}
                <a href="https://www.jamroom.net" target="_blank">{jrCore_image image="180x150_banner.png" width="180" height="150" alt="180x150 Ad" title="Get Jamroom5!"}</a>
            {/if}
            <br><span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="35" default="Advertisment"}</span>
        </div>
        <br>
    {/if}
{/if}

{* site wide tag cloud*}
{jrTags_cloud height="300" assign="tag_cloud"}
{if strlen($tag_cloud) > 0}
    <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">Tag</span> Cloud</h3>
    <div class="border-1px block_content">
        <div class="item">
            {$tag_cloud}
        </div>
    </div>
{/if}
