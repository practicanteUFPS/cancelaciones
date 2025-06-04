<?php

class Materia extends CMS_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!isset($this->usuario)) {
            redirect();
        }

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


    public function mostrar_materia_estadistica()
    {
        $carrera = $this->carreras_array();

        if ($this->input->method() === 'post') {

            // búsqueda por semestre único
            $this->session->set_userdata('mate_anio', $this->input->post('anio_unico'));
            $this->session->set_userdata('mate_semestre', $this->input->post('semestre_unico'));


            // redirige para evitar reenvío de formulario
            redirect('materia/mostrar_materia_estadistica');
        } else {
            $anio = $this->session->userdata('mate_anio');
            $semestre = $this->session->userdata('mate_semestre');

            if (!$anio) $anio = '2024';
            if (!$semestre)  $semestre = 2;

            $this->load->model('Alumno_model');
            $this->load->model('Carrera_model');
            $this->load->model('Materia_model');

            $sems = $this->Alumno_model->get_primer_ingreso($carrera);
            $listado = $this->Materia_model->get_conteo_por_semestre($semestre, $anio, $carrera);

            $anio_inicio = (int) $sems->ANO;
            $anio_actual = date('Y');

            


            $breadcrumb_items = [

                'Por semestre' => 'materia/mostrar_materia_estadistica'
            ];
            $this->breadcrumb->add_item($breadcrumb_items);

            $bread = $this->breadcrumb->generate();
            $this->template->set('bread', $bread);

            $this->template->add_js('js/asignaturas/asignaturas_semestre');
            
            $this->template->set('datos', $listado);
            $this->template->set('carrera', $carrera);
            $this->template->set('anio', $anio);
            $this->template->set('semestre', $semestre);
            $this->template->set('anio_actual', $anio_actual);
            $this->template->set('anio_inicio', $anio_inicio);
            $this->template->set('item_sidebar_active', 'ver_asignaturas_semestre');
            $this->template->set('content_header', 'Resumen de rendimiento académico por semestre');
            $this->template->set('content_sub_header', 'Estadísticas de aprobación, reprobación, cancelaciones');
            $this->template->render('dashboard/ver_asignaturas_semestre');
        }
    }


    public function ver_asignaturas()
    {

        $carrera =  $this->carreras_array();

        $sem_select =  $this->session->userdata('sem_mat');

        if (!$sem_select) $sem_select = -1;

        $this->load->model('Carrera_model');

        $duracion = $this->Carrera_model->get_duracion($carrera);

        $num_semestres = (int) $duracion->DURACION;

        $this->load->model('Materia_model');
        //$asignaturas = array();
        $materias = $this->Materia_model->get_carrera_electivas($carrera);

        // Inicializar array de semestres
        $asignaturas = array_fill(0, $num_semestres + 1, []);

        foreach ($materias as $materia) {
            $semestre = ltrim($materia->SEMESTRE, '0'); // Eliminar ceros a la izquierda
            $semestre = ($semestre === '') ? 0 : (int) $semestre; // Convertir a número, asegurando que "0" quede bien manejado

            // Si el semestre es 0 o mayor que la duración, asignarlo al índice 0
            if ($semestre <= 0 || $semestre > $num_semestres) {
                $semestre = 0;
            }

            $asignaturas[$semestre][] = $materia;
        }


        $breadcrumb_items = [


            'Lista de asignaturas' => 'materia/ver_asignaturas'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);



        $this->template->set('sem_select', $sem_select);
        $this->template->set('duracion', $duracion);
        $this->template->set('semestres', $asignaturas);
        $this->template->set('item_sidebar_active', 'ver_asignaturas');
        $this->template->set('content_header', 'Ver asignaturas');
        $this->template->set('content_sub_header', 'Listado de asignaturas');
        $this->template->add_js('js/asignaturas/detalle_asignatura');
        $this->template->render('dashboard/ver_asignaturas');
    }
}
