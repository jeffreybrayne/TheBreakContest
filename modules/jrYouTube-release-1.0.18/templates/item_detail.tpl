{jrCore_module_url module="jrYouTube" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrYouTube" item=$item}

        </div>
        <h1>{$item.youtube_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrYouTube" id="40" default="YouTube"}</a> &raquo; {$item.youtube_title}
        </div>
    </div>

    <div class="block_content">

        <div class="item">

            <div class="container">
                <div class="row">
                    <div class="col12 last">
                        <div class="5">
                            {jrYouTube_embed type="iframe" item_id=$item._item_id auto_play=true width="100%"}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col2">
                        <div class="block_image" style="margin-top:12px;">
                            <img src="{$item.youtube_artwork_url}" class="iloutline img_scale"><br>
                            <div style="margin:6px 0 0 20px;">
                                {jrCore_module_function function="jrRating_form" type="star" module="jrYouTube" index="1" item_id=$item._item_id current=$item.youtube_rating_1_average_count|default:0 votes=$item.youtube_rating_1_count|default:0}
                            </div>
                        </div>
                    </div>
                    <div class="col10 last">
                        <div class="p5" style="margin:12px 0 0 6px;">
                            <span class="info">{jrCore_lang module="jrYouTube" id="14" default="Category"}:</span> <span class="info_c">{$item.youtube_category}</span><br>
                            <span class="info">{jrCore_lang module="jrYouTube" id="35" default="Duration"}:</span> <span class="info_c">{$item.youtube_duration}</span><br>
                            <span class="info">{jrCore_lang module="jrYouTube" id="17" default="Description"}:</span><br>
                            <div class="normal p5" style="max-width:555px;overflow:auto;height:75px">
                                {$item.youtube_description|jrCore_format_string:$item.profile_quota_id}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrYouTube" item=$item}

    </div>

</div>
