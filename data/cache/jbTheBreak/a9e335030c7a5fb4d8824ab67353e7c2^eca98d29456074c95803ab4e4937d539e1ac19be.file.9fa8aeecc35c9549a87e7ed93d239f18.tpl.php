<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/9fa8aeecc35c9549a87e7ed93d239f18.tpl" */ ?>
<?php /*%%SmartyHeaderCode:148563370854d67641010576-71093596%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eca98d29456074c95803ab4e4937d539e1ac19be' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/9fa8aeecc35c9549a87e7ed93d239f18.tpl',
      1 => 1423341121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '148563370854d67641010576-71093596',
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
  'unifunc' => 'content_54d676410f6da1_86054716',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d676410f6da1_86054716')) {function content_54d676410f6da1_86054716($_smarty_tpl) {?><tr>
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
