{if isset($_conf.jrMediaPro_auto_play) && $_conf.jrMediaPro_auto_play == 'on'}
    {assign var="vap" value="true"}
{elseif isset($_post.autoplay) && $_post.autoplay == 'true'}
    {assign var="vap" value="true"}
{else}
    {assign var="vap" value="false"}
{/if}

{jrCore_media_player type="jrPlaylist_player_dark" module="jrPlaylist" item=$item autoplay=$vap}
