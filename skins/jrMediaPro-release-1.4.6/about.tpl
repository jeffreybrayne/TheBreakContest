{assign var="selected" value="about"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About Us" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="container">
    <div class="row">

        <div class="col12 last">

            <div class="body_1 mr5">

                {* ROW TEMPLATE *}
                {capture name="row_template" assign="site_about_template"}
                    {literal}
                        {if isset($_items)}
                        {jrCore_module_url module="jrBlog" assign="murl"}
                        {foreach from=$_items item="item"}
                        <div style="padding:10px;">
                            <div class="title">
                                {if jrUser_is_master()}
                                <div class="float-right">
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="18"}</a>
                                </div>
                                {/if}
                                <h1>{$item.blog_title}</h1>
                                <div class="breadcrumbs">
                                    <a href="{$jamroom_url}/">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="Home"}</a> &raquo; {$item.blog_title}
                                </div>
                            </div>

                            <div class="blog-text">
                                {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                                {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width="128" height="128" crop="auto" class="iloutline img_shadow" style="float:left;margin-right:8px;margin_bottom:8px;"}
                                {/if}
                                {$item.blog_text|jrCore_format_string:$item.profile_quota_id}
                            </div>
                        </div>
                        {/foreach}
                        {else}
                        <div style="padding:10px;">
                            {if jrUser_is_master() || jrUser_is_admin()}
                            <div class="br-info" style="margin-bottom:20px;">
                                <h3>No About Page Setup</h3>
                            </div>
                            <div class="blog-text">
                                Create an admin about blog, be sure to set the category to about so it shows here.
                            </div>
                            {else}
                            <div class="br-info" style="margin-bottom:20px;">
                                <h3>About Us</h3>
                            </div>
                            <div class="blog-text">
                                Coming Soon!
                            </div>
                            {/if}
                        </div>
                        {/if}
                    {/literal}
                {/capture}

                {* EVENT LIST FUNCTION *}
                {jrCore_list module="jrBlog" order_by="_created desc" limit="1" profile_id=$_conf.jrMediaPro_blog_profile  search2="blog_category = about" template=$site_about_template}

            </div>

        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
