<style>
    .equal-height {
        display: flex;
        flex-wrap: wrap;
    }

    .equal-height>[class*='col-'] {
        display: flex;
    }

    .equal-height .panel {
        width: 100%;
    }
</style>
<?php echo $bread; ?>
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Asignaturas por semestre</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="box-group" id="accordion">
            <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
            <?php
            $collapse_id = 10;
            $rowcount = 10;
            foreach (array_reverse($semestres) as $semestre) {
            ?>

                <div class="panel box box-primary">
                    <div class="box-header with-border">
                        <h4 class="box-title">
                            <a data-toggle="collapse" data-parent="#accordion"
                             href="#collapse<?php echo $collapse_id; ?>">
                                 <?php  
                                if($collapse_id!=0){

                                    echo 'Semestre '.$collapse_id;
                                } else {
                                    echo 'otro';
                                } ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse<?php echo $collapse_id; ?>" class="panel-collapse collapse <?php if($sem_select == $collapse_id) echo 'in' ?>">


                    


                        <div class="box-body">
                            <div class="row equal-height">
                                <?php
                                $semestre_contenido = '<div class="row">';
                                foreach ($semestre as $asignatura) {
                                ?>
                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">
                                                    <?php echo $asignatura->COD_CARRERA . $asignatura->COD_MATERIA; ?></h3>
                                            </div>
                                            <div class="panel-body">
                                                <p>
                                                    <?php
                                                    echo $asignatura->NOMBRE . '<br>';

                                                    $button_value = "/$asignatura->COD_CARRERA/$asignatura->COD_MATERIA/$collapse_id";
                                                    ?>

                                                    <span class="badge bg-green">
                                                        <?php echo $asignatura->CREDITOS . ' Creditos'; ?>
                                                    </span>


                                                    <?php if ($asignatura->ELECTIVA) { ?>
                                                        <span class="badge bg-light-blue">
                                                            Electiva
                                                        </span>
                                                    <?php } ?>

                                                </p>
                                                <br>

                                                <a class="btn btn-block btn-info" href="<?php echo site_url('nota/mostrar_historico') . $button_value; ?>">

                                                    Ver historial

                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php $collapse_id--;
            } ?>

        </div>
    </div>
    <!-- /.box-body -->
</div>



<?php //echo var_dump($informacion_usuario); 
//echo var_dump($duracion);
//echo  $duracion->DURACION;
//echo var_dump($semestres);
?>
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


<script>
    var baseUrl = "<?php echo site_url(); ?>"; // Guarda la URL base de CodeIgniter
    var cod_carrera = 122;
</script>