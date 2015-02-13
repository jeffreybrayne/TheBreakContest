{if isset($order) && $order == 'alpha'}
    {assign var="order_by" value="profile_name asc"}
{elseif isset($order) && $order == 'newest'}
    {assign var="order_by" value="_created desc"}
{elseif isset($order) && $order == 'most_viewed'}
    {assign var="order_by" value="profile_view_count numerical_desc"}
{else}
    {assign var="order_by" value="profile_name asc"}
{/if}


{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" quota_id=$qid require_image="profile_image" template="account_row.tpl" pagebreak="12" page=$_post.p order=$order qid=$qid}
{else}
    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" quota_id=$qid template="account_row.tpl" pagebreak="12" page=$_post.p order=$order qid=$qid}
{/if}
