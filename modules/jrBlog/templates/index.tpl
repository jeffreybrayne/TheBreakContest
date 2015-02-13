{jrCore_lang module="jrBlog" id=29 default="Blogs" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="blog_title,blog_text"}
        <h1>{jrCore_lang module="jrBlog" id=29 default="Blogs"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrBlog" search1="blog_publish_date <= `$smarty.now`" order_by="blog_publish_date numerical_desc" pagebreak=10 page=$_post.p pager=true}
    </div>

</div>

{jrCore_include template="footer.tpl"}