<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:02
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/dc1b4e3a4f278f9879a028f69b480248.tpl" */ ?>
<?php /*%%SmartyHeaderCode:126856483154d673eabd7a15-22608730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f036da2bf69c7acccd13b7b3f4311fb4f4c944d8' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/dc1b4e3a4f278f9879a028f69b480248.tpl',
      1 => 1423340522,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '126856483154d673eabd7a15-22608730',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'item' => 0,
    'jamroom_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673eac42829_19293438',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673eac42829_19293438')) {function content_54d673eac42829_19293438($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)&&is_array($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <div class="online_status_table">
    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>

        <?php if ($_smarty_tpl->tpl_vars['item']->value['user_is_online']=='1') {?>
        <div class="online_status_online" style="display:table-row">
        <?php } else { ?>
        <div class="online_status_offline" style="display:table-row">
        <?php }?>

            <div class="online_status_image">
                <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"small",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"img_shadow",'width'=>"40",'height'=>"40",'style'=>"padding:2px"),$_smarty_tpl); } ?>

            </div>

            <div class="online_status_user">
                <h2><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['user_name'];?>
</a></h2><br>
                <?php if ($_smarty_tpl->tpl_vars['item']->value['user_is_online']=='1') {?>
                <i><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrUser",'id'=>"101",'default'=>"online"),$_smarty_tpl); } ?>
</i>
                <?php } else { ?>
                <i><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrUser",'id'=>"102",'default'=>"offline"),$_smarty_tpl); } ?>
</i>
                <?php }?>
            </div>

        </div>

    <?php } ?>
    </div>
<?php }?><?php }} ?>
