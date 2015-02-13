<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/778f4c035cde0f6ba3970f5d83476961.tpl" */ ?>
<?php /*%%SmartyHeaderCode:73120121654d676418ab554-56645853%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7bc863163bd64cdbecc41a20c44d609f5ee706bb' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/778f4c035cde0f6ba3970f5d83476961.tpl',
      1 => 1423341121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '73120121654d676418ab554-56645853',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page_title' => 0,
    '_conf' => 0,
    'meta' => 0,
    'mname' => 0,
    'mvalue' => 0,
    'css_href' => 0,
    '_css' => 0,
    'css_embed' => 0,
    'javascript_embed' => 0,
    'javascript_href' => 0,
    '_js' => 0,
    'javascript_ready_function' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d67641927030_94044576',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d67641927030_94044576')) {function content_54d67641927030_94044576($_smarty_tpl) {?><!doctype html>
<html lang="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"_settings",'id'=>"lang",'default'=>"en"),$_smarty_tpl); } ?>
" dir="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"_settings",'id'=>"direction",'default'=>"ltr"),$_smarty_tpl); } ?>
">
<head>
<title><?php if (isset($_smarty_tpl->tpl_vars['page_title']->value)&&strlen($_smarty_tpl->tpl_vars['page_title']->value)>0) {?><?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
<?php } else { ?><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"1",'default'=>"Home"),$_smarty_tpl); } ?>
<?php }?> | <?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php if (isset($_smarty_tpl->tpl_vars['meta']->value)) {?>
<?php  $_smarty_tpl->tpl_vars["mvalue"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["mvalue"]->_loop = false;
 $_smarty_tpl->tpl_vars["mname"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['meta']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["mvalue"]->key => $_smarty_tpl->tpl_vars["mvalue"]->value) {
$_smarty_tpl->tpl_vars["mvalue"]->_loop = true;
 $_smarty_tpl->tpl_vars["mname"]->value = $_smarty_tpl->tpl_vars["mvalue"]->key;
?>
<meta name="<?php echo $_smarty_tpl->tpl_vars['mname']->value;?>
" content="<?php echo $_smarty_tpl->tpl_vars['mvalue']->value;?>
" />
<?php } ?>
<?php }?>
<link rel="stylesheet" href="<?php if (function_exists('smarty_function_jrCore_css_src')) { echo smarty_function_jrCore_css_src(array(),$_smarty_tpl); } ?>
" media="screen" />
<?php if (isset($_smarty_tpl->tpl_vars['css_href']->value)) {?>
<?php  $_smarty_tpl->tpl_vars["_css"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_css"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['css_href']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_css"]->key => $_smarty_tpl->tpl_vars["_css"]->value) {
$_smarty_tpl->tpl_vars["_css"]->_loop = true;
?>
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['_css']->value['source'];?>
" media="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_css']->value['media'])===null||strlen($tmp)===0 ? "screen" : $tmp);?>
" />
<?php } ?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['css_embed']->value)) {?>
<style type="text/css">
<?php echo $_smarty_tpl->tpl_vars['css_embed']->value;?>
</style>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['javascript_embed']->value)) {?>
<script type="text/javascript">
<?php echo $_smarty_tpl->tpl_vars['javascript_embed']->value;?>
</script>
<?php }?>
<script type="text/javascript" src="<?php if (function_exists('smarty_function_jrCore_javascript_src')) { echo smarty_function_jrCore_javascript_src(array(),$_smarty_tpl); } ?>
"></script>
<?php if (isset($_smarty_tpl->tpl_vars['javascript_href']->value)) {?>
<?php  $_smarty_tpl->tpl_vars["_js"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_js"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['javascript_href']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_js"]->key => $_smarty_tpl->tpl_vars["_js"]->value) {
$_smarty_tpl->tpl_vars["_js"]->_loop = true;
?>
<script type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_js']->value['type'])===null||strlen($tmp)===0 ? "text/javascript" : $tmp);?>
" src="<?php echo $_smarty_tpl->tpl_vars['_js']->value['source'];?>
"></script>
<?php } ?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['javascript_ready_function']->value)) {?>
<script type="text/javascript">
$(document).ready(function(){
<?php echo $_smarty_tpl->tpl_vars['javascript_ready_function']->value;?>
return true;
 });
</script>
<?php }?>
</head>
<?php }} ?>
