<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:21:47
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/bf5fd23a9c9297f69a25039cc6fa3fcd.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13715646954d673dbc119b0-06171018%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6d2f5ea3c4ef1c1ee7bc47bee8afa94e6f6d837a' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/bf5fd23a9c9297f69a25039cc6fa3fcd.tpl',
      1 => 1423340507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13715646954d673dbc119b0-06171018',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    'st' => 0,
    'jamroom_url' => 0,
    'core_url' => 0,
    'url' => 0,
    'murl' => 0,
    'purl' => 0,
    'uurl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673dbd14de2_26856684',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673dbd14de2_26856684')) {function content_54d673dbd14de2_26856684($_smarty_tpl) {?><div id="menu_content">
    <nav id="menu-wrap">
        <ul id="menu">

            
            <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrNotify_bell_icon"),$_smarty_tpl); } ?>


            
            <?php if (jrCore_module_is_active('jrFoxyCart')&&strlen($_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_api_key'])>0) {?>
                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_store_domain'];?>
/cart?cart=view"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"cart24.png",'width'=>"24",'height'=>"24",'alt'=>"cart"),$_smarty_tpl); } ?>
</a>
                    <span id="fc_minicart"><span id="fc_quantity"></span></span>
                </li>
            <?php }?>

            <?php if (jrCore_module_is_active('jrSearch')) {?>
                <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"24",'default'=>"search",'assign'=>"st"),$_smarty_tpl); } ?>

                <li><a onclick="jrSearch_modal_form();" title="<?php echo $_smarty_tpl->tpl_vars['st']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['st']->value;?>
</a></li>
            <?php }?>

            <?php if (jrUser_is_logged_in()) {?>
                <?php if (jrUser_is_admin()) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrCore"),$_smarty_tpl); } ?>
/dashboard"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"17",'default'=>"dashboard"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrUser_home_profile_key')) { echo smarty_function_jrUser_home_profile_key(array('key'=>"profile_url"),$_smarty_tpl); } ?>
"><?php if (function_exists('smarty_function_jrUser_home_profile_key')) { echo smarty_function_jrUser_home_profile_key(array('key'=>"profile_name"),$_smarty_tpl); } ?>
</a>
                    <ul>
                        <?php if (function_exists('smarty_function_jrCore_skin_menu')) { echo smarty_function_jrCore_skin_menu(array('template'=>"menu.tpl",'category'=>"user"),$_smarty_tpl); } ?>

                    </ul>
                </li>
            <?php }?>


            

            <?php if (jrUser_is_logged_in()) {?>
                <?php if (jrUser_is_master()) {?>
                    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"core_url"),$_smarty_tpl); } ?>

                    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrMarket",'assign'=>"murl"),$_smarty_tpl); } ?>

                    <?php if (function_exists('smarty_function_jrCore_get_module_index')) { echo smarty_function_jrCore_get_module_index(array('module'=>"jrCore",'assign'=>"url"),$_smarty_tpl); } ?>

                    <li>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin/global">ACP</a>
                        <ul>
                            <li>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin/tools">system tools</a>
                                <ul>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
">activity logs</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/cache_reset">reset caches</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/integrity_check">integrity check</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/system_update">system updates</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/system_check">system check</a></li>
                                </ul>
                            </li>
                            <li>
                                <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"purl"),$_smarty_tpl); } ?>

                                <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl); } ?>

                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/admin/tools">users</a>
                                <ul>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/quota_browser">quota browser</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/browser">profile browser</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/browser">user accounts</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/online">users online</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/global/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
">skin settings</a>
                                <ul>
                                    <li><a onclick="popwin('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/readme.html','readme',600,500,'yes');">skin notes</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_menu">user menu editor</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/images/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
">skin images</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/style/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
">skin style</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/templates/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
">skin templates</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                <?php }?>
            <?php } else { ?>
                <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl); } ?>

                <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrCore_maintenance_mode']!='on'&&$_smarty_tpl->tpl_vars['_conf']->value['jrUser_signup_on']=='on') {?>
                    <li><a id="user-create-account" href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/signup"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"2",'default'=>"create"),$_smarty_tpl); } ?>
&nbsp;<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"3",'default'=>"account"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/login"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"6",'default'=>"login"),$_smarty_tpl); } ?>
</a></li>
            <?php }?>


        </ul>
    </nav>

</div>
<?php }} ?>
