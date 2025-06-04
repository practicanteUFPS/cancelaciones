$(document).ready(function () {
	var table = $('#datatable').DataTable({
		responsive: true,
		ordering: true,
		paging: true,     // Quita la paginación  
		dom: 'rtBip',
		buttons: [
			{
				extend: 'csvHtml5',
				text: 'Exportar CSV',
				className: 'btn btn-default btn-sm',
				exportOptions: {
					columns: function (idx, data, node) {
						const total = table.columns(':visible').indexes().length;
						return idx < total - 1;
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
						return idx < total - 1;
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
		]
	});


});


function modal(boton) {

	let documento = boton.value;

	console.log(documento);

	//document.getElementById("docAlumno").textContent = documento;
	let aasd = document.getElementById("modalContent");
	console.log(aasd);
	document.getElementById("modalContent").innerHTML = "Cargando información...";
	document.getElementById("contactModalLabel").innerHTML = "Informacion de contacto"
	let url = baseUrl + 'datos_exa/get_datos' + `?documento=${documento}&carrera=${cod_carrera}`;

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

async function exportarPDF(dt) {
	const { jsPDF } = window.jspdf;
	const doc = new jsPDF();

	const logoUrl = baseUrl + 'assets/img/ufps/ufps.png';
	// Convertir la imagen a base64
	const logoBase64 = await getBase64FromImageUrl(logoUrl);

	doc.addImage(logoBase64, 'PNG', 10, 10, 30, 15);
	doc.setFontSize(14);
	doc.text("Alumnos graduados", 50, 20);

	// Omitir sólo la última columna en encabezados
	const headerCells = $('#datatable thead tr:first th:visible')
		.slice(0, -1)  // aquí solo se quita la última columna
		.map(function () {
			return $(this).text().trim();
		}).get();

	// Omitir sólo la última columna en datos
	const data = [];
	dt.rows({ search: 'applied', page: 'all' }).every(function () {
		const rowDataArray = this.data(); // <-- Esto accede a los datos, no al nodo
		const cleanData = [];

		// Quitar las últimas 2 columnas si es necesario
		for (let i = 0; i < rowDataArray.length - 1; i++) {
			const cellText = $('<div>').html(rowDataArray[i]).text().trim(); // limpia HTML
			cleanData.push(cellText);
		}

		data.push(cleanData);
	});

	// Si no hay datos, evita crear un PDF vacío
	if (data.length === 0) {
		alert("No hay datos para exportar.");
		return;
	}

	doc.autoTable({
		head: [headerCells],
		body: data,
		startY: 25,
		styles: { fontSize: 8 },
		headStyles: {
			fillColor: [30, 144, 255]
		}
	});

	doc.save("reporte_filtrado.pdf");
}