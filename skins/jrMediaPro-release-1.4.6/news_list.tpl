{if isset($_post.option) && strlen($_post.option) > 0}
    {assign var="news_category" value=$_post.option}
{else}
    {assign var="news_category" value="latest"}
{/if}
{if $news_category == 'featured' || $news_category == 'Featured'}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="Featured" assign="news_category_title"}
{elseif $news_category == 'exclusive' || $news_category == 'Exclusive'}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="115" default="Exclusive" assign="news_category_title"}
{else}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="78" default="Latest" assign="news_category_title"}
{/if}

<div class="body_1">
    <h2>{$news_category_title} {jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News"}</h2>&nbsp;
    {if $_post.option == 'exclusive' || $_post.option == 'Exclusive' || $_post.option == 'featured' || $_post.option == 'Featured'}
        &raquo;&nbsp;<a onclick="jrLoad('#news_listing','{$jamroom_url}/news_list');$('html, body').animate({ scrollTop: $('#newslist').offset().top });return false;">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
    {/if}
</div>
{capture name="row_template" assign="latest_news_template"}
    {literal}
        {jrCore_module_url module="jrBlog" assign="murl"}
        {foreach from=$_items item="item"}
            <div class="body_5 page mb10" style="margin-right:10px;">
                <div class="block_config">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
                </div>
                <h3><a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3><br>
                {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                    <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="small" alt=$item.blog_title crop="auto" class="iloutline" style="float:left;margin:8px 8px 0 0;"}</a>
                {/if}
                <div class="normal blog-text mb15">
                    {$item.blog_text|truncate:350:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}
                </div>
                {if jrCore_module_is_active('jrComment')}
                <div class="float-left">
                    <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                </div>
                {/if}
                <div class="float-right"><a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}"><div class="button-more">&nbsp;</div></a></div>
                <div class="clear"></div>
            </div>
        {/foreach}
        {if $info.total_pages > 1}
            <div class="block">
                <table style="width:100%;">
                    <tr>

                        <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                            {if isset($info.prev_page) && $info.prev_page > 0}
                                <a onclick="jrLoad('#news_listing','{$jamroom_url}/news_list/{$item.blog_category}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#newslist').offset().top });return false;"><span class="button-arrow-previous">&nbsp;</span></a>
                            {else}
                                <span class="button-arrow-previous-off">&nbsp;</span>
                            {/if}
                        </td>

                        <td class="body_4 p5 middle" style="width:50%;text-align:center;">
                            {if $info.total_pages <= 5 || $info.total_pages > 500}
                                {$info.page} &nbsp;/ {$info.total_pages}
                            {else}
                                <form name="form" method="post" action="_self">
                                    <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#news_listing','{$jamroom_url}/news_list/{$item.blog_category}/p=' +sel);$('html, body').animate({ scrollTop: $('#newslist').offset().top });return false;">
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
                                <a onclick="jrLoad('#news_listing','{$jamroom_url}/news_list/{$item.blog_category}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#newslist').offset().top });return false;"><span class="button-arrow-next">&nbsp;</span></a>
                            {else}
                                <span class="button-arrow-next-off">&nbsp;</span>
                            {/if}
                        </td>

                    </tr>
                </table>
            </div>
        {/if}
    {/literal}
{/capture}

{if isset($_conf.jrMediaPro_blog_profile) && $_conf.jrMediaPro_blog_profile > 0}
    {jrCore_list module="jrBlog" order_by="_created desc" search1="blog_category = `$news_category`"  search2="_profile_id in `$_conf.jrMediaPro_blog_profile`" template=$latest_news_template pagebreak="6" page=$_post.p}
{else}
    {jrCore_list module="jrBlog" order_by="_created desc" search1="blog_category = `$news_category`" search2="_profile_id = 1" template=$latest_news_template pagebreak="6" page=$_post.p}
{/if}
