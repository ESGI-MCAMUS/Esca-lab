$( document ).ready(function() {

    const modalUpdateEvent = new bootstrap.Modal(document.getElementById('modal-update-event'), {});

    $('.btn-participation-event').on('click', (e) => {
        const shortId = e.target.dataset.bEvent;
        const status = e.target.classList.contains('event-add');
        const url_route = status ? '/event/resolved/' : '/event/unresolved/';
        document.getElementById(e.target.id).classList.add('disabled');
        document.getElementById('spinner-' + shortId).hidden = false;

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + url_route + shortId,
        })
        .done(function(data) {
            console.log(data);
            if(data.success) {
                if(status) {
                    document.getElementById(e.target.id).classList.remove('btn-outline-success', 'event-add');
                    document.getElementById(e.target.id).classList.add('btn-outline-warning', 'event-remove');
                    document.getElementById(e.target.id).innerHTML = 'Je ne participe plus';
                    document.getElementById(`event-participant-${shortId}`).textContent = 
                        parseInt(document.getElementById(`event-participant-${shortId}`).textContent) + 1; 
                } else {
                    document.getElementById(e.target.id).classList.remove('btn-outline-warning', 'event-remove');
                    document.getElementById(e.target.id).classList.add('btn-outline-success', 'event-add');
                    document.getElementById(e.target.id).innerHTML = `Je participe`;
                    document.getElementById(`event-participant-${shortId}`).textContent = 
                        parseInt(document.getElementById(`event-participant-${shortId}`).textContent) - 1;
                }
            } else {
                new Toast('error-route-add-' + Math.floor(Math.random() * 100), 'error', 'bi bi-exclamation-triangle', 'Il y a eu une erreur  !', false, true).show();
            }
    
            // Adding the spinner to the button so it is not removed when adding the conent of the button
            document.getElementById(e.target.id).classList.remove('disabled');
            const template= $('#templateSpan').data('t-span');
            const crValues = {
                '__id__':           shortId,
                '__spinnerColor__': status ? 'warning' : 'success' 
            };
            const re = new RegExp(Object.keys(crValues).join('|'),'gi');
            $(`#bouton-event-${shortId}`).prepend(template.replace(re, function(matched){
                return crValues[matched];
            }));
        });
    });

    $('.btn-update-event').on('click', (e) => {
        const shortId = e.target.dataset.bEvent;
        
        document.getElementsByClassName('modal-title')[0].textContent = 
            'Modification ' + 
            document.getElementById(`event-${shortId}-type`).textContent +
            ' à ' +
            document.getElementById(`event-${shortId}-gym`).textContent;


        document.getElementById('select-type-title').value = document.getElementById(`event-${shortId}-type`).dataset.eTitle;
        document.getElementById('select-type-gym').value = document.getElementById(`event-${shortId}-gym`).dataset.eGym;

        modalUpdateEvent.show();

        console.log(e);
    });

    $('.btn-delete-event').on('click', (e) => {
        const shortId = e.target.dataset.bEvent;
        const url_route = `/user/event/delete/${shortId}`;
        document.getElementById(e.target.id).classList.add('disabled');
        document.getElementById('spinner-delete-' + shortId).hidden = false;

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + url_route,
        })
        .done(function(data) {
            console.log(data);

            $(`#card-event-${shortId}`).remove();
        });
    });
});