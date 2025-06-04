<?php

class Exalumno_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();

    }

public function get_graduados_por_semestre( $semestre, $carrera)
    {
        $sql = "SELECT
                    SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, -1) AS PERIODO_RETIRO,
                    e.NUM_DIPLOMA,
                    e.COD_ALUMNO,
                    e.COD_CARRERA,
                    e.DOCUMENTO,
                    e.PROMEDIO,
                    dp.NOMBRES,
                    dp.SEXO,
                    CASE 
        WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (11, 12, 1, 2, 3) THEN 1
        WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (6, 7, 8, 9) THEN 2
        ELSE NULL
        END AS SEM_INGRESO,
        TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '1' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-01-01', 'YYYY-MM-DD')
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '2' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-07-01', 'YYYY-MM-DD')
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12) AS edad_SEMESTRE,
                    TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_actual
                FROM
                    EXALUMNO e
                JOIN DATOS_EXA dp ON
                    dp.DOCUMENTO = e.DOCUMENTO
                JOIN CARRERA c ON
                    e.COD_CARRERA = c.COD_CARRERA
                WHERE  SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, -1) = '$semestre' 
                AND e.COD_CARRERA in $carrera
                ORDER BY  e.FECHA_GRADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }



    public function get_graduados_por_semestre_ingreso($anio, $semestre, $carrera)
    {
        $sql = "SELECT
                    SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, -1) AS PERIODO_RETIRO,
                    e.NUM_DIPLOMA,
                    e.COD_ALUMNO,
                    e.COD_CARRERA,
                    e.DOCUMENTO,
                    e.PROMEDIO,
                    dp.NOMBRES,
                    dp.SEXO,
                    CASE 
        WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (11, 12, 1, 2, 3) THEN 1
        WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (6, 7, 8, 9) THEN 2
        ELSE NULL
        END AS SEM_INGRESO,
        TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '1' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-01-01', 'YYYY-MM-DD')
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '2' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-07-01', 'YYYY-MM-DD')
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12) AS edad_SEMESTRE,
                    TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_actual
                FROM
                    EXALUMNO e
                JOIN DATOS_EXA dp ON
                    dp.DOCUMENTO = e.DOCUMENTO
                JOIN CARRERA c ON
                    e.COD_CARRERA = c.COD_CARRERA
                WHERE  EXTRACT(YEAR FROM e.FEC_INGRESO) = '$anio' 
                AND CASE 
        WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (11, 12, 1, 2, 3) THEN 1
        WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (6, 7, 8, 9) THEN 2
        ELSE NULL
        END =$semestre
                AND e.COD_CARRERA in $carrera
                ORDER BY  e.FECHA_GRADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }
}
