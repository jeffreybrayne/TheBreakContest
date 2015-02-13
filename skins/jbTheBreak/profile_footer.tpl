<div class="col4 last">
    <div>
        <div class="block mb20" style="border:1px solid #282828;">
            <div class="block_content">
                <div class="body_1">
                    <div class="profile_image">
                        {if jrProfile_is_profile_owner($_profile_id)}
                            {jrCore_module_url module="jrProfile" assign="purl"}
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="25" default="Change Image" assign="hover"}
                            <a href="{$_conf.jrCore_base_url}/{$purl}/settings/profile_id={$_profile_id}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xlarge" class="img_scale img_shadow" alt=$profile_name title=$hover width=false height=false}</a>
                            <div class="profile_hoverimage">
                                <span class="normal" style="font-weight:bold;color:#FFF;">{$hover}</span>&nbsp;{jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}
                            </div>
                        {else}
                            {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xxlarge" class="img_scale img_shadow" alt=$profile_name width=false height=false}
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <div class="block">
            <div class="block_content mt10">
                <div style="padding-top:8px;min-height:48px;max-height:288px;overflow:auto;">
                    {jrUser_online_status profile_id=$_profile_id}
                </div>
            </div>
        </div>


        {if isset($profile_bio) && strlen($profile_bio) > 0}
            <div class="head_2" style="margin-top:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"} {$profile_name}</div>
            <div class="block mb20" style="border:1px solid #282828;">
                <div class="block_content">
                    <div class="item" style="max-height:350px;overflow:auto;">
                        {$profile_bio|jrCore_format_string:$profile_quota_id}
                    </div>
                </div>
            </div>
        {else}
            <div class="head_2" style="margin-top:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"} {$profile_name}</div>
            <div class="block mb20" style="border:1px solid #282828;">
                <div class="block_content">
                    <div class="item" style="max-height:350px;overflow:auto;">
                        <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="198" default="Location"}:</h4> {$profile_location} &nbsp; {$profile_country} &nbsp; {$profile_zip}
                        <br>
                        {jrNingImport_signup_questions user_id=$_user_id}
                    </div>
                </div>
            </div>
        {/if}

        {if isset($profile_influences) && strlen($profile_influences) > 0}
            <div class="head_2"> {jrCore_lang skin=$_conf.jrCore_active_skin id="143" default="Influences"}:</div>
            <div class="block mb20" style="border:1px solid #282828;">
                <div class="block_content">
                    <div class="item">
                        <span class="hl-4">{$profile_influences}</span><br>
                    </div>
                </div>
            </div>
        {/if}

        {if jrCore_module_is_active('jrFollower')}
        {capture name="row_template" assign="follower_row"}
            {literal}
                {if isset($_items)}
                {foreach from=$_items item="item"}
                <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" crop="auto" class="img_shadow" style="padding:2px;margin-bottom:4px;" alt="{$item.user_name}" title="{$item.user_name}" width=false height=false}</a>
                {/foreach}
                {/if}
            {/literal}
        {/capture}
        {jrCore_list module="jrFollower" limit="12" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_created desc" template=$follower_row assign="followers"}
        {if isset($followers) && strlen($followers) > 0}
        <div class="head_2"> {jrCore_lang skin=$_conf.jrCore_active_skin id="131" default="Followers"}:</div>
        <div class="block mb20" style="border:1px solid #282828;">
            <div class="block_content">
                <div class="item center">
                    {$followers}
                </div>
            </div>
        </div>
        {/if}
        {/if}

        {jrCore_list module="jrRating" profile_id=$_profile_id search1="rating_image_size > 0" order_by="_updated desc" limit="14" assign="rated"}
        {if strlen($rated) > 0}
            <div class="head_2">{jrCore_lang skin=$_conf.jrCore_active_skin id="157" default="Recently Rated"}:</div>
            <div class="block mb20" style="border:1px solid #282828;">
                <div class="block_content">
                    <div class="item center">
                        {$rated}
                    </div>
                </div>
            </div>
        {/if}
        <div class="head_2">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
        <div class="block mb20" style="border:1px solid #282828;">
            <div class="block_content">
                <div class="item">

                    {capture name="template" assign="stats_tpl"}
                        {literal}
                            {foreach $_stats as $title => $_stat}
                            {jrCore_module_url module=$_stat.module assign="murl"}
                            <div class="stat_entry_box" onclick="window.location='{$jamroom_url}/{$profile_url}/{$murl}'">
                                {$title}: {$_stat.count|default:0}
                            </div>
                            {/foreach}
                        {/literal}
                    {/capture}
                    {jrProfile_stats profile_id=$_profile_id template=$stats_tpl}

                    <div class="clear"></div>
                </div>
            </div>
        </div>

        {jrTags_cloud profile_id=$_profile_id height="350" assign="tag_cloud"}
        {if strlen($tag_cloud) > 0}
            <div class="head_2">{jrCore_lang module="jrTags" id="1" default="Profile Tag Cloud"}:</div>
            <div class="block mb20" style="border:1px solid #282828;">
                <div class="block_content">
                    <div class="item">
                        {$tag_cloud}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        {/if}

    </div>
</div>


</div>
</div>

{jrCore_include template="footer.tpl"}

{* setup counter for page view *}
{jrCore_counter module="jrProfile" item_id=$_profile_id name="profile_view"}