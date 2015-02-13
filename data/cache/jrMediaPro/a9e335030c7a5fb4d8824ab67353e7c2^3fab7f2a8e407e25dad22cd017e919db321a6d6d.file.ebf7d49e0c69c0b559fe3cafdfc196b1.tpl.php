<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:11
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/ebf7d49e0c69c0b559fe3cafdfc196b1.tpl" */ ?>
<?php /*%%SmartyHeaderCode:53723657754d6760f5f3638-32037402%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3fab7f2a8e407e25dad22cd017e919db321a6d6d' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/ebf7d49e0c69c0b559fe3cafdfc196b1.tpl',
      1 => 1423341071,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '53723657754d6760f5f3638-32037402',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'item' => 0,
    'jamroom_url' => 0,
    '_conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760f6903d0_41667843',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760f6903d0_41667843')) {function content_54d6760f6903d0_41667843($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
        <div class="body_5" style="margin-right:auto;">
            <div class="container">
                <div class="row">

                    <div class="col1">
                        <div class="rank mobile" style="font-size:24px;vertical-align:middle;padding-top:50px;">
                            <?php echo $_smarty_tpl->tpl_vars['item']->value['list_rank'];?>
&nbsp;
                        </div>
                    </div>
                    <div class="col2">
                        <div class="center middle">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"medium",'crop'=>"auto",'class'=>"iloutline img_shadow img_scale",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'title'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'style'=>"max-width:190px;margin-bottom:10px;"),$_smarty_tpl); } ?>
</a><br>
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="left" style="padding-left:15px;">
                            <span class="capital bold"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"121",'default'=>"Name"),$_smarty_tpl); } ?>
</span>: <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
"><span class="capital bold"><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</span></a><br>
                            <?php if (isset($_smarty_tpl->tpl_vars['item']->value['profile_influences'])&&strlen($_smarty_tpl->tpl_vars['item']->value['profile_influences'])>0) {?>
                                <span class="capital bold"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"143",'default'=>"Influences"),$_smarty_tpl); } ?>
</span>: <span class="hl-2"><?php if (isset($_smarty_tpl->tpl_vars['item']->value['profile_influences'])&&strlen($_smarty_tpl->tpl_vars['item']->value['profile_influences'])>70) {?><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_influences'],70,"...",true);?>
&nbsp;And more!!!<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_influences'];?>
<?php }?></span><br>
                            <?php }?>
                            <span class="capital bold"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"50",'default'=>"Views"),$_smarty_tpl); } ?>
</span>: <span class="hl-3"><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_view_count'];?>
</span></a><br>
                            <?php if (!isset($_smarty_tpl->tpl_vars['item']->value['profile_influences'])||strlen($_smarty_tpl->tpl_vars['item']->value['profile_influences'])==0) {?>
                                <br>
                            <?php }?>
                            <?php if (isset($_smarty_tpl->tpl_vars['item']->value['profile_bio'])&&strlen($_smarty_tpl->tpl_vars['item']->value['profile_bio'])>0) {?>
                                <span class="hl-4 bold"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"118",'default'=>"About"),$_smarty_tpl); } ?>
:</span><br>
                                <?php echo nl2br(smarty_modifier_jrCore_format_string(smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_bio'],106,"...",false),$_smarty_tpl->tpl_vars['item']->value['profile_quota_id']));?>
<br>
                            <?php } else { ?>
                                <br><br><br>
                            <?php }?>
                            <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAudio",'order_by'=>"_created desc",'limit'=>"1",'search1'=>"_profile_id = ".((string)$_smarty_tpl->tpl_vars['item']->value['_profile_id']),'template'=>"index_top_artists_song.tpl"),$_smarty_tpl); } ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    <?php } ?>
<?php }?>

<?php }} ?>
