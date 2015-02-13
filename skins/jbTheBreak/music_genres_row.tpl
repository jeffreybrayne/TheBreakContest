{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}
        <option value="{$item.audio_genre}">{$item.audio_genre}</option>
    {/foreach}
{/if}
