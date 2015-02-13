{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_active = 1" template="hot_artists_row.tpl" require_image="profile_image" pagebreak="8" page=$_post.p}
{else}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" search1="profile_active = 1" quota_id=$_conf.jrMediaPro_artist_quota template="hot_artists_row.tpl" pagebreak="8" page=$_post.p}
{/if}
