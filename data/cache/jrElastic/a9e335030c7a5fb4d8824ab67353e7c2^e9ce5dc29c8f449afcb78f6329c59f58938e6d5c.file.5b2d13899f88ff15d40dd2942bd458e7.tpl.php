<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:07
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/5b2d13899f88ff15d40dd2942bd458e7.tpl" */ ?>
<?php /*%%SmartyHeaderCode:201500901954d673ef0174b9-84697514%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e9ce5dc29c8f449afcb78f6329c59f58938e6d5c' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/5b2d13899f88ff15d40dd2942bd458e7.tpl',
      1 => 1423340527,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '201500901954d673ef0174b9-84697514',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tabs' => 0,
    'tab' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673ef0ba386_62056235',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673ef0ba386_62056235')) {function content_54d673ef0ba386_62056235($_smarty_tpl) {?><tr>
    <td colspan="2" class="page_tab_bar_holder">
        <ul class="page_tab_bar">
            <?php  $_smarty_tpl->tpl_vars["tab"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["tab"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["tab"]->key => $_smarty_tpl->tpl_vars["tab"]->value) {
$_smarty_tpl->tpl_vars["tab"]->_loop = true;
?>
                <?php if (isset($_smarty_tpl->tpl_vars['tab']->value['onclick'])) {?>
                    <?php if (isset($_smarty_tpl->tpl_vars['tab']->value['active'])&&$_smarty_tpl->tpl_vars['tab']->value['active']=='1') {?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
 page_tab_active" onclick="<?php echo $_smarty_tpl->tpl_vars['tab']->value['onclick'];?>
"><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</li>
                    <?php } else { ?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
" onclick="<?php echo $_smarty_tpl->tpl_vars['tab']->value['onclick'];?>
"><a href=""><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</a></li>
                    <?php }?>
                <?php } else { ?>
                    <?php if (isset($_smarty_tpl->tpl_vars['tab']->value['active'])&&$_smarty_tpl->tpl_vars['tab']->value['active']=='1') {?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
 page_tab_active"><a href="<?php echo $_smarty_tpl->tpl_vars['tab']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</a>
                        </li>
                    <?php } else { ?>
                        <li id="<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['tab']->value['class'];?>
"><a href="<?php echo $_smarty_tpl->tpl_vars['tab']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['tab']->value['label'];?>
</a></li>
                    <?php }?>
                <?php }?>
            <?php } ?>
        </ul>
    </td>
</tr>
<tr>
    <td colspan="2" class="page_tab_bar_spacer"></td>
</tr>
<?php }} ?>
