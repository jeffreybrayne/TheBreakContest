{jrCore_module_url module="jbArtistProfile" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item['action_mode'] == 'create'}
        {jrCore_lang module="jbArtistProfile" id="11" default="Posted a new artistprofile"}:
        {else}
        {jrCore_lang module="jbArtistProfile" id="12" default="Updated a artistprofile"}:
    {/if}
    </span><br>
    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.artistprofile_title_url}" title="{$item.action_data.artistprofile_title|htmlentities}"><h4>{$item.action_data.artistprofile_title}</h4></a>
</div>
