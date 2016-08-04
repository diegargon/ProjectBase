/* 
 *  Copyright @ 2016 Diego Garcia
 */
 $(document).ready(function() {
    $(".btnNewsRate").on('click', function() {
        $('.btnNewsRate').attr('disabled','disabled');
        var rate_uid = $(".rate_uid").val();
        var rate_rid = $(".rate_rid").val();
        var rate_lid = $(".rate_lid").val();

        if(rate_uid === null || rate_rid === null || rate_lid === null) {
            alert("Internal error, please reload");
        } else {
            $.post("", $( "#form_news_rate" ).serialize() + '&news_rate=' + $(this).val(),
            function(data) {
                //alert($data);
                var json = $.parseJSON(data);
                alert(json[0].msg);
            });
        }
        $('.btnNewsRate').removeAttr("disabled");
    });

    $(".btnCommentRate").on('click', function() {
        $('.btnCommentRate').attr('disabled','disabled');
        var rate_uid = $(this).closest("form").find("input[name='rate_uid']").val();
        var rate_rid = $(this).closest("form").find("input[name='rate_rid']").val();
        var rate_lid =  $(this).closest("form").find("input[name='rate_lid']").val();

        if(rate_uid === null || rate_rid === null || rate_lid === null) {
            alert("Internal error, please reload" + rate_uid +":"+ rate_rid +":"+ rate_lid);
        } else {
            $.post("", $( "#form_comment_rate\\[" +rate_rid+"\\]" ).serialize() + '&comment_rate=' + $(this).val(),
            function(data) {
                //alert(data);
                var json = $.parseJSON(data);
                alert(json[0].msg);
            });
        }
        $('.btnCommentRate').removeAttr("disabled");
    });
 });