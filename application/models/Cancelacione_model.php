<?php
class Cancelacione_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }




    public function materia_conteo($carrera, $anio, $semestre, $estado)
    {
        $sql = "SELECT  c.ANO, c.SEMESTRE , c.COD_CARRERA , cd.COD_MATERIA , m.NOMBRE , 
        TO_NUMBER(m.SEMESTRE) AS SEM_MAT, count(*) AS TOTAL
                FROM cancelacione c
                JOIN CANCELACIONE_DETALLE cd ON cd.CONSECUTIVO = c.CONSECUTIVO 
                JOIN MATERIA m ON m.COD_CARRERA  = c.COD_CARRERA AND m.COD_MATERIA  = cd.COD_MATERIA
                WHERE cd.FACTOR  IS NOT NULL AND c.ANO  = $anio AND c.SEMESTRE= $semestre 
                AND c.COD_CARRERA in $carrera  AND c.ESTADO = '$estado'
                GROUP BY c.ANO, c.SEMESTRE , c.COD_CARRERA , cd.COD_MATERIA , m.NOMBRE , TO_NUMBER(m.SEMESTRE)
                ORDER BY c.ANO , c.SEMESTRe , TOTAL DESC , c.COD_CARRERA, cd.COD_MATERIA";
        //c.ESTADO = '$estado'

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

public function semestre_factores($carrera)
    {
        $sql = "SELECT DISTINCT c.ANO, c.SEMESTRE AS semestre  FROM cancelacione c
                JOIN CANCELACIONE_DETALLE cd ON cd.CONSECUTIVO = c.CONSECUTIVO 
                WHERE cd.FACTOR  IS NOT NULL  AND c.COD_CARRERA  in $carrera
                ORDER BY  c.ANO, c.SEMESTRE ASC ";
        //c.ESTADO = '$estado'

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function factores_conteo($carrera, $anio, $semestre, $estado)
    {
        $sql = "SELECT c.ANO, c.SEMESTRE, fc.COD_FACTOR , fc.DESCRIPCION , count(*) FROM FACTOR_CANCELACION fc  
                JOIN CANCELACIONE_DETALLE cd ON cd.FACTOR = fc.COD_FACTOR
                JOIN cancelacione c ON  c.CONSECUTIVO  = cd.CONSECUTIVO
                WHERE c.ANO  = $anio AND c.SEMESTRE= $semestre AND c.COD_CARRERA in $carrera AND c.ESTADO = '$estado'
                GROUP BY  c.ANO, c.SEMESTRE, fc.COD_FACTOR , fc.DESCRIPCION
                ORDER BY c.ANO , c.SEMESTRE , fc.COD_FACTOR";
        //c.ESTADO = '$estado'

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function caracteristica_conteo_factor($carrera, $anio, $semestre, $factor, $estado)
    {
        $sql = "SELECT c.ANO, c.SEMESTRE,  cc.COD_FACTOR, fc.DESCRIPCION AS fact_descripcion,  cc.COD_CARACT ,
                cc.CARACTERISTICA , cc.DESCRIPCION AS caract_descripcion, count(*) FROM CARACTERISTICA_CANCELACION cc
                JOIN CANCELACIONE_DETALLE cd ON cd.FACTOR = cc.COD_FACTOR  AND cd.CARACTERISTICA = cc.COD_CARACT 
                JOIN cancelacione c ON  c.CONSECUTIVO  = cd.CONSECUTIVO
                JOIN FACTOR_CANCELACION fc ON  fc.COD_FACTOR = cc.COD_FACTOR
                WHERE c.ANO  = $anio AND c.SEMESTRE= $semestre AND c.COD_CARRERA in $carrera  
                AND c.ESTADO = '$estado' AND cc.COD_FACTOR =$factor
                GROUP BY c.ANO, c.SEMESTRE, cc.COD_FACTOR ,fc.DESCRIPCION , cc.COD_CARACT , cc.CARACTERISTICA , cc.DESCRIPCION 
                ORDER BY c.ANO, c.SEMESTRE ,cc.COD_FACTOR , cc.COD_CARACT ";
        //c.ESTADO = '$estado'
     
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function caracteristica_conteo_general($carrera, $anio, $semestre, $estado)
    {
        $sql = "SELECT c.ANO, c.SEMESTRE,  cc.COD_FACTOR, fc.DESCRIPCION AS fact_descripcion,  cc.COD_CARACT ,
                cc.CARACTERISTICA , cc.DESCRIPCION AS caract_descripcion, count(*) FROM CARACTERISTICA_CANCELACION cc
                JOIN CANCELACIONE_DETALLE cd ON cd.FACTOR = cc.COD_FACTOR  AND cd.CARACTERISTICA = cc.COD_CARACT 
                JOIN cancelacione c ON  c.CONSECUTIVO  = cd.CONSECUTIVO
                JOIN FACTOR_CANCELACION fc ON  fc.COD_FACTOR = cc.COD_FACTOR
                WHERE c.ANO  = $anio AND c.SEMESTRE= $semestre AND c.COD_CARRERA in $carrera AND c.ESTADO = '$estado'
                GROUP BY c.ANO, c.SEMESTRE, cc.COD_FACTOR ,fc.DESCRIPCION , cc.COD_CARACT , cc.CARACTERISTICA , cc.DESCRIPCION 
                ORDER BY c.ANO, c.SEMESTRE ,cc.COD_FACTOR , cc.COD_CARACT";
        //c.ESTADO = '$estado'
      
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function factores_conteo_materia($carrera, $anio, $semestre, $estado, $materia)
    {
        $sql = "SELECT c.ANO, c.SEMESTRE, fc.COD_FACTOR , fc.DESCRIPCION , count(*) FROM FACTOR_CANCELACION fc  
                JOIN CANCELACIONE_DETALLE cd ON cd.FACTOR = fc.COD_FACTOR
                JOIN cancelacione c ON  c.CONSECUTIVO  = cd.CONSECUTIVO
                WHERE c.ANO  = $anio AND c.SEMESTRE= $semestre AND c.COD_CARRERA in $carrera AND c.ESTADO = '$estado'
                AND cd.COD_MATERIA = $materia
                GROUP BY  c.ANO, c.SEMESTRE, fc.COD_FACTOR , fc.DESCRIPCION
                ORDER BY c.ANO , c.SEMESTRE , fc.COD_FACTOR";
        //c.ESTADO = '$estado'
       
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function caracteristica_conteo_materia($carrera, $anio, $semestre, $estado, $materia)
    {
        $sql = "SELECT c.ANO, c.SEMESTRE,  cc.COD_FACTOR, fc.DESCRIPCION AS fact_descripcion,  cc.COD_CARACT ,
                cc.CARACTERISTICA , cc.DESCRIPCION AS caract_descripcion, count(*) FROM CARACTERISTICA_CANCELACION cc
                JOIN CANCELACIONE_DETALLE cd ON cd.FACTOR = cc.COD_FACTOR  AND cd.CARACTERISTICA = cc.COD_CARACT 
                JOIN cancelacione c ON  c.CONSECUTIVO  = cd.CONSECUTIVO
                JOIN FACTOR_CANCELACION fc ON  fc.COD_FACTOR = cc.COD_FACTOR
                WHERE c.ANO  = $anio AND c.SEMESTRE= $semestre AND c.COD_CARRERA in $carrera AND c.ESTADO = '$estado'
                AND cd.COD_MATERIA = $materia
                GROUP BY c.ANO, c.SEMESTRE, cc.COD_FACTOR ,fc.DESCRIPCION , cc.COD_CARACT , cc.CARACTERISTICA , cc.DESCRIPCION 
                ORDER BY c.ANO, c.SEMESTRE ,cc.COD_FACTOR , cc.COD_CARACT";
        //c.ESTADO = '$estado'
      
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function cancelacione_estadistica($carrera, $estado)
    {
        $sql = "SELECT c.ANO, c.SEMESTRE ,c.ESTADO , cd.TIPO , c.COD_CARRERA, c.COD_ALUMNO , cd.COD_MATERIA , 
        cd.COD_CAR_MAT||cd.COD_MAT_MAT||'-'||cd.GRUPO,
cd.FACTOR , fc.DESCRIPCION , cd.CARACTERISTICA as cod_caract, cc.CARACTERISTICA , cd.OTRA
FROM cancelacione c
JOIN CANCELACIONE_DETALLE cd ON cd.CONSECUTIVO = c.CONSECUTIVO 
JOIN FACTOR_CANCELACION fc ON  fc.COD_FACTOR = cd.FACTOR
JOIN CARACTERISTICA_CANCELACION  cc ON  cc.COD_FACTOR = cd.FACTOR AND cc.COD_CARACT = cd.CARACTERISTICA 
WHERE c.COD_CARRERA = $carrera
AND  c.ESTADO = '$estado'";


        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


    public function get_cancelacion_ordinaria_alumno($codigo, $carrera, $semestre, $anio)
    {


        $sql = "SELECT 
                    dp.NOMBRES,
                    a.DOCUMENTO ,
                    a.COD_ALUMNO ,
                    a.COD_CARRERA ,
                    NULL AS PER_ACA_RETIRO
                FROM REGISTRO_SEMESTRE rs
                JOIN ALUMNO a 
                    ON a.COD_CARRERA = rs.COD_CARRERA 
                    AND a.COD_ALUMNO = rs.COD_ALUMNO
                LEFT JOIN EXALUMNO e ON
                    a.DOCUMENTO = e.DOCUMENTO
                    AND a.COD_CARRERA = e.COD_CARRERA
                    AND a.COD_ALUMNO = e.COD_ALUMNO
                    AND e.TIPO_EXALUMNO = 'G'
                JOIN DATOS_PER dp 
                    ON dp.DOCUMENTO = a.DOCUMENTO
                WHERE 
                    NOT (a.MATRICULADO IN('X', 'G')
                        AND e.COD_ALUMNO IS NOT NULL)
                    AND rs.VALOR < 0 -- solo cancelaciones
                    AND (
        (rs.FECHA BETWEEN TO_DATE('2021-03-19', 'YYYY-MM-DD') AND TO_DATE('2021-03-26', 'YYYY-MM-DD'))
		OR
        (rs.FECHA BETWEEN TO_DATE('2021-09-20', 'YYYY-MM-DD') AND TO_DATE('2021-10-01', 'YYYY-MM-DD'))
			OR
        (rs.FECHA BETWEEN TO_DATE('2022-03-24', 'YYYY-MM-DD') AND TO_DATE('2022-04-01', 'YYYY-MM-DD'))
				OR
        (rs.FECHA BETWEEN TO_DATE('2022-09-05', 'YYYY-MM-DD') AND TO_DATE('2022-09-30', 'YYYY-MM-DD'))
					OR
        (rs.FECHA BETWEEN TO_DATE('2023-03-13', 'YYYY-MM-DD') AND TO_DATE('2023-03-31', 'YYYY-MM-DD'))
						OR
        (rs.FECHA BETWEEN TO_DATE('2023-09-04', 'YYYY-MM-DD') AND TO_DATE('2023-09-20', 'YYYY-MM-DD'))
							OR
        (rs.FECHA BETWEEN TO_DATE('2024-03-11', 'YYYY-MM-DD') AND TO_DATE('2024-04-05', 'YYYY-MM-DD'))
								OR
        (rs.FECHA BETWEEN TO_DATE('2024-09-02', 'YYYY-MM-DD') AND TO_DATE('2024-09-20', 'YYYY-MM-DD'))
									OR
        (rs.FECHA BETWEEN TO_DATE('2025-02-24', 'YYYY-MM-DD') AND TO_DATE('2025-03-21', 'YYYY-MM-DD'))
            ) 
            AND EXTRACT(YEAR FROM rs.FECHA) = $anio
            AND CASE
                WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN '1'
                ELSE '2'
            END = $semestre
                    AND rs.COD_MATERIA = '$codigo'
                    AND rs.COD_CARRERA = '$carrera'
                ORDER BY rs.FECHA";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_cancelacion_ordinaria_alumno_graduado($codigo, $carrera, $semestre, $anio)
    {



        $sql = "SELECT 
                    de.NOMBRES,
                    e.DOCUMENTO,
                    e.COD_ALUMNO,
                    e.COD_CARRERA ,
                    SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, -1) AS PER_ACA_RETIRO
                FROM REGISTRO_SEMESTRE rs
                JOIN EXALUMNO e 
                    ON e.COD_CARRERA = rs.COD_CARRERA 
                    AND e.COD_ALUMNO = rs.COD_ALUMNO
                JOIN DATOS_EXA de 
                    ON de.DOCUMENTO = e.DOCUMENTO
                WHERE 
                    rs.VALOR < 0 -- solo cancelaciones
                    AND (
        (rs.FECHA BETWEEN TO_DATE('2021-03-19', 'YYYY-MM-DD') AND TO_DATE('2021-03-26', 'YYYY-MM-DD'))
		OR
        (rs.FECHA BETWEEN TO_DATE('2021-09-20', 'YYYY-MM-DD') AND TO_DATE('2021-10-01', 'YYYY-MM-DD'))
			OR
        (rs.FECHA BETWEEN TO_DATE('2022-03-24', 'YYYY-MM-DD') AND TO_DATE('2022-04-01', 'YYYY-MM-DD'))
				OR
        (rs.FECHA BETWEEN TO_DATE('2022-09-05', 'YYYY-MM-DD') AND TO_DATE('2022-09-30', 'YYYY-MM-DD'))
					OR
        (rs.FECHA BETWEEN TO_DATE('2023-03-13', 'YYYY-MM-DD') AND TO_DATE('2023-03-31', 'YYYY-MM-DD'))
						OR
        (rs.FECHA BETWEEN TO_DATE('2023-09-04', 'YYYY-MM-DD') AND TO_DATE('2023-09-20', 'YYYY-MM-DD'))
							OR
        (rs.FECHA BETWEEN TO_DATE('2024-03-11', 'YYYY-MM-DD') AND TO_DATE('2024-04-05', 'YYYY-MM-DD'))
								OR
        (rs.FECHA BETWEEN TO_DATE('2024-09-02', 'YYYY-MM-DD') AND TO_DATE('2024-09-20', 'YYYY-MM-DD'))
									OR
        (rs.FECHA BETWEEN TO_DATE('2025-02-24', 'YYYY-MM-DD') AND TO_DATE('2025-03-21', 'YYYY-MM-DD'))
            ) 
            AND EXTRACT(YEAR FROM rs.FECHA) = $anio
            AND CASE
                WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN '1'
                ELSE '2'
            END = $semestre
                    AND rs.COD_MATERIA = '$codigo'
                    AND rs.COD_CARRERA = '$carrera'
                ORDER BY rs.FECHA";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


    public function get_cancelacion_alumno($codigo, $carrera, $semestre, $ano)
    {
        $sql = "SELECT 
                    dp.NOMBRES,
                    a.DOCUMENTO ,
                    a.COD_ALUMNO ,
                    a.COD_CARRERA ,
                    NULL AS PER_ACA_RETIRO
            FROM CANCELACIONE_DETALLE cd 
            JOIN CANCELACIONE c oN c.CONSECUTIVO = cd.CONSECUTIVO 
            JOIN ALUMNO a ON a.COD_CARRERA = c.COD_CARRERA AND a.COD_ALUMNO =c.COD_ALUMNO 
            JOIN DATOS_PER dp ON dp.DOCUMENTO = a.DOCUMENTO 
            WHERE c.ESTADO = 'R' AND ANO = '$ano' AND c.SEMESTRE = '$semestre'
            AND cd.COD_MATERIA = '$codigo' AND c.COD_CARRERA  = '$carrera'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_cancelacion_alumno_graduado($codigo, $carrera, $semestre, $ano)
    {
        $sql = "SELECT 
                    SUBSTR(ex.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(ex.PER_ACA_RETIRO, -1) AS PER_ACA_RETIRO,
                    de.NOMBRES,
                    ex.DOCUMENTO,
                    ex.COD_ALUMNO,
                    ex.COD_CARRERA
            FROM CANCELACIONE_DETALLE cd
            JOIN CANCELACIONE c ON c.CONSECUTIVO = cd.CONSECUTIVO
            JOIN EXALUMNO ex ON ex.COD_CARRERA = c.COD_CARRERA AND ex.COD_ALUMNO = c.COD_ALUMNO
            JOIN DATOS_EXA de ON de.DOCUMENTO = ex.DOCUMENTO
            WHERE c.ESTADO = 'R' AND c.ANO = '$ano' AND c.SEMESTRE = '$semestre'
            AND cd.COD_MATERIA = '$codigo' AND c.COD_CARRERA  = '$carrera'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


    public function get_carrera($codigo)
    {
        $sql = "SELECT c.* FROM CANCELACIONE c 
       WHERE c.COD_CARRERA = '$codigo'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_semestre($carrera, $anio, $semestre)
    {
        $sql = "SELECT c.* , cd.COD_CAR_MAT as codigo_carrerra_donde_curo ,
        cd.COD_MAT_MAT as codigo_materia_en_carrera, cd.GRUPO, cd.TIPO 
        FROM CANCELACIONE c 
        JOIN CANCELACION_DETALLE cd oN c.CONSECUTIVO = cd.CONSECUTIVO
        WHERE c.ANO = '$anio' AND c.SEMESTRE = '$semestre'
        AND c.CARRERA = '$carrera'";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_materia($codigo, $carrera)
    {
        $sql = "SELECT c.* , cd.COD_CAR_MAT as codigo_carrerra_donde_curo ,
        cd.COD_MAT_MAT as codigo_materia_en_carrera, cd.GRUPO, cd.TIPO 
        FROM CANCELACIONE c 
        JOIN CANCELACION_DETALLE cd oN c.CONSECUTIVO = cd.CONSECUTIVO
        WHERE cd.COD_MATERIA = '$codigo' AND c.CARRERA = '$carrera'";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_materia_semestre($codigo, $carrera, $anio, $semestre)
    {
        $sql = "SELECT c.* , cd.COD_CAR_MAT as codigo_carrerra_donde_curo ,
        cd.COD_MAT_MAT as codigo_materia_en_carrera, cd.GRUPO, cd.TIPO FROM CANCELACIONE c 
        JOIN CANCELACION_DETALLE cd oN c.CONSECUTIVO = cd.CONSECUTIVO
        WHERE c.ANO = '$anio' AND c.SEMESTRE = '$semestre'
        AND cd.COD_MATERIA='$codigo' AND c.CARRERA = '$carrera'";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_alumno($codigo, $carrera)
    {
        $sql = "SELECT c.* , cd.COD_CAR_MAT as codigo_carrerra_donde_curo ,
        cd.COD_MAT_MAT as codigo_materia_en_carrera, cd.GRUPO, cd.TIPO 
        FROM CANCELACIONE c 
        JOIN CANCELACION_DETALLE cd oN c.CONSECUTIVO = cd.CONSECUTIVO
        WHERE c.COD_ALUMNO = '$codigo' AND c.COD_CARRERA = '$carrera'";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_alumno_semestre($codigo, $carrera, $anio, $semestre)
    {
        $sql = "SELECT c.* , cd.COD_CAR_MAT as codigo_carrerra_donde_curo ,
        cd.COD_MAT_MAT as codigo_materia_en_carrera, cd.GRUPO, cd.TIPO FROM CANCELACIONE c 
        JOIN CANCELACION_DETALLE cd oN c.CONSECUTIVO = cd.CONSECUTIVO
        WHERE c.ANO = '$anio' AND c.SEMESTRE = '$semestre'
        AND c.COD_ALUMNO = '$codigo' AND c.CARRERA = '$carrera'";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }
}
