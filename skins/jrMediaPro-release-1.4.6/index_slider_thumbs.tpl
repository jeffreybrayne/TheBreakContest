{if isset($_items)}
{jrCore_module_url module="jrAudio" assign="murl"}
{foreach from=$_items item="item"}
<li>
    {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xxlarge" crop="square" alt=$item.profile_name title=$item.profile_name style="max-width:150px;"}
</li>
{/foreach}
{/if}
