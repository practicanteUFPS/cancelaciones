window.onload = function () {


	cargarYPredecirDesdeJSON().then(datos => {


		generar_grafico_probabilidad(datos);

		generar_grafico_sexo(datos);

		generar_grafico_edad(datos);

		datos.forEach(est => {
			const fila = document.querySelector(`tr[data-codigo="${est.codigo}"]`);
			if (fila) {
				fila.querySelector('.predicho').textContent = est.predicho == 1 ? 'Podria desertar' : 'Permanece';
				fila.querySelector('.probabilidad').textContent = est.probabilidad.toFixed(2) * 100;
			}
		});

		//$('#tablaPrediccion').DataTable();

		$(document).ready(function () {
			var table = $('#tablaPrediccion').DataTable({
				responsive: true,
				ordering: true,
				paging: true,
				info: false,
				orderCellsTop: true,
				dom: 'lrtiBp',
				columnDefs: [
					{ targets: [-1, -2], orderable: false } // desactiva la última columna
				],
				buttons: [
					{
						extend: 'csvHtml5',
						text: 'Exportar CSV',
						className: 'btn btn-default btn-sm',
						exportOptions: {
							columns: function (idx, data, node) {
								const total = table.columns(':visible').indexes().length;
								return idx < total - 2;
							}
						}
					},
					{
						extend: 'excelHtml5',
						text: 'Exportar Excel',
						className: 'btn btn-default btn-sm',
						exportOptions: {
							columns: function (idx, data, node) {
								const total = table.columns(':visible').indexes().length;
								return idx < total - 2;
							}
						}
					},
					{
						text: 'Exportar PDF',
						className: 'btn btn-default btn-sm',
						action: function (e, dt, node, config) {
							exportarPDF(dt);
						}
					}
				],
				initComplete: function () {
					var api = this.api();
					api.columns().every(function () {
						var that = this;
						$('input[type="text"]:not(.filtro-aprobados):not(.filtro-cursados):not(.filtro-semestres):not(.filtro-probabilidad)', this.header()).on('keyup change clear', function () {
							if (that.search() !== this.value) {
								that.search(this.value).draw();
							}
						});
					});

				}
			});

			// Filtro personalizado
			$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
				// Columnas (ajusta índices si cambian)
				var semestres = parseFloat(data[6]) || 0;
				var aprobados = parseFloat(data[7]) || 0;
				var cursados = parseFloat(data[8]) || 0;
				var probabilidad = parseFloat(data[10]) || 0;

				var edad = parseInt(data[4]) || 0;
				var sexo = data[3];
				var carrera = data[5];
				var prediccion = data[9];

				// Lectura de filtros numéricos
				var opSem = $('.operador-semestres').val();
				var valSem = parseFloat($('.filtro-semestres').val()) || null;

				var opAprob = $('.operador-aprobados').val();
				var valAprob = parseFloat($('.filtro-aprobados').val()) || null;

				var opCurs = $('.operador-cursados').val();
				var valCurs = parseFloat($('.filtro-cursados').val()) || null;

				var opProb = $('.operador-probabilidad').val();
				var valProb = parseFloat($('.filtro-probabilidad').val()) || null;

				// Filtros select
				var filtroSexo = $('.filtro-sexo').val();
				var filtroCarrera = $('.filtro-carrera').val();
				var filtroPrediccion = $('.filtro-prediccion').val();
				var filtroEdad = $('.filtro-edad').val();

				// Evaluador de operadores
				function evalFiltro(op, col, val) {
					if (val === null) return true;
					switch (op) {
						case '>': return col > val;
						case '<': return col < val;
						case '>=': return col >= val;
						case '<=': return col <= val;
						default: return col === val;
					}
				}

				// Filtro por edad
				var evalEdad = true;
				switch (filtroEdad) {
					case 'menor18':
						evalEdad = edad < 18;
						break;
					case '18_24':
						evalEdad = edad >= 18 && edad <= 24;
						break;
					case '25_29':
						evalEdad = edad >= 25 && edad <= 29;
						break;
					case '30mas':
						evalEdad = edad >= 30;
						break;
				}

				return evalFiltro(opSem, semestres, valSem) &&
					evalFiltro(opAprob, aprobados, valAprob) &&
					evalFiltro(opCurs, cursados, valCurs) &&
					evalFiltro(opProb, probabilidad, valProb) &&
					(filtroSexo === "" || sexo === filtroSexo) &&
					(filtroCarrera === "" || carrera === filtroCarrera) &&
					(filtroPrediccion === "" || prediccion === filtroPrediccion) &&
					evalEdad;
			});

			// Redibujar al cambiar cualquier filtro
			$('.operador-aprobados, .filtro-aprobados, .operador-cursados, .filtro-cursados, .operador-semestres, .filtro-semestres, .operador-probabilidad, .filtro-probabilidad, .filtro-sexo, .filtro-carrera, .filtro-prediccion, .filtro-edad')
				.on('change keyup', function () {
					table.draw();
				});

			// Evitar que clics en filtros activen ordenamiento
			$('#datatable thead tr.filters input, #datatable thead tr.filters select').on('click', function (e) {
				e.stopPropagation();
			});



		});



	});

}







async function cargarYPredecirDesdeJSON() {



	if (typeof datos === "undefined" || !Array.isArray(datos)) {
		alert("No se encontró la variable 'datos'");
		return;
	}

	// Cargar modelo
	const session = await ort.InferenceSession.create("../assets/modelo.onnx");

	const resultados = [];
	const reales = [];
	const predichos = [];

	const start = performance.now();

	for (const fila of datos) {
		const codigo = fila.CODIGO_ALUMNO;
		const DOCUMENTO = fila.DOCUMENTO;
		const APROBADOS = fila.APROBADOS;
		const CURSADOS = fila.CURSADOS;
		const NOMBRE_CARRERA = fila.NOMBRE_CARRERA;
		const NUM_SEM_MAT = fila.NUM_SEM_MAT;
		const NOMBRES = fila.NOMBRES;
		const SEXO_C = fila.SEXO_C;
		const EDAD_ACTUAL = fila.EDAD_ACTUAL;

		try {
			const entrada = [];
			for (let i = 0; i <= 13; i++) {
				let valor = fila["f" + i];

				// Convertir BigInt explícitamente si existe
				if (typeof valor === "bigint") {
					valor = Number(valor);
				}

				if (valor === undefined || valor === null || isNaN(valor)) {
					throw new Error(`Valor inválido en f${i}: ${valor}`);
				}

				entrada.push(parseFloat(valor));
			}

			// Crear tensor y ejecutar modelo
			const tensor = new ort.Tensor("float32", new Float32Array(entrada), [1, 14]);
			const output = await session.run({
				float_input: tensor
			});
			//console.log("Output completo:", output);
			//console.log("Clave:", Object.keys(output));
			//console.log("Output data:", output[Object.keys(output)[0]].data);
			const prob = output["probabilities"].data[1];
			//const pred = prob >= 0.5 ? 1 : 0;
			const pred = prob >= 0.6 ? 1 : 0;
			//console.log(prob);

			predichos.push(pred);
			reales.push(fila.DESERCION);
			resultados.push({
				codigo,
				DOCUMENTO,
				APROBADOS,
				CURSADOS,
				NOMBRE_CARRERA,
				NUM_SEM_MAT,
				NOMBRES,
				SEXO_C,
				EDAD_ACTUAL,
				probabilidad: prob,
				predicho: pred
			});

		} catch (error) {
			console.error(`❌ Error con alumno ${codigo}:`, error.message);
		}
	}



	const end = performance.now();
	const tiempoMs = end - start;

	console.log(tiempoMs);
	return resultados;
}



async function generar_grafico_probabilidad(datos) {
	// === HISTOGRAMA DE PROBABILIDAD ===
	const bins = Array(10).fill(0); // 10 intervalos [0.0 - 1.0]
	datos.forEach(est => {
		const bin = Math.floor(est.probabilidad * 10);
		bins[bin === 10 ? 9 : bin]++;
	});

	const histogramaChart = new Chart(document.getElementById('histogramaChart'), {
		type: 'bar',
		data: {
			labels: ['0–10%', '10–20%', '20–30%', '30–40%', '40–50%', '50–60%', '60–70%', '70–80%', '80–90%', '90–100%'],
			datasets: [{
				label: 'Cantidad de estudiantes',
				data: bins,
				backgroundColor: 'rgba(54, 162, 235, 0.6)',
				borderColor: 'rgba(54, 162, 235, 1)',
				borderWidth: 1
			}]
		},
		options: {
			plugins: {
				title: {
					display: true,
					text: 'Histograma de Probabilidad de Deserción'
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						stepSize: 1
					}
				}
			}
		}
	});

}


async function generar_grafico_sexo(datos) {
	// === PROPORCIÓN POR SEXO ===
	const sexoStats = {
		F: {
			total: 0,
			desertaron: 0
		},
		M: {
			total: 0,
			desertaron: 0
		}
	};

	datos.forEach(est => {
		const sexo = est.SEXO_C;
		if (sexoStats[sexo]) {
			sexoStats[sexo].total++;
			if (est.predicho === 1) {
				sexoStats[sexo].desertaron++;
			}
		}
	});

	const sexos = Object.keys(sexoStats);
	const cantidades = sexos.map(sexo => sexoStats[sexo].desertaron);


	const proporcionSexoChart = new Chart(document.getElementById('proporcionSexoChart'), {
		type: 'bar',
		data: {
			labels: sexos.map(s => s === 'F' ? 'Femenino' : 'Masculino'),
			datasets: [{
				label: '% Predichos como Desertores',
				data: cantidades,
				backgroundColor: ['#ff6384', '#36a2eb'],
				borderColor: ['#ff6384', '#36a2eb'],
				borderWidth: 1
			}]
		},
		options: {
			plugins: {
				title: {
					display: true,
					text: 'Cantidad de Estudiantes Predichos como Desertores Sexo'
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						callback: value => `${value}%`
					}
				}
			}
		}
	});

}


async function generar_grafico_edad(datos) {
	// Inicializar contadores por rango
	const rangos = {
		"<18": 0,
		"18-24": 0,
		"25-29": 0,
		"30+": 0
	};

	// Clasificación por edad
	datos.forEach(est => {
		const edad = parseInt(est.EDAD_ACTUAL);
		if (est.predicho === 1 && !isNaN(edad)) {
			if (edad < 18) rangos["<18"]++;
			else if (edad <= 24) rangos["18-24"]++;
			else if (edad <= 29) rangos["25-29"]++;
			else rangos["30+"]++;
		}
	});

	const desercionesEdadChart = new Chart(document.getElementById('desercionesEdadChart'), {
		type: 'bar',
		data: {
			labels: Object.keys(rangos),
			datasets: [{
				label: 'Cantidad de estudiantes desertores',
				data: Object.values(rangos),
				backgroundColor: 'rgba(255, 99, 132, 0.6)',
				borderColor: 'rgba(255, 99, 132, 1)',
				borderWidth: 1
			}]
		},
		options: {
			plugins: {
				title: {
					display: true,
					text: 'Deserciones por Rango de Edad'
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						stepSize: 1
					}
				}
			}
		}
	});

}

async function getBase64FromImageUrl(url) {
    const response = await fetch(url);
    const blob = await response.blob();
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
    });
}

// Función para exportar el PDF
async function exportarPDF(dt) {
	const { jsPDF } = window.jspdf;
	const doc = new jsPDF({  unit: 'pt' }); // 'pt' para precisión


     const logoUrl = baseUrl + 'assets/img/ufps/ufps.png';
    // Convertir la imagen a base64
    const logoBase64 = await getBase64FromImageUrl(logoUrl);

  	// Ajuste de tamaño del logo: 120x40 pt
	const logoWidth = 120;
	const logoHeight = 40;
	const pageWidth = doc.internal.pageSize.getWidth();
	const logoX = 40; // o (pageWidth - logoWidth) / 2 para centrar
	const logoY = 20;

	doc.addImage(logoBase64, 'PNG', logoX, logoY, logoWidth, logoHeight);
    doc.setFontSize(14);
	doc.text("Estimaciones de alumnos", logoX + logoWidth + 20, logoY + 20); 


	const columnasDeseadas = [0, 2, 3, 4, 6, 7, 8, 9, 10];

	// Captura encabezados filtrados
	const headerCells = $('#tablaPrediccion thead tr:first th').map(function (index) {
		return columnasDeseadas.includes(index) ? $(this).text().trim() : null;
	}).get().filter(Boolean);

	const data = [];
	dt.rows({ search: 'applied' }).every(function () {
		const row = this.node();
		const rowData = [];

		$('td', row).each(function (index) {
			if (columnasDeseadas.includes(index)) {
				rowData.push($(this).text().trim());
			}
		});

		data.push(rowData);
	});

	if (data.length === 0) {
		alert("No hay datos para exportar.");
		return;
	}

	doc.autoTable({
		head: [headerCells],
		body: data,
		startY: 60,
		styles: {
			fontSize: 9,
			overflow: 'linebreak',
			cellPadding: 5,
		},
		columnStyles: {
			0: { cellWidth: 60 },   // Código (primera columna en el PDF)
			1: { cellWidth: 120 },  // Nombre
			2: { cellWidth: 40 },   // Sexo
			3: { cellWidth: 30 },   // Edad
			4: { cellWidth: 50 },   // Semestres
			5: { cellWidth: 60 },   // Créditos aprobados
			6: { cellWidth: 60 },   // Créditos cursados
			7: { cellWidth: 50 },   // Predicción
			8: { cellWidth: 70 },   // Probabilidad
		},
		tableWidth: 'wrap', // ⚠️ Esto es clave para evitar el error de ancho
		margin: { left: 40, right: 40 },
		headStyles: {
			fillColor: [30, 144, 255]
		}
	});

	doc.save("reporte_filtrado.pdf");
}



function modal(boton) {

	let documento = boton.value;

	console.log(documento);

	//document.getElementById("docAlumno").textContent = documento;
	document.getElementById("modalContent").innerHTML = "Cargando información...";
	document.getElementById("contactModalLabel").innerHTML = "Informacion de contacto"
	let url = baseUrl + 'datos_per/get_datos' + `?documento=${documento}&carrera=${cod_carrera}`;

	// Muestra el modal
	$("#contactModal").modal("show");

	fetch(url)
		.then(response => response.json())
		.then(data => {
			console.log(data);


			// Construir el contenido del modal
			let contenido = `
                <p><strong>Documento:</strong> ${data.DOCUMENTO || 'N/A'}</p>
                <p><strong>Nombres:</strong> ${data.NOMBRES || 'N/A'}</p>
                <p><strong>Fecha de Nacimiento:</strong> ${data.FECHA_NACIMIENTO || 'N/A'}</p>
                <p><strong>Lugar Documento:</strong> ${data.LUG_DOCUMENTO || 'N/A'}</p>
                <p><strong>Municipio de Nacimiento:</strong> ${data.MPIO_NACIMIENTO || 'N/A'}</p>
                <p><strong>Sexo:</strong> ${data.SEXO || 'N/A'}</p>
                <p><strong>Tipo de Documento:</strong> ${data.TIPO_DOCUMENTO || 'N/A'}</p>
                <p><strong>Dirección:</strong> ${data.DIRECCION || 'N/A'}</p>
                <p><strong>Celular:</strong> ${data.CELULAR || 'N/A'}</p>
                <p><strong>Email:</strong> ${data.EMAIL || 'N/A'}</p>
                <p><strong>Email UFPS:</strong> ${data.EMAIL_UFPS || 'N/A'}</p>
            `;

			// Insertar los datos en el modal
			document.getElementById("modalContent").innerHTML = contenido;
		})
		.catch(error => {
			document.getElementById("modalContent").innerHTML =
				"<p class='text-danger'>Error al cargar los datos.</p>";
			console.error("Error en fetch:", error);
		});
}

function cargarNotas(event) {
	const valor = event.value;
	console.log("Valor del botón:", valor);
	let url_nota = baseUrl + 'nota/mostrar_notas_json' + valor;
	console.log(url_nota);
	fetch(url_nota)
		.then(response => response.json())
		.then(data => {
			const accordion = document.getElementById('accordionNotas');
			accordion.innerHTML = ''; // Limpiar

			data.forEach((semestre, index) => {
				const collapseId = `collapse_${index}`;

				// Construcción de filas
				let filas = '';
				semestre.NOTAS.forEach(nota => {
					filas += `
                        <tr>
                            <td>${nota.COD_CARRERA}${nota.COD_MATERIA}</td>
                            <td>${nota.NOMBRE}</td>
                            <td>${nota.DEFINITIVA}</td>
                            <td>${nota.CREDITOS}</td>
                            <td>${nota.COD_CAR_MAT}${nota.COD_MAT_MAT}</td>
                        </tr>
                    `;
				});

				// Construcción del panel del acordeón
				const panel = `
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordionNotas" href="#${collapseId}" ${index > 0 ? 'class="collapsed"' : ''}>
                                    Semestre: ${semestre.ANO}-${semestre.SEMESTRE}
                                </a>
                            </h4>
                        </div>
                        <div id="${collapseId}" class="panel-collapse collapse ${index === 0 ? 'in' : ''}">
                            <div class="box-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Definitiva</th>
                                            <th>Créditos</th>
                                            <th>Matriculado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${filas}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
				accordion.innerHTML += panel;
			});

			// Mostrar modal (Bootstrap 3)
			$('#modalNotas').modal('show');
		})
		.catch(error => {
			console.error('Error al cargar las notas:', error);
			alert('Ocurrió un error al obtener las notas.');
		});
}