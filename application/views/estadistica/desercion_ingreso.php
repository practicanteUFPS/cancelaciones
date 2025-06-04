<?php echo $bread; ?>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Resumen por semestre: </h3>
    </div>

    <div class="box-body">
        <div class="form-group">
            <label for="filtroSemestres">Mostrar últimos:</label>
            <select id="filtroSemestres" class="form-control" style="width: auto; display: inline-block;">
                <option value="5" selected>5 semestres</option>
                <option value="10">10 semestres</option>
                <option value="15">15 semestres</option>
                <option value="todos">Todos</option>
            </select>
        </div>

        <canvas id="resumenChart" height="100"></canvas>
    </div>
</div>


<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Estudiantes por Semestre de ingreso</h3>
    </div>

    <div class="box-body">
        <table class="table table-hover table-bordered table-striped table-condensed text-center no-margin"
            id="miTabla">
            <thead>
                <tr>

                    <th>Semestre ingreso</th>
                    <th>Total</th>
                    <th>Inactivos</th>
                    <th>Activos</th>
                    <th>Graduados</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach (array_reverse($resumen) as $data) {
                    list($anio, $num_semestre) = explode('-', $data->SEMESTRE);
                ?>
                    <tr>
                        <td class="text-center" style="vertical-align: middle;"><?= $data->SEMESTRE; ?></td>
                        <td class="text-center" style="vertical-align: middle;"><?= $data->TOTAL; ?></td>
                        <td <?= $data->PORCENTAJE_INACTIVOS == 0 ? 'title="No hay estudiantes para mostrar"' : '' ?>>
                            <?php if ($data->PORCENTAJE_INACTIVOS > 0): ?>
                                <a href="<?= site_url("alumno/est_inactivos_tabla_ingreso/$anio/$num_semestre") ?>"
                                    data-toggle="tooltip" data-placement="top" title="" data-original-title=" <?= $data->INACTIVOS; ?> inactivos , Presiona para ver el listado de estudiantes inactivos">
                                    <span class="badge bg-red">
                                        <?= $data->INACTIVOS; ?>
                                        <i class="fa fa-close"></i>
                                    </span>
                                </a>
                            <?php else: ?>
                                0
                            <?php endif; ?>
                        </td>

                        <td <?= $data->PORCENTAJE_ACTIVOS == 0 ? 'title="No hay estudiantes para mostrar"' : '' ?>>
                            <?php if ($data->PORCENTAJE_ACTIVOS > 0): ?>
                                <a href="<?= site_url("alumno/est_activos_tabla_ingreso/$anio/$num_semestre") ?>"
                                    data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= $data->ACTIVOS; ?> Alumnos activos , presiona para ver el listado de estudiantes activos">
                                    <span class="badge bg-green">
                                        <?= $data->ACTIVOS; ?>
                                        <i class="fa fa-check"></i>
                                    </span>
                                </a>
                            <?php else: ?>
                                0
                            <?php endif; ?>
                        </td>

                        <td <?= $data->PORCENTAJE_GRADUADOS == 0 ? 'title="No hay estudiantes para mostrar"' : '' ?>>
                            <?php if ($data->PORCENTAJE_GRADUADOS > 0): ?>
                                <a href="<?= site_url("exalumno/lista_graduados_ingreso/$anio/$num_semestre") ?>"
                                    data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= $data->GRADUADOS; ?> Alumnos graduados , presiona para ver el listado de estudiantes graduados">
                                    <span class="badge bg-green">
                                        <?= $data->GRADUADOS; ?>
                                    </span>
                                </a>
                            <?php else: ?>
                                0
                            <?php endif; ?>
                        </td>

                    </tr>

                <?php
                } ?>
            </tbody>
        </table>

    </div>
</div>






<?php
$labels = $totales = $activos = $inactivos = $graduados = [];
foreach ($resumen as $r) {
    $labels[]         = $r->SEMESTRE;
    $activos[]        = (float) $r->PORCENTAJE_ACTIVOS;
    $inactivos[]      = (float) $r->PORCENTAJE_INACTIVOS;
    $graduados[]      = (float) $r->PORCENTAJE_GRADUADOS;
    $activosCant[]    = (int) $r->ACTIVOS;
    $inactivosCant[]  = (int) $r->INACTIVOS;
    $graduadosCant[]  = (int) $r->GRADUADOS;
}
?>

<script>
    const etiquetas = <?= json_encode($labels) ?>;
    const activos = <?= json_encode($activos) ?>;
    const inactivos = <?= json_encode($inactivos) ?>;
    const graduados = <?= json_encode($graduados) ?>;

    const activosCant = <?= json_encode($activosCant) ?>;
    const inactivosCant = <?= json_encode($inactivosCant) ?>;
    const graduadosCant = <?= json_encode($graduadosCant) ?>;
</script>