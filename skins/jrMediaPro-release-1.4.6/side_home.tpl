    {* SITE NEWS *}
{if isset($spt) && $spt == 'home'}
<div id="site_news_div" class="mb20">
    {* ROW TEMPLATE *}
    {capture name="row_template" assign="site_news_template"}
        {literal}
            {if isset($_items)}
            {jrCore_module_url module="jrBlog" assign="murl"}
            <div class="body_1">
                <h3>
                    {if jrUser_is_master()}
                    <div class="float-right" style="padding-right:10px;">
                        <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}/{$_items.0._item_id}/{$_items.0.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
                    </div>
                    {/if}
                    <span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="8" default="Site"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News"}
                </h3>
            </div>
            <div class="body_4 mb20 pt10">
            {foreach from=$_items item="item"}
            <div style="height:425px;padding:10px;overflow: auto;">
                <div class="br-info" style="margin-bottom:20px;">
                    <div class="blog-div">
                        <span class="blog-user capital"> By <span class="hl-3">{$item.profile_name}</span></span><br>
                        <span class="blog-date" style="margin-left:0;"> {$item.blog_publish_date|jrCore_format_time}</span><br>
                        <span class="blog-tag capital" style="margin-left:0;"> Tag: <span class="hl-4">{$item.blog_category}</span></span>
                        {if jrCore_module_is_active('jrComment')}
                            <br>
                            <span class="blog-replies" style="margin-left:0;">
                                {if $item.profile_id == '1'}
                                    <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                {else}
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                {/if}
                            </span>
                        {/if}
                    </div>
                    <div class="clear"></div>
                </div>
                {if $item.profile_id == '1'}
                    <h3><a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3>
                {else}
                    <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3>
                {/if}
                <div class="blog-text">
                    {$item.blog_text|truncate:800:"...":false|jrCore_format_string:$item.profile_quota_id:null:nl2br}
                </div>
            </div>
            {/foreach}
            </div>
            {if $info.total_pages > 1}
            <div class="block">
                <table style="width:100%;">
                    <tr>

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            {if isset($info.prev_page) && $info.prev_page > 0}
                            <a onclick="jrLoad('#site_news_div','{$jamroom_url}/site_news/p={$info.prev_page}');"><span class="button-arrow-previous">&nbsp;</span></a>
                            {else}
                            <span class="button-arrow-previous-off">&nbsp;</span>
                            {/if}
                        </td>

                        <td class="body_5" style="width:50%;text-align:center;border:1px solid #282828;">
                            {if $info.total_pages <= 5 || $info.total_pages > 500 || $info.total_pages > 500}
                            {$info.page} &nbsp;/ {$info.total_pages}
                            {else}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#site_news_div','{$jamroom_url}/site_news/p=' +sel);">
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

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            {if isset($info.next_page) && $info.next_page > 1}
                            <a onclick="jrLoad('#site_news_div','{$jamroom_url}/site_news/p={$info.next_page}');"><span class="button-arrow-next">&nbsp;</span></a>
                            {else}
                            <span class="button-arrow-next-off">&nbsp;</span>
                            {/if}
                        </td>

                    </tr>
                </table>
            </div>
            {/if}
            {/if}
        {/literal}
    {/capture}

    {* SITE NEWS FUNCTION *}
    {if isset($_conf.jrMediaPro_blog_profile) && $_conf.jrMediaPro_blog_profile > 0}
        {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id in `$_conf.jrMediaPro_blog_profile`" search2="blog_category = news" template=$site_news_template pagebreak="1" page=$_post.p}
    {else}
        {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = 1" search2="blog_category = news" template=$site_news_template pagebreak="1" page=$_post.p}
    {/if}

</div>
{/if}

{* STATS | ONLINE *}
<table class="menu_tab">
    <tr>
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
    </tr>
</table>

<div id="stats" class="body_2 mb20">
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
</div>

{if isset($selected) && $selected == 'home'}

<h3>
    <span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="167" default="New"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="168" default="Listeners"}
</h3>
<div class="page mb20 pt10">
    {* NEW LISTENER ROW TEMPLATE *}
    {capture name="row_template" assign="new_listener"}
        {literal}
            {if isset($_items)}
            {foreach from=$_items item="row"}
            <div class="center p5">
                <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow"}</a><br>
                <div class="spacer10"></div>
                <a href="{$jamroom_url}/{$row.profile_url}" title="{$row.profile_name}"><span class="capital bold">{$row.profile_name|truncate:20:"...":false}</span></a><br>
                <div class="spacer10"></div>
                <div align="right"><a href="{$jamroom_url}/members" title="View More"><div class="button-more">&nbsp;</div></a></div>
            </div>
            {/foreach}
            {/if}
        {/literal}
    {/capture}
    {* NEW LISTENER FUNCTION *}
    {jrCore_list module="jrProfile" order_by="_created desc" limit="1" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_member_quota template=$new_listener require_image="profile_image"}
</div>

{elseif isset($spt) && ($spt == 'artist' || $spt == 'music' || $spt == 'channels')}

<h3>
    <i>{jrCore_lang skin=$_conf.jrCore_active_skin id="119" default="Today's"}</i><br><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="110" default="Artist"}
</h3>
<div class="page mb20 pt10">
    {* NEW LISTENER ROW TEMPLATE *}
    {capture name="row_template" assign="featured_artist"}
        {literal}
            {if isset($_items)}
            {foreach from=$_items item="row"}
            <div class="center p5">
                <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow"}</a><br>
                <div class="spacer10"></div>
                <a href="{$jamroom_url}/{$row.profile_url}" title="{$row.profile_name}"><span class="capital bold">{$row.profile_name|truncate:20:"...":false}</span></a><br>
                <div class="spacer10"></div>
                <div align="right"><a href="{$jamroom_url}/artists" title="View More"><div class="button-more">&nbsp;</div></a></div>
            </div>
            {/foreach}
            {/if}
        {/literal}
    {/capture}
    {* NEW LISTENER FUNCTION *}
    {if isset($_conf.jrMediaPro_todays_featured) && strlen($_conf.jrMediaPro_todays_featured) > 0}
        {jrCore_list module="jrProfile" search="_profile_id = `$_conf.jrMediaPro_todays_featured`" template=$featured_artist}
    {else}
        {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="1" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template=$featured_artist require_image="profile_image"}
    {/if}
</div>

{/if}

{* LATEST COMMENTS *}
{if isset($spt) && $spt != 'profiles' && $spt != 'events'}

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

{* HOUSE STATION *}
{if isset($spt) && ($spt == 'music' || $spt == 'galleries' || $spt == 'home' || $spt == 'artist' || $spt == 'member' || $spt == 'profiles')}
    {if isset($_conf.jrMediaPro_show_radio) && $_conf.jrMediaPro_show_radio == 'on'}
        {if isset($_conf.jrMediaPro_radio_title) && strlen($_conf.jrMediaPro_radio_title) > 0}
            {jrCore_list module="jrPlaylist" profile_id="1" order_by="_created desc" search1="playlist_title = `$_conf.jrMediaPro_radio_title`" limit="1" template="index_radio.tpl"}
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

{if jrCore_module_is_active('jrRecommend')}
<div class="body_2b mb20">
    <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="172" default="Sounds"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="173" default="Like"}...</h3>
    <div class="p10">
        <b>{jrCore_lang skin=$_conf.jrCore_active_skin id="32" default="Enter an Artist you would like to find music similar to"}:</b><br /><br />
        <div class="p5">
            {jrRecommend_form class="form_text" value="{jrCore_lang skin=$_conf.jrCore_active_skin id="24" default="search"}" submit_value="{jrCore_lang skin=$_conf.jrCore_active_skin id="31" default="find new music"}" template="recommend_form.tpl" style="max-width:260px;"}
        </div>
    </div>
</div>
{/if}

{if isset($spt) && ($spt == 'music' || $spt == 'galleries' || $spt == 'home' || $spt == 'artist' || $spt == 'member' || $spt == 'profiles')}
{* HOUSE CHANNEL *}
    {if isset($_conf.jrMediaPro_show_tv) && $_conf.jrMediaPro_show_tv == 'on'}
        {if isset($_conf.jrMediaPro_tv_title) && strlen($_conf.jrMediaPro_tv_title) > 0}
            {jrCore_list module="jrPlaylist" profile_id="1" order_by="_created desc" search1="playlist_title = `$_conf.jrMediaPro_tv_title`" template="index_channel.tpl"}
        {else}
            {if jrUser_is_logged_in()}
                {if jrUser_is_master()}
                    <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="44" default="channel"}</h3>
                    <div class="body_2b normal p20 mb20">
                        Admin Note:&nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin={$_conf.jrCore_active_skin}">Settings</a> <b>"Channel Title"</b> is not set!
                    </div>
                {/if}
            {/if}
        {/if}
    {/if}
{/if}

{* TOP 10 CHARTS *}
{if jrCore_module_is_active('jrCharts')}
    <a id="ttcharts" name="ttcharts"></a>
    {if isset($spt) && $spt != 'profiles' && $spt != 'events'}

        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="365" template="side_charts_row.tpl" pagebreak="10" page=$_post.p assign="TOP_TEN_CHARTS"}
        {if isset($TOP_TEN_CHARTS) && strlen($TOP_TEN_CHARTS) > 0}
            <h3>
                <span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="181" default="Top 10"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="Charts"}
            </h3>
            <br>
            <br>
            <div id="side_charts" style="max-height:560px;">
                {$TOP_TEN_CHARTS}
            </div>
            <div class="clear"> </div>
        {/if}
    {/if}
{/if}

{* OUR SPONSORS *}
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
