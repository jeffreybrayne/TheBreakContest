<?php /* Smarty version Smarty-3.1.19, created on 2015-02-07 20:26:37
         compiled from "/home/twinli5/public_html/break/data/cache/jrCore/600909b4b1a23e0a1c66a46e3385b63c.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12365006054d674fd545c02-19632898%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '144e590dd50139dfb4e3fc6162d9354f49234b57' => 
    array (
      0 => '/home/twinli5/public_html/break/data/cache/jrCore/600909b4b1a23e0a1c66a46e3385b63c.tpl',
      1 => 1423340797,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12365006054d674fd545c02-19632898',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_conf' => 0,
    'jamroom_url' => 0,
    'selected' => 0,
    'active_color' => 0,
    'forum_url' => 0,
    'furl' => 0,
    '_post' => 0,
    'check_forum_url' => 0,
    'doc_url' => 0,
    'durl' => 0,
    'check_doc_url' => 0,
    'core_url' => 0,
    'from_profile' => 0,
    'acp_menu_item' => 0,
    'url' => 0,
    'purl' => 0,
    'uurl' => 0,
    'artist_menu_item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_54d674fdd166f4_43747546',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d674fdd166f4_43747546')) {function content_54d674fdd166f4_43747546($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars["active_color"] = new Smarty_variable("#99CC00", null, 0);?>
<div id="menu_content">

<nav id="menu-wrap">
<ul id="menu">

<?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrCore_maintenance_mode']!='on'||jrUser_is_master()||jrUser_is_admin()) {?>
    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['selected']->value=='home') {?> style="color:<?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"1",'default'=>"home"),$_smarty_tpl); } ?>
</a></li>
    <li>
        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/profiles"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['selected']->value=='lists') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"12",'default'=>"profiles"),$_smarty_tpl); } ?>
</a>
        <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota']>0||$_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_member_quota']>0) {?>
            <ul>
                <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_artist_quota']>0) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/artists"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"48",'default'=>"artists"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_member_quota']>0) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/members"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"40",'default'=>"members"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
            </ul>
        <?php }?>
    </li>

    <?php if (jrCore_module_is_active('jrAudio')) {?>
        <li>
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&($_smarty_tpl->tpl_vars['selected']->value=='music'||$_smarty_tpl->tpl_vars['selected']->value=='stations')) {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"56",'default'=>"music"),$_smarty_tpl); } ?>
</a>
            <ul>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music/by_album"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"185",'default'=>"By Album"),$_smarty_tpl); } ?>
</a></li>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music/by_plays"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"59",'default'=>"By Plays"),$_smarty_tpl); } ?>
</a></li>
                <?php if (jrCore_module_is_active('jrRating')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music/by_ratings"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"60",'default'=>"By Rating"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
                <?php if (jrCore_module_is_active('jrCharts')) {?>
                    <li>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music_charts"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"56",'default'=>"Music"),$_smarty_tpl); } ?>
&nbsp;<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"27",'default'=>"charts"),$_smarty_tpl); } ?>
</a>
                        <ul>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music_charts"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"53",'default'=>"Weekly"),$_smarty_tpl); } ?>
</a></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music_charts_monthly"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"54",'default'=>"Monthly"),$_smarty_tpl); } ?>
</a></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/music_charts_yearly"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"55",'default'=>"Yearly"),$_smarty_tpl); } ?>
</a></li>
                        </ul>
                    </li>
                <?php }?>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/stations"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"138",'default'=>"Stations"),$_smarty_tpl); } ?>
</a></li>
                <?php if (jrCore_module_is_active('jrSoundCloud')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/sound_cloud"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"154",'default'=>"SoundCloud"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
            </ul>
        </li>
    <?php } elseif (jrCore_module_is_active('jrSoundCloud')) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/sound_cloud"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"154",'default'=>"SoundCloud"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>

    <?php if (jrCore_module_is_active('jrVideo')) {?>
        <li>
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/videos"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&($_smarty_tpl->tpl_vars['selected']->value=='videos'||$_smarty_tpl->tpl_vars['selected']->value=='channels')) {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"14",'default'=>"videos"),$_smarty_tpl); } ?>
</a>
            <ul>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/videos/by_album"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"185",'default'=>"By Album"),$_smarty_tpl); } ?>
</a></li>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/videos/by_plays"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"59",'default'=>"By Plays"),$_smarty_tpl); } ?>
</a></li>
                <?php if (jrCore_module_is_active('jrRating')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/videos/by_ratings"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"60",'default'=>"By Rating"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
                <?php if (jrCore_module_is_active('jrCharts')) {?>
                    <li>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/video_charts"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"57",'default'=>"Video"),$_smarty_tpl); } ?>
&nbsp;<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"27",'default'=>"charts"),$_smarty_tpl); } ?>
</a>
                        <ul>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/video_charts"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"53",'default'=>"Weekly"),$_smarty_tpl); } ?>
</a></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/video_charts_monthly"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"54",'default'=>"Monthly"),$_smarty_tpl); } ?>
</a></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/video_charts_yearly"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"55",'default'=>"Yearly"),$_smarty_tpl); } ?>
</a></li>
                        </ul>
                    </li>
                <?php }?>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/channels"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"139",'default'=>"Channels"),$_smarty_tpl); } ?>
</a></li>
                <?php if (jrCore_module_is_active('jrYouTube')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/youtube_videos"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"73",'default'=>"YouTube"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
                <?php if (jrCore_module_is_active('jrVimeo')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/vimeo_videos"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"72",'default'=>"Vimeo"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
            </ul>
        </li>
    <?php } elseif (jrCore_module_is_active('jrYouTube')) {?>
        <li>
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/youtube_videos"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"73",'default'=>"YouTube"),$_smarty_tpl); } ?>
</a>
            <ul>
                <?php if (jrCore_module_is_active('jrVimeo')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/vimeo_videos"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"72",'default'=>"Vimeo"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
            </ul>
        </li>
    <?php } elseif (jrCore_module_is_active('jrVimeo')) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/vimeo_videos"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"72",'default'=>"Vimeo"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>

    <?php if (jrCore_module_is_active('jrGallery')) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/galleries"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['selected']->value=='galleries') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"29",'default'=>"galleries"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>

    <?php if (jrCore_module_is_active('jrEvent')) {?>
        <li>
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/events"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['selected']->value=='events') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"30",'default'=>"gigs/events"),$_smarty_tpl); } ?>
</a>
            <ul>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/events/by_upcoming"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"68",'default'=>"upcoming"),$_smarty_tpl); } ?>
</a></li>
                <?php if (jrCore_module_is_active('jrRating')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/events/by_ratings"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"60",'default'=>"By Rating"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
            </ul>
        </li>
    <?php }?>

    <?php if (jrCore_module_is_active('jrBlog')) {?>
        <li>
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/blogs"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['selected']->value=='ban') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"140",'default'=>"blogs"),$_smarty_tpl); } ?>
/<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"9",'default'=>"News"),$_smarty_tpl); } ?>
</a>
            <ul>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/site_blogs"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"188",'default'=>"User"),$_smarty_tpl); } ?>
&nbsp;<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"140",'default'=>"blogs"),$_smarty_tpl); } ?>
</a></li>
                <?php if (jrCore_module_is_active('jrPage')) {?>
                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/articles"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"67",'default'=>"articles"),$_smarty_tpl); } ?>
</a></li>
                <?php }?>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/news"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"9",'default'=>"news"),$_smarty_tpl); } ?>
</a></li>
            </ul>
        </li>
    <?php } elseif (jrCore_module_is_active('jrPage')) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/articles"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"67",'default'=>"articles"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>

    <?php if (jrCore_module_is_active('jrGroup')) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/groups"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['selected']->value=='groups') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"195",'default'=>"Groups"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>
    <?php if (jrCore_module_is_active('jrGroupDiscuss')) {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/discussions"<?php if (isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['selected']->value=='discussions') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"196",'default'=>"Discussions"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>

    <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_forum_profile_url'])&&strlen($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_forum_profile_url'])>0) {?>
        <?php $_smarty_tpl->tpl_vars["forum_url"] = new Smarty_variable($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_forum_profile_url'], null, 0);?>
    <?php }?>
    <?php if (jrCore_module_is_active('jrForum')&&isset($_smarty_tpl->tpl_vars['forum_url']->value)) {?>
        <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrForum",'assign'=>"furl"),$_smarty_tpl); } ?>

        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['forum_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['furl']->value;?>
"<?php if (isset($_smarty_tpl->tpl_vars['_post']->value['option'])&&$_smarty_tpl->tpl_vars['_post']->value['option']=='forum'&&$_smarty_tpl->tpl_vars['_post']->value['_uri']==$_smarty_tpl->tpl_vars['check_forum_url']->value) {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrForum",'id'=>"36",'default'=>"Forum"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>

    <?php if (isset($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_docs_profile_url'])&&strlen($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_docs_profile_url'])>0) {?>
        <?php $_smarty_tpl->tpl_vars["doc_url"] = new Smarty_variable($_smarty_tpl->tpl_vars['_conf']->value['jrMediaPro_docs_profile_url'], null, 0);?>
    <?php }?>
    <?php if (jrCore_module_is_active('jrDocs')&&isset($_smarty_tpl->tpl_vars['doc_url']->value)) {?>
        <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrDocs",'assign'=>"durl"),$_smarty_tpl); } ?>

        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['doc_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['durl']->value;?>
"<?php if (isset($_smarty_tpl->tpl_vars['_post']->value['option'])&&$_smarty_tpl->tpl_vars['_post']->value['option']=='documentation'&&$_smarty_tpl->tpl_vars['_post']->value['_uri']==$_smarty_tpl->tpl_vars['check_doc_url']->value) {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('module'=>"jrDocs",'id'=>"53",'default'=>"Documentation"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>

<?php }?>





<?php if (jrUser_is_logged_in()) {?>
    <?php if (jrUser_is_master()) {?>
        <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"core_url"),$_smarty_tpl); } ?>

        <?php if (function_exists('smarty_function_jrCore_get_module_index')) { echo smarty_function_jrCore_get_module_index(array('module'=>"jrCore",'assign'=>"url"),$_smarty_tpl); } ?>

        <li>
            <?php if ($_smarty_tpl->tpl_vars['_post']->value['_uri']=='/profile/settings'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/user/account'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/user/notifications'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/foxycart/subscription_browser'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/foxycart/items'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/oneall/networks'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/profiletweaks/customize'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/follow/browse'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/note/notes') {?>
                <?php $_smarty_tpl->tpl_vars["acp_menu_item"] = new Smarty_variable("no", null, 0);?>
            <?php } else { ?>
                <?php $_smarty_tpl->tpl_vars["acp_menu_item"] = new Smarty_variable("yes", null, 0);?>
            <?php }?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin/global"<?php if (!isset($_smarty_tpl->tpl_vars['from_profile']->value)&&!isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['acp_menu_item']->value=='yes') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"16",'default'=>"ACP"),$_smarty_tpl); } ?>
</a>
            <ul>
                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/admin/tools"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"75",'default'=>"System Tools"),$_smarty_tpl); } ?>
</a>
                    <ul>
                        <li>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"91",'default'=>"Activity Logs"),$_smarty_tpl); } ?>
</a>
                            <ul>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/debug_log"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"186",'default'=>"Debug Logs"),$_smarty_tpl); } ?>
</a></li>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/php_error_log"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"187",'default'=>"PHP Error Logs"),$_smarty_tpl); } ?>
</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/cache_reset"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"92",'default'=>"Reset Cache"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrImage"),$_smarty_tpl); } ?>
/cache_reset"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"145",'default'=>"Reset Image Cache"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/integrity_check"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"93",'default'=>"Integrity Check"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/marketplace/system_update"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"189",'default'=>"System Updates"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/system_check"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"97",'default'=>"System Check"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrBanned"),$_smarty_tpl); } ?>
/browse"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"94",'default'=>"Banned Items"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_menu"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"95",'default'=>"Skin Menu Editor"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrSitemap"),$_smarty_tpl); } ?>
/admin/tools"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"96",'default'=>"Create Sitemap"),$_smarty_tpl); } ?>
</a></li>
                    </ul>
                </li>
                <li>
                    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrProfile",'assign'=>"purl"),$_smarty_tpl); } ?>

                    <?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl); } ?>

                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/admin/tools"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"163",'default'=>"Users"),$_smarty_tpl); } ?>
</a>
                    <ul>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/quota_browser"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"158",'default'=>"Quota Browser"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['purl']->value;?>
/browser"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"161",'default'=>"Profile Browser"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/browser"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"159",'default'=>"User Accounts"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/online"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"162",'default'=>"Who's Online"),$_smarty_tpl); } ?>
</a></li>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/global/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"74",'default'=>"Skin Settings"),$_smarty_tpl); } ?>
</a>
                    <ul>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/style/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"190",'default'=>"Skin Styles"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/images/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"191",'default'=>"Skin Images"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/language/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"193",'default'=>"Skin Langauge"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_admin/templates/skin=<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"192",'default'=>"Skin Templates"),$_smarty_tpl); } ?>
</a></li>
                        <li><a onclick="popwin('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/readme.html','readme',800,500,'yes');"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"194",'default'=>"Skin Notes"),$_smarty_tpl); } ?>
</a></li>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['core_url']->value;?>
/skin_menu"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"95",'default'=>"Skin Menu Editor"),$_smarty_tpl); } ?>
</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrCore"),$_smarty_tpl); } ?>
/dashboard"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"17",'default'=>"dashboard"),$_smarty_tpl); } ?>
</a></li>
            </ul>
        </li>
    <?php } elseif (jrUser_is_admin()) {?>
        <?php if ($_smarty_tpl->tpl_vars['_post']->value['_uri']=='/profile/settings'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/user/account'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/user/notifications'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/foxycart/subscription_browser'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/foxycart/items'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/oneall/networks'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/profiletweaks/customize'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/follow/browse'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/note/notes') {?>
            <?php $_smarty_tpl->tpl_vars["acp_menu_item"] = new Smarty_variable("no", null, 0);?>
        <?php } else { ?>
            <?php $_smarty_tpl->tpl_vars["acp_menu_item"] = new Smarty_variable("yes", null, 0);?>
        <?php }?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrCore"),$_smarty_tpl); } ?>
/dashboard"<?php if (!isset($_smarty_tpl->tpl_vars['from_profile']->value)&&!isset($_smarty_tpl->tpl_vars['selected']->value)&&$_smarty_tpl->tpl_vars['acp_menu_item']->value=='yes') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"17",'default'=>"dashboard"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>
<?php } else { ?>
    <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrCore_maintenance_mode']!='on'&&$_smarty_tpl->tpl_vars['_conf']->value['jrUser_signup_on']=='on') {?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrUser"),$_smarty_tpl); } ?>
/signup"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"2",'default'=>"create"),$_smarty_tpl); } ?>
&nbsp;<?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"3",'default'=>"account"),$_smarty_tpl); } ?>
</a></li>
    <?php }?>
    <li><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrCore_module_url')) { echo smarty_function_jrCore_module_url(array('module'=>"jrUser"),$_smarty_tpl); } ?>
/login"><?php if (function_exists('smarty_function_jrCore_lang')) { echo smarty_function_jrCore_lang(array('skin'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'],'id'=>"6",'default'=>"login"),$_smarty_tpl); } ?>
</a></li>
<?php }?>

<?php if (jrUser_is_logged_in()) {?>
    <li>
        <?php if ($_smarty_tpl->tpl_vars['_post']->value['_uri']!=$_smarty_tpl->tpl_vars['check_forum_url']->value&&$_smarty_tpl->tpl_vars['_post']->value['_uri']!=$_smarty_tpl->tpl_vars['check_doc_url']->value&&isset($_smarty_tpl->tpl_vars['from_profile']->value)&&$_smarty_tpl->tpl_vars['from_profile']->value=='yes'||($_smarty_tpl->tpl_vars['_post']->value['_uri']=='/profile/settings'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/user/account'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/user/notifications'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/foxycart/subscription_browser'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/foxycart/items'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/oneall/networks'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/profiletweaks/customize'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/follow/browse'||$_smarty_tpl->tpl_vars['_post']->value['_uri']=='/note/notes')) {?>
            <?php $_smarty_tpl->tpl_vars["artist_menu_item"] = new Smarty_variable("yes", null, 0);?>
        <?php } else { ?>
            <?php $_smarty_tpl->tpl_vars["artist_menu_item"] = new Smarty_variable("no", null, 0);?>
        <?php }?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php if (function_exists('smarty_function_jrUser_home_profile_key')) { echo smarty_function_jrUser_home_profile_key(array('key'=>"profile_url"),$_smarty_tpl); } ?>
"<?php if (isset($_smarty_tpl->tpl_vars['artist_menu_item']->value)&&$_smarty_tpl->tpl_vars['artist_menu_item']->value=='yes') {?> style="color: <?php echo $_smarty_tpl->tpl_vars['active_color']->value;?>
;"<?php }?>><?php if (function_exists('smarty_function_jrUser_home_profile_key')) { echo smarty_function_jrUser_home_profile_key(array('key'=>"profile_name"),$_smarty_tpl); } ?>
</a>
        <ul>
            <?php if (function_exists('smarty_function_jrCore_skin_menu')) { echo smarty_function_jrCore_skin_menu(array('template'=>"menu.tpl",'category'=>"user"),$_smarty_tpl); } ?>

        </ul>
    </li>
<?php }?>


<!-- Cart contents -->
<?php if (jrCore_module_is_active('jrFoxyCart')&&strlen($_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_api_key'])>0) {?>
    <?php if (jrUser_is_logged_in()) {?>
        <li>
            <a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrFoxyCart_store_domain'];?>
/cart?cart=view"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"cart.png",'width'=>"24",'height'=>"24",'alt'=>"cart"),$_smarty_tpl); } ?>
<span id="fc_minicart"><span id="fc_quantity" class="hl-4"></span></span></a>
        </li>
    <?php }?>
<?php }?>

<?php if (jrCore_module_is_active('jrSearch')) {?>
    <li><a onclick="jrSearch_modal_form();" title="Site Search"><?php if (function_exists('smarty_function_jrCore_image')) { echo smarty_function_jrCore_image(array('image'=>"magnifying_glass.png",'width'=>"24",'height'=>"24",'alt'=>"search"),$_smarty_tpl); } ?>
</a></li>
<?php }?>
</ul>
</nav>

</div>
<?php }} ?>
