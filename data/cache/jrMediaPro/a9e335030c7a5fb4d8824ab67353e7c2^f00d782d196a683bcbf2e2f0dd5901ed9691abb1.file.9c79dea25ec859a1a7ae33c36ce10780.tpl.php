<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:09
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/9c79dea25ec859a1a7ae33c36ce10780.tpl" */ ?>
<?php /*%%SmartyHeaderCode:78364401854d6760d510fa6-04596692%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f00d782d196a683bcbf2e2f0dd5901ed9691abb1' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/9c79dea25ec859a1a7ae33c36ce10780.tpl',
      1 => 1423341069,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '78364401854d6760d510fa6-04596692',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    '_post' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760d647247_40525153',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760d647247_40525153')) {function content_54d6760d647247_40525153($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars["selected"] = new Smarty_variable("home", null, 0);?>
<?php $_smarty_tpl->tpl_vars["spt"] = new Smarty_variable("home", null, 0);?>
<?php $_smarty_tpl->tpl_vars["no_inner_div"] = new Smarty_variable("true", null, 0);?>
<?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"header.tpl"),$_smarty_tpl); } ?>

<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#top_singles',core_system_url + '/index_top_singles');
        jrLoad('#newest_artists',core_system_url + '/index_new_artists');
        jrLoad('#top_artists',core_system_url + '/index_top_artists');
         });
</script>


<div class="container">
    <div class="row">
        <div class="col12 last">

            <div class="slider_container">
                <a onfocus="blur();" href="javascript:void(0);" id="fadeout-carousel"><div class="button-toggle"></div></a>
                <div class="toggle-carousel">

                    <section class="slider">
                        <div id="slider" class="flexslider">
                            <ul class="slides">
                                <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_slider_profile_ids'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_slider_profile_ids']>0) {?>
                                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'search'=>"_item_id in ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_slider_profile_ids']),'template'=>"index_slider.tpl",'limit'=>"21"),$_smarty_tpl); } ?>

                                <?php } elseif (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images']=='on') {?>
                                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'search2'=>"profile_jrAudio_item_count > 0",'template'=>"index_slider.tpl",'limit'=>"21",'require_image'=>"profile_image"),$_smarty_tpl); } ?>

                                <?php } else { ?>
                                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'search2'=>"profile_jrAudio_item_count > 0",'template'=>"index_slider.tpl",'limit'=>"21"),$_smarty_tpl); } ?>

                                <?php }?>
                            </ul>
                        </div>
                        <div id="carousel" class="flexslider">
                            <ul class="slides">
                                <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_slider_profile_ids'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_slider_profile_ids']>0) {?>
                                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'search'=>"_item_id in ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_slider_profile_ids']),'template'=>"index_slider_thumbs.tpl",'limit'=>"21"),$_smarty_tpl); } ?>

                                <?php } elseif (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images']=='on') {?>
                                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'search2'=>"profile_jrAudio_item_count > 0",'template'=>"index_slider_thumbs.tpl",'limit'=>"21",'require_image'=>"profile_image"),$_smarty_tpl); } ?>

                                <?php } else { ?>
                                    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'search2'=>"profile_jrAudio_item_count > 0",'template'=>"index_slider_thumbs.tpl",'limit'=>"21"),$_smarty_tpl); } ?>

                                <?php }?>
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


<div class="col9">
<div class="body_1 mr5">
    <div class="container">

        
        <div class="row">

            <div class="col12 last">

                <h1><span style="font-weight:normal;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"21",'default'=>"Featured"),$_smarty_tpl); } ?>
</span>&nbsp;<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"48",'default'=>"Artist"),$_smarty_tpl); } ?>
</h1>
                <div id="featured_artists" class="mb20">

                    <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_profile_ids'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_profile_ids']>0) {?>
                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'search'=>"_item_id in ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_profile_ids']),'template'=>"index_featured.tpl",'pagebreak'=>"1",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

                    <?php } elseif (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images']=='on') {?>
                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'limit'=>"10",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'search2'=>"profile_jrAudio_item_count > 0",'template'=>"index_featured.tpl",'require_image'=>"profile_image",'pagebreak'=>"1",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

                    <?php } else { ?>
                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'limit'=>"10",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'search2'=>"profile_jrAudio_item_count > 0",'template'=>"index_featured.tpl",'pagebreak'=>"1",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

                    <?php }?>

                </div>

            </div>

        </div>

        
        <a id="tsingles" name="tsingles"></a>
        <div class="row">

            <div class="col12 last">
                <br>
                <br>
                <br>
                <h1><span style="font-weight: normal;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"58",'default'=>"Top"),$_smarty_tpl); } ?>
</span> <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"171",'default'=>"Singles"),$_smarty_tpl); } ?>
</h1><br>
                <br>
                <div class="top_singles_body mb30 pt20">
                    <div id="top_singles">
                    </div>
                </div>

            </div>

        </div>

        
        <div class="row">

            <div class="col12 last">
                <a id="newartists" name="newartists"></a>
                <br>
                <br>
                <br>
                <h1><span style="font-weight: normal;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"11",'default'=>"Newest"),$_smarty_tpl); } ?>
</span> <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"48",'default'=>"Artists"),$_smarty_tpl); } ?>
</h1><br>
                <div class="mb30 pt20">
                    <div id="newest_artists">

                    </div>
                </div>

            </div>

        </div>

        
        <div class="row">

            <div class="col12 last">
                <br>
                <br>
                <br>
                <h1><span style="font-weight: normal;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"58",'default'=>"top"),$_smarty_tpl); } ?>
</span>&nbsp;10&nbsp;<span style="font-weight: normal;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"48",'default'=>"Artists"),$_smarty_tpl); } ?>
</span></h1><br>
                <br>
                <div class="mb30 pt20">
                    <div id="top_artists">

                    </div>
                </div>

            </div>

        </div>

        
        <div class="row">
            <div class="col12 last">

                <div class="center">
                    <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_ads_off']!='on') {?>
                        <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_google_ads'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_google_ads']=='on') {?>
                            <script type="text/javascript"><!--
                                google_ad_client = "<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_google_id'];?>
";
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
                        <?php } elseif (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_bottom_ad'])&&strlen($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_bottom_ad'])>0) {?>
                            <?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_bottom_ad'];?>

                        <?php } else { ?>
                            <a href="https://www.jamroom.net/" target="_blank"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"728x90_banner.png",'alt'=>"728x90 Ad",'title'=>"Get Jamroom5!",'class'=>"img_scale",'style'=>"max-width:728px;max-height:90px;"),$_smarty_tpl); } ?>
</a>
                        <?php }?>
                    <?php }?>
                </div>

            </div>
        </div>

    </div>

</div>

</div>


<div class="col3 last">
    <div class="body_1">
        <?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"side_home.tpl"),$_smarty_tpl); } ?>

    </div>
</div>

</div>

</div>

<?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"footer.tpl"),$_smarty_tpl); } ?>


<?php }} ?>
