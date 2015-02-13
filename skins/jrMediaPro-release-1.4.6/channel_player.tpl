{jrCore_include template="meta.tpl"}
<body>

<div class="container">
    <div class="row">
        <div class="col12 last">
            {if isset($_post.playlist_id)}
                {jrPlaylist_util mode="embed_playlist" playlist_id=$_post.playlist_id template="channel_playlist.tpl"}
            {else}
                {capture name="row_template" assign="video_player_row"}
                    {literal}
                        {if isset($_items)}
                        {if isset($_conf.jrMediaPro_auto_play) && $_conf.jrMediaPro_auto_play == 'on'}
                        {assign var="vap" value="true"}
                        {elseif isset($_post.autoplay) && $_post.autoplay == 'true'}
                        {assign var="vap" value="true"}
                        {else}
                        {assign var="vap" value="false"}
                        {/if}

                        {foreach from=$_items item="item"}
                        {jrCore_media_player type="jrVideo_player_dark" module="jrVideo" field="video_file" item=$item autoplay=$vap}
                        {/foreach}
                        {/if}
                    {/literal}
                {/capture}
                {jrCore_list module="jrVideo" order_by="_item_id asc" limit="1" search1="_item_id = `$option`" template=$video_player_row}
            {/if}
        </div>
    </div>
</div>

</body>
</html>