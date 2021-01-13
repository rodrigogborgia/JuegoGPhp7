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
                labelString: 'Tiempo transcurrido'
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
                labelString: 'Valores'
            }
        }]
    },
    title: {
        display: true,
        text: 'Movimientos dentro del juego'
    }
};

// Chart Data
var chartData = {
    labels: [],
    datasets: [ {
        label: "Movimientos Realizados",
        data: [],
        backgroundColor: "rgba(81,117,224,.5)",
        borderColor: "transparent",
        pointBorderColor: "#5175E0",
        pointBackgroundColor: "#FFF",
        pointBorderWidth: 2,
        pointHoverBorderWidth: 2,
        pointRadius: 4,
    }, {
        label: "Aciertos",
        data: [],
        backgroundColor: "rgba(22,211,154,.5)",
        borderColor: "transparent",
        pointBorderColor: "#28D094",
        pointBackgroundColor: "#FFF",
        pointBorderWidth: 2,
        pointHoverBorderWidth: 2,
        pointRadius: 4,
    }, {
        label: "Equivocaciones",
        data: [],
        backgroundColor: "rgba(249,142,118,.5)",
        borderColor: "transparent",
        pointBorderColor: "#F98E76",
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