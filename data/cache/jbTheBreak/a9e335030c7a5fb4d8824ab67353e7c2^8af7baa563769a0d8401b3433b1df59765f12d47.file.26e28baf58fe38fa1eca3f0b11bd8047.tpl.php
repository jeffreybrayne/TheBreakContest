<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/26e28baf58fe38fa1eca3f0b11bd8047.tpl" */ ?>
<?php /*%%SmartyHeaderCode:206070166154d676410ffc49-04160622%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8af7baa563769a0d8401b3433b1df59765f12d47' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/26e28baf58fe38fa1eca3f0b11bd8047.tpl',
      1 => 1423341121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '206070166154d676410ffc49-04160622',
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
  'unifunc' => 'content_54d676411655d9_80131230',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d676411655d9_80131230')) {function content_54d676411655d9_80131230($_smarty_tpl) {?><tr>
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
