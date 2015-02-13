{jrCore_module_url module="jrBlog" assign="murl"}

<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrBlog" item=$item}

        </div>
        <h1>{$item.blog_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrBlog" id="24" default="Blog"}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url}">{$item.blog_category}</a>
            {if isset($_post.p) && is_numeric($_post.p) && $_post.p > 1}
                &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}/p=1">{$item.blog_title}</a> &raquo; {jrCore_lang module="jrBlog" id=33 default="Page"} {$_post.p}
            {else}
                &raquo; {$item.blog_title}
            {/if}
        </div>
    </div>

    <div class="block_content">

        <div class="item blogpost">

            <div class="blog_info" style="padding-bottom:12px;border-bottom:1px solid #DDD;">
                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon96" class="action_item_user_img iloutline" style="margin-right:12px"}
                <span class="info">{$item.blog_publish_date|jrCore_format_time:false:"%F"}</span><br>
                <span class="info">{jrCore_lang module="jrBlog" id="28" default="By"}: {$item.user_name}</span><br>
                <span class="info">{jrCore_lang module="jrBlog" id="26" default="Posted in"}: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url}">{$item.blog_category}</a></span><br>
                <span style="display:inline-block;margin-top:6px;">{jrCore_module_function function="jrRating_form" type="star" module="jrBlog" index="1" item_id=$item._item_id current=$item.blog_rating_1_average_count|default:0 votes=$item.blog_rating_1_count|default:0}</span>
                <div style="clear:both"></div>
            </div>

            <div class="p10">
                {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                    <div class="float-right" style="margin-top:12px;">
                        {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width=false height=false class="iloutline img_shadow" style="margin-left:12px;margin-bottom:12px;"}
                    </div>
                {/if}

                {$item.blog_text|jrCore_format_string:$item.profile_quota_id}

            </div>
            <div class="clear"></div>

        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrBlog" item=$item}

    </div>

</div>
