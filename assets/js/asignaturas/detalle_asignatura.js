window.onload = function () {
    $('#miTabla').DataTable({
        responsive: false,
        order: [],
        ordering: true,
        autoWidth: false,
        orderCellsTop: true,
         
        columnDefs: [
            { targets: [0], width: '10px' },  // Ajusta el ancho de las columnas que necesitas  // Ajusta el ancho de las columnas con inputs
            { targets: '_all', className: 'dt-center' } // Centra el contenido de todas las columnas (opcional)
        ],
        initComplete: function () {
            // Esto es para que DataTables no aplique su lógica a las celdas de filtro
            // Si se necesita personalización para los filtros después de la inicialización
            $('input[type="text"], input[type="number"], select').css('width', '100%');
        },
        className: 'compact',
        dom: 'rtipB',  // Establece la ubicación de los elementos en la tabla
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'Exportar CSV',
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'  // Exporta solo las columnas visibles
                }
            },
            {
                extend: 'excelHtml5',
                text: 'Exportar Excel',
                className: 'btn btn-default btn-sm',
                exportOptions: {
                    columns: ':visible'  // Exporta solo las columnas visibles
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
    const doc = new jsPDF();

    //doc.setFontSize(14);
    //doc.text("Reporte Filtrado", 14, 15);

    
    const logoUrl = baseUrl + 'assets/img/ufps/ufps.png';
    // Convertir la imagen a base64
    const logoBase64 = await getBase64FromImageUrl(logoUrl);

    doc.addImage(logoBase64, 'PNG', 10, 10, 30, 15);
    doc.setFontSize(14);
    doc.text("Historico asignatura", 50, 20);

    // Captura solo la PRIMERA fila del thead (títulos)
    const headerCells = $('#miTabla thead tr:first th:visible').map(function () {
        return $(this).text().trim();
    }).get();

    // Captura filas visibles en la tabla (filtradas)
    const data = [];
      // Captura TODAS las filas filtradas, sin limitarse a las visibles o paginadas
    dt.rows({ search: 'applied', page: 'all' }).every(function () {
        const rowData = this.data(); // Obtiene el array de datos crudos de la fila
        const cleanData = [];

        for (let i = 0; i < rowData.length; i++) {
            const cellText = $('<div>').html(rowData[i]).text().trim(); // Elimina HTML
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


function modal(boton) {

    let value = boton.value;



    //document.getElementById("docAlumno").textContent = documento;
    document.getElementById("modalContent").innerHTML = "Cargando información...";
    document.getElementById("contactModalLabel").innerHTML = "Informacion de contacto"
    let url = baseUrl + 'Nota/get_conteo' + `?${value}`;




    // Muestra el modal
    $("#contactModal").modal("show");

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log(data);

            // Construir el contenido del modal
            let contenido = `<div class="table-responsive">
         <table  class="table table-striped table-bordered"> 
         <thead class="table-dark">
         <tr>
     
         <td>Semestre</td>
         <td>Cancelaciones</td>
         
         <td>Total notas</td>
         <td>Zeros </td>
         <td>Reprobado</td>
         <td>Aprobado</td>

         <td>Entre 3 y 3.9</td>
         <td>Entre 4 y 5</td>
         </tr>
         </thead>
         <tbody>`;
            data.forEach(nota => {

                let fila = `<tr>
                 <th>${nota.ANO}-${nota.SEMESTRE}</th>
                 <th>${nota.CANCELACIONES}</th>
                  <th>${nota.TOTAL_NOTAS}</th>
                   <th>${nota.ZERO}</th>
                    <th>${nota.REPROBADO}</th>
                     <th>${nota.APROBADO}</th>
                      <th>${nota.ENTRE_3_Y_3_9}</th>
                       <th>${nota.ENTRE_4_Y_5}</th>
             
                 </tr>`

                contenido = contenido + fila;

            })
            contenido = contenido + '</tbody></table></div>';


            // Insertar los datos en el modal
            document.getElementById("modalContent").innerHTML = contenido;
        })
        .catch(error => {
            document.getElementById("modalContent").innerHTML =
                "<p class='text-danger'>Error al cargar los datos.</p>";
            console.error("Error en fetch:", error);
        });
}
