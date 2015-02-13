<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:26:36
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/2757bf4ed04a18725f72f06be7ca7061.tpl" */ ?>
<?php /*%%SmartyHeaderCode:58598600454d674fc638323-29496883%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4c332bd546d9bac127ff334ed862ddc52484500b' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/2757bf4ed04a18725f72f06be7ca7061.tpl',
      1 => 1423340796,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '58598600454d674fc638323-29496883',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'spt' => 0,
    '_conf' => 0,
    '_user' => 0,
    'jamroom_url' => 0,
    '_post' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d674fc74b8b3_44394853',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674fc74b8b3_44394853')) {function content_54d674fc74b8b3_44394853($_smarty_tpl) {?><?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"meta.tpl"),$_smarty_tpl); } ?>


<body<?php if (isset($_smarty_tpl->tpl_vars['spt']->value)&&$_smarty_tpl->tpl_vars['spt']->value=='home') {?> class="loading"<?php }?>>

<?php if (jrCore_is_mobile_device()) {?>
    <?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"header_menu_mobile.tpl"),$_smarty_tpl); } ?>

<?php }?>


<?php if (!jrCore_is_mobile_device()) {?>
<div id="top-bar">
    <div class="top-bar-wrapper">
        <div class="container">
            <div class="row">
                <div class="col8">
                    <div class="welcome">

                    <?php if (jrUser_is_logged_in()) {?>
                        <span style="color:#999999;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"102",'default'=>"Welcome"),$_smarty_tpl); } ?>
&nbsp;&nbsp;</span><span class="bold hl-1"><?php if (function_exists('smarty_function_jrUser_home_profile_key')) { echo smarty_function_jrUser_home_profile_key(array('key'=>"profile_name"),$_smarty_tpl); } ?>
</span>&nbsp;|&nbsp;
                        <?php if (jrCore_module_is_active('jrPrivateNote')) {?>
                            <?php if (isset($_smarty_tpl->tpl_vars['_user']->value['user_jrPrivateNote_unread_count'])&&$_smarty_tpl->tpl_vars['_user']->value['user_jrPrivateNote_unread_count']>0) {?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/note/notes" target="_top"><span class="page-welcome" style="padding:2px;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"103",'default'=>"In Box"),$_smarty_tpl); } ?>
</span></a> <span class="hl-3">(<?php echo $_smarty_tpl->tpl_vars['_user']->value['user_jrPrivateNote_unread_count'];?>
)</span> |
                            <?php } else { ?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/note/notes" target="_top"><span class="page-welcome" style="padding:2px;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"103",'default'=>"In Box"),$_smarty_tpl); } ?>
</span></a> |
                            <?php }?>
                        <?php }?>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/user/logout" target="_top" onclick="if (!confirm('Are you Sure you want to Log out?')) return false;"><span class="page-welcome" style="padding:2px;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"5",'default'=>"Logout"),$_smarty_tpl); } ?>
</span></a>
                    <?php } else { ?>
                        <span style="color:#999999;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"7",'default'=>"Welcome Guest"),$_smarty_tpl); } ?>
!</span> | <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/user/login" target="_top"><span class="page-welcome" style="padding:2px;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"6",'default'=>"Login"),$_smarty_tpl); } ?>
</span></a>
                    <?php }?>

                        <?php if (jrUser_is_logged_in()) {?>
                            | <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrUser_home_profile_key')) { echo smarty_function_jrUser_home_profile_key(array('key'=>"profile_url"),$_smarty_tpl); } ?>
" title="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"102",'default'=>"Welcome"),$_smarty_tpl); } ?>
 <?php if (function_exists('smarty_function_jrUser_home_profile_key')) { echo smarty_function_jrUser_home_profile_key(array('key'=>"profile_name"),$_smarty_tpl); } ?>
"><span class="page-welcome" style="padding:2px;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"104",'default'=>"Your Home"),$_smarty_tpl); } ?>
</span></a>
                        <?php }?>

                    </div>
                </div>
                <div class="col4 last">
                    <div class="flags">
                        <a href="?set_user_language=en-US"><img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/flags/us.png" alt="US" title="English US"></a>
                        <a href="?set_user_language=es-ES"><img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/flags/es.png" alt="ES" title="Spanish"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>


<div id="header">

    <div id="header_content">

        <div class="container">

            <div class="row">

                <div class="col6">
                    
                    <div id="main_logo">
                        <?php if (jrCore_is_mobile_device()) {?>
                            <?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('id'=>"mmt",'skin'=>"jrMediaPro",'image'=>"menu.png",'alt'=>"menu"),$_smarty_tpl); } ?>

                            <?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"logo.png",'class'=>"img_scale",'alt'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'],'title'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'],'style'=>"max-width:225px;max-height:48px;",'custom'=>"logo"),$_smarty_tpl); } ?>

                        <?php } else { ?>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"logo.png",'class'=>"img_scale",'alt'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'],'title'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'],'style'=>"max-width:375px;max-height:80px;",'custom'=>"logo"),$_smarty_tpl); } ?>
</a>
                        <?php }?>
                    </div>
                </div>
                <div class="col6 last">
                    <div class="logo-ads">
                        <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_ads_off']!='on') {?>
                            <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_google_ads'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_google_ads']=='on') {?>
                                <script type="text/javascript"><!--
                                    google_ad_client = "<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_google_id'];?>
";
                                    google_ad_width = 468;
                                    google_ad_height = 60;
                                    google_ad_format = "468x60_as";
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
                            <?php } elseif (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_top_ad'])&&strlen($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_top_ad'])>0) {?>
                                <?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_top_ad'];?>

                            <?php } else { ?>
                                <a href="http://www.jamroom.net/" target="_blank"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"468x60_banner.png",'alt'=>"486x60 Ad",'title'=>"Get Jamroom5!",'class'=>"img_scale",'style'=>"max-width:468px;max-height:60px;"),$_smarty_tpl); } ?>
</a>
                            <?php }?>
                        <?php }?>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>


<?php if (!jrCore_is_mobile_device()) {?>
    <?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"header_menu_desktop.tpl"),$_smarty_tpl); } ?>

<?php }?>


<div id="wrapper">


    <div id="searchform" class="search_box" style="display:none;">
        <div class="float-right ml10"><input type="button" class="simplemodal-close form_button" value="x"></div>
        <span class="media_title"><?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
 Site Search</span><br><br>
        <?php if (function_exists('smarty_function_jrSearch_form')) { echo smarty_function_jrSearch_form(array('class'=>"form_text",'value'=>"Search Site",'style'=>"width:70%"),$_smarty_tpl); } ?>

        <div class="clear"></div>
    </div>

    <?php if ($_smarty_tpl->tpl_vars['spt']->value=='home'||$_smarty_tpl->tpl_vars['spt']->value=='profiles') {?>
    <?php } else { ?>
        <div id="content">
    <?php }?>
        <!-- end header.tpl -->

        
        <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['module'])&&$_smarty_tpl->tpl_vars['_post']->value['option']!='admin'&&($_smarty_tpl->tpl_vars['_post']->value['module']=='jrRecommend'||$_smarty_tpl->tpl_vars['_post']->value['module']=='jrSearch')) {?>
        <div class="container">

            <div class="row">

                <div class="col9">
                    <div class="body_1 mr5">

                        <div class="title"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"124",'default'=>"Search Results"),$_smarty_tpl); } ?>
</div>
                            <div class="body_5">
        <?php }?>
<?php }} ?>
