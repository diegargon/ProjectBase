/* 
 *  Copyright @ 2016 Diego Garcia
 */
 $(document).ready(function() {
    $(".btnRate").on('click', function() {
        $('.btnRate').attr('disabled','disabled');
        var rate_uid = $("#rate_uid").val();
        var rate_rid = $("#rate_rid").val();
        var rate_lid = $("#rate_lid").val();

        if(rate_uid == null || rate_rid == null || rate_lid == null) {
            alert("Internal error, please reload");
        } else {
            $.post("", $( "#form_rate" ).serialize() + '&rate=' + $(this).val(),
            function(data) {
                var json = $.parseJSON(data);
                alert(json[0].msg);
            });
        }
        $('.btnRate').removeAttr("disabled");
    });
 });