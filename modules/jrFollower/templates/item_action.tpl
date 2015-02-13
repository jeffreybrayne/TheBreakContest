{jrCore_module_url module="jrFollower" assign="murl"}
<div class="p5">
    <span class="action_item_desc">
    {if isset($item.action_original_profile_name)}
        <a href="{$jamroom_url}/{$item.action_original_profile_url}">@{$item.action_original_profile_name}</a> {jrCore_lang module="jrFollower" id="22" default="is now following"} <a href="{$jamroom_url}/{$item.action_data.profile_url}">@{$item.action_data.profile_url}</a>
    {else}
        <a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_name}</a> {jrCore_lang module="jrFollower" id="22" default="is now following"} <a href="{$jamroom_url}/{$item.action_data.profile_url}">@{$item.action_data.profile_url}</a>
    {/if}
    </span>
</div>