<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<script>
 window.addEventListener("load", function() {
    $("#btnRemoteUpload").on('click', function() {
        $('#btnRemoteUpload').hide();
        var remote_url = $("#news_remote_media").val();
        //TODO: Do basic URL check on client side
        $('#news_remote_media').css("border","1px solid black");
        $('#news_remote_media').css("box-shadow","0 0 3px black");        
        if (remote_url == '') {
            $('#btnRemoteUpload').show();
            return false;
        } else {
            $.post("<?= $cfg['CON_FILE'] ?>?module=NewsMediaUploader&page=remote_upload", { url: remote_url },
            function(data) {
                console.log(data);
                var json = $.parseJSON(data);
                if(json.status == 'ok') {
                    $("#news_remote_media").val('');
                    $("#remote_upload_status").text(json.msg);
                    $("#news_text").append("[localimg w=600]" + json.filename + "[/localimg]");
                } else {
                    $('#news_remote_media').css("border","2px solid red");
                    $('#news_remote_media').css("box-shadow","0 0 3px red");
                    $("#remote_upload_status").text(json.msg);
                }
                $('#btnRemoteUpload').show();
            });
        }
    });
 });
</script>
        <div class="submit_items">
            <p>
                <label for="news_remote_media"><?= $LNG['L_NMU_REMOTE_MEDIA'] ?> </label>
                <input value=""
                    minlength="<?= $cfg['NEWS_LINK_MIN_LENGHT']?>"
                    maxlength="<?= $cfg['NEWS_LINK_MAX_LENGHT']?>"
                    id="news_remote_media" class="news_remote_link" name="news_remote_media" 
                    type="text" placeholder="http://site.com/image.jpg"/>
                <button id="btnRemoteUpload" type="button" value="" name="btnRemoteUpload">Enviar</button>
            </p>
            <p id="remote_upload_status" class="center"></p>
        </div>