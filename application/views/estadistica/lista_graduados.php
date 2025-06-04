<?php echo $bread; ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Lista de graduados</h3>
    </div>
    <div class="box-body">

        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="datatable">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Fecha grado</th>
                        <th>Num diploma</th>
                        <th>Sexo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $estudiante) { ?>
                        <tr>
                            <td><?php echo $estudiante->COD_CARRERA . $estudiante->COD_ALUMNO; ?></td>
                            <td><?= $estudiante->NOMBRES; ?></td>
                            <td><?= $estudiante->PERIODO_RETIRO;?> </td>
                            <td><?= $estudiante->NUM_DIPLOMA; ?></td>
                            <td><?= $estudiante->SEXO; ?></td>
                            <td>
                                <button type="button" value="<?= $estudiante->DOCUMENTO; ?>" onclick="modal(this)"
                                    class="btn btn-sm btn-primary">
                                    Contactar
                                </button>
                            </td>
                           
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

  <div id="errorModal"
            style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid red;">
            <h3 style="color: red;">Error en el Servidor</h3>
            <div id="errorContent" style="color: black; max-height: 300px; overflow: auto;"></div>
            <button onclick="document.getElementById('errorModal').style.display='none'">Cerrar</button>
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