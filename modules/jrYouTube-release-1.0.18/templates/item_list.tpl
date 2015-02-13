{if isset($_items)}
{jrCore_module_url module="jrYouTube" assign="murl"}
{foreach from=$_items item="item"}
    <div class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}"><img src="{$item.youtube_artwork_url}" alt="{$item.youtube_title|jrCore_entity_string}" class="iloutline img_scale"></a>
                    </div>
                </div>
                <div class="col6">
                    <div class="p5">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}">{$item.youtube_title}</a></h3><br>
                        <span class="info">{jrCore_lang module="jrYouTube" id="14" default="Category"}:</span> <span class="info_c">{$item.youtube_category}</span><br>
                        <span class="info">{jrCore_lang module="jrYouTube" id="35" default="Duration"}:</span> <span class="info_c">{$item.youtube_duration}</span>
                    </div>
                </div>
                <div class="col2">
                    <div class="p5">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrYouTube" index="1" item_id=$item._item_id current=$item.youtube_rating_1_average_count|default:0 votes=$item.youtube_rating_1_count|default:0}
                    </div>
                </div>
                <div class="col2 last">
                    <div class="block_config">
                        {jrCore_item_list_buttons module="jrYouTube" item=$item}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

    </div>
{/foreach}
{/if}
