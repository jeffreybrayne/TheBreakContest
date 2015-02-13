{jrCore_module_url module="jrGallery" assign="murl"}
{foreach $_items as $item}
    {if $item@iteration is div by 4}
        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_image_name}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="icon" width="120" class="index_img" crop="portrait" style="margin:0 0 13px 0" alt=$item.gallery_title}</a>
    {else}
        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_image_name}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="icon" width="120" class="index_img" crop="portrait" style="margin:0 9px 13px 0" alt=$item.gallery_title}</a>
    {/if}
{/foreach}

