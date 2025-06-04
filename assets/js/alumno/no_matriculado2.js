$(document).ready(function () {
    var table = $('#datatable').DataTable({
        responsive: false,
        ordering: false,
        dom: 'rtipB',
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

            // Filtros por columnas de texto (excepto los personalizados)
            api.columns().every(function () {
                var that = this;

                $('input[type="text"]:not(.filtro-aprobados):not(.filtro-cursados):not(.filtro-semestres)', this.header()).on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }
    });

    // Filtro personalizado para Aprobados, Cursados, Semestres y Sexo
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        // Columnas
        var semestres = parseFloat(data[2]) || 0;
        var sexo = data[3];
        var aprobados = parseFloat(data[5]) || 0;
        var cursados = parseFloat(data[6]) || 0;

        // Valores de filtros
        var opSem = $('.operador-semestres').val();
        var valSem = parseFloat($('.filtro-semestres').val()) || null;

        var opAprob = $('.operador-aprobados').val();
        var valAprob = parseFloat($('.filtro-aprobados').val()) || null;

        var opCurs = $('.operador-cursados').val();
        var valCurs = parseFloat($('.filtro-cursados').val()) || null;

        var filtroSexo = $('.filtro-sexo').val();

        // Evaluaciones
        let evalSem = true, evalAprob = true, evalCurs = true, evalSexo = true;

        // Semestres
        if (valSem !== null) {
            switch (opSem) {
                case '>': evalSem = semestres > valSem; break;
                case '<': evalSem = semestres < valSem; break;
                case '>=': evalSem = semestres >= valSem; break;
                case '<=': evalSem = semestres <= valSem; break;
                default: evalSem = semestres === valSem;
            }
        }

        // Aprobados
        if (valAprob !== null) {
            switch (opAprob) {
                case '>': evalAprob = aprobados > valAprob; break;
                case '<': evalAprob = aprobados < valAprob; break;
                case '>=': evalAprob = aprobados >= valAprob; break;
                case '<=': evalAprob = aprobados <= valAprob; break;
                default: evalAprob = aprobados === valAprob;
            }
        }

        // Cursados
        if (valCurs !== null) {
            switch (opCurs) {
                case '>': evalCurs = cursados > valCurs; break;
                case '<': evalCurs = cursados < valCurs; break;
                case '>=': evalCurs = cursados >= valCurs; break;
                case '<=': evalCurs = cursados <= valCurs; break;
                default: evalCurs = cursados === valCurs;
            }
        }

        // Sexo
        if (filtroSexo !== "") {
            evalSexo = sexo === filtroSexo;
        }

        return evalSem && evalAprob && evalCurs && evalSexo;
    });

    // Redibujar tabla al cambiar filtros
    $('.operador-aprobados, .filtro-aprobados, .operador-cursados, .filtro-cursados, .operador-semestres, .filtro-semestres, .filtro-sexo').on('change keyup', function () {
        $('#datatable').DataTable().draw();
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


async function exportarPDF(dt) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const logoUrl = baseUrl + 'assets/img/ufps/ufps.png';
    // Convertir la imagen a base64
    const logoBase64 = await getBase64FromImageUrl(logoUrl);

    doc.addImage(logoBase64, 'PNG', 10, 10, 30, 15);
    doc.setFontSize(14);
    doc.text("Alumnos inactivos", 50, 20);

   

    // Captura solo la PRIMERA fila del thead (títulos), excluyendo las dos últimas ("Acciones")
    const headerCells = $('#datatable thead tr:first th:visible')
        .slice(0, -2) // Elimina las últimas dos columnas
        .map(function () {
            return $(this).text().trim();
        }).get();

    // Captura filas visibles en la tabla (filtradas)
    const data = [];
    dt.rows({ search: 'applied', page: 'all' }).every(function () {
        const rowDataArray = this.data(); // <-- Esto accede a los datos, no al nodo
        const cleanData = [];

        // Quitar las últimas 2 columnas si es necesario
        for (let i = 0; i < rowDataArray.length - 2; i++) {
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

document.addEventListener('DOMContentLoaded', function () {
    var radios = document.querySelectorAll('input[name="tipo_busqueda"]');
    var campoUnico = document.getElementById('campo_unico');
    var campoRango = document.getElementById('campo_rango');
    var labelUnico = document.getElementById('label_unico');

    // Oculta todo al inicio
    //campoUnico.classList.add('hidden');
    //campoRango.classList.add('hidden');

    // Cambiar visibilidad de campos según el tipo de búsqueda
    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            campoUnico.classList.add('hidden');
            campoRango.classList.add('hidden');

            if (radio.value === 'semestre') {
                labelUnico.textContent = 'Inactivos del semestre:';
                campoUnico.classList.remove('hidden');
            } else if (radio.value === 'desde') {
                labelUnico.textContent = 'Inactivos desde semestre:';
                campoUnico.classList.remove('hidden');
            } else if (radio.value === 'rango') {
                campoRango.classList.remove('hidden');
            }
        });
    });


});

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