$( document ).ready(function() {
    $('#send-message').on('click', (e) => {
        if(document.getElementById('input-message').value.trim().length === 0) {
            return;
        }
        const shortId = e.target.dataset.bRoute;
        const url_route = `/route/addMessage/${shortId}`;

        const message = document.getElementById('input-message').value.trim();

        document.getElementById(e.target.id).classList.add('disabled');
        document.getElementById('spinner-send-message').hidden = false;
        $.ajax({
            method: "POST",
            url: document.getElementById('url_root').value + url_route,
            data: {
                message: message
            }
        })
        .done(function(data) {
            console.log(data);
            if(data.success) {
                document.getElementById('spinner-send-message').hidden = true;
                document.getElementById('input-message').value = "" 

                if(document.getElementById('no-comment-display')) {
                    document.getElementById('no-comment-display').remove();
                }

                const template= $('#templateMessage').data('t-span');
                const crValues = {
                    '__idMessageTemplate__':        'lastMessage',
                    '__messageContentTemplate__':   message,
                };
                const re = new RegExp(Object.keys(crValues).join('|'),'gi');
                $('#messages-list').append(template.replace(re, function(matched){
                    return crValues[matched];
                }));
            }
        });
    });
});