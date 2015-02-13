<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/583f070f8ad24ad2d4a95bfe28466ba4.tpl" */ ?>
<?php /*%%SmartyHeaderCode:128415328454d673e9c421a4-13549523%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b36120aea91b3b7aa8536e4b12710378d73619d2' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/583f070f8ad24ad2d4a95bfe28466ba4.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '128415328454d673e9c421a4-13549523',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'jamroom_url' => 0,
    'murl' => 0,
    'type' => 0,
    'unique_id' => 0,
    'seconds' => 0,
    'template' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e9c5def3_12334329',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9c5def3_12334329')) {function content_54d673e9c5def3_12334329($_smarty_tpl) {?><?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"murl"),$_smarty_tpl); } ?>

<script type="text/javascript">
$(document).ready(function(){
    $.get('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/online_status/<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['seconds']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['template']->value;?>
/__ajax=1', function(res) { $('#<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
').html(res); });
});
</script>
<div id="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"></div><?php }} ?>
