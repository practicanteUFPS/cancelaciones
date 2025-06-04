<?php echo $bread; ?>





<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>

<!-- Select para filtrar -->
<div class="form-group">
    <label for="filtroSemestres">Mostrar últimos:</label>
    <select id="filtroSemestres" class="form-control" style="width: auto; display: inline-block;">
        <option value="5">5 semestres</option>
        <option value="10">10 semestres</option>
        <option value="15">15 semestres</option>
        <option value="todos" selected>Todos</option>
    </select>
</div>

        <div class="form-group">
            <label>Rangos de edad:</label><br>
            <label><input type="checkbox" class="filtroEdad" value="<18" checked> Menores de 18</label>
            <label><input type="checkbox" class="filtroEdad" value="18-24" checked> 18-24</label>
            <label><input type="checkbox" class="filtroEdad" value="25-29" checked> 25-29</label>
            <label><input type="checkbox" class="filtroEdad" value=">30" checked> Mayores de 30</label>
        </div>

    </div>
    <div class="box-body">
        <canvas id="graficoEdad" height="120"></canvas>
    </div>
    <!-- /.box-body -->
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Alumnos activos , inactivos y graduados por edad </h3>
    </div>
    <div class="box-body">

        <div class="table-responsive">

            <table id="datatable" class="table table-hover table-bordered table-striped table-condensed text-center no-margin" id="datatable">

                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle; text-align: center;">Semestre</th>
                        <th colspan="4">Inactivos</th>
                        <th colspan="4">Activos</th>
                        <th colspan="3">Graduados</th>
                    </tr>
                    <tr>

                        <th>&lt;18</th>
                        <th>18–24</th>
                        <th>25–29</th>
                        <th>≥30</th>
                        <th>&lt;18</th>
                        <th>18–24</th>
                        <th>25–29</th>
                        <th>≥30</th>
                        <th>18–24</th>
                        <th>25–29</th>
                        <th>≥30</th>
                    </tr>

                </thead>
                <tbody>

                    <?php foreach (array_reverse($datos) as $row): ?>
                        <?php
                        list($anio, $num_semestre) = explode('-', $row['SEMESTRE']);
                        ?>
                        <tr>
                            <td><?= $row['SEMESTRE'] ?></td>

                            <td>
                                <?php if ($row['INACTIVOS_MENOR_18'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_inactivos_edad/$anio/$num_semestre/MENOR_18") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         <?= $row['INACTIVOS_MENOR_18'] ?> estudiantes menores de 18 pasaron a ser inactivos  ">
                                        <span class="badge bg-yellow">
                                            <?= $row['INACTIVOS_MENOR_18'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['INACTIVOS_MENOR_18'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['INACTIVOS_ENTRE_18_24'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_inactivos_edad/$anio/$num_semestre/ENTRE_18_24") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         <?= $row['INACTIVOS_ENTRE_18_24'] ?> 
                                        estudiantes de entre 18 y 24 pasaron a ser inactivos">
                                        <span class="badge bg-yellow">
                                            <?= $row['INACTIVOS_ENTRE_18_24'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['INACTIVOS_ENTRE_18_24'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['INACTIVOS_ENTRE_25_29'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_inactivos_edad/$anio/$num_semestre/ENTRE_25_29") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         <?= $row['INACTIVOS_ENTRE_25_29'] ?> 
                                        estudiantes de entre 25 y 29 pasaron a ser inactivos">
                                        <span class="badge bg-yellow">
                                            <?= $row['INACTIVOS_ENTRE_25_29'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['INACTIVOS_ENTRE_25_29'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['INACTIVOS_MAYOR_30'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_inactivos_edad/$anio/$num_semestre/MAYOR_30") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         <?= $row['INACTIVOS_MAYOR_30'] ?> 
                                        estudiantes mayores de 30 pasaron a ser inactivos">
                                        <span class="badge bg-yellow">
                                            <?= $row['INACTIVOS_MAYOR_30'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['INACTIVOS_MAYOR_30'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['ACTIVOS_MENOR_18'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_activos_edad/$anio/$num_semestre/MENOR_18") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         hubo  <?= $row['ACTIVOS_MENOR_18'] ?> 
                                        estudiantes menores de 18 activos">
                                        <span class="badge bg-light-blue">
                                            <?= $row['ACTIVOS_MENOR_18'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['ACTIVOS_MENOR_18'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['ACTIVOS_ENTRE_18_24'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_activos_edad/$anio/$num_semestre/ENTRE_18_24") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         hubo  <?= $row['ACTIVOS_ENTRE_18_24'] ?> 
                                        estudiantes activos de entre 18 y 24">
                                        <span class="badge bg-light-blue">
                                            <?= $row['ACTIVOS_ENTRE_18_24'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['ACTIVOS_ENTRE_18_24'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['ACTIVOS_ENTRE_25_29'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_activos_edad/$anio/$num_semestre/ENTRE_25_29") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         hubo  <?= $row['ACTIVOS_ENTRE_25_29'] ?> 
                                        estudiantes activos de entre 18 y 24">
                                        <span class="badge bg-light-blue">
                                            <?= $row['ACTIVOS_ENTRE_25_29'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['ACTIVOS_ENTRE_25_29'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['ACTIVOS_MAYOR_30'] > 0): ?>
                                    <a href="<?= site_url("estadistica/buscar_activos_edad/$anio/$num_semestre/MAYOR_30") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         hubo  <?= $row['ACTIVOS_MAYOR_30'] ?> 
                                        estudiantes activos mayores de 30">
                                        <span class="badge bg-light-blue">
                                            <?= $row['ACTIVOS_MAYOR_30'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['ACTIVOS_MAYOR_30'] ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['GRADUADOS_ENTRE_18_24'] > 0): ?>
                                    <a href="<?= site_url("exalumno/lista_graduados_edad/$anio/$num_semestre/ENTRE_18_24") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         hubo  <?= $row['GRADUADOS_ENTRE_18_24'] ?> 
                                        estudiantes graduados de entre 18 y 24">
                                        <span class="badge bg-green">
                                            <?= $row['GRADUADOS_ENTRE_18_24'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['GRADUADOS_ENTRE_18_24'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['GRADUADOS_ENTRE_25_29'] > 0): ?>
                                    <a href="<?= site_url("exalumno/lista_graduados_edad/$anio/$num_semestre/ENTRE_25_29") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         hubo  <?= $row['GRADUADOS_ENTRE_25_29'] ?> 
                                        estudiantes graduados de entre 25 y 29">
                                        <span class="badge bg-green">
                                            <?= $row['GRADUADOS_ENTRE_25_29'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['GRADUADOS_ENTRE_25_29'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['GRADUADOS_MAYOR_30'] > 0): ?>
                                    <a href="<?= site_url("exalumno/lista_graduados_edad/$anio/$num_semestre/MAYOR_30") ?>"
                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="durante el semestre <?= $row['SEMESTRE'] ?>
                                         hubo  <?= $row['GRADUADOS_MAYOR_30'] ?> 
                                        estudiantes graduados mayores de 30">
                                        <span class="badge bg-green">
                                            <?= $row['GRADUADOS_MAYOR_30'] ?>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $row['GRADUADOS_MAYOR_30'] ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
    const datosOriginales = <?= json_encode($datos) ?>;
</script>