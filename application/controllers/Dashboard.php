<?php

class Dashboard extends CMS_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!isset($this->usuario)) {
            redirect();
        }


        $this->load->library('breadcrumb');


        // Custom style
        $template = [
            'tag_open' => '<ol class="breadcrumb">',
            'crumb_open' => '<li class="breadcrumb-item">',
            'crumb_active' => '<li class="breadcrumb-item active" aria-current="page">'
        ];
        $this->breadcrumb->set_template($template);

        $this->template->set_template('default_template/default_template');

        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');

        $this->template->add_css('css/adminlte/skins/skin-red-light.min');
        $this->template->add_css('css/adminlte/skins/_all-skins.min');
    }

    public function index()
    {
        $this->template->set('item_sidebar_active', 'dashboard');
        $this->template->set('content_header', 'Dashboard (Titulo de la sección)');
        $this->template->set('content_sub_header', 'Subtitulo de la sección');
        $this->template->render('dashboard/index');
    }

/*
    public function ver_por_semestre()
    {

        $carrera = 122;

        $this->load->model('Alumno_model');

        $datos = $this->Alumno_model->get_cantidad_estados($carrera);


        //$this->template->add_message(array("error" => "ocurrio un error"));
        //$this->template->add_message(array("warning" => "ocurrio una alerta"));
        //$this->template->add_message(array("success" => "ocurrio algo sin problemas"));

        //estas alertas se mostrarán en la siquiente página que acceda el usuario (se utilizan antes de un redirect())
        //$this->template->set_flash_message(array("mensaje de información cargado en la página anterior por el controlador item2"), "info");


        //$this->template->set('cantidad',$cantidad);



        $breadcrumb_items = [
            'Dashboard' => 'dashboard',
            'Ver por semestre' => 'dashboard/ver_por_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);
        $this->template->set('carrera', $carrera);
        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_por_semestre');
        $this->template->set('content_header', 'Ver por semestre');
        $this->template->set('content_sub_header', 'Estado de alumnos por semestre');
        $this->template->render('dashboard/ver_por_semestre');
    }

    public function ver_estudiantes2()
    {

        
        $breadcrumb_items = [
            'Dashboard' => 'dashboard',
            'Ver por semestre' => 'dashboard/ver_por_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();

        $carrera = 122;
        $this->load->model('Alumno_model');
        $this->load->model('Carrera_model');

        $tipo = 'desde';



        $duracion = (int) $this->Carrera_model->get_duracion($carrera)->DURACION / 2;

        $sems = $this->Alumno_model->get_primer_ingreso($carrera);

        $sem_activos = $this->Alumno_model->get_sem_activos($carrera);
        //get_sem_activos

        $anio_inicio = (int) $sems->ANO;
        $anio_actual = date('Y');
        $anio = $anio_actual - $duracion;
        $semestre = 1;


        //$datos = $this->Alumno_model->get_lista($carrera);
        $datos = $this->Alumno_model->get_desde_semestre($anio, $semestre, $carrera);

        $mensaje = "Mostrando inactivos que se han matriculado a partir del año " . ($anio_actual - $duracion);


        $this->template->add_message(array("info" => $mensaje));

        $this->template->add_js('js/alumno/no_matriculado2');


        $anio_inicio = $anio;
        $semestre_inicio = $semestre;

        $anio_fin = $anio_actual;
        $semestre_fin = 1;

        $this->template->set('anio', $anio);
        $this->template->set('semestre', $semestre);

        $this->template->set('anio_inicio', $anio_inicio);
        $this->template->set('semestre_inicio', $semestre_inicio);
        $this->template->set('anio_fin', $anio_fin);
        $this->template->set('semestre_fin', $semestre_fin);



        $this->template->set('datos', $datos);
        $this->template->set('tipo', $tipo);
        $this->template->set('sem_activos', $sem_activos);
        $this->template->set('item_sidebar_active', 'ver_estudiantes');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        //
        $this->template->render('dashboard/ver_estudiantes2');
    }

    public function ver_estudiantes_activos()
    {

        $carrera = 122;
        $this->load->model('Alumno_model');

        $datos = $this->Alumno_model->get_activos($carrera);

        $tipo = 'desde';

        // JS (antes del cierre de </body>)
        $this->template->add_js('js/alumno/activos');

        $sems = $this->Alumno_model->get_primer_ingreso($carrera);

        $anio = (int) $sems->ANO;
        $anio_actual = date('Y');


        $sem_activos = $this->Alumno_model->get_sem_activos($carrera);

        $anio_inicio = $anio;
        $semestre_inicio = 1;

        $anio_fin = $anio_actual;
        $semestre_fin = 1;

        $this->template->set('anio', $anio);
        $this->template->set('semestre', 1);

        $this->template->set('anio_inicio', $anio_inicio);
        $this->template->set('semestre_inicio', $semestre_inicio);
        $this->template->set('anio_fin', $anio_fin);
        $this->template->set('semestre_fin', $semestre_fin);


        $this->template->set('datos', $datos);
        $this->template->set('sem_activos', $sem_activos);
        $this->template->set('tipo', $tipo);
        $this->template->set('item_sidebar_active', 'ver_activos');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        //
        $this->template->render('dashboard/ver_activos');
    }

    */
}
