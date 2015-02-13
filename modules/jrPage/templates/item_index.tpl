{jrCore_module_url module="jrPage" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrPage" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrPage" id="19" default="Pages"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; {jrCore_lang module="jrPage" id="19" default="Pages"}
        </div>
    </div>

    <div class="block_content">

        {jrCore_list module="jrPage" profile_id=$_profile_id search="page_location = 1" order_by="page_display_order numerical_asc" pagebreak="12" page=$_post.p pager=true}

    </div>

</div>
