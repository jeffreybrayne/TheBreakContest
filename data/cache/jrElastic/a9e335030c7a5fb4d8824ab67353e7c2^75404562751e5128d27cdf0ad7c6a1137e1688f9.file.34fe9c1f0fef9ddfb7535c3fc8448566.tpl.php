<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:07
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/34fe9c1f0fef9ddfb7535c3fc8448566.tpl" */ ?>
<?php /*%%SmartyHeaderCode:51971472454d673ef0c2ba4-55215704%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '75404562751e5128d27cdf0ad7c6a1137e1688f9' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/34fe9c1f0fef9ddfb7535c3fc8448566.tpl',
      1 => 1423340527,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '51971472454d673ef0c2ba4-55215704',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'onclick' => 0,
    'label' => 0,
    'label_url' => 0,
    'target' => 0,
    'description' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673ef10c729_47369200',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673ef10c729_47369200')) {function content_54d673ef10c729_47369200($_smarty_tpl) {?><tr>

  <?php if (isset($_smarty_tpl->tpl_vars['onclick']->value)&&strlen($_smarty_tpl->tpl_vars['onclick']->value)>0) {?>
      <td class="element_left tool_element_left"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['label']->value;?>
" class="form_button" style="width:100%;" onclick="<?php echo $_smarty_tpl->tpl_vars['onclick']->value;?>
"></td>
  <?php } elseif (strlen($_smarty_tpl->tpl_vars['label_url']->value)>0) {?>
      <?php if (isset($_smarty_tpl->tpl_vars['target']->value)&&$_smarty_tpl->tpl_vars['target']->value=="_self") {?>
          <td class="element_left tool_element_left"><span class="form_button_anchor"><a href="<?php echo $_smarty_tpl->tpl_vars['label_url']->value;?>
"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['label']->value;?>
" class="form_button" style="width:100%;"></a></span></td>
      <?php } else { ?>
          <td class="element_left tool_element_left"><span class="form_button_anchor"><a href="<?php echo $_smarty_tpl->tpl_vars['label_url']->value;?>
" target="<?php echo $_smarty_tpl->tpl_vars['target']->value;?>
"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['label']->value;?>
" class="form_button" style="width:100%;"></a></span></td>
      <?php }?>
  <?php } else { ?>
      <td class="element_left tool_element_left"><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</td>
  <?php }?>

  <td class="element_right tool_element_right"><?php echo $_smarty_tpl->tpl_vars['description']->value;?>
</td>
</tr>
<?php }} ?>
