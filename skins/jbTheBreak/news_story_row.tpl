{if isset($_items)}
    {jrCore_module_url module="jrBlog" assign="murl"}
    {foreach from=$_items item="item"}
    <div class="body_2">
        <div class="block_config">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
        </div>
        <h2 style="padding-left:10px;">{$item.blog_title}</h2>&nbsp;
        &raquo;&nbsp;<a onclick="jrLoad('#news_listing','{$jamroom_url}/news_list');">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
    </div>
    <div class="body_3" style="margin-right:10px;">

        <div class="block blogpost">

            <div class="blog_info">
                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xsmall" crop="auto" class="action_item_user_img iloutline" style="margin-right:12px"}
                <span class="normal">{$item.blog_publish_date|jrCore_format_time}</span><br>
                <span class="media_title">{jrCore_lang module="jrBlog" id="28" default="By"}:</span> <span class="capital">{$item.user_name}</span> <span class="media_title">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url}"><span class="capital">{$item.blog_category}</span></a><br>
                <span style="display:inline-block;margin-top:6px;">{jrCore_module_function function="jrRating_form" type="star" module="jrBlog" index="1" item_id=$item._item_id current=$item.blog_rating_1_average_count|default:0 votes=$item.blog_rating_1_count|default:0}</span>
            </div>

            <div class="normal p5">
                {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                    <div style="float:right">
                        {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width=false height=false class="iloutline img_shadow" style="margin-left:12px;margin-bottom:12px;"}
                    </div>
                {/if}
                {$item.blog_text|jrCore_format_string:$item.profile_quota_id|nl2br}
            </div>
            <hr>

        </div>
        {* bring in module features *}
        {jrCore_item_detail_features module="jrBlog" item=$item}

    </div>
    {/foreach}
{/if}
