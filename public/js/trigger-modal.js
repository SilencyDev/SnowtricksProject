$(document).ready(function() {
    //On écoute le "click" sur le bouton ayant la classe "modal-trigger"
    $('.modal-trigger-newS').click(function () {
    //On initialise les modales materialize
         $('#newSnowtrick').modal();
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr('data-target');
        //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
        $.get(url, function (data) {
            //on injecte le html dans la modale
            $('.modal-body').html(data);
            //on ouvre la modale
            $('#newSnowtrick').modal('open');
        });
    })

    $('.modal-trigger-editS').click(function () {
        //On initialise les modales materialize
        $('#editSnowtrick').modal();
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr('data-target');
        //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
        $.get(url, function (data) {
            //on injecte le html dans la modale
            $('.modal-body').html(data);
            //on ouvre la modale
            $('#editSnowtrick').modal('open');
        });
    })

    $('.modal-trigger-viewS').click(function () {
        //On initialise les modales materialize
        $('#showSnowtrick').modal();
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr('data-target');
        //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
        $.get(url, function (data) {
            //on injecte le html dans la modale
            $('.modal-body').html(data);
            //on ouvre la modale
            $('#showSnowtrick').modal('open');
        });
    })

    $('.modal-trigger-newC').click(function () {
        //On initialise les modales materialize
             $('#newCategory').modal();
            //On récupère l'url depuis la propriété "Data-target" de la balise html a
            url = $(this).attr('data-target');
            //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
            $.get(url, function (data) {
                //on injecte le html dans la modale
                $('.modal-body').html(data);
                //on ouvre la modale
                $('#newCategory').modal('open');
            });
        })
    
        $('.modal-trigger-editC').click(function () {
            //On initialise les modales materialize
            $('#editCategory').modal();
            //On récupère l'url depuis la propriété "Data-target" de la balise html a
            url = $(this).attr('data-target');
            //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
            $.get(url, function (data) {
                //on injecte le html dans la modale
                $('.modal-body').html(data);
                //on ouvre la modale
                $('#editCategory').modal('open');
            });
        })
});