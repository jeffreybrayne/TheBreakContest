{if isset($_conf.jbTheBreak_profile_ids) && $_conf.jbTheBreak_profile_ids > 0}
    {jrCore_list module="jrProfile" limit="10" search="_item_id in `$_conf.jbTheBreak_profile_ids`" quota_id=$quotaId template="index_featured.tpl" pagebreak="1" page=$_post.p}
{elseif isset($_conf.jbTheBreak_require_images) && $_conf.jbTheBreak_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$quotaId search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_featured.tpl" require_image="profile_image" pagebreak="1" page=$_post.p}
{else}
    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$quotaId search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_featured.tpl" pagebreak="1" page=$_post.p}
{/if}
