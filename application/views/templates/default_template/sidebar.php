<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php if (isset($informacion_usuario->LLAVE_IMAGEN)): ?>
                    <img src="<?php echo is_readable("public/imagenes/{$informacion_usuario->LLAVE_IMAGEN}.JPEG") ? base_url("public/imagenes/{$informacion_usuario->LLAVE_IMAGEN}.JPEG") : base_url("public/imagenes/foto_default.JPEG"); ?>"
                        class="img-circle" alt="User Image" />
                <?php else: ?>
                    <img src="<?php echo base_url("public/imagenes/foto_default.JPEG"); ?>" class="img-circle"
                        alt="User Image" />
                <?php endif; ?>
            </div>
            <div class="pull-left info">
                <p>
                    <?php
                    if (!empty(trim($informacion_usuario->NOMBRES))) {
                        echo ucwords($informacion_usuario->NOMBRES);
                    } elseif (isset($informacion_usuario->NOMBRE_COMPLETO)) {
                        $var = explode(" ", $informacion_usuario->NOMBRE_COMPLETO);
                        echo ucwords($var[2] . " " . $var[3]);
                    }
                    ?>
                </p>
                <!--<a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="header">NAVEGACION PRINCIPAL</li>
            <li class="<?php echo (in_array($item_sidebar_active, array(
                            "dashboard",
                            "ver_estudiantes",
                            "ver_activos",
                            "ver_asignaturas",
                            "ver_por_semestre",
                            "ver_asignaturas_semestre",
                            "ver_est_desercion_semestre",
                            "ver_est_desercion_semestre_ingreso",
                            "ver_est_desercion_sexo",
                            "ver_est_desercion_edad",
                            "estimacion",
                            "ver_est_cancelaciones",
                            "ver_est_materia_cancel",
                            "carrera_vista"
                        ))) ? "active" : ""; ?> treeview">
                <a href="#">
                    <i class="fa fa-info-circle"></i> <span>Grupo de enlaces</span> <i
                        class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="hvr-icon-back2 <?php echo ($item_sidebar_active == "dashboard") ? "active" : ""; ?>">
                        <a class="no-padding-left" href="<?php echo site_url('dashboard'); ?>"> Dashboard </a>
                    </li>


                    <li class="treeview" style="height: auto;">
                        <a href="#">
                            <i class="fa fa-info-circle"></i> <span>seguimiento desercion</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu" style="display: block;">

                            <li
                                class="hvr-icon-back2 <?php echo ($item_sidebar_active == "carrera_vista") ? "active" : ""; ?>">
                                <a href="<?php echo site_url('carrera/vista'); ?>">Seleccionar carreras
                                </a>
                            </li>








                            <li class="treeview" style="height: auto;">
                                <a href="#">
                                    <i class="fa fa-user"></i> <span>Alumnos</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="display: block;">

                                    <li
                                        class="<?php echo ($item_sidebar_active == "ver_estudiantes") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('alumno/buscar_inactivos'); ?>"> Ver inactivos </a>
                                    </li>


                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_activos") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('alumno/buscar_activos'); ?>">Ver activos
                                        </a>
                                    </li>

                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "estimacion") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('estimacion/mostrar'); ?>">Estimaciones
                                        </a>
                                    </li>

                                </ul>
                            </li>




                            <li class="treeview" style="height: auto;">
                                <a href="#">
                                    <i class="fa fa-book"></i> <span>Asignaturas</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="display: block;">
                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_asignaturas") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('materia/ver_asignaturas'); ?>">Lista de asignaturas </a>
                                    </li>
                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_asignaturas_semestre") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('materia/mostrar_materia_estadistica'); ?>">Por semestre
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                       <li class="treeview" style="height: auto;">
                                <a href="#">
                                    <i class="fa fa-bar-chart"></i> <span>Estadisticas desercion</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="display: block;">


                            <li class="treeview" style="height: auto;">
                                <a href="#">
                                    <i class="fa fa-info-circle"></i> <span>Motivos de cancelacion</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="display: block;">
                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_est_cancelaciones") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('estadistica/cancelaciones'); ?>">Motivos de cancelacion</a>
                                    </li>

                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_est_materia_cancel") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('estadistica/materia_cancel'); ?>">Motivos por materia</a>
                                    </li>
                                </ul>
                            </li>




                            <li class="treeview" style="height: auto;">
                                <a href="#">
                                    <i class="fa fa-bar-chart"></i> <span>Estadisticas</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu" style="display: block;">



                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_est_desercion_semestre") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('estadistica/desercion_semestre'); ?>"> por semestre</a>
                                    </li>

                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_est_desercion_semestre_ingreso") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('estadistica/desercion_semestre_ingreso'); ?>"> por semestre de ingreso</a>
                                    </li>

                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_est_desercion_sexo") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('estadistica/desercion_sexo'); ?>"> por sexo</a>
                                    </li>

                                    <li
                                        class="hvr-icon-back2 <?php echo ($item_sidebar_active == "ver_est_desercion_edad") ? "active" : ""; ?>">
                                        <a href="<?php echo site_url('estadistica/desercion_edad'); ?>"> por edad</a>
                                    </li>

                                </ul>
                            </li>

                        </ul>
                    </li>

                </ul>
            </li>
         
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>