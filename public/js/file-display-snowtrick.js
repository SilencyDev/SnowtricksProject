$("#newSnowtrick .modal-body").on("change", "#snowtrick_pictures",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_pictures'].custom-file-label").text(filename);
    }
});

$("#newSnowtrick .modal-body").on("change", "#snowtrick_mainpicture",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_mainpicture'].custom-file-label").text(filename);
    }
});

$("#editSnowtrick .modal-body").on("change", "#snowtrick_pictures",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_pictures'].custom-file-label").text(filename);
    }
});

$("#editSnowtrick .modal-body").on("change", "#snowtrick_mainpicture",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_mainpicture'].custom-file-label").text(filename);
    }
});