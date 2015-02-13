<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:26:36
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/9fd42d01dab37bf36408ee8862e35273.tpl" */ ?>
<?php /*%%SmartyHeaderCode:212991628254d674fc3f2546-65976228%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9f818580a110484348a54ac099d573ddcb5f1c66' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/9fd42d01dab37bf36408ee8862e35273.tpl',
      1 => 1423340796,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '212991628254d674fc3f2546-65976228',
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
  'unifunc' => 'content_54d674fc41f6b3_09404292',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674fc41f6b3_09404292')) {function content_54d674fc41f6b3_09404292($_smarty_tpl) {?><tr>
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
