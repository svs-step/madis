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
            scales: {
                yAxes: [{
                    stacked: true,
                    ticks : {
                        beginAtZero: false,
                        stepSize: 1,
                        callback: function(label, index, labels) {
                            switch (label) {
                                case 1:
                                    return 'Négligeable'
                                case 2:
                                    return 'Limité'
                                case 3:
                                    return 'Important'
                                case 4:
                                    return 'Maximal'
                            }
                        }
                    },

                }],
                xAxes: [{
                    stacked: true,
                }],
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                },
            },
            animation: {
                duration: 0
            }
        }
    });
}

function bubbleChart(id, labels, data) {
    new Chart($('#' + id), {
        type: 'bubble',
        data: {
            datasets: data,
        },
        options: {
            legend: {
              display: false
            },
            scales: {
                yAxes: [{
                    ticks : {
                        beginAtZero: true,
                        stepSize: 1,
                        min: 0,
                        max: 4,
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Gravité',
                    }
                }],
                xAxes: [{
                    ticks : {
                        beginAtZero: true,
                        stepSize: 1,
                        min: 0,
                        max: 4,
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Vraisemblance',
                    }
                }],
            },
            animation: {
                duration: 0
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

    bubbleChart(
        'dicResiduels-chart',
        risquesLabels,
        dicResiduelsData,
    );
});
