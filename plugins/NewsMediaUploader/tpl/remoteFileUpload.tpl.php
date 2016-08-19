<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<script>
 $(document).ready(function() {
    $("#btnRemoteUpload").on('click', function() {        
        var remote_url = $("#news_remote_media").val();
        //TODO: Do extra basic URL check on client side
        $('#news_remote_media').css("border","1px solid black");
        $('#news_remote_media').css("box-shadow","0 0 3px black");        
        if (remote_url == '') {
            return false;
        } else {            
            $.post("<?php print $config['ROOT_FILE'] ?>?module=NewsMediaUploader&page=remote_upload", { url: remote_url },
            function(data) {
                //console.log(data);                
                var json = $.parseJSON(data);
                if(json.status == 'ok') {
                    $("#news_remote_media").val('');
                    $("#remote_upload_status").text(json.msg);
                    $("#news_text").append("[localimg w=600]" + json.filename + "[/localimg]");                                    
                } else {
                    $('#news_remote_media').css("border","2px solid red");
                    $('#news_remote_media').css("box-shadow","0 0 3px red");
                    
                }
            });            
        }        
    });
   
 });
</script>
        <div class="submit_items">
            <p>
                <label for="news_remote_media"><?php print $LANGDATA['L_NMU_REMOTE_MEDIA'] ?> </label>
                <input value=""  
                    minlength="<?php print $config['NEWS_LINK_MIN_LENGHT']?>" 
                    maxlength="<?php print $config['NEWS_LINK_MAX_LENGHT']?>" 
                    id="news_remote_media" class="news_remote_link" name="news_remote_media" 
                    type="text" placeholder="http://site.com/image.jpg"/>
                <button id="btnRemoteUpload" type="button" value="" name="btnRemoteUpload">Enviar</button>
            </p>
            <p id="remote_upload_status" class="center"></p>
        </div> 