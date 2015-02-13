</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                {* Logo *}
                <div class="col6">
                    <div id="footer_logo">
                    </div>
                </div>

                {* Text *}
                <div class="col6 last">
                    <div id="footer_text">
                        &copy;{$smarty.now|date_format:"%Y"} <a href="{$jamroom_url}">{$_conf.jrCore_system_name}</a><br>
                        {* An auto footer that rotates phrases to help jamroom.net.  If you like jamroom, leave this here. We'd appreciate it.  Thanks. *}
                        {jrCore_powered_by}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</div>

{if isset($css_footer_href)}
    {foreach from=$css_footer_href item="_css"}
        <link rel="stylesheet" href="{$_css.source}" media="{$_css.media|default:"screen"}"/>
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
        $(function () {
            $('#menu-wrap').prepend('<div id="menu-trigger">{jrCore_lang skin=$_conf.jrCore_active_skin id="20" default="menu"}</div>');
            $("#menu-trigger").on("click", function () {
                $("#menu").slideToggle();

            });
            var isiPad = navigator.userAgent.match(/iPad/i) != null;
            if (isiPad) $('#menu ul').addClass('no-transition');
        });
    </script>

{/if}

</body>
</html>
