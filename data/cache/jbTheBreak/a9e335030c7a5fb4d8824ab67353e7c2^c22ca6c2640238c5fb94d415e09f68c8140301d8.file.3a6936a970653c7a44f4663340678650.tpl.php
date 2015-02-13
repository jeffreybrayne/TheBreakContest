<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/3a6936a970653c7a44f4663340678650.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2903883854d6764116e1f8-21973320%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c22ca6c2640238c5fb94d415e09f68c8140301d8' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/3a6936a970653c7a44f4663340678650.tpl',
      1 => 1423341121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2903883854d6764116e1f8-21973320',
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
  'unifunc' => 'content_54d676411c49f4_93798343',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d676411c49f4_93798343')) {function content_54d676411c49f4_93798343($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['label']->value)&&strlen($_smarty_tpl->tpl_vars['label']->value)>0) {?>
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
