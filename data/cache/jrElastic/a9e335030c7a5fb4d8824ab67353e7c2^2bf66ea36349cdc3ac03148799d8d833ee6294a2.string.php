<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "2bf66ea36349cdc3ac03148799d8d833ee6294a2" */ ?>
<?php /*%%SmartyHeaderCode:141108216054d673e9c9eb86-98441486%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2bf66ea36349cdc3ac03148799d8d833ee6294a2' => 
    array (
      0 => '2bf66ea36349cdc3ac03148799d8d833ee6294a2',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '141108216054d673e9c9eb86-98441486',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_stats' => 0,
    '_stat' => 0,
    'jamroom_url' => 0,
    'profile_url' => 0,
    'murl' => 0,
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e9cbb5d0_59087002',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9cbb5d0_59087002')) {function content_54d673e9cbb5d0_59087002($_smarty_tpl) {?>                    
                        <?php  $_smarty_tpl->tpl_vars['_stat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['_stat']->_loop = false;
 $_smarty_tpl->tpl_vars['title'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_stats']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['_stat']->key => $_smarty_tpl->tpl_vars['_stat']->value) {
$_smarty_tpl->tpl_vars['_stat']->_loop = true;
 $_smarty_tpl->tpl_vars['title']->value = $_smarty_tpl->tpl_vars['_stat']->key;
?>
                        <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>$_smarty_tpl->tpl_vars['_stat']->value['module'],'assign'=>"murl"),$_smarty_tpl); } ?>

                        <div class="stat_entry_box">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
"><span class="stat_entry_title"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
:</span> <span class="stat_entry_count"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['_stat']->value['count'])===null||strlen($tmp)===0 ? 0 : $tmp);?>
</span></a>
                        </div>
                        <?php } ?>
                    
                    <?php }} ?>
