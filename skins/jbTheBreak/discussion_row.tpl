{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">
            <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.discuss_title_url}">{$item.discuss_title}</a></h2>
            <br>
            {$item.discuss_description|strip_tags|truncate:"140":" . . ."}
            <br>
            <strong>{jrCore_lang skin=$_conf.jrCore_active_skin id="197" default="Category"}:</strong> {$item.discuss_category}
            <br>
            <strong>{jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="Comments"}:</strong> {$item.discuss_comment_count}
        </div>
    {/foreach}
{/if}
