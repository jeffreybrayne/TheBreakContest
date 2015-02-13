{jrCore_module_url module="jrYouTube" assign="murl"}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="youtube_title,youtube_description"}
        <h1>{jrCore_lang module="jrYouTube" id=40 default="YouTube"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrYouTube" order_by="_created numerical_desc" pagebreak=10 page=$_post.p pager=true}
    </div>

</div>

{jrCore_include template="footer.tpl"}
