{jrCore_module_url module="jrYouTube" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">
        <form method="POST" onsubmit="jrYouTube_insert_url();return false">
            <table class="page_table">
                <tr class="page_table_row">
                    <td class="page_table_cell" colspan="4">
                        <div id="youtube_notice_box" class="item error" style="display: none"><!-- youtube item not found  messages load here --></div>
                        <input placeholder="{jrCore_lang module="jrYouTube" id="4" default="YouTube ID or URL"}" class="form_text" type="text" id="youtube_url" style="width: 98%"/>
                    </td>
                    <td class="page_table_cell">
                        <input class="form_button" type="submit" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}"/>
                    </td>
                </tr>
                {foreach $_items as $key => $item}
                    <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                        <td class="page_table_cell center" style="width:5%"><img src="{$item.youtube_artwork_url}" class="img_scale"></td>
                        <td class="page_table_cell"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}" target="_blank">{$item.youtube_title}</a></td>
                        <td class="page_table_cell center" style="width:16%"><a href="javascript:void(0);" onclick="jrYouTube_load_videos(1, '{$item.profile_url}', false, false);">@{$item.profile_name}</a></td>
                        <td class="page_table_cell center" style="width:16%"><a href="javascript:void(0);" onclick="jrYouTube_load_videos(1, false, '{$item.youtube_category_url}', false);">{$item.youtube_category}</a></td>
                        <td class="page_table_cell" style="width:10%"><input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrYouTube_insert_video({$item._item_id})"></td>
                    </tr>
                {/foreach}
            </table>
        </form>
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
                                        <input type="button" class="form_button" value="&lt;" onclick="jrYouTube_load_videos({$info.prev_page},'{$_post.profile_url}','{$_post.category_url}','{$_post.ss}');">
                                    {/if}
                                </td>
                                <td style="width:50%;text-align:center">
                                    {$info.this_page}&nbsp;/&nbsp;{$info.total_pages}
                                </td>
                                <td style="width:25%;text-align:right">
                                    {if $info.next_page > 0}
                                        <input type="button" class="form_button" value="&gt;" onclick="jrYouTube_load_videos({$info.next_page},'{$_post.profile_url}','{$_post.category_url}','{$_post.ss}');">
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
        <form method="POST" onsubmit="jrYouTube_insert_url();return false">
            <table class="page_table">
                <tr class="page_table_row">
                    <td class="page_table_cell">
                        <div id="youtube_notice_box" class="item error" style="display: none"><!-- youtube item not found  messages load here --></div>
                        <input placeholder="{jrCore_lang module="jrYouTube" id="4" default="YouTube ID or URL"}" class="form_text" type="text" id="youtube_url" style="width: 98%"/>
                    </td>
                    <td class="page_table_cell" style="width:10%">
                        <input class="form_button" type="submit" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}"/>
                    </td>
                </tr>
                <tr class="page_table_row">
                    <td class="page_table_cell center" colspan="2">{jrCore_lang module="jrYouTube" id="43" default="no youtube videos were found"}</td>
                </tr>
            </table>
        </form>
    </div>
{/if}


<script type="text/javascript">

    function jrYouTube_insert_video(item_id) {
        top.tinymce.activeEditor.insertContent('[jrEmbed module="jrYouTube" id="' + item_id + '"]');
        top.tinymce.activeEditor.windowManager.close();
    }
</script>
