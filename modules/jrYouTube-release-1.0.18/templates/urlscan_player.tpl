{if $_item_id != 0}
    {jrYouTube_embed item_id=$_item_id type="iframe" width="100%" height="300" auto_play=true}
{else}
    <iframe type="text/html" width="100%" height="300" src="{jrCore_server_protocol}://www.youtube.com/embed/{$remote_media_id}?autoplay=1" frameborder="0"></iframe>
{/if}
