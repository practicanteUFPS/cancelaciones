window.onload = function () {
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

	let chart;

	function dibujarGrafico(cantidad) {
		let subset;
		if (cantidad === 'todos') {
			subset = datos;
		} else {
			cantidad = parseInt(cantidad);
			subset = datos.slice(-cantidad);
		}

		const labels = subset.map(d => d.SEMESTRE);
		const inactivosF = subset.map(d => parseFloat(d.PCT_INACTIVOS_F));
		const inactivosM = subset.map(d => parseFloat(d.PCT_INACTIVOS_M));

		const data = {
			labels: labels,
			datasets: [{
				label: 'Inactivas F (%)',
				data: inactivosF,
				backgroundColor: '#FF6384'
			},
			{
				label: 'Inactivos M (%)',
				data: inactivosM,
				backgroundColor: '#36A2EB'
			}
			]
		};

		const config = {
			type: 'bar',
			data: data,
			options: {
				responsive: true,
				plugins: {
					tooltip: {
						callbacks: {
							label: function (context) {
								return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
							}
						}
					},
					legend: {
						position: 'top'
					},
					title: {
						display: false
					}
				},
				scales: {
					x: {
						stacked: false
					},
					y: {
						stacked: false,
						beginAtZero: true,
						title: {
							display: true,
							text: '% de Inactivos'
						}
					}
				}
			}
		};

		// Destruye el gráfico anterior si existe
		if (chart) {
			chart.destroy();
		}

		const ctx = document.getElementById('myChart').getContext('2d');
		chart = new Chart(ctx, config);
	}

	// Inicializa el gráfico con valor por defecto
	document.getElementById('filtroSemestres').value = '5';
	dibujarGrafico('5');

	// Evento de cambio
	document.getElementById('filtroSemestres').addEventListener('change', function () {
		dibujarGrafico(this.value);
	});
};