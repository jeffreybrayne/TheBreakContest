<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:27:08
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/2fbce36b820ad522d37d9bbb2ab75dc2.tpl" */ ?>
<?php /*%%SmartyHeaderCode:103883746954d6751ce40e35-41907959%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ba8ff460af41e1b1c0ffc82a10d0d73cdaf03bb9' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/2fbce36b820ad522d37d9bbb2ab75dc2.tpl',
      1 => 1423340828,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '103883746954d6751ce40e35-41907959',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'notice_type' => 0,
    'notice_text' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6751ce6da79_38082851',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6751ce6da79_38082851')) {function content_54d6751ce6da79_38082851($_smarty_tpl) {?>


<tr>
  <td colspan="2" class="page_notice_drop"><div id="page_notice" class="page_notice <?php echo $_smarty_tpl->tpl_vars['notice_type']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['notice_text']->value;?>
</div></td>
</tr>
<?php }} ?>
