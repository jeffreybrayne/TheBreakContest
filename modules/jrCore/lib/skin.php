<?php
/**
 * Jamroom 5 System Core module
 *
 * copyright 2003 - 2015
 * by The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
 *
 * This software is provided "as is" and any express or implied
 * warranties, including, but not limited to, the implied warranties
 * of merchantability and fitness for a particular purpose are
 * disclaimed.  In no event shall the Jamroom Network be liable for
 * any direct, indirect, incidental, special, exemplary or
 * consequential damages (including but not limited to, procurement
 * of substitute goods or services; loss of use, data or profits;
 * or business interruption) however caused and on any theory of
 * liability, whether in contract, strict liability, or tort
 * (including negligence or otherwise) arising from the use of this
 * software, even if advised of the possibility of such damage.
 * Some jurisdictions may not allow disclaimers of implied warranties
 * and certain statements in the above disclaimer may not apply to
 * you as regards implied warranties; the other terms and conditions
 * remain enforceable notwithstanding. In some jurisdictions it is
 * not permitted to limit liability and therefore such limitations
 * may not apply to you.
 *
 * @package Skin
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Verify a skin is installed properly
 * @param string $skin Skin to verify
 * @return bool Returns true
 */
function jrCore_verify_skin($skin)
{
    if (is_file(APP_DIR . "/skins/{$skin}/include.php")) {
        require_once APP_DIR . "/skins/{$skin}/include.php";
        $func = "{$skin}_skin_init";
        if (function_exists($func)) {
            $func();
        }
    }
    if (is_file(APP_DIR . "/skins/{$skin}/config.php")) {
        require_once APP_DIR . "/skins/{$skin}/config.php";
        $func = "{$skin}_skin_config";
        if (function_exists($func)) {
            $func();
        }
    }
    // Install Language strings for Skin
    jrUser_install_lang_strings('skin', $skin);

    // Build skin CSS and JS
    jrCore_create_master_css($skin);
    jrCore_create_master_javascript($skin);
    return true;
}

/**
 * Generate a CSS Sprite background image from existing Icon images
 * @param $skin string Skin to use for overriding default icon images
 * @param $color string Icon color set to use (black||white)
 * @param $width int Pixel width for Icons
 * @return bool
 */
function jrCore_create_css_sprite($skin, $color = 'black', $width = 64)
{
    global $_conf;
    // Our ICON sprites live in jrCore/img/icons, and each can
    // be overridden by the skin with it's own version of the sprite
    $swidth = 0;

    // Modules
    $_icons = glob(APP_DIR . "/modules/*/img/icons_{$color}/*.png");
    if (is_array($_icons)) {
        foreach ($_icons as $k => $v) {
            $name          = basename($v);
            $_icons[$name] = $v;
            unset($_icons[$k]);
            $swidth += $width;
        }
    }
    // Override core with skin
    if (is_dir(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/icons_{$color}")) {
        $_skin = glob(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/icons_{$color}/*.png");
        if (is_array($_skin)) {
            foreach ($_skin as $v) {
                $name = basename($v);
                if (!isset($_icons[$name])) {
                    $swidth += $width;
                }
                $_icons[$name] = $v;
            }
        }
        unset($_skin);
    }

    // Now create our Sprite image
    $sprite = imagecreatetruecolor($swidth, $width);
    imagealphablending($sprite, false);
    imagesavealpha($sprite, true);
    $left = 0;
    $css  = ".sprite_icon_{$width}{display:inline-block;width:{$width}px;height:{$width}px;}\n";
    $css .= ".sprite_icon_{$width}_img{background:url('" . str_replace(array('http:', 'https:'), '', $_conf['jrCore_base_url']) . "/data/media/0/0/{$_conf['jrCore_active_skin']}_sprite_{$width}.png') no-repeat top left; height:100%;width:100%;}";
    foreach ($_icons as $name => $image) {
        $img = imagecreatefrompng($image);
        imagecopyresampled($sprite, $img, $left, 0, 0, 0, $width, $width, 64, 64);
        // Generate CSS
        $nam = str_replace('.png', '', $name);
        if ($left > 0) {
            $css .= "\n.sprite_icon_{$width}_{$nam}{background-position:-{$left}px 0;}";
        }
        else {
            $css .= "\n.sprite_icon_{$width}_{$nam}{background-position:0 0;}";
        }
        $left += $width;
    }
    $dir = jrCore_get_media_directory(0);
    jrCore_write_to_file("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$width}.css", $css . "\n");
    imagepng($sprite, "{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$width}.png");
    imagedestroy($sprite);
    return true;
}

/**
 * Get HTML code for a given CSS icon sprite
 * @param $name string Name of CSS Icon to get
 * @param $size int Size (in pixels) for icon
 * @param $class string Additional icon HTML class
 * @return string
 */
function jrCore_get_sprite_html($name, $size = null, $class = null)
{
    global $_conf;
    if (is_null($size)) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_size');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $size = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $size = reset($size);
            if (!is_numeric($size)) {
                $size = 32;
            }
        }
        else {
            $size = 32;
        }
    }
    $out = '';
    if (!jrCore_get_flag("jrcore_include_icon_css_{$size}")) {
        // We have not included this size yet on our page - bring in now
        $dir = jrCore_get_media_directory(0, FORCE_LOCAL);
        $mtm = @filemtime("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$size}.css");
        if (!$mtm) {
            $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_color');
            if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
                $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
                $color = reset($color);
            }
            else {
                $color = 'black';
            }
            jrCore_create_css_sprite($_conf['jrCore_active_skin'], $color, $size);
            $mtm = filemtime("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$size}.css");
        }
        $out = '<link rel="stylesheet" property="stylesheet" href="' . $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url('jrCore') . '/icon_css/' . $size . '?_v=' . $mtm . '" />';
        jrCore_set_flag("jrcore_include_icon_css_{$size}", 1);
    }
    // See if we are doing a highlighted icon
    $cls = '';
    if (strlen($class) > 0) {
        $cls = " {$class}";
    }
    if (strpos($name, '-hilighted')) {
        $name = str_replace('-hilighted', '', $name);
        $out .= "<span class=\"sprite_icon sprite_icon_hilighted sprite_icon_{$size}{$cls}\"><span class=\"sprite_icon_{$size} sprite_icon_{$size}_img sprite_icon_{$size}_{$name}\">&nbsp;</span></span>";
    }
    else {
        $out .= "<span class=\"sprite_icon sprite_icon_{$size}{$cls}\"><span class=\"sprite_icon_{$size} sprite_icon_{$size}_img sprite_icon_{$size}_{$name}\">&nbsp;</span></span>";
    }
    return $out;
}

/**
 * jrCore_skin_meta_data - get meta data for a skin
 * @param string $skin skin string skin name
 * @return mixed returns metadata/key if found, false if not
 */
function jrCore_skin_meta_data($skin)
{
    $func = "{$skin}_skin_meta";
    if (!function_exists($func) && is_file(APP_DIR . "/skins/{$skin}/include.php")) {
        require_once APP_DIR . "/skins/{$skin}/include.php";
    }
    if (!function_exists($func)) {
        return false;
    }
    $_tmp = $func();
    if ($_tmp && is_array($_tmp)) {
        return $_tmp;
    }
    return false;
}

/**
 * Add a skin's global config to $_conf
 * @param $skin string Skin name to load
 * @param $_conf array Global Config
 * @return bool|array
 */
function jrCore_load_skin_config($skin, $_conf)
{
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT CONCAT_WS('_', `module`, `name`) AS k, `value` AS v FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($skin) . "'";
    $_cf = jrCore_db_query($req, 'k', false, 'v');
    if ($_cf && is_array($_cf)) {
        foreach ($_cf as $k => $v) {
            $_conf[$k] = $v;
        }
    }
    return $_conf;
}

/**
 * jrCore_get_skins
 * Retrieves a list of skins available on the system
 */
function jrCore_get_skins()
{
    $tmp = jrCore_get_flag('jrcore_get_skins');
    if ($tmp) {
        return $tmp;
    }
    $_sk = array();
    // and now do our deletion
    if ($h = opendir(APP_DIR . '/skins')) {
        while (($file = readdir($h)) !== false) {
            if ($file == '.' || $file == '..' || strpos($file, '-release-')) {
                continue;
            }
            elseif (is_file(APP_DIR . "/skins/{$file}/include.php")) {
                $_sk[$file] = $file;
            }
        }
        closedir($h);
    }
    ksort($_sk);
    jrCore_set_flag('jrcore_get_skins', $_sk);
    return $_sk;
}

/**
 * Delete an existing Skin Menu Item
 * @param $module string Module Name that created the Skin Menu Item
 * @param $unique string Unique name/tag for the Skin Menu Item
 * @return mixed
 */
function jrCore_delete_skin_menu_item($module, $unique)
{
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "DELETE FROM {$tbl} WHERE menu_module = '" . jrCore_db_escape($module) . "' AND menu_unique = '" . jrCore_db_escape($unique) . "' LIMIT 1";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Parses a template and returns the result
 *
 * <br><p>
 * This is one of the main functions used to move data from php out to the smarty templates.
 * anything you put in the $_rep array becomes a template variable.  so if you have $_rep['foo'] = 'bar'; then you can call
 *  &#123;$foo} in the template to produce 'bar' output.
 * </p><br>
 * <p>
 *  If you have a template in your module, the system will look for it in the /templates directory.  so call it with <br>
 *  <i>$out = jrCore_parse_template('embed.tpl',null,'jrDisqus');</i>  will call /modules/jrDisqus/templates/embed.tpl
 * </p><br>
 * <p>
 *  Skins can over-ride the modules template by defining their own version of it. so
 * /module/jrDisqus/templates/embed.tpl can be over-ridden by the skin by defining:
 * /skin/jrElastic/jrDisqus_embed.tpl
 * </p>
 * @param string $template Name of template
 * @param array $_rep (Optional) replacement variables for use in template.
 * @param string $directory default active skin directory, module directory for module/templates
 * @param bool $disable_override - set to TRUE to disable skin template override
 * @return string
 */
function jrCore_parse_template($template, $_rep = null, $directory = null, $disable_override = false)
{
    global $_conf, $_post, $_user, $_mods;
    // make sure we get smarty included
    if (!isset($GLOBALS['smarty_object']) || !is_object($GLOBALS['smarty_object'])) {

        if (!class_exists('Smarty')) {
            require_once APP_DIR . '/modules/jrCore/contrib/smarty/libs/Smarty.class.php';
        }
        // Set our compile dir
        $GLOBALS['smarty_object']             = new Smarty;
        $GLOBALS['smarty_object']->compile_id = md5(APP_DIR);
        $GLOBALS['smarty_object']->setCompileDir(APP_DIR . '/data/cache/' . $_conf['jrCore_active_skin']);

        // Get plugin directories
        $_dir = array(APP_DIR . '/modules/jrCore/contrib/smarty/libs/plugins');
        $GLOBALS['smarty_object']->setPluginsDir($_dir);
    }
    else {
        $GLOBALS['smarty_object']->clearAllAssign();
    }

    // If we are running in developer mode, make sure compiled template is removed on every call
    if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
        $GLOBALS['smarty_object']->error_reporting = (E_ALL ^ E_NOTICE);
        $GLOBALS['smarty_object']->force_compile   = true;
    }

    // Our template directory
    if (is_null($directory)) {
        $directory = $_conf['jrCore_active_skin'];
    }

    // Our Data
    $_data = array();
    if ($_rep && is_array($_rep)) {
        $_data = $_rep;
    }
    $_data['page_title']         = jrCore_get_flag('jrcore_html_page_title');
    $_data['jamroom_dir']        = APP_DIR;
    $_data['jamroom_url']        = $_conf['jrCore_base_url'];
    $_data['current_url']        = jrCore_get_current_url();
    $_data['_conf']              = $_conf;
    $_data['_post']              = $_post;
    $_data['_mods']              = $_mods;
    $_data['_user']              = (isset($_SESSION)) ? $_SESSION : $_user;
    $_data['jr_template']        = $template;
    $_data['jr_template_no_ext'] = str_replace('.tpl', '', $template);

    // Remove User and MySQL info - we don't want this to ever leak into a template
    unset($_data['_user']['user_password'], $_data['_user']['user_old_password'], $_data['_user']['user_forgot_key']);
    unset($_data['_conf']['jrCore_db_host'], $_data['_conf']['jrCore_db_user'], $_data['_conf']['jrCore_db_pass'], $_data['_conf']['jrCore_db_name'], $_data['_conf']['jrCore_db_port']);

    if (strpos($template, '.tpl') && jrCore_checktype($template, 'file_name')) {
        $file = jrCore_get_template_file($template, $directory, false, $disable_override);
        $tkey = "{$directory}_{$template}";
    }
    else {
        $file = 'string:' . $template;
        $tkey = md5($template);
    }
    // Lastly, see if we have already shown this template in this process
    $_data['template_already_shown'] = '1';
    if (!jrCore_get_flag("template_shown_{$tkey}")) {
        jrCore_set_flag("template_shown_{$tkey}", 1);
        $_data['template_already_shown'] = '0';
    }

    // Trigger for additional replacement vars
    $_data = jrCore_trigger_event('jrCore', 'template_variables', $_data, $_rep);

    // Take care of additional page elements in meta/footer
    switch ($template) {
        case 'meta.tpl':
        case 'footer.tpl':
            $_tmp = jrCore_get_flag('jrcore_page_elements');
            if ($_tmp) {
                $_data = array_merge($_data, $_tmp);
            }
            break;
    }

    $GLOBALS['smarty_object']->assign($_data);
    ob_start();
    $GLOBALS['smarty_object']->display($file);
    return ob_get_clean();
}

/**
 * Returns the proper template to use for display.  Will also create/maintain the template cache
 * @param string $template Template file to get
 * @param string $directory Name of module or skin that the template belongs to
 * @param bool $reset Set to TRUE to reset the template cache
 * @param bool $disable_override Set to TRUE to disable Skin template override of module template
 * @return mixed Returns full file path on success, bool false on failure
 */
function jrCore_get_template_file($template, $directory, $reset = false, $disable_override = false)
{
    global $_conf;
    // Check for skin override
    if (!$disable_override && is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$directory}_{$template}")) {
        $template  = "{$directory}_{$template}";
        $directory = $_conf['jrCore_active_skin'];
    }
    if (is_null($directory) || $directory === false || strlen($directory) === 0) {
        $directory = $_conf['jrCore_active_skin'];
    }

    // Trigger template event
    $_tmp = array(
        'template'  => $template,
        'directory' => $directory
    );
    $_tmp = jrCore_trigger_event('jrCore', 'template_file', $_tmp);
    if (isset($_tmp['template']{0}) && $_tmp['template'] != $template || isset($_tmp['directory']) && $_tmp['directory'] != $directory) {
        $template  = $_tmp['template'];
        $directory = $_tmp['directory'];
    }

    // We check for our "cached" template, as that will be the proper one to display
    // depending on if the admin has customized the template or not.  If we do NOT
    // have the template in our cache, we have to go get it.
    $cdir = jrCore_get_module_cache_dir('jrCore');
    $hash = md5($_conf['jrCore_active_skin'] . '-' . $directory . '-' . $template);
    $file = "{$cdir}/{$hash}.tpl";
    if (!is_file($file) || $reset || $_conf['jrCore_default_cache_seconds'] == '0' || (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on')) {

        $_rt = jrCore_get_flag("jrcore_get_template_cache");
        if (!$_rt) {
            // We need to check for a customized version of this template
            $tbl = jrCore_db_table_name('jrCore', 'template');
            $req = "SELECT CONCAT_WS('_',template_module,template_name) AS template_name, template_body FROM {$tbl} WHERE template_active = '1'";
            $_rt = jrCore_db_query($req, 'template_name');
            if ($_rt && is_array($_rt)) {
                jrCore_set_flag('jrcore_get_template_cache', $_rt);
            }
            else {
                jrCore_set_flag('jrcore_get_template_cache', 1);
            }
        }
        $key = "{$directory}_{$template}";
        if ($_rt && is_array($_rt) && isset($_rt[$key])) {
            if (!jrCore_write_to_file($file, $_rt[$key]['template_body'])) {
                jrCore_notice('CRI', "Unable to write to template cache directory: data/cache/jrCore");
            }
        }
        // Check for skin template
        elseif (is_file(APP_DIR . "/skins/{$directory}/{$template}")) {
            if (!copy(APP_DIR . "/skins/{$directory}/{$template}", $file)) {
                jrCore_notice('CRI', "Unable to copy skins/{$directory}/{$template} to template cache directory: data/cache/jrCore");
            }
        }
        // Module template
        elseif (is_dir(APP_DIR . "/modules/{$directory}/templates")) {
            if (!copy(APP_DIR . "/modules/{$directory}/templates/{$template}", $file)) {
                jrCore_notice('CRI', "Unable to copy modules/{$directory}/templates/{$template} to template cache directory: data/cache/jrCore");
            }
        }
        else {
            $_tmp = array(
                'template'  => $template,
                'directory' => $directory
            );
            $_data = jrCore_trigger_event('jrCore', 'tpl_404', $_tmp);
            if (!isset($_data['file'])) {
                jrCore_notice('CRI', "Invalid template: {$template}, or template directory: {$directory}");
            }
            $file = $_data['file'];
        }
    }
    return $file;
}

/**
 * Returns a 404 page not found
 * @return null
 */
function jrCore_page_not_found()
{
    global $_post;
    jrCore_trigger_event('jrCore', '404_not_found', $_post);
    $_ln = jrUser_load_lang_strings();
    jrCore_page_title($_ln['jrCore'][84]);
    $out = jrCore_parse_template('404.tpl', array());
    header('HTTP/1.0 404 Not Found');
    header('Connection: close');
    header('Content-Length: ' . strlen($out));
    header("Content-Type: text/html; charset=utf-8");
    ob_start();
    echo $out;
    ob_end_flush();
    exit;
}

/**
 * Create a new master CSS files from module and skin CSS files
 * @param string $skin Skin to create CSS file for
 * @return string Returns MD5 checksum of CSS contents
 */
function jrCore_create_master_css($skin)
{
    global $_conf, $_mods;
    // Make sure we get a good skin
    if (!is_dir(APP_DIR . "/skins/{$skin}")) {
        return false;
    }
    $out = '';

    // First - round up any custom CSS from modules
    $_tm = jrCore_get_registered_module_features('jrCore', 'css');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_entries) {
            if (!jrCore_module_is_active($mod) || !is_dir(APP_DIR . "/modules/{$mod}")) {
                // Skin gets added below so it can override everything it needs
                continue;
            }
            foreach ($_entries as $script => $ignore) {
                if (strpos($script, 'http') === 0 || strpos($script, '//') === 0) {
                    continue;
                }
                if (strpos($script, APP_DIR) !== 0) {
                    $script = APP_DIR . "/modules/{$mod}/css/{$script}";
                }
                if (is_file(APP_DIR . "/skins/{$skin}/css/{$mod}_{$script}")) {
                    $script = APP_DIR . "/skins/{$skin}/css/{$mod}_{$script}";
                }
                // Developer mode OR already minimized
                if (strpos($script, '.min') || isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
                    $out .= "\n/* " . str_replace(APP_DIR . '/', '', $script) . " */\n";
                    $out .= "\n\n" . @file_get_contents($script);
                }
                else {
                    $o    = false;
                    $_tmp = @file($script);
                    if ($_tmp && is_array($_tmp)) {
                        foreach ($_tmp as $line) {
                            $line = trim($line);
                            // check for start of comment
                            if (strpos($line, '/*') === 0 && !$o) {
                                if (!strpos(' ' . $line, '*/')) {
                                    // start of multi-line comment
                                    $o = true;
                                }
                                continue;
                            }
                            if ($o) {
                                // We're still in a comment - see if we are closing
                                if (strpos(' ' . $line, '*/')) {
                                    // Closed - continue
                                    $o = false;
                                }
                                continue;
                            }
                            elseif (strpos(' ' . $line, '*/')) {
                                // Closing comment tag
                                continue;
                            }
                            if (strlen($line) > 0) {
                                $out .= $line;
                            }
                        }
                    }
                }
                $out .= "\n";
            }
        }
    }

    // Skin last (so it can override modules if needed)
    if (isset($_tm[$skin]) && is_array($_tm[$skin])) {
        foreach ($_tm[$skin] as $script => $ignore) {
            if (strpos($script, 'http') === 0 || strpos($script, '//') === 0) {
                // full URLs to external sources are handled at registration time
                continue;
            }
            if (strpos($script, APP_DIR) !== 0) {
                $script = APP_DIR . "/skins/{$skin}/css/{$script}";
            }
            if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
                $out .= "\n/* " . str_replace(APP_DIR . '/', '', $script) . " */\n";
                $out .= "\n\n" . @file_get_contents($script);
            }
            else {
                $_tmp = @file($script);
                if ($_tmp && is_array($_tmp)) {
                    foreach ($_tmp as $line) {
                        $line = trim($line);
                        // Check for comment line
                        if (strpos($line, '/*') === 0 || strpos($line, '*') === 0) {
                            continue;
                        }
                        if (strlen($line) > 0) {
                            $out .= $line;
                        }
                    }
                }
            }
            $out .= "\n";
        }
    }

    // Next, get our customized style from the database
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($skin) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt['skin_custom_css']{1})) {
        $_custom = json_decode($_rt['skin_custom_css'], true);
        if ($_custom && is_array($_custom)) {
            foreach ($_custom as $sel => $_rules) {
                $out .= $sel . " {\n";
                $_cr = array();
                foreach ($_rules as $k => $v) {
                    if (!strpos($v, '!important')) {
                        $_cr[] = $k . ':' . $v . ' !important;';
                    }
                    else {
                        $_cr[] = $k . ':' . $v;
                    }
                }
                $out .= implode(' ', $_cr) . "}\n";
            }
        }
    }

    $url = $_conf['jrCore_base_url'];
    $prt = jrCore_get_server_protocol();
    if ($prt && $prt === 'https') {
        $url = str_replace('http:', 'https:', $url);
    }
    // Save file
    $sum = md5($out);
    $_rp = array(
        '{$jamroom_url}' => $url,
        '/* @ignore */'  => ''
    );
    $crl = jrCore_get_module_url('jrImage');
    foreach ($_tm as $mod => $_entries) {
        if (isset($_mods[$mod]['module_url'])) {
            $_rp['{$' . $mod . '_img_url}'] = "{$url}/{$crl}/img/module/{$mod}";
        }
        else {
            $_rp['{$' . $mod . '_img_url}'] = "{$url}/{$crl}/img/skin/{$mod}";
        }
    }
    $out = "/* {$_conf['jrCore_system_name']} css " . date('r') . " */\n" . str_replace(array_keys($_rp), $_rp, $out);
    $cdr = jrCore_get_module_cache_dir($skin);

    // Our SSL version of the CSS file is prefixed with an "S".
    if ($prt && $prt === 'https') {
        jrCore_write_to_file("{$cdr}/S{$sum}.css", $out, true);
    }
    else {
        jrCore_write_to_file("{$cdr}/{$sum}.css", $out, true);
    }

    // We need to store the MD5 of this file in the settings table - thus
    // we don't have to look it up on each page load, and we can then set
    // a VERSION on the css so our visitors will immediately see any CSS
    // changes without having to worry about a cached old version
    $_field = array(
        'name'     => "{$skin}_css_version",
        'type'     => 'hidden',
        'validate' => 'md5',
        'value'    => $sum,
        'default'  => $sum
    );
    jrCore_update_setting('jrCore', $_field);
    return $sum;
}

/**
 * jrCore_create_master_javascript
 * @param string $skin Skin to create Javascript file for
 * @return string Returns MD5 checksum of Javascript contents
 */
function jrCore_create_master_javascript($skin)
{
    global $_conf, $_urls;
    // Make sure we get a good skin
    if (!is_dir(APP_DIR . "/skins/{$skin}")) {
        return false;
    }

    // Create our output
    require_once APP_DIR . '/modules/jrCore/contrib/jsmin/jsmin.php';

    // Create our output
    $kurl = $_conf['jrCore_base_url'];
    $kprt = jrCore_get_server_protocol();
    if ($kprt && $kprt === 'https') {
        $kurl = str_replace('http:', 'https:', $kurl);
    }
    $out = "var jrImage_url='" . jrCore_get_module_url('jrImage') . "';\n";

    // We keep track of the MP5 hash of every JS script we include - this
    // keeps us from including the same JS from different modules
    $_hs = array();

    // First - round up any custom JS from modules
    $_tm = jrCore_get_registered_module_features('jrCore', 'javascript');
    // Add in custom module javascript
    if ($_tm && is_array($_tm)) {
        $_ur = array_flip($_urls);
        $_dn = array();
        foreach ($_tm as $mod => $_entries) {
            if ($mod == $skin || !jrCore_module_is_active($mod)) {
                continue;
            }
            $url = $_ur[$mod];
            if (!isset($_dn[$url])) {
                $out .= "var {$mod}_url='{$url}';\n";
                $_dn[$url] = 1;
            }
        }
        foreach ($_tm as $mod => $_entries) {
            if ($mod == $skin || !jrCore_module_is_active($mod)) {
                continue;
            }
            foreach ($_entries as $script => $ignore) {
                // NOTE: Javascript that is external the JR system is loaded in the jrCore_enable_external_javascript() function
                if (strpos($script, 'http') === 0 || strpos($script, '//') === 0) {
                    continue;
                }
                if (strpos($script, APP_DIR) !== 0) {
                    $script = APP_DIR . "/modules/{$mod}/js/{$script}";
                }
                if (is_file(APP_DIR ."/skins/{$skin}/js/{$mod}_{$script}")) {
                    $script = APP_DIR ."/skins/{$skin}/js/{$mod}_{$script}";
                }
                $tmp = @file_get_contents($script);
                // This MD5 check ensures we don't include the same JS script 2 times from different modules
                $key = md5($tmp);
                if (!isset($_hs[$key])) {
                    if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
                        $out .= "{$tmp}\n\n";
                    }
                    else {
                        if (!strpos($script, '.min')) {
                            $out .= JSMin::minify($tmp) . "\n\n";
                        }
                        else {
                            $out .= "{$tmp}\n\n";
                        }
                    }
                    $_hs[$key] = 1;
                }
            }
        }
    }

    // Skin last (so it can override modules if needed)
    if (isset($_tm[$skin]) && is_array($_tm[$skin])) {
        foreach ($_tm[$skin] as $script => $ignore) {
            if (strpos($script, 'http') === 0 || strpos($script, '//') === 0) {
                continue;
            }
            if (strpos($script, APP_DIR) !== 0) {
                $script = APP_DIR . "/skins/{$skin}/js/{$script}";
            }
            $tmp = @file_get_contents($script);
            $key = md5($tmp);
            if (!isset($_hs[$key])) {
                if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
                    $out .= "{$tmp}\n\n";
                }
                else {
                    if (!strpos($script, '.min')) {
                        $out .= JSMin::minify($tmp) . "\n\n";
                    }
                    else {
                        $out .= "{$tmp}\n\n";
                    }
                }
                $_hs[$key] = 1;
            }
        }
    }

    // Save file
    $cdr = jrCore_get_module_cache_dir($skin);
    $sum = md5($out);
    $out = "/* {$_conf['jrCore_system_name']} js */\nvar core_system_url='{$kurl}';\nvar core_active_skin='{$skin}';\n{$out}";
    if ($kprt && $kprt === 'https') {
        jrCore_write_to_file("{$cdr}/S{$sum}.js", $out, true);
    }
    else {
        jrCore_write_to_file("{$cdr}/{$sum}.js", $out, true);
    }

    // We need to store the MD5 of this file in the settings table - thus
    // we don't have to look it up on each page load, and we can then set
    // a VERSION on the js so our visitors will immediately see any JS
    // changes without having to worry about a cached old version
    $_field = array(
        'name'     => "{$skin}_javascript_version",
        'type'     => 'hidden',
        'validate' => 'md5',
        'value'    => $sum,
        'default'  => $sum
    );
    jrCore_update_setting('jrCore', $_field);
    return $sum;
}

