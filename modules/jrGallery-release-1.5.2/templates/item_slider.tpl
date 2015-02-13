{if isset($_items)}
    {jrCore_module_url module="jrGallery" assign="murl"}
    <div class="gallery_slider_prev">
    {if $info.prev_page > 0}
        <a onclick="jrGallery_slider('{$_items[0]._profile_id}', '{$_items[0].gallery_title_url}', '{$info.prev_page}', '{$info.pagebreak}');">{jrCore_icon icon="previous" size="20"}</a>
    {else}
        {jrCore_icon icon="cancel" size="20"}
    {/if}
    </div>
    {foreach $_items as $img}
        <div class="gallery_slider_img">
            <a href="{jrGallery_get_gallery_image_url item=$img}#gallery_img">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$img._item_id size="small" crop="auto" class="img_shadow" alt=$img.gallery_alt_text width=46 height=46}</a>
        </div>
    {/foreach}
    {if $info.next_page > 0}
    <div class="gallery_slider_next">
        <a onclick="jrGallery_slider('{$_items[0]._profile_id}','{$_items[0].gallery_title_url}','{$info.next_page}', '{$info.pagebreak}');">{jrCore_icon icon="next" size="20"}</a>
    </div>
    {/if}
    <div style="clear:both"></div>
{/if}
