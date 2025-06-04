<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Alumnos no matriculados</h3>
    </div>
    <div class="box-body">

        <div class="row my-4">

            <div class="col-md-4">
                <label for="buscar_por_semestre" class="form-label">Inactivos del semestre:</label>
                <input type="text" id="buscar_por_semestre" class="form-control" placeholder="Ej: 2024-1">

            </div>

            <div class="col-md-4">
                <label for="buscar_desde_semestre" class="form-label">Inactivos desde semestre :</label>
                <input type="text" id="buscar_desde_semestre" class="form-control" placeholder="Ej: 2024-1">
            </div>

            <div class="col-md-4">
                <label for="buscar_rango" class="form-label">Buscar inactivos por rango de semestres:</label>
                <div class="input-group">
                    <input type="text" id="semestre_inicio" class="form-control" placeholder="Ej: 2023-1">
                    <span class="input-group-text">a</span>
                    <input type="text" id="semestre_fin" class="form-control" placeholder="Ej: 2024-2">
                </div>
            </div>
        </div>
        <br>

        <div class="row my-4">
            <div class="col-md-4">
                <button class="btn btn-primary lista-btn"
                    data-url="<?php echo site_url('alumno/alumnos_por_semestre'); ?>"
                    onclick="buscarPorSemestre()">buscar por semestre</button>

            </div>

            <div class="col-md-4">
                <button class="btn btn-primary buscar-btn"
                    data-url="<?php echo site_url('alumno/alumnos_desde_semestre'); ?>" onclick="cargarLista()">Buscar
                    desde semestre</button>

            </div>

            <div class="col-md-4">
                <button class="btn btn-primary buscar-rango-btn"
                    data-url="<?php echo site_url('alumno/alumnos_no_matriculados_rango'); ?>"
                    onclick="buscarPorRango()">Buscar Rango</button>

            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th># semestres matriculados</th>
                        <th>Sexo</th>
                        <th>Último Semestre Matriculado</th>
                        <th>Creditos aprobados</th>
                        <th>Creditos cursados</th>
                        <th>Acciones</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tabla_alumnos">
                    <tr>
                        <td colspan="6" class="text-center">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="errorModal"
            style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid red;">
            <h3 style="color: red;">Error en el Servidor</h3>
            <div id="errorContent" style="color: black; max-height: 300px; overflow: auto;"></div>
            <button onclick="document.getElementById('errorModal').style.display='none'">Cerrar</button>
        </div>

     

    </div>
</div>

   <!-- Modal -->
   <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="contactModalLabel">Contacto del Alumno</h4>
                    </div>
                    <div class="modal-body">

                        <div id="modalContent">Cargando información...</div> <!-- Aquí se insertará la respuesta -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

<script>
    var baseUrl = "<?php echo site_url(); ?>"; // Guarda la URL base de CodeIgniter
    var cod_carrera = 122;
</script>