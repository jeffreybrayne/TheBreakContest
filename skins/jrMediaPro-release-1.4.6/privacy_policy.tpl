{if jrCore_module_is_active('jrPage')}
    {jrCore_list module="jrPage" order_by="_created desc" limit="1" search1="page_title_url like %privacy%" search2="page_location = 0" template="tos_pp_row.tpl" assign="UPP"}
{/if}

{assign var="selected" value="privacy"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="80" default="Privacy Policy" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="container">

<div class="row">

    <div class="col12 last">

    {if isset($UPP) && strlen($UPP) > 0}

        {$UPP}

    {else}

        <div class="title">
            {if jrUser_is_admin()}
                <div class="block_config">
                    <a onclick="window.location='{$jamroom_url}/page/admin/tools'" title="update" href="{$jamroom_url}/page/admin/tools">{jrCore_icon icon="gear"}</a>
                </div>
            {/if}
            <h1>{$_conf.jrCore_system_name} {jrCore_lang skin=$_conf.jrCore_active_skin id="80" default="Privacy Policy"}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="Home"}</a> &raquo; {jrCore_lang skin=$_conf.jrCore_active_skin id="80" default="Privacy Policy"}
            </div>
        </div>

        <div class="body_1">

            {if jrUser_is_admin()}
                <span style="font-size: 10px; padding-left: 10px;">(Only site Admins will see this note.)</span>
                <div class="page_notice error" style="text-align: left;">
                    To change the text on this page, either modify the privacy_policy.tpl template found in your active skin directory, or click the gear button to the right and create a new Privacy Policy page.<br>
                    <br>
                    <div style="text-align: center;">
                        <span class="bold">Note:</span>&nbsp;Make sure to name the new page <span class="bold">Privacy Policy</span>.
                    </div>
                </div>
                <br><br>
            {/if}

            <div class="p20">

                <h3>Information Gathering</h3>
                When you create a User Account on {$system_name}, information including your email address, and physical IP Address are recorded and stored. This information is used internally by {$system_name} and is never distributed to any third parties.
                <br><br>

                <h3>Web Site Cookies</h3>
                When interacting with the {$system_name} website, your Internet Browser and the {$system_name} server communicate via a "session cookie" mechanism. This cookie contains an anonymous identifier that is used by the {$system_name} website.
                <br><br>

                <h3>Information Disclosure</h3>
                At no time is any account information shared with any outside parties, with the exception of law enforcement should the need arise, and only then to comply with a subpoena.
                <br><br>

                <h3>Contact Us</h3>
                If you have any questions or concerns about the {$_conf.jrCore_system_name} {jrCore_lang skin=$_conf.jrCore_active_skin id="80" default="Privacy Policy"}, please {if jrCore_module_is_active('jrCustomForm')}<a href="{$jamroom_url}/form/contact_us">{else}<a href="{$jamroom_url}/contact_us">{/if}{jrCore_lang skin=$_conf.jrCore_active_skin id="81" default="Contact Us"}</a>
                <br><br>



            </div>


        </div>

    {/if}

    </div>

</div>

</div>

{jrCore_include template="footer.tpl"}
