 $(document).ready(function() {

        var barChartData = {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                    label: "Total",
                    fillColor: "rgba(54,162,235,0.6)",
                    strokeColor: "rgba(54,162,235,1)",
                    data: <?= json_encode($totales) ?>
                },
                {
                    label: "Activos",
                    fillColor: "rgba(75,192,192,0.6)",
                    strokeColor: "rgba(75,192,192,1)",
                    data: <?= json_encode($activos) ?>
                },
                {
                    label: "Inactivos",
                    fillColor: "rgba(255,206,86,0.6)",
                    strokeColor: "rgba(255,206,86,1)",
                    data: <?= json_encode($inactivos) ?>
                },
                {
                    label: "Graduados",
                    fillColor: "rgba(153,102,255,0.6)",
                    strokeColor: "rgba(153,102,255,1)",
                    data: <?= json_encode($graduados) ?>
                }
            ]
        };

        var ctx = document.getElementById("resumenChart").getContext("2d");
        new Chart(ctx).Bar(barChartData, {
            responsive: true,
            scaleBeginAtZero: true
        });


    });