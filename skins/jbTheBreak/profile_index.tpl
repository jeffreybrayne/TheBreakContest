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
{$_user|@print_r}

<h1>User info: {$_user._user_id} {$_user.profile_name}</h1>
<div class="block">
	<div class="col8 last">
		<div class="row">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="YJBKGSE7AWZNW">
			<input type="hidden" name="profile_id" value="{$_user._user_id}">
			<input type="hidden" name="profile_name" value="{$_user.profile_name}">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>
</div>

<div class="col8 last">
	{jrCore_include module="jbArtistProfile" template="item_index.tpl"}
</div>
