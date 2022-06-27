$( document ).ready(function() {
    $('#btn-update-route').on('click', (e) => {
        const status = e.target.classList.contains('btn-add');
        const shortId = e.target.dataset.bRoute;
        const url_route = status ? '/route/resolved/' : '/route/unresolved/'
        document.getElementById(e.target.id).classList.add('disabled');
        document.getElementById('spinner-' + shortId).hidden = false;
        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + url_route + shortId,
            data: {
                // ... 
            }
        })
        .done(function(data) {
            if(data.success) {
                if(status) {
                    document.getElementById(e.target.id).classList.remove('btn-info', 'btn-add');
                    document.getElementById(e.target.id).classList.add('btn-warning', 'btn-remove');
                    document.getElementById(e.target.id).innerHTML = 'Je me suis trompé !';
                    document.getElementById('times-route-solved').textContent = 
                        parseInt(document.getElementById('times-route-solved').textContent) + 1; 
                } else {
                    document.getElementById(e.target.id).classList.remove('btn-warning', 'btn-remove');
                    document.getElementById(e.target.id).classList.add('btn-info', 'btn-add');
                    document.getElementById(e.target.id).innerHTML = `Je l'ai faite !`;
                    document.getElementById('times-route-solved').textContent = 
                        parseInt(document.getElementById('times-route-solved').textContent) - 1;
                }
            } else {
                new Toast('error-route-add-' + Math.floor(Math.random() * 100), 'error', 'bi bi-exclamation-triangle', 'Il y a eu une erreur  !', false, true).show();
            }

            // Adding the spinner to the button so it is not removed when adding the conent of the button
            document.getElementById(e.target.id).classList.remove('disabled');
            const template= $('#templateSpan').data('t-span');
            const crValues = {
                '__id__':     shortId
            };
            const re = new RegExp(Object.keys(crValues).join('|'),'gi');
            $('#btn-update-route').prepend(template.replace(re, function(matched){
                return crValues[matched];
            }));
        });
    });
});