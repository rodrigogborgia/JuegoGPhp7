/*=========================================================================================
    File Name: line-stacked-area.js
    Description: Chartjs line stacked area chart
    ----------------------------------------------------------------------------------------
    Item Name: Modern Admin - Clean Bootstrap 4 Dashboard HTML Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

// Line stacked area chart
// ------------------------------
$(window).on("load", function(){

    //Get the context of the Chart canvas element we want to select
    // var ctx = document.getElementById("line-stacked-area").getContext("2d");
    var ctx = $('#line-stacked-area');

    // Chart Options
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
        labels: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 210, 220, 230, 240, 250, 260, 270, 280, 290, 300, 310, 320, 330, 340, 350, 360, 370, 380, 390, 400, 410, 420, 430, 440, 450, 460, 470, 480, 490, 500, 510, 520, 530, 540, 550, 560, 570, 580, 590, 600],
        datasets: [ {
            label: "Movimientos Realizados",
            data: [3, 1, 2, 1, 4, 1, 3, 0, 2, 3, 2, 0, 2, 3, 1, 3, 4, 0, 1, 3, 1, 0, 2, 4, 2, 3, 0, 1, 1, 0, 2, 3, 3, 3, 3, 1, 1, 3, 1, 1, 1, 2, 3, 1, 2, 2, 2, 4, 1, 0, 0, 1, 0, 2, 3, 4, 1, 4, 0, 4,1],
            backgroundColor: "rgba(81,117,224,.5)",
            borderColor: "transparent",
            pointBorderColor: "#5175E0",
            pointBackgroundColor: "#FFF",
            pointBorderWidth: 2,
            pointHoverBorderWidth: 2,
            pointRadius: 4,
        }, {
            label: "Aciertos",
            data: [3, 0, 0, 1, 1, 0, 1, 2, 0, 4, 3, 2, 3, 1, 4, 1, 4, 3, 2, 3, 0, 2, 3, 4, 4, 4, 4, 1, 4, 4, 3, 1, 3, 2, 1, 3, 0, 3, 3, 2, 3, 4, 4, 4, 1, 1, 0, 0, 1, 1, 3, 4, 2, 2, 3, 4, 3, 3, 3, 2,0],
            backgroundColor: "rgba(22,211,154,.5)",
            borderColor: "transparent",
            pointBorderColor: "#28D094",
            pointBackgroundColor: "#FFF",
            pointBorderWidth: 2,
            pointHoverBorderWidth: 2,
            pointRadius: 4,
        }, {
            label: "Equivocaciones",
            data: [3, 3, 1, 2, 3, 4, 1, 2, 3, 4, 1, 3, 0, 1, 1, 4, 0, 0, 0, 1, 1, 0, 1, 0, 4, 1, 3, 4, 2, 0, 1, 4, 3, 2, 4, 2, 4, 3, 2, 1, 4, 0, 2, 2, 1, 3, 0, 1, 2, 4, 0, 1, 2, 3, 2, 2, 1, 3, 0, 1,2],
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

    // Create the chart
    var stackedAreaChart = new Chart(ctx, config);
});