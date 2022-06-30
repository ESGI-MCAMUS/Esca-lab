$( document ).ready(function() {

    $('.btn-update-favorite').on('click', (e) => {

        const status = document.getElementById(e.target.id).classList.contains('addToFavorites');
        const uri = "/gym/favorite/" + (status ? "add/" : "remove/");

        console.log( document.getElementById('url_root').value + uri + e.target.dataset.bRoute);

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + uri + e.target.dataset.bRoute,
            data: {
                // ...
            }
        })
        .done(function(data) {
            if (data.success) {
                if (status) {
                    document.getElementById(e.target.id).classList.remove('addToFavorites', 'bi-star');
                    document.getElementById(e.target.id).classList.add('removeFromFavorites', 'bi-star-fill');
                } else {
                    document.getElementById(e.target.id).classList.remove('removeFromFavorites', 'bi-star-fill');
                    document.getElementById(e.target.id).classList.add('addToFavorites', 'bi-star');

                }
            }
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
    });
});