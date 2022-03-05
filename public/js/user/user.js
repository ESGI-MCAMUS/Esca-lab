window.addEventListener('load', function () {

    $('.alert').alert();

    const labels = ['jaune', 'vert', 'bleu', 'rouge', 'noir', 'violet'];
    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Dataset 1',
                data: [Math.floor(Math.random() * 15), Math.floor(Math.random() * 15), Math.floor(Math.random() * 15), Math.floor(Math.random() * 15), Math.floor(Math.random() * 15), Math.floor(Math.random() * 15)],
                borderColor: '#ff0000',
                backgroundColor: ['#FFFF00', '#00ff00', '#0000ff', '#ff0000', '#000', '#ff00ff'],
            }
        ]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Nombre de voies realisees'
                }
            },
            responsive: true
        },
    };

    const ctx = document.getElementById('routesResume').getContext('2d');

    const routeResumeChart = new Chart(ctx, config);
});