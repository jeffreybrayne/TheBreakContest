{* default index for profile *}
<!-- <div class="col8 last">
	<h1>Profile</h1>
    {jrCore_include module="jrAction" template="item_index.tpl"}
    {if $_conf.jbTheBreak_profile_comments == 'on'}
        <br>
        <div class="block">
            <div class="title">
                <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="Comments"}</h2>
            </div>
        </div>
        {jrComment_form module="jrProfile" profile_id=$_profile_id item_id=$_item_id}
    {/if}
</div> -->

<div class="col8 last">
	{jrCore_include module="jbArtistProfile" template="item_index.tpl"}
</div>
