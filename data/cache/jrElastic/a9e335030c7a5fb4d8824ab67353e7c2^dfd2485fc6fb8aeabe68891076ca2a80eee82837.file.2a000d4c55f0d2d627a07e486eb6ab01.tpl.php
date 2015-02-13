<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:21:47
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/2a000d4c55f0d2d627a07e486eb6ab01.tpl" */ ?>
<?php /*%%SmartyHeaderCode:78972268954d673db0aea63-36494843%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dfd2485fc6fb8aeabe68891076ca2a80eee82837' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/2a000d4c55f0d2d627a07e486eb6ab01.tpl',
      1 => 1423340507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '78972268954d673db0aea63-36494843',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    'page_title' => 0,
    'default_title' => 0,
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
  'unifunc' => 'content_54d673db17c141_79717368',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673db17c141_79717368')) {function content_54d673db17c141_79717368($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_capitalize')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.capitalize.php';
?><!doctype html>
<html lang="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"_settings",'id'=>"lang",'default'=>"en"),$_smarty_tpl); } ?>
" dir="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"_settings",'id'=>"direction",'default'=>"ltr"),$_smarty_tpl); } ?>
">
<head><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"1",'assign'=>"default_title"),$_smarty_tpl); } ?>

<title><?php echo smarty_modifier_capitalize((($tmp = @$_smarty_tpl->tpl_vars['page_title']->value)===null||strlen($tmp)===0 ? ((string)$_smarty_tpl->tpl_vars['default_title']->value) : $tmp));?>
 | <?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
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
<link rel="stylesheet" href="<?php if (function_exists('smarty_function_jrCore_server_protocol')) { echo smarty_function_jrCore_server_protocol(array(),$_smarty_tpl); } ?>
://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700" type="text/css">
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
