<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Asignaturas por semestre</h3>
    </div>
    <div class="box-body">
        <div class="panel-group" id="accordion">
            <!-- First Panel -->
           
            <?php
                $collapse_id=0;
                foreach($semestres as $semestre){
            ?>
				<div class="panel panel-danger">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $collapse_id; ?>">
                                Semestre #<?php echo $semestre->ANO.'-'.$semestre->SEMESTRE; 
                                echo $semestre->DESCRIPCION; ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse<?php echo $collapse_id; ?>" class="panel-collapse collapse">
                        <div class="panel-body">
                           
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Codigo</th>    
                                            <th>Nombre</th>
                                            <th>Definitiva</th>
                                            <th>Creditos</th>
                                            <th>Matriculado</th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($semestre->NOTAS as $nota){ ?>
                                            <tr>
                                                <td><?= $nota->COD_CARRERA.$nota->COD_MATERIA; ?></td>
                                                <td><?= $nota->NOMBRE; ?> </td>
                                                <td><?= $nota->DEFINITIVA; ?></td>
                                                <td><?= $nota->CREDITOS; ?></td>
                                                <td><?= $nota->COD_CAR_MAT.$nota->COD_MAT_MAT; ?></td>
                                               
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                           
                        </div>
                    </div>
                </div>
            <?php 
                $collapse_id++;
            }
            ?>
        </div>
    </div>
</div>
