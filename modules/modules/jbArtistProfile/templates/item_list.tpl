{jrCore_module_url module="jbArtistProfile" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">

            <div class="block_config">
                {jrCore_item_list_buttons module="jbArtistProfile" item=$item}
            </div>

            <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.artistprofile_title_url}">{$item.artistprofile_title}</a></h2>
            <br>
        </div>

    {/foreach}
{/if}
