/* 
 *  Copyright @ 2016 Diego Garcia
 */

window.addEventListener("load", function () {
    $('#optional_switcher').change(function () {
        if ($(this).is(":checked")) {
            $('#optional_profile_fields').css("display", "block");
        } else {
            $('#optional_profile_fields').css("display", "none");
        }
    });
});