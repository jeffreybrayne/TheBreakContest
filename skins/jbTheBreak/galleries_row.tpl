{if isset($_post.option) && $_post.option == 'alpha'}
    {assign var="jrload_div" value="alpha_galleries"}
    {assign var="scrolltag" value="alphagallery"}
{elseif isset($_post.option) && $_post.option == 'newest'}
    {assign var="jrload_div" value="new_galleries"}
    {assign var="scrolltag" value="newgallery"}
{else}
    {assign var="jrload_div" value="top_galleries"}
    {assign var="scrolltag" value="topgallery"}
{/if}
{jrCore_module_url module="jrGallery" assign="murl"}
{if isset($_items)}
<div class="container">

    {foreach from=$_items item="item"}
    {if $item@first || ($item@iteration % 4) == 1}
    <div class="row">
    {/if}
        <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
            <div class="center mb10">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.gallery_title title=$item.gallery_title class="iloutline img_scale" style="max-width:182px;"}</a><br>
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all" title="{$item.gallery_title}"><b>{if strlen($item.gallery_title) > 15}{$item.gallery_title|truncate:15:"...":false}{else}{$item.gallery_title}{/if}</b></a>
                {* jrCore_item_update_button module="jrGallery" profile_id=$item._profile_id item_id=$item._item_id *}
                {* jrCore_item_delete_button module="jrGallery" profile_id=$item._profile_id action="`$murl`/delete_save/`$item.profile_url`/`$item.gallery_title_url`" *}<br>
                {if isset($_post.option) && $_post.option == 'newest'}
                    <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="153" default="Created"}:</span> <span class="hl-4">{$item._created|date_format:"%A<br>%B %e, %Y<br>%I:%M:%S %p"}</span>
                {elseif isset($_post.option) && $_post.option == 'top'}
                    <div style="width:91px;padding-top:4px;margin:0 auto;">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrGallery" index="1" item_id=$item._item_id current=$item.gallery_rating_1_average_count|default:0 votes=$item.gallery_rating_1_count|default:0 }
                    </div>
                {/if}
            </div>
        </div>
    {if $item@last || ($item@iteration % 4) == 0}
    </div>
    {/if}
    {/foreach}

    <div class="row">
        <div class="col12 last">
            {if $info.total_pages > 1}

                {if $info.page == '1'}

                    <div class="float-right"><a onclick="jrLoad('#{$jrload_div}','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;"><div class="button-more">&nbsp;</div></a></div>
                    <div class="clear"></div>

                {else}

                    <div class="block">
                        <table style="width:100%;">
                            <tr>

                                <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                                    {if isset($info.prev_page) && $info.prev_page > 0}
                                        <a onclick="jrLoad('#{$jrload_div}','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;"><span class="button-arrow-previous">&nbsp;</span></a>
                                    {else}
                                        <span class="button-arrow-previous-off">&nbsp;</span>
                                    {/if}
                                </td>

                                <td class="body_4 p5 middle" style="width:50%;text-align:center;border:1px solid #282828;">
                                    {if $info.total_pages <= 5 || $info.total_pages > 500}
                                        {$info.page} &nbsp;/ {$info.total_pages}
                                    {else}
                                        <form name="form" method="post" action="_self">
                                            <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#{$jrload_div}','{$info.page_base_url}/p=' +sel);$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;">
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
                                        <a onclick="jrLoad('#{$jrload_div}','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;"><span class="button-arrow-next">&nbsp;</span></a>
                                    {else}
                                        <span class="button-arrow-next-off">&nbsp;</span>
                                    {/if}
                                </td>

                            </tr>
                        </table>
                    </div>

                {/if}
            {/if}
        </div>
    </div>

</div>
{/if}
