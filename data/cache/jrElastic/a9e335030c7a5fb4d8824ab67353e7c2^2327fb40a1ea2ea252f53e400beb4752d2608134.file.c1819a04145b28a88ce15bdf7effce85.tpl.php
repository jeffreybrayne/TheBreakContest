<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:21:47
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/c1819a04145b28a88ce15bdf7effce85.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7035765154d673dbede889-46232990%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2327fb40a1ea2ea252f53e400beb4752d2608134' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/c1819a04145b28a88ce15bdf7effce85.tpl',
      1 => 1423340507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7035765154d673dbede889-46232990',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'jamroom_url' => 0,
    'url' => 0,
    '_conf' => 0,
    'html' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673dbef4c78_98212534',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673dbef4c78_98212534')) {function content_54d673dbef4c78_98212534($_smarty_tpl) {?>
<tr>
  <td colspan="2" class="form_submit_box">
    <div class="form_submit_section">
        <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"url"),$_smarty_tpl); } ?>

        <img id="form_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
/img/skin/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/submit.gif" width="24" height="24" alt="<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl); } ?>
"><?php echo $_smarty_tpl->tpl_vars['html']->value;?>

    </div>
  </td>
</tr>
<?php }} ?>
