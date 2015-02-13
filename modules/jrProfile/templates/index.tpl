{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="profile_name,profile_url"}
        <h1>{jrCore_lang module="jrProfile" id="26" default="Profiles"}</h1>
    </div>

    <div class="block_content">

        {jrCore_list module="jrProfile" order_by="_item_id desc" search1="profile_active = 1" pagebreak="10" page=$_post.p pager=true}

    </div>

</div>

{jrCore_include template="footer.tpl"}
