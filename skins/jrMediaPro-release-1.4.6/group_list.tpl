{if isset($_post.option) && $_post.option == 'alpha'}
    {assign var="order_by" value="group_title asc"}
    {assign var="jrload_div" value="alpha_groups"}
    {assign var="gpb" value=$_conf.jrMediaPro_default_group_pagebreak}
{elseif isset($_post.option) && $_post.option == 'newest'}
    {assign var="order_by" value="_created desc"}
    {assign var="jrload_div" value="new_groups"}
    {assign var="gpb" value=$_conf.jrMediaPro_default_group_pagebreak}
{else}
    {assign var="order_by" value="group_title asc"}
    {assign var="jrload_div" value="top_groups"}
    {assign var="gpb" value="16"}
{/if}


{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {if isset($_post.option) && $_post.option != 'top'}
        {jrCore_list module="jrGroup" order_by=$order_by require_image="group_image" pagebreak=$gpb page=$_post.p}
    {else}
        {jrCore_list module="jrGroup" order_by="group_comment_count numerical_desc" require_image="group_image" pagebreak=$gpb page=$_post.p}
    {/if}
{else}
    {if isset($_post.option) && $_post.option != 'top'}
        {jrCore_list module="jrGroup" order_by=$order_by pagebreak=$gpb page=$_post.p}
    {else}
        {jrCore_list module="jrGroup" order_by="group_comment_count numerical_desc" pagebreak=$gpb page=$_post.p}
    {/if}
{/if}
