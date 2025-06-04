<?php echo $bread; ?>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            Selecciona semestre
        </h3>
    </div>
    <div class="box-body">
        <form action="<?php echo site_url('estadistica/cancelaciones'); ?>" method="post">
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
        <h3 class="box-title">Grafico de factores de desercion</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <canvas id="graficoFactores"></canvas>
    </div>
</div>





<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Grafico de caracteristicas por factor de desercion</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <label for="selectFactor">Selecciona un factor:</label>
        <select id="selectFactor">
        </select>

        <canvas id="graficoCaracteristicas"></canvas>
    </div>
</div>

<script>
    const datos_factores = <?php echo json_encode($list_fact); ?>;
    const datos_caracteristicas = <?php echo json_encode($list_caract); ?>;
</script>