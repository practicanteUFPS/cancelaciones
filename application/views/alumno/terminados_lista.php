<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Lista de terminados</h3>
    </div>
    <div class="box-body">

        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="datatable">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Sexo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $estudiante) { ?>
                        <tr>
                            <td><?php echo $estudiante->COD_CARRERA . $estudiante->COD_ALUMNO; ?></td>
                            <td><?= $estudiante->NOMBRES; ?></td>
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