{* ROW TEMPLATE *}
{capture name="row_template" assign="new_members_template"}
    {literal}
    {if isset($_items)}
    <div class="container">
        <div class="row">
            {foreach from=$_items item="row"}
            <div class="col4{if $row@last} last{/if}">
                <div class="center">
                    <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" width="175" height="175" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow"}</a><br>
                    <h4><a href="{$jamroom_url}/{$row.profile_url}" title="{$row.profile_name}">{if strlen($row.profile_name) > 20}{$row.profile_name|truncate:20:"...":false}{else}{$row.profile_name}{/if}</a></h4>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
    {if $info.total_pages > 1}
    <div style="float:left; padding-top:9px;padding-bottom:9px;">
        {if $info.prev_page > 0}
        <span class="button-arrow-previous" onclick="jrLoad('#newest_members','{$info.page_base_url}/p={$info.prev_page}');">&nbsp;</span>
        {else}
        <span class="button-arrow-previous-off">&nbsp;</span>
        {/if}
        {if $info.next_page > 1}
        <span class="button-arrow-next" onclick="jrLoad('#newest_members','{$info.page_base_url}/p={$info.next_page}');">&nbsp;</span>
        {else}
        <span class="button-arrow-next-off">&nbsp;</span>
        {/if}
    </div>
    {/if}
    <div style="float:right; padding-top:9px;">
        <a href="{$jamroom_url}/members" title="More Artists"><div class="button-more">&nbsp;</div></a>
    </div>

    <div class="clear"> </div>
    {/if}
    {/literal}
{/capture}


{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {if isset($_conf.jrMediaPro_member_quota) && $_conf.jrMediaPro_member_quota > 0}
        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrMediaPro_member_quota`" template=$new_members_template require_image="profile_image" pagebreak="3" page=$_post.p}
    {else}
        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" template=$new_members_template require_image="profile_image" pagebreak="3" page=$_post.p}
    {/if}
{else}
    {if isset($_conf.jrMediaPro_member_quota) && $_conf.jrMediaPro_member_quota > 0}
        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrMediaPro_member_quota`" template=$new_members_template pagebreak="3" page=$_post.p}
    {else}
        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" template=$new_members_template pagebreak="3" page=$_post.p}
    {/if}
{/if}