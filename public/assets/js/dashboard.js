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

// --------
// MATURITY
// --------

function radarChart(id, labels, serieLabel, data, color)
{
    let dataset = [];
    data.forEach(function(item, index) {
        dataset.push(
            {
                label: serieLabel[index],
                data: item,
                backgroundColor: color[index],
            }
        );
    });
    new Chart($('#'+id), {
        type: 'radar',
        data: {
            labels: labels,
            datasets: dataset,
        },
        options: {
            scale: {
                ticks: {
                    min: 0,
                    max: 5,
                }
            }
        }
    });
}
