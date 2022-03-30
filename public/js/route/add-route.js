$( document ).ready(function() {
    console.log( "ready!" );

    $('.btn-add').on('click', (e) => {
        console.log(`Adding route ${e.target.dataset.bRoute}`);

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + "/route/resolved/" + e.target.dataset.bRoute,
            data: {
                // ... 
            }
        })
        .done(function(data) {
            console.log(data);

            if(data.success) {
                new Toast('success-route-add-' + Math.floor(Math.random() * 100), 'success', 'bi bi-check', 'Route ajouté !', false, true).show();
            } else {
                new Toast('error-route-add-' + Math.floor(Math.random() * 100), 'error', 'bi bi-exclamation-triangle', 'Il y a eu une erreur lors de l\'ajout !', false, true).show();
            }
        });
    });
});