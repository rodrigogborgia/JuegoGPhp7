var chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
        position: 'bottom',
    },
    hover: {
        mode: 'label'
    },
    scales: {
        xAxes: [{
            display: true,
            gridLines: {
                color: "#f3f3f3",
                drawTicks: false,
            },
            scaleLabel: {
                display: true,
                labelString: 'Tiempo'
            }
        }],
        yAxes: [{
            display: true,
            gridLines: {
                color: "#f3f3f3",
                drawTicks: false,
            },
            scaleLabel: {
                display: true,
                labelString: 'Pasos'
            }
        }]
    },
    title: {
        display: true,
        text: 'Distancia del objetivo'
    }
};

// Chart Data
var chartData = {
    labels: [],
    datasets: [ {
        label: "Distancia del objetivo",
        data: [],
        backgroundColor: "rgba(81,117,224,.5)",
        borderColor: "transparent",
        pointBorderColor: "#5175E0",
        pointBackgroundColor: "#FFF",
        pointBorderWidth: 2,
        pointHoverBorderWidth: 2,
        pointRadius: 4,
    }]
};

var config = {
    type: 'line',

    // Chart Options
    options : chartOptions,

    // Chart Data
    data : chartData
};