<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<label><?= $LNG['L_NMU_UPLOAD_FILES'] ?><span class='text_small'><?= $LNG['L_NMU_MAX'] . $cfg['NMU_MAX_FILESIZE'] ?></span></label>
<div id="upload_container">
    <a id="pickfiles" href="javascript:;"><?= $LNG['L_NMU_SELECT_FILES'] ?></a>
    <a id="uploadfiles" href="javascript:;"><?= $LNG['L_NMU_UPLOAD_FILES'] ?></a>
</div>
<pre id="console"></pre>
<div id="filelist"><?= $LNG['L_NMU_E_BROWSER_UPLOAD'] ?></div>

<?php if(!empty($data['UPLOAD_EXTRA'])) { ?>
<div id="uploaded_user_list"><?= $data['UPLOAD_EXTRA'] ?></div>
<?php } ?>
<script type="text/javascript">

var uploader = new plupload.Uploader({
    runtimes : 'html5, html4',
    browse_button : 'pickfiles', // you can pass an id...
    container: document.getElementById('upload_container'), // ... or DOM Element itself
    url : '<?= $cfg['CON_FILE'] ?>?module=NewsMediaUploader&page=upload',
    unique_names: false,

    filters : {
	max_file_size : '<?= $cfg['NMU_MAX_FILESIZE'] ?>',
        mime_types: [
            {title : "Image files", extensions : "<?= $cfg['NMU_ACCEPTED_FILES']?>"}
	]
    },

    init: {
	PostInit: function() {
            document.getElementById('filelist').innerHTML = '';
            document.getElementById('uploadfiles').onclick = function() {
                uploader.start();
                return false;
            };
	},
	FilesAdded: function(up, files) {
            plupload.each(files, function(file) {
		document.getElementById('filelist').innerHTML += '<div id="' + file.id + '"><span class="file_details"><b></b>' + file.name + ' (' + plupload.formatSize(file.size) + ') </span></div>';
            });
	},
	UploadProgress: function(up, file) {
            var span_size = file.percent / 8;
            if (file.percent == 100) {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span class="file_percent" style="background-color:#4479BA;padding-left:'+ span_size +'%;">' + file.percent + "%</span>";
            } else {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span class="file_percent" style=padding-left:'+ span_size +'%;>' + file.percent + "%</span>";
            }
        },
	Error: function(up, err) {
            document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
	},
        FileUploaded: function (up, file, object) {
            var myData;

            myData = $.parseJSON(object.response);
            if (myData.error) {
                document.getElementById('console').appendChild(document.createTextNode("\nError with " + file.name + ": " + myData.error.code + ": " + myData.error.message));
            }
            if (myData.result) {
                var textarea = document.getElementById('news_text');
                textarea.value += "[localimg w=600]" + myData.result + "[/localimg]";
            }
            //console.log(object);console.log(myData); console.log(file);
        }
    }
});
uploader.init();

function addtext(text) {
    var textarea = document.getElementById('news_text');
    textarea.value += text;
}

</script>