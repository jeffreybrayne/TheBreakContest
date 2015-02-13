<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:11
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/bc4148f4b2650a61416cb6451d9c5ccd.tpl" */ ?>
<?php /*%%SmartyHeaderCode:58665879754d6760f9489a0-48676424%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7e4f7d4289ceb78539aaf3322da3e4982a73ed5f' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/bc4148f4b2650a61416cb6451d9c5ccd.tpl',
      1 => 1423341071,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '58665879754d6760f9489a0-48676424',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'jamroom_url' => 0,
    'item' => 0,
    'murl' => 0,
    '_conf' => 0,
    'alttitle' => 0,
    'info' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760fb4f5d2_90861310',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760fb4f5d2_90861310')) {function content_54d6760fb4f5d2_90861310($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?><?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrAudio",'assign'=>"murl"),$_smarty_tpl); } ?>

    <div class="container">
        <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars["item"]->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars["item"]->iteration=0;
 $_smarty_tpl->tpl_vars["item"]->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
 $_smarty_tpl->tpl_vars["item"]->iteration++;
 $_smarty_tpl->tpl_vars["item"]->index++;
 $_smarty_tpl->tpl_vars["item"]->first = $_smarty_tpl->tpl_vars["item"]->index === 0;
 $_smarty_tpl->tpl_vars["item"]->last = $_smarty_tpl->tpl_vars["item"]->iteration === $_smarty_tpl->tpl_vars["item"]->total;
?>
        <?php if ($_smarty_tpl->tpl_vars['item']->first||($_smarty_tpl->tpl_vars['item']->iteration%3)==1) {?>
        <div class="row">
        <?php }?>
            <div class="col4<?php if ($_smarty_tpl->tpl_vars['item']->last||($_smarty_tpl->tpl_vars['item']->iteration%3)==0) {?> last<?php }?>">
                <div class="center mb15">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title'];?>
"><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrAudio",'type'=>"audio_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_item_id'],'size'=>"medium",'crop'=>"auto",'width'=>"190",'height'=>"190",'alt'=>$_smarty_tpl->tpl_vars['item']->value['audio_title'],'title'=>$_smarty_tpl->tpl_vars['item']->value['audio_title'],'class'=>"iloutline img_shadow"),$_smarty_tpl); } ?>
</a><br>
                    <br>
                    <h3><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title_url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['item']->value['audio_title'];?>
"><span class="hl-3"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['audio_title'],20,"...",false);?>
</span></a></h3><br>
                    <h4><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['profile_name'],20,"...",false);?>
</a></h4><br>
                    <div class="page box_shadow" style="width: 190px;margin:10px auto 10px auto;">

                        <div class="container">
                            <div class="row">
                                <div class="col12 last">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="width:5%;text-align:center;vertical-align:middle;padding:0;margin:0;">
                                                <?php if ($_smarty_tpl->tpl_vars['item']->value['audio_file_extension']=='mp3') {?>
                                                    <?php if (function_exists('smarty_function_jrCore_media_player')) { echo smarty_function_jrCore_media_player(array('type'=>"jrAudio_button",'module'=>"jrAudio",'field'=>"audio_file",'item'=>$_smarty_tpl->tpl_vars['item']->value,'image'=>"button_player"),$_smarty_tpl); } ?>

                                                <?php } else { ?>
                                                    <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"156",'default'=>"Download",'assign'=>"alttitle"),$_smarty_tpl); } ?>

                                                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/download/audio_file/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"download.png",'alt'=>$_smarty_tpl->tpl_vars['alttitle']->value,'title'=>$_smarty_tpl->tpl_vars['alttitle']->value),$_smarty_tpl); } ?>
</a>
                                                <?php }?>
                                            </td>
                                            <td>
                                                <div class="center p5">
                                                    <span class="capital hl-4"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"51",'default'=>"Plays:"),$_smarty_tpl); } ?>
</span>&nbsp;<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['audio_file_stream_count'])===null||strlen($tmp)===0 ? 0 : $tmp);?>

                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        <?php if ($_smarty_tpl->tpl_vars['item']->last||($_smarty_tpl->tpl_vars['row']->iteration%3)==0) {?>
        </div>
        <?php }?>
        <?php } ?>
    </div>
    <?php if ($_smarty_tpl->tpl_vars['info']->value['total_pages']>1) {?>
        <div style="float:left; padding-top:9px;padding-bottom:9px;">
            <?php if ($_smarty_tpl->tpl_vars['info']->value['prev_page']>0) {?>
                <span class="button-arrow-previous" onclick="jrLoad('#top_singles','<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
');$('html, body').animate({ scrollTop: $('#tsingles').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            <?php } else { ?>
                <span class="button-arrow-previous-off">&nbsp;</span>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['info']->value['next_page']>1) {?>
                <span class="button-arrow-next" onclick="jrLoad('#top_singles','<?php echo $_smarty_tpl->tpl_vars['info']->value['page_base_url'];?>
/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
');$('html, body').animate({ scrollTop: $('#tsingles').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            <?php } else { ?>
                <span class="button-arrow-next-off">&nbsp;</span>
            <?php }?>
        </div>
    <?php }?>
    <div style="float:right; padding-top:9px;">
        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music" title="More Singles"><div class="button-more">&nbsp;</div></a>
    </div>

    <div class="clear"> </div>
<?php }?>

<?php }} ?>
