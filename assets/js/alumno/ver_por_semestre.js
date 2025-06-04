window.onload = function () {
    $('#miTabla').DataTable({
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
}