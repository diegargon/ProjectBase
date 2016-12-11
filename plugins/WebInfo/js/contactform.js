/* 
 *  Copyright @ 2016 Diego Garcia
 */
window.addEventListener("load", function () {
    $("#btnSend").click(function () {
        $('#btnSend').attr('disabled', 'disabled');
        //Email Validation        
        var reg = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;

        var email = $("#email").val();
        var name = $("#name").val();
        var subject = $("#subject").val();
        var message = $("#message").val();

        //reset red borders
        $('#email').css("border", "1px solid black");
        $('#email').css("box-shadow", "0 0 0px black");
        $('#name').css("border", "1px solid black");
        $('#name').css("box-shadow", "0 0 0px black");
        $('#subject').css("border", "1px solid black");
        $('#subject').css("box-shadow", "0 0 0px black");
        $('#message').css("border", "1px solid black");
        $('#message').css("box-shadow", "0 0 0px black");

        // Checking for blank fields.
        if (email == '') {
            $('#email').css("border", "2px solid red");
            $('#email').css("box-shadow", "0 0 3px red");
            alert("Email is required");
        } else if (reg.test(email) == false) {
            $('#email').css("border", "2px solid red");
            $('#email').css("box-shadow", "0 0 3px red");
            alert("Email incorrect");
        } else if (name == '') {
            $('#name').css("border", "2px solid red");
            $('#name').css("box-shadow", "0 0 3px red");
            alert("Name its mandatory");
        } else if (subject == '') {
            $('#subject').css("border", "2px solid red");
            $('#subject').css("box-shadow", "0 0 3px red");
            alert("Subject its mandatory");
        } else if (message == '') {
            $('#message').css("border", "2px solid red");
            $('#message').css("box-shadow", "0 0 3px red");
            alert("Message its mandatory");
        } else {
            $.post("", $("#contact_form").serialize() + '&submit=1',
                    function (data) {
                        console.log(data); //DEBUG
                        var json = $.parseJSON(data);
                        if (json.status == 'ok') {
                            $("#contact_form")[0].reset();
                            $("#contact_form").hide();
                            $("#info-panel span").text(json.msg);
                        } else if (json.status == 1) {
                            $('#email').css("border", "2px solid red");
                            $('#email').css("box-shadow", "0 0 3px red");
                            alert(json.msg);
                        } else if (json.status == 2) {
                            $('#name').css("border", "2px solid red");
                            $('#name').css("box-shadow", "0 0 3px red");
                            alert(json.msg);
                        } else if (json.status == 3) {
                            $('#subject').css("border", "2px solid red");
                            $('#subject').css("box-shadow", "0 0 3px red");
                            alert(json.msg);
                        } else if (json.status == 4) {
                            $('#message').css("border", "2px solid red");
                            $('#message').css("box-shadow", "0 0 3px red");
                            alert(json.msg);
                        } else {
                            alert(json.msg);
                        }
                    });
        }
        $('#btnSend').removeAttr("disabled");
        return false;
    });
});