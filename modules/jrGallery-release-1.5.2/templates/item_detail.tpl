{jrCore_module_url module="jrGallery" assign="murl"}
<div class="block" id="gallery_img">

    <div class="title">
        <div class="block_config">

            {jrCore_module_function function="jrGallery_download_button" item=$item}
            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrGallery" field="gallery_image" item=$item}
            {jrCore_module_function function="jrFoxyCartBundle_button" module="jrGallery" field="gallery_image" item=$item}
            {jrCore_item_update_button module="jrGallery" profile_id=$item._profile_id action="`$murl`/detail/id=`$item._item_id`" item_id=$item._item_id}
            {jrCore_item_delete_button module="jrGallery" profile_id=$item._profile_id action="`$murl`/delete_image/id=`$item._item_id`" item_id=$item._item_id}

        </div>
        <h1>{$item.gallery_title}</h1>

        <div class="breadcrumbs">
            {if isset($quota_jrGallery_gallery_group) && $quota_jrGallery_gallery_group == 'off'}
                {jrCore_lang module="jrGallery" id=38 default="Images" assign="heading"}
            {else}
                {jrCore_lang module="jrGallery" id=24 default="Image Galleries" assign="heading"}
            {/if}
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{$heading}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a> &raquo; {$item.gallery_title}
        </div>
    </div>

    <div class="block_content">

        {if isset($item.gallery_pending) && $item.gallery_pending == '1'}
        <div class="page_notice_drop mt10">
            <div id="page_notice" class="page_notice success">
                {jrCore_pending_notice module="jrGallery" item=$item}
            </div>
        </div>
        {/if}

        {* Gallery Slider *}
        <div style="padding:12px 0 0 0;padding-left:16px">
            <div id="gallery_slider">
                {capture assign="imgs"}
                {literal}
                    {if isset($_items)}
                    {jrCore_module_url module="jrGallery" assign="murl"}
                    <div class="gallery_slider_prev">
                        {if $info.prev_page > 0}
                        <a onclick="jrGallery_slider('{$_items[0]._profile_id}', '{$_items[0].gallery_title_url}', '{$info.prev_page}', '{$info.pagebreak}');">{jrCore_icon icon="previous" size="20"}</a>
                        {elseif $info.total_pages > 1}
                        {jrCore_icon icon="cancel" size="20"}
                        {/if}
                    </div>
                    {foreach $_items as $img}
                    <div class="gallery_slider_img">
                        <a href="{jrGallery_get_gallery_image_url item=$img}#gallery_img">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$img._item_id size="small" crop="portrait" class="img_shadow" alt=$img.gallery_alt_text width=46 height=46}</a>
                    </div>
                    {/foreach}
                    {if $info.next_page > 0}
                    <div class="gallery_slider_next">
                        <a onclick="jrGallery_slider('{$_items[0]._profile_id}','{$_items[0].gallery_title_url}','{$info.next_page}', '{$info.pagebreak}');">{jrCore_icon icon="next" size="20"}</a>
                    </div>
                    {/if}
                    {/if}
                {/literal}
                {/capture}
                {assign var="pb" value="12"}
                {if jrCore_is_mobile_device()}
                    {assign var="pb" value="4"}
                {/if}
                {jrCore_list module="jrGallery" search1="_profile_id = `$item._profile_id`" search2="gallery_title_url = `$item.gallery_title_url`" order_by="gallery_order numerical_asc" pagebreak=$pb page=$smarty.session.jrGallery_page_num template=$imgs}
                <div style="clear:both"></div>
            </div>
        </div>

        {if !empty($item.gallery_image_title)}
            <div class="item center">
                <h2>{$item.gallery_image_title}</h2>
            </div>
        {/if}

        {* Galery Image *}
        <div class="item center">
            <div class="block_image" style="position: relative;">
                {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="1280" id="gallery_img_src" class="img_scale img_shadow" alt=$item.gallery_alt_text width=false height=false}
                <div class="lb-nav" style="display: block;">
                    {if $prev._item_id > 0}
                        <a href="{jrGallery_get_gallery_image_url item=$prev}#gallery_img" class="lb-prev" style="display:block"></a>
                    {/if}
                    {if $next._item_id > 0}
                        <a href="{jrGallery_get_gallery_image_url item=$next}#gallery_img" class="lb-next" style="display:block"></a>
                    {/if}
                </div>
            </div>
            <div class="gallery_rating">
                {jrCore_module_function function="jrRating_form" type="star" module="jrGallery" index="1" item_id=$item._item_id current=$item.gallery_rating_1_average_count|default:0 votes=$item.gallery_rating_1_count|default:0}
            </div>
        </div>

        {if !empty($item.gallery_caption)}
            <div class="item">
                {$item.gallery_caption|jrCore_format_string:$item.profile_quota_id}
            </div>
        {/if}

        {* bring in module features *}
        {jrCore_item_detail_features module="jrGallery" item=$item}

    </div>

</div>
