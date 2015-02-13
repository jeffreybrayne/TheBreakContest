{if isset($_items)}
    <div class="container">

        {foreach from=$_items item="item"}
            <div class="body_5 page mb20" style="margin-right:auto;">
                <div class="row">
                    <div class="col9">
                        {if isset($item.page_location) && $item.page_location == '0'}
                            <h3><a href="{$jamroom_url}/page/{$item._item_id}/{$item.page_title_url}">{$item.page_title}</a></h3><br>
                        {else}
                            <h3><a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}">{$item.page_title}</a></h3><br>
                        {/if}
                        <span class="normal">{$item.page_body|truncate:350:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}</span>
                    </div>
                    <div class="col3 last">
                        <div class="block_config">
                            <div class="p5">
                                {if isset($item.page_location) && $item.page_location == '0'}
                                    <a href="{$jamroom_url}/page/{$item._item_id}/{$item.page_title_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" class="iloutline" item_id=$item._user_id size="xsmall" alt=$item.user_name}</a><br>
                                {else}
                                    <a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" class="iloutline" item_id=$item._user_id size="xsmall" alt=$item.user_name}</a><br>
                                {/if}
                            </div>
{*
                            <div class="p5">
                                {jrCore_item_update_button module="jrPage" profile_id=$item._profile_id item_id=$item._item_id}
                                {jrCore_item_delete_button module="jrPage" profile_id=$item._profile_id item_id=$item._item_id}<br>
                            </div>
 *}
                            <div class="p5">
                                {if isset($item.page_location) && $item.page_location == '0'}
                                    <a href="{$jamroom_url}/page/{$item._item_id}/{$item.page_title_url}"><div class="button-more">&nbsp;</div></a>
                                {else}
                                    <a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}"><div class="button-more">&nbsp;</div></a>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
    {if $info.total_pages > 1}
        <div class="block">
            <table style="width:100%;">
                <tr>

                    <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                        {if isset($info.prev_page) && $info.prev_page > 0}
                            <a href="{$info.page_base_url}/p={$info.prev_page}"><span class="button-arrow-previous">&nbsp;</span></a>
                        {else}
                            <span class="button-arrow-previous-off">&nbsp;</span>
                        {/if}
                    </td>

                    <td class="body_4 p5 middle" style="width:50%;text-align:center;border:1px solid #282828;">
                        {if $info.total_pages <= 5 || $info.total_pages > 500}
                            {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;window.location='{$info.page_base_url}/p=' +sel">
                                    {for $pages=1 to $info.total_pages}
                                        {if $info.page == $pages}
                                            <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                        {else}
                                            <option value="{$pages}"> {$pages}</option>
                                        {/if}
                                    {/for}
                                </select>&nbsp;/&nbsp;{$info.total_pages}
                            </form>
                        {/if}
                    </td>

                    <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                        {if isset($info.next_page) && $info.next_page > 1}
                            <a href="{$info.page_base_url}/p={$info.next_page}"><span class="button-arrow-next">&nbsp;</span></a>
                        {else}
                            <span class="button-arrow-next-off">&nbsp;</span>
                        {/if}
                    </td>

                </tr>
            </table>
        </div>
    {/if}
{/if}
