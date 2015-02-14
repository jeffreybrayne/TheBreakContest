{jrCore_module_url module="jbRegister" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item['action_mode'] == 'create'}
        {jrCore_lang module="jbRegister" id="11" default="Posted a new register"}:
        {else}
        {jrCore_lang module="jbRegister" id="12" default="Updated a register"}:
    {/if}
    </span><br>
    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.register_title_url}" title="{$item.action_data.register_title|htmlentities}"><h4>{$item.action_data.register_title}</h4></a>
</div>
