<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:25:18
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/bde7f71369a7bc44f79b4754bb9f661a.tpl" */ ?>
<?php /*%%SmartyHeaderCode:71079583354d674ae109965-86854864%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9637ae055321f3027d85e291ba40aa3dbee5d5ab' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/bde7f71369a7bc44f79b4754bb9f661a.tpl',
      1 => 1423340718,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '71079583354d674ae109965-86854864',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cells' => 0,
    '_cell' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d674ae12dc28_56959626',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674ae12dc28_56959626')) {function content_54d674ae12dc28_56959626($_smarty_tpl) {?><?php if (is_array($_smarty_tpl->tpl_vars['cells']->value)) {?>
<tr class="nodrag nodrop">
<?php  $_smarty_tpl->tpl_vars["_cell"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_cell"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cells']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_cell"]->key => $_smarty_tpl->tpl_vars["_cell"]->value) {
$_smarty_tpl->tpl_vars["_cell"]->_loop = true;
?>
    <?php if (isset($_smarty_tpl->tpl_vars['_cell']->value['class'])) {?>
        <th class="page_table_footer <?php echo $_smarty_tpl->tpl_vars['_cell']->value['class'];?>
" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
    <?php } else { ?>
        <th class="page_table_footer" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
    <?php }?>
<?php } ?>
</tr>
<?php }?>

</table>
</td>
</tr>    
<?php }} ?>
