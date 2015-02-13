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

{* OUR SPONSORS *}
{if $_conf.jrMediaPro_ads_off != 'on'}
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
{/if}
