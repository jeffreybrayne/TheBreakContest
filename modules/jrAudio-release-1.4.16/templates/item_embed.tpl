<div style="display: inline-block; width:75%">
{if isset($item) && is_array($item)}
    {jrCore_media_player module="jrAudio" field="audio_file" item=$item}
{elseif isset($_items) && is_array($_items)}
    {jrCore_media_player module="jrAudio" field="audio_file" items=$_items}
{/if}
</div>
