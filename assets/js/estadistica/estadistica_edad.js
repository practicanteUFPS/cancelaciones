document.addEventListener("DOMContentLoaded", function() {
        
    
    var table = $('#datatable').DataTable({
		searching: false,       // ❌ Sin campo de búsqueda
		paging: true,           // ✅ Paginación
		order: [],
		ordering: true,         // ✅ Orden por columnas
		lengthChange: true,     // ✅ Selección de registros por página
		language: {
			lengthMenu: "Mostrar _MENU_ registros por página",
			zeroRecords: "No se encontraron resultados",
			info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
			infoEmpty: "No hay registros disponibles",
			infoFiltered: "(filtrado de _MAX_ registros totales)",
			paginate: {
				first: "Primero",
				last: "Último",
				next: "Siguiente",
				previous: "Anterior"
			}
		}

	});

    
    const ctx = document.getElementById("graficoEdad").getContext("2d");

        let chart;
        let filtrosEdad = new Set(["<18", "18-24", "25-29", ">30"]);

        function procesarDatos(data) {
            const etiquetas = data.map(d => d.SEMESTRE);
            const activos_inactivos_por_grupo = {
                "<18 Activos": [],
                "<18 Inactivos": [],
                "18-24 Activos": [],
                "18-24 Inactivos": [],
                "25-29 Activos": [],
                "25-29 Inactivos": [],
                ">30 Activos": [],
                ">30 Inactivos": []
            };

            data.forEach(d => {
                const total = d.TOTAL > 0 ? d.TOTAL : 1;
                activos_inactivos_por_grupo["<18 Activos"].push((d.ACTIVOS_MENOR_18 / total) * 100);
                activos_inactivos_por_grupo["<18 Inactivos"].push((d.INACTIVOS_MENOR_18 / total) * 100);

                activos_inactivos_por_grupo["18-24 Activos"].push((d.ACTIVOS_ENTRE_18_24 / total) * 100);
                activos_inactivos_por_grupo["18-24 Inactivos"].push((d.INACTIVOS_ENTRE_18_24 / total) * 100);

                activos_inactivos_por_grupo["25-29 Activos"].push((d.ACTIVOS_ENTRE_25_29 / total) * 100);
                activos_inactivos_por_grupo["25-29 Inactivos"].push((d.INACTIVOS_ENTRE_25_29 / total) * 100);

                activos_inactivos_por_grupo[">30 Activos"].push((d.ACTIVOS_MAYOR_30 / total) * 100);
                activos_inactivos_por_grupo[">30 Inactivos"].push((d.INACTIVOS_MAYOR_30 / total) * 100);
            });

            const todosDatasets = [{
                    label: "<18 Activos",
                    data: activos_inactivos_por_grupo["<18 Activos"],
                    backgroundColor: "#4CAF50",
                    stack: "<18"
                },
                {
                    label: "<18 Inactivos",
                    data: activos_inactivos_por_grupo["<18 Inactivos"],
                    backgroundColor: "#C8E6C9",
                    stack: "<18"
                },
                {
                    label: "18-24 Activos",
                    data: activos_inactivos_por_grupo["18-24 Activos"],
                    backgroundColor: "#2196F3",
                    stack: "18-24"
                },
                {
                    label: "18-24 Inactivos",
                    data: activos_inactivos_por_grupo["18-24 Inactivos"],
                    backgroundColor: "#BBDEFB",
                    stack: "18-24"
                },
                {
                    label: "25-29 Activos",
                    data: activos_inactivos_por_grupo["25-29 Activos"],
                    backgroundColor: "#FF9800",
                    stack: "25-29"
                },
                {
                    label: "25-29 Inactivos",
                    data: activos_inactivos_por_grupo["25-29 Inactivos"],
                    backgroundColor: "#FFE0B2",
                    stack: "25-29"
                },
                {
                    label: ">30 Activos",
                    data: activos_inactivos_por_grupo[">30 Activos"],
                    backgroundColor: "#9C27B0",
                    stack: ">30"
                },
                {
                    label: ">30 Inactivos",
                    data: activos_inactivos_por_grupo[">30 Inactivos"],
                    backgroundColor: "#E1BEE7",
                    stack: ">30"
                }
            ];

            // Filtra los datasets por los grupos de edad seleccionados
            const datasetsFiltrados = todosDatasets.filter(ds => filtrosEdad.has(ds.stack));

            return {
                etiquetas,
                datasets: datasetsFiltrados
            };
        }

        function renderGrafico(dataFiltrada) {
            const datos = procesarDatos(dataFiltrada);

            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: datos.etiquetas,
                    datasets: datos.datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: "Semestre"
                            }
                        },
                        y: {
                            stacked: true,
                            max: 100,
                            ticks: {
                                callback: value => value + '%'
                            },
                            title: {
                                display: true,
                                text: "Porcentaje"
                            }
                        }
                    }
                }
            });
        }

        document.getElementById("filtroSemestres").addEventListener("change", function() {
            const cantidad = this.value;
            let filtrado = datosOriginales;
            if (cantidad !== "todos") {
                filtrado = datosOriginales.slice(-parseInt(cantidad));
            }
            renderGrafico(filtrado);
        });

        document.querySelectorAll(".filtroEdad").forEach(cb => {
            cb.addEventListener("change", () => {
                filtrosEdad = new Set(
                    Array.from(document.querySelectorAll(".filtroEdad:checked")).map(cb => cb.value)
                );
                const cantidad = document.getElementById("filtroSemestres").value;
                let filtrado = datosOriginales;
                if (cantidad !== "todos") {
                    filtrado = datosOriginales.slice(-parseInt(cantidad));
                }
                renderGrafico(filtrado);
            });
        });

        // Inicializar con 5 semestres
        document.getElementById("filtroSemestres").value = "5";
        renderGrafico(datosOriginales.slice(-5));
    });