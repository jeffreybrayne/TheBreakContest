{if isset($_post.option) && $_post.option == 'alpha'}
    {assign var="order_by" value="discuss_title asc"}
    {assign var="jrload_div" value="alpha_discussions"}
    {assign var="gpb" value=$_conf.jrMediaPro_default_discussion_pagebreak}
{elseif isset($_post.option) && $_post.option == 'newest'}
    {assign var="order_by" value="_created desc"}
    {assign var="jrload_div" value="new_discussions"}
    {assign var="gpb" value=$_conf.jrMediaPro_default_discussion_pagebreak}
{else}
    {assign var="order_by" value="discuss_title asc"}
    {assign var="jrload_div" value="top_discussions"}
    {assign var="gpb" value="16"}
{/if}

{if isset($_post.option) && $_post.option != 'top'}
    {jrCore_list module="jrGroupDiscuss" order_by=$order_by template="discussion_row.tpl" pagebreak=$gpb page=$_post.p}
{else}
    {jrCore_list module="jrGroupDiscuss" order_by="discuss_comment_count numerical_desc" template="discussion_row.tpl" pagebreak=$gpb page=$_post.p}
{/if}
