{if isset($_items)}
    {jrCore_module_url module="jrProfile" assign="murl"}
    <div class="container">
        {foreach from=$_items item="item"}
        {if $item@first || ($item@iteration % 4) == 1}
        <div class="row">
        {/if}
            <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                <div class="center mb15">
                    <a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" width="190" height="190" alt=$item.profile_name title=$item.profile_name class="iloutline img_shadow"}</a><br>
                    <br>
                    <h3><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></h3><br>
                </div>
            </div>
        {if $item@last || ($item@iteration % 4) == 0}
        </div>
        {/if}
        {/foreach}
    </div>
    {if $info.total_pages > 1}
        <div style="float:left; padding-top:9px;padding-bottom:9px;">
            {if $info.prev_page > 0}
                <span class="button-arrow-previous" onclick="jrLoad('#hot_artists','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#hotartists').offset().top });return false;">&nbsp;</span>
            {else}
                <span class="button-arrow-previous-off">&nbsp;</span>
            {/if}
            {if $info.next_page > 1}
                <span class="button-arrow-next" onclick="jrLoad('#hot_artists','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#hotartists').offset().top });return false;">&nbsp;</span>
            {else}
                <span class="button-arrow-next-off">&nbsp;</span>
            {/if}
        </div>
        <div class="clear"></div>
    {/if}
{/if}

