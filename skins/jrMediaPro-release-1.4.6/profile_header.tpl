{if jrProfile_is_profile_owner($_profile_id)}
    {assign var="from_profile" value="yes"}
{/if}
{assign var="page_title" value=$profile_name}
{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="profile_name_box">

                <div class="block_config" style="margin-top:3px">

                    {jrCore_module_function function="jrFollower_button" profile_id=$_profile_id title="Follow This Profile"}
                    {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}

                    {if jrUser_is_admin() || jrUser_is_power_user()}
                        {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title="Create new Profile"}
                    {/if}

                    {if jrUser_is_master()}
                        {jrCore_item_delete_button module="jrProfile" view="delete_save" profile_id=$_profile_id item_id=$_profile_id title="Delete this Profile" prompt="Are you sure you want to delete this profile?"}
                    {/if}

                </div>
                <a href="{$jamroom_url}/{$profile_url}"><h1 class="profile_name">{$profile_name}</h1></a>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col12 last">
            <div class="profile_menu">
                {if jrCore_is_mobile_device()}
                    {jrProfile_menu template="profile_menu_mobile.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {else}
                    {jrProfile_menu template="profile_menu.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {/if}
            </div>
        </div>
    </div>

    <div class="row">

        {* next <div> starts in body *}
