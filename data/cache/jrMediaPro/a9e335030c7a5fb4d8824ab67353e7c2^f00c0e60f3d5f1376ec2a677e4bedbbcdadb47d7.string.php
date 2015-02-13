<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:31:09
         compiled from "f00c0e60f3d5f1376ec2a677e4bedbbcdadb47d7" */ ?>
<?php /*%%SmartyHeaderCode:108966896954d6760dbe3b77-07984769%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f00c0e60f3d5f1376ec2a677e4bedbbcdadb47d7' => 
    array (
      0 => 'f00c0e60f3d5f1376ec2a677e4bedbbcdadb47d7',
      1 => 0,
      2 => 'string',
    ),
  ),
  'nocache_hash' => '108966896954d6760dbe3b77-07984769',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_items' => 0,
    'jamroom_url' => 0,
    'murl' => 0,
    '_conf' => 0,
    'item' => 0,
    'info' => 0,
    'pages' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d6760dde2527_87790010',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d6760dde2527_87790010')) {function content_54d6760dde2527_87790010($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
?>        
            <?php if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>
            <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrBlog",'assign'=>"murl"),$_smarty_tpl); } ?>

            <div class="body_1">
                <h3>
                    <?php if (jrUser_is_master()) {?>
                    <div class="float-right" style="padding-right:10px;">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_items']->value[0]['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_items']->value[0]['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['_items']->value[0]['blog_title_url'];?>
"><?php if (function_exists('smarty_function_jrCore_icon')) { echo smarty_function_jrCore_icon(array('icon'=>"gear",'size'=>"18"),$_smarty_tpl); } ?>
</a>
                    </div>
                    <?php }?>
                    <span style="font-weight: normal;line-height:24px;padding-left:5px;"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"8",'default'=>"Site"),$_smarty_tpl); } ?>
</span>&nbsp;<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"9",'default'=>"News"),$_smarty_tpl); } ?>

                </h3>
            </div>
            <div class="body_4 mb20 pt10">
            <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value) {
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
            <div style="height:425px;padding:10px;overflow: auto;">
                <div class="br-info" style="margin-bottom:20px;">
                    <div class="blog-div">
                        <span class="blog-user capital"> By <span class="hl-3"><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</span></span><br>
                        <span class="blog-date" style="margin-left:0;"> <?php echo jrCore_format_time($_smarty_tpl->tpl_vars['item']->value['blog_publish_date']);?>
</span><br>
                        <span class="blog-tag capital" style="margin-left:0;"> Tag: <span class="hl-4"><?php echo $_smarty_tpl->tpl_vars['item']->value['blog_category'];?>
</span></span>
                        <?php if (jrCore_module_is_active('jrComment')) {?>
                            <br>
                            <span class="blog-replies" style="margin-left:0;">
                                <?php if ($_smarty_tpl->tpl_vars['item']->value['profile_id']=='1') {?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/news_story/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['blog_title_url'];?>
#comments"><span class="capital"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrBlog",'id'=>"27",'default'=>"comments"),$_smarty_tpl); } ?>
</span>: <?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['blog_comment_count'])===null||strlen($tmp)===0 ? 0 : $tmp);?>
</a>
                                <?php } else { ?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['blog_title_url'];?>
#comments"><span class="capital"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrBlog",'id'=>"27",'default'=>"comments"),$_smarty_tpl); } ?>
</span>: <?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value['blog_comment_count'])===null||strlen($tmp)===0 ? 0 : $tmp);?>
</a>
                                <?php }?>
                            </span>
                        <?php }?>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['item']->value['profile_id']=='1') {?>
                    <h3><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/news_story/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['blog_title_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['blog_title'];?>
</a></h3>
                <?php } else { ?>
                    <h3><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['blog_title_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['blog_title'];?>
</a></h3>
                <?php }?>
                <div class="blog-text">
                    <?php echo smarty_modifier_jrCore_format_string(smarty_modifier_truncate($_smarty_tpl->tpl_vars['item']->value['blog_text'],800,"...",false),$_smarty_tpl->tpl_vars['item']->value['profile_quota_id'],null,'nl2br');?>

                </div>
            </div>
            <?php } ?>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['info']->value['total_pages']>1) {?>
            <div class="block">
                <table style="width:100%;">
                    <tr>

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            <?php if (isset($_smarty_tpl->tpl_vars['info']->value['prev_page'])&&$_smarty_tpl->tpl_vars['info']->value['prev_page']>0) {?>
                            <a onclick="jrLoad('#site_news_div','<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/site_news/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['prev_page'];?>
');"><span class="button-arrow-previous">&nbsp;</span></a>
                            <?php } else { ?>
                            <span class="button-arrow-previous-off">&nbsp;</span>
                            <?php }?>
                        </td>

                        <td class="body_5" style="width:50%;text-align:center;border:1px solid #282828;">
                            <?php if ($_smarty_tpl->tpl_vars['info']->value['total_pages']<=5||$_smarty_tpl->tpl_vars['info']->value['total_pages']>500||$_smarty_tpl->tpl_vars['info']->value['total_pages']>500) {?>
                            <?php echo $_smarty_tpl->tpl_vars['info']->value['page'];?>
 &nbsp;/ <?php echo $_smarty_tpl->tpl_vars['info']->value['total_pages'];?>

                            <?php } else { ?>
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#site_news_div','<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/site_news/p=' +sel);">
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

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            <?php if (isset($_smarty_tpl->tpl_vars['info']->value['next_page'])&&$_smarty_tpl->tpl_vars['info']->value['next_page']>1) {?>
                            <a onclick="jrLoad('#site_news_div','<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/site_news/p=<?php echo $_smarty_tpl->tpl_vars['info']->value['next_page'];?>
');"><span class="button-arrow-next">&nbsp;</span></a>
                            <?php } else { ?>
                            <span class="button-arrow-next-off">&nbsp;</span>
                            <?php }?>
                        </td>

                    </tr>
                </table>
            </div>
            <?php }?>
            <?php }?>
        
    <?php }} ?>
