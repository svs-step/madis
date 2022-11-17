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
                display: false,
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

function doughnutChartPadding(id, labels, data, color) {
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
            layout: {
                padding: {
                    left: 15,
                    right: 15,
                }
            },
            legend: {
                display: false,
                labels: {
                    defaultFontSize: 5
                },
            },
            tooltips: {
                displayColors: false,
                bodyFontSize: 10,
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

function pieChart(id, labels, data, color) {
    new Chart($('#' + id), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: color,
                hoverBackgroundColor: color
            }],
        },
        options: {
            tooltips: {
                displayColors: false,
            },
            responsive: true,
        }
    });
}

function radarChart(id, labels, serieLabel, data, color, legend=false) {
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
            },
            legend: {
                display: legend,
            },
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
                        stepSize: 0.5,
                    },
                }]
            }
        },
    });
}

// LOAD GRAPHS
$(document).ready(function() {
    if (contractorClausesData.length > 0) {
        doughnutChart(
            'contractor-clauses',
            labelYesNo,
            contractorClausesData,
            [colorBlue, colorRed]
        );
    }

    if (contractorAdoptedSecurityFeaturesData.length > 0) {
        doughnutChart(
            'contractor-adopted-security-features',
            labelYesNo,
            contractorAdoptedSecurityFeaturesData,
            [colorBlue, colorRed]
        )
    }

    if (contractorMaintainsTreatmentRegisterData.length > 0) {
        doughnutChart(
            'contractor-maintains-treatment-register',
            labelYesNo,
            contractorMaintainsTreatmentRegisterData,
            [colorBlue, colorRed]
        );
    }

    if (contractorSendingDataOutsideEuData.length > 0) {
        doughnutChart(
            'contractor-sending-data-outside-eu',
            labelYesNo,
            contractorSendingDataOutsideEuData,
            [colorRed, colorBlue]
        );
    }

    if (requestTypeData.length > 0) {
        doughnutChartPadding(
            'request-type',
            requestTypeLabel,
            requestTypeData,
            [colorBlue, colorRed, colorGreen, colorOrange, colorPurple, colorTeal]
        );
    }

    if (requestStatusData.length > 0) {
        doughnutChartPadding(
            'request-status',
            requestStatusLabel,
            requestStatusData,
            [colorGreen, colorRed, colorOrange]
        );
    }

    if (maturityData.length > 0) {
        radarChart(
            'maturity-radar',
            maturityLabels,
            maturitySerieLabel,
            maturityData,
            [colorBlueOpacity, colorRedOpacity],
            true
        );
    }

    if (treatmentDatasetYes.length  > 0 && treatmentDatasetNo.length > 0) {
        barChart(
            'treatment-bar',
            treatmentLabels,
            ['Conforme', 'Non conforme'],
            [treatmentDatasetYes, treatmentDatasetNo],
            [colorBlueOpacity, colorRedOpacity]
        );
    }

    if (conformiteTraitementData.length > 0) {
        pieChart(
            'conformiteTraitement-pie',
            conformiteTraitementLabels,
            conformiteTraitementData,
            conformiteTraitementColors
        );
    }
});
