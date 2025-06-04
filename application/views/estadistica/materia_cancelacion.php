<?php echo $bread; ?>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            Selecciona semestre
        </h3>
    </div>
    <div class="box-body">
        <form action="<?php echo site_url('estadistica/materia_cancel'); ?>" method="post">
            <div class="row">
                <div class="col-md-6" id="campo_unico">
                    <label id="label_unico" class="control-label">Semestre:</label>
                    <div class="form-inline">

                        <select id="anio_unico" class="form-control semestre-seleccion" name="anio_unico">
                            <?php foreach ($sem_activos as $y): ?>

                                <option value="<?= $y->ANO ?>" <?= ($y->ANO == $anio) ? 'selected' : '' ?>><?= $y->ANO ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="semestre_unico" class="form-control semestre-seleccion" name="semestre_unico">
                            <option value="1" <?= ($semestre == 1) ? 'selected' : '' ?>>1</option>
                            <option value="2" <?= ($semestre == 2) ? 'selected' : '' ?>>2</option>
                        </select>
                    </div>
                </div>


                <!-- Estado de Cancelación (radio buttons) -->
                <div class="col-md-6">
                    <label class="control-label d-block">Estado de la Cancelación:</label>
                    <div class="form-inline">
                        <div class="form-check mr-3">
                            <input class="form-check-input" type="radio" name="estado_cancelacion" id="estado_p" value="P"
                                <?= ($estado === 'P') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="estado_p">Solicitud de Cancelación</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estado_cancelacion" id="estado_r" value="R"
                                <?= ($estado === 'R') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="estado_r">Cancelación Realizada</label>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

    </div>
</div>


<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            Motivos de cancelacion
        </h3>
    </div>
    <div class="box-body">
        <table id="datatable" class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Código Materia</th>
                    <th>Nombre</th>
                    <th>Semestre Materia</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>

                </tr>
            </thead>
            <tbody>

                <?php foreach ($datos as $fila):
                    $dat_mat = "/$fila->ANO/$fila->SEMESTRE/$estado/$fila->COD_CARRERA/$fila->COD_MATERIA/";
                ?>
                    <tr>
                        <td><?= $fila->COD_CARRERA . $fila->COD_MATERIA  ?></td>
                        <td><?= $fila->NOMBRE ?></td>
                        <td><?= $fila->SEM_MAT ?></td>
                        <td><?= $fila->TOTAL ?></td>
                        <td class="td_center">
                            <a class="btn btn-sm btn-primary" href="<?php echo site_url('estadistica/cancelaciones_materia') . $dat_mat ?>">

                                Ver motivos

                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>