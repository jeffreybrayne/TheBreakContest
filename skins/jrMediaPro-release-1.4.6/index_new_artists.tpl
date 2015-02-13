{* ROW TEMPLATE *}
{capture name="row_template" assign="new_artists_template"}
{literal}
{if isset($_items)}
<div class="container">
    {foreach from=$_items item="row"}
    {if $row@first || ($row@iteration % 6) == 1}
    <div class="row">
    {/if}
        <div class="col2{if $row@last || ($row@iteration % 6) == 0} last{/if}">
            <div class="center" style="padding:10px;">
                <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow img_scale" style="max-width:190px;"}</a>
            </div>
        </div>
    {if $row@last || ($row@iteration % 6) == 0}
    </div>
    {/if}
    {/foreach}
</div>
{if $info.total_pages > 1}
<div style="float:left; padding-top:9px;padding-bottom:9px;">
    {if $info.prev_page > 0}
    <span class="button-arrow-previous" onclick="jrLoad('#newest_artists','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#newartists').offset().top -100 }, 'slow');return false;">&nbsp;</span>
    {else}
    <span class="button-arrow-previous-off">&nbsp;</span>
    {/if}
    {if $info.next_page > 1}
    <span class="button-arrow-next" onclick="jrLoad('#newest_artists','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#newartists').offset().top -100 }, 'slow');return false;">&nbsp;</span>
    {else}
    <span class="button-arrow-next-off">&nbsp;</span>
    {/if}
</div>
{/if}

<div style="float:right; padding-top:9px;">
    <a href="{$jamroom_url}/artists" title="More Artists"><div class="button-more">&nbsp;</div></a>
</div>

<div class="clear"> </div>
{/if}
{/literal}
{/capture}


{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template=$new_artists_template require_image="profile_image" pagebreak="6" page=$_post.p}
{else}
    {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template=$new_artists_template pagebreak="6" page=$_post.p}
{/if}
