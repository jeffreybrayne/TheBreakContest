{jrCore_module_url module="jrBlog" assign="murl"}
{if isset($_post._1) && $_post._1 == 'category'}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_create_button module="jrBlog" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrBlog" id="20" default="Category"}: {$_items[0].blog_category|default:"default"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrBlog" id="24" default="Blog"}</a> &raquo; {$_items[0].blog_category|default:"default"}
        </div>
    </div>

    <div class="block_content">
{/if}

        {if isset($_items)}
            {foreach from=$_items item="item"}
                <div class="item">

                    <div class="block_config">
                        {jrCore_item_list_buttons module="jrBlog" item=$item}
                    </div>

                    <div style="padding-left:5px">
                        <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h2>
                        <br>
                        <span class="normal">{jrCore_lang module="jrBlog" id="28" default="By"} {$item.user_name}, {$item.blog_publish_date|jrCore_format_time:false:"%F"}</span>
                    </div>

                    <div class="p20 pt10">
                        {$item.blog_text|jrCore_format_string:$item.profile_quota_id|jrBlog_readmore}
                    </div>

                    <div style="display:table;width:100%;border-top:1px solid #DDD">
                        <div style="display:table-row">
                            <div style="display:table-cell;padding:10px;width:50%">
                                <span class="info">{jrCore_lang module="jrBlog" id="26" default="Posted in"}: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url|default:"default"}">{$item.blog_category|default:"default"}</a></span>
                                {if jrCore_module_is_active('jrComment')}
                                    <span class="info"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comment_section"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"}</a></span>
                                {/if}
                            </div>
                            <div style="display:table-cell;padding:10px;width:50%;text-align:right">
                                {* check to see if the blog has a pagebreak in it *}
                                {if strpos($item.blog_text,'<!-- pagebreak -->')}
                                    <span class="info"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_lang module="jrBlog" id="25" default="Read more"} &raquo;</a></span>
                                {/if}
                            </div>
                        </div>
                    </div>

                </div>

            {/foreach}
        {/if}

{if isset($_post._1) && $_post._1 == 'category'}
    </div>

</div>
{/if}
