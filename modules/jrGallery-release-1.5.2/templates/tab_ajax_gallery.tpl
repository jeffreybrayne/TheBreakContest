{jrCore_module_url module="jrGallery" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            <tbody>
            <tr class="nodrag nodrop">
                <th style="width:5%" class="page_table_header">{jrCore_lang module="jrGallery" id="26" default="size"}</th>
                <th style="width:5%" class="page_table_header">{jrCore_lang module="jrGallery" id="28" default="position"}</th>
            </tr>
            <tr class="page_table_row">
                <td class="page_table_cell center">
                    <select id="imgsizg" class="form_select" style="width: auto;">
                        {foreach $image_sizes as $pixels => $desc}
                            {if $pixels == 256}
                                <option value="{$pixels}" selected="selected">{$desc}</option>
                            {else}
                                <option value="{$pixels}">{$desc}</option>
                            {/if}
                        {/foreach}
                    </select>
                </td>
                <td class="page_table_cell center">
                    <select id="imgposg" class="form_select" style="width: auto;">
                        <option value="">{jrCore_lang module="jrGallery" id="30" default="normal"}</option>
                        <option value="left">{jrCore_lang module="jrGallery" id="31" default="float left"}</option>
                        <option value="right">{jrCore_lang module="jrGallery" id="32" default="float right"}</option>
                        <option value="stretch">{jrCore_lang module="jrGallery" id="43" default="stretch"}</option>
                    </select>
                </td>
            </tr>
            <tr class="page_table_row_alt">
                <td colspan="2">
                {foreach $_items as $key => $item}
                    <div style="float:left; padding:20px;text-align:center">
                        <a href="javascript:void(0);" onclick="jrGallery_insert_image('/{$murl}/image/gallery_image/{$item._item_id}', '{$item.gallery_image_name|escape}')" title="{$item.gallery_image_name}">
                            {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="icon" class="jrgallery_update_image" crop="auto" alt=$item.gallery_image_name width=false height=false title=$item.gallery_title}<br/>
                            <a href="javascript:void(0);" onclick="jrGallery_load_images(1, '{$item.profile_url}', false, false);">@{$item.profile_name}</a>
                        </a>
                    </div>
                {/foreach}
                </td>
            </tr>

            </tbody>
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
                                        <input type="button" class="form_button" value="&lt;" onclick="jrGallery_load_images({$info.prev_page},'{$_post.profile_url}','{$_post.ss}');">
                                    {/if}
                                </td>
                                <td style="width:50%;text-align:center">
                                    {$info.this_page}&nbsp;/&nbsp;{$info.total_pages}
                                </td>
                                <td style="width:25%;text-align:right">
                                    {if $info.next_page > 0}
                                        <input type="button" class="form_button" value="&gt;" onclick="jrGallery_load_images({$info.next_page},'{$_post.profile_url}','{$_post.ss}');">
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
                <td class="page_table_cell center" colspan="2">{jrCore_lang module="jrGallery" id="45" default="no gallery images were found"}</td>
            </tr>
        </table>
    </div>
{/if}


<script type="text/javascript">
    /**
     * output the image off into the editor.
     * @param image_url
     * @param title
     */
    function jrGallery_insert_image(image_url, title) {
        var ed = top.tinymce.activeEditor, dom = ed.dom;
        var imgsiz = $('#imgsizg').val();
        var imgpos = $('#imgposg').val();
        if (imgsiz == '') {
            imgsiz = 'icon';
        }
        switch (imgpos) {
            case 'stretch':
                imgpos = 'width:100%';
                break;
            case 'left':
                imgpos = 'float:left';
                break;
            case 'right':
                imgpos = 'float:right';
                break;
            case '':
                imgpos = '';
                break;
        }
        ed.insertContent(dom.createHTML('img', {
            src: '{$jamroom_url}' + image_url + '/' + imgsiz,
            alt: title,
            title: title,
            border: 0,
            style: imgpos
        }));
        ed.windowManager.close();
    }
</script>