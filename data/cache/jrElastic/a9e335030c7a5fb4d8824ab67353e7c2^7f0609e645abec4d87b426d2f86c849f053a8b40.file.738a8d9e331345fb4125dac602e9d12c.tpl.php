<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:25:18
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/738a8d9e331345fb4125dac602e9d12c.tpl" */ ?>
<?php /*%%SmartyHeaderCode:167248181054d674ae0a3471-69250842%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f0609e645abec4d87b426d2f86c849f053a8b40' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/738a8d9e331345fb4125dac602e9d12c.tpl',
      1 => 1423340718,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '167248181054d674ae0a3471-69250842',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'rownum' => 0,
    'class' => 0,
    'cells' => 0,
    '_cell' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d674ae0d2b48_15027509',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674ae0d2b48_15027509')) {function content_54d674ae0d2b48_15027509($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['rownum']->value)&&$_smarty_tpl->tpl_vars['rownum']->value%2===0) {?>
<tr class="page_table_row<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
<?php } else { ?>
<tr class="page_table_row_alt<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
<?php }?>
<?php  $_smarty_tpl->tpl_vars["_cell"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_cell"]->_loop = false;
 $_smarty_tpl->tpl_vars["num"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cells']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_cell"]->key => $_smarty_tpl->tpl_vars["_cell"]->value) {
$_smarty_tpl->tpl_vars["_cell"]->_loop = true;
 $_smarty_tpl->tpl_vars["num"]->value = $_smarty_tpl->tpl_vars["_cell"]->key;
?>
  <?php if (isset($_smarty_tpl->tpl_vars['_cell']->value['class'])) {?>
  <td class="page_table_cell <?php echo $_smarty_tpl->tpl_vars['_cell']->value['class'];?>
"<?php echo $_smarty_tpl->tpl_vars['_cell']->value['colspan'];?>
><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</td>
  <?php } else { ?>
  <td class="page_table_cell"<?php echo $_smarty_tpl->tpl_vars['_cell']->value['colspan'];?>
><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</td>
  <?php }?>
<?php } ?>
</tr>
<?php }} ?>
