$(document).ready(function () {
    // Inicialización de la tabla DataTable

    var table = $('#datatable').DataTable({
        responsive: false,
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



    function agregarFiltroNumerico(nombre, columna) {
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            const valor = parseFloat(data[columna]) || 0;
            const operador = $(`.operador-${nombre}`).val();
            const valorFiltro = parseFloat($(`.filtro-${nombre}`).val());

            if (!isNaN(valorFiltro)) {
                switch (operador) {
                    case '>': return valor > valorFiltro;
                    case '<': return valor < valorFiltro;
                    case '>=': return valor >= valorFiltro;
                    case '<=': return valor <= valorFiltro;
                    case '=': return valor === valorFiltro;
                    default: return true;
                }
            }
            return true;
        });

        $(`.operador-${nombre}, .filtro-${nombre}`).on('change keyup', function () {
            table.draw();
        });
    }

    // Agregar filtros por nombre de clase y número de columna
    agregarFiltroNumerico('cancelaciones', 3);
    agregarFiltroNumerico('extraordinarias', 4);
    agregarFiltroNumerico('notas', 5);
    agregarFiltroNumerico('ceros', 6);
    agregarFiltroNumerico('reprobado', 7);
    agregarFiltroNumerico('aprobado', 8);
    agregarFiltroNumerico('3-3-9', 9);
    agregarFiltroNumerico('4-5', 10);



    // Llenar el select de semestre con valores únicos de la columna "Semestre"
    var semestres = [];
    table.column(2).data().unique().sort().each(function (value, index) {
        semestres.push(value);
    });


    semestres.forEach(function (semestre) {
        $('#filtro-semestre').append('<option value="' + semestre + '">' + semestre + '</option>');
    });

    // Filtro para "Semestre" usando el select
    $('#filtro-semestre').on('change', function () {
        var semestreValue = this.value;
        if (semestreValue) {
            table.column(2).search('^' + semestreValue + '$', true, false).draw();
        } else {
            table.column(2).search('').draw();  // Restablece el filtro si no hay valor seleccionado
        }
    });
    // Filtro independiente para la columna Código
    $('#filtro-codigo').on('keyup change clear', function () {
        table.column(0).search(this.value).draw();
    });

    // Filtro independiente para la columna Nombre
    $('#filtro-nombre').on('keyup change clear', function () {
        table.column(1).search(this.value).draw();
    });

    // Evitar que clics en filtros activen ordenamiento
    $('#datatable thead tr.filters input, #datatable thead tr.filters select').on('click', function (e) {
        e.stopPropagation();
    });

});

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

    const logoUrl = baseUrl + 'assets/img/ufps/ufps.png';
    // Convertir la imagen a base64
    const logoBase64 = await getBase64FromImageUrl(logoUrl);

    doc.addImage(logoBase64, 'PNG', 10, 10, 30, 15);
    doc.setFontSize(14);
    doc.text("Asignaturas por semestres", 50, 20);
    // Captura solo la PRIMERA fila del thead (títulos)
    const headerCells = $('#datatable thead tr:first th:visible').map(function () {
        return $(this).text().trim();
    }).get();

    // Captura filas visibles en la tabla (filtradas)
    const data = [];
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
