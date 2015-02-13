{if isset($_post.option) && $_post.option == 'alpha'}
    {assign var="order_by" value="gallery_title asc"}
    {assign var="jrload_div" value="alpha_galleries"}
    {assign var="gpb" value=$_conf.jrMediaPro_default_gallery_pagebreak}
{elseif isset($_post.option) && $_post.option == 'newest'}
    {assign var="order_by" value="_created desc"}
    {assign var="jrload_div" value="new_galleries"}
    {assign var="gpb" value=$_conf.jrMediaPro_default_gallery_pagebreak}
{else}
    {assign var="order_by" value="gallery_title asc"}
    {assign var="jrload_div" value="top_galleries"}
    {assign var="gpb" value="16"}
{/if}


{if isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
    {if isset($_post.option) && $_post.option != 'top'}
        {jrCore_list module="jrGallery" order_by=$order_by group_by="_profile_id, gallery_title_url" template="galleries_row.tpl" require_image="gallery_image" pagebreak=$gpb page=$_post.p}
    {else}
        {jrCore_list module="jrGallery" order_by="gallery_rating_1_average_count numerical_asc" group_by="_profile_id, gallery_title_url" template="galleries_row.tpl" require_image="gallery_image" pagebreak=$gpb page=$_post.p}
    {/if}
{else}
    {if isset($_post.option) && $_post.option != 'top'}
        {jrCore_list module="jrGallery" order_by=$order_by group_by="_profile_id, gallery_title_url" template="galleries_row.tpl" pagebreak=$gpb page=$_post.p}
    {else}
        {jrCore_list module="jrGallery" order_by="gallery_rating_1_average_count numerical_asc" group_by="_profile_id, gallery_title_url" template="galleries_row.tpl" pagebreak=$gpb page=$_post.p}
    {/if}
{/if}
