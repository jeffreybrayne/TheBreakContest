<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:26:37
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/7fabeb56d2181ef4df9dad3d400b1809.tpl" */ ?>
<?php /*%%SmartyHeaderCode:166736090254d674fdd4bc90-34172312%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fe3560d785d3a875669e6966501c3f8890c429c' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/7fabeb56d2181ef4df9dad3d400b1809.tpl',
      1 => 1423340797,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '166736090254d674fdd4bc90-34172312',
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
  'unifunc' => 'content_54d674fde054e8_21898978',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674fde054e8_21898978')) {function content_54d674fde054e8_21898978($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars["entry"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["entry"]->_loop = false;
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
