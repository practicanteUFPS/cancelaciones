 let chart;

    function renderizarGrafico(cantidad) {
        const limite = (cantidad === 'todos') ? etiquetas.length : parseInt(cantidad);
        const offset = etiquetas.length - limite;

        const etiquetasFiltradas = etiquetas.slice(offset);
        const activosFiltrados = activos.slice(offset);
        const inactivosFiltrados = inactivos.slice(offset);
        const graduadosFiltrados = graduados.slice(offset);

        const activosCantFiltrados = activosCant.slice(offset);
        const inactivosCantFiltrados = inactivosCant.slice(offset);
        const graduadosCantFiltrados = graduadosCant.slice(offset);

        const data = {
            labels: etiquetasFiltradas,
            datasets: [{
                    label: "% Activos",
                    backgroundColor: "#007bff",
                    data: activosFiltrados,
                    _cantidad: activosCantFiltrados
                },
                {
                    label: "% Inactivos",
                    backgroundColor: "#dc3545",
                    data: inactivosFiltrados,
                    _cantidad: inactivosCantFiltrados
                },
                {
                    label: "% Graduados",
                    backgroundColor: "#28a745",
                    data: graduadosFiltrados,
                    _cantidad: graduadosCantFiltrados
                }
            ]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: false
                    },
                    y: {
                        stacked: false,
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + "%";
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const porcentaje = context.raw;
                                const cantidad = context.dataset._cantidad[context.dataIndex];
                                return `${label}: ${porcentaje.toFixed(1)}% (${cantidad} estudiantes)`;
                            }
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                }
            }
        };

        if (chart) chart.destroy();
        const ctx = document.getElementById('resumenChart').getContext('2d');
        chart = new Chart(ctx, config);
    }

    window.onload = function() {
        document.getElementById('filtroSemestres').value = '5';
        renderizarGrafico('5');

        document.getElementById('filtroSemestres').addEventListener('change', function() {
            renderizarGrafico(this.value);
        });

            $('#miTabla').DataTable({
        searching: false,       // ❌ Sin campo de búsqueda
        paging: true,           // ✅ Paginación
        order : [],
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
    };