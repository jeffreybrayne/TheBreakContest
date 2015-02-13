<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:10
         compiled from "5aa43f2b251afbb23ac778f164845c1ab1452583" */ ?>
<?php /*%%SmartyHeaderCode:150180855354d6760e0d3f15-86331364%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5aa43f2b251afbb23ac778f164845c1ab1452583' => 
    array (
      0 => '5aa43f2b251afbb23ac778f164845c1ab1452583',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '150180855354d6760e0d3f15-86331364',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'jamroom_url' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760e159d91_64559541',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760e159d91_64559541')) {function content_54d6760e159d91_64559541($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?>        
            <?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
            <?php  $_smarty_tpl->tpl_vars["row"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["row"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["row"]->key => $_smarty_tpl->tpl_vars["row"]->value) {
$_smarty_tpl->tpl_vars["row"]->_loop = true;
?>
            <div class="center p5">
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['row']->value['profile_url'];?>
"><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['row']->value['_profile_id'],'size'=>"medium",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['row']->value['profile_name'],'title'=>$_smarty_tpl->tpl_vars['row']->value['profile_name'],'class'=>"iloutline img_shadow"),$_smarty_tpl); } ?>
</a><br>
                <div class="spacer10"></div>
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['row']->value['profile_url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['row']->value['profile_name'];?>
"><span class="capital bold"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['row']->value['profile_name'],20,"...",false);?>
</span></a><br>
                <div class="spacer10"></div>
                <div align="right"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/members" title="View More"><div class="button-more">&nbsp;</div></a></div>
            </div>
            <?php } ?>
            <?php }?>
        
    <?php }} ?>
