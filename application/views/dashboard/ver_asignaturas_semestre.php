<?php echo $bread; ?>
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            Selecciona semestre
        </h3>
    </div>
    <div class="box-body">
        <form action="<?php echo site_url('materia/mostrar_materia_estadistica'); ?>" method="post">
            <div class="row">
                <div class="col-md-6" id="campo_unico">
                    <label id="label_unico" class="control-label">Semestre:</label>
                    <div class="form-inline">
                        <select id="anio_unico" class="form-control semestre-seleccion" name="anio_unico">
                            <?php for ($y = $anio_actual; $y >= $anio_inicio; $y--): ?>
                                <option value="<?= $y ?>" <?= ($y == $anio) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <select id="semestre_unico" class="form-control semestre-seleccion" name="semestre_unico">
                            <option value="1" <?= ($semestre == 1) ? 'selected' : '' ?>>1</option>
                            <option value="2" <?= ($semestre == 2) ? 'selected' : '' ?>>2</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

    </div>
</div>


<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            Asignaturas por semestre
        </h3>


    </div>
    <div class="box-body">


        <p class="text-muted">
            <i class="fa fa-info-circle"></i> Haz clic en cualquier número para ver el listado de estudiantes por
            categoría.
        </p>
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped  text-center " id="datatable">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Semestre</th>
                        <th>Cancelaciones Ordinarias</th>
                        <th>Cancelaciones Extraordinarias</th>
                        <th>Total Notas</th>
                        <th>Ceros</th>
                        <th>Reprobado</th>
                        <th>Aprobado</th>
                        <th>Entre 3 y 3.9</th>
                        <th>Entre 4 y 5</th>
                    </tr>
                    <tr>
                        <th><input type="text" class="form-control " id="filtro-codigo" placeholder="Buscar Código" />
                        </th>
                        <th><input type="text" class="form-control input-sm" id="filtro-nombre"
                                placeholder="Buscar Nombre" /></th>
                        <th >
                            <select class="form-control input-sm" id="filtro-semestre">
                                <option value="">Todos</option>
                                <!-- Se llena dinámicamente -->
                            </select>
                        </th>
                        <!-- Para los siguientes campos: select + input -->
                        <th>
                            <select class="form-control input-sm operador-cancelaciones">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>

                            </select>
                            <input type="number" class="form-control input-sm filtro-cancelaciones"
                                placeholder="Cancelaciones">
                        </th>

                        <!-- Cancelaciones Extraordinarias -->
                        <th>
                            <select class="form-control input-sm operador-extraordinarias">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-extraordinarias"
                                placeholder="Extraordinarias">
                        </th>

                        <!-- Total Notas -->
                        <th>
                            <select class="form-control input-sm operador-notas">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-notas" placeholder="Total Notas">

                        </th>

                        <!-- Ceros -->
                        <th>
                            <select class="form-control input-sm operador-ceros">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-ceros" placeholder="Ceros">

                        </th>

                        <!-- Reprobado -->
                        <th>
                            <select class="form-control input-sm operador-reprobado">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-reprobado" placeholder="Reprobado">

                        </th>

                        <!-- Aprobado -->
                        <th>
                            <select class="form-control input-sm operador-aprobado">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-aprobado" placeholder="Aprobado">
                        </th>

                        <!-- Entre 3 y 3.9 -->
                        <th>
                            <select class="form-control input-sm operador-3-3-9">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-3-3-9" placeholder="Entre 3 y 3.9">

                        </th>

                        <!-- Entre 4 y 5 -->
                        <th>
                            <select class="form-control input-sm operador-4-5">
                                <option value="=">Igual que</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual que</option>
                                <option value="<=">Menor o igual que</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-4-5" placeholder="Entre 4 y 5">

                        </th>
                    </tr>
                  
                   
                </thead>

                <tbody>
                    <?php
                    $url = site_url('nota/alumno_nota_tipo');
                    $url2 =  site_url('cancelaciones/alumno_nota_tipo');
                    foreach ($datos as $caso) {

                        $url_canc = $url2 . "/$caso->COD_CARRERA/$anio/$semestre/$caso->COD_MATERIA/a/";
                        $url_view = $url . "/$caso->COD_CARRERA/$anio/$semestre/$caso->COD_MATERIA/a/";
                        //echo $url_view;
                    ?>

                        <tr>
                            <td><?php echo $caso->COD_CARRERA . $caso->COD_MATERIA; ?>

                            </td>
                            <td><?php echo $caso->NOMBRE_MATERIA; ?></td>
                            <td><?php echo $caso->SEMESTRE_MATERIA; ?></td>
                            <td <?= $caso->CANCELACIONES_ORDINARIAS == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->CANCELACIONES_ORDINARIAS > 0): ?>
                                    <a href="<?= $url_canc . "cancelord"; ?>" title="Presiona para ver cancelaciones ordinarias" data-toggle="tooltip">
                                        <span class="badge bg-yellow">
                                            <?= $caso->CANCELACIONES_ORDINARIAS; ?>
                                            <i class="fa fa-eraser"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->CANCELACIONES_ORDINARIAS; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->CANCELACIONES_EXTRAORDINARIAS == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->CANCELACIONES_EXTRAORDINARIAS > 0): ?>
                                    <a href="<?= $url_canc . "cancel"; ?>" title="Presiona para ver cancelaciones extraordinarias" data-toggle="tooltip">
                                        <span class="badge bg-yellow">
                                            <?= $caso->CANCELACIONES_EXTRAORDINARIAS; ?>
                                            <i class="fa fa-eraser"></i>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->CANCELACIONES_EXTRAORDINARIAS; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->TOTAL_NOTAS == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->TOTAL_NOTAS > 0): ?>
                                    <a href="<?= $url_view . "total"; ?>" title="Presiona para ver todo listado de estudiantes que cursaron en el semestre" data-toggle="tooltip">
                                        <span class="badge bg-blue"><?= $caso->TOTAL_NOTAS; ?></span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->TOTAL_NOTAS; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->ZERO == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->ZERO > 0): ?>
                                    <a href="<?= $url_view . "zero"; ?>" title="Presiona para ver el listado de estudiantes que sacaron 0" data-toggle="tooltip">
                                        <span class="badge bg-red"><?= $caso->ZERO; ?> <i class="fa fa-close"></i></span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->ZERO; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->REPROBADO == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->REPROBADO > 0): ?>
                                    <a href="<?= $url_view . "reprobado"; ?>" title="Presiona para ver el listado de estudiantes reprobados" data-toggle="tooltip">
                                        <span class="badge bg-red"><?= $caso->REPROBADO; ?> <i class="fa fa-close"></i></span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->REPROBADO; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->APROBADO == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->APROBADO > 0): ?>
                                    <a href="<?= $url_view . "aprobado"; ?>" title="Presiona para ver el listado de estudiantes aprobados" data-toggle="tooltip">
                                        <span class="badge bg-green"><?= $caso->APROBADO; ?> <i class="fa fa-check"></i></span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->APROBADO; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->ENTRE_3_Y_3_9 == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->ENTRE_3_Y_3_9 > 0): ?>
                                    <a href="<?= $url_view . "rango"; ?>" title="Presiona para ver el listado de estudiantes con notas entre 3 y 3.9" data-toggle="tooltip">
                                        <span class="badge bg-blue"><?= $caso->ENTRE_3_Y_3_9; ?> <i class="fa fa-check"></i></span>
                                    </a>
                                <?php else: ?>
                                    <?= $caso->ENTRE_3_Y_3_9; ?>
                                <?php endif; ?>
                            </td>

                            <td <?= $caso->ENTRE_4_Y_5 == 0 ? 'title="No hay registros"' : '' ?>>
                                <?php if ($caso->ENTRE_4_Y_5 > 0): ?>
                                    <a href="<?= $url_view . "cuatro"; ?>" title="Presiona para ver el listado de estudiantes con nota mayor a 4" data-toggle="tooltip">
                                        <span class="badge bg-green"><?= $caso->ENTRE_4_Y_5; ?> <i class="fa fa-check"></i></span>
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