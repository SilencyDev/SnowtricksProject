$("#form-data").submit(function (e) {
    e.preventDefault();
    let url = $(this).attr("action");
    let formdata = new FormData(document.getElementById("form-data"));
    $.ajax({
        url,
        method: "POST",
        data: formdata,
        processData: false,  // indique à jQuery de ne pas traiter les données
        contentType: false,  // indique à jQuery de ne pas configurer le contentType
        success(data) {
            if($("#comments div").hasClass("comment-row")) {
                $(data).insertBefore($(".comment-row:first"));
            } else {
                $("#comments").html(data);
            }
        },
    });
});
