<?php

class Carrera extends CMS_Controller
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



    public function vista()
    {

        $datos_carreras = $this->Carrera_model->get_carreras_jefe($this->usuario->CODIGO);
        if ($this->input->method() === 'post') {


            $carreras_string = $this->input->post('carreras');

            $carreras_array = explode('-', $carreras_string);


            $datos_carreras_filtrados = array_filter($datos_carreras, function ($carrera) use ($carreras_array) {
                return in_array($carrera->COD_CARRERA, $carreras_array);
            });

            // Opcional: reindexar el array para que empiece en 0, 1, 2...
            $datos_carreras_filtrados = array_values($datos_carreras_filtrados);


            $this->session->set_userdata('datos_carrera', $datos_carreras_filtrados);

            // redirect('carrera/vista');
        }

        $datos_carreras = $this->Carrera_model->get_carreras_jefe($this->usuario->CODIGO);
        $datos_carreras_select = $this->session->userdata('datos_carrera');

        $breadcrumb_items = [
            'Seleccionar carreras' => 'carrera/vista',
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->set('carrera_todas', $datos_carreras);
        $this->template->set('carrera_select', $datos_carreras_select);
        $this->template->set('item_sidebar_active', 'carrera_vista');
        $this->template->set('content_header', 'Seleccionar carreras');
        $this->template->set('content_sub_header', 'Seleccione las carreras de las que se mostrara informacion');

        $this->template->render('carrera/select_carrera');
    }
}
