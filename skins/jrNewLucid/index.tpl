{jrCore_include template="header.tpl" show_bg=1}

{* This is the embedded template that is shown for EACH blog entry *}
{capture name="template1" assign="blog_tpl"}
{literal}
{if isset($_items)}
    {jrCore_module_url module="jrBlog" assign="burl"}
    {foreach from=$_items item="item"}
    <div class="row blog-index-header">
        <div class="col2">
            <div class="p20">
                {assign var="field" value="profile_image"}
                {if $item.user_image_size > 0}
                    {assign var="field" value="user_image"}
                {/if}
                {jrCore_module_function function="jrImage_display" module="jrUser" type=$field item_id=$item._user_id size="medium" crop="portrait" class="img_scale img-rounded" alt=$item.user_name}
            </div>
        </div>
        <div class="col10 last">
            <div class="p20">
                <h1 class="blog-index-title"><a href="{$jamroom_url}/{$item.profile_url}/{$burl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h1>
                <br><span class="blog-byline">{$item.user_name} - <a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a><br>{$item.blog_publish_date|jrCore_format_time:false:"%e %b %Y"}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col12 last">
            <div class="blog-index-text">
                {$item.blog_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:600}
                <br><br>
                <a href="{$jamroom_url}/{$item.profile_url}/{$burl}/{$item._item_id}/{$item.blog_title_url}"><div class="blog-index-read-more">Read More</div></a>
                {if isset($item.blog_comment_count) && $item.blog_comment_count > 0}
                    <a href="{$jamroom_url}/{$item.profile_url}/{$burl}/{$item._item_id}/{$item.blog_title_url}#cform"><div class="blog-index-comments">{$item.blog_comment_count} Comments</div></a>
                {/if}
            </div>
        </div>
    </div>
    {/foreach}
{/if}
{/literal}
{/capture}


{* This is the embedded template that is shown for EACH archive entry *}
{capture name="template2" assign="archive_tpl"}
{literal}
{if isset($_items)}
    {jrCore_module_url module="jrBlog" assign="burl"}
    {foreach from=$_items item="item"}
    <div class="blog-archive-entry">
        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xsmall" crop="portrait" class="blog-archive-img" alt=$item.user_name width=24 height=24}
        <h3 class="blog-archive-title"><a href="{$jamroom_url}/{$item.profile_url}/{$burl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3>
        <span class="blog-archive-byline"><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a></span>
    </div>
    {/foreach}
{/if}
{/literal}
{/capture}


<div class="container">
    <div class="row">
        <div class="col8">
            <div class="p10">
                {if jrCore_checktype($_post.option, 'number_nz')}
                    {jrCore_list module="jrBlog" search1="_item_id = `$_post.option`" template=$blog_tpl}
                {else}
                    {if strlen($_conf.jrLucid_blog_profile_ids) > 0}
                        {jrCore_list module="jrBlog" search1="_profile_id in `$_conf.jrLucid_blog_profile_ids`" order_by="blog_publish_date numerical_desc" pagebreak="3" template=$blog_tpl page=$_post.p pager=true}
                    {else}
                        {jrCore_list module="jrBlog" order_by="blog_publish_date numerical_desc" pagebreak="12" template=$blog_tpl page=$_post.p pager=true}
                    {/if}
                {/if}
            </div>
        </div>
        <div class="col4 last">
            <div class="item blog-archive-header">
                <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="6" default="Older Posts"}</h2>
            </div>
            <div class="p20">
                {if strlen($_conf.jrLucid_blog_profile_ids) > 0}
                    {jrCore_list module="jrBlog" search1="_profile_id in `$_conf.jrLucid_blog_profile_ids`" order_by="blog_publish_date numerical_desc" limit="20" template=$archive_tpl}
                {else}
                    {jrCore_list module="jrBlog" order_by="blog_publish_date numerical_desc" limit="20" template=$archive_tpl}
                {/if}
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}

