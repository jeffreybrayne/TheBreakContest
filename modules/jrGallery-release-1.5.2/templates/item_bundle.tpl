{if isset($bundle_item)}

    {jrCore_module_url module="jrGallery" assign="murl"}
    <div class="container">
        <div class="row">
            <div class="col2">
                <div class="block_image">
                    <a href="{jrGallery_get_gallery_image_url item=$bundle_item}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$bundle_item._item_id size="small" crop="auto" class="img_scale iloutline" alt=$bundle_item.gallery_alt_text width=false height=false}</a>
                </div>
            </div>
            <div class="col7">
                <div class="p5" style="overflow-wrap:break-word">
                    <h2><a href="{jrGallery_get_gallery_image_url item=$bundle_item}">
                    {if isset($bundle_item.gallery_image_title)}
                        {$bundle_item.gallery_image_title}
                    {else}
                        {$bundle_item.gallery_image_name}
                    {/if}
                    </a></h2>
                    {if isset($bundle_item.gallery_caption) && strlen($bundle_item.gallery_caption) > 0}
                        <br>{$bundle_item.gallery_caption|jrCore_format_string:$bundle_item.profile_quota_id|truncate:200}
                    {/if}
                </div>
            </div>
            <div class="col2">
                <div class="p5">
                    {jrCore_module_function function="jrRating_form" type="star" module="jrGallery" index="1" item_id=$bundle_item._item_id current=$bundle_item.gallery_rating_1_average_count|default:0 votes=$bundle_item.gallery_rating_1_number|default:0}
                </div>
            </div>
            <div class="col1 last">
                <div class="block_config">
                    {jrCore_module_function function="jrFoxyCartBundle_button" module="jrGallery" field="gallery_image" item=$bundle_item}
                    {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrGallery`$bundle_item._item_id`" module="jrGallery" bundle_id=$bundle_id item=$bundle_item}
                </div>
            </div>
        </div>
    </div>

{/if}