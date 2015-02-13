{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="page mt5 box_shadow">

            <div class="container">
                <div class="row">
                    <div class="col6">
                        <div class="table_div">
                            <div class="table_div_row">
                                <div class="table_div_cell">
                                    {if $item.audio_file_extension == 'mp3'}
                                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                                    {else}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                                        <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                                    {/if}
                                </div>
                                <div class="table_div_cell" style="white-space: nowrap; vertical-align: middle;">
                                    <h3 style="padding-left:15px;margin-bottom:0;"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h3><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col4">
                        <div class="pt10">
                            <span class="capital hl-2">{jrCore_lang skin=$_conf.jrCore_active_skin id="170" default="length"}:</span>&nbsp;{$item.audio_file_length}&nbsp;
                            <span class="capital hl-4">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="plays"}:</span>&nbsp;{$item.audio_file_stream_count}
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="block_config nowrap pt5">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                        </div>
                    </div>
                </div>
            </div>

        </div>

    {/foreach}

{/if}
