<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
?>
<label><?php print $LANGDATA['L_NMU_UPLOAD_FILES'] ?><span class='text_small'><?php print $LANGDATA['L_NMU_MAX'] . $config['NMU_MAX_FILESIZE'] ?></span></label>
<div id="upload_container">
    <a id="pickfiles" href="javascript:;"><?php print $LANGDATA['L_NMU_SELECT_FILES'] ?></a>
    <a id="uploadfiles" href="javascript:;"><?php print $LANGDATA['L_NMU_UPLOAD_FILES'] ?></a>
</div>
<pre id="console"></pre>
<div id="filelist"><?php  print $LANGDATA['L_NMU_E_BROWSER_UPLOAD'] ?></div>

<script type="text/javascript">

var uploader = new plupload.Uploader({
    runtimes : 'html5, html4',
    browse_button : 'pickfiles', // you can pass an id...
    container: document.getElementById('upload_container'), // ... or DOM Element itself
    url : '<?php print $config['ROOT_FILE'] ?>?module=NewsMediaUploader&page=upload',
    unique_names: false,

    filters : {
	max_file_size : '<?php print $config['NMU_MAX_FILESIZE'] ?>',
        mime_types: [
            {title : "Image files", extensions : "<?php print $config['NMU_ACCEPTED_FILES']?>"}
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
</script>