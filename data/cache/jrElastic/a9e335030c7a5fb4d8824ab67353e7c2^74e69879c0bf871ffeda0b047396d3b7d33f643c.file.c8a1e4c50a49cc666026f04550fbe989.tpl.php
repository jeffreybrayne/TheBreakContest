<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:25:15
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/c8a1e4c50a49cc666026f04550fbe989.tpl" */ ?>
<?php /*%%SmartyHeaderCode:56232795854d674ab41fcf2-22565979%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '74e69879c0bf871ffeda0b047396d3b7d33f643c' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/c8a1e4c50a49cc666026f04550fbe989.tpl',
      1 => 1423340715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '56232795854d674ab41fcf2-22565979',
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
  'unifunc' => 'content_54d674ab4342d1_24370259',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674ab4342d1_24370259')) {function content_54d674ab4342d1_24370259($_smarty_tpl) {?>


<tr>
  <td colspan="2" class="page_notice_drop"><div id="page_notice" class="page_notice <?php echo $_smarty_tpl->tpl_vars['notice_type']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['notice_text']->value;?>
</div></td>
</tr>
<?php }} ?>
