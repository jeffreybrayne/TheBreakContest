{jrCore_module_url module="jrAudio" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrAudio" field="audio_file" item=$item}

        </div>
        <h1>{$item.audio_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrAudio" id="41" default="Audio"}</a> &raquo; {$item.audio_title}
        </div>
    </div>

    <div class="block_content">

        <div class="item">

            <div class="jraudio_detail_player">
                <div class="jraudio_detail_player_left">

                    {* Make sure we're active *}
                    {if isset($item.audio_active) && $item.audio_active == 'off' && isset($item.quota_jrAudio_audio_conversions) && $item.quota_jrAudio_audio_conversions == 'on'}

                        <p class="center">{jrCore_lang module="jrAudio" id="40" default="This audio file is currently being processed and will appear here when complete."}</p>

                    {elseif $item.audio_file_extension == 'mp3'}

                        {assign var="ap" value="`$_conf.jrCore_active_skin`_auto_play"}
                        {jrCore_media_player module="jrAudio" field="audio_file" item=$item autoplay=$_conf.$ap}<br>

                        <div style="text-align:left;padding-left:6px">
                            <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a></span><br>
                            <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$item.audio_genre}</span><br>
                            <span class="info">{jrCore_lang module="jrAudio" id="51" default="streams"}:</span> <span class="info_c">{$item.audio_file_stream_count|default:"0"|number_format}</span><br>
                            {if !empty($item.audio_file_item_price)}
                                <span class="info">{jrCore_lang module="jrAudio" id="54" default="purchase"}:</span> <span class="info_c">{$item.audio_file_original_extension}, {$item.audio_file_original_size|jrCore_format_size}, {$item.audio_file_length}</span>
                            {/if}
                            <br>{jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0}
                        </div>

                    {else}

                        {* allow downloads if we are not blocked *}
                        {if isset($_conf.jrAudio_block_download) && $_conf.jrAudio_block_download == 'off'}
                            <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_icon icon="download"}</a><br>
                        {/if}

                        <div style="text-align:left;padding-left:6px">
                            <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}"><span class="info_c">{$item.audio_album}</span></a><br>
                            <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$item.audio_genre}</span><br>
                            {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0}
                        </div>

                    {/if}
                </div>

                <!-- <div class="jraudio_detail_player_right">
                    {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="large" class="iloutline img_shadow" alt=$item.audio_title width=false height=false}
                </div> -->

            </div>

        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrAudio" item=$item}

    </div>

</div>
