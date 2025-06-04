<?php

class Datos_exa extends CMS_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Datos_exa_model');
        $this->output->set_content_type('application/json'); 
        $this->output->enable_profiler(FALSE); 
      

    }

    public function get_datos(){

        $documento = $this->input->get('documento');
        $datos = $this->Datos_exa_model->get_datos_per($documento );
        echo json_encode($datos);
    }


}