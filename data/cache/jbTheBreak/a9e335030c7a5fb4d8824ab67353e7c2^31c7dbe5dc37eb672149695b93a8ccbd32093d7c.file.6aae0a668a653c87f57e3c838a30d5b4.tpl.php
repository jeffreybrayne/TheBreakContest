<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/6aae0a668a653c87f57e3c838a30d5b4.tpl" */ ?>
<?php /*%%SmartyHeaderCode:126882736054d67641406d25-23019775%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '31c7dbe5dc37eb672149695b93a8ccbd32093d7c' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/6aae0a668a653c87f57e3c838a30d5b4.tpl',
      1 => 1423341121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '126882736054d67641406d25-23019775',
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
  'unifunc' => 'content_54d67641438fd6_01737579',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d67641438fd6_01737579')) {function content_54d67641438fd6_01737579($_smarty_tpl) {?>
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
