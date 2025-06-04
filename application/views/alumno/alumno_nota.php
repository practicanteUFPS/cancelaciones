<?php echo $bread; ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Estadistica de aprobaciones y cancelaciones</h3>
    </div>
    <div class="box-body">

        <div class="table">
            <table class="table table-hover table-bordered table-striped table-condensed text-center no-margin" id="datatable">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2">Codigo</th>
                        <th rowspan="2">Nombre</th>
                        <th rowspan="2">Definitiva</th>
                        <th rowspan="2">Tipo nota</th>
                        <th rowspan="2"></th>
                        <th colspan="2">Acciones</th>
                    </tr>

                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista as $alumno) {
                        $est_data = "/$alumno->COD_CARRERA/$alumno->COD_ALUMNO"; ?>

                        <tr>
                            <td><?php echo $alumno->COD_CARRERA . $alumno->COD_ALUMNO; ?></td>
                            <td><?php echo $alumno->NOMBRES; ?> </td>
                            <td><?= $alumno->DEFINITIVA; ?></td>
                            <td><?= $alumno->TIPO_NOTA; ?></td>

                            <td></td>
                            <td class="td_center">
                                <button type="button" value="<?= $alumno->DOCUMENTO; ?>" onclick="modal(this)"
                                    class="btn btn-block btn-info" data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Presiona para mostrar datos de contacto">
                                    <i class="fa  fa-phone"></i>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-block btn-info" value="<?= $est_data; ?>"
                                    onclick="cargarNotas(this)" data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Presiona para ver las calificaciones semestre a semestre">
                                    <i class="fa  fa-book"></i>
                                </button>

                            </td>
                        </tr>
                    <?php } ?>

                    <?php foreach ($listagrads as $alumno) {
                        $est_data = "/$alumno->COD_CARRERA/$alumno->COD_ALUMNO"; ?>
                        <tr>
                            <td><?php echo $alumno->COD_CARRERA . $alumno->COD_ALUMNO; ?></td>
                            <td><?php echo $alumno->NOMBRES; ?> </td>
                            <td><?= $alumno->DEFINITIVA; ?></td>
                            <td><?= $alumno->TIPO_NOTA; ?></td>
                            <td title="Fecha de grado">
                                <i class="fa fa-fw fa-graduation-cap"></i>
                                <?= $alumno->PER_ACA_RETIRO; ?>
                            </td>


                            <td class="td_center">
                                <button type="button" value="<?= $alumno->DOCUMENTO; ?>" onclick="modalExa(this)"
                                    class="btn btn-block btn-info" data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Presiona para mostrar datos de contacto">
                                    <i class="fa  fa-phone"></i>
                                </button>
                            </td>

                            <td>



                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
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


<!-- Modal -->
<div class="modal fade" id="modalNotas" tabindex="-1" role="dialog" aria-labelledby="modalNotasLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalNotasLabel">Notas por Semestre</h4>
            </div>
            <div class="modal-body">
                <div class="box-group" id="accordionNotas"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var baseUrl = "<?php echo site_url(); ?>"; // Guarda la URL base de CodeIgniter
    var cod_carrera = 122;
</script>