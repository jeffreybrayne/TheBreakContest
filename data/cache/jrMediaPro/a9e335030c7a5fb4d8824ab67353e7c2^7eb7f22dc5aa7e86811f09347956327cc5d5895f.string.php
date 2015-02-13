<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:10
         compiled from "7eb7f22dc5aa7e86811f09347956327cc5d5895f" */ ?>
<?php /*%%SmartyHeaderCode:74269459854d6760e0b0c55-63141621%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7eb7f22dc5aa7e86811f09347956327cc5d5895f' => 
    array (
      0 => '7eb7f22dc5aa7e86811f09347956327cc5d5895f',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '74269459854d6760e0b0c55-63141621',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_stats' => 0,
    'title' => 0,
    '_stat' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760e0c6454_14809907',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760e0c6454_14809907')) {function content_54d6760e0c6454_14809907($_smarty_tpl) {?>            
                <?php  $_smarty_tpl->tpl_vars['_stat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_stat']->_loop = false;
 $_smarty_tpl->tpl_vars['title'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_stats']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_stat']->key => $_smarty_tpl->tpl_vars['_stat']->value) {
$_smarty_tpl->tpl_vars['_stat']->_loop = true;
 $_smarty_tpl->tpl_vars['title']->value = $_smarty_tpl->tpl_vars['_stat']->key;
?>
                <div style="display:table-row">
                    <div class="capital bold" style="display:table-cell"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</div>
                    <div class="hl-3" style="width:5%;display:table-cell;text-align:right;"><?php echo $_smarty_tpl->tpl_vars['_stat']->value['count'];?>
</div>
                </div>
                <?php } ?>
            
        <?php }} ?>
