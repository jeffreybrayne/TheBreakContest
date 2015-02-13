<?php
/**
 * Jamroom 5 Installer
 * copyright 2003 - 2014 by The Jamroom Network - All Rights Reserved
 * http://www.jamroom.net
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * Jamroom includes works that are not developed by The Jamroom Network
 * and are used under license - copies of all licenses are included and
 * can be found in the "contrib" directory within the module, as well
 * as within the "license.html" file.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license than the Jamroom
 * Core - please reference the individual module or skin license that
 * is included with your download.
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
 */

// Define our base dir
define('APP_DIR', dirname(__FILE__));
define('IN_JAMROOM_INSTALLER', 1);
define('MARKETPLACE_URL', 'http://www.jamroom.net/networkmarket/create_user');

// Distribution specific
if (is_dir(APP_DIR ."/skins/jrElastic")) {
    define('DEFAULT_JAMROOM_SKIN', 'jrElastic');
}
else {
    define('DEFAULT_JAMROOM_SKIN', 'jrElastic');
}

// Distribution Name
$dist = 'Jamroom';
if (strpos(' ' . $dist, '%%')) {
    define('DISTRIBUTION_NAME', 'Jamroom');
}
else {
    define('DISTRIBUTION_NAME', $dist);
}

// Typically no need to edit below here
date_default_timezone_set('UTC');
ini_set('session.auto_start', 0);
ini_set('session.use_trans_sid', 0);
ini_set('display_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
session_start();

// Bring in core functionality
$_conf = array();
require_once APP_DIR . "/modules/jrCore/include.php";

// Default permissions
$_conf['jrCore_dir_perms']  = 0755;
$_conf['jrCore_file_perms'] = 0644;

// Check for already being installed
if (is_file(APP_DIR . '/data/config/config.php')) {
    echo 'ERROR: Config file found - ' . DISTRIBUTION_NAME . ' already appears to be installed';
    exit;
}

// Check PHP version
$min = '5.3.0';
if (version_compare(phpversion(), $min) == -1) {
    echo "ERROR: " . DISTRIBUTION_NAME . " requires PHP {$min} or newer - you are currently running PHP version " . phpversion() . " - contact your hosting provider and see if they can upgrade your PHP install to a newer release";
    exit;
}

// Make sure we have session support
if (!function_exists('session_start')) {
    echo 'ERROR: PHP does not appear to have Session Support - ' . DISTRIBUTION_NAME . ' requires PHP Session Support in order to work. Please contact your system administrator and have Session Support activated in your PHP.';
    exit;
}

// Check for skin install
if (!is_file(APP_DIR ."/skins/" . DEFAULT_JAMROOM_SKIN . "/include.php")) {
    echo 'ERROR: default skin directory skins/' . DEFAULT_JAMROOM_SKIN .' not found - check that all files have been uploaded';
    exit;
}

// Load modules
$_mods = array('jrCore' => jrCore_meta());
$_urls = array('core' => 'jrCore');
if (is_dir(APP_DIR . "/modules")) {
    if ($h = opendir(APP_DIR . "/modules")) {
        while (($file = readdir($h)) !== false) {
            if ($file == 'index.html' || $file == '.' || $file == '..' || $file == 'jrCore') {
                continue;
            }
            if ((is_link($file) || (is_dir(APP_DIR . "/modules/{$file}") && !strpos($file, '-release-'))) && is_file(APP_DIR . "/modules/{$file}/include.php")) {
                require_once APP_DIR . "/modules/{$file}/include.php";
            }
            $mfunc = "{$file}_meta";
            if (function_exists($mfunc)) {
                $_mods[$file] = $mfunc();
                $murl = $_mods[$file]['url'];
                $_urls[$murl] = $file;
            }
        }
    }
    closedir($h);
}

// kick off installer
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'install') {
    jrInstall_install_system();
}
else {
    jrInstall_show_install_form();
}

/**
 * jrInstall_show_install_form
 */
function jrInstall_show_install_form()
{
    jrInstall_header();
    jrInstall_install_form();
    jrInstall_footer();
}

/**
 * jrInstall_show_install_form
 */
function jrInstall_install_system()
{
    global $_conf, $_mods;
    sleep(1);

    // Setup session
    $_todo = array(
        'base_url' => 'System URL',
        'db_host'  => 'Database Host',
        'db_port'  => 'Database Port',
        'db_name'  => 'Database Name',
        'db_user'  => 'Database User',
        'db_pass'  => 'Database User Password',
        'email'    => 'Email Address'
    );
    foreach ($_todo as $k => $v) {
        if (isset($_REQUEST[$k]) && strlen($_REQUEST[$k]) > 0) {
            $_SESSION[$k] = $_REQUEST[$k];
        }
        elseif ($k != 'email') {
            $_SESSION['install_error'] = 'You have entered an invalid value for ' . $v . ' - please enter a valid value';
            $_SESSION['install_hilight'] = $k;
            jrCore_location('install.php');
        }
    }

    // Write out our database stuff
    $config = APP_DIR . "/data/config/config.php";
    if (!is_file($config)) {
        touch($config);
        if (!is_file($config)) {
            $_SESSION['install_error'] = 'data/config/config.php does not exist, and cannot be opened or created - please create the config.php file';
            jrCore_location('install.php');
        }
        unlink($config);
    }

    // Try to connect to MySQL
    if (!function_exists('mysqli_init')) {
        $_SESSION['install_error'] = 'Unable to initialize MySQLi support - please check your PHP config for MySQLi support';
        jrCore_location('install.php');
    }
    $myi = mysqli_init();
    if (!$myi) {
        $_SESSION['install_error'] = 'Unable to initialize MySQLi support - please check your PHP config for MySQLi support';
        jrCore_location('install.php');
    }
    if (!mysqli_real_connect($myi, $_REQUEST['db_host'], $_REQUEST['db_user'], $_REQUEST['db_pass'], $_REQUEST['db_name'], $_REQUEST['db_port'], null, MYSQLI_CLIENT_FOUND_ROWS)) {
        // If it is still at "localhost", try "127.0.0.1"
        if ($_REQUEST['db_host'] == 'localhost') {
            $_REQUEST['db_host'] = '127.0.0.1';
        }
        if (!mysqli_real_connect($myi, $_REQUEST['db_host'], $_REQUEST['db_user'], $_REQUEST['db_pass'], $_REQUEST['db_name'], $_REQUEST['db_port'], null, MYSQLI_CLIENT_FOUND_ROWS)) {
            $_SESSION['install_error'] = 'Unable to connect to the MySQL database using the credentials provided - please check:<br>MySQL error: ' . mysqli_connect_error();
            jrCore_location('install.php');
        }
    }

    // Create config file
    $data = "<?php\n\$_conf['jrCore_db_host'] = '" . $_REQUEST['db_host'] . "';\n\$_conf['jrCore_db_port'] = '" . $_REQUEST['db_port'] . "';\n\$_conf['jrCore_db_name'] = '" . $_REQUEST['db_name'] . "';\n\$_conf['jrCore_db_user'] = '" . $_REQUEST['db_user'] . "';\n\$_conf['jrCore_db_pass'] = '" . $_REQUEST['db_pass'] . "';\n\$_conf['jrCore_base_url'] = '" . $_REQUEST['base_url'] . "';\n";
    jrCore_write_to_file($config, $data);

    // Bring it in for install
    require_once $config;

    // Init Core first
    $_conf['jrCore_active_skin'] = DEFAULT_JAMROOM_SKIN;
    jrCore_init();
    foreach ($_mods as $mod_dir => $_inf) {
        if ($mod_dir != 'jrCore') {
            $ifunc = "{$mod_dir}_init";
            if (function_exists($ifunc)) {
                $ifunc();
            }
        }
    }

    // schema
    require_once APP_DIR . "/modules/jrCore/schema.php";
    jrCore_db_schema();
    foreach ($_mods as $mod_dir => $_inf) {
        if ($mod_dir != 'jrCore') {
            if (is_file(APP_DIR . "/modules/{$mod_dir}/schema.php")) {
                require_once APP_DIR . "/modules/{$mod_dir}/schema.php";
                $func = "{$mod_dir}_db_schema";
                if (function_exists($func)) {
                    $func();
                }
            }
        }
    }

    foreach ($_mods as $mod_dir => $_inf) {

        // config
        if (is_file(APP_DIR . "/modules/{$mod_dir}/config.php")) {
            require_once APP_DIR . "/modules/{$mod_dir}/config.php";
            $func = "{$mod_dir}_config";
            if (function_exists($func)) {
                $func();
            }
        }

        // quota
        if (is_file(APP_DIR . "/modules/{$mod_dir}/quota.php")) {
            require_once APP_DIR . "/modules/{$mod_dir}/quota.php";
            $func = "{$mod_dir}_quota_config";
            if (function_exists($func)) {
                $func();
            }
        }

        // lang strings
        if (is_dir(APP_DIR . "/modules/{$mod_dir}/lang")) {
            jrUser_install_lang_strings('module', $mod_dir);
        }
    }

    // Create first profile quota
    $qid = jrProfile_create_quota('example quota');

    // Build modules
    $_feat = jrCore_get_registered_module_features('jrCore', 'quota_support');
    foreach ($_mods as $mod_dir => $_inf) {
        jrCore_verify_module($mod_dir);
        // Turn on Quota if this module has quota options
        if (isset($_feat[$mod_dir])) {
            jrProfile_set_quota_value($mod_dir, $qid, 'allowed', 'on');
        }
        $_mods[$mod_dir]['module_active'] = 1;
    }

    // Setup skins
    $_skns = jrCore_get_skins();
    if (isset($_skns) && is_array($_skns)) {
        foreach ($_skns as $sk) {
            if (is_file(APP_DIR . "/skins/{$sk}/include.php")) {
                require_once APP_DIR . "/skins/{$sk}/include.php";
                $func = "{$sk}_skin_init";
                if (function_exists($func)) {
                    $func();
                }
            }
        }
        foreach ($_skns as $sk) {
            if (is_file(APP_DIR . "/skins/{$sk}/config.php")) {
                require_once APP_DIR . "/skins/{$sk}/config.php";
                $func = "{$sk}_skin_config";
                if (function_exists($func)) {
                    $func();
                }
            }
        }
        foreach ($_skns as $sk) {
            // Install Language strings for Skin
            jrUser_install_lang_strings('skin', $sk);
        }
    }

    // Turn on Sign ups for the first quota
    jrProfile_set_quota_value('jrUser', 1, 'allow_signups', 'on');

    // Activate all modules....
    $tbl = jrCore_db_table_name('jrCore', 'module');
    $req = "UPDATE {$tbl} SET module_active = '1'";
    jrCore_db_query($req);

    // Now we need to full reload conf here since we only have core
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT module AS m, name AS k, value AS v FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    // Make sure we got settings
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_notice('CRI', "unable to initialize any settings - very installation");
    }
    foreach ($_rt as $_s) {
        $_conf["{$_s['m']}_{$_s['k']}"] = $_s['v'];
    }

    // Set default skin
    jrCore_set_setting_value('jrCore', 'active_skin', DEFAULT_JAMROOM_SKIN);
    $_conf['jrCore_default_skin'] = DEFAULT_JAMROOM_SKIN;

    // Set skin CSS and JS for our default skin
    jrCore_create_master_css(DEFAULT_JAMROOM_SKIN);
    jrCore_create_master_javascript(DEFAULT_JAMROOM_SKIN);

    // On a new install we just enable all modules for all quotas
    $tbl = jrCore_db_table_name('jrProfile', 'quota_setting');
    $req = "UPDATE {$tbl} SET `default` = 'on' WHERE `name` = 'allowed'";
    jrCore_db_query($req);

    // If the user entered a valid email address, setup their Marketplace if we can
    if (isset($_REQUEST['email']) && jrCore_checktype($_REQUEST['email'], 'email')) {
        $res = jrCore_load_url(MARKETPLACE_URL . '/email=' . jrCore_url_encode_string($_REQUEST['email']));
        if ($res && strpos($res, 'user_system_id')) {
            $_tm = json_decode($res, true);
            if (isset($_tm['user_system_id'])) {

                // Update Marketplace
                $tbl = jrCore_db_table_name('jrMarket', 'system');
                $req = "UPDATE {$tbl} SET system_email = '" . jrCore_db_escape($_REQUEST['email']) ."', system_code = '" . jrCore_db_escape($_tm['user_system_id']) ."' WHERE system_id = '1'";
                jrCore_db_query($req);

                // Update Support Center
                jrCore_set_setting_value('jrSupport', 'support_email', $_REQUEST['email']);
            }
        }
    }

    jrCore_notice_page('success', '<br>' . DISTRIBUTION_NAME . ' has been successfully installed!<br><br>', $_REQUEST['base_url'], 'Continue', false);
    session_destroy();
}

/**
 * jrInstall_header
 */
function jrInstall_header()
{
    echo '
    <!doctype html>
    <html lang="en" dir="ltr">
    <head>
    <title>' . DISTRIBUTION_NAME . ' Installer</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script type="text/javascript" src="modules/jrCore/js/jquery-1.11.0.min.js"></script>
    <style type="text/css">
    ';

    // Bring in style sheets
    $_css = glob(APP_DIR . '/skins/' . DEFAULT_JAMROOM_SKIN . '/css/*.css');
    foreach ($_css as $css_file) {
        // {$jrElastic_img_url}
        $_rep = array(
            '{$jamroom_url}/'                         => '',
            '{$' . DEFAULT_JAMROOM_SKIN . '_img_url}' => 'skins/' . DEFAULT_JAMROOM_SKIN . '/img'
        );
        echo str_replace(array_keys($_rep), $_rep, file_get_contents($css_file));
    }

    // Check for install logo
    if (is_file(APP_DIR . '/skins/' . DEFAULT_JAMROOM_SKIN . '/img/install_logo.png')) {
        $logo = 'skins/' . DEFAULT_JAMROOM_SKIN . '/img/install_logo.png';
    }
    else {
        $logo = 'modules/jrCore/img/install_logo.png';
    }

    echo '
    </style>
    </head>
    <body id="installer">

    <div id="header">
        <div id="header_content">
            <div class="container">
                <div class="row">
                    <div class="col4">
                        <div id="main_logo" style="padding:0">
                            <img src="' . $logo . '" width="280" height="55" alt="' . DISTRIBUTION_NAME . '" style="vertical-align:middle">
                        </div>
                    </div>
                    <div class="col8 last">
                        <div style="width:94%;padding:18px;text-align:right">
                            Welcome to the ' . DISTRIBUTION_NAME . ' Installer!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="wrapper" style="padding-bottom:0">
      <div id="content">';
    return true;
}

/**
 * jrInstall_install_notice
 */
function jrInstall_install_notice($type, $text)
{
    echo '<tr><td colspan="2" class="page_notice_drop"><div id="page_notice" class="page_notice ' . $type . '">' . $text . '</div></td></tr>';
    return true;
}

/**
 * jrInstall_install_form
 */
function jrInstall_install_form()
{
    global $_conf;
    $disabled = '';
    echo '
    <div class="container">
      <div class="row">
        <div class="col12 last">
          <div style="padding:12px">
            <form id="install" method="post" action="install.php?action=install" accept-charset="utf-8" enctype="multipart/form-data">
            <table class="page_content">
              <tr>
                <td colspan="2" class="element page_note">
                  Thank you for downloading ' . DISTRIBUTION_NAME . '! Fill out the following database settings, and you will be up and running in 30 seconds.<br><small>(If you are not sure what your settings are, contact your hosting provider and they can tell you)</small>
                </td>
              </tr>
              <tr><td>&nbsp;</td></tr>
              <tr>
                <td class="element_left form_input_left">
                  License
                </td>
                <td class="element_right form_input_right" style="height:160px">
                  <iframe src="modules/jrCore/license.html" style="width:76%;height:160px;border:1px solid #7F7F7F;border-radius:3px;box-shadow: inset 0 0 2px #111;"></iframe>
                </td>
              </tr>';

    // Test to make sure our server is setup properly
    if (!is_dir(APP_DIR . '/data')) {
        jrInstall_install_notice('error', "&quot;data&quot; directory does not exist - create data directory and permission so web user can write to it");
        $disabled = ' disabled="disabled"';
    }
    // Check each dir
    $_dirs = array('cache', 'config', 'logs', 'media');
    $error = array();
    foreach ($_dirs as $dir) {
        $fdir = APP_DIR . "/data/{$dir}";
        if (!is_dir($fdir)) {
            mkdir($fdir, $_conf['jrCore_dir_perms']);
            if (!is_dir($fdir)) {
                $error[] = "data/{$dir}";
            }
        }
        elseif (!is_writable($fdir)) {
            chmod($fdir, $_conf['jrCore_dir_perms']);
            if (!is_writable($fdir)) {
                $error[] = "data/{$dir}";
            }
        }
    }
    if (isset($error) && is_array($error) && count($error) > 0) {
        jrInstall_install_notice('error', "The following directories are not writable:<br>" . implode('<br>', $error) . "<br>ensure they are permissioned so the web user can write to them");
        $disabled = ' disabled="disabled"';
    }

    // mod_rewrite check
    if (function_exists('apache_get_modules') && function_exists('php_sapi_name') && stristr(php_sapi_name(), 'apache')) {
        if (!in_array('mod_rewrite', apache_get_modules())) {
            jrInstall_install_notice('error', 'mod_rewrite does not appear to be enabled on your server - mod_rewrite is required for ' . DISTRIBUTION_NAME . ' to function.<br>Contact your hosting provider and ensure mod_rewrite is active in your account.');
        }
    }

    // Check for disabled functions
    $_funcs = array('system', 'json_encode', 'json_decode', 'ob_start', 'ob_end_clean', 'curl_init', 'curl_version', 'gd_info');
    $_flist = array();
    foreach ($_funcs as $rfunc) {
        if (!function_exists($rfunc)) {
            $_flist[] = $rfunc;
        }
    }
    if (isset($_flist) && is_array($_flist) && count($_flist) > 0) {
        jrInstall_install_notice('error', "The following function(s) are not enabled in your PHP install:<br><br><b>" . implode('</b><br><b>', $_flist) . "</b><br><br>" . DISTRIBUTION_NAME . " will not function properly without these functions enabled so contact your hosting provider and make sure they are enabled.");
        $disabled = ' disabled="disabled"';
    }

    // Check that ffmpeg works
    if (!jrCore_check_ffmpeg_install(false)) {
        jrInstall_install_notice('error', "The FFMpeg binary located at modules/jrCore/tools/ffmpeg does not appear to be executable - FFMpeg is required for audio and video support in " . DISTRIBUTION_NAME . ". After installation ensure FFMpeg can be executed via a system() function call.");
    }

    // Make sure .htaccess exists
    if (stristr($_SERVER['SERVER_SOFTWARE'], 'apache') && !is_file(APP_DIR . "/.htaccess")) {
        jrInstall_install_notice('error', "Unable to find the .htaccess file - please ensure the .htaccess from the " . DISTRIBUTION_NAME . " ZIP file is uploaded to your server.");
        $disabled = ' disabled="disabled"';
    }

    // Check for session errors
    if (isset($_SESSION['install_error'])) {
        jrInstall_install_notice('error', $_SESSION['install_error']);
        unset($_SESSION['install_error']);
    }

    if (!isset($_SESSION['base_url']{1})) {
        $_SESSION['base_url'] = preg_replace('/\/$/', '', 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']));
    }
    if (!isset($_SESSION['db_host'])) {
        $_SESSION['db_host'] = 'localhost';
    }
    if (!isset($_SESSION['db_port'])) {
        $_SESSION['db_port'] = '3306';
    }

    jrInstall_text_field('text', 'System URL', 'base_url', $_SESSION['base_url']);
    jrInstall_text_field('text', 'Database Host', 'db_host', $_SESSION['db_host']);
    jrInstall_text_field('text', 'Database Port<br><span class="sublabel">(default: 3306)</span>', 'db_port', $_SESSION['db_port']);
    jrInstall_text_field('text', 'Database Name', 'db_name', $_SESSION['db_name']);
    jrInstall_text_field('text', 'Database User', 'db_user', $_SESSION['db_user']);
    jrInstall_text_field('password', 'Database User Password', 'db_pass', $_SESSION['db_pass']);
    jrInstall_text_field('text', 'Email Address<br><span class="sublabel">(for Marketplace updates)</span>', 'email', $_SESSION['email']);

    $refresh = '';
    $disclass = '';
    if (isset($disabled) && strlen($disabled) > 0) {
        $disclass = ' form_button_disabled';
        $refresh = '<input type="button" value="Check Again" class="form_button" onclick="location.reload();">';
    }
    echo '    <tr><td style="height:12px"></td></tr><tr>
                <td colspan="2" class="element form_submit_section">
                  <img id="form_submit_indicator" src="skins/' . DEFAULT_JAMROOM_SKIN . '/img/form_spinner.gif" width="24" height="24" alt="working...">' . $refresh . '
                  <input type="button" value="Install ' . DISTRIBUTION_NAME . '" class="form_button' . $disclass . '"' . $disabled . ' onclick="if (confirm(\'Please be patient - the installion can take up to 30 seconds to run. Are you ready to install?\')){$(\'#form_submit_indicator\').show(300,function(){ $(\'#install\').submit(); });}">
                </td>
              </tr>  
            </table>
            </form>
          </div>
        </div>
      </div>
    </div>';
    return true;
}

/**
 * jrInstall_text_field
 */
function jrInstall_text_field($type, $label, $name, $value = '')
{
    $cls = '';
    if (isset($_SESSION['install_hilight']) && $_SESSION['install_hilight'] == $name) {
        $cls = ' field-hilight';
        unset($_SESSION['install_hilight']);
    }
    echo '<tr><td class="element_left form_input_left">' . $label . '</td><td class="element_right form_input_right">';
    switch ($type) {
        case 'text':
            echo '<input type="text" name="' . $name . '" value="' . $value . '" class="form_text' . $cls . '"></td></tr>';
            break;
        case 'password':
            echo '<input type="password" name="' . $name . '" value="' . $value . '" class="form_text' . $cls . '"></td></tr>';
            break;
    }
    return true;
}

/**
 * jrInstall_footer
 */
function jrInstall_footer()
{
    echo '</div>
    <div id="footer">
        <div id="footer_content">
            <div class="container">
                <div class="row">
                    <div class="col12 last">
                        <div id="footer_text">Powered by <a href="http://www.jamroom.net">Jamroom</a><br><span class="sublabel">&copy;' . strftime('%Y') . ' The Jamroom Network</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>';
    return true;
}
?>
