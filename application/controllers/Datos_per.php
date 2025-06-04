<?php

class Datos_per extends CMS_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Datos_per_model');
        $this->output->set_content_type('application/json'); 
        $this->output->enable_profiler(FALSE); 

    }

    public function get_datos(){

        $documento = $this->input->get('documento');
        $listado = $this->Datos_per_model->get_datos_per($documento );
        echo json_encode($listado);
    }


}