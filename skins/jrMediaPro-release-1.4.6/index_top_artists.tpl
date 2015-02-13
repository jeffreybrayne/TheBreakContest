{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_jrAudio_item_count > 0" template="index_top_artists_row.tpl" require_image="profile_image"}
{else}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_jrAudio_item_count > 0" template="index_top_artists_row.tpl"}
{/if}
