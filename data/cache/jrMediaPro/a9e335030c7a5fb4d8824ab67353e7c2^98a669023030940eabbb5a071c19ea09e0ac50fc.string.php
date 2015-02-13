<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:11
         compiled from "98a669023030940eabbb5a071c19ea09e0ac50fc" */ ?>
<?php /*%%SmartyHeaderCode:108748401154d6760f768bd0-59426178%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98a669023030940eabbb5a071c19ea09e0ac50fc' => 
    array (
      0 => '98a669023030940eabbb5a071c19ea09e0ac50fc',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '108748401154d6760f768bd0-59426178',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'jamroom_url' => 0,
    'row' => 0,
    'info' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760f8fc7e5_80347692',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760f8fc7e5_80347692')) {function content_54d6760f8fc7e5_80347692($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
<div class="container">
    <?php  $_smarty_tpl->tpl_vars["row"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["row"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars["row"]->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars["row"]->iteration=0;
 $_smarty_tpl->tpl_vars["row"]->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars["row"]->key => $_smarty_tpl->tpl_vars["row"]->value) {
$_smarty_tpl->tpl_vars["row"]->_loop = true;
 $_smarty_tpl->tpl_vars["row"]->iteration++;
 $_smarty_tpl->tpl_vars["row"]->index++;
 $_smarty_tpl->tpl_vars["row"]->first = $_smarty_tpl->tpl_vars["row"]->index === 0;
 $_smarty_tpl->tpl_vars["row"]->last = $_smarty_tpl->tpl_vars["row"]->iteration === $_smarty_tpl->tpl_vars["row"]->total;
?>
    <?php if ($_smarty_tpl->tpl_vars['row']->first||($_smarty_tpl->tpl_vars['row']->iteration%6)==1) {?>
    <div class="row">
    <?php }?>
        <div class="col2<?php if ($_smarty_tpl->tpl_vars['row']->last||($_smarty_tpl->tpl_vars['row']->iteration%6)==0) {?> last<?php }?>">
            <div class="center" style="padding:10px;">
                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['row']->value['profile_url'];?>
"><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['row']->value['_profile_id'],'size'=>"medium",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['row']->value['profile_name'],'title'=>$_smarty_tpl->tpl_vars['row']->value['profile_name'],'class'=>"iloutline img_shadow img_scale",'style'=>"max-width:190px;"),$_smarty_tpl); } ?>
</a>
            </div>
        </div>
    <?php if ($_smarty_tpl->tpl_vars['row']->last||($_smarty_tpl->tpl_vars['row']->iteration%6)==0) {?>
    </div>
    <?php }?>
    <?php } ?>
</div>
<?php if ($_smarty_tpl->tpl_vars['info']->value['total_pages']>1) {?>
<div style="float:left; padding-top:9px;padding-bottom:9px;">
    <?php if ($_smarty_tpl->tpl_vars['info']->value['prev_page']>0) {?>
    <span class="button-arrow-previous" onclick="jrLoad('#newest_artists','<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
');$('html, body').animate({ scrollTop: $('#newartists').offset().top -100 }, 'slow');return false;">&nbsp;</span>
    <?php } else { ?>
    <span class="button-arrow-previous-off">&nbsp;</span>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['info']->value['next_page']>1) {?>
    <span class="button-arrow-next" onclick="jrLoad('#newest_artists','<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
');$('html, body').animate({ scrollTop: $('#newartists').offset().top -100 }, 'slow');return false;">&nbsp;</span>
    <?php } else { ?>
    <span class="button-arrow-next-off">&nbsp;</span>
    <?php }?>
</div>
<?php }?>

<div style="float:right; padding-top:9px;">
    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/artists" title="More Artists"><div class="button-more">&nbsp;</div></a>
</div>

<div class="clear"> </div>
<?php }?>

<?php }} ?>
