<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:22:01
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/350501ccfcbd6bd0040199ab6bea39a0.tpl" */ ?>
<?php /*%%SmartyHeaderCode:65797633354d673e9af1d02-39493839%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '941395a14be101a7cb50698271bcc9a9469af042' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/350501ccfcbd6bd0040199ab6bea39a0.tpl',
      1 => 1423340521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '65797633354d673e9af1d02-39493839',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_profile_id' => 0,
    '_conf' => 0,
    'purl' => 0,
    'profile_name' => 0,
    'hover' => 0,
    'profile_bio' => 0,
    'profile_quota_id' => 0,
    '_user_id' => 0,
    'profile_location' => 0,
    '_puser' => 0,
    'profile_country' => 0,
    'profile_zip' => 0,
    'uk' => 0,
    '_uff' => 0,
    'uv' => 0,
    'profile_influences' => 0,
    'followers' => 0,
    'rated' => 0,
    'stats_tpl' => 0,
    'tag_cloud' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d673e9c3c1a9_01619656',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d673e9c3c1a9_01619656')) {function content_54d673e9c3c1a9_01619656($_smarty_tpl) {?><div class="col3">
    <div>

        <div class="block">
            <div class="profile_image">
                <?php if (jrProfile_is_profile_owner($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>
                    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"purl"),$_smarty_tpl); } ?>

                    <?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"25",'default'=>"Change Image",'assign'=>"hover"),$_smarty_tpl); } ?>

                    <a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_base_url'];?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/settings/profile_id=<?php echo $_smarty_tpl->tpl_vars['_profile_id']->value;?>
"><?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'size'=>"xlarge",'class'=>"img_scale img_shadow",'alt'=>$_smarty_tpl->tpl_vars['profile_name']->value,'title'=>$_smarty_tpl->tpl_vars['hover']->value,'width'=>false,'height'=>false),$_smarty_tpl); } ?>
</a>
                    <div class="profile_hoverimage">
                        <span class="normal" style="font-weight:bold;color:#FFF;"><?php echo $_smarty_tpl->tpl_vars['hover']->value;?>
</span>&nbsp;<?php if (function_exists('smarty_function_jrCore_item_update_button')) { echo smarty_function_jrCore_item_update_button(array('module'=>"jrProfile",'view'=>"settings/profile_id=".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'title'=>"Update Profile"),$_smarty_tpl); } ?>

                    </div>
                <?php } else { ?>
                    <?php if (function_exists('smarty_function_jrCore_module_function')) { echo smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'size'=>"xxlarge",'class'=>"img_scale img_shadow",'alt'=>$_smarty_tpl->tpl_vars['profile_name']->value,'width'=>false,'height'=>false),$_smarty_tpl); } ?>

                <?php }?>
            </div>
        </div>

        <?php if (!jrCore_is_mobile_device()) {?>
            <div class="block">
                <div class="block_content mt10">
                    <div style="padding-top:8px;min-height:48px;max-height:288px;overflow:auto;">
                        <?php if (function_exists('smarty_function_jrUser_online_status')) { echo smarty_function_jrUser_online_status(array('profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value),$_smarty_tpl); } ?>

                    </div>
                </div>
            </div>
        <?php }?>


        <?php if (strlen($_smarty_tpl->tpl_vars['profile_bio']->value)>0) {?>
            <div class="block">
                <h3><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"44",'default'=>"About"),$_smarty_tpl); } ?>
 <?php echo $_smarty_tpl->tpl_vars['profile_name']->value;?>
</h3>
                <div class="block_content mt10">
                    <div style="padding-top:8px;max-height:350px;overflow:auto;">
                        <?php echo smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['profile_bio']->value,$_smarty_tpl->tpl_vars['profile_quota_id']->value);?>

                    </div>
                </div>
            </div>
        <?php } else { ?>
            <?php $_smarty_tpl->tpl_vars["_puser"] = new Smarty_variable(jrCore_db_get_item('jrUser',$_smarty_tpl->tpl_vars['_user_id']->value), null, 0);?>
            <?php if ($_smarty_tpl->tpl_vars['profile_location']->value!=''||$_smarty_tpl->tpl_vars['_puser']->value['user_signup_question_1']!='') {?>
                <?php $_smarty_tpl->tpl_vars["_uff"] = new Smarty_variable(jrCore_get_designer_form_fields('jrUser'), null, 0);?>
                <div class="block">
                    <h3><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"44",'default'=>"About"),$_smarty_tpl); } ?>
 <?php echo $_smarty_tpl->tpl_vars['profile_name']->value;?>
</h3><br>
                    <h4><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"60",'default'=>"Location"),$_smarty_tpl); } ?>
:</h4> <?php echo $_smarty_tpl->tpl_vars['profile_location']->value;?>
 &nbsp; <?php echo $_smarty_tpl->tpl_vars['profile_country']->value;?>
 &nbsp; <?php echo $_smarty_tpl->tpl_vars['profile_zip']->value;?>

                    <br>
                    <?php  $_smarty_tpl->tpl_vars["uv"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["uv"]->_loop = false;
 $_smarty_tpl->tpl_vars["uk"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['_puser']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["uv"]->key => $_smarty_tpl->tpl_vars["uv"]->value) {
$_smarty_tpl->tpl_vars["uv"]->_loop = true;
 $_smarty_tpl->tpl_vars["uk"]->value = $_smarty_tpl->tpl_vars["uv"]->key;
?>
                        <?php if (substr($_smarty_tpl->tpl_vars['uk']->value,0,21)=="user_signup_question_") {?>
                            <h4><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrUser",'id'=>$_smarty_tpl->tpl_vars['_uff']->value[((string)$_smarty_tpl->tpl_vars['uk']->value)]["label"]),$_smarty_tpl); } ?>
:</h4> <?php echo $_smarty_tpl->tpl_vars['uv']->value;?>
<br>
                        <?php }?>
                    <?php } ?>
                </div>
            <?php }?>
        <?php }?>


        <?php if (!jrCore_is_mobile_device()&&isset($_smarty_tpl->tpl_vars['profile_influences']->value)&&strlen($_smarty_tpl->tpl_vars['profile_influences']->value)>0) {?>
            <div class="block">
                <h3><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"47",'default'=>"Influences"),$_smarty_tpl); } ?>
</h3>
                <div class="block_content mt10">
                    <div style="padding-top:8px;">
                        <span class="highlight-txt bold"><?php echo $_smarty_tpl->tpl_vars['profile_influences']->value;?>
</span><br>
                    </div>
                </div>
            </div>
        <?php }?>


        <?php if (!jrCore_is_mobile_device()&&jrCore_module_is_active('jrFollower')) {?>
            <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrFollower",'search1'=>"follow_profile_id = ".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'search2'=>"follow_active = 1",'order_by'=>"_created desc",'limit'=>"15",'assign'=>"followers"),$_smarty_tpl); } ?>

            <?php if (strlen($_smarty_tpl->tpl_vars['followers']->value)>0) {?>
                <div class="block">
                    <h3><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"43",'default'=>"Latest Followers"),$_smarty_tpl); } ?>
:</h3>
                    <div class="block_content mt10">
                        <div style="padding-top:8px">
                            <?php echo $_smarty_tpl->tpl_vars['followers']->value;?>

                        </div>
                    </div>
                </div>
            <?php }?>
        <?php }?>


        <?php if (!jrCore_is_mobile_device()) {?>
            <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrRating",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'search1'=>"rating_image_size > 0",'order_by'=>"_updated desc",'limit'=>"14",'assign'=>"rated"),$_smarty_tpl); } ?>

            <?php if (strlen($_smarty_tpl->tpl_vars['rated']->value)>0) {?>
                <div class="block">
                    <h3><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"46",'default'=>"Recently Rated"),$_smarty_tpl); } ?>
:</h3>
                    <div class="block_content mt10">
                        <div style="padding-top:8px">
                            <?php echo $_smarty_tpl->tpl_vars['rated']->value;?>

                        </div>
                    </div>
                </div>
            <?php }?>
        <?php }?>


        <?php if (!jrCore_is_mobile_device()) {?>
            <div class="block mb10">
                <h3><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"45",'default'=>"Profile Stats"),$_smarty_tpl); } ?>
:</h3>
                <div class="block_content mt10">

                    <?php $_smarty_tpl->_capture_stack[0][] = array("template", "stats_tpl", null); ob_start(); ?>
                    
                        {foreach $_stats as $title => $_stat}
                        {jrCore_module_url module=$_stat.module assign="murl"}
                        <div class="stat_entry_box">
                            <a href="{$jamroom_url}/{$profile_url}/{$murl}"><span class="stat_entry_title">{$title}:</span> <span class="stat_entry_count">{$_stat.count|default:0}</span></a>
                        </div>
                        {/foreach}
                    
                    <?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
                    <?php if (function_exists('smarty_function_jrProfile_stats')) { echo smarty_function_jrProfile_stats(array('profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'template'=>$_smarty_tpl->tpl_vars['stats_tpl']->value),$_smarty_tpl); } ?>


                </div>
                <div class="clear"></div>
            </div>

            
            <?php if (strlen($_smarty_tpl->tpl_vars['tag_cloud']->value)>0) {?>
                <div class="block mb10">
                    <h3><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrTags",'id'=>"1",'default'=>"Profile Tag Cloud"),$_smarty_tpl); } ?>
:</h3>
                    <div class="block_content mt10">
                        <?php echo $_smarty_tpl->tpl_vars['tag_cloud']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>
            <?php }?>
        <?php }?>


    </div>
</div><?php }} ?>
