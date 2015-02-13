<div style="display: inline-block; width:100%">
    {if !isset($item) && isset($_params.youtube_id)}
        {jrYouTube_embed youtube_id=$_params.youtube_id type="iframe" width="100%"}
    {else}
        {jrYouTube_embed item_id=$item._item_id type="iframe" width="100%"}
    {/if}
</div>