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
        
        document.getElementById('final-update-event').dataset.eId = shortId;

        document.getElementsByClassName('modal-title')[0].textContent = 
            'Modification ' + 
            document.getElementById(`event-${shortId}-type`).textContent +
            ' à ' +
            document.getElementById(`event-${shortId}-gym`).textContent;


        document.getElementById('select-type-title').value = document.getElementById(`event-${shortId}-type`).dataset.eTitle;
        document.getElementById('select-type-gym').value = document.getElementById(`event-${shortId}-gym`).dataset.eGym;

        document.getElementById('select-start-event-gym').value = document.getElementById(`event-${shortId}-dateStart`).dataset.eDateStart.replaceAll('UTC', 'T');
        document.getElementById('select-start-event-gym').min = new Date().toISOString().split('.')[0];
        
        document.getElementById('select-end-event-gym').value = document.getElementById(`event-${shortId}-dateStart`).dataset.eDateEnd.replaceAll('UTC', 'T');

        document.getElementById('select-description-event').value = document.getElementById(`event-${shortId}-description`).textContent.trim();
        

        modalUpdateEvent.show();
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
            $(`#card-event-${shortId}`).remove();
        });
    });

    $('#final-update-event').on('click', (e) => {
        const shortId = e.target.dataset.eId;
        const url_route = `/event/edit/${shortId}`;

        if(
            new Date(document.getElementById('select-start-event-gym').value) >= 
            new Date(document.getElementById('select-end-event-gym').value)
        ) {
            document.getElementById('select-start-event-gym').classList.add('border', 'border-danger');
            document.getElementById('select-end-event-gym').classList.add('border', 'border-danger');
            return;
        }

        document.getElementById('final-update-event').classList.add('disabled');
        document.getElementById('spinner-update-event').hidden = false;

        document.getElementById('select-start-event-gym').classList.remove('border', 'border-danger');
        document.getElementById('select-end-event-gym').classList.remove('border', 'border-danger');

        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + url_route,
            data: {
                titre: document.getElementById('select-type-title').value,
                gym:   document.getElementById('select-type-gym').value,
                startEvent: document.getElementById('select-start-event-gym').value,
                endEvent: document.getElementById('select-end-event-gym').value,
                description: document.getElementById('select-description-event').value,
            }
        })
        .done(function(data) {
            console.log(data);

            if(data.success) {
                document.getElementById('final-update-event').classList.remove('disabled');
                document.getElementById('spinner-update-event').hidden = true;

                document.getElementById(`event-${shortId}-type`).textContent = document.getElementById('select-type-title').selectedOptions[0].textContent; 
                document.getElementById(`event-${shortId}-type`).dataset.eTitle = document.getElementById('select-type-title').selectedOptions[0].value;
                
                document.getElementById(`event-${shortId}-gym`).textContent = document.getElementById('select-type-gym').selectedOptions[0].textContent; 
                document.getElementById(`event-${shortId}-gym`).dataset.eGym = document.getElementById('select-type-gym').selectedOptions[0].value;
                
                document.getElementById(`event-${shortId}-dateStart`).textContent = document.getElementById('select-start-event-gym').value.replace('T', ' ');
                document.getElementById(`event-${shortId}-dateStart`).dataset.eDateStart = document.getElementById('select-start-event-gym').value;
                document.getElementById(`event-${shortId}-dateEnd`).textContent = 
                    DDHHMM(
                        (new Date(document.getElementById('select-end-event-gym').value) - 
                        new Date(document.getElementById('select-start-event-gym').value)) / 1000
                    );
                document.getElementById(`event-${shortId}-dateStart`).dataset.eDateEnd = document.getElementById('select-end-event-gym').value;

                document.getElementById(`event-${shortId}-description`).textContent = document.getElementById('select-description-event').value;

                let args_href = document.getElementById("event-href-9").href.split('/');

                args_href[args_href.length - 1] = document.getElementById('select-type-gym').selectedOptions[0].value;

                document.getElementById(`event-${shortId}-gym-href-span`).textContent = document.getElementById('select-type-gym').selectedOptions[0].textContent;
                document.getElementById(`event-href-${shortId}`).href = args_href.join('/');

                modalUpdateEvent.hide();
            }
        });
    });

    function DDHHMM(ms) {
        var days = Math.floor(ms / (3600 * 24));
        var hours = Math.floor((ms - (days * (3600 * 24))) / 3600);
        var minutes = Math.floor((ms - (days * (3600 * 24)) - (hours * 3600)) / 60);
        var seconds = ms - (days * (3600 * 24)) - (hours * 3600) - (minutes * 60);
  
        if (hours < 10) {hours = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        return days + ' j ' + hours + ' H ' + minutes;
    }
});