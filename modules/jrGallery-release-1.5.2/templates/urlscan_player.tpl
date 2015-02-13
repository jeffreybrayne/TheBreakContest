{jrCore_module_url module="jrGallery" assign="murl"}
{if isset($_items)}
{foreach from=$_items item="item"}
    <a href="{jrGallery_get_gallery_image_url item=$item}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="small" crop="auto" class="iloutline" alt=$item.gallery_alt_text}</a>
{/foreach}
{else}
    <a href="{jrGallery_get_gallery_image_url item=$item}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="xxxlarge" class="img_scale img_shadow" alt=$item.gallery_alt_text width=false height=false}</a>
{/if}
