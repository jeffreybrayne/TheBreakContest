<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:11
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/a26757e328ba7e79a919e4fc5d02d157.tpl" */ ?>
<?php /*%%SmartyHeaderCode:44524700154d6760f5a4155-37159602%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f963d048b6c37d0c1c71e90eb68df37f62512ca' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/a26757e328ba7e79a919e4fc5d02d157.tpl',
      1 => 1423341071,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '44524700154d6760f5a4155-37159602',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760f5e9875_81671870',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760f5e9875_81671870')) {function content_54d6760f5e9875_81671870($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images']=='on') {?>
    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'limit'=>"10",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_jrAudio_item_count > 0",'template'=>"index_top_artists_row.tpl",'require_image'=>"profile_image"),$_smarty_tpl); } ?>

<?php } else { ?>
    <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_view_count numerical_desc",'limit'=>"10",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_jrAudio_item_count > 0",'template'=>"index_top_artists_row.tpl"),$_smarty_tpl); } ?>

<?php }?>
<?php }} ?>
