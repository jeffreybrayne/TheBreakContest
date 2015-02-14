{jrCore_module_url module=$params.module assign="murl"}

{assign var="ext" value="`$params.field`_extension"}

<script type="text/javascript">
$(document).ready(function(){
    new jPlayerPlaylist({
        jPlayer: "#jquery_jplayer_{$uniqid}",
        cssSelectorAncestor: "#jp_container_{$uniqid}"
    },[
    {if is_array($media)}
        {foreach $media as $a}
            {if $a._item.$ext == 'mp3'}
            {
                title: "{$a.title}",
                artist: "{$a.artist}",
                mp3: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.mp3",
                {if strstr($formats, 'oga')}
                oga: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.ogg",
                {/if}
                poster: "{$jamroom_url}/{$a.module_url}/image/audio_image/{$a.item_id}/large"
            },
            {/if}
        {/foreach}
    {/if}
    ],{
        error: function(res) { jrCore_stream_url_error(res); },
        playlistOptions: {
            autoPlay: {$autoplay},
            displayTime: 'fast'
        },
        swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
        supplied: "{$formats}",
        solution: "{$solution}",
        volume: 0.8,
        wmode: 'window',
        preload: 'none'
    });
});
</script>

<div class="jr_audio_dark_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jquery_jplayer_{$uniqid}" class="jp-jplayer"></div>

    <div id="jp_container_{$uniqid}" class="jp-audio">
        <div class="jp-type-playlist">
            <div class="jp-gui">
                <div class="jp-interface">
                    <div class="jp-seek-holder">
                        <div class="jp-progress">
                            <div class="jp-seek-bar">
                                <div class="jp-play-bar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="jp-current-time"></div>
                    <div class="jp-duration"></div>
                    <div class="jp-controls-holder">
                        <ul class="jp-controls">
                            <li><a href="javascript:" class="jp-previous" tabindex="1" title="{jrCore_lang module="jrAudio" id="44" default="previous"}"></a></li>
                            <li><a href="javascript:" class="jp-play" tabindex="2" title="{jrCore_lang module="jrAudio" id="1" default="play"}"></a></li>
                            <li><a href="javascript:" class="jp-pause" tabindex="3" title="{jrCore_lang module="jrAudio" id="2" default="pause"}"></a></li>
                            <li><a href="javascript:" class="jp-next" tabindex="4" title="{jrCore_lang module="jrAudio" id="45" default="next"}"></a></li>
                            <li><a href="javascript:" class="jp-stop" tabindex="5" title="{jrCore_lang module="jrAudio" id="3" default="stop"}"></a></li>
                            <li><a href="javascript:" class="jp-mute" tabindex="6" title="{jrCore_lang module="jrAudio" id="4" default="mute"}"></a></li>
                            <li><a href="javascript:" class="jp-unmute" tabindex="7" title="{jrCore_lang module="jrAudio" id="5" default="unmute"}"></a></li>
                            <li><a href="javascript:" class="jp-volume-max" tabindex="8" title="{jrCore_lang module="jrAudio" id="6" default="max volume"}"></a></li>
                        </ul>
                        <div class="jp-volume-bar">
                            <div class="jp-volume-bar-value"></div>
                        </div>
                        <ul class="jp-toggles">
                            <li><a href="javascript:" class="jp-shuffle" tabindex="10" title="{jrCore_lang module="jrAudio" id="46" default="shuffle"}"></a></li>
                            <li><a href="javascript:" class="jp-shuffle-off" tabindex="11" title="{jrCore_lang module="jrAudio" id="47" default="shuffle off"}"></a></li>
                            <li><a href="javascript:" class="jp-repeat" tabindex="12" title="{jrCore_lang module="jrAudio" id="7" default="repeat"}"></a></li>
                            <li><a href="javascript:" class="jp-repeat-off" tabindex="13" title="{jrCore_lang module="jrAudio" id="8" default="repeat off"}"></a></li>
                        </ul>
                    </div>
                    <div class="jp-title">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="jp-playlist">
                <ul>
                    <li></li>
                </ul>
            </div>
        </div>
    </div>
</div>

