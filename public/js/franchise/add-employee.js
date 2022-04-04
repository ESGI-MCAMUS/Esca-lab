$( document ).ready(function() {
    console.log( "ready!" );

    $('.btn-add-employee').on('click', (e) => {
        console.log('On va ajouter l\'employé(e) : ' + e.target.dataset.dId);

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + "/" + e.target.dataset.dId,
            data: {
                "gym": $('#gyms').find(':selected').val(),
            }
        })
        .done(function(data) {
            console.log(data);

            if(data.success) {
                new Toast('success-friend-add-' + Math.floor(Math.random() * 100), 'success', 'bi bi-check', 'Employé(e) ajouté !', false, true).show();

                $('#carte-user-' + e.target.dataset.dId).remove();
            } else {
                new Toast('error-friend-add-' + Math.floor(Math.random() * 100), 'error', 'bi bi-exclamation-triangle', 'Il y a eu une erreur lors de l\'ajout !', false, true).show();
            }
        });
    });
});