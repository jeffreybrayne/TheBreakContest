{jrCore_module_url module="jrAudio" assign="murl"}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="audio_title,audio_album,audio_genre"}
        <h1>{jrCore_lang module="jrAudio" id=41 default="Audio"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrAudio" order_by="_item_id numerical_desc" pagebreak=10 page=$_post.p pager=true}
    </div>

</div>

{jrCore_include template="footer.tpl"}
