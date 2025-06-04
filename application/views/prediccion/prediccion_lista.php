<?php echo $bread; ?>

<div class="nav-tabs-custom">
	<ul class="nav nav-tabs pull-right">
		<li class="active"><a href="#tab_1-1" data-toggle="tab" aria-expanded="true">Probabilidades</a></li>
		<li class=""><a href="#tab_2-2" data-toggle="tab" aria-expanded="false">Por sexo</a></li>
		<li class=""><a href="#tab_3-2" data-toggle="tab" aria-expanded="false">Por edad</a></li>

		<li class="pull-left header"><i class="fa fa-th"></i>Probabilidades de desercion</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_1-1">

			<canvas id="histogramaChart"></canvas>
		</div>
		<!-- /.tab-pane -->
		<div class="tab-pane" id="tab_2-2">

			<canvas id="proporcionSexoChart"></canvas>
		</div>
		<!-- /.tab-pane -->
		<div class="tab-pane" id="tab_3-2">
			<canvas id="desercionesEdadChart"></canvas>

		</div>
		<!-- /.tab-pane -->
	</div>
	<!-- /.tab-content -->
</div>

<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Estimaciones por alumno</h3>
	</div>
	<div class="box-body table-responsive">

		<div class="table">
			<table id="tablaPrediccion" class="table table-hover table-striped table-condensed text-center no-margin">
				<thead>
					<tr>
						<th>Codigo</th>
						<th>Documento</th>
						<th>Nombre</th>
						<th>Sexo</th>
						<th>Edad</th>
						<th>Carrera</th>
						<th>Semestres matriculados</th>
						<th>Creditos aprobados</th>
						<th>Creditos cursados</th>
						<th>Prediccion</th>
						<th>Probabilidad de desercion</th>
						<th colspan="2">Acciones</th>
						
					</tr>

					<tr>
						<!-- Código -->
						<th><input type="text" class="form-control input-sm filtro-codigo" placeholder="Buscar código" style="max-width: 90px; font-size: 12px; padding: 2px 4px;" /></th>

						<!-- Documento y Nombre -->
						<th></th>
						<th></th>

						<!-- Sexo -->
						<th>
							<select class="form-control input-sm filtro-sexo" style="max-width: 90px; font-size: 12px; padding: 2px 4px;">
								<option value="">Todos</option>
								<option value="M">M</option>
								<option value="F">F</option>
							</select>
						</th>

						<!-- Edad -->
						<th>
							<select class="form-control input-sm filtro-edad" style="font-size: 12px; padding: 2px 4px; max-width: 130px;">
								<option value="">Todas</option>
								<option value="menor18">Menor de 18</option>
								<option value="18_24">18 - 24</option>
								<option value="25_29">25 - 29</option>
								<option value="30mas">30 o más</option>
							</select>
						</th>

						<!-- Carrera -->
						<th>
							<select class="form-control input-sm filtro-carrera" style="max-width: 120px; font-size: 12px; padding: 2px 4px;">
								<option value="">Todas</option>
								<?php
								// Extraer carreras únicas del array de estudiantes
								$carrerasUnicas = array();
								foreach ($datos as $estudiante) {
									$carrera = $estudiante['NOMBRE_CARRERA'] ;
									if (!in_array($carrera, $carrerasUnicas)) {
										$carrerasUnicas[] = $carrera;
									}
								}
								foreach ($carrerasUnicas as $carrera) {
									echo "<option value=\"$carrera\">$carrera</option>";
								}
								?>
							</select>
						</th>

						<!-- Semestres Matriculados -->
						<th>
							<select class="form-control input-sm operador-semestres" style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
								<option value="=">Igual</option>
								<option value=">">Mayor</option>
								<option value="<">Menor</option>
								<option value=">=">≥</option>
								<option value="<=">≤</option>
							</select>
							<input type="number" class="form-control input-sm filtro-semestres" style="max-width: 80px; font-size: 12px; padding: 2px 4px;" />
						</th>

						<!-- Créditos Aprobados -->
						<th>
							<select class="form-control input-sm operador-aprobados" style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
								<option value="=">Igual</option>
								<option value=">">Mayor</option>
								<option value="<">Menor</option>
								<option value=">=">≥</option>
								<option value="<=">≤</option>
							</select>
							<input type="number" class="form-control input-sm filtro-aprobados" style="max-width: 80px; font-size: 12px; padding: 2px 4px;" />
						</th>

						<!-- Créditos Cursados -->
						<th>
							<select class="form-control input-sm operador-cursados" style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
								<option value="=">Igual</option>
								<option value=">">Mayor</option>
								<option value="<">Menor</option>
								<option value=">=">≥</option>
								<option value="<=">≤</option>
							</select>
							<input type="number" class="form-control input-sm filtro-cursados" style="max-width: 80px; font-size: 12px; padding: 2px 4px;" />
						</th>

						<!-- Predicción -->
						<th>
							<select class="form-control input-sm filtro-prediccion"
								style="max-width: 130px; font-size: 12px; padding: 2px 4px;">
								<option value="">Todos</option>
								<option value="Podria desertar">Podría desertar</option>
								<option value="Permanece">Permanece</option>
							</select>
						</th>

						<!-- Probabilidad -->
						<th>
							<select class="form-control input-sm operador-probabilidad" style="max-width: 100px; font-size: 12px; padding: 2px 4px; margin-bottom: 2px;">
								<option value="=">Igual</option>
								<option value=">">Mayor</option>
								<option value="<">Menor</option>
								<option value=">=">≥</option>
								<option value="<=">≤</option>
							</select>
							<input type="number" step="0.01" class="form-control input-sm filtro-probabilidad" style="max-width: 80px; font-size: 12px; padding: 2px 4px;" />
						</th>

						<th></th>
						<th></th>
					</tr>
				
				</thead>
				<tbody>
					<?php foreach ($datos as $est):
						$cod_carrera = substr($est['CODIGO_ALUMNO'], 0, 3);
						$cod_alumno = substr($est['CODIGO_ALUMNO'], 3);
						$est_data = "/$cod_carrera/$cod_alumno";
					?>
						<tr data-codigo="<?= $est['CODIGO_ALUMNO'] ?>">
							<td><?= $est['CODIGO_ALUMNO'] ?></td>
							<td><?= $est['DOCUMENTO'] ?></td>
							<td><?= $est['NOMBRES'] ?></td>
							<td><?= $est['SEXO_C'] ?></td>
							<td><?= $est['EDAD_ACTUAL'] ?></td>
							<td><?= $est['NOMBRE_CARRERA'] ?></td>
							<td><?= $est['NUM_SEM_MAT'] ?></td>
							<td><?= $est['APROBADOS'] ?></td>
							<td><?= $est['CURSADOS'] ?></td>
							<td class="predicho">-</td>
							<td class="probabilidad">-</td>
							<td class="td_center">
								<button type="button" value="<?= $est['DOCUMENTO'] ?>" onclick="modal(this)"
									class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title=""
									data-original-title="Presiona para mostrar datos de contacto">
									<i class="fa  fa-phone"></i>
								</button>
							</td>
							<td class="td_center">
								<button class="btn  btn-sm btn-primary" value="<?= $est_data; ?>"
									onclick="cargarNotas(this)" data-toggle="tooltip" data-placement="top" title=""
									data-original-title="Presiona para ver las calificaciones semestre a semestre">
									<i class="fa  fa-book"></i>
								</button>

							</td>
						</tr>
					<?php endforeach; ?>
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

<script src="https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/ort.min.js"></script>
<script>
	const datos = <?php echo json_encode($datos); ?>;
	//const file_url = '<?= site_url('assets/modelo.onnx') ?>';
	//console.log(file_url);
</script>