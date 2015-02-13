<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:25:18
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/cc6cf03251ac602d74507bb881eba8c3.tpl" */ ?>
<?php /*%%SmartyHeaderCode:149934863454d674ae04e957-93858069%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1e19752abf80b2898f624b375db163d4fb6c367' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/cc6cf03251ac602d74507bb881eba8c3.tpl',
      1 => 1423340718,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '149934863454d674ae04e957-93858069',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'class' => 0,
    'cells' => 0,
    '_cell' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d674ae09e202_49919321',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674ae09e202_49919321')) {function content_54d674ae09e202_49919321($_smarty_tpl) {?><tr>
    <td colspan="2">
        <table class="page_table<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
        <?php if (count($_smarty_tpl->tpl_vars['cells']->value)>0) {?>
            <tr class="nodrag nodrop">
            <?php  $_smarty_tpl->tpl_vars["_cell"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_cell"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cells']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_cell"]->key => $_smarty_tpl->tpl_vars["_cell"]->value) {
$_smarty_tpl->tpl_vars["_cell"]->_loop = true;
?>
            <?php if (isset($_smarty_tpl->tpl_vars['_cell']->value['class'])) {?>
                <th class="page_table_header <?php echo $_smarty_tpl->tpl_vars['_cell']->value['class'];?>
" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
            <?php } else { ?>
                <th class="page_table_header" style="width:<?php echo $_smarty_tpl->tpl_vars['_cell']->value['width'];?>
"><?php echo $_smarty_tpl->tpl_vars['_cell']->value['title'];?>
</th>
            <?php }?>
            <?php } ?>
            </tr>
        <?php }?>
<?php }} ?>
