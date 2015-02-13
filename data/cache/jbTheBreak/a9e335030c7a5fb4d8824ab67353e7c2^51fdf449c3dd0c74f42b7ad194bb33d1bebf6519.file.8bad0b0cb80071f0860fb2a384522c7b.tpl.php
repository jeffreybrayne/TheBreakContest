<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:02
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/8bad0b0cb80071f0860fb2a384522c7b.tpl" */ ?>
<?php /*%%SmartyHeaderCode:132756015854d67642c58f57-45953675%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '51fdf449c3dd0c74f42b7ad194bb33d1bebf6519' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/8bad0b0cb80071f0860fb2a384522c7b.tpl',
      1 => 1423341122,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '132756015854d67642c58f57-45953675',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'entry' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d67642c9eb89_58334305',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d67642c9eb89_58334305')) {function content_54d67642c9eb89_58334305($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars["entry"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["entry"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["entry"]->key => $_smarty_tpl->tpl_vars["entry"]->value) {
$_smarty_tpl->tpl_vars["entry"]->_loop = true;
?>
    <?php $_smarty_tpl->tpl_vars["oc"] = new Smarty_variable('', null, 0);?>
    <?php if (isset($_smarty_tpl->tpl_vars['entry']->value['menu_onclick'])) {?>
        <?php $_smarty_tpl->tpl_vars["oc"] = new Smarty_variable(" onclick=\"".((string)$_smarty_tpl->tpl_vars['entry']->value['menu_onclick']), null, 0);?>
    <?php }?>
    <?php if (isset($_smarty_tpl->tpl_vars['entry']->value['menu_function_result'])&&strlen($_smarty_tpl->tpl_vars['entry']->value['menu_function_result'])>0) {?>
        <?php if (is_numeric($_smarty_tpl->tpl_vars['entry']->value['menu_function_result'])) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
 [<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_function_result'];?>
]</a></li>
            <?php } else { ?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
 <img src="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_function_result'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
"></a></li>
        <?php }?>
        <?php } else { ?>
    <li><a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['entry']->value['menu_label'];?>
</a></li>
    <?php }?>
<?php } ?>
<?php }} ?>
