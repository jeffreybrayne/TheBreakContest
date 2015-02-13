{jrCore_module_url module="jrAudio" assign="murl"}
<div class="p5">
    <span class="action_item_title">

    {if $item.action_mode == 'create'}

        {jrCore_lang module="jrAudio" id="33" default="Posted a new Audio File"}:<br>
        <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.audio_title_url}" title="{$item.action_data.audio_title|jrCore_entity_string}">{$item.action_data.audio_title}</a>

    {elseif $item.action_mode == 'create_album'}

        {jrCore_lang module="jrAudio" id="59" default="Created a new Audio Album"}: <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/albums/{$item.action_data.audio_album_url}" title="{$item.action_data.audio_album|jrCore_entity_string}">{$item.action_data.audio_album}</a>
        {jrCore_list module="jrAudio" search1="audio_album_url = `$item.action_data.audio_album_url`" search2="_profile_id = `$item.action_data._profile_id`" template='null' order_by="audio_display_order numerical_asc" limit="4" assign="preview"}
        {if isset($preview[0]) && is_array($preview[0])}
            {foreach $preview as $_i}
                <br>&bull;&nbsp;<a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$_i._item_id}/{$_i.audio_title_url}">{$_i.audio_title|truncate:60}</a>
            {/foreach}
        {/if}

    {elseif $item.action_mode == 'update_album'}

        {jrCore_lang module="jrAudio" id="63" default="Updated an Audio Album"}: <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/albums/{$item.action_data.audio_album_url}" title="{$item.action_data.audio_album|jrCore_entity_string}">{$item.action_data.audio_album}</a>
        {jrCore_list module="jrAudio" search1="audio_album_url = `$item.action_data.audio_album_url`" search2="_profile_id = `$item.action_data._profile_id`" template='null' order_by="audio_display_order numerical_asc" limit="4" assign="preview"}
        {if isset($preview[0]) && is_array($preview[0])}
            {foreach $preview as $_i}
                <br>&bull;&nbsp;<a href="{$jamroom_url}/{$_i.profile_url}/{$murl}/{$_i._item_id}/{$_i.audio_title_url}">{$_i.audio_title|truncate:60}</a>
            {/foreach}
        {/if}

    {else}

        {jrCore_lang module="jrAudio" id="55" default="Updated an Audio File"}:<br>
        <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.audio_title_url}" title="{$item.action_data.audio_title|jrCore_entity_string}">{$item.action_data.audio_title}</a>
    {/if}
    </span>
</div>
