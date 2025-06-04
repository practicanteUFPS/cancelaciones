<?php

class Matricula_alumno extends CMS_Controller {

    function __construct() {
        parent::__construct();


        if (!isset($this->usuario)) {
            redirect();
        }

        $this->load->model('Matricula_alumno_model');
        $this->output->set_content_type('application/json'); 
        $this->output->enable_profiler(FALSE); 

    }

    public function get_datos(){

        $codigo = $this->input->get('codigo');
        $carrera = $this->input->get('carrera');
        $listado = $this->Matricula_alumno_model->get_matricula($codigo, $carrera );
        echo json_encode($listado);
    }


}