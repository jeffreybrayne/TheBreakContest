{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}

        <div class="SliderSongs">
            <div class="container">
                <div class="row">
                    <div class="col7">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="width:1%;text-align:center;vertical-align:middle;padding:0;margin:0;">
                                    {if $item.audio_file_extension == 'mp3'}
                                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                                    {else}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                                        <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                                    {/if}
                                </td>
                                <td style="width:99%;">
                                    <div style="text-align:left;padding-left:10px;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}"><span style="text-transform:uppercase;font-weight:bold;font-size:12px;">{$item.audio_title}</span></a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col5 last">
                        <div style="padding-right:5px;">
                            <div class="block_config nowrap">
                                {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {/foreach}
{/if}
