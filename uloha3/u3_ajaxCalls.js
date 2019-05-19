function getTemplate(){
    var template = document.getElementById("templateName");
    var templateName= template.options[template.selectedIndex].value;
    console.log(templateName);

    $.ajax({
        url: "u3_templateHolder.php",
        type: "POST",
        data: {"templateName":templateName},
        success: function(response){
            $("#templateShowPlace").val(response);
        },
        error: function() {
            console.log("wrong template name");
        }
    });

}

function clearInput() {
    document.getElementById("file").value = "";
}