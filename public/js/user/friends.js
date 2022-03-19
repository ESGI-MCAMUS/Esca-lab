$( document ).ready(function() {
    console.log( "ready!" );

    $('.btn-remove-friend').on('click', (e) => {
        console.log('On va retirer l\'ami : ' + e.target.dataset.dId);

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + "/user/friends/remove/" + e.target.dataset.dId,
            data: {
                // ... 
            }
        })
        .done(function(data) {
            console.log(data);

            if(data.success) {
                new Toast('success-friend-remove-' + Math.floor(Math.random() * 100), 'success', 'bi bi-check', 'Ami retiré !', false, true).show();

                $('#carte-user-' + e.target.dataset.dId).remove();
            } else {
                new Toast('error-friend-remove-' + Math.floor(Math.random() * 100), 'error', 'bi bi-exclamation-triangle', 'Il y a eu une erreur lors de la suppression !', false, true).show();
            }
        });
    });
});