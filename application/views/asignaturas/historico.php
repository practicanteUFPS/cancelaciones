<?php echo $bread; ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= $asignatura->COD_CARRERA . $asignatura->COD_MATERIA . ' - ' . $asignatura->NOMBRE; ?>
        </h3>


    </div>
    <div class="box-body">


        <p class="text-muted">
            <i class="fa fa-info-circle"></i> Haz clic en cualquier número para ver el listado de estudiantes por
            categoría.
        </p>
        <div class="table">
            <table id="miTabla" class="table table-hover table-bordered table-striped table-condensed text-center no-margin">
                <thead class="table-dark">
                    <tr>
                        <th>Semestre</th>
                        <th>Cancelacione ordinarias</th>
                        <th>Cancelaciones extraordinarias</th>
                        <th>Total notas</th>
                        <th>Ceros </th>
                        <th>Reprobado</th>
                        <th>Aprobado</th>
                        <th>Entre 3 y 3.9</th>
                        <th>Entre 4 y 5</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $url = site_url('nota/alumno_nota_tipo');
                    $url2 =  site_url('cancelaciones/alumno_nota_tipo');
                    foreach ($conteo as $caso) {
                        $url_canc = $url2 . "/$carrera/$caso->ANO/$caso->SEMESTRE/$codigo/b/";
                        $url_view = $url . "/$carrera/$caso->ANO/$caso->SEMESTRE/$codigo/b/";
                    ?>

                        <tr>
                            <td>
                                <?php echo $caso->ANO . '-' . $caso->SEMESTRE; ?>
                            </td>
                            <td <?= $caso->CANCELACIONES_ORDINARIAS == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->CANCELACIONES_ORDINARIAS > 0): ?>
                                    <a href="<?= $url_canc . 'cancelord' ?>" title="Presiona para ver cancelaciones ordinarias" data-toggle="tooltip">
                                        <span class="badge bg-yellow">
                                            <?= $caso->CANCELACIONES_ORDINARIAS; ?>
                                            <i class="fa fa-eraser"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->CANCELACIONES_ORDINARIAS; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->CANCELACIONES == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->CANCELACIONES > 0): ?>
                                    <a href="<?= $url_canc . 'cancel'; ?>" title="Presiona para ver cancelaciones extraordinarias" data-toggle="tooltip">
                                        <span class="badge bg-yellow">
                                            <?= $caso->CANCELACIONES; ?>
                                            <i class="fa fa-eraser"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->CANCELACIONES; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->TOTAL_NOTAS == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->TOTAL_NOTAS > 0): ?>
                                    <a href="<?= $url_view . 'total'; ?>" title="Presiona para ver todo listado de estudiantes que cursaron en el semestre" data-toggle="tooltip">
                                        <span class="badge bg-blue"><?= $caso->TOTAL_NOTAS; ?></span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->TOTAL_NOTAS; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->ZERO == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->ZERO > 0): ?>
                                    <a href="<?= $url_view . 'zero'; ?>" title="Presiona para ver el listado de estudiantes que sacaron 0" data-toggle="tooltip">
                                        <span class="badge bg-red">
                                            <?= $caso->ZERO; ?>
                                            <i class="fa fa-close"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->ZERO; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->REPROBADO == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->REPROBADO > 0): ?>
                                    <a href="<?= $url_view . 'reprobado'; ?>" title="Presiona para ver el listado de estudiantes reprobados" data-toggle="tooltip">
                                        <span class="badge bg-red">
                                            <?= $caso->REPROBADO; ?>
                                            <i class="fa fa-close"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->REPROBADO; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->APROBADO == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->APROBADO > 0): ?>
                                    <a href="<?= $url_view . 'aprobado'; ?>" title="Presiona para ver el listado de estudiantes aprobados" data-toggle="tooltip">
                                        <span class="badge bg-green">
                                            <?= $caso->APROBADO; ?>
                                            <i class="fa fa-check"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->APROBADO; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->ENTRE_3_Y_3_9 == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->ENTRE_3_Y_3_9 > 0): ?>
                                    <a href="<?= $url_view . 'rango'; ?>" title="Presiona para ver el listado de estudiantes con notas entre 3 y 3.9" data-toggle="tooltip">
                                        <span class="badge bg-blue">
                                            <?= $caso->ENTRE_3_Y_3_9; ?>
                                            <i class="fa fa-check"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->ENTRE_3_Y_3_9; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->ENTRE_4_Y_5 == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->ENTRE_4_Y_5 > 0): ?>
                                    <a href="<?= $url_view . 'cuatro'; ?>" title="Presiona para ver el listado de estudiantes con nota mayor a 4" data-toggle="tooltip">
                                        <span class="badge bg-green">
                                            <?= $caso->ENTRE_4_Y_5; ?>
                                            <i class="fa fa-check"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->ENTRE_4_Y_5; ?>
                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var baseUrl = "<?php echo site_url(); ?>"; // Guarda la URL base de CodeIgniter
    
</script>