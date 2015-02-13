<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:02
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/2372a409442b5af4be207f65af01d7e0.tpl" */ ?>
<?php /*%%SmartyHeaderCode:140213061254d67642ca3e92-53713672%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8657602cf4b5d5be82bacd595c2eba5da3abcedd' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/2372a409442b5af4be207f65af01d7e0.tpl',
      1 => 1423341122,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '140213061254d67642ca3e92-53713672',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'form_name' => 0,
    'jamroom_url' => 0,
    'jrSearch' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d67642cd1e99_07033991',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d67642cd1e99_07033991')) {function content_54d67642cd1e99_07033991($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars["form_name"] = new Smarty_variable("jrSearch", null, 0);?>
<div style="white-space:nowrap">
    <form name="<?php echo $_smarty_tpl->tpl_vars['form_name']->value;?>
" action="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/search/results/<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['module'];?>
/<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['page'];?>
/<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['pagebreak'];?>
" method="<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['method'];?>
" style="margin-bottom:0">
    <input id="search_input" type="text" name="search_string" value="<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['value'];?>
" style="<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['style'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['class'];?>
" onfocus="if(this.value=='<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['value'];?>
'){ this.value=''; }" onblur="if(this.value==''){ this.value='<?php echo $_smarty_tpl->tpl_vars['jrSearch']->value['value'];?>
'; }">&nbsp;<input type="submit" class="form_button" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['jrSearch']->value['submit_value'])===null||strlen($tmp)===0 ? "search" : $tmp);?>
">
    </form>
</div>
<?php }} ?>
