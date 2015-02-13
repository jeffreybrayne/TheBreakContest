{assign var="selected" value="ban"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="140" default="Blogs" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
         });
</script>
{if isset($_post.option) && $_post.option == 'featured'}
    {assign var="order_by" value="blog_rating_1_average_count desc"}
{else}
    {assign var="order_by" value="blog_publish_date desc"}
{/if}

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1 mr5">

                {if isset($_conf.jrMediaPro_blog_profile) && $_conf.jrMediaPro_blog_profile > 0}
                    {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = `$_conf.jrMediaPro_blog_profile`" search2="blog_category = blog OR blog_category = Blog" template="blog_row.tpl" pagebreak="1" page=$_post.p}
                {else}
                    {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = 1" search2="blog_category = blog OR blog_category = Blog" template="blog_row.tpl" pagebreak="1" page=$_post.p}
                {/if}

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1">
                {jrCore_include template="side_home.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
