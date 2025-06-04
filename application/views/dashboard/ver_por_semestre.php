<section class="content">
	<div class="container-fluid">
		<!-- Panel -->

		<?php echo $bread; ?>

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Estudiantes por Semestre de ingreso</h3>
			</div>
			<div class="box-body">
				<div class="row">
					<!-- Columna derecha: Tabla -->
					<div class="col-md-8">
						<div class="table-responsive">
							<table
								class="table table-hover table-bordered table-striped  text-center "
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

									<?php foreach ($datos as $data) { ?>
										<!-- Los datos se llenarán dinámicamente -->
										
										<tr>

										
											<td><?= $data->ANO.'-'.$data->SEM_INGRESO; ?></td>
											<td><?= $data->TOTAL +$data->CANTIDAD_G ; ?></td>
											<td>
												<a href="<?= site_url("alumno/buscar_inactivos_tabla/$data->ANO/$data->SEM_INGRESO")?>"
													data-toggle="tooltip" data-placement="top" title="" data-original-title="Presiona para ver el listado de  estudiantes inactivos">
													<span class="badge bg-red">
														<?= $data->CANTIDAD_X; ?>
														<i class="fa  fa-close"></i>
													</span>
												</a>
											</td>
										
											<td>
												<a href="<?= site_url("alumno/buscar_activos_tabla/$data->ANO/$data->SEM_INGRESO") ?>"
													data-toggle="tooltip" data-placement="top" title="" data-original-title="Presiona para ver el listado de  estudiantes activos">
													<span class="badge bg-green">
														<?= $data->ACTIVO; ?>
														<i class="fa  fa-check"></i>
													</span>
												</a>
											</td>
											
											<td>
												<a href="<?= site_url("exalumno/lista_graduados/$data->ANO/$data->SEM_INGRESO") ?>"
													data-toggle="tooltip" data-placement="top" title="" data-original-title="Presiona para ver el listado de  estudiantes graduados">
													<span class="badge bg-green">
														<?= $data->CANTIDAD_G; ?>
														<i class="fa  fa-check"></i>
													</span>
												</a>
											</td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
					</div>

				</div> <!-- /.row -->
			</div> <!-- /.card-body -->
		</div> <!-- /.card -->
	</div> <!-- /.container-fluid -->
</section>