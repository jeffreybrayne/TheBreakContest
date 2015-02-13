{jrCore_module_url module="jrPage" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == "update"}
        {jrCore_lang module="jrPage" id="21" default="Updated a Page"}:
    {else}
        {jrCore_lang module="jrPage" id="18" default="Created a new Page"}:
    {/if}
    <br>
    {if $item.action_data.page_location == 0}
        <a href="{$jamroom_url}/{$murl}/{$item.action_item_id}/{$item.action_data.page_title|jrCore_url_string}" title="{$item.action_data.page_title|jrCore_entity_string}">{$item.action_data.page_title}</a>
    {else}
        <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.page_title_url}" title="{$item.action_data.page_title|jrCore_entity_string}">{$item.action_data.page_title}</a>
    {/if}
    </span>
</div>
