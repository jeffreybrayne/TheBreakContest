{if isset($_items) && is_array($_items)}
    <div class="online_status_table">
    {foreach $_items as $item}

        {if $item.user_is_online == '1'}
        <div class="online_status_online" style="display:table-row">
        {else}
        <div class="online_status_offline" style="display:table-row">
        {/if}

            <div class="online_status_image">
                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" crop="auto" alt=$item.user_name class="img_shadow" width="40" height="40" style="padding:2px"}
            </div>

            <div class="online_status_user">
                <h2><a href="{$jamroom_url}/{$item.profile_url}">{$item.user_name}</a></h2><br>
                {if $item.user_is_online == '1'}
                <i>{jrCore_lang module="jrUser" id="101" default="online"}</i>
                {else}
                <i>{jrCore_lang module="jrUser" id="102" default="offline"}</i>
                {/if}
            </div>

        </div>

    {/foreach}
    </div>
{/if}