/* 
 *  Copyright @ 2016 Diego Garcia
 */
$(document).ready(function(){
    $("#sendnews").click(function(){       
        $('#news_author').css("border","1px solid black");
        $('#news_author').css("box-shadow","0 0 3px black");
        $('#news_title').css("border","1px solid black");          
        $('#news_title').css("box-shadow","0 0 3px black");
        $('#news_lead').css("border","1px solid black");
        $('#news_lead').css("box-shadow","0 0 3px black");
        $('#news_text').css("border","1px solid black");
        $('#news_text').css("box-shadow","0 0 3px black");            
        $('#news_main_media').css("border","1px solid black");
        $('#news_main_media').css("box-shadow","0 0 3px black");            
           
        $.post("", $( "#form_news" ).serialize() + '&sendnews_stage2=1' ,
        function(data) {                
            alert(data); //DEBUG             
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
            } else if (json[0].status == 6) {
                $('#news_main_media').css("border","2px solid red");
                $('#news_main_media').css("box-shadow","0 0 3px red");                    
                alert(json[0].msg);                    
            } else {
                 alert(json[0].msg);
            }                
            });
        return false;
    });
    
});