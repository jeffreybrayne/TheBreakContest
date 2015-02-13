{assign var="selected" value="contact"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="81" default="Contact Us" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">

        <div class="col12 last">

            <div class="title">
                <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="81" default="Contact Us"}</h1>
            </div>

            <div class="body_1">

                {if jrUser_is_logged_in() && jrCore_module_is_active("jrPrivateNote")}
                    {jrCore_module_url module="jrPrivateNote" assign="notesurl"}
                    {jrCore_form_create_session module="jrPrivateNote" option="new" assign="token"}
                    <div class="page_notice_drop">
                        <div id="jrPrivateNote_new_msg" class="page_notice form_notice"></div>
                    </div>
                    <div>
                        <div class="page_note">{jrCore_lang skin=$_conf.jrCore_active_skin id="84" default="Please enter the message you would like to send"}{jrCore_lang skin=$_conf.jrCore_active_skin id="86" default=" and we will get back to you ASAP."}</div>

                        <form id="jrPrivateNote_new" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="{$jamroom_url}/{$notesurl}/new_save" name="jrPrivateNote_new">
                            <input id="jr_html_form_token" type="hidden" value="{$token}" name="jr_html_form_token">
                            <input type="hidden" name="note_to_id" value="1">
                            <div class="left capital bold" style="width:75%;margin:0 auto;">
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="82" default="subject"}:<br><br>
                                <input id="note_subject" class="form_text" type="text" value="" name="note_subject" style="width:100%;">
                            </div>
                            <br><br>
                            <div class="left capital bold" style="width:75%;margin:0 auto;">
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="83" default="message"}:<br><br>
                                <textarea id="note_text" class="form_textarea" name="note_text" style="width:100%;"></textarea>
                            </div>
                            <br><br>
                            <div class="center capital bold" style="width:75%;margin:0 auto;">
                                <input id="jrPrivateNote_new_submit" class="form_button" type="submit" value="{jrCore_lang skin=$_conf.jrCore_active_skin id="88" default="Send Message"}">&nbsp;
                                &nbsp;<input type="button" value="{jrCore_lang  skin=$_conf.jrCore_active_skin id="87" default="Cancel"}" onclick="window.location='{$jamroom_url}'" class="form_button">
                            </div>
                        </form>
                    </div>
                {/if}

            </div>

        </div>

    </div>

</div>

{jrCore_include template="footer.tpl"}
