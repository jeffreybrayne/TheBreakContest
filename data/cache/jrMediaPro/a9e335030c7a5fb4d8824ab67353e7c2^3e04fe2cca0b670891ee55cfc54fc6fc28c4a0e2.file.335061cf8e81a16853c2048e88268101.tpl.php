<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:11
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/335061cf8e81a16853c2048e88268101.tpl" */ ?>
<?php /*%%SmartyHeaderCode:45495202954d6760f77fd07-01995249%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e04fe2cca0b670891ee55cfc54fc6fc28c4a0e2' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/335061cf8e81a16853c2048e88268101.tpl',
      1 => 1423341071,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '45495202954d6760f77fd07-01995249',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    '_post' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760f923359_33181280',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760f923359_33181280')) {function content_54d6760f923359_33181280($_smarty_tpl) {?><?php if (jrCore_module_is_active('jrRating')) {?>
    <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images']=='on') {?>
        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAudio",'order_by'=>"audio_rating_overall_average_count numerical_desc",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'template'=>"index_top_singles_rating_row.tpl",'require_image'=>"audio_image",'pagebreak'=>"6",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

    <?php } else { ?>
        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAudio",'order_by'=>"audio_rating_overall_average_count numerical_desc",'search1'=>"profile_active = 1",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'template'=>"index_top_singles_rating_row.tpl",'pagebreak'=>"6",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

    <?php }?>
<?php } else { ?>
    <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images'])&&$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_require_images']=='on') {?>
        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAudio",'order_by'=>"audio_file_stream_count numerical_desc",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'search1'=>"profile_active = 1",'template'=>"index_top_singles_row.tpl",'require_image'=>"audio_image",'pagebreak'=>"6",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

    <?php } else { ?>
        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAudio",'order_by'=>"audio_file_stream_count numerical_desc",'search1'=>"profile_active = 1",'quota_id'=>$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota'],'template'=>"index_top_singles_row.tpl",'pagebreak'=>"6",'page'=>$_smarty_tpl->tpl_vars['_post']->value['p']),$_smarty_tpl); } ?>

    <?php }?>
<?php }?><?php }} ?>
