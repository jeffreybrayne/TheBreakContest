{assign var="selected" value="ban"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
     });
</script>

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="mr5">
                {capture name="row_template" assign="news_story_template"}
                    {literal}
                        {if isset($_items)}
                            {jrCore_module_url module="jrBlog" assign="murl"}
                            {foreach from=$_items item="item"}
                                <div class="body_1">
                                    <div class="float-right" style="padding-right:10px;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
                                    </div>
                                    <h2 style="padding-left:10px;">{$item.blog_title}</h2>&nbsp;
                                    &raquo;&nbsp;<a href="{$jamroom_url}/news"><span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="news"}</span></a>
                                </div>
                                <div class="body_5" style="margin-left:10px;">

                                    <div class="block blogpost">

                                        <div class="blog_info">
                                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xsmall" crop="auto" class="action_item_user_img iloutline" style="margin-right:12px"}
                                            <span class="normal">{$item.blog_publish_date|jrCore_format_time}</span><br>
                                            <span class="media_title">{jrCore_lang module="jrBlog" id="28" default="By"}:</span> <span class="capital">{$item.user_name}</span> <span class="media_title">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <span class="capital">{$item.blog_category}</span><br>
                                            <span style="display:inline-block;margin-top:6px;">{jrCore_module_function function="jrRating_form" type="star" module="jrBlog" index="1" item_id=$item._item_id current=$item.blog_rating_1_average_count|default:0 votes=$item.blog_rating_1_count|default:0}</span>
                                        </div>

                                        <div class="normal p5">
                                            {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                                                <div style="float:right">
                                                    {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width=false height=false class="iloutline img_shadow" style="margin-left:12px;margin-bottom:12px;"}
                                                </div>
                                            {/if}
                                            {$item.blog_text|jrCore_format_string:$item.profile_quota_id|nl2br}
                                        </div>
                                        <div class="clear"></div>
                                        <hr>

                                    </div>
                                    <a id="comments" name="comments"></a>
                                    {* bring in module features *}
                                    {jrCore_item_detail_features module="jrBlog" item=$item}


                                </div>
                            {/foreach}
                        {/if}
                    {/literal}
                {/capture}

                {if isset($_conf.jrMediaPro_blog_profile) && $_conf.jrMediaPro_blog_profile > 0}
                    {jrCore_list module="jrBlog" search1="_profile_id in `$_conf.jrMediaPro_blog_profile`" search2="_item_id = `$_post.option`" template=$news_story_template}
                {else}
                    {jrCore_list module="jrBlog" search1="_profile_id = 1" search2="_item_id = `$_post.option`" template=$news_story_template}
                {/if}

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1">
                {jrCore_include template="side_news_story.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
