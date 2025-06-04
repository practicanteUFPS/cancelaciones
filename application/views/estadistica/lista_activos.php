<?php echo $bread; ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Alumnos activos</h3>
    </div>
    <div class="box-body table-responsive">

        <div class="table">
            <table id="datatable"
                class="table table-hover table-bordered table-striped table-condensed text-center no-margin">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th># semestres matriculados</th>
                        <th>Sexo</th>
                        <th>Creditos aprobados</th>
                        <th>Creditos cursados</th>
                        <th colspan="2">Acciones</th>
                        
                    </tr>
                    <tr>
                        <th>
                           
                        </th>
                        <th>
                            
                        </th>

                        <!-- Semestres Matriculados -->
                        <th>
                            <select class="form-control input-sm operador-semestres"
                                style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
                                <option value="=">Igual a</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual</option>
                                <option value="<=">Menor o igual</option>
                            </select>
                           
                        </th>

                        <!-- Sexo -->
                        <th>
                            <select class="form-control input-sm filtro-sexo"
                                style="max-width: 90px; font-size: 12px; padding: 2px 4px;">
                                <option value="">Todos</option>
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </th>

                        <!-- Créditos Aprobados -->
                        <th>
                            <select class="form-control input-sm operador-aprobados"
                                style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
                                <option value="=">Igual a</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual</option>
                                <option value="<=">Menor o igual</option>
                            </select>
                            
                        </th>

                        <!-- Créditos Cursados -->
                        <th>
                            <select class="form-control input-sm operador-cursados"
                                style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
                                <option value="=">Igual a</option>
                                <option value=">">Mayor que</option>
                                <option value="<">Menor que</option>
                                <option value=">=">Mayor o igual</option>
                                <option value="<=">Menor o igual</option>
                            </select>
                            
                        </th>

                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th> <input type="text" class="form-control input-sm" placeholder="Buscar"
                                style="max-width: 90px; font-size: 12px; padding: 2px 4px;" /></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Buscar"
                                style="max-width: 130px; font-size: 12px; padding: 2px 4px;" /></th>
                        <th> <input type="number" class="form-control input-sm filtro-semestres" placeholder="N°"
                                style="max-width: 80px; font-size: 12px; padding: 2px 4px;" /></th>
                        <th></th>
                        <th><input type="number" class="form-control input-sm filtro-aprobados" placeholder="Créditos"
                                style="max-width: 80px; font-size: 12px; padding: 2px 4px;" /></th>
                        <th><input type="number" class="form-control input-sm filtro-cursados" placeholder="Créditos"
                                style="max-width: 80px; font-size: 12px; padding: 2px 4px;" /></th>
                        
                        <th></th>
                        <th></th>
                        
                    </tr>

                </thead>
                <tbody>

                    <?php foreach ($datos as $estudiante) {
                        $est_data = "/$estudiante->COD_CARRERA/$estudiante->COD_ALUMNO";
                    ?>
                        <tr>
                            <td><?php echo $estudiante->COD_CARRERA . $estudiante->COD_ALUMNO; ?></td>
                            <td><?= $estudiante->NOMBRES; ?></td>
                            <td><?= $estudiante->NUM_SEM_MAT; ?></td>
                            <td><?= $estudiante->SEXO; ?></td>
                            <td><?= $estudiante->CRE_APROBADOS; ?></td>
                            <td><?= $estudiante->CRE_CURSADOS; ?></td>
                            <td>
                                <button type="button" value="<?= $estudiante->DOCUMENTO; ?>" onclick="modal(this)"
                                    class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Presiona para mostrar datos de contacto">

                                    <i class="fa  fa-phone"></i>

                                </button>
                            </td>
                            <td>


                                <button type="button" class="btn btn-sm btn-primary" value="<?= $est_data; ?>"
                                    onclick="cargarNotas(this)" data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Presiona para ver las calificaciones semestre a semestre">
                                    <i class="fa  fa-book"></i>
                                </button>

                            </td>

                        </tr>

                    <?php } ?>
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