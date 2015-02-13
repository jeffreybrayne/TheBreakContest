{if isset($_items)}
    {jrCore_module_url module="jrBlog" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="br-info" style="margin-bottom:20px;">
            <div class="blog-div">
                <span class="blog-user capital bold"> By <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>
            </div>
            <div class="blog-div">
                <span class="blog-date bold"> {$item.blog_publish_date|jrCore_format_time}</span>
            </div>
            <div class="blog-div">
                <span class="blog-tag capital bold"> Tag: <span class="hl-3">{$item.blog_category}</span></span>
            </div>
            {if jrCore_module_is_active('jrComment')}
                <div class="blog-div">
                    <span class="blog-replies">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital bold">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                    </span>
                </div>
            {/if}
            {if isset($_post.option) && $_post.option == 'featured'}
            <div class="blog-div">
                &nbsp;&nbsp;<span style="display:inline-block;">{jrCore_module_function function="jrRating_form" type="star" module="jrBlog" index="1" item_id=$item._item_id current=$item.blog_rating_1_average_count|default:0 votes=$item.blog_rating_1_count|default:0}</span>
            </div>
            {/if}
            {if jrUser_is_master()}
            <div class="float-right">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
            </div>
            {/if}
            <div class="clear"></div>
        </div>
        <div class="entry-box-icon"> </div>
        <div class="body_5 page mb20" style="margin-right:auto;">
            <div class="p10">
                <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3>
                <div class="blog-text">
                    {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="small" alt=$item.blog_title crop="auto" class="iloutline img_shadow" style="float:left;margin-right:8px;margin_bottom:8px;"}</a>
                    {/if}
                    {$item.blog_text|truncate:300:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}
                </div>
                <br>
                <div class="float-right"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}"><div class="button-more">&nbsp;</div></a></div>
                <div class="clear"></div>
            </div>
        </div>
    {/foreach}
    {if $info.total_pages > 1}
    <div class="block">
        <table style="width:100%;">
            <tr>

                <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                    {if isset($info.prev_page) && $info.prev_page > 0}
                        <a href="{$info.page_base_url}/p={$info.prev_page}"><span class="button-arrow-previous">&nbsp;</span></a>
                    {else}
                        <span class="button-arrow-previous-off">&nbsp;</span>
                    {/if}
                </td>

                <td class="body_4 p5 middle" style="width:50%;text-align:center;border: 1px solid #282828;">
                    {if $info.total_pages <= 5 || $info.total_pages > 500}
                        {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                        <form name="form" method="post" action="_self">
                            <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;window.location='{$info.page_base_url}/p=' +sel">
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
                        <a href="{$info.page_base_url}/p={$info.next_page}"><span class="button-arrow-next">&nbsp;</span></a>
                    {else}
                        <span class="button-arrow-next-off">&nbsp;</span>
                    {/if}
                </td>

            </tr>
        </table>
    </div>
    {/if}
{/if}
