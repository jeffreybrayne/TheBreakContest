{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form module="jrGallery" fields="gallery_title,gallery_image_title,gallery_caption,gallery_description,gallery_image_name"}
        {if isset($_post.ss)}
            <h1>{jrCore_lang module="jrGallery" id=38 default="Images"}</h1>
        {else}
            <h1>{jrCore_lang module="jrGallery" id=24 default="Image Galleries"}</h1>
        {/if}
    </div>

    <div class="block_content">
        <div class="item">
        {if isset($_post.ss)}
            {jrCore_list module="jrGallery" profile_id=$_profile_id order_by="_updated desc" require_image="gallery_image" pagebreak=12 page=$_post.p pager=true}
        {else}
            {jrCore_list module="jrGallery" profile_id=$_profile_id order_by="_updated desc" group_by="gallery_title_url" require_image="gallery_image" pagebreak=6 page=$_post.p pager=true}
        {/if}
        </div>
    </div>

</div>

{jrCore_include template="footer.tpl"}
