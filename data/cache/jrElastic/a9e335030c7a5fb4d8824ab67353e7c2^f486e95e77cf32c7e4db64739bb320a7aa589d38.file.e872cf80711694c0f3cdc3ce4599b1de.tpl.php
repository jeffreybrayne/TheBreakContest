<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/e872cf80711694c0f3cdc3ce4599b1de.tpl" */ ?>
<?php /*%%SmartyHeaderCode:181009924154d673e9590ef2-28505903%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f486e95e77cf32c7e4db64739bb320a7aa589d38' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/e872cf80711694c0f3cdc3ce4599b1de.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '181009924154d673e9590ef2-28505903',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'item' => 0,
    'jamroom_url' => 0,
    'murl' => 0,
    '_user' => 0,
    '_post' => 0,
    'img' => 0,
    'lurl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e9833004_87006445',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9833004_87006445')) {function content_54d673e9833004_87006445($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>

    <?php if ((jrCore_module_is_active('jrComment')&&$_smarty_tpl->tpl_vars['_items']->value[0]['quota_jrComment_allowed']=='on')||(jrCore_module_is_active('jrDisqus')&&$_smarty_tpl->tpl_vars['_items']->value[0]['quota_jrDisqus_allowed']=='on')) {?>
        <?php $_smarty_tpl->tpl_vars["img"] = new Smarty_variable("comments.png", null, 0);?>
        <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"22",'default'=>"Comments",'assign'=>"alt"),$_smarty_tpl); } ?>

    <?php } else { ?>
        <?php $_smarty_tpl->tpl_vars["img"] = new Smarty_variable("link.png", null, 0);?>
        <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"23",'default'=>"Link To This",'assign'=>"alt"),$_smarty_tpl); } ?>

    <?php }?>

    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrAction",'assign'=>"murl"),$_smarty_tpl); } ?>

    <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>

    
    <?php if (isset($_smarty_tpl->tpl_vars['item']->value['action_original_profile_url'])) {?>

        <div id="a<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_holder_shared">
            <div class="container">
                <div class="row">

                    <div class="col2">
                        <div class="action_item_media" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['action_original_profile_name']);?>
" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_item_id'];?>
')">
                            <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['action_original_user_id'],'size'=>"icon",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"action_item_user_img img_shadow img_scale"),$_smarty_tpl); } ?>

                        </div>
                    </div>

                    <div class="col9">
                        <div class="action_item_desc">

                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_profile_url'];?>
" class="action_item_title" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['action_original_profile_name']);?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_profile_url'];?>
</a> <span class="action_item_actions">&bull; <?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['item']->value['_created'],"relative");?>
 &bull; <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"21",'default'=>"Shared By"),$_smarty_tpl); } ?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</a></span><br>

                            <div class="action_item_link" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['action_original_profile_name']);?>
" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_original_profile_url'];?>
');">
                            <?php if (isset($_smarty_tpl->tpl_vars['item']->value['action_data'])&&strlen($_smarty_tpl->tpl_vars['item']->value['action_data'])>0) {?>
                                <?php echo $_smarty_tpl->tpl_vars['item']->value['action_data'];?>

                            <?php } else { ?>
                                <div class="p5"><?php echo smarty_modifier_jrAction_convert_hash_tags(smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['action_text'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id']));?>
</div>
                            <?php }?>
                            </div>

                        </div>
                    </div>

                    <div class="col1 last">
                        <div id="d<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_delete">
                            <script>$(function () { $('#a<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').hover(function() { $('#d<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').toggle(); }); });</script>
                            <?php if (function_exists('smarty_function_jrCore_item_delete_button')) { echo smarty_function_jrCore_item_delete_button(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl); } ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    
    <?php } elseif (isset($_smarty_tpl->tpl_vars['item']->value['action_text'])) {?>

        <div id="a<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_holder">
            <div class="container">
                <div class="row">

                    <div class="col2">
                        <div class="action_item_media" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
')">
                            <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"icon",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"action_item_user_img img_shadow img_scale"),$_smarty_tpl); } ?>

                        </div>
                    </div>
                    <div class="col9">

                        <div class="action_item_desc">

                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
" class="action_item_title" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</a> <span class="action_item_actions">&bull; <?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['item']->value['_created'],"relative");?>
<?php if (jrUser_is_logged_in()&&$_smarty_tpl->tpl_vars['_user']->value['_user_id']!=$_smarty_tpl->tpl_vars['item']->value['_user_id']&&$_smarty_tpl->tpl_vars['item']->value['action_shared_by_user']!='1') {?> &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/share/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" onclick="if(!confirm('<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"9",'default'=>"Share this update with your followers?"),$_smarty_tpl); } ?>
')) { return false; }"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"10",'default'=>"Share This"),$_smarty_tpl); } ?>
</a><?php }?> <?php if ($_smarty_tpl->tpl_vars['_post']->value['module_url']==$_smarty_tpl->tpl_vars['_user']->value['profile_url']&&$_smarty_tpl->tpl_vars['item']->value['action_shared_by_user']=='1') {?> &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"26",'default'=>"shared by you"),$_smarty_tpl); } ?>
</a> <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['action_shared_by_count']>0) {?> &bull; <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"24",'default'=>"shared by"),$_smarty_tpl); } ?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['action_shared_by_count'];?>
 <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"25",'default'=>"follower(s)"),$_smarty_tpl); } ?>
</a><?php }?><?php if ($_smarty_tpl->tpl_vars['img']->value=="comments.png") {?> &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"22",'default'=>"Comments"),$_smarty_tpl); } ?>
: <?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['action_comment_count'])===null||strlen($tmp)===0 ? 0 : $tmp);?>
</a><?php }?></span><br>

                            <div class="action_item_link">
                                <div class="p5"><?php echo smarty_modifier_jrAction_convert_hash_tags(smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['action_text'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id']));?>
</div>
                            </div>

                        </div>
                    </div>
                    <div class="col1 last">
                        <div id="d<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_delete">
                            <script>$(function () { $('#a<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').hover(function() { $('#d<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').toggle(); }); });</script>
                            <?php if (function_exists('smarty_function_jrCore_item_delete_button')) { echo smarty_function_jrCore_item_delete_button(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl); } ?>

                        </div>
                    </div>

                </div>
            </div>
         </div>

    
    <?php } elseif (isset($_smarty_tpl->tpl_vars['item']->value['action_data'])&&strpos($_smarty_tpl->tpl_vars['item']->value['action_data'],'{')!==0) {?>

        <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>$_smarty_tpl->tpl_vars['item']->value['action_module'],'assign'=>"lurl"),$_smarty_tpl); } ?>


        <div id="a<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_holder">
            <div class="container">
                <div class="row">

                    <div class="col2">
                    <?php if (isset($_smarty_tpl->tpl_vars['item']->value['album_title_url'])) {?>
                        <div class="action_item_media" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['lurl']->value;?>
/albums/<?php echo $_smarty_tpl->tpl_vars['item']->value['album_title_url'];?>
')">
                    <?php } elseif (isset($_smarty_tpl->tpl_vars['item']->value['action_title_url'])) {?>
                        <div class="action_item_media" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['lurl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_title_url'];?>
')">
                    <?php } else { ?>
                        <div class="action_item_media">
                    <?php }?>
                            <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"icon",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"action_item_user_img img_shadow img_scale"),$_smarty_tpl); } ?>

                        </div>
                    </div>
                    <div class="col9">

                        <div class="action_item_desc">

                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
" class="action_item_title" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</a> <span class="action_item_actions">&bull; <?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['item']->value['_created'],"relative");?>
<?php if (jrUser_is_logged_in()&&$_smarty_tpl->tpl_vars['_user']->value['_user_id']!=$_smarty_tpl->tpl_vars['item']->value['_user_id']&&$_smarty_tpl->tpl_vars['item']->value['action_shared_by_user']!='1') {?> &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/share/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" onclick="if(!confirm('<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"9",'default'=>"Share this update with your followers?"),$_smarty_tpl); } ?>
')) { return false; }"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"10",'default'=>"Share This"),$_smarty_tpl); } ?>
</a><?php }?> <?php if ($_smarty_tpl->tpl_vars['_post']->value['module_url']==$_smarty_tpl->tpl_vars['_user']->value['profile_url']&&$_smarty_tpl->tpl_vars['item']->value['action_shared_by_user']=='1') {?> &bull; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"26",'default'=>"shared by you"),$_smarty_tpl); } ?>
</a> <?php } elseif ($_smarty_tpl->tpl_vars['item']->value['action_shared_by_count']>0) {?> &bull; <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"24",'default'=>"shared by"),$_smarty_tpl); } ?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['action_shared_by_count'];?>
 <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"25",'default'=>"follower(s)"),$_smarty_tpl); } ?>
</a><?php }?></span><br>

                            <?php if (isset($_smarty_tpl->tpl_vars['item']->value['album_title_url'])) {?>
                            <div class="action_item_link" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['lurl']->value;?>
/albums/<?php echo $_smarty_tpl->tpl_vars['item']->value['album_title_url'];?>
')">
                            <?php } elseif (isset($_smarty_tpl->tpl_vars['item']->value['action_title_url'])) {?>
                            <div class="action_item_link" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['lurl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['action_title_url'];?>
')">
                            <?php } else { ?>
                            <div class="action_item_link">
                            <?php }?>
                                <?php echo $_smarty_tpl->tpl_vars['item']->value['action_data'];?>

                            </div>

                        </div>
                    </div>
                    <div class="col1 last">
                        <div id="d<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
" class="action_item_delete">
                            <script>$(function () { $('#a<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').hover(function() { $('#d<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
').toggle(); }); });</script>
                            <?php if (function_exists('smarty_function_jrCore_item_delete_button')) { echo smarty_function_jrCore_item_delete_button(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id']),$_smarty_tpl); } ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    <?php }?>

    <?php } ?>

<?php }?>
<?php }} ?>
