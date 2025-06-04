<?php
class Alumno_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }


    public function get_activos($carrera)
    {

        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO, a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                c.NOMBRE AS NOMBRE_CARRERA, 
                dp.NOMBRES, dp.SEXO, 
                m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                m.ACTIVO AS MATRICULA_ACTIVO, 
                m.MATRICULADO AS MATRICULA_MATRICULADO
        FROM ALUMNO a
        JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
        JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
        JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
        WHERE a.COD_CARRERA in $carrera
        AND a.MATRICULADO  IN ('I','S','R','P','M')
        ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    //cantidad de estudiantes que pasaron a ser inactivos para un x semestre
    //toma los activos del semestre anterior y lista los que pasaron a ser inactivos
    public function inactivos_semestre($carrera, $semestre)
    {

        $sql = "WITH ultimo_sem AS (
                SELECT
                    ma.COD_ALUMNO,
                    ma.COD_CARRERA,
                    ma.ANO,
                    ma.SEMESTRE
                FROM
                    (
                    SELECT
                        COD_ALUMNO,
                        COD_CARRERA,
                        ANO,
                        SEMESTRE,
                        ROW_NUMBER() OVER (
                            PARTITION BY COD_ALUMNO,COD_CARRERA
                    ORDER BY
                        ANO DESC,
                        SEMESTRE DESC,
                        ROWID
                        ) AS rn
                    FROM
                        MATRICULA_ALUMNO
                ) ma
                WHERE
                    ma.rn = 1
            ),  cance AS  (
            SELECT
                a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO,
                       ma.ANO || '-' || ma.SEMESTRE AS semestre,
                       TRUNC(MONTHS_BETWEEN(
                            CASE 
                            WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                            WHEN ma.SEMESTRE = 2 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                            END,
                            dp.FECHA_NACIMIENTO
                        ) / 12) AS edad_semestre,
                        TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_actual
            FROM
                MATRICULA_ALUMNO ma
                JOIN ALUMNO a ON a.COD_CARRERA = ma.COD_CARRERA AND a.COD_ALUMNO = ma.COD_ALUMNO
                 JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
            WHERE
                ma.COD_CARRERA in $carrera
                AND ma.MATRICULADO IN ('C')
                AND ma.ANO || '-' || ma.SEMESTRE  = '$semestre'
            ), inact AS (
            SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                                a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                                c.NOMBRE AS NOMBRE_CARRERA, 
                                dp.NOMBRES, dp.SEXO, 
                                m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                                m.ACTIVO AS MATRICULA_ACTIVO, 
                                m.MATRICULADO AS MATRICULA_MATRICULADO,
                CASE
                    WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                    WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                END AS semestre,
                TRUNC(MONTHS_BETWEEN(
                            CASE 
                            WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                            WHEN ma.SEMESTRE = 2 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                            END,
                            dp.FECHA_NACIMIENTO
                        ) / 12) AS edad_semestre,
                        TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_actual
            FROM
                ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                            JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                            JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
            LEFT JOIN EXALUMNO e ON
                a.DOCUMENTO = e.DOCUMENTO
                AND a.COD_CARRERA = e.COD_CARRERA
                AND a.COD_ALUMNO = e.COD_ALUMNO
                AND e.TIPO_EXALUMNO = 'G'
            JOIN ultimo_sem ma ON
                ma.COD_ALUMNO = a.COD_ALUMNO
                AND ma.COD_CARRERA = a.COD_CARRERA
            WHERE
                a.MATRICULADO IN ('X', 'Y', 'L', 'A') 
                AND NOT (a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                    AND e.COD_ALUMNO IS NOT NULL)
                AND a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                AND a.COD_CARRERA in $carrera
                AND CASE
                    WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                    WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                END = '$semestre'
            ORDER BY
                semestre DESC
                )
            SELECT * FROM inact
            UNION ALL
            SELECT * FROM cance";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    //estudiantes que estuvieron activos en un semestre especifico
    public function activos_semestre($carrera, $semestre)
    {
        $sql = "WITH activos AS  (
            SELECT
                ma.ANO || '-' || ma.SEMESTRE AS semestre,
                a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO,
                       TRUNC(MONTHS_BETWEEN(
                            CASE 
                            WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                            WHEN ma.SEMESTRE = 2 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                            END,
                            dp.FECHA_NACIMIENTO
                        ) / 12) AS edad_semestre,
                        TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_actual
            FROM
                MATRICULA_ALUMNO ma
                JOIN ALUMNO a ON a.COD_CARRERA = ma.COD_CARRERA AND a.COD_ALUMNO = ma.COD_ALUMNO
                 JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
            WHERE
                ma.COD_CARRERA in $carrera
                AND ma.MATRICULADO IN ('I', 'S', 'R', 'P', 'M')
                AND ma.ANO || '-' || ma.SEMESTRE  = '$semestre'
            ), actual AS (
            SELECT
                TO_CHAR(SYSDATE, 'YYYY') || '-' ||
                CASE
                    WHEN EXTRACT(MONTH FROM SYSDATE) < 8 THEN '1'
                    ELSE '2'
                END AS semestre,
                a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO,
                       TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_semestre,
                        TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_actual
            FROM
                alumno a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
            WHERE
                a.MATRICULADO IN ('I', 'S', 'R', 'P', 'M')
                    AND a.COD_CARRERA in $carrera
                    AND TO_CHAR(SYSDATE, 'YYYY') || '-' ||
                CASE
                    WHEN EXTRACT(MONTH FROM SYSDATE) < 8 THEN '1'
                   ELSE '2' END  = '$semestre'
            ) 
         SELECT * FROM activos
UNION ALL
SELECT * FROM actual";


        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_activos_por_semestre_rango($anio_inicio, $semestre_inicio, $anio_fin, $semestre_fin, $carrera)
    {
        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO
                FROM ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
                WHERE (EXTRACT(YEAR FROM a.FEC_INGRESO) < '$anio_fin' OR 
                      (EXTRACT(YEAR FROM a.FEC_INGRESO) = '$anio_fin' AND a.SEM_INGRESO <= '$semestre_fin'))
                AND (EXTRACT(YEAR FROM a.FEC_INGRESO)> '$anio_inicio' OR 
                      (EXTRACT(YEAR FROM a.FEC_INGRESO)= '$anio_inicio' AND a.SEM_INGRESO >= '$semestre_inicio'))
                AND a.COD_CARRERA in $carrera
                AND a.MATRICULADO IN ('I','S','R','P','M')
                ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_activos_desde_semestre($anio, $semestre, $carrera)
    {
        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO, 
                       a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO
                FROM ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
                WHERE EXTRACT(YEAR FROM a.FEC_INGRESO) >= '$anio' 
                AND a.SEM_INGRESO = '$semestre'
                AND a.COD_CARRERA in $carrera
                AND a.MATRICULADO IN ('I','S','R','P', 'M')
                ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_activos_por_semestre($anio, $semestre, $carrera)
    {
        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO
                FROM ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
                WHERE  EXTRACT(YEAR FROM a.FEC_INGRESO) = '$anio' 
                AND a.SEM_INGRESO = '$semestre'
                AND a.COD_CARRERA in $carrera
                AND a.MATRICULADO IN ('I','S','R','P', 'M')
                ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_lista($carrera)
    {

        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO, a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                c.NOMBRE AS NOMBRE_CARRERA, 
                dp.NOMBRES, dp.SEXO, 
                m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                m.ACTIVO AS MATRICULA_ACTIVO, 
                m.MATRICULADO AS MATRICULA_MATRICULADO
        FROM ALUMNO a
        JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
        JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
        JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
        LEFT JOIN EXALUMNO e ON a.DOCUMENTO = e.DOCUMENTO 
                            AND a.COD_CARRERA = e.COD_CARRERA 
                            AND a.COD_ALUMNO = e.COD_ALUMNO
                            AND e.TIPO_EXALUMNO = 'G'
        WHERE a.COD_CARRERA = '$carrera'
        AND a.MATRICULADO IN ('X', 'C')
        AND NOT (a.MATRICULADO = 'X' AND e.COD_ALUMNO IS NOT NULL)
        ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_cantidad_estados($carrera)
    {

        $sql =   "SELECT 
                    EXTRACT(YEAR FROM a.FEC_INGRESO) AS ANO,
                    a.SEM_INGRESO,
                    COUNT(*) AS total,
                    COUNT(CASE 
                            WHEN a.MATRICULADO IN ('X', 'Y', 'L', 'A') THEN 1 
                        END) AS cantidad_x,
                    NVL(g.cantidad_graduados, 0) AS cantidad_g,
                    COUNT(CASE 
                            WHEN a.MATRICULADO IN ('I','S','R','P','M') THEN 1 
                        END) AS activo
                FROM ALUMNO a
                LEFT JOIN EXALUMNO e ON
	a.DOCUMENTO = e.DOCUMENTO
	AND a.COD_CARRERA = e.COD_CARRERA
	AND a.COD_ALUMNO = e.COD_ALUMNO
	AND e.TIPO_EXALUMNO = 'G'
                LEFT JOIN (
                    SELECT 
                        EXTRACT(YEAR FROM e.FEC_INGRESO) AS ANO_INGRESO,
                        CASE 
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (11, 12, 1, 2, 3) THEN 1
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (6, 7, 8, 9) THEN 2
                            ELSE NULL
                        END AS SEM_INGRESO,
                        e.COD_CARRERA,
                        COUNT(*) AS cantidad_graduados
                    FROM EXALUMNO e
                    WHERE e.TIPO_EXALUMNO = 'G'
                    AND e.COD_CARRERA in $carrera
                    GROUP BY 
                        EXTRACT(YEAR FROM e.FEC_INGRESO),
                        CASE 
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (11, 12, 1, 2, 3) THEN 1
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (6, 7, 8, 9) THEN 2
                            ELSE NULL
                        END,
                        e.COD_CARRERA
                ) g ON g.ANO_INGRESO = EXTRACT(YEAR FROM a.FEC_INGRESO)
                    AND g.SEM_INGRESO = a.SEM_INGRESO
                    AND g.COD_CARRERA = a.COD_CARRERA
                WHERE a.COD_CARRERA in $carrera
                AND NOT (a.MATRICULADO IN ('X', 'Y', 'L', 'A')
		AND e.COD_ALUMNO IS NOT NULL)
                GROUP BY EXTRACT(YEAR FROM a.FEC_INGRESO), a.SEM_INGRESO, g.cantidad_graduados
                ORDER BY ANO DESC, a.SEM_INGRESO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_sem_activos($carrera)
    {
        $sql = "SELECT TO_CHAR(FEC_INGRESO, 'YYYY') AS ANO 
                FROM ALUMNO a 
                WHERE COD_CARRERA in $carrera
                GROUP BY TO_CHAR(FEC_INGRESO, 'YYYY')
                ORDER BY ano DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_primer_ingreso($carrera)
    {
        $sql = "SELECT 
                    TO_CHAR(FEC_INGRESO, 'YYYY') AS ANO,
                    SEM_INGRESO
                FROM ALUMNO
                WHERE FEC_INGRESO = (
                    SELECT MIN(FEC_INGRESO)
                    FROM ALUMNO
                    WHERE COD_CARRERA in $carrera
                )
                AND COD_CARRERA in $carrera
                AND ROWNUM = 1";

        $arr = array();
        $this->database2->get_sql_object($sql, $arr);
        return $arr;
    }

    public function get_por_semestre_rango($anio_inicio, $semestre_inicio, $anio_fin, $semestre_fin, $carrera)
    {
        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO
                FROM ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
                LEFT JOIN EXALUMNO e ON a.DOCUMENTO = e.DOCUMENTO 
                            AND a.COD_CARRERA = e.COD_CARRERA 
                            AND a.COD_ALUMNO = e.COD_ALUMNO
                            AND e.TIPO_EXALUMNO = 'G'
                WHERE (EXTRACT(YEAR FROM a.FEC_INGRESO) < '$anio_fin' OR 
                      (EXTRACT(YEAR FROM a.FEC_INGRESO) = '$anio_fin' AND a.SEM_INGRESO <= '$semestre_fin'))
                AND (EXTRACT(YEAR FROM a.FEC_INGRESO)> '$anio_inicio' OR 
                      (EXTRACT(YEAR FROM a.FEC_INGRESO)= '$anio_inicio' AND a.SEM_INGRESO >= '$semestre_inicio'))
                AND a.COD_CARRERA in $carrera
                AND a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                AND NOT (a.MATRICULADO = 'X' AND e.COD_ALUMNO IS NOT NULL)
                ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_desde_semestre($anio, $semestre, $carrera)
    {
        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO, 
                       a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO
                FROM ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
                LEFT JOIN EXALUMNO e ON a.DOCUMENTO = e.DOCUMENTO 
                            AND a.COD_CARRERA = e.COD_CARRERA 
                            AND a.COD_ALUMNO = e.COD_ALUMNO
                            AND e.TIPO_EXALUMNO = 'G'
                WHERE EXTRACT(YEAR FROM a.FEC_INGRESO) >= '$anio' 
                AND a.SEM_INGRESO = '$semestre'
                AND a.COD_CARRERA in $carrera
                AND a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                AND NOT (a.MATRICULADO = 'X' AND e.COD_ALUMNO IS NOT NULL)
                ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_por_semestre($anio, $semestre, $carrera)
    {
        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO
                FROM ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
                LEFT JOIN EXALUMNO e ON a.DOCUMENTO = e.DOCUMENTO 
                            AND a.COD_CARRERA = e.COD_CARRERA 
                            AND a.COD_ALUMNO = e.COD_ALUMNO
                            AND e.TIPO_EXALUMNO = 'G'
                WHERE  EXTRACT(YEAR FROM a.FEC_INGRESO) = '$anio' 
                AND a.SEM_INGRESO = '$semestre'
                AND a.COD_CARRERA in $carrera
                AND a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                AND NOT (a.MATRICULADO = 'X' AND e.COD_ALUMNO IS NOT NULL)
                ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


    public function get_terminados_por_semestre($anio, $semestre, $carrera)
    {
        $sql = "SELECT a.COD_ALUMNO, a.COD_CARRERA, a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO,  a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       c.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, dp.SEXO, 
                       m.DESCRIPCION AS MATRICULA_DESCRIPCION, 
                       m.ACTIVO AS MATRICULA_ACTIVO, 
                       m.MATRICULADO AS MATRICULA_MATRICULADO
                FROM ALUMNO a
                JOIN CARRERA c ON a.COD_CARRERA = c.COD_CARRERA
                JOIN DATOS_PER dp ON a.DOCUMENTO = dp.DOCUMENTO
                JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
                WHERE  EXTRACT(YEAR FROM a.FEC_INGRESO) = '$anio' 
                AND a.SEM_INGRESO = '$semestre'
                AND a.COD_CARRERA in $carrera
                AND a.MATRICULADO IN ('Y')
                ORDER BY a.ULT_ANO_MATRICULADO DESC, a.ULT_SEM_MATRICULADO DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }
}
