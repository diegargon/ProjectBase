/* 
 *  Copyright @ 2016 Diego Garcia
 */
$(function()
{
  $("#btnAddField").click(function(event)
  {
    var intId = $("#extra_input div").length + 1;
    var fieldWrapper = $("<div class=\"fieldwrapper\" id=\"field" + intId + "\"/>");
    var input = $("<input type=\"text\" class=\"news_extra_link\"  name=\"news_new_extra_media[]\"/>");
    var removeButton = $("<input type=\"button\" class=\"remove\" value=\"-\" />");

    removeButton.click(function() {
        $(this).parent().remove();
    });                    
    
    fieldWrapper.append(input);
    fieldWrapper.append(removeButton);
    
    $('#extra_input').append(fieldWrapper);
    event.preventDefault();
  }); 
});

removeParent = function(e) {
    $(e).parent().remove();
};
