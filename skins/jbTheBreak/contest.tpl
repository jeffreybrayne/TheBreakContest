{assign var='quotaId' value=$option|default:'1'}
{assign var="selected" value="home"}
{assign var="spt" value="home"}
{assign var="no_inner_div" value="true"}
{jrCore_include template="header.tpl"}
{jrCore_include template="contest_header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        // jrSetActive('#default');
        // jrLoad('#top_singles',core_system_url + '/index_top_singles');
        // jrLoad('#newest_artists',core_system_url + '/index_new_artists');
        // jrLoad('#top_artists',core_system_url + '/index_top_artists');
         });
</script>

{* FLEX-SLIDER *}
<div class="container">
    <div class="row">
        <div class="col12 last">

            <div class="slider_container">
                <a onfocus="blur();" href="javascript:void(0);" id="fadeout-carousel"><div class="button-toggle"></div></a>
                <div class="toggle-carousel">

                    <section class="slider">
                        <div id="slider" class="flexslider">
                            <ul class="slides">
                                {if isset($_conf.jrMediaPro_slider_profile_ids) && $_conf.jrMediaPro_slider_profile_ids > 0}
                                    {jrCore_list module="jrProfile" search="_item_id in `$_conf.jrMediaPro_slider_profile_ids`" quota_id={$quotaId} template="index_slider.tpl" limit="21"}
                                {elseif isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
                                    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" quota_id={$quotaId} search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider.tpl" limit="21" require_image="profile_image"}
                                {else}
                                    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" quota_id={$quotaId} search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider.tpl" limit="21"}
                                {/if}
                            </ul>
                        </div>
                        <div id="carousel" class="flexslider">
                            <ul class="slides">
                                {if isset($_conf.jrMediaPro_slider_profile_ids) && $_conf.jrMediaPro_slider_profile_ids > 0}
                                    {jrCore_list module="jrProfile" search="_item_id in `$_conf.jrMediaPro_slider_profile_ids`" quota_id={$quotaId} template="index_slider_thumbs.tpl" limit="21"}
                                {elseif isset($_conf.jrMediaPro_require_images) && $_conf.jrMediaPro_require_images == 'on'}
                                    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc"  quota_id={$quotaId} search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider_thumbs.tpl" limit="21" require_image="profile_image"}
                                {else}
                                    {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc"  quota_id={$quotaId} search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider_thumbs.tpl" limit="21"}
                                {/if}
                            </ul>
                        </div>
                    </section>

                </div>
            </div>

        </div>
    </div>
</div>

<div id="content">

<div class="container">

<div class="row">

{* BEGIN LEFT SIDE *}
<div class="col9">
<div class="body_1 mr5">
    <div class="container">     

        {* TOP 25 ARTISTS *}
        {jrCore_include template="contest_top25.tpl" quota_id=${$quotaId}}

        {* BOTTOM AD *}
        <div class="row">
            <div class="col12 last">

                <div class="center">
                    {if $_conf.jrMediaPro_ads_off != 'on'}
                        {if isset($_conf.jrMediaPro_google_ads) && $_conf.jrMediaPro_google_ads == 'on'}
                            <script type="text/javascript"><!--
                                google_ad_client = "{$_conf.jrMediaPro_google_id}";
                                google_ad_width = 728;
                                google_ad_height = 90;
                                google_ad_format = "728x90_as";
                                google_ad_type = "text_image";
                                google_ad_channel ="";
                                google_color_border = "CCCCCC";
                                google_color_bg = "CCCCCC";
                                google_color_link = "FF9900";
                                google_color_text = "333333";
                                google_color_url = "333333";
                                //--></script>
                            <script type="text/javascript"
                                    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                            </script>
                        {elseif isset($_conf.jrMediaPro_bottom_ad) && strlen($_conf.jrMediaPro_bottom_ad) > 0}
                            {$_conf.jrMediaPro_bottom_ad}
                        {/if}
                    {/if}
                </div>

            </div>
        </div>

    </div>

</div>

</div>

{* BEGIN RIGHT SIDE *}
<div class="col3 last">
    <div class="body_1">
        {jrCore_include template="side_home.tpl"}
    </div>
</div>

</div>

</div>

{jrCore_include template="footer.tpl"}

