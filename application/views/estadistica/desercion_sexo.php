<?php echo $bread; ?>





<div class="box box-solid">
	<div class="box-header with-border">
		<h3 class="box-title">Grafico de barras desercion estudiantil por sexo</h3>

		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
			</button>
		</div>
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

		<div class="chart">
			<canvas id="myChart"></canvas>
		</div>
	</div>
	<!-- /.box-body -->
</div>


<div class="box box-solid">
	<div class="box-header with-border">
		<h3 class="box-title">Estudiantes activos , inactivos y graduados por sexo</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
			</button>
		</div>
	</div>
	<div class="box-body">

		<div class="table-responsive">

			<table id="datatable" class="table table-hover table-bordered table-striped table-condensed text-center no-margin" id="datatable">

				<thead>
					<tr>
						<th rowspan="2">Semestre</th>
						<th colspan="2">Inactivos</th>
						<th colspan="2">Activos</th>
						<th colspan="2">Graduados</th>
					</tr>

					<tr>
						<th>F</th>
						<th>M</th>
						<th>F</th>
						<th>M</th>
						<th>F</th>
						<th>M</th>
					</tr>

				</thead>
				<tbody>

					<?php foreach (array_reverse($datos) as $row):
						list($anio, $num_semestre) = explode('-', $row->SEMESTRE);
					?>
						<tr>
							<td><?= $row->SEMESTRE ?></td>

							<td>
								<?php if ($row->INACTIVOS_F > 0): ?>
									<a href="<?= site_url("estadistica/buscar_inactivos_sexo/$anio/$num_semestre/F") ?>"
										data-toggle="tooltip" data-placement="top" title="" data-original-title="INACTIVAS: <?= $row->PCT_INACTIVOS_F ?> %">
										<?= $row->INACTIVOS_F ?>
									</a>
								<?php else: ?>
									<?= $row->INACTIVOS_F ?>
								<?php endif; ?>
							</td>

							<td>
								<?php if ($row->INACTIVOS_M > 0): ?>
									<a href="<?= site_url("estadistica/buscar_inactivos_sexo/$anio/$num_semestre/M") ?>"
										data-toggle="tooltip" data-placement="top" title="" data-original-title="INACTIVOS: <?= $row->PCT_INACTIVOS_M ?> %">
										<?= $row->INACTIVOS_M ?>
									</a>
								<?php else: ?>
									<?= $row->INACTIVOS_M ?>
								<?php endif; ?>
							</td>

							<td>
								<?php if ($row->ACTIVOS_F > 0): ?>
									<a href="<?= site_url("estadistica/buscar_activos_sexo/$anio/$num_semestre/F") ?>"
										data-toggle="tooltip" data-placement="top" title="" data-original-title="ACTIVAS: <?= $row->PCT_ACTIVOS_F ?> %">
										<?= $row->ACTIVOS_F ?>
									</a>
								<?php else: ?>
									<?= $row->ACTIVOS_F ?>
								<?php endif; ?>
							</td>

							<td>
								<?php if ($row->ACTIVOS_M > 0): ?>
									<a href="<?= site_url("estadistica/buscar_activos_sexo/$anio/$num_semestre/M") ?>"
										data-toggle="tooltip" data-placement="top" title="" data-original-title="ACTIVOS: <?= $row->PCT_ACTIVOS_M ?> %">
										<?= $row->ACTIVOS_M ?>
									</a>
								<?php else: ?>
									<?= $row->ACTIVOS_M ?>
								<?php endif; ?>
							</td>

							<td>
								<?php if ($row->GRADUADOS_F > 0): ?>
									<a href="<?= site_url("exalumno/lista_graduados_sexo/$anio/$num_semestre/F") ?>" title="GRADUADAS: <?= $row->PCT_GRADUADOS_F ?> %">
										<?= $row->GRADUADOS_F ?>
									</a>
								<?php else: ?>
									<?= $row->GRADUADOS_F ?>
								<?php endif; ?>
							</td>

							<td>
								<?php if ($row->GRADUADOS_M > 0): ?>
									<a href="<?= site_url("exalumno/lista_graduados_sexo/$anio/$num_semestre/M") ?>" title="GRADUADOS: <?= $row->PCT_GRADUADOS_M ?> %">
										<?= $row->GRADUADOS_M ?>
									</a>
								<?php else: ?>
									<?= $row->GRADUADOS_M ?>
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
	const datos = <?php echo json_encode($datos); ?>;
</script>