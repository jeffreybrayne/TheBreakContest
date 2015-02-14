{jrCore_module_url module="jrBlog" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrBlog" id="19" default="Posted a new Blog"}:
    {else}
        {jrCore_lang module="jrBlog" id="30" default="Updated a Blog"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.blog_title_url}" title="{$item.action_data.blog_title|jrCore_entity_string}">{$item.action_data.blog_title}</a>
    </span>
    <div class="action_item_desc">
        {$item.action_data.blog_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:170}
    </div>
</div>
