{jrCore_module_url module="jbRegister" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">

            <div class="block_config">
                {jrCore_item_list_buttons module="jbRegister" item=$item}
            </div>

            <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.register_title_url}">{$item.register_title}</a></h2>
            <br>
        </div>

    {/foreach}
{/if}
