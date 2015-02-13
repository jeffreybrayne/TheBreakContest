{if isset($_params.order) && $_params.order == 'alpha'}
    {assign var="jrload_div" value="alpha_accounts"}
    {assign var="scrolltag" value="alphaaccounts"}
{elseif isset($_params.order) && $_params.order == 'newest'}
    {assign var="jrload_div" value="new_accounts"}
    {assign var="scrolltag" value="newaccounts"}
{elseif isset($_params.order) && $_params.order == 'most_viewed'}
    {assign var="jrload_div" value="most_viewed_accounts"}
    {assign var="scrolltag" value="viewedaccounts"}
{else}
    {assign var="jrload_div" value="alpha_accounts"}
    {assign var="scrolltag" value="alphaaccounts"}
{/if}
{if isset($_items)}
    <div class="container">
        <div class="row">

            {foreach from=$_items item="item"}
                <div class="col3{if $item@last} last{/if}">
                    <div class="center mb10">
                        <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" class="iloutline img_shadow img_scale" alt=$item.profile_name title=$item.profile_name style="max-width:182px;max-height:182px;"}</a><br>
                        <br><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}"><span class="capital bold">{$item.profile_name|truncate:15:"...":false}</span></a>
                        <br>
                        {if isset($scrolltag) && $scrolltag == 'newaccounts'}
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="122" default="Joined"}:</span> <span class="hl-4">{$item._created|date_format:"%A<br>%B %e, %Y<br>%I:%M:%S %p"}</span>
                        {elseif isset($scrolltag) && $scrolltag == 'viewedaccounts'}
                            <span class="capital bold"> Views</span> <span class="hl-3">({$item.profile_view_count})</span>
                        {/if}
                    </div>
                </div>
            {/foreach}

            <div class="clear"></div>
        </div>
    </div>
    {if $info.total_pages > 1}
        {if $info.page == '1'}

            <div class="float-right"><a onclick="jrLoad('#{$jrload_div}','{$jamroom_url}/account_list/order={$_params.order}/qid={$_params.qid}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;"><div class="button-more">&nbsp;</div></a></div>
            <div class="clear"></div>

        {else}

            <div class="block">
                <table style="width:100%;">
                    <tr>

                        <td class="body_4 p5 middle" style="width:25%;text-align:center;">
                            {if isset($info.prev_page) && $info.prev_page > 0}
                                <a onclick="jrLoad('#{$jrload_div}','{$jamroom_url}/account_list/order={$_params.order}/qid={$_params.qid}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;"><span class="button-arrow-previous">&nbsp;</span></a>
                            {else}
                                <span class="button-arrow-previous-off">&nbsp;</span>
                            {/if}
                        </td>

                        <td class="body_4 p5 middle" style="width:50%;text-align:center;color:#000;">
                            {if $info.total_pages <= 5 || $info.total_pages > 500}
                                {$info.page} &nbsp;/ {$info.total_pages}
                            {else}
                                <form name="form" method="post" action="_self">
                                    <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#{$jrload_div}','{$jamroom_url}/account_list/order={$_params.order}/qid={$_params.qid}/p=' +sel);$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;">
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
                                <a onclick="jrLoad('#{$jrload_div}','{$jamroom_url}/account_list/order={$_params.order}/qid={$_params.qid}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#{$scrolltag}').offset().top });return false;"><span class="button-arrow-next">&nbsp;</span></a>
                            {else}
                                <span class="button-arrow-next-off">&nbsp;</span>
                            {/if}
                        </td>

                    </tr>
                </table>
            </div>
        {/if}
    {/if}
{/if}
