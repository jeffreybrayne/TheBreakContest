<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:32
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/80ddb56f0a35518b4cca219d505002be.tpl" */ ?>
<?php /*%%SmartyHeaderCode:109420068954d67624830ff5-70348409%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98903b90959112b0b45dcf423fcd1174d4394762' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/80ddb56f0a35518b4cca219d505002be.tpl',
      1 => 1423341092,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '109420068954d67624830ff5-70348409',
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
  'unifunc' => 'content_54d6762488ff85_52959325',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6762488ff85_52959325')) {function content_54d6762488ff85_52959325($_smarty_tpl) {?><tr>

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
