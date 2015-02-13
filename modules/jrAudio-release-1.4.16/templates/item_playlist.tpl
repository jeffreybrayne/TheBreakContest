{if isset($playlist_item)}

    {jrCore_module_url module="jrAudio" assign="murl"}
    <div id="a{$playlist_item._item_id}" class="item">
        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="p5">
                        <a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/{$playlist_item._item_id}/{$playlist_item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$playlist_item._item_id size="small" crop="auto" class="iloutline" alt=$playlist_item.audio_title width=false height=false}</a>
                    </div>
                </div>
                <div class="col7">
                    <div class="p5">
                        <h3><a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/{$playlist_item._item_id}/{$playlist_item.audio_title_url}">{$playlist_item.audio_title}</a></h3><br>
                        <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$playlist_item.audio_genre}</span><br>
                        <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/albums/{$playlist_item.audio_album_url}"><span class="info_c">{$playlist_item.audio_album}</span></a>
                    </div>
                </div>
                <div class="col1">
                    <div class="p5">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$playlist_item._item_id current=$playlist_item.audio_rating_1_average_count|default:0 votes=$playlist_item.audio_rating_1_number|default:0 }
                    </div>
                </div>
                <div class="col2 last">
                    <div class="block_config">
                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$playlist_item._item_id}
                        {jrCore_module_function function="jrPlaylist_remove_button" id="#a`$playlist_item._item_id`" module="jrAudio" playlist_id=$playlist_id item_id=$playlist_item._item_id}
                    </div>
                </div>
            </div>
        </div>
    </div>

{/if}
