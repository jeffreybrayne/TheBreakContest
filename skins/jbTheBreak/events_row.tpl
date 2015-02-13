{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="body_5 page" style="margin-right:auto;">

        <div class="container">

            <div class="row">

                <div class="col2">
                    <div class="center">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="icon" alt=$item.event_title title=$item.event_title class="iloutline" style="max-width:140px;"}</a><br>
                    </div>
                </div>

                <div class="col8">
                    <div class="p5" style="padding-left:10px;">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a></h3><br>
                        <span class="capital bold">{jrCore_lang module="jrEvent" id="13" default="Featuring"}:</span> <a href="{$jamroom_url}/{$item.profile_url}"><span class="capital">{$item.profile_name}</span></a><br>
                        <span class="capital bold">{jrCore_lang module="jrEvent" id="11" default="Event Date"}:</span> <span class="hl-2">{$item.event_date|jrCore_date_format}</span><br>
                        <span class="capital bold">{jrCore_lang module="jrEvent" id="6" default="Event location"}:</span> <span class="hl-3 capital">{$item.event_location|truncate:60}</span>
                        <div style="padding:4px 0 8px 4px;">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrEvent" index="1" item_id=$item._item_id current=$item.event_rating_1_average_count|default:0 votes=$item.event_rating_1_count|default:0 }
                        </div>
                    </div>
                </div>

                <div class="col2 last">
{*
                    <div class="nowrap float-right">
                        {jrCore_item_update_button module="jrEvent" profile_id=$item._profile_id item_id=$item._item_id}
                        {jrCore_item_delete_button module="jrEvent" profile_id=$item._profile_id item_id=$item._item_id}
                    </div>
 *}
                </div>

            </div>

        </div>

    </div>

    {/foreach}

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


