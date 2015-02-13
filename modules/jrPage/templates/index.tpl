{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="page_title,page_body"}
        <h1>{jrCore_lang module="jrPage" id="19" default="Pages"}</h1>
    </div>

    <div class="block_content">

        {jrCore_list module="jrPage" search="page_location = 0" order_by="_created desc" pagebreak="12" page=$_post.p pager=true}

    </div>

</div>

{jrCore_include template="footer.tpl"}
