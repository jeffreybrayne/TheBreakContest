<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/004562e9653ca4ce9a8e95486cb599a0.tpl" */ ?>
<?php /*%%SmartyHeaderCode:138460302554d673e983cee6-83750574%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a8f2a9f156dbdb74f797b13e203b92ac46d236bd' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/004562e9653ca4ce9a8e95486cb599a0.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '138460302554d673e983cee6-83750574',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'info' => 0,
    'pager_load_id' => 0,
    'pager_load_url' => 0,
    'pages' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e990bda2_02592480',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e990bda2_02592480')) {function content_54d673e990bda2_02592480($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['info']->value['prev_page']>0||$_smarty_tpl->tpl_vars['info']->value['next_page']>0) {?>
<div class="block">
    <table style="width:100%">
        <tr>
            <td style="width:25%">
            <?php if ($_smarty_tpl->tpl_vars['info']->value['prev_page']>0) {?>
                <?php if (isset($_smarty_tpl->tpl_vars['pager_load_id']->value)) {?>
                    <a onclick="jrCore_load_into('<?php echo $_smarty_tpl->tpl_vars['pager_load_id']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['pager_load_url']->value;?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
')"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"previous"),$_smarty_tpl); } ?>
</a>
                <?php } else { ?>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"previous"),$_smarty_tpl); } ?>
</a>
                <?php }?>
            <?php }?>
            </td>
            <td style="width:50%;text-align:center">
                <?php if ($_smarty_tpl->tpl_vars['info']->value['total_pages']<=3) {?>
                    <?php echo $_smarty_tpl->tpl_vars['info']->value['page'];?>
 &nbsp;/ <?php echo $_smarty_tpl->tpl_vars['info']->value['total_pages'];?>

                <?php } else { ?>
                    <form name="form" method="post" action="_self">
                    <?php if (isset($_smarty_tpl->tpl_vars['pager_load_id']->value)) {?>
                        <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrCore_load_into('<?php echo $_smarty_tpl->tpl_vars['pager_load_id']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['pager_load_url']->value;?>
/p=' + $(this).val());">
                    <?php } else { ?>
                        <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="window.location='<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=' + $(this).val();">
                    <?php }?>
                    <?php $_smarty_tpl->tpl_vars['pages'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['pages']->step = 1;$_smarty_tpl->tpl_vars['pages']->total = (int) ceil(($_smarty_tpl->tpl_vars['pages']->step > 0 ? $_smarty_tpl->tpl_vars['info']->value['total_pages']+1 - (1) : 1-($_smarty_tpl->tpl_vars['info']->value['total_pages'])+1)/abs($_smarty_tpl->tpl_vars['pages']->step));
if ($_smarty_tpl->tpl_vars['pages']->total > 0) {
for ($_smarty_tpl->tpl_vars['pages']->value = 1, $_smarty_tpl->tpl_vars['pages']->iteration = 1;$_smarty_tpl->tpl_vars['pages']->iteration <= $_smarty_tpl->tpl_vars['pages']->total;$_smarty_tpl->tpl_vars['pages']->value += $_smarty_tpl->tpl_vars['pages']->step, $_smarty_tpl->tpl_vars['pages']->iteration++) {
$_smarty_tpl->tpl_vars['pages']->first = $_smarty_tpl->tpl_vars['pages']->iteration == 1;$_smarty_tpl->tpl_vars['pages']->last = $_smarty_tpl->tpl_vars['pages']->iteration == $_smarty_tpl->tpl_vars['pages']->total;?>
                        <?php if ($_smarty_tpl->tpl_vars['info']->value['page']==$_smarty_tpl->tpl_vars['pages']->value) {?>
                            <option value="<?php echo $_smarty_tpl->tpl_vars['info']->value['this_page'];?>
" selected="selected"> <?php echo $_smarty_tpl->tpl_vars['info']->value['this_page'];?>
</option>
                        <?php } else { ?>
                            <option value="<?php echo $_smarty_tpl->tpl_vars['pages']->value;?>
"> <?php echo $_smarty_tpl->tpl_vars['pages']->value;?>
</option>
                        <?php }?>
                    <?php }} ?>
                        </select>&nbsp;/&nbsp;<?php echo $_smarty_tpl->tpl_vars['info']->value['total_pages'];?>

                    </form>
                <?php }?>
            </td>
            <td style="width:25%;text-align:right">
            <?php if ($_smarty_tpl->tpl_vars['info']->value['next_page']>0) {?>
                <?php if (isset($_smarty_tpl->tpl_vars['pager_load_id']->value)) {?>
                    <a onclick="jrCore_load_into('<?php echo $_smarty_tpl->tpl_vars['pager_load_id']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['pager_load_url']->value;?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
')"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"next"),$_smarty_tpl); } ?>
</a>
                <?php } else { ?>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"next"),$_smarty_tpl); } ?>
</a>
                <?php }?>
            <?php }?>
            </td>
        </tr>
    </table>
</div>
<?php }?><?php }} ?>
