<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:07
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/46fcd7251f0306dabbc6f10374adc785.tpl" */ ?>
<?php /*%%SmartyHeaderCode:113528328654d673efbfb134-43285291%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8571dffd90d52a76fbe9c339eba2a234aa35ecd1' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/46fcd7251f0306dabbc6f10374adc785.tpl',
      1 => 1423340527,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '113528328654d673efbfb134-43285291',
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
  'unifunc' => 'content_54d673efc3ef74_42504836',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673efc3ef74_42504836')) {function content_54d673efc3ef74_42504836($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['label']->value)&&strlen($_smarty_tpl->tpl_vars['label']->value)>0) {?>
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
