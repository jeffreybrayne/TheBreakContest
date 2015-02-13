{jrCore_module_url module="jrGallery" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item['action_mode'] == 'create'}
        {jrCore_lang module="jrGallery" id="23" default="Created a New Gallery"}:
    {else}
        {jrCore_lang module="jrGallery" id="39" default="Updated a Gallery"}:
    {/if}
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data.gallery_title_url}/all" title="{$item.action_data.gallery_title|jrCore_entity_string}">{$item.action_data.gallery_title|truncate:70}</a>
    </span>

    <br>

    {* each image template *}
    {capture assign="imgs"}
    {literal}
        {if isset($_items)}
        {foreach $_items as $_i}
        <a href="{jrGallery_get_gallery_image_url item=$_i}" title="{$_i.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$_i._item_id size="icon96" crop="portrait" class="iloutline" alt=$_i.gallery_alt_text width=64 height=64}</a>
        {/foreach}
        {/if}
    {/literal}
    {/capture}
    {jrCore_list module="jrGallery" search1="gallery_title_url = `$item.action_data.gallery_title_url`" search2="_profile_id = `$item.action_data._profile_id`" template=$imgs order_by="gallery_order numerical_asc" limit=4}

</div>
