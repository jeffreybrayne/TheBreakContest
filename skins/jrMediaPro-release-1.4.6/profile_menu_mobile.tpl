{if isset($_items)}
    <span class="small ml10">Profile Menu</span>
    <select name="pchoice" id="profile_select_menu" onchange="var v=this.options[this.selectedIndex].value;jrCore_window_location(v);">
        {foreach from=$_items key="module" item="entry"}
            {if $entry.active == '1'}
                <option value="{$entry.target}" selected="selected"> {$entry.label}</option>
            {else}
                <option value="{$entry.target}"> {$entry.label}</option>
            {/if}
        {/foreach}
    </select>
{/if}

