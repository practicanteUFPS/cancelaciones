<?php

class Nota extends CMS_Controller
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

        $this->load->model('Nota_model');



        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
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


    public function mostrar_notas_json($carrera, $codigo)
    {

        //$codigo = $this->input->get('codigo');
        //$carrera = $this->input->get('carrera');

        $this->load->model('Matricula_alumno_model');
        $this->output->set_content_type('application/json');
        $this->output->enable_profiler(FALSE);

        $semestres = $this->Matricula_alumno_model->get_matricula($codigo, $carrera);
        $tam = count($semestres);
        $notasarr = array_fill(0, $tam, []);
        $notas = $this->Nota_model->get_nota($codigo, $carrera);

        foreach ($semestres as $semestre) {

            $semestre->NOTAS = [];

            foreach ($notas as $nota) {
                if ($semestre->ANO == $nota->ANO and $semestre->SEMESTRE == $nota->SEMESTRE) {
                    $semestre->NOTAS[] = $nota;
                }
            }
        }
        echo json_encode($semestres);
    }

    public function mostrar_notas_semestre_graduado()
    {

        $codigo = $this->input->get('codigo');
        $carrera = $this->input->get('carrera');

        $notas = $this->Nota_model->get_nota_graduado($codigo, $carrera);

        if (empty($notas)) {
            $notas = $this->Nota_model->get_nota($codigo, $carrera);
        }

        $nota_semestre = [];

        // Recorrer y agrupar
        foreach ($notas as $nota) {
            $semestre = $nota->ANO . '-' . $nota->SEMESTRE;

            if (!isset($nota_semestre[$semestre])) {
                $nota_semestre[$semestre] = [];
            }

            $nota_semestre[$semestre][] = $nota;
        }

        $this->template->set('nota_semestre', $nota_semestre);
        $this->template->set('item_sidebar_active', 'ver_asignaturas');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de asignaturas');
        $this->template->render('alumno/notas_graduado');
    }

    public function mostrar_historico($carrera, $codigo, $sem)
    {

        $this->session->set_userdata('sem_mat', $sem);


        $this->load->model('Materia_model');

        $listado = $this->Nota_model->get_conteo_cancelaciones($codigo, $carrera);
        $materia = $this->Materia_model->get_codigo($codigo, $carrera);


        $breadcrumb_items = [

            'Lista de asignaturas' => 'materia/ver_asignaturas',
            'Ver notas materia' => 'nota/mostrar_historico'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_js('js/asignaturas/detalle_asignatura');

        $this->template->set('conteo', $listado);
        $this->template->set('codigo', $codigo);
        $this->template->set('carrera', $carrera);
        $this->template->set('asignatura', $materia);
        $this->template->set('item_sidebar_active', 'ver_asignaturas');
        $this->template->set('content_header', 'Resumen de rendimiento académico por semestre');
        $this->template->set('content_sub_header', 'Estadísticas de aprobación, reprobación, cancelaciones');

        $this->template->render('asignaturas/historico');
    }

    public function alumno_nota_tipo($carrera, $ano, $semestre, $codigo, $mostrar, $tipo)
    {

        switch ($tipo) {
            case 'aprobado':
                $listado = $this->Nota_model->get_estudiante_nota_tipo($codigo, $carrera, $semestre, $ano, '>=', 3);
                $listado2 = $this->Nota_model->get_estudiante_graduado_nota_tipo($codigo, $carrera, $semestre, $ano, '>=', 3);

                $msg = 'aprobados';
                break;
            case 'reprobado':
                $listado = $this->Nota_model->get_estudiante_nota_tipo($codigo, $carrera, $semestre, $ano, '<', 3);
                $listado2 = $this->Nota_model->get_estudiante_graduado_nota_tipo($codigo, $carrera, $semestre, $ano, '<', 3);
                $msg = 'reprobados';
                break;

            case 'zero':
                $listado = $this->Nota_model->get_estudiante_nota_tipo($codigo, $carrera, $semestre, $ano, '=', 0);
                $listado2 = $this->Nota_model->get_estudiante_graduado_nota_tipo($codigo, $carrera, $semestre, $ano, '=', 0);
                $msg = 'con nota en cero';
                break;
            case 'cuatro':
                $listado = $this->Nota_model->get_estudiante_nota_tipo($codigo, $carrera, $semestre, $ano, '>=', 4);
                $listado2 = $this->Nota_model->get_estudiante_graduado_nota_tipo($codigo, $carrera, $semestre, $ano, '>=', 4);
                $msg = 'con nota mayor de 4';
                break;
            case 'total':
                $listado = $this->Nota_model->get_estudiante_nota_tipo($codigo, $carrera, $semestre, $ano, '>=', 0);
                $listado2 = $this->Nota_model->get_estudiante_graduado_nota_tipo($codigo, $carrera, $semestre, $ano, '>=', 0);
                $msg = '';
                break;
            case 'rango':
                $listado = $this->Nota_model->get_estudiante_nota_rango($codigo, $carrera, $semestre, $ano, 3, 4);
                $listado2 = $this->Nota_model->get_estudiante_graduado_nota_rango($codigo, $carrera, $semestre, $ano, 3, 4);
                $msg = 'con nota entre 3 y 4';
                break;
        }




        switch ($mostrar) {
            case 'a':
                $this->template->set('item_sidebar_active', 'ver_asignaturas_semestre');
                $breadcrumb_items = [

                    'Por semestre' => 'materia/mostrar_materia_estadistica',
                    'Estudiantes materia' => 'nota/alumno_nota_tipo'
                ];
                break;

            case 'b':
                $this->template->set('item_sidebar_active', 'ver_asignaturas');
                $breadcrumb_items = [

                    'Lista de asignaturas' => 'materia/ver_asignaturas',
                    'Ver notas materia' => 'nota/mostrar_historico',
                    'Estudiantes materia' => 'nota/alumno_nota_tipo'
                ];
                break;
        }


        $this->breadcrumb->add_item($breadcrumb_items);
        $bread = $this->breadcrumb->generate();

        $this->template->set('bread', $bread);
        $this->template->set('lista', $listado);
        $this->template->set('listagrads', $listado2);

        $this->template->add_js('js/nota/alumno_nota');
        $this->template->set('content_header', 'Ver alumnos ');
        $this->template->set('content_sub_header', "Listado de alumnos $msg");
        $this->template->render('alumno/alumno_nota');
    }
}
