{capture name="row_template" assign="blogrow"}
    {literal}

        {if isset($_items)}
        {jrCore_module_url module="jrBlog" assign="murl"}
        {foreach from=$_items item="item"}
        <div style="padding:10px;">
            <div class="br-info" style="margin-bottom:20px;">
                <div class="blog-div">
                    <span class="blog-user capital"> By {$item.profile_name}</span>
                </div>
                <div class="blog-div">
                    <span class="blog-date"> {$item.blog_publish_date|jrCore_format_time}</span>
                </div>
                <div class="blog-div">
                    <span class="blog-tag capital"> Tag: {$item.blog_category}</span>
                </div>
                {if jrCore_module_is_active('jrComment')}
                <div class="blog-div">
                    <span class="blog-replies">
                        <a href="#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                    </span>
                </div>
                {/if}
                <div class="float-right">
                    <a onclick="jrLoad('#siteblog','{$jamroom_url}/blogs_list/p={$_post.p}');$('html, body').animate({ scrollTop: $('#sblogs').offset().top });return false;">{jrCore_icon icon="arrow-left" size="18"}</a>
                {if jrUser_is_master()}
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
                {/if}
                </div>
                <div class="clear"></div>
            </div>
            <div class="p10">
                <h3>{$item.blog_title}</h3>
                <div class="blog-text">
                    {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                    {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width="128" height="128" crop="auto" class="iloutline img_shadow" style="float:left;margin-right:8px;margin_bottom:8px;"}
                    {/if}
                    {$item.blog_text|jrCore_format_string:$item.profile_quota_id|nl2br}
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <br>
        <hr>
        <br>
        <a id="comments" name="comments"></a>
        {* bring in module features *}
        {jrCore_item_detail_features module="jrBlog" item=$item}

        {/foreach}
        {/if}

    {/literal}
{/capture}

{jrCore_list module="jrBlog" search="_item_id = `$_post.option`" template=$blogrow}
