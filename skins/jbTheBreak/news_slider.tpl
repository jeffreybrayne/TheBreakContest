{if isset($_items)}
    {jrCore_module_url module="jrBlog" assign="murl"}
    {foreach from=$_items item="row"}
    <li style="height:370px;">
        <a href="{$jamroom_url}/news_story/{$row._item_id}/{$row.blog_title_url}">{jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$row._item_id size="xxlarge" alt=$row.blog_title title=$row.blog_title style="max-width:495px;max-height:370px;"}</a>
        <div class="caption">
            <a href="{$jamroom_url}/news_story/{$row._item_id}/{$row.blog_title_url}">{$row.blog_title}</a><br>
            <span class="slider_caption_text">{$row.blog_text|truncate:150:"...":false|jrCore_format_string:$row.profile_quota_id}</span>
        </div>
    </li>
    {/foreach}
{/if}
