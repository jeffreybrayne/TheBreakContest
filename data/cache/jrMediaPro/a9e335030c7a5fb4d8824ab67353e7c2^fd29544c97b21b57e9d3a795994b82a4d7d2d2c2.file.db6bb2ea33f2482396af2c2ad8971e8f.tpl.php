<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:26:36
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/db6bb2ea33f2482396af2c2ad8971e8f.tpl" */ ?>
<?php /*%%SmartyHeaderCode:208140977254d674fc4237c5-07027381%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fd29544c97b21b57e9d3a795994b82a4d7d2d2c2' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/db6bb2ea33f2482396af2c2ad8971e8f.tpl',
      1 => 1423340796,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '208140977254d674fc4237c5-07027381',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'label' => 0,
    'type' => 0,
    'sublabel' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d674fc44ba60_58620175',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674fc44ba60_58620175')) {function content_54d674fc44ba60_58620175($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['label']->value)&&strlen($_smarty_tpl->tpl_vars['label']->value)>0) {?>
  <tr>
    <td class="element_left form_input_left <?php echo $_smarty_tpl->tpl_vars['type']->value;?>
_left">
      <?php echo $_smarty_tpl->tpl_vars['label']->value;?>
<?php if (isset($_smarty_tpl->tpl_vars['sublabel']->value)&&strlen($_smarty_tpl->tpl_vars['sublabel']->value)>0) {?><br><span class="sublabel"><?php echo $_smarty_tpl->tpl_vars['sublabel']->value;?>
</span><?php }?>
    </td>
    <td class="element_right form_input_right <?php echo $_smarty_tpl->tpl_vars['type']->value;?>
_right"><?php echo $_smarty_tpl->tpl_vars['html']->value;?>
</td>
  </tr>
<?php } else { ?>
  <tr>
    <td colspan="2" class="element page_custom"><?php echo $_smarty_tpl->tpl_vars['html']->value;?>
</td>
  </tr>
<?php }?>
<?php }} ?>
