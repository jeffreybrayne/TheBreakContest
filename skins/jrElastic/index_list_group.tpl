{if isset($_items)}
    {jrCore_module_url module="jrGroup" assign="murl"}
    {foreach from=$_items item="row"}
        <div style="display:table">
            <div style="display:table-cell">
                <a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.group_title_url}">{jrCore_module_function function="jrImage_display" module="jrGroup" type="group_image" item_id=$row._item_id size="small" crop="auto" alt=$row.group_title title=$row.group_title class="iloutline iindex"}</a>
            </div>
            <div class="p5" style="display:table-cell;vertical-align:middle">
                <a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.group_title_url}" class="media_title">{$row.group_title}</a>
            </div>
        </div>
    {/foreach}
{/if}
