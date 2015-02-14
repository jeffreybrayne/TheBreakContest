{jrCore_module_url module="jrAparna" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item['action_mode'] == 'create'}
        {jrCore_lang module="jrAparna" id="11" default="Posted a new aparna"}:
        {else}
        {jrCore_lang module="jrAparna" id="12" default="Updated a aparna"}:
    {/if}
    </span><br>
    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.aparna_title_url}" title="{$item.action_data.aparna_title|htmlentities}"><h4>{$item.action_data.aparna_title}</h4></a>
</div>
