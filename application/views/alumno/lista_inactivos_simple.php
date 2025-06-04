<?php echo $bread; ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Alumnos no matriculados</h3>
    </div>
    <div class="box-body no-padding table-responsive">

            <table id="datatable" table id="datatable"
                class="table table-hover table-bordered table-striped table-condensed text-center no-margin compact">
                <thead>
                    <tr>
                        <th class="td_center">Código</th>
                        <th class="td_center">Nombre</th>
                        <th class="td_center"># semestres matriculados</th>
                        <th class="td_center">Sexo</th>
                        <th class="td_center">Último Semestre Matriculado</th>
                        <th class="td_center">Creditos aprobados</th>
                        <th class="td_center">Creditos cursados</th>
                        <th class="td_center">Acciones</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th class="td_center">
                            <input type="text" class="form-control input-sm" placeholder="Buscar"
                                style="max-width: 90px; font-size: 12px; padding: 2px 4px;" />
                        </th>
                        <th class="td_center">
                            <input type="text" class="form-control input-sm" placeholder="Buscar"
                                style="max-width: 120px; font-size: 12px; padding: 2px 4px;" />
                        </th>

                        <!-- Semestres Matriculados -->
                        <th class="td_center">
                            <select class="form-control input-sm operador-semestres"
                                style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
                                <option value="=">Igual a</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual</option>
                                <option value="<=">Menor o igual</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-semestres" placeholder="N°"
                                style="max-width: 80px; font-size: 12px; padding: 2px 4px;" />
                        </th>

                        <!-- Sexo -->
                        <th class="td_center">
                            <select class="form-control input-sm filtro-sexo"
                                style="max-width: 90px; font-size: 12px; padding: 2px 4px;">
                                <option value="">Todos</option>
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </th>

                        <th class="td_center">
                            <input type="text" class="form-control input-sm" placeholder="Buscar"
                                style="max-width: 110px; font-size: 12px; padding: 2px 4px;" />
                        </th>

                        <th class="td_center">
                            <select class="form-control input-sm operador-aprobados"
                                style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
                                <option value="=">Igual a</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual</option>
                                <option value="<=">Menor o igual</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-aprobados" placeholder="Créditos"
                                style="max-width: 90px; font-size: 12px; padding: 2px 4px;" />
                        </th>

                        <th class="td_center">
                            <select class="form-control input-sm operador-cursados"
                                style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
                                <option value="=">Igual a</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual</option>
                                <option value="<=">Menor o igual</option>
                            </select>
                            <input type="number" class="form-control input-sm filtro-cursados" placeholder="Créditos"
                                style="max-width: 90px; font-size: 12px; padding: 2px 4px;" />
                        </th>

                        <th class="td_center"></th>
                        <th class="td_center"></th>
                    </tr>


                </thead>
                <tbody>

                    <?php foreach ($datos as $estudiante) {
                         $est_data = "/$estudiante->COD_CARRERA/$estudiante->COD_ALUMNO"; ?>
                        <tr>
                            <td class="td_center"><?php echo $estudiante->COD_CARRERA . $estudiante->COD_ALUMNO; ?></td>
                            <td class="td_center"><?= $estudiante->NOMBRES; ?></td>
                            <td class="td_center"><?= $estudiante->NUM_SEM_MAT; ?></td>
                            <td class="td_center"><?= $estudiante->SEXO; ?></td>
                            <td class="td_center">
                                <?= $estudiante->ULT_ANO_MATRICULADO . '-' . $estudiante->ULT_SEM_MATRICULADO; ?></td>
                            <td class="td_center"><?= $estudiante->CRE_APROBADOS; ?></td>
                            <td><?= $estudiante->CRE_CURSADOS; ?></td>
                            <td class="td_center">
                                <button type="button" value="<?= $estudiante->DOCUMENTO; ?>" onclick="modal(this)"
                                    class="btn btn-sm btn-primary">
                                    Contactar
                                </button>
                            </td>
                            <td class="td_center">
                                                           <button class="btn btn-primary" value="<?= $est_data; ?>" onclick="cargarNotas(this)">Mostrar Notas</button>

                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
       


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