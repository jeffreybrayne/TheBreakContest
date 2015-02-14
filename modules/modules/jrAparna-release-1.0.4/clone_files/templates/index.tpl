{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        <h1>{jrCore_lang module="jrAparna" id="10" default="Aparna"}</h1>
    </div>

    <div class="block_content">

        {jrCore_list module="jrAparna" order_by="_item_id numerical_desc" pagebreak="10" page=$_post.p pager=true}

    </div>

</div>

{jrCore_include template="footer.tpl"}
