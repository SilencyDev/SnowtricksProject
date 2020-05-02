$(".custom-file").on("change", ".custom-file-input",function(){
    if ($(this).val()) {
       var filename = $(this).val().split("\\");
       filename = filename[filename.length-1];
       $(".custom-file-label").text(filename);
    }
 });