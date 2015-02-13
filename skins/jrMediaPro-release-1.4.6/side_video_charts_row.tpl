{* ROW TEMPLATE *}
{if isset($_items)}

<div class="border-1px" style="display:table;width:100%;">
    <div style="display:table-row;">
        <div class="table-title" style="display:table-cell;text-align:center;padding:4px;border-bottom:1px solid #282828;border-right:1px solid #282828;">Rnk</div>
        <div class="table-title" style="display:table-cell;text-align:left;padding:4px;border-bottom:1px solid #282828;">Video</div>
    </div>
    {jrCore_module_url module="jrVideo" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="body_2" style="display:table-row;">
            <div style="display:table-cell;text-align:center;vertical-align:middle;padding:4px;font-size:15px;{if $item@last}border-right:1px solid #282828;{else}border-right:1px solid #282828;border-bottom:1px solid #282828;{/if}"><span class="hl-4"><b>{$item.list_rank}</b></span></div>
            <div style="display:table-cell;text-align:left;vertical-align:top;padding:4px;font-size:11px;{if $item@last}{else}border-bottom:1px solid #282828;{/if}">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}"><span class="capital hl-2">{$item.video_title|truncate:25:"...":false}</span></a><br>
                <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name|truncate:25:"...":false}</a>
                <div style="text-align: right;">
                    <span class="bold" style="font-size:9px;">Plays:</span> <span class="hl-3">{$item.video_file_stream_count}</span>
                </div>
            </div>
        </div>

    {/foreach}
</div>

    {if $info.total_pages > 1}

    <div style="float:left;padding-top:8px;">
        {if $info.prev_page > 0}
            <span class="button-arrow-previous" onclick="jrLoad('#side_video_charts','{$jamroom_url}/side_video_charts/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#ttvcharts').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            {else}
            <span class="button-arrow-previous-off">&nbsp;</span>
        {/if}
        {if $info.next_page > 1}
            <span class="button-arrow-next" onclick="jrLoad('#side_video_charts','{$jamroom_url}/side_video_charts/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#ttvcharts').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            {else}
            <span class="button-arrow-next-off">&nbsp;</span>
        {/if}
    </div>

    <div style="float:right; padding-top:9px;">
        <a href="{$jamroom_url}/video_charts" title="More Video Charts"><div class="button-more">&nbsp;</div></a>
    </div>

    <div class="clear"> </div>
    <div class="spacer20"> </div>
    {/if}
{/if}
