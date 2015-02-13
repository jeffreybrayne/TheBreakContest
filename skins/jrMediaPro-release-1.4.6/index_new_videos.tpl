{capture name="row_template" assign="new_videos_template"}
    {literal}
        {jrCore_module_url module="jrVideo" assign="murl"}
    {if isset($_items)}
    <div class="container">
        <div class="row">
            {foreach from=$_items item="row"}
            <div class="col4{if $row@last} last{/if}">
                <div class="center">
                    <a href="{$jamroom_url}/{$row.profile_url}/{$murl}/{$row._item_id}/{$row.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$row._item_id size="medium" crop="auto" width="175" height="175" alt=$row.video_title title=$row.video_title class="iloutline img_shadow"}</a><br>
                    <h4><a href="{$jamroom_url}/{$row.profile_url}" title="{$row.profile_name}">{if strlen($row.video_title) > 20}{$row.video_title|truncate:20:"...":false}{else}{$row.video_title}{/if}</a></h4>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
    {if $info.total_pages > 1}
    <div style="float:left; padding-top:12px;">
        {if $info.prev_page > 0}
        <span class="button-arrow-previous" onclick="jrLoad('#newest_videos','{$info.page_base_url}/p={$info.prev_page}');">&nbsp;</span>
        {else}
        <span class="button-arrow-previous-off">&nbsp;</span>
        {/if}
        {if $info.next_page > 1}
        <span class="button-arrow-next" onclick="jrLoad('#newest_videos','{$info.page_base_url}/p={$info.next_page}');">&nbsp;</span>
        {else}
        <span class="button-arrow-next-off">&nbsp;</span>
        {/if}
    </div>
    {/if}
    <div style="float:right; padding-top:9px;">
        <a href="{$jamroom_url}/videos" title="More Artists"><div class="button-more">&nbsp;</div></a>
    </div>

    <div class="clear"> </div>
    {/if}
    {/literal}
{/capture}



{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {jrCore_list module="jrVideo" order_by="_created desc" template=$new_videos_template require_image="video_image" pagebreak="3" page=$_post.p}
{else}
    {jrCore_list module="jrVideo" order_by="_created desc" template=$new_videos_template pagebreak="3" page=$_post.p}
{/if}
