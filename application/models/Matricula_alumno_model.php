<?php
class Matricula_alumno_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_matricula($codigo, $carrera)
    {
        $sql = "SELECT ma.*, m.DESCRIPCION FROM MATRICULA_ALUMNO ma 
        JOIN MATRICULADO m ON ma.MATRICULADO = m.ESTADO
        WHERE  ma.COD_ALUMNO = '$codigo' AND ma.COD_CARRERA = '$carrera'
        ORDER BY ma.FECHA DESC ";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

   
}
