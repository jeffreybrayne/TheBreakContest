{foreach from=$_items key="id" item="item"}
<table class="page_section_header jrcore_file_detail" onmouseover="$('#d{$id}').show()" onmouseout="$('#d{$id}').hide()">
    <tr>
        <td class="jrcore_file_detail_left">
            {* If this is an image field, show image in lightbox *}
            {if $item.is_image == 1}
                <a href="{$jamroom_url}/{$item.module_url}/image/{$item.field_name}/{$item._item_id}/xxxlarge" target="_blank" data-lightbox="images">
                    {jrCore_module_function function="jrImage_display" module=$_post.module type=$item.field_name item_id=$item._item_id size="icon" crop="auto" alt=$item.name width=52 height=52}
                </a>
            {else}
                <a href="{$jamroom_url}/{$item.module_url}/download/{$item.field_name}/{$item._item_id}">{jrCore_image module="jrFile" image="`$item.extension`.png" width="32" height="32" alt=$item.extension}</a>
            {/if}
        </td>
        <td class="jrcore_file_detail_right">
            {if $item.is_image == 1}
            <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="74" default="name"}:&nbsp;</span> <a href="{$jamroom_url}/{$item.module_url}/download/{$item.field_name}/{$item._item_id}">{$item.name}</a><br>
            {else}
            <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="74" default="name"}:&nbsp;</span> {$item.name}<br>
            {/if}
            <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="75" default="size"}:&nbsp;</span> {$item.size|jrCore_format_size}<br>
            <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="76" default="date"}:&nbsp;</span> {$item.time|jrCore_format_time}
            <div id="d{$id}" class="image_delete">
                {jrCore_module_url module="jrCore" assign="curl"}
                {jrCore_lang module="jrCore" id="40" default="are you sure you want to delete this item?" assign="prompt"}
                {jrCore_lang module="jrCore" id="38" default="delete" assign="title"}
                <a href="{$jamroom_url}/{$curl}/delete/{$_post.module}/{$item.field_name}/{$item._item_id}" title="{$title|jrCore_entity_string}" onclick="jrCore_set_csrf_cookie('{$jamroom_url}/{$curl}/delete/{$_post.module}/{$item.field_name}/{$item._item_id}'); if(!confirm('{$prompt|jrCore_entity_string}')){ return false; }">{jrCore_icon icon="close" size=16}</a>
            </div>
        </td>
    </tr>
</table>
{/foreach}

