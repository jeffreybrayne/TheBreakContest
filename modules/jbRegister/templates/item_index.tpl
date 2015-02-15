{jrCore_module_url module="jbRegister" assign="murl"}

<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jbRegister" profile_id=$_profile_id}
        </div>
        <!-- <h1>{jrCore_lang module="jbRegister" id="10" default="Register"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jbRegister" id="10" default="Register"}</a>
        </div> -->
        <h1>Register for Contest</h1>
        <p>Post:{$_post|@print_r}</p>
    </div>

<div class="block_content">
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

<!-- {jrCore_list module="jbRegister" profile_id=$_profile_id order_by="_created desc" pagebreak="8" page=$_post.p pager=true} -->

</div>

</div>
