{assign var="selected" value="ban"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#news_listing',core_system_url + '/news_list');
     });
</script>

<div class="container">
    <div class="row">

        <div class="col3">
            <div class="body_1">
            {jrCore_include template="side_news.tpl"}
            </div>
        </div>

        <div class="col9 last">

            <div class="body_1 mr5">

                <div class="container">
                    <div class="row">
                    {* LATEST NEWS SLIDER *}
                        <div class="col8">

                            <div class="body_1">

                                <script>
                                    $(function () {

                                        // Slideshow 2
                                        $("#slider2").responsiveSlides({
                                            auto: true,          // Boolean: Animate automatically, true or false
                                            speed: 400,          // Integer: Speed of the transition, in milliseconds
                                            timeout: 4000,       // Integer: Time between slide transitions, in milliseconds
                                            pager: false,         // Boolean: Show pager, true or false
                                            random: false,        // Boolean: Randomize the order of the slides, true or false
                                            pause: true,         // Boolean: Pause on hover, true or false
                                            maxwidth: 495,       // Integer: Max-width of the slideshow, in pixels
                                            namespace: "latest-news" // String: change the default namespace used
                                         });

                                     });
                                </script>
                                <div class="callbacks_container">
                                    <div class="ioutline" style="max-width:495px;margin:0 auto;">
                                        <ul id="slider2" class="rslides callbacks" style="height:370px;">
                                    {if isset($_conf.jrMediaPro_blog_profile) && $_conf.jrMediaPro_blog_profile > 0}
                                        {jrCore_list module="jrBlog" order_by="_created desc" limit="10" search1="_profile_id in `$_conf.jrMediaPro_blog_profile`" search2="blog_category = latest" template="news_slider.tpl"}
                                    {else}
                                        {jrCore_list module="jrBlog" order_by="_created desc" limit="10" search1="_profile_id = 1" search2="blog_category = latest" template="news_slider.tpl"}
                                    {/if}
                                        </ul>
                                    </div>
                                </div>
                                <br>
                                <div class="clear"></div>

                            </div>

                            {* LATEST NEWS *}
                            <a name="newslist" id="newslist"></a>
                            <div id="news_listing">

                            </div>

                        </div>

                    {* EXCLUSIVE NEWS *}
                        <div class="col4 last">

                            {capture name="row_template" assign="exclusive_news"}
                                {literal}
                                    {jrCore_module_url module="jrBlog" assign="murl"}
                                    {foreach from=$_items item="item"}
                                        <div class="body_1">
                                            <div class="float-right" style="padding-right:10px;">
                                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
                                            </div>
                                            <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="115" default="Exclusive"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News"}</h3><br>
                                        </div>
                                        <div class="page mb20 pt10">
                                            {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                                            <div class="center mb10">
                                                <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="medium" alt=$item.blog_title crop="auto" class="iloutline"}</a>
                                            </div>
                                            {/if}
                                            <h3><a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3><br>
                                            <div class="blog-text mb15">
                                                {$item.blog_text|truncate:100:"...":false|jrCore_format_string:$item.profile_quota_id}
                                            </div>
                                            {if jrCore_module_is_active('jrComment')}
                                            <div class="float-left">
                                                <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                            </div>
                                            {/if}
                                            <div class="float-right"><a onclick="jrLoad('#news_listing','{$jamroom_url}/news_list/{$item.blog_category}');$('html, body').animate({ scrollTop: $('#newslist').offset().top });return false;"><div class="button-more">&nbsp;</div></a></div>
                                            <div class="clear"></div>
                                        </div>
                                    {/foreach}
                                {/literal}
                            {/capture}

                            {if isset($_conf.jrMediaPro_blog_profile) && $_conf.jrMediaPro_blog_profile > 0}
                                {jrCore_list module="jrBlog" order_by="_created desc" limit="1" search1="_profile_id in `$_conf.jrMediaPro_blog_profile`" search2="blog_category = exclusive" template=$exclusive_news}
                            {else}
                                {jrCore_list module="jrBlog" order_by="_created desc" limit="1" search1="_profile_id = 1" search2="blog_category = exclusive" template=$exclusive_news}
                            {/if}

                            {* NEWEST VIDEO *}
                            {if jrCore_module_is_active('jrVideo')}
                            <div class="body_1">
                                <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="video"}</h3><br>
                            </div>
                            {capture name="row_template" assign="newest_video"}
                                {literal}
                                    {jrCore_module_url module="jrVideo" assign="murl"}
                                    {foreach from=$_items item="item"}
                                        <div class="body_1 mb20">
                                            <div class="center mb10">
                                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" alt=$item.blog_title crop="auto" class="iloutline"}</a>
                                            </div>
                                            Now Playing: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{$item.video_title}</a><br>
                                            By: <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a>
                                        </div>
                                    {/foreach}
                                {/literal}
                            {/capture}

                            {jrCore_list module="jrVideo" order_by="_created desc" limit="1" template=$newest_video}
                            {/if}

                            {* EVENTS *}
                            {if jrCore_module_is_active('jrEvent')}
                            <div class="body_1">
                                <h3><span style="font-weight: normal;line-height:24px;padding-left:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="19" default="events"}</h3><br>
                            </div>
                            {capture name="row_template" assign="newest_events"}
                                {literal}
                                <div class="body_1 mb20">
                                    {jrCore_module_url module="jrEvent" assign="murl"}
                                    {foreach from=$_items item="item"}
                                        <div class="body_5 page" style="margin-right:0;">
                                            <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a></h3><br>
                                            {jrEvent_get_featuring json=$item.event_featuring assign="_f"}
                                            <span class="capital bold">{jrCore_lang module="jrEvent" id="13" default="Featuring"}:</span> <a href="{$jamroom_url}/{$item.profile_url}"><span class="capital">{$item.profile_name}</span></a><br>
                                            <span class="capital bold">{jrCore_lang module="jrEvent" id="11" default="Event Date"}:</span> <span class="hl-2">{$item.event_date|jrCore_date_format}</span><br>
                                            <span class="capital bold">{jrCore_lang module="jrEvent" id="6" default="Event location"}:</span> <span class="hl-3 capital">{$item.event_location|truncate:60}</span>
                                            <div style="padding:4px 0 8px 4px;">
                                                {jrCore_module_function function="jrRating_form" type="star" module="jrEvent" index="1" item_id=$item._item_id current=$item.event_rating_1_average_count|default:0 votes=$item.event_rating_1_count|default:0 }
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                                {/literal}
                            {/capture}

                            {jrCore_list module="jrEvent" order_by="_created desc" limit="6" template=$newest_events}
                            {/if}

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
