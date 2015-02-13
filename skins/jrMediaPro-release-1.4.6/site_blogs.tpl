{assign var="selected" value="ban"}
{assign var="spt" value="ban"}
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

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div id="nav-180">
                                <ul>
                                {if !isset($_post.option)}
                                    <li id="nav-180-current">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</li>
                                 {else}
                                    <li><a onfocus="blur();" href="{$jamroom_url}/site_blogs">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a></li>
                                {/if}
                                {if isset($_post.option) && $_post.option == 'featured'}
                                    <li id="nav-180-current">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}</li>
                                    {else}
                                    <li><a onfocus="blur();" href="{$jamroom_url}/site_blogs/featured">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}</a></li>
                                {/if}
                                </ul>
                            </div>
                            <div class="clear"></div>
                            <div class="body_2">
                                {* SITE BLOG LIST FUNCTION *}
                                {jrCore_list module="jrBlog" order_by=$order_by template="site_blogs_list.tpl" search1="blog_category not_in about,news,welcome,latest,featured,exclusive" pagebreak=$_conf.jrMediaPro_default_pagebreak page=$_post.p}
                            </div>
                        </div>
                    </div>
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
