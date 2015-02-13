{jrCore_module_url module="jrGallery" assign="murl"}

{if isset($_items)}

    {if $_post.module == 'jrTags' || $_post.module == 'jrSearch' || isset($_post['ss'])}

        <div class="container">
            {foreach from=$_items item="item"}
            {if $item@iteration === 1 || ($item@iteration % 4) === 1}
                <div class="row">
            {/if}
            <div class="col3{if ($item@iteration % 4) === 0} last{/if}">
                <div class="p5 center">
                    <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.gallery_image_name title=$item.gallery_alt_text}</a><br>
                    <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">
                    {if isset($item.gallery_image_title)}
                        {$item.gallery_image_title|truncate:25:"...":false}
                    {else}
                        {$item.gallery_image_name|truncate:25:"...":true}
                    {/if}
                    </a><br><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a><br><a href="{$jamroom_url}/{$item.profile_url}" style="margin-bottom:10px">@{$item.profile_url}</a>
                </div>
            </div>
            {if ($item@iteration % 4) === 0 || $item@last}
                </div>
            {/if}
            {/foreach}
        </div>

    {else}

        {capture name="row_template" assign="template"}
        {literal}
        {jrCore_module_url module="jrGallery" assign="murl"}
        {foreach from=$_items item="item"}
            <a href="{jrGallery_get_gallery_image_url item=$item}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="small" crop="auto" class="iloutline" alt=$item.gallery_title}</a>
        {/foreach}
        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all"><span style="margin-left:6px;">{jrCore_icon icon="next"}</span></a>
        {/literal}
        {/capture}

        {foreach from=$_items item="item"}
            <div class="item">
                <div class="container">
                    <div class="row">
                        <div class="col10">
                            <div class="jr_gallery_row">
                                <div>
                                    <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a></h2>
                                    {if !empty($item.gallery_description)}
                                        <br>
                                        <span class="normal">{$item.gallery_description}</span>
                                    {/if}
                                </div>
                                <div class="mt10" style="padding-top:0">
                                    {assign var="limit" value="6"}
                                    {if jrCore_is_mobile_device()}
                                        {assign var="limit" value="5"}
                                    {/if}
                                    {jrCore_list module="jrGallery" profile_id=$item._profile_id search1="gallery_title_url = `$item.gallery_title_url`" template=$template order_by="gallery_order numerical_asc" exclude_jrUser_keys="true" exclude_jrProfile_quota_keys="true" limit=$limit}
                                </div>
                            </div>
                        </div>
                        <div class="col2 last">
                            <div class="block_config">

                                {jrCore_item_update_button module="jrGallery" profile_id=$item._profile_id item_id=$item._item_id}
                                {jrCore_item_delete_button module="jrGallery" profile_id=$item._profile_id action="`$murl`/delete_save/`$item.profile_url`/`$item.gallery_title_url`"}

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        {/foreach}

    {/if}

{/if}
