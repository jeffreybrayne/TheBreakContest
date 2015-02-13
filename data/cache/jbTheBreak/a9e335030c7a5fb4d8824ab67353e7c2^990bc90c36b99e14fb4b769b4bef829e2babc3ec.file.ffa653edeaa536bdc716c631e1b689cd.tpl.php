<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:32:02
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/ffa653edeaa536bdc716c631e1b689cd.tpl" */ ?>
<?php /*%%SmartyHeaderCode:70159836854d67642cd7971-42385541%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '990bc90c36b99e14fb4b769b4bef829e2babc3ec' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/ffa653edeaa536bdc716c631e1b689cd.tpl',
      1 => 1423341122,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70159836854d67642cd7971-42385541',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_post' => 0,
    'jamroom_url' => 0,
    '_conf' => 0,
    'footer_contact_row' => 0,
    'css_footer_href' => 0,
    '_css' => 0,
    'javascript_footer_href' => 0,
    '_js' => 0,
    'javascript_footer_function' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d67642df0f66_41637950',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d67642df0f66_41637950')) {function content_54d67642df0f66_41637950($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/twinli5/public_html/break/modules/jrCore/contrib/smarty/libs/plugins/modifier.date_format.php';
?><?php if (isset($_smarty_tpl->tpl_vars['_post']->value['module'])&&$_smarty_tpl->tpl_vars['_post']->value['option']!='admin'&&($_smarty_tpl->tpl_vars['_post']->value['module']=='jrRecommend'||$_smarty_tpl->tpl_vars['_post']->value['module']=='jrSearch')) {?>
                        </div>
                    </div>
                </div>
    <div class="col3 last">
        <div class="body_1">
            <?php if (function_exists('smarty_function_jrCore_include')) { echo smarty_function_jrCore_include(array('template'=>"side_home.tpl"),$_smarty_tpl); } ?>

        </div>
    </div>
            </div>
        </div>
<?php }?>
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                
                <div class="col2">
                    <div id="footer_logo">
                        <?php ob_start();?><?php echo smarty_modifier_date_format(time(),"%Y");?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo smarty_modifier_date_format(time(),"%Y");?>
<?php $_tmp2=ob_get_clean();?><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"logo.png",'width'=>"150",'height'=>"38",'alt'=>"TheBreak Skin &copy; ".$_tmp1." The Jamroom Network",'title'=>"TheBreak Skin &copy; ".$_tmp2." The Jamroom Network"),$_smarty_tpl); } ?>

                    </div>
                </div>

                
                <div class="col7">
                    <div class="footer pt10">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/terms_of_service"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"79",'default'=>"Terms Of Service"),$_smarty_tpl); } ?>
</a>&nbsp;|
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/privacy_policy"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"80",'default'=>"Privacy Policy"),$_smarty_tpl); } ?>
</a>&nbsp;|
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/about"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"118",'default'=>"About Us"),$_smarty_tpl); } ?>
</a>&nbsp;|
                    <?php if (jrCore_module_is_active('jrCustomForm')) {?>
                        |&nbsp;<a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/form/contact_us"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"81",'default'=>"Contact Us"),$_smarty_tpl); } ?>
</a>
                    <?php } else { ?>
                        <?php $_smarty_tpl->_capture_stack[0][] = array("footer_contact", "footer_contact_row", null); ob_start(); ?>
                        
                            {if isset($_items)}
                                {foreach from=$_items item="item"}
                                    |&nbsp;<a href="mailto:{$item.user_email}?subject={$_conf.jrCore_system_name} Contact">{jrCore_lang skin=$_conf.jrCore_active_skin id="81" default="Contact Us"}</a>
                                {/foreach}
                            {/if}
                        
                        <?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
                        <?php if (function_exists('smarty_function_jrCore_list')) { echo smarty_function_jrCore_list(array('module'=>"jrUser",'limit'=>"1",'profile_id'=>"1",'template'=>$_smarty_tpl->tpl_vars['footer_contact_row']->value),$_smarty_tpl); } ?>

                    <?php }?>
                    </div>
                </div>

                <div class="col3 last">
                    <div id="footer_sn">

                        
                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_twitter_name'])>0) {?>
                            <a href="https://twitter.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_twitter_name'];?>
" target="_blank"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-twitter.png",'width'=>"24",'height'=>"24",'class'=>"social-img",'alt'=>"twitter",'title'=>"Follow @".((string)$_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_twitter_name'])),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_facebook_name'])>0) {?>
                            <a href="https://facebook.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_facebook_name'];?>
" target="_blank"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-facebook.png",'width'=>"24",'height'=>"24",'class'=>"social-img",'alt'=>"facebook",'title'=>"Like ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_facebook_name'])." on Facebook"),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_linkedin_name'])>0) {?>
                            <a href="https://linkedin.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_linkedin_name'];?>
" target="_blank"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-linkedin.png",'width'=>"24",'height'=>"24",'class'=>"social-img",'alt'=>"linkedin",'title'=>"Link up with ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_linkedin_name'])." on LinkedIn"),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                        <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_google_name'])>0) {?>
                            <a href="https://google.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_google_name'];?>
" target="_blank"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"sn-google-plus.png",'width'=>"24",'height'=>"24",'class'=>"social-img",'alt'=>"google+",'title'=>"Follow ".((string)$_smarty_tpl->tpl_vars['_conf']->value['jbTheBreak_google_name'])." on Google+"),$_smarty_tpl); } ?>
</a>
                        <?php }?>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<a href="#" class="scrollup">Scroll</a>

<div id="footer-bar">
    <div class="container">
        <div class="row">
            <div class="col6">
                <div class="footer-copy">
                    <span class="capital"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"99",'default'=>"Copyright"),$_smarty_tpl); } ?>
 &copy;<?php echo smarty_modifier_date_format(time(),"%Y");?>
</span> <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
</a>, <span class="hl-2 capital"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"100",'default'=>"all rights reserved"),$_smarty_tpl); } ?>
.</span>
                </div>
            </div>
            <div class="col6 last">
                <div class="footer-design">
                    
                    <?php if (function_exists('smarty_function_jrCore_powered_by')) { echo smarty_function_jrCore_powered_by(array(),$_smarty_tpl); } ?>

                </div>
            </div>
        </div>
    </div>
</div>

</div>

<?php if (isset($_smarty_tpl->tpl_vars['css_footer_href']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars["_css"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_css"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['css_footer_href']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_css"]->key => $_smarty_tpl->tpl_vars["_css"]->value) {
$_smarty_tpl->tpl_vars["_css"]->_loop = true;
?>
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['_css']->value['source'];?>
" media="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_css']->value['media'])===null||strlen($tmp)===0 ? "screen" : $tmp);?>
" />
    <?php } ?>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['javascript_footer_href']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars["_js"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["_js"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['javascript_footer_href']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["_js"]->key => $_smarty_tpl->tpl_vars["_js"]->value) {
$_smarty_tpl->tpl_vars["_js"]->_loop = true;
?>
    <script type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_js']->value['type'])===null||strlen($tmp)===0 ? "text/javascript" : $tmp);?>
" src="<?php echo $_smarty_tpl->tpl_vars['_js']->value['source'];?>
"></script>
    <?php } ?>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['javascript_footer_function']->value)) {?>
<script type="text/javascript">
    <?php echo $_smarty_tpl->tpl_vars['javascript_footer_function']->value;?>

</script>
<?php }?>


<div id="jr_temp_work_div" style="display:none"></div>

<?php if (jrCore_is_mobile_device()) {?>

    
    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                var ms = new $.slidebars();
                $('#mmt').on('click', function() {
                    ms.slidebars.open('left');
                });
            });
        }) (jQuery);
    </script>

    </div>

<?php } else { ?>


<script type="text/javascript">

    $(function() {
        /* Mobile */
        $('#menu-wrap').prepend('<div id="menu-trigger"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"20",'default'=>"menu"),$_smarty_tpl); } ?>
</div>');
        $("#menu-trigger").on("click", function(){
            $("#menu").slideToggle();
         });

        // iPad
        var isiPad = navigator.userAgent.match(/iPad/i) != null;
        if (isiPad) $('#menu ul').addClass('no-transition');
     });
</script>

<?php }?>

</body>
</html>
<?php }} ?>
