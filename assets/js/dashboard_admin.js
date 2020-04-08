function doughnutChart(id, labels, data, color) {
    new Chart($('#' + id), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: color,
                hoverBackgroundColor: color
            }],
        },
        options: {
            legend: {
                display: true,
                labels: {
                    padding: 8,
                    boxWidth: 20
                }
            },
            tooltips: {
                displayColors: false,
            },
            responsive: true,
            scales: {
                label: {
                    display: false,
                }
            }
        }
    });
}


// LOAD GRAPHS
$(document).ready(function() {
    doughnutChart(
        'collectivity-type',
        collectivityByTypeLabel,
        collectivityByTypeData,
        [colorBlue, colorRed, colorGreen, colorOrange, colorPurple, colorTeal]
    );
});
