<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:21:47
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/ddd5a38b65328dd5c7e238855f6e97b8.tpl" */ ?>
<?php /*%%SmartyHeaderCode:44004805354d673dbdac953-63977236%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a1ec2d8aff260e6545a2cf92ecc9da938b67e47e' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/ddd5a38b65328dd5c7e238855f6e97b8.tpl',
      1 => 1423340507,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '44004805354d673dbdac953-63977236',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'icon_url' => 0,
    '_post' => 0,
    'jamroom_url' => 0,
    'url' => 0,
    'title' => 0,
    'subtitle' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673dbddaa66_50939280',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673dbddaa66_50939280')) {function content_54d673dbddaa66_50939280($_smarty_tpl) {?><tr>
    <td colspan="2" class="page_banner_box">
        <table class="page_banner">
            <tr>
                <?php if (strlen($_smarty_tpl->tpl_vars['icon_url']->value)>0) {?>
                    <?php if (jrUser_is_master()) {?>
                        <?php if (function_exists('smarty_function_jrCore_get_module_index')) { echo smarty_function_jrCore_get_module_index(array('module'=>$_smarty_tpl->tpl_vars['_post']->value['module'],'assign'=>"url"),$_smarty_tpl); } ?>

                        <td class="page_banner_icon"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_post']->value['module_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['icon_url']->value;?>
" alt="icon" height="32" width="32"></a></td>
                    <?php } else { ?>
                        <td class="page_banner_icon"><img src="<?php echo $_smarty_tpl->tpl_vars['icon_url']->value;?>
" alt="icon" height="32" width="32"></td>
                    <?php }?>
                    <td class="page_banner_left"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</td>
                    <td class="page_banner_right" style="width:69%"><?php echo $_smarty_tpl->tpl_vars['subtitle']->value;?>
</td>
                <?php } else { ?>
                    <td class="page_banner_left"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</td>
                    <td class="page_banner_right"><?php echo $_smarty_tpl->tpl_vars['subtitle']->value;?>
</td>
                <?php }?>
            </tr>
        </table>
    </td>
</tr>
<?php }} ?>
