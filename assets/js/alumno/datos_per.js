
function modal(boton){

    let documento = boton.value;

    console.log(documento);

    //document.getElementById("docAlumno").textContent = documento;
    document.getElementById("modalContent").innerHTML = "Cargando información...";
    document.getElementById("contactModalLabel").innerHTML="Informacion de contacto"
    let url = baseUrl+ 'datos_per/get_datos'+`?documento=${documento}&carrera=${cod_carrera}`;

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


function modal_exa(boton){

    let documento = boton.value;

    console.log(documento);

    //document.getElementById("docAlumno").textContent = documento;
    document.getElementById("modalContent").innerHTML = "Cargando información...";
    document.getElementById("contactModalLabel").innerHTML="Informacion de contacto"
    let url = baseUrl+ 'datos_exa/get_datos'+`?documento=${documento}&carrera=${cod_carrera}`;

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