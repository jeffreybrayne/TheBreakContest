<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:09
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/0de172e1fc58e821f8b8c18e27c9d615.tpl" */ ?>
<?php /*%%SmartyHeaderCode:97530996854d6760d6db9e8-15871337%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3204a0a600c9cb882367752e8aef7e8e17a46084' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/0de172e1fc58e821f8b8c18e27c9d615.tpl',
      1 => 1423341069,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97530996854d6760d6db9e8-15871337',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760d6fafa6_41776100',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760d6fafa6_41776100')) {function content_54d6760d6fafa6_41776100($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl); } ?>

<?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
<li>
    <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"xxlarge",'crop'=>"square",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'title'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'style'=>"max-width:150px;"),$_smarty_tpl); } ?>

</li>
<?php } ?>
<?php }?>
<?php }} ?>
