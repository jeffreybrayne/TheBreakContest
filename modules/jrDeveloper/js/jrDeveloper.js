// Jamroom 5 Developer Module Javascript
// @copyright 2003-2015 by Talldude Networks LLC

/**
 * Get license for skin/module
 * @param type string License Type
 * @param dir string Module/Skin directory name
 */
function jrDeveloper_get_license(type, dir)
{
    var url = core_system_url + '/' + jrDeveloper_url + '/get_license/type=' + jrE(type) + '/dir=' + jrE(dir) + '/__ajax=1';
    $.get(url, function (res) {
        if (typeof res.error != "undefined") {
            $('#zip_license_error').hide();
            alert(res.error);
        }
        else if (typeof res.empty != "undefined") {
            $('#zip_license_error').show();
        }
        else {
            $('#zip_license_error').hide();
            $('#zip_license').val(res.success);
        }
    });
}