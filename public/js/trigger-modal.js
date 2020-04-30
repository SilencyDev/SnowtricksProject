$(document).ready(function() {
    //On écoute le "click" sur le bouton ayant la classe "modal-trigger"
    $(".modal-trigger-newS").click(function () {
    //On initialise les modales materialize
         $("#newSnowtrick").modal();
        //On récupère l"url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr("data-target");
        //on fait un appel ajax vers l"action symfony qui nous renvoie la vue
        $.ajax({
            url,
            method: "GET",
            success(response) {
                $("#newSnowtrick .modal-body").html(response);
                $("#newSnowtrick").modal("open");
            },
            error(response) {
                $("#newSnowtrick .modal-body").html(response.responseText);
            }
        }).then($(".modal-body").on("submit", "form#form-data", function (event) {
            event.preventDefault();
            let url = $(this).attr("action");
            let formdata = new FormData(document.getElementById("form-data"));
            $.ajax({
                url,
                method: "POST",
                data: formdata,
                processData: false,  // indique à jQuery de ne pas traiter les données
                contentType: false,  // indique à jQuery de ne pas configurer le contentType
                success(response) {
                    location.href = "/snowtrick/mytricks";
                },
                error(response) {
                    $("#newSnowtrick .modal-body").html(response.responseText);
                }
            });
        }));
    });

    $(".modal-trigger-editS").click(function () {
        //On initialise les modales materialize
        $("#editSnowtrick").modal();
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr("data-target");
        //on fait un appel ajax vers l"action symfony qui nous renvoie la vue
        $.ajax({
            url,
            method: "GET",
            success(response) {
                $("#editSnowtrick .modal-body").html(response);
                $("#editSnowtrick").modal("open");
            },
            error(response) {
                $("#editSnowtrick .modal-body").html(response.responseText);
            }
        }).then($(".modal-body").on("submit", "form#form-data", function (event) {
            event.preventDefault();
            let url = $(this).attr("action");
            let formdata = new FormData(document.getElementById("form-data"));
            $.ajax({
                url,
                method: "POST",
                data: formdata,
                processData: false,  // indique à jQuery de ne pas traiter les données
                contentType: false,  // indique à jQuery de ne pas configurer le contentType
                success(response) {
                    location.href = "/snowtrick/mytricks";
                },
                error(response) {
                    $("#editSnowtrick .modal-body").html(response.responseText);
                }
            });
        }));
    });

    $(".modal-trigger-newC").click(function () {
        //On initialise les modales materialize
        $("#newCategory").modal();
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr("data-target");
        //on fait un appel ajax vers l"action symfony qui nous renvoie la vue
        $.ajax({
            url,
            method: "GET",
            success(response) {
                $("#newCategory .modal-body").html(response);
                $("#newCategory").modal("open");
            },
            error(response) {
                $("#newCategory .modal-body").html(response.responseText);
            }
        }).then($(".modal-body").on("submit", "form#form-data", function (event) {
            event.preventDefault();
            let url = $(this).attr("action");
            let formdata = new FormData(document.getElementById("form-data"));
            $.ajax({
                url,
                method: "POST",
                data: formdata,
                processData: false,  // indique à jQuery de ne pas traiter les données
                contentType: false,  // indique à jQuery de ne pas configurer le contentType
                success(response) {
                    location.href = "/category";
                },
                error(response) {
                    $("#newCategory .modal-body").html(response.responseText);
                }
            });
        }));
    });
    
    $(".modal-trigger-editC").click(function () {
        //On initialise les modales materialize
        $("#editCategory").modal();
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr("data-target");
        //on fait un appel ajax vers l"action symfony qui nous renvoie la vue
        $.ajax({
            url,
            method: "GET",
            success(response) {
                $("#editCategory .modal-body").html(response);
                $("#editCategory").modal("open");
            },
            error(response) {
                $("#editCategory .modal-body").html(response.responseText);
            }
        }).then($(".modal-body").on("submit", "form#form-data", function (event) {
            event.preventDefault();
            let url = $(this).attr("action");
            let formdata = new FormData(document.getElementById("form-data"));
            $.ajax({
                url,
                method: "POST",
                data: formdata,
                processData: false,  // indique à jQuery de ne pas traiter les données
                contentType: false,  // indique à jQuery de ne pas configurer le contentType
                success(response) {
                    location.href = "/category";
                },
                error(response) {
                    $("#editCategory .modal-body").html(response.responseText);
                }
            });
        }));
    });
});