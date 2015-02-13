{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    <div class="container">
        <div class="row">
            {foreach from=$_items item="item"}
                <div class="col4{if $item@last} last{/if}">
                    <div class="center mb15">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}" title="{$item.audio_title}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" width="190" height="190" alt=$item.audio_title title=$item.audio_title class="iloutline img_shadow"}</a><br>
                        <br>
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}" title="{$item.audio_title}"><span class="hl-3">{$item.audio_title|truncate:20:"...":false}</span></a></h3><br>
                        <h4><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name|truncate:20:"...":false}</a></h4><br>
                        <div class="page box_shadow" style="width: 190px;margin:10px auto 10px auto;">

                            <div class="container">
                                <div class="row">
                                    <div class="col12 last">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="width:5%;text-align:center;vertical-align:middle;padding:0;margin:0;">
                                                    {if $item.audio_file_extension == 'mp3'}
                                                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                                                    {else}
                                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                                                        <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                                                    {/if}
                                                </td>
                                                <td>
                                                    <div class="center p5">
                                                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                                                        <br><span class="capital hl-2">{jrCore_lang module="jrRating" id="3" default="Votes"}:</span>&nbsp;{$item.audio_rating_overall_count}&nbsp;
                                                        <span class="capital hl-4">{jrCore_lang module="jrRating" id="2" default="Average:"}</span>&nbsp;{$item.audio_rating_overall_average_count}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    {if $info.total_pages > 1}
        <div style="float:left; padding-top:9px;padding-bottom:9px;">
            {if $info.prev_page > 0}
                <span class="button-arrow-previous" onclick="jrLoad('#top_singles','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#tsingles').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            {else}
                <span class="button-arrow-previous-off">&nbsp;</span>
            {/if}
            {if $info.next_page > 1}
                <span class="button-arrow-next" onclick="jrLoad('#top_singles','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#tsingles').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            {else}
                <span class="button-arrow-next-off">&nbsp;</span>
            {/if}
        </div>
    {/if}
    <div style="float:right; padding-top:9px;">
        <a href="{$jamroom_url}/music" title="More Singles"><div class="button-more">&nbsp;</div></a>
    </div>

    <div class="clear"> </div>
{/if}

