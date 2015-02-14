<script type="text/javascript">
$(document).ready(function(){
    var pl = $('#{$uniqid}');
    pl.jPlayer({
        swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
        ready: function(event) { return true; },
        cssSelectorAncestor: "#jp_container_{$uniqid}",
        supplied: '{$formats}',
        solution: "{$solution}",
        volume: 0.8,
        wmode: 'window',
        consoleAlerts: true,
        preload: 'none',
        error: function(res) { jrCore_stream_url_error(res); },
        play: function() {
            pl.jPlayer("pauseOthers");
        }
    });
    var ps = $('#{$uniqid}_play');
    ps.click(function(e) {
        pl.jPlayer("clearMedia");
        pl.jPlayer("setMedia", {
            {if strstr($formats, 'oga')}
            oga: "{$jamroom_url}/{$media[0].module_url}/stream/{$params.field}/{$media[0].item_id}/key=[jrCore_media_play_key]/file.ogg",
            {/if}
            mp3: "{$jamroom_url}/{$media[0].module_url}/stream/{$params.field}/{$media[0].item_id}/key=[jrCore_media_play_key]/file.mp3"
        });
        pl.jPlayer("play");
        e.preventDefault();
    });
});
</script>

{jrCore_lang module="jrAudio" id="1" default="play" assign="play"}
{jrCore_lang module="jrAudio" id="2" default="pause" assign="pause"}

{jrCore_module_url module="jrImage" assign="murl"}
{if isset($params.image)}
    {assign var="play_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_play.png"}
    {assign var="play_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_play_hover.png"}
    {assign var="pause_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_pause.png"}
    {assign var="pause_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/`$params.image`_pause_hover.png"}
{elseif is_file("`$jamroom_dir`/skins/`$_conf.jrCore_active_skin`/img/button_player_play.png")}
    {assign var="play_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_play.png"}
    {assign var="play_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_play_hover.png"}
    {assign var="pause_i" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_pause.png"}
    {assign var="pause_h" value="`$jamroom_url`/`$murl`/img/skin/`$_conf.jrCore_active_skin`/button_player_pause_hover.png"}
{else}
    {assign var="play_i" value="`$jamroom_url`/`$murl`/img/module/jrAudio/button_player_play.png"}
    {assign var="play_h" value="`$jamroom_url`/`$murl`/img/module/jrAudio/button_player_play_hover.png"}
    {assign var="pause_i" value="`$jamroom_url`/`$murl`/img/module/jrAudio/button_player_pause.png"}
    {assign var="pause_h" value="`$jamroom_url`/`$murl`/img/module/jrAudio/button_player_pause_hover.png"}
{/if}

<div class="button_player" onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="{$uniqid}" class="jp-jplayer"></div>
    <div id="jp_container_{$uniqid}" class="jp-audio">
        <div class="jp-type-single">
            <div class="jp-gui jp-interface">
                <ul class="jp-controls">
                    <li><a id="{$uniqid}_play" href="javascript:" class="jp-play" tabindex="1"><img src="{$play_i}" alt="{$play}" title="{$play}" onmouseover="$(this).attr('src','{$play_h}');" onmouseout="$(this).attr('src','{$play_i}');"></a></li>
                    <li><a href="javascript:" class="jp-pause" tabindex="1"><img src="{$pause_i}" alt="{$pause}" title="{$pause}" onmouseover="$(this).attr('src','{$pause_h}');" onmouseout="$(this).attr('src','{$pause_i}');"></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
