

    window.onload = function() {
        const etiquetas = datos_factores.map(f => f.DESCRIPCION);
        const cantidades = datos_factores.map(f => parseInt(f["COUNT(*)"]));

        const ctx = document.getElementById('graficoFactores').getContext('2d');
        const grafico = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [{
                    label: 'Cantidad de casos por factor',
                    data: cantidades,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Factores'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Factores más comunes'
                    }
                }
            }
        });



        // careacteristicas

        const factoresUnicos = [...new Map(
            datos_caracteristicas.map(item => [item.COD_FACTOR, item.FACT_DESCRIPCION])
        )].map(([codigo, descripcion]) => ({
            codigo,
            descripcion
        }));

        const selectFactor = document.getElementById("selectFactor");

        // Llenar el select y seleccionar el primero por defecto
        factoresUnicos.forEach((factor, index) => {
            const option = document.createElement("option");
            option.value = factor.codigo;
            option.textContent = factor.descripcion;
            if (index === 0) option.selected = true; // seleccionar el primero
            selectFactor.appendChild(option);
        });

        let grafico_c = null;

        // Función para mostrar el gráfico
        function mostrarGrafico(codFactor) {
            const caracteristicas = datos_caracteristicas.filter(
                item => item.COD_FACTOR === codFactor
            );

            const etiquetas = caracteristicas.map(c => c.CARACTERISTICA);
            const cantidades = caracteristicas.map(c => parseInt(c["COUNT(*)"]));

            const ctx = document.getElementById("graficoCaracteristicas").getContext("2d");

            if (grafico_c) grafico_c.destroy();

            grafico_c = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: 'Cantidad por característica',
                        data: cantidades,
                        backgroundColor: 'rgba(153, 33, 209, 0.61)',
                        borderColor: 'rgb(174, 219, 48)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    plugins: {
                        title: {
                            display: true,
                            text: 'Características del factor seleccionado'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Características'
                            }
                        }
                    }
                }
            });
        }

        // Escuchar cambios en el select
        selectFactor.addEventListener("change", function() {
            mostrarGrafico(this.value);
        });

        // Mostrar gráfico inicial al cargar
        if (factoresUnicos.length > 0) {
            mostrarGrafico(factoresUnicos[0].codigo);
        }


    }