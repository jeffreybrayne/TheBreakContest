<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:09
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/ebf5ad146f3ecbebc5b4f61209023ed5.tpl" */ ?>
<?php /*%%SmartyHeaderCode:124515224354d6760d66a8f7-78041506%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f0c9b567e6bb982cdf047816594ad90beb6dee54' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/ebf5ad146f3ecbebc5b4f61209023ed5.tpl',
      1 => 1423341069,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '124515224354d6760d66a8f7-78041506',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'jamroom_url' => 0,
    'item' => 0,
    '_conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760d6d09e6_48861189',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760d6d09e6_48861189')) {function content_54d6760d6d09e6_48861189($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"murl"),$_smarty_tpl); } ?>

    <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
        <li>
            <div class="container">
                <div class="row">
                    <div class="col3">
                        <div class="fleximage">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"xxlarge",'crop'=>"square",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'title'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'class'=>"img_shadow img_scale"),$_smarty_tpl); } ?>
</a>
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="fav_body ml20">
                            <div class="flex-caption">
                                <div class="flex-caption-content">
                                    <div  class="slidetext2" style="padding:0;margin:0;">
                                        <h1><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</a></h1><br>
                                        <br>
                                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAudio",'order_by'=>"_created desc",'limit'=>"2",'search1'=>"_profile_id = ".((string)$_smarty_tpl->tpl_vars['item']->value['_profile_id']),'template'=>"index_slider_song.tpl"),$_smarty_tpl); } ?>
<br>
                                        <span class="capital bold"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"143",'default'=>"Influences"),$_smarty_tpl); } ?>
:</span>&nbsp;<span class="hl-1"><?php if (isset($_smarty_tpl->tpl_vars['item']->value['profile_influences'])&&strlen($_smarty_tpl->tpl_vars['item']->value['profile_influences'])>70) {?><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_influences'],70,"...",true);?>
&nbsp;And more!!!<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_influences'];?>
<?php }?></span><br>
                                        <br>
                                        <div class="mobile">
                                            <span class="hl-4 bold"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"118",'default'=>"About"),$_smarty_tpl); } ?>
:</span><br>
                                            <?php echo smarty_modifier_jrCore_format_string(smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_bio'],260,"...",false),$_smarty_tpl->tpl_vars['item']->value['profile_quota_id']);?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    <?php } ?>
<?php }?>
<?php }} ?>
