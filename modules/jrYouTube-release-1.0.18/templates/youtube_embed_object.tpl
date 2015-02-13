{if strpos($jamroom_url, 'https') === 0}
    {assign var="scheme" value="https"}
{else}
    {assign var="scheme" value="http"}
{/if}
<object width="{$params.width}" height="{$params.height}">
    <param name="movie" value="{$scheme}://www.youtube.com/v/{$youtube_id}&hl=en&fs=1"></param>
    <param name="allowFullScreen" value="true"></param>
    <param name="allowscriptaccess" value="always"></param>
    <param name="wmode" value="transparent"></param>
    <embed src="{$scheme}://www.youtube.com/v/{$youtube_id}&hl=en&fs=1&autoplay={$params.auto_play}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="{$params.width}" height="{$params.height}" wmode="transparent"></embed>
</object>
