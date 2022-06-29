$( document ).ready(function() {
    $('.collapse-event').on('click', (e) => {
        let targetCollapse = e.target.id.split('-');
        targetCollapse.shift();

        if(e.target.children[0].children[0].className.includes('down')) {
            e.target.children[0].children[0].classList.remove('bi-caret-down-fill');
            e.target.children[0].children[0].classList.add('bi-caret-right-fill');

            document.getElementById(targetCollapse.join('-')).hidden = true;
        } else {
            e.target.children[0].children[0].classList.remove('bi-caret-right-fill');
            e.target.children[0].children[0].classList.add('bi-caret-down-fill');

            document.getElementById(targetCollapse.join('-')).hidden = false;
        }

    });
});