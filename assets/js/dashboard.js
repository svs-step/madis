function doughnutChart(id, labels, data, color) {
    new Chart($('#' + id), {
        type: 'doughnut',
        data: {
            labels: labels,
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

function barChart(id, labels, serieLabel, data, color) {
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
        type: 'bar',
        data: {
            labels: labels,
            datasets: dataset,
        },
        options: {
            scales: {
                xAxes: [{
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    ticks : {
                        beginAtZero: true,
                        stepSize: 1,
                    },
                }]
            }
        },
    });
}

// LOAD GRAPHS
$(document).ready(function() {
    doughnutChart(
        'contractor-clauses',
        labelYesNo,
        contractorClausesData,
        [colorBlue, colorRed]
    );

    doughnutChart(
        'contractor-conform',
        labelYesNo,
        contractorConformData,
        [colorBlue, colorRed]
    );

    radarChart(
        'maturity-radar',
        maturityLabels,
        maturitySerieLabel,
        maturityData,
        [colorBlueOpacity, colorRedOpacity]
    );

    barChart(
        'treatment-bar',
        treatmentLabels,
        ['Conforme', 'Non conforme'],
        [treatmentDatasetYes, treatmentDatasetNo],
        [colorBlueOpacity, colorRedOpacity]
    );
});
