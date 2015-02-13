{if isset($_items)}
        {foreach from=$_items item="item"}
        <div class="body_5 page" style="margin-right:auto;">
            <div class="container">
                <div class="row">

                    <div class="col2">
                        <div class="center mb10">
                            <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="large" crop="auto" class="iloutline img_shadow img_scale" alt=$item.profile_name title=$item.profile_name style="max-width:290px;"}</a><br>
                        </div>
                    </div>
                    <div class="col10 last">
                        <div class="left" style="padding-left:15px;">
                            {if jrUser_is_admin() || jrUser_is_master()}
                                <div class="block_config">
                                    {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$item._profile_id`" profile_id=$item._profile_id item_id=$item._profile_id title="Update Profile"}
                                </div>
                            {/if}
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="121" default="Name"}</span>: <a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}"><span class="capital bold">{$item.profile_name}</span></a><br>
                            {if isset($item.profile_influences) && strlen($item.profile_influences) > 0}
                                <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="143" default="Influences"}</span>: <span class="capital">{$item.profile_influences}</span><br>
                            {/if}
                            {if isset($_post.option) && $_post.option == 'by_newest'}
                                <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="122" default="Joined"}</span>: <span class="hl-2">{$item._created|date_format:"%A, %B %e, %Y %I:%M:%S %p"}</span><br>
                            {/if}
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Songs"}</span>: <span class="hl-3">{$item.profile_jrAudio_item_count}</span><br>
                            <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="50" default="Views"}</span>: <span class="hl-4">{$item.profile_view_count}</span><br>
                            <br>
                            {if strlen($item.profile_bio) > 0}
                                <span class="capital bold">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"}</span>:<br>
                                {if strlen($item.profile_bio) > 150}{$item.profile_bio|jrCore_format_string:$item._profile_id:$item.profile_quota_id|truncate:150:"...":false}{else}{$item.profile_bio|jrCore_format_string:$item.profile_quota_id}{/if}
                            {/if}
                        </div>
                        <div class="float-right" style=";padding-top:9px;">
                            <a href="{$jamroom_url}/{$item.profile_url}" title="View {$item.profile_name}"><div class="button-more">&nbsp;</div></a>
                        </div>
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

                <td class="body_4 p5 middle" style="width:50%;text-align:center;color:#000;">
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
