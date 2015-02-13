{if isset($_post.module) && $_post.option != 'admin' && ($_post.module == 'jrRecommend' || $_post.module == 'jrSearch')}
                        </div>
                    </div>
                </div>
    <div class="col3 last">
        <div class="body_1">
            {jrCore_include template="side_home.tpl"}
        </div>
    </div>
            </div>
        </div>
{/if}
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                {* Logo *}
                <div class="col2">
                    <div id="footer_logo">
                        {jrCore_image image="GameLoudLogo.png" width="150" height="38" alt="TheBreak Skin &copy; {$smarty.now|date_format:"%Y"} The Jamroom Network" title="TheBreak Skin &copy; {$smarty.now|date_format:"%Y"} The Jamroom Network"}
                    </div>
                </div>

                {* Links *}
                <div class="col7">
                    <div class="footer pt10">
                        <a href="{$jamroom_url}/terms_of_service">{jrCore_lang skin=$_conf.jrCore_active_skin id="79" default="Terms Of Service"}</a>&nbsp;|
                        <a href="{$jamroom_url}/privacy_policy">{jrCore_lang skin=$_conf.jrCore_active_skin id="80" default="Privacy Policy"}</a>&nbsp;|
                        <a href="{$jamroom_url}/about">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About Us"}</a>&nbsp;|
                    {if jrCore_module_is_active('jrCustomForm')}
                        |&nbsp;<a href="{$jamroom_url}/form/contact_us">{jrCore_lang skin=$_conf.jrCore_active_skin id="81" default="Contact Us"}</a>
                    {else}
                        {capture name="footer_contact" assign="footer_contact_row"}
                        {literal}
                            {if isset($_items)}
                                {foreach from=$_items item="item"}
                                    |&nbsp;<a href="mailto:{$item.user_email}?subject={$_conf.jrCore_system_name} Contact">{jrCore_lang skin=$_conf.jrCore_active_skin id="81" default="Contact Us"}</a>
                                {/foreach}
                            {/if}
                        {/literal}
                        {/capture}
                        {jrCore_list module="jrUser" limit="1" profile_id="1" template=$footer_contact_row}
                    {/if}
                    </div>
                </div>

                <div class="col3 last">
                    <div id="footer_sn">

                        {* Social Network Linkup *}
                        {if strlen($_conf.jbTheBreak_twitter_name) > 0}
                            <a href="https://twitter.com/{$_conf.jbTheBreak_twitter_name}" target="_blank">{jrCore_image image="sn-twitter.png" width="24" height="24" class="social-img" alt="twitter" title="Follow @{$_conf.jbTheBreak_twitter_name}"}</a>
                        {/if}

                        {if strlen($_conf.jbTheBreak_facebook_name) > 0}
                            <a href="https://facebook.com/{$_conf.jbTheBreak_facebook_name}" target="_blank">{jrCore_image image="sn-facebook.png" width="24" height="24" class="social-img" alt="facebook" title="Like {$_conf.jbTheBreak_facebook_name} on Facebook"}</a>
                        {/if}

                        {if strlen($_conf.jbTheBreak_linkedin_name) > 0}
                            <a href="https://linkedin.com/{$_conf.jbTheBreak_linkedin_name}" target="_blank">{jrCore_image image="sn-linkedin.png" width="24" height="24" class="social-img" alt="linkedin" title="Link up with {$_conf.jbTheBreak_linkedin_name} on LinkedIn"}</a>
                        {/if}

                        {if strlen($_conf.jbTheBreak_google_name) > 0}
                            <a href="https://google.com/{$_conf.jbTheBreak_google_name}" target="_blank">{jrCore_image image="sn-google-plus.png" width="24" height="24" class="social-img" alt="google+" title="Follow {$_conf.jbTheBreak_google_name} on Google+"}</a>
                        {/if}

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<a href="#" class="scrollup">Scroll</a>

<div id="footer-bar">
    <div class="container">
        <div class="row">
            <div class="col6">
                <div class="footer-copy">
                    <span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="99" default="Copyright"} &copy;{$smarty.now|date_format:"%Y"}</span> <a href="{$jamroom_url}">{$_conf.jrCore_system_name}</a>, <span class="hl-2 capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="100" default="all rights reserved"}.</span>
                </div>
            </div>
            <div class="col6 last">
                <div class="footer-design">
                    {* An auto footer that rotates phrases to help jamroom.net.  If you like jamroom, leave this here. We'd appreciate it.  Thanks. *}
                    {jrCore_powered_by}
                </div>
            </div>
        </div>
    </div>
</div>

</div>

{if isset($css_footer_href)}
    {foreach from=$css_footer_href item="_css"}
        <link rel="stylesheet" href="{$_css.source}" media="{$_css.media|default:"screen"}" />
    {/foreach}
{/if}

{if isset($javascript_footer_href)}
    {foreach from=$javascript_footer_href item="_js"}
    <script type="{$_js.type|default:"text/javascript"}" src="{$_js.source}"></script>
    {/foreach}
{/if}

{if isset($javascript_footer_function)}
<script type="text/javascript">
    {$javascript_footer_function}
</script>
{/if}

{* do not remove this hidden div *}
<div id="jr_temp_work_div" style="display:none"></div>

{if jrCore_is_mobile_device()}

    {* Slidebars *}
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                var ms = new $.slidebars();
                $('#mmt').on('click', function() {
                    ms.slidebars.open('left');
                });
            });
        }) (jQuery);
    </script>

    </div>

{else}

{* Responsive Menu *}
<script type="text/javascript">

    $(function() {
        /* Mobile */
        $('#menu-wrap').prepend('<div id="menu-trigger">{jrCore_lang skin=$_conf.jrCore_active_skin id="20" default="menu"}</div>');
        $("#menu-trigger").on("click", function(){
            $("#menu").slideToggle();
         });

        // iPad
        var isiPad = navigator.userAgent.match(/iPad/i) != null;
        if (isiPad) $('#menu ul').addClass('no-transition');
     });
</script>

{/if}

</body>
</html>
