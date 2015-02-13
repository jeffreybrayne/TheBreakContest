<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:26:36
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/5a1e4e0cac53e8ceeecbc2464562d68c.tpl" */ ?>
<?php /*%%SmartyHeaderCode:23449776254d674fc54a5b9-44300007%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '23cf0d62cbae0b539c54fc387c43a5279c554378' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/5a1e4e0cac53e8ceeecbc2464562d68c.tpl',
      1 => 1423340796,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '23449776254d674fc54a5b9-44300007',
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
  'unifunc' => 'content_54d674fc560b03_28094447',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674fc560b03_28094447')) {function content_54d674fc560b03_28094447($_smarty_tpl) {?>
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
