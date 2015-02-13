{assign var="selected" value="ban"}
{assign var="spt" value="ban"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="140" default="Blogs" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#siteblog',core_system_url + '/blogs_list');
         });
</script>
{if isset($_post.option) && $_post.option == 'featured'}
    {assign var="order_by" value="blog_rating_1_average_count desc"}
{else}
    {assign var="order_by" value="blog_publish_date desc"}
{/if}

<a id="sblogs" name="sblogs"></a>
<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1 mr5">

                <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="8" default="Site"} {jrCore_lang skin=$_conf.jrCore_active_skin id="10" default="Blog"}</h1><br>
                <br>

                <div id="siteblog">

                </div>

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
