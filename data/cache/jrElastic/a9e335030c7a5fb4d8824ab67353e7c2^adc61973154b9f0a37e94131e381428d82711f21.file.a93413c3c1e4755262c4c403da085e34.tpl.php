<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/a93413c3c1e4755262c4c403da085e34.tpl" */ ?>
<?php /*%%SmartyHeaderCode:170775089754d673e9c6cae1-93468771%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'adc61973154b9f0a37e94131e381428d82711f21' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/a93413c3c1e4755262c4c403da085e34.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '170775089754d673e9c6cae1-93468771',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'jamroom_url' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e9c98307_15275306',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9c98307_15275306')) {function content_54d673e9c98307_15275306($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
<?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php ob_start();?><?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['user_name']);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['user_name']);?>
<?php $_tmp2=ob_get_clean();?><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"small",'crop'=>"auto",'class'=>"img_shadow",'width'=>"40",'height'=>"40",'style'=>"padding:2px;margin-bottom:4px;",'alt'=>$_tmp1,'title'=>$_tmp2),$_smarty_tpl); } ?>
</a>
<?php } ?>
<?php }?>
<?php }} ?>
