/* 
 *  Copyright @ 2016 Diego Garcia
 */

window.addEventListener("load", function () {
    $("#newsFormSubmit").click(function () {
        $('#newsFormSubmit').attr('disabled', 'disabled');
        var news_author = $("#news_author").val();
        var news_title = $("#news_title").val();
        var news_text = $("#news_text").val();

        if ($("#tos").is(':checked')) {
            var tos = 1;
        } else {
            var tos = 0;
        }

        $('#tos').css("box-shadow", "0 0 0px black");
        $('#tos').css("border", "1px solid black");
        $('#news_author').css("box-shadow", "0 0 0px black");
        $('#news_author').css("border", "1px solid black");
        $('#news_title').css("box-shadow", "0 0 0px black");
        $('#news_title').css("border", "1px solid black");
        $('#news_text').css("box-shadow", "0 0 0px black");
        $('#news_text').css("border", "1px solid black");

        if (news_author == '') {
            $('#news_author').css("border", "5px solid red");
            $('#news_author').css("box-shadow", "0 0 5px red");
            alert("Author can't be empty");
        } else if (news_title == '') {
            $('#news_title').css("border", "5px solid red");
            $('#news_title').css("box-shadow", "0 0 5px red");
            alert("Title can't be empty");
        } else if (news_text == '') {
            $('#news_text').css("border", "5px solid red");
            $('#news_text').css("box-shadow", "0 0 5px red");
            alert("Text can't be empty");
        } else if (tos !== 1) {
            $('#tos').css("border", "10px solid red");
            $('#tos').css("box-shadow", "0 0 10px red");
            alert("You must accept the terms of service");
        } else {
            $.post("", $("#form_news").serialize(),
                    function (data) {
                        console.log(data); //DEBUG
                        var json = $.parseJSON(data);
                        if (json[0].status === 'ok') {
                            alert(json[0].msg);
                            $("form")[0].reset();
                            $(location).attr('href', json[0].url);
                        } else if (json[0].status == 1) { //Internal
                            alert(json[0].msg);
                        } else if (json[0].status == 2) {
                            $('#news_author').css("border", "2px solid red");
                            $('#news_author').css("box-shadow", "0 0 3px red");
                            alert(json[0].msg);
                        } else if (json[0].status == 3) {
                            $('#news_title').css("border", "2px solid red");
                            $('#news_title').css("box-shadow", "0 0 3px red");
                            alert(json[0].msg);
                        } else if (json[0].status == 5) {
                            $('#news_text').css("border", "2px solid red");
                            $('#news_text').css("box-shadow", "0 0 3px red");
                            alert(json[0].msg);
                        } else {
                            alert(json[0].msg);
                        }
                    });
        }
        $('#newsFormSubmit').removeAttr("disabled");
        return false;
    });
});