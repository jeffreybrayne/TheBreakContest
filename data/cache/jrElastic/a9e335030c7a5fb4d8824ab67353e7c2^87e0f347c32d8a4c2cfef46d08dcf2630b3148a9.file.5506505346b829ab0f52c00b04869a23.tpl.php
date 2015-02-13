<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/5506505346b829ab0f52c00b04869a23.tpl" */ ?>
<?php /*%%SmartyHeaderCode:182544862754d673e9ab2095-59716097%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '87e0f347c32d8a4c2cfef46d08dcf2630b3148a9' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/5506505346b829ab0f52c00b04869a23.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '182544862754d673e9ab2095-59716097',
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
  'unifunc' => 'content_54d673e9aebc62_59444309',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9aebc62_59444309')) {function content_54d673e9aebc62_59444309($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
<?php  $_smarty_tpl->tpl_vars["entry"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["entry"]->_loop = false;
 $_smarty_tpl->tpl_vars["module"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["entry"]->key => $_smarty_tpl->tpl_vars["entry"]->value) {
$_smarty_tpl->tpl_vars["entry"]->_loop = true;
 $_smarty_tpl->tpl_vars["module"]->value = $_smarty_tpl->tpl_vars["entry"]->key;
?>
    <?php if ($_smarty_tpl->tpl_vars['entry']->value['active']=='1') {?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['target'];?>
"><div class="profile_menu_entry profile_menu_entry_active"><?php echo $_smarty_tpl->tpl_vars['entry']->value['label'];?>
</div></a>
    <?php } else { ?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['entry']->value['target'];?>
"><div class="profile_menu_entry"><?php echo $_smarty_tpl->tpl_vars['entry']->value['label'];?>
</div></a>
    <?php }?>
<?php } ?>
<?php }?>

<?php }} ?>
