{if isset($_conf.jrMediaPro_profile_ids) && $_conf.jrMediaPro_profile_ids > 0}
    {jrCore_list module="jrProfile" limit="10" search="_item_id in `$_conf.jrMediaPro_profile_ids`" template="index_featured.tpl" pagebreak="1" page=$_post.p}
{elseif isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_featured.tpl" require_image="profile_image" pagebreak="1" page=$_post.p}
{else}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaPro_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_featured.tpl" pagebreak="1" page=$_post.p}
{/if}
