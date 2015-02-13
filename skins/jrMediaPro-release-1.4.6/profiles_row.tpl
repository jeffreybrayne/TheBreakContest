{if isset($_items)}
    {jrCore_module_url module="jrProfile" assign="purl"}
    <div class="container">
    {foreach from=$_items item="item"}
        <div class="row">
            <div class="col12 last">
                <div class="head_title mb10 box_shadow">
                    <h2>{$item.quota_jrProfile_name}: <span class="hl-3 bold">{$item.quota_jrProfile_profile_count}</span></h2>
                    <div style="float:right; padding-top:4px;">
                        {if $item.profile_quota_id == $_conf.jrMediaPro_artist_quota}
                            {assign var="more_url" value="artists"}
                        {elseif $item.profile_quota_id == $_conf.jrMediaPro_member_quota}
                            {assign var="more_url" value="members"}
                        {else}
                            {assign var="more_url" value="account/`$item.profile_quota_id`/`$item.quota_jrProfile_name`"}
                        {/if}
                        <a href="{$jamroom_url}/{$more_url}" title="More {$item.quota_jrProfile_name}"><div class="button-more">&nbsp;</div></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col12 last">
                {capture name="row_template" assign="profile_row"}
                    {literal}
                        {if isset($_items)}
                        {jrCore_module_url module="jrProfile" assign="purl"}
                        <div class="container">
                            {foreach from=$_items item="row"}
                            {if $row@first || ($row@iteration % 4) == 1}
                            <div class="row">
                            {/if}
                                <div class="col3{if $row@last || ($row@iteration % 4) == 0} last{/if}">
                                    <div class="center mb20">
                                        <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="large" crop="auto" class="iloutline img_scale" alt=$row.profile_name title=$row.profile_name style="max-width:190px;margin-bottom:10px;"}</a><br>
                                        <a href="{$jamroom_url}/{$row.profile_url}" title="{$row.profile_name}"><span class="capital bold">{$row.profile_name|truncate:15:"...":false}</span></a><br>
                                        <br>
                                    </div>
                                </div>
                            {if $row@last || ($row@iteration % 4) == 0}
                            </div>
                            {/if}
                            {/foreach}
                        </div>
                        {/if}
                    {/literal}
                {/capture}
                {if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
                    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="8" quota_id=$item.profile_quota_id template=$profile_row require_image="profile_image"}
                {else}
                    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="8" quota_id=$item.profile_quota_id template=$profile_row}
                {/if}
            </div>
        </div>
    {/foreach}
    </div>
{/if}
