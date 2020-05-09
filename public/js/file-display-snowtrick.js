$("#newSnowtrick .modal-body").on("change", "#snowtrick_file",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_file'].custom-file-label").text(filename);
    }
});

$("#newSnowtrick .modal-body").on("change", "#snowtrick_mainfile",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_mainfile'].custom-file-label").text(filename);
    }
});

$("#editSnowtrick .modal-body").on("change", "#snowtrick_file",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_file'].custom-file-label").text(filename);
    }
});

$("#editSnowtrick .modal-body").on("change", "#snowtrick_mainfile",function(){
    if ($(this).val()) {
    var filename = $(this).val().split("\\");
    filename = filename[filename.length-1];
    $("label[for='snowtrick_mainfile'].custom-file-label").text(filename);
    }
});