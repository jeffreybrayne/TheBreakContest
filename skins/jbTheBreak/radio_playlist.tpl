{if isset($_conf.jbTheBreak_auto_play) && $_conf.jbTheBreak_auto_play == 'on'}
    {assign var="sap" value="true"}
{elseif isset($_post.autoplay) && $_post.autoplay == 'true'}
    {assign var="sap" value="true"}
{else}
    {assign var="sap" value="false"}
{/if}

{jrCore_media_player type="jrPlaylist_player_dark" module="jrPlaylist" item=$item autoplay=$sap}
