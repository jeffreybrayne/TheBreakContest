<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/2708593697309b00e09d61be2b2d9aed.tpl" */ ?>
<?php /*%%SmartyHeaderCode:121560960954d673e99e8bc1-42116962%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '29acefd685e9243e5563edbb11d5d8a0e8eb35a5' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/2708593697309b00e09d61be2b2d9aed.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '121560960954d673e99e8bc1-42116962',
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
  'unifunc' => 'content_54d673e9a28472_86976106',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9a28472_86976106')) {function content_54d673e9a28472_86976106($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars["entry"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["entry"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["entry"]->key => $_smarty_tpl->tpl_vars["entry"]->value) {
$_smarty_tpl->tpl_vars["entry"]->_loop = true;
?>
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
