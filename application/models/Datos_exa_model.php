<?php

class Datos_exa_model extends CI_Model {

function __construct() {
        parent::__construct();
    }
    public function get_datos_per($documento){ 
        
        $sql = "SELECT de.* FROM DATOS_EXA de WHERE  de.DOCUMENTO = '$documento'";

        //echo $sql
        //$this->database2->get_obj_array($sql, $arr);
        $arr= array();
        $this->database2->get_sql_object($sql, $arr);
        return $arr;
    }


}