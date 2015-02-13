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
            <div style="padding:10px;">
                <div class="br-info" style="margin-bottom:20px;">
                    <div class="blog-div">
                        <span class="blog-user capital"> By <span class="hl-3">{$item.profile_name}</span></span><br>
                        <span class="blog-date" style="margin-left:0;"> {$item.blog_publish_date|jrCore_format_time}</span><br>
                        <span class="blog-tag capital" style="margin-left:0;"> Tag: <span class="hl-4">{$item.blog_category}</span></span>
                        {if jrCore_module_is_active('jrComment')}
                        <br>
                            <span class="blog-replies" style="margin-left:0;">
                                {if $item.profile_id == '1'}
                                    <a href="{$jamroom_url}/news_story/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                {else}
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                {/if}
                            </span>
                        {/if}
                    </div>
                    <div class="clear"></div>
                </div>
                <h3><a href="{$jamroom_url}/news_story/{$item.blog_title_url}">{$item.blog_title}</a></h3>
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

{* EVENT LIST FUNCTION *}
{if isset($_conf.jrMediaPro_blog_profile) && $_conf.jrMediaPro_blog_profile > 0}
    {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id in `$_conf.jrMediaPro_blog_profile`" search2="blog_category = news" template=$site_news_template pagebreak="1" page=$_post.p}
{else}
    {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = 1" search2="blog_category = news" template=$site_news_template pagebreak="1" page=$_post.p}
{/if}
