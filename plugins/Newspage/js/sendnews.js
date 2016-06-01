/* 
 *  Copyright @ 2016 Diego Garcia
 */
$(document).ready(function(){
    $("#sendnews").click(function(){
        var news_author = $("#news_author").val();
        var news_title = $("#news_title").val();
        var news_lead = $("#news_lead").val();
        var news_text = $("#news_text").val();        
        var news_lang = $("#news_lang").val();
        var sendnews = $("#sendnews").val();
        
            $('#news_author').css("border","1px solid black");
            $('#news_author').css("box-shadow","0 0 3px black");
            $('#news_title').css("border","1px solid black");          
            $('#news_title').css("box-shadow","0 0 3px black");
            $('#news_lead').css("border","1px solid black");
            $('#news_lead').css("box-shadow","0 0 3px black");
            $('#news_text').css("border","1px solid black");
            $('#news_text').css("box-shadow","0 0 3px black");            
           
            $.post("",{ news_author1: news_author, news_title1: news_title, news_lead1: news_lead, news_text1: news_text, news_lang1: news_lang, sendnews1:sendnews},
            function(data) {                
                //alert(data); //DEBUG             
                var json = $.parseJSON(data);
                if(json[0].status === 'ok') {
                    alert(json[0].msg);
                    $("form")[0].reset();                
                    $(location).attr('href', json[0].url);
                } else if (json[0].status == 1) {
                        alert(json[0].msg);
                } else if (json[0].status == 2) {
                    $('#news_author').css("border","2px solid red");
                    $('#news_author').css("box-shadow","0 0 3px red");                    
                    alert(json[0].msg);
                } else if (json[0].status == 3) {
                    $('#news_title').css("border","2px solid red");
                    $('#news_title').css("box-shadow","0 0 3px red");                    
                    alert(json[0].msg);
                } else if (json[0].status == 4) {
                    $('#news_lead').css("border","2px solid red");
                    $('#news_lead').css("box-shadow","0 0 3px red");                    
                    alert(json[0].msg);
                } else if (json[0].status == 5) {
                    $('#news_text').css("border","2px solid red");
                    $('#news_text').css("box-shadow","0 0 3px red");                    
                    alert(json[0].msg);                    
                } else {
                    alert(json[0].msg);
                }                
            });
            return false;
    });
    
});