{if isset($_post.option) && $_post.option == 'alpha'}
    {assign var="order_by" value="profile_name asc"}
{elseif isset($_post.option) && $_post.option == 'newest'}
    {assign var="order_by" value="_created desc"}
{elseif isset($_post.option) && $_post.option == 'most_viewed'}
    {assign var="order_by" value="profile_view_count numerical_desc"}
{else}
    {assign var="order_by" value="profile_name asc"}
{/if}


{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" quota_id=$_conf.jrMediaPro_member_quota require_image="profile_image" template="members_row.tpl" pagebreak="12" page=$_post.p}
{else}
    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" quota_id=$_conf.jrMediaPro_member_quota template="members_row.tpl" pagebreak="12" page=$_post.p}
{/if}
