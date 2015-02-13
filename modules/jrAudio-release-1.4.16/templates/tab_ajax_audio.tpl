{jrCore_module_url module="jrAudio" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            {foreach $_items as $key => $item}
            <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                <td class="page_table_cell center" style="width:5%">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" class="img_scale" alt=$item.audio_title width=false height=false}</td>
                <td class="page_table_cell center" style="width:2%">{jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}</td>
                <td class="page_table_cell" style="width:30%"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}" target="_blank">{$item.audio_title}</a></td>
                <td class="page_table_cell center" style="width:16%"><a href="javascript:void(0);" onclick="jrAudio_load_audio(1, '{$item.profile_url}', false, false, false);">@{$item.profile_name}</a></td>
                <td class="page_table_cell center" style="width:16%"><a href="javascript:void(0);" onclick="jrAudio_load_audio(1, false, false, '{$item.audio_album_url}', false);">{$item.audio_album}</a></td>
                <td class="page_table_cell center" style="width:16%"><a href="javascript:void(0);" onclick="jrAudio_load_audio(1, false, '{$item.audio_genre_url}', false, false);"{$jamroom_url}/{$murl}/tab/genre_url={$item.audio_genre_url}/__ajax=1">{$item.audio_genre}</a></td>
                <td class="page_table_cell" style="width:15%"><input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrAudio_embed_insert_audio({$item._item_id})"></td>
            </tr>
        {/foreach}
        </table>
    </div>

    {* prev/next page footer links *}
    {if $info.prev_page > 0 || $info.next_page > 0}
        <div class="container">
            <div class="row">
                <div class="col12 last">
                    <div class="block">
                        <table style="width:100%">
                            <tr>
                                <td style="width:25%">
                                    {if $info.prev_page > 0}
                                        <input type="button" class="form_button" value="&lt;" onclick="jrAudio_load_audio({$info.prev_page},'{$_post.profile_url}','{$_post.genre_url}','{$_post.album_url}','{$_post.ss}');">
                                    {/if}
                                </td>
                                <td style="width:50%;text-align:center">
                                    {$info.this_page}&nbsp;/&nbsp;{$info.total_pages}
                                </td>
                                <td style="width:25%;text-align:right">
                                    {if $info.next_page > 0}
                                        <input type="button" class="form_button" value="&gt;" onclick="jrAudio_load_audio({$info.next_page},'{$_post.profile_url}','{$_post.genre_url}','{$_post.album_url}','{$_post.ss}');">
                                    {/if}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{else}
    <div class="container">
        <table class="page_table">
            <tr class="page_table_row">
                <td class="page_table_cell center" colspan="8">{jrCore_lang module="jrAudio" id="53" default="no audio files were found"}</td>
            </tr>
        </table>
    </div>
{/if}


<script type="text/javascript">
    function jrAudio_embed_insert_audio(id) {
        top.tinymce.activeEditor.insertContent('[jrEmbed module="jrAudio" id="' + id + '"]');
        top.tinymce.activeEditor.windowManager.close();
    }
</script>