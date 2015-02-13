{jrCore_module_url module="jrGallery" assign="murl"}
{if isset($_items) && is_array($_items)}
{foreach $_items as $key => $item}

  <div class="jrgallery_update_div">
    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/xxxlarge/_v={$item.gallery_image_time}" target="_blank" data-lightbox="images" title="{$item.gallery_image_name|jrCore_entity_string}">
        {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="icon" class="jrgallery_update_image" alt=$item.gallery_alt_text width=false height=false}
    </a><br>
    <input type="button" class="jrgallery_update_button" value="{jrCore_lang module="jrGallery" id="20" default="details"}" onclick="window.location='{$jamroom_url}/{$murl}/detail/id={$item._item_id}'">
    <input type="button" class="jrgallery_update_button" value="{jrCore_lang module="jrGallery" id="21" default="delete"}" onclick="jrCore_window_location('{$jamroom_url}/{$murl}/delete_image/id={$item._item_id}')">
  </div>

{/foreach}
{/if}
