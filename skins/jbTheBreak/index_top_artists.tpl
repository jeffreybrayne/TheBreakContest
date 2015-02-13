<h1>Quota: {$_conf.contestId}</h1>
{if isset($_conf.jbTheBreak_require_images) && $_conf.jbTheBreak_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="25" search1="profile_jrAudio_item_count > 0" search2="profile_quota_id = '$_params.quotaId'" template="index_top_artists_row.tpl" require_image="profile_image"}
{else}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="25" search1="profile_jrAudio_item_count > 0" search2="profile_quota_id = '$_params.quotaId'" template="index_top_artists_row.tpl"}
{/if}
