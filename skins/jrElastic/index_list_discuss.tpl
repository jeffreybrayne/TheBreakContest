{if isset($_items)}
    {jrCore_module_url module="jrGroupDiscuss" assign="murl"}
    {foreach from=$_items item="row"}
        <div style="display:table">
            <div class="p5" style="display:table-cell;vertical-align:middle">
                <a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.discuss_title_url}" class="media_title">{$row.discuss_title}</a>
            </div>
        </div>
    {/foreach}
{/if}
