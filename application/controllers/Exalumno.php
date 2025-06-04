<?php

class Exalumno extends CMS_Controller
{
    function __construct()
    {
        parent::__construct();

        if (!isset($this->usuario)) {
            redirect();
        }
        $this->load->model('Exalumno_model');

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

    public function lista_graduados_sexo($anio, $semestre, $sexo)
    {

        $carrera = $this->carreras_array();

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');


        $datos = $this->Exalumno_model->get_graduados_por_semestre($anio . '-' . $semestre, $carrera);

        $datos_filtrados = array_filter($datos, function ($alumno) use ($sexo) {
            return isset($alumno->SEXO) && $alumno->SEXO === $sexo;
        });

        // Reindexar si es necesario
        $datos_filtrados = array_values($datos_filtrados);

        $breadcrumb_items = [

            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Graduados' => 'exalumno/lista_graduados_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');

        $this->template->add_js('js/exalumno/exalumno');

        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');


        $this->template->set('datos', $datos_filtrados);
        $this->template->set('item_sidebar_active', 'ver_por_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        //
        $this->template->render('estadistica/lista_graduados');

        //echo var_dump($datos);



    }

    public function lista_graduados_edad($anio, $semestre, $categoria)
    {

        $carrera = $this->carreras_array();

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');


        $datos = $this->Exalumno_model->get_graduados_por_semestre($anio . '-' . $semestre, $carrera);
        $datos_filtrados = array_filter($datos, function ($alumno) use ($categoria) {
            if (!isset($alumno->EDAD_SEMESTRE)) return false;

            $edad = (int)$alumno->EDAD_SEMESTRE;

            switch ($categoria) {
                case 'MENOR_18':
                    return $edad < 18;
                case 'ENTRE_18_24':
                    return $edad >= 18 && $edad <= 24;
                case 'ENTRE_25_29':
                    return $edad >= 25 && $edad <= 29;
                case 'MAYOR_30':
                    return $edad > 30;
                default:
                    return false; // categoría no válida
            }
        });

        // Reindexar si lo necesitas
        $datos_filtrados = array_values($datos_filtrados);

        $breadcrumb_items = [

            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Graduados' => 'exalumno/lista_graduados_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');

        $this->template->add_js('js/exalumno/exalumno');

        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');


        $this->template->set('datos', $datos_filtrados);
        $this->template->set('item_sidebar_active', 'ver_por_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        //
        $this->template->render('estadistica/lista_graduados');

        //echo var_dump($datos);



    }

    public function lista_graduados_semestre($anio, $semestre)
    {

        $carrera =  $this->carreras_array();


        $datos = $this->Exalumno_model->get_graduados_por_semestre($anio . '-' . $semestre, $carrera);


        $breadcrumb_items = [

            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Graduados' => 'exalumno/lista_graduados_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');

        $this->template->add_js('js/exalumno/exalumno');

        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');


        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver graduados');
        $this->template->set('content_sub_header', 'Listado de alumnos graduados en el semestre '.$anio . '-' . $semestre);
        //
        $this->template->render('estadistica/lista_graduados');

        //echo var_dump($datos);



    }



    public function lista_graduados_ingreso($anio, $semestre)
    {

        $carrera =  $this->carreras_array();

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');


        $datos = $this->Exalumno_model->get_graduados_por_semestre_ingreso($anio, $semestre, $carrera);


        $breadcrumb_items = [

            'Desercion por semestre de ingreso' => 'estadistica/desercion_semestre_ingreso',
            'Graduados' => 'exalumno/lista_graduados_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');

        $this->template->add_js('js/exalumno/exalumno');

        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');


        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre_ingreso');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de graduados que ingresaron en el semestre '.$anio . '-' . $semestre);
        //
        $this->template->render('estadistica/lista_graduados');

        //echo var_dump($datos);
    }

    public function lista_graduados($anio, $semestre)
    {

        $carrera =  $this->carreras_array();


        $datos = $this->Exalumno_model->get_graduados_por_semestre_ingreso($anio, $semestre, $carrera);


        $breadcrumb_items = [

            'Ver por semestre' => 'alumno/ver_por_semestre',
            'Graduados' => 'exalumno/lista_graduados'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');

        $this->template->add_js('js/exalumno/exalumno');

        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');


        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_por_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        //
        $this->template->render('exalumno/exalumno_lista');
    }
}
