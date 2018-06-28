// -----------
// CONTRACTORS
// -----------

function doughnutChart(id, data, color)
{
    new Chart($('#'+id), {
        type: 'doughnut',
        data: {
            labels: [labelYes, labelNo],
            datasets: [{
                data: data,
                backgroundColor: color,
            }],
        },
        options: {
            legend: {
                display: false
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
