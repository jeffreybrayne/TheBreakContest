<?php
/**
 * Jamroom 5 Image Support module
 *
 * copyright 2003 - 2014
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrImage_config
 *
 */
function jrImage_config()
{
    // Convert to JPG
    $_tmp = array(
        'name'     => 'convert_to_jpg',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Convert to JPG',
        'help'     => 'PNG and GIF images can be very large when uploaded - check this option so uploaded PNG and GIF images are converted to the JPG format before being saved - this can significantly reduce the disk space required for cached image thumbnails.',
        'order'    => 1
    );
    jrCore_register_setting('jrImage', $_tmp);

    // Cache Cleanup
    $_opt = array(
        '0'  => 'disabled',
        '1'  => '24 hours',
        '2'  => '48 hours',
        '3'  => '3 days',
        '7'  => '7 days',
        '14' => '14 days',
        '30' => '30 days'
    );
    $_tmp = array(
        'name'     => 'clean_days',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '1',
        'validate' => 'number_nn',
        'label'    => 'Delete Cached Images',
        'help'     => 'If a cached image has not been viewed in the time duration specified here, the cached version of the image will be removed.  This can help cut down on the amount of space used by cached images.',
        'order'    => 2
    );
    jrCore_register_setting('jrImage', $_tmp);

    return true;
}
