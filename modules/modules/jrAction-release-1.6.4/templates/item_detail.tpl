{jrCore_module_url module="jrAction" assign="murl"}
<div class="block">

    <div class="title">
        <h1>{jrCore_lang module="jrAction" id="11" default="Activity Stream"}</h1>

        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_lang module="jrAction" id="11" default="Activity Stream"}</a>
        </div>
    </div>

    <div class="block_content">
        <div class="item" style="padding:12px 0 12px 0">
            <div class="container">
                <div class="row">

                {* Shared Action *}
                {if isset($item.action_original_profile_url)}

                    <div class="col2">
                        <div class="action_item_media">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item.action_original_user_id size="icon" crop="auto" alt="@`$item.action_original_profile_name`" class="action_item_user_img img_shadow img_scale"}
                        </div>
                    </div>

                    <div class="col9">
                        <div class="action_item_desc">
                            <a href="{$jamroom_url}/{$item.action_original_profile_url}" class="action_item_title" title="{$item.action_original_profile_name|jrCore_entity_string}">@{$item.action_original_profile_url}</a> <span class="action_item_actions">&bull; {$item._created|jrCore_date_format:"relative"} &bull; {jrCore_lang module="jrAction" id="21" default="Shared By"} <a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">@{$item.profile_url}</a></span>
                            {if isset($item.action_data) && strlen($item.action_data) > 0}
                                {$item.action_data}
                            {else}
                                <div class="p5">{$item.action_text|jrCore_format_string:$item.profile_quota_id|jrAction_convert_hash_tags}</div>
                            {/if}
                        </div>
                    </div>

                    <div class="col1 last">
                        <div class="action_item_delete" style="display:inherit">
                            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>
                    </div>


                {* Activity Updates *}
                {elseif isset($item.action_text)}

                    <div class="col2">
                        <div class="action_item_media">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name class="action_item_user_img img_shadow img_scale"}
                        </div>
                    </div>

                    <div class="col9">
                        <div class="action_item_desc">
                            <a href="{$jamroom_url}/{$item.profile_url}" class="action_item_title" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a> <span class="action_item_actions">&bull; {$item._created|jrCore_date_format:"relative"}</span>
                            <div class="p5">{$item.action_text|jrCore_format_string:$item.profile_quota_id|jrAction_convert_hash_tags}</div>
                        </div>
                    </div>

                    <div class="col1 last">
                        <div class="action_item_delete" style="display:inherit">
                            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>
                    </div>

                {* Registered Module Action templates *}
                {elseif isset($item.action_data)}

                    <div class="col2">
                        <div class="action_item_media">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name class="action_item_user_img img_shadow img_scale"}
                        </div>
                    </div>

                    <div class="col9">
                        <div class="action_item_desc">
                            <a href="{$jamroom_url}/{$item.profile_url}" class="action_item_title" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a> <span class="action_item_actions">&bull; {$item._created|jrCore_date_format:"relative"}</span>
                            {$item.action_data}
                        </div>
                    </div>

                    <div class="col1 last">
                        <div class="action_item_delete" style="display:inherit">
                            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>
                    </div>

                {/if}
                </div>

                {if isset($item.action_shared_by_user_info)}
                <div class="row">
                    <div class="col12 last">
                        <br>{jrCore_lang module="jrAction" id=21 default="Shared By"}:<br>
                        <div style="margin-top:9px;border-top:1px solid #eee;padding:9px 0 9px 0">
                        {foreach $item.action_shared_by_user_info as $usr}
                            <div style="float:left"><a href="{$jamroom_url}/{$usr.profile_url}" title="{$usr.user_name|jrCore_entity_string}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$usr._user_id size="xsmall" crop="auto" alt=$usr.user_name class="action_item_user_img img_shadow"}</a></div>
                        {/foreach}
                        </div>
                    </div>
                </div>
                {/if}

            </div>
        </div>

        {* bring in module features - only for action udpates*}
        {if $item.action_module == 'jrAction'}
            {jrCore_item_detail_features module="jrAction" item=$item}
        {else}
            {jrCore_module_url module=$item.action_module assign="iurl"}
            <div class="item action_item_title">
                View this item:<br>
                <h2><a href="{$jamroom_url}/{$item.profile_url}/{$iurl}/{$item.action_item_id}/{$item.action_title_url}" title="{$item.action_title|jrCore_entity_string}">{$item.action_title}</a></h2>
            </div>
        {/if}

    </div>
</div>

