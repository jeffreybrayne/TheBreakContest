{if isset($_items)}
    {foreach from=$_items item="row"}
    <li><a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="xxlarge" crop="auto" alt=$row.profile_name title=$row.profile_name style="max-width:525px;max-height:348px;"}</a><p class="caption"><a href="{$jamroom_url}/{$row.profile_url}">{$row.profile_name}</a></p></li>
    {/foreach}
{/if}
