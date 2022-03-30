$( document ).ready(function() {
    console.log( "ready!" );

    $('.btn-remove').on('click', (e) => {
        console.log(`Removing route ${e.target.dataset.bRoute}`);

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + "/route/unresolved/" + e.target.dataset.bRoute,
            data: {
                // ... 
            }
        })
        .done(function(data) {
            console.log(data);

            if(data.success) {
                new Toast('success-friend-add-' + Math.floor(Math.random() * 100), 'success', 'bi bi-check', 'Route retirée !', false, true).show();
            } else {
                new Toast('error-friend-add-' + Math.floor(Math.random() * 100), 'error', 'bi bi-exclamation-triangle', 'Il y a eu une erreur lors de la suppression !', false, true).show();
            }
        });
    });
});