<?php echo $bread; ?>
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