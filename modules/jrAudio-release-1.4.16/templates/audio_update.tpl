<table class="page_section_header jraudio_detail">
    <tr>
        <td class="jraudio_detail_left">
            {jrCore_media_player type="jrAudio_button" module=$module field=$field_name item=$item}
        </td>
        <td class="jraudio_detail_right">
            {if isset($item.original_name) && strlen($item.original_name) > 0}
            <span class="jraudio_title">{jrCore_lang module="jrAudio" id="14" default="audio file"}:&nbsp;</span> {$item.original_name}<br>
            {else}
            <span class="jraudio_title">{jrCore_lang module="jrAudio" id="14" default="audio file"}:&nbsp;</span> {$item.name}<br>
            {/if}
            <span class="jraudio_title">{jrCore_lang module="jrAudio" id="26" default="audio length"}:&nbsp;</span> {$item.length}<br>
            <span class="jraudio_title">{jrCore_lang module="jrAudio" id="27" default="audio bitrate"}:&nbsp;</span> {$item.bitrate}kbps
        </td>
    </tr>
</table>
