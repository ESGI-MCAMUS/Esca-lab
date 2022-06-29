window.addEventListener('load', function () {
    let config = {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Opened",
                fill: true,
                data: values,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)'
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: {
                display: false,
                labels: {
                    fontStyle: "normal"
                }
            },
            title: {
                fontStyle: "normal",
                display: false
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        color: 'rgb(234, 236, 244)',
                        zeroLineColor: 'rgb(234, 236, 244)',
                        drawBorder: false,
                        drawTicks: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2],
                        drawOnChartArea: false
                    },
                    ticks: {
                        fontColor: "#858796",
                        fontStyle: "normal",
                        padding: 20
                    }
                }],
                yAxes: [{
                    gridLines: {
                        color: 'rgb(234, 236, 244)',
                        zeroLineColor: 'rgb(234, 236, 244)',
                        drawBorder: false,
                        drawTicks: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    },
                    ticks: {
                        fontColor: "#858796",
                        fontStyle: "normal",
                        padding: 20
                    }
                }]
            }
        }
    }


    const ctx = document.getElementById('lineChart').getContext('2d');
    console.log(ctx);

    const lineChart = new Chart(ctx, config);
});