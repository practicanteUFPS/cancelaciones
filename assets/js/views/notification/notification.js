$(document).ready(function () {
    $('#table_notifications').DataTable({
        "language": {
            "url": "/assets/plugins/datatables/lenguaje/spanish.json"
        },
        "order": [[0, "asc"]],
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false
            }
        ]
    });
});


            