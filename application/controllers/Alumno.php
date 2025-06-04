<?php

class Alumno extends CMS_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!isset($this->usuario)) {
            redirect();
        }


        $this->load->model('Alumno_model');

        $this->load->model('Carrera_model');
        $datos_carreras = $this->session->userdata('datos_carrera');
        if (!$datos_carreras) {
            $datos_carreras = $this->Carrera_model->get_carreras_jefe($this->usuario->CODIGO);
            $this->session->set_userdata('datos_carrera', $datos_carreras);
        }

        $this->load->library('breadcrumb');
        $template = [
            'tag_open' => '<ol class="breadcrumb">',
            'crumb_open' => '<li class="breadcrumb-item">',
            'crumb_active' => '<li class="breadcrumb-item active" aria-current="page">'
        ];
        $this->breadcrumb->set_template($template);


         $this->template->add_css('css/custom_table_size');
        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');
        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');

        $this->template->set_template('default_template/default_template');
        $this->template->add_css('css/adminlte/skins/skin-red-light.min');
        $this->template->add_css('css/adminlte/skins/_all-skins.min');
    }




    function carreras_array()
    {

        $datos_carreras = $this->session->userdata('datos_carrera');

        $codigos = array_map(function ($obj) {
            return "'" . addslashes($obj->COD_CARRERA) . "'";
        }, $datos_carreras);

        return '(' . implode(', ', $codigos) . ')';
    }

    /*
        Muestra la vista de estudiantes inactivos para un semestre y año específicos.

        @input GET string $anio Año académico.

        @input GET string $semestre Semestre académico (1 o 2).

        @return void Renderiza la vista 'dashboard/ver_estudiantes2' con datos de alumnos inactivos.
    */

    public function buscar_inactivos_tabla_semestre($anio, $semestre)
    {

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');
        $carrera = $this->carreras_array();


        $datos = $this->Alumno_model->inactivos_semestre($carrera, $anio . '-' . $semestre);

        $this->template->add_js('js/alumno/no_matriculado2');

        $breadcrumb_items = [
        
            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Inactivos' => 'alumno/buscar_inactivos_tabla_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver alumnos inactivos');
        $this->template->set('content_sub_header', 'Alumnos que pasaron a ser inactivos en el semestre '.$anio . '-' . $semestre);

        $this->template->render('estadistica/lista_inactivos');
    }



    public function buscar_activos_tabla_semestre($anio, $semestre)
    {

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');
        $carrera =  $this->carreras_array();

        $datos = $this->Alumno_model->activos_semestre($carrera, $anio . '-' . $semestre);

        $breadcrumb_items = [
        
            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Activos' => 'alumno/buscar_activos_tabla_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos);

        $this->template->add_js('js/alumno/activos');
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver alumnos activos');
        $this->template->set('content_sub_header', 'Listado de alumnos que estuvieron activos en el semestre '.$anio . '-' . $semestre);
        $this->template->render('estadistica/lista_activos');
    }
    /*
        Muestra la vista de estudiantes inactivos para un semestre y año  de ingreso específicos.

        @input GET string $anio Año académico.

        @input GET string $semestre Semestre académico (1 o 2).

        @return void Renderiza la vista 'dashboard/ver_estudiantes2' con datos de alumnos inactivos.
    */

    public function est_inactivos_tabla_ingreso($anio, $semestre)
    {


        $carrera =  $this->carreras_array();

        $datos = $this->Alumno_model->get_por_semestre($anio, $semestre, $carrera);

        $this->template->add_js('js/alumno/no_matriculado2');


        $breadcrumb_items = [
            
            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Inactivos' => 'alumno/est_inactivos_tabla_ingreso'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre_ingreso');
        $this->template->set('content_header', 'Ver alumnos por semestre de ingreso');
        $this->template->set('content_sub_header', 'Listado de alumnos inactivos que ingresaron en el semestre '.$anio . '-' . $semestre);

        $this->template->render('estadistica/lista_inactivos');
    }



    public function est_activos_tabla_ingreso($anio, $semestre)
    {

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');
        $carrera =  $this->carreras_array();


        $datos = $this->Alumno_model->get_activos_por_semestre($anio, $semestre, $carrera);



        $breadcrumb_items = [
           
            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Activos' => 'alumno/est_activos_tabla_ingreso'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos);

        $this->template->add_js('js/alumno/activos');
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre_ingreso');
        $this->template->set('content_header', 'Ver alumnos por semestre de ingreso');
        $this->template->set('content_sub_header', 'Listado de alumnos activos que ingresaron en el semestre '.$anio . '-' . $semestre);
        $this->template->render('estadistica/lista_activos');
    }

    /*
        Muestra la vista de estudiantes inactivos para un semestre y año  de ingreso específicos.

        @input GET string $anio Año académico.

        @input GET string $semestre Semestre académico (1 o 2).

        @return void Renderiza la vista 'dashboard/ver_estudiantes2' con datos de alumnos inactivos.
    */

    public function buscar_inactivos_tabla($anio, $semestre)
    {


        $carrera =  $this->carreras_array();

        $datos = $this->Alumno_model->get_por_semestre($anio, $semestre, $carrera);

        $this->template->add_js('js/alumno/no_matriculado2');


        $breadcrumb_items = [
            
            'Ver por semestre' => 'alumno/ver_por_semestre',
            'Inactivos' => 'alumno/buscar_inactivos'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_por_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');

        $this->template->render('alumno/lista_inactivos_simple');
    }



    public function buscar_activos_tabla($anio, $semestre)
    {

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');
        $carrera =  $this->carreras_array();


        $datos = $this->Alumno_model->get_activos_por_semestre($anio, $semestre, $carrera);



        $breadcrumb_items = [
       
            'Ver por semestre' => 'alumno/ver_por_semestre',
            'Activos' => 'alumno/buscar_activos'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos);

        $this->template->add_js('js/alumno/activos');
        $this->template->set('item_sidebar_active', 'ver_por_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        $this->template->render('alumno/lista_activos_simple');
    }

    public function buscar_activos()
    {

        $carrera =  $this->carreras_array();


        if ($this->input->method() === 'post') {
            $this->session->set_userdata('tipo_busqueda', $this->input->post('tipo_busqueda'));

            // búsqueda por semestre único
            $this->session->set_userdata('anio_unico', $this->input->post('anio_unico'));
            $this->session->set_userdata('semestre_unico', $this->input->post('semestre_unico'));

            // búsqueda por rango
            $this->session->set_userdata('anio_inicio', $this->input->post('anio_inicio'));
            $this->session->set_userdata('semestre_inicio', $this->input->post('semestre_inicio'));
            $this->session->set_userdata('anio_fin', $this->input->post('anio_fin'));
            $this->session->set_userdata('semestre_fin', $this->input->post('semestre_fin'));


            // redirige para evitar reenvío de formulario
            redirect('alumno/buscar_activos');
        } else {

            $tipo_busqueda = $this->session->userdata('tipo_busqueda');

            $anio = $this->session->userdata('anio_unico');
            $semestre = $this->session->userdata('semestre_unico');

            $anio_inicio = $this->session->userdata('anio_inicio');
            $semestre_inicio = $this->session->userdata('semestre_inicio');
            $anio_fin = $this->session->userdata('anio_fin');
            $semestre_fin = $this->session->userdata('semestre_fin');


            $sems = $this->Alumno_model->get_primer_ingreso($carrera);

            if (!$tipo_busqueda)  $tipo_busqueda = 'desde';

            if (!$anio)  $anio =  (int) $sems->ANO;
            if (!$semestre)  $semestre = 1;
            if (!$anio_inicio) $anio_inicio = $anio;
            if (!$semestre_inicio) $semestre_inicio = 1;
            if (!$anio_fin) $anio_fin = date('Y');
            if (!$semestre_fin) {

                $mes_actual = date('n');
                $dia_actual = date('j');

                if ($mes_actual < 8 || ($mes_actual == 8 && $dia_actual < 1)) {
                    $semestre_actual = 1;
                } else {
                    $semestre_actual = 2;
                }

                $semestre_fin = $semestre_actual;
            }


            if ($tipo_busqueda === 'semestre' || $tipo_busqueda === 'desde') {

                if ($tipo_busqueda === 'semestre') {
                    $datos = $this->Alumno_model->get_activos_por_semestre($anio, $semestre, $carrera);
                } else {
                    $datos = $this->Alumno_model->get_activos_desde_semestre($anio, $semestre, $carrera);
                }
            } elseif ($tipo_busqueda === 'rango') {

                $datos = $this->Alumno_model->get_activos_por_semestre_rango($anio_inicio, $semestre_inicio, $anio_fin, $semestre_fin, $carrera);
            } else {
                log_message('error', 'Tipo de búsqueda no válido');
            }

            $sem_activos = $this->Alumno_model->get_sem_activos($carrera);


            $breadcrumb_items = [
               
                'Activos' => 'alumno/buscar_activos'
            ];
            $this->breadcrumb->add_item($breadcrumb_items);

            $bread = $this->breadcrumb->generate();
            $this->template->set('bread', $bread);

            $this->template->add_js('js/alumno/activos');

            $this->template->set('tipo', $tipo_busqueda);
            $this->template->set('sem_activos', $sem_activos);

            $this->template->set('anio', $anio);
            $this->template->set('semestre', $semestre);

            $this->template->set('anio_inicio', $anio_inicio);
            $this->template->set('semestre_inicio', $semestre_inicio);
            $this->template->set('anio_fin', $anio_fin);
            $this->template->set('semestre_fin', $semestre_fin);

            $this->template->set('datos', $datos);
            $this->template->set('item_sidebar_active', 'ver_activos');
            $this->template->set('content_header', 'Ver alumnos');
            $this->template->set('content_sub_header', 'Listado de alumnos');
            $this->template->render('dashboard/ver_activos');
        }
    }


    public function buscar_inactivos()
    {

        //echo $this->carreras_array();


        $carrera = $this->carreras_array();

        if ($this->input->method() === 'post') {
            $this->session->set_userdata('inact_tipo_busqueda', $this->input->post('tipo_busqueda'));

            // búsqueda por semestre único
            $this->session->set_userdata('inact_anio_unico', $this->input->post('anio_unico'));
            $this->session->set_userdata('inact_semestre_unico', $this->input->post('semestre_unico'));

            // búsqueda por rango
            $this->session->set_userdata('inact_anio_inicio', $this->input->post('anio_inicio'));
            $this->session->set_userdata('inact_semestre_inicio', $this->input->post('semestre_inicio'));
            $this->session->set_userdata('inact_anio_fin', $this->input->post('anio_fin'));
            $this->session->set_userdata('inact_semestre_fin', $this->input->post('semestre_fin'));


            // redirige para evitar reenvío de formulario
            redirect('alumno/buscar_inactivos');
        } else {

            $tipo_busqueda = $this->session->userdata('inact_tipo_busqueda');



            $anio = $this->session->userdata('inact_anio_unico');
            $semestre = $this->session->userdata('inact_semestre_unico');

            $anio_inicio = $this->session->userdata('inact_anio_inicio');
            $semestre_inicio = $this->session->userdata('inact_semestre_inicio');
            $anio_fin = $this->session->userdata('inact_anio_fin');
            $semestre_fin = $this->session->userdata('inact_semestre_fin');

            $duracion = (int) $this->Carrera_model->get_duracion($carrera)->DURACION / 2;

            if (!$tipo_busqueda)  $tipo_busqueda = 'desde';

            if (!$anio)  $anio = date('Y') - $duracion;
            if (!$semestre)  $semestre = 1;
            if (!$anio_inicio) $anio_inicio = $anio;
            if (!$semestre_inicio) $semestre_inicio = $semestre;
            if (!$anio_fin) $anio_fin = date('Y');


            if (!$semestre_fin) {

                $mes_actual = date('n');
                $dia_actual = date('j');

                if ($mes_actual < 8 || ($mes_actual == 8 && $dia_actual < 1)) {
                    $semestre_actual = 1;
                } else {
                    $semestre_actual = 2;
                }

                $semestre_fin = $semestre_actual;
            }

            if ($tipo_busqueda === 'semestre' || $tipo_busqueda === 'desde') {


                if ($tipo_busqueda === 'semestre') {
                    $datos = $this->Alumno_model->get_por_semestre($anio, $semestre, $carrera);
                } else {
                    $datos = $this->Alumno_model->get_desde_semestre($anio, $semestre, $carrera);
                }
            } elseif ($tipo_busqueda === 'rango') {



                $datos = $this->Alumno_model->get_por_semestre_rango($anio_inicio, $semestre_inicio, $anio_fin, $semestre_fin, $carrera);
            } else {
                log_message('error', 'Tipo de búsqueda no válido');
            }



            $sem_activos = $this->Alumno_model->get_sem_activos($carrera);


            $breadcrumb_items = [
                
                'Inactivos' => 'alumno/buscar_inactivos'
            ];
            $this->breadcrumb->add_item($breadcrumb_items);

            $bread = $this->breadcrumb->generate();
            $this->template->set('bread', $bread);

            $this->template->add_js('js/alumno/no_matriculado2');


            $this->template->set('anio', $anio);
            $this->template->set('semestre', $semestre);

            $this->template->set('anio_inicio', $anio_inicio);
            $this->template->set('semestre_inicio', $semestre_inicio);
            $this->template->set('anio_fin', $anio_fin);
            $this->template->set('semestre_fin', $semestre_fin);


            $this->template->set('tipo', $tipo_busqueda);
            $this->template->set('sem_activos', $sem_activos);



            $this->template->set('datos', $datos);
            $this->template->set('item_sidebar_active', 'ver_estudiantes');
            $this->template->set('content_header', 'Ver alumnos');
            $this->template->set('content_sub_header', 'Listado de alumnos');
            //
            $this->template->render('dashboard/ver_estudiantes2');
        }
    }

    public function ver_por_semestre()
    {

        $carrera =  $this->carreras_array();

        $this->load->model('Alumno_model');

        $datos = $this->Alumno_model->get_cantidad_estados($carrera);


        //$this->template->add_message(array("error" => "ocurrio un error"));
        //$this->template->add_message(array("warning" => "ocurrio una alerta"));
        //$this->template->add_message(array("success" => "ocurrio algo sin problemas"));

        //estas alertas se mostrarán en la siquiente página que acceda el usuario (se utilizan antes de un redirect())
        //$this->template->set_flash_message(array("mensaje de información cargado en la página anterior por el controlador item2"), "info");


        //$this->template->set('cantidad',$cantidad);



        $breadcrumb_items = [
           
            'Ver por semestre' => 'alumno/ver_por_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);
        $this->template->add_js('js/alumno/ver_por_semestre');
        $this->template->set('carrera', $carrera);
        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_por_semestre');
        $this->template->set('content_header', 'Ver por semestre');
        $this->template->set('content_sub_header', 'Estado de alumnos por semestre');
        $this->template->render('dashboard/ver_por_semestre');
    }
}
