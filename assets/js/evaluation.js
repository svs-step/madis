
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
});
