<?php

class Cancelaciones extends CMS_Controller
{

    function __construct()
    {
        parent::__construct();


        if (!isset($this->usuario)) {
            redirect();
        }

        $this->load->library('breadcrumb');
        $template = [
            'tag_open' => '<ol class="breadcrumb">',
            'crumb_open' => '<li class="breadcrumb-item">',
            'crumb_active' => '<li class="breadcrumb-item active" aria-current="page">'
        ];
        $this->breadcrumb->set_template($template);

        $this->load->model('Cancelacione_model');


        $this->template->set_template('default_template/default_template');

        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');



        $this->template->add_css('css/adminlte/skins/skin-red-light.min');
        $this->template->add_css('css/adminlte/skins/_all-skins.min');
    }

    public function alumno_nota_tipo($carrera, $ano, $semestre, $codigo,$mostrar, $tipo)
    {

        switch ($tipo) {

            case 'cancel':
                $this->load->model('Cancelacione_model');
                $listado = $this->Cancelacione_model->get_cancelacion_alumno($codigo, $carrera, $semestre, $ano);
                $listado2 = $this->Cancelacione_model->get_cancelacion_alumno_graduado($codigo, $carrera, $semestre, $ano);
                $msg = 'con cancelaciones extrardinarias';
                break;
            case 'cancelord':
                $this->load->model('Cancelacione_model');
                $listado = $this->Cancelacione_model->get_cancelacion_ordinaria_alumno($codigo, $carrera, $semestre, $ano);
                $listado2 = $this->Cancelacione_model->get_cancelacion_ordinaria_alumno_graduado($codigo, $carrera, $semestre, $ano);
                $msg ='con cancelaciones ordinarias';
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
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', "Listado de alumnos $msg");
        $this->template->render('alumno/cancelaciones_materia_lista');
    }
}
