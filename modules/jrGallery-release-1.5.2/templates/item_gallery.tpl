{jrCore_module_url module="jrGallery" assign="murl"}
<div class="block">

    {if isset($_items)}
        <div class="title">
            <div class="block_config">

                {if isset($_items[0].gallery_image_item_bundle)}
                {jrCore_db_get_item module="jrFoxyCartBundle" item_id=$_items[0].gallery_image_item_bundle assign="bundle"}
                {/if}
                {if is_array($bundle)}
                    {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$bundle.bundle_item_price no_bundle="true" item=$bundle}
                {/if}

                {if !$show_all_galleries}
                {jrCore_item_create_button module="jrGallery" profile_id=$_profile_id}
                {jrCore_item_update_button module="jrGallery" profile_id=$_profile_id item_id=$_items[0]._item_id}
                {jrCore_item_delete_button module="jrGallery" profile_id=$_profile_id action="`$murl`/delete_save/`$_items[0].profile_url`/`$_items[0].gallery_title_url`"}
                {/if}

            </div>
            <h1>{$_items[0].gallery_title}</h1>

            <div class="breadcrumbs">
                {if isset($quota_jrGallery_gallery_group) && $quota_jrGallery_gallery_group == 'off'}
                    {jrCore_lang module="jrGallery" id=38 default="Images" assign="heading"}
                {else}
                    {jrCore_lang module="jrGallery" id=24 default="Image Galleries" assign="heading"}
                {/if}
                {if $show_all_galleries}
                <a href="{$jamroom_url}/{$_items[0].profile_url}/">{$_items[0].profile_name}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}">{$heading}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}/all">All</a>
                {else}
                <a href="{$jamroom_url}/{$_items[0].profile_url}/">{$_items[0].profile_name}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}">{$heading}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}/{$_items[0].gallery_title_url}/all">{$_items[0].gallery_title}</a>
                {/if}
            </div>
        </div>
        <div class="block_content">

            <div class="block">
                {if !jrCore_is_mobile_device()}
                <div style="float:right;margin-top:12px;">
                    <a onclick="jrGallery_xup(2)" class="form_button">2</a>
                    <a onclick="jrGallery_xup(3)" class="form_button">3</a>
                    <a onclick="jrGallery_xup(4)" class="form_button">4</a>
                    <a onclick="jrGallery_xup(6)" class="form_button">6</a>
                    <a onclick="jrGallery_xup(8)" class="form_button">8</a>
                </div>
                {/if}
                <div class="gallery_lightbox">
                    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$_items[0]._item_id}/1280" data-lightbox="images" title="{$item.gallery_caption|default:$item.gallery_image_name|jrGallery_title_name:$item.gallery_caption}">{jrCore_icon icon="search2"}&nbsp;&nbsp;<h3>{jrCore_lang module="jrGallery" id="37" default="View images in Lightbox"}</h3></a>
                </div>
            </div>

            <ul class="sortable grid">
                {foreach $_items as $key => $item}
                    <li data-id="{$item._item_id}" style="width:24.5%">
                        <div id="p{$item._item_id}" class="p5" style="position:relative">

                            {if $item@iteration > 1}
                                <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/1280" data-lightbox="images" title="{$item.gallery_caption|default:$item.gallery_image_name|jrCore_entity_string}"></a>
                            {/if}
                            <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="medium" crop="auto" class="gallery_img iloutline" alt=$item.gallery_alt_text width=false height=false}</a><br>

                            <div class="gallery_rating">
                                {jrCore_module_function function="jrRating_form" type="star" module="jrGallery" index="1" item_id=$item._item_id current=$item.gallery_rating_1_average_count|default:0 votes=$item.gallery_rating_1_count|default:0}
                            </div>

                            {if jrProfile_is_profile_owner($_profile_id)}
                                <script>$(function () {
                                    $('#p{$item._item_id}').hover(function () {
                                        $('#m{$item._item_id}').fadeToggle('fast');
                                    });
                                });</script>
                                <div id="m{$item._item_id}" class="gallery_actions">
                                    {jrCore_item_update_button module="jrGallery" action="`$murl`/detail/id=`$item._item_id`" profile_id=$item._profile_id item_id=$item._item_id height="16" width="16"}
                                    {jrCore_item_delete_button module="jrGallery" action="`$murl`/delete_image/id=`$item._item_id`" profile_id=$item._profile_id item_id=$item._item_id height="16" width="16"}
                                </div>
                            {/if}

                        </div>
                    </li>
                {/foreach}
            </ul>

        </div>
    {/if}

</div>


<style type="text/css">
    .sortable {
        margin: 0;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .sortable.grid {
        overflow: hidden;
    }

    .sortable li {
        margin: 0 auto;
        list-style: none;
        display: inline-block;
        width: 32%;
    }
</style>

{* We want to allow the item owner to re-order *}
{if jrProfile_is_profile_owner($_profile_id)}
    <style type="text/css">
        .sortable li { cursor: move; }
        li.sortable-placeholder { border: 2px dashed #BBB; background: none; height: 62px; width: 16%; margin: 13px; }
    </style>
    <script type="text/javascript">
        $(function () {
            $('.sortable').sortable().bind('sortupdate', function (event, ui) {
                // Triggered when the user stopped sorting and the DOM position has changed.
                var o = $('ul.sortable li').map(function () {
                    return $(this).data("id");
                }).get();
                $.post(core_system_url + '/' + jrGallery_url + "/order_update/__ajax=1", {
                    gallery_order: o
                });
            });
        });
    </script>
{/if}
