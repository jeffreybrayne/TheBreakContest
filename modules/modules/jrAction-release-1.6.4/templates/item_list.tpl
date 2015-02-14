{if isset($_items)}

    {if (jrCore_module_is_active('jrComment') && $_items[0].quota_jrComment_allowed == 'on') || (jrCore_module_is_active('jrDisqus') && $_items[0].quota_jrDisqus_allowed == 'on')}
        {assign var="img" value="comments.png"}
        {jrCore_lang module="jrAction" id="22" default="Comments" assign="alt"}
    {else}
        {assign var="img" value="link.png"}
        {jrCore_lang module="jrAction" id="23" default="Link To This" assign="alt"}
    {/if}

    {jrCore_module_url module="jrAction" assign="murl"}
    {foreach from=$_items item="item"}

    {* Shared Action *}
    {if isset($item.action_original_profile_url)}

        <div id="a{$item._item_id}" class="action_item_holder_shared">
            <div class="container">
                <div class="row">

                    <div class="col2">
                        <div class="action_item_media" title="{$item.action_original_profile_name|jrCore_entity_string}" onclick="jrCore_window_location('{$jamroom_url}/{$item.action_original_profile_url}/{$murl}/{$item.action_original_item_id}')">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item.action_original_user_id size="icon" crop="auto" alt=$item.user_name class="action_item_user_img img_shadow img_scale"}
                        </div>
                    </div>

                    <div class="col9">
                        <div class="action_item_desc">

                            <a href="{$jamroom_url}/{$item.action_original_profile_url}" class="action_item_title" title="{$item.action_original_profile_name|jrCore_entity_string}">@{$item.action_original_profile_url}</a> <span class="action_item_actions">&bull; {$item._created|jrCore_date_format:"relative"} &bull; {jrCore_lang module="jrAction" id="21" default="Shared By"} <a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">@{$item.profile_url}</a></span><br>

                            <div class="action_item_link" title="{$item.action_original_profile_name|jrCore_entity_string}" onclick="jrCore_window_location('{$jamroom_url}/{$item.action_original_profile_url}');">
                            {if isset($item.action_data) && strlen($item.action_data) > 0}
                                {$item.action_data}
                            {else}
                                <div class="p5">{$item.action_text|jrCore_format_string:$item.profile_quota_id|jrAction_convert_hash_tags}</div>
                            {/if}
                            </div>

                        </div>
                    </div>

                    <div class="col1 last">
                        <div id="d{$item._item_id}" class="action_item_delete">
                            <script>$(function () { $('#a{$item._item_id}').hover(function() { $('#d{$item._item_id}').toggle(); }); });</script>
                            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    {* Activity Updates *}
    {elseif isset($item.action_text)}

        <div id="a{$item._item_id}" class="action_item_holder">
            <div class="container">
                <div class="row">

                    <div class="col2">
                        <div class="action_item_media" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}')">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name class="action_item_user_img img_shadow img_scale"}
                        </div>
                    </div>
                    <div class="col9">

                        <div class="action_item_desc">

                            <a href="{$jamroom_url}/{$item.profile_url}" class="action_item_title" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a> <span class="action_item_actions">&bull; {$item._created|jrCore_date_format:"relative"}{if jrUser_is_logged_in() && $_user._user_id != $item._user_id && $item.action_shared_by_user != '1'} &bull; <a href="{$jamroom_url}/{$murl}/share/{$item._item_id}" onclick="if(!confirm('{jrCore_lang module="jrAction" id="9" default="Share this update with your followers?"}')) { return false; }">{jrCore_lang module="jrAction" id="10" default="Share This"}</a>{/if} {if $_post.module_url == $_user.profile_url && $item.action_shared_by_user == '1'} &bull; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{jrCore_lang module="jrAction" id="26" default="shared by you"}</a> {elseif $item.action_shared_by_count > 0} &bull; {jrCore_lang module="jrAction" id="24" default="shared by"} <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{$item.action_shared_by_count} {jrCore_lang module="jrAction" id="25" default="follower(s)"}</a>{/if}{if $img == "comments.png"} &bull; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{jrCore_lang module="jrAction" id="22" default="Comments"}: {$item.action_comment_count|default:0}</a>{/if}</span><br>

                            <div class="action_item_link">
                                <div class="p5">{$item.action_text|jrCore_format_string:$item.profile_quota_id|jrAction_convert_hash_tags}</div>
                            </div>

                        </div>
                    </div>
                    <div class="col1 last">
                        <div id="d{$item._item_id}" class="action_item_delete">
                            <script>$(function () { $('#a{$item._item_id}').hover(function() { $('#d{$item._item_id}').toggle(); }); });</script>
                            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>
                    </div>

                </div>
            </div>
         </div>

    {* Registered Module Action templates *}
    {elseif isset($item.action_data) && strpos($item.action_data, '{') !== 0}

        {jrCore_module_url module=$item.action_module assign="lurl"}

        <div id="a{$item._item_id}" class="action_item_holder">
            <div class="container">
                <div class="row">

                    <div class="col2">
                    {if isset($item.album_title_url)}
                        <div class="action_item_media" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$lurl}/albums/{$item.album_title_url}')">
                    {elseif isset($item.action_title_url)}
                        <div class="action_item_media" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$lurl}/{$item.action_item_id}/{$item.action_title_url}')">
                    {else}
                        <div class="action_item_media">
                    {/if}
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name class="action_item_user_img img_shadow img_scale"}
                        </div>
                    </div>
                    <div class="col9">

                        <div class="action_item_desc">

                            <a href="{$jamroom_url}/{$item.profile_url}" class="action_item_title" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a> <span class="action_item_actions">&bull; {$item._created|jrCore_date_format:"relative"}{if jrUser_is_logged_in() && $_user._user_id != $item._user_id && $item.action_shared_by_user != '1'} &bull; <a href="{$jamroom_url}/{$murl}/share/{$item._item_id}" onclick="if(!confirm('{jrCore_lang module="jrAction" id="9" default="Share this update with your followers?"}')) { return false; }">{jrCore_lang module="jrAction" id="10" default="Share This"}</a>{/if} {if $_post.module_url == $_user.profile_url && $item.action_shared_by_user == '1'} &bull; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{jrCore_lang module="jrAction" id="26" default="shared by you"}</a> {elseif $item.action_shared_by_count > 0} &bull; {jrCore_lang module="jrAction" id="24" default="shared by"} <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{$item.action_shared_by_count} {jrCore_lang module="jrAction" id="25" default="follower(s)"}</a>{/if}</span><br>

                            {if isset($item.album_title_url)}
                            <div class="action_item_link" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$lurl}/albums/{$item.album_title_url}')">
                            {elseif isset($item.action_title_url)}
                            <div class="action_item_link" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$lurl}/{$item.action_item_id}/{$item.action_title_url}')">
                            {else}
                            <div class="action_item_link">
                            {/if}
                                {$item.action_data}
                            </div>

                        </div>
                    </div>
                    <div class="col1 last">
                        <div id="d{$item._item_id}" class="action_item_delete">
                            <script>$(function () { $('#a{$item._item_id}').hover(function() { $('#d{$item._item_id}').toggle(); }); });</script>
                            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    {/if}

    {/foreach}

{/if}
