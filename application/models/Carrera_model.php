<?php
class Carrera_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }


    public function get_carreras_jefe($codigo)
    {
        $sql = "SELECT COD_CARRERA, NOMCORTO
FROM CARRERA
WHERE COD_JEFE = '$codigo'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


    public function get_carrera($codigo)
    {
        $sql = "SELECT c.* FROM CARRERA C 
        WHERE  c.COD_CARRERA = '$codigo'";

        $arr = array();
        $this->database2->get_sql_object($sql, $arr);
        return $arr;
    }

    public function get_duracion($codigo)
    {
        $sql = "SELECT MAX(C.DURACION) AS DURACION
FROM CARRERA C
WHERE C.COD_CARRERA IN $codigo";
        $arr = array();

        $this->database2->get_sql_object($sql, $arr);
        return $arr;
    }
}
