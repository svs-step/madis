
function radarChart(id, labels, serieLabel, data, color) {
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

    new Chart($('#' + id), {
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


function stackedBarChart(id, labels, data) {

    new Chart($('#' + id), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: data,
        },
        options: {
            scale: {
                ticks: {
                    min: 0,
                    max: 5,
                },
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                }
            }
        }
    });
}



$(document).ready(function() {
    radarChart(
        'grandsDomaines-chart',
        domainesLabels,
        [''],
        domainesDatas,
        [colorBlue]
    );

    radarChart(
        'mesuresSecurite-chart',
        mesuresLabels,
        [''],
        mesuresDatas,
        [colorPurple]
    );

    stackedBarChart(
        'risquesResiduels-chart',
        risquesLabels,
        risquesDatas,
    );
});
