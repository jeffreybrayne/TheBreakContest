<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:09
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/4da66c33dad68f1426ad2551c76d3464.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6670905254d6760d707bc9-03408552%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6d76f0b8820768d98127c5cd3dd1b56112254ebe' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/4da66c33dad68f1426ad2551c76d3464.tpl',
      1 => 1423341069,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6670905254d6760d707bc9-03408552',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'info' => 0,
    'jamroom_url' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760d773881_70880359',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760d773881_70880359')) {function content_54d6760d773881_70880359($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"murl"),$_smarty_tpl); } ?>

    <div class="container">
        <?php if ($_smarty_tpl->tpl_vars['info']->value['total_pages']>1) {?>
            <div class="row">
                <div class="col12 last">
                    <div class="page mb10">
                        <?php if ($_smarty_tpl->tpl_vars['info']->value['prev_page']>0) {?>
                            <div class="float-left">
                                <a onclick="jrLoad('#featured_artists','<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/index_featured_list/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
');"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"arrow-left"),$_smarty_tpl); } ?>
</a>
                            </div>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['info']->value['next_page']>1) {?>
                            <div class="float-right">
                                <a onclick="jrLoad('#featured_artists','<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/index_featured_list/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
');"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"arrow-right"),$_smarty_tpl); } ?>
</a>
                            </div>
                        <?php }?>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        <?php }?>
        <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
            <div class="row">
                <div class="col8">
                    <div class="p5">
                        <h2><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</a></h2><br>
                        <br>
                        <?php echo nl2br(smarty_modifier_jrCore_format_string(smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_bio'],220,"...",false),$_smarty_tpl->tpl_vars['item']->value['profile_quota_id']));?>
<br>
                        <br>
                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrAudio",'order_by'=>"_created desc",'limit'=>"1",'search1'=>"_profile_id = ".((string)$_smarty_tpl->tpl_vars['item']->value['_profile_id']),'template'=>"index_featured_song.tpl"),$_smarty_tpl); } ?>

                    </div>
                </div>
                <div class="col4 last">
                    <div class="featured_img">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"xxlarge",'crop'=>"height",'height'=>"250",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'title'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'class'=>"iloutline img_shadow img_scale",'style'=>"max-height:250px;"),$_smarty_tpl); } ?>
</a><br>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>
<?php }?>

<?php }} ?>
