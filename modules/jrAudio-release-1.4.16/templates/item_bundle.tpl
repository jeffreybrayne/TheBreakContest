{if isset($bundle_item)}

    {jrCore_module_url module="jrAudio" assign="murl"}
    <div class="container">
        <div class="row">
            <div class="col2">
                <div class="block_image">
                    <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$bundle_item._item_id size="small" crop="auto" class="iloutline" alt=$bundle_item.audio_title width=false height=false}</a>
                </div>
            </div>
            <div class="col1">
                {if $bundle_item.audio_file_extension == 'mp3'}
                    {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$bundle_item}
                {/if}
            </div>
            <div class="col5">
                <div class="p5" style="overflow-wrap:break-word">
                    <h3><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.audio_title_url}">{$bundle_item.audio_title}</a></h3><br>
                    <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$bundle_item.audio_genre}</span><br>
                    <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/albums/{$bundle_item.audio_album_url}">{$bundle_item.audio_album}</a></span>
                </div>
            </div>
            <div class="col2">
                <div class="p5">
                    {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$bundle_item._item_id current=$bundle_item.audio_rating_1_average_count|default:0 votes=$bundle_item.audio_rating_1_number|default:0 }
                </div>
            </div>
            <div class="col2 last">
                <div class="block_config">
                    {jrCore_module_function function="jrFoxyCartBundle_button" module="jrAudio" field="audio_file" item=$bundle_item}
                    {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrAudio`$bundle_item._item_id`" module="jrAudio" bundle_id=$bundle_id item=$bundle_item}
                </div>
            </div>
        </div>
    </div>

{/if}
