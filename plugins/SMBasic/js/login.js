/* 
 *  Copyright @ 2016 Diego Garcia
 */


$(document).ready(function(){
    $("#login").click(function(){
        //Email Validation
        
        var reg = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;

        var email = $("#email").val();
        var password = $("#password").val();
        var rememberme = $('#rememberme').is(':checked'); 
        // Checking for blank fields.
            $('input[type="text"]').css("border","1px solid black");
            $('input[type="text"]').css("box-shadow","0 0 0px black");
            $('input[type="password"]').css("border","1px solid black");
            $('input[type="password"]').css("box-shadow","0 0 0px black");            
        if( email == '' ) {              
            $('input[type="text"]').css("border","2px solid red");
            $('input[type="text"]').css("box-shadow","0 0 3px red");
            alert("Email es obligatorio");
        } else if(reg.test(email) == false ) {
            $('input[type="text"]').css("border","2px solid red");
            $('input[type="text"]').css("box-shadow","0 0 3px red");         
            alert("Email incorrecto");        
        } else if( password == '' ) {
                $('input[type="password"]').css("border","2px solid red");
                $('input[type="password"]').css("box-shadow","0 0 3px red");                            
                alert("Password es obligatorio");
        } else if( password.length < 8 ){
            $('input[type="password"]').css("border","2px solid red");
            $('input[type="password"]').css("box-shadow","0 0 3px red");
            alert("La contraseña tiene que tener más de 8 caracteres");                                
        } else {
            
            $.post("login.php",{ email1: email, password1:password, rememberme1:rememberme},
            function(data) {
                //alert(data); //DEBUG
                var json = $.parseJSON(data);
                if(json[0].status == 'ok') {
                    $("form")[0].reset();                
                    $(location).attr('href', json[0].msg);
                } else {
                    $('input[type="text"],input[type="password"]').css({"border":"2px solid red","box-shadow":"0 0 3px red"});
                    alert(json[0].msg);
                    return false;
                }
            });
            
        }
        return false;
    });

});