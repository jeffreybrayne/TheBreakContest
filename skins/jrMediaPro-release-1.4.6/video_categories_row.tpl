{if isset($_items)}
    {jrCore_module_url module="jrVideo" assign="murl"}
    {foreach from=$_items item="item"}
        <option value="{$item.video_category}">{$item.video_category}</option>
    {/foreach}
{/if}
