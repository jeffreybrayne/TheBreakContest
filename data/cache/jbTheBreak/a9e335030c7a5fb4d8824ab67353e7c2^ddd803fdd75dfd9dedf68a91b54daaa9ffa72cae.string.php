<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:02
         compiled from "ddd803fdd75dfd9dedf68a91b54daaa9ffa72cae" */ ?>
<?php /*%%SmartyHeaderCode:214397142454d67642dff561-40462449%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ddd803fdd75dfd9dedf68a91b54daaa9ffa72cae' => 
    array (
      0 => 'ddd803fdd75dfd9dedf68a91b54daaa9ffa72cae',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '214397142454d67642dff561-40462449',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'item' => 0,
    '_conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d67642e18918_95956485',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d67642e18918_95956485')) {function content_54d67642e18918_95956485($_smarty_tpl) {?>                        
                            <?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
                                <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
                                    |&nbsp;<a href="mailto:<?php echo $_smarty_tpl->tpl_vars['item']->value['user_email'];?>
?subject=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
 Contact"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"81",'default'=>"Contact Us"),$_smarty_tpl); } ?>
</a>
                                <?php } ?>
                            <?php }?>
                        
                        <?php }} ?>
