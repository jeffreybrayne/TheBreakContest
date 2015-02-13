{jrCore_module_url module="jbArtistProfile" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jbArtistProfile" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jbArtistProfile" id="10" default="ArtistProfile"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jbArtistProfile" id="10" default="ArtistProfile"}</a>
        </div>
    </div>

<div class="block_content">

	{jrCore_list module="jbArtistProfile" profile_id=$_profile_id order_by="_created desc" pagebreak="8" page=$_post.p pager=true}
	
	<div class="block">
	
	    <div class="title">
	        {jrSearch_module_form fields="audio_title,audio_album,audio_genre"}
	        <h2>{jrCore_lang module="jrAudio" id=41 default="Audio"}</h2>
	    </div>
	
	    <div class="block_content">
	        {jrCore_list module="jrAudio" order_by="_item_id numerical_desc" profile_id=$_profile_id pagebreak=10 page=$_post.p pager=true}
	    </div>
	
	</div>
	
	<div class="block">
	
	    <div class="title">
	        {jrSearch_module_form fields="youtube_title,youtube_description"}
	        <h2>{jrCore_lang module="jrYouTube" id=40 default="YouTube"}</h2>
	    </div>
	
	    <div class="block_content">
	        {jrCore_list module="jrYouTube" order_by="_created numerical_desc" profile_id=$_profile_id pagebreak=10 page=$_post.p pager=true}
	    </div>
	
	</div>
	
	<!-- <div class="block">
		<div class="title">
	        {jrSearch_module_form fields="timeline_title,timeline_description"}
	        <h2>{jrCore_lang module="jrAction" id=40 default="Timeline"}</h2>
	    </div>
		<div class="block_content">
			{jrCore_include module="jrAction" template="item_index.tpl"}
		</div>
	</div> -->


</div>

</div>
