$( document ).ready(function() {
    console.log( "ready!" );

    $('.btn-add-friend').on('click', (e) => {
        console.log('On va ajouter l\'ami : ' + e.target.dataset.dId);

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + "/" + e.target.dataset.dId,
            data: {
                // ... 
            }
        })
        .done(function(data) {
            console.log(data);
        });
    });
});