<?php
class Materia_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_codigo($codigo,$carrera)
    {
        $sql = "SELECT m.* FROM MATERIA m 
        WHERE  m.COD_MATERIA = '$codigo' AND m.COD_CARRERA = $carrera";

        $arr = array();
        $this->database2->get_sql_object($sql, $arr);
        return $arr;
    }

    public function get_carrera($codigo)
    {
        $sql = "SELECT m.* FROM MATERIA m 
        WHERE  m.COD_CARRERA = '$codigo'
        ORDER BY m.SEMESTRE ASC";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_carrera_electivas($codigo)
    {
        $sql = "SELECT m.SEMESTRE , m.*,
                CASE
                    WHEN pm.TIPO_ELECTIVA = 'S' THEN 1 
                    ELSE 0 
                END AS ELECTIVA 
                FROM MATERIA m 
                JOIN PENSUM_MATERIA pm ON pm.COD_CARRERA  = m.COD_CARRERA 
                                        AND pm.COD_MATERIA = m.COD_MATERIA
                WHERE  m.COD_CARRERA in $codigo
                and m.ACTIVA= 'S'
                ORDER BY TO_NUMBER(m.SEMESTRE) ASC , ELECTIVA ASC , m.COD_MATERIA ASC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;

    }

    public function get_carrera_semestre($codigo, $semestre)
    {
        $sql = "SELECT m.* FROM MATERIA m 
        WHERE  m.COD_CARRERA = '$codigo' 
        AND m.SEMESTRE = '$semestre'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_carrera_conteo($codigo)
    {
        $sql = "SELECT count(*) FROM MATERIA m 
        WHERE  m.COD_CARRERA = '$codigo'";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_carrera_semestre_conteo($codigo, $semestre)
    {
        $sql = "SELECT count(* FROM MATERIA m 
        WHERE  m.COD_CARRERA = '$codigo' 
        AND m.SEMESTRE = '$semestre'";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }




    public function get_conteo_por_semestre($semestre, $anio, $carrera)
    {

        $sql = "WITH impresion_act AS (
                SELECT
                    a.COD_CARRERA,
                    a.COD_ALUMNO,
                    a.SEMESTRE,
                    a.ANO,
                    a.COD_MATERIA,
                    a.TIPO_NOTA,
                    a.DEFINITIVA,
                    a.COD_CAR_MAT,
                    a.COD_MAT_MAT,
                    a.RESOLUCION,
                    a.CREDITOS,
                    b.definitiva HABILITACION,
                    M.NOMBRE
                FROM
                    materia m,
                    (
                    SELECT
                        *
                    FROM
                        nota
                    WHERE
                        tipo_nota <> 'V') a,
                    (
                    SELECT
                        *
                    FROM
                        nota
                    WHERE
                        tipo_nota = 'H') b
                WHERE
                    m.cod_carrera = a.cod_carrera
                    AND m.cod_materia = a.cod_materia
                    AND a.cod_carrera = b.cod_carrera(+)
                    AND a.cod_materia = b.cod_materia(+)
                    AND a.cod_alumno = b.cod_alumno(+)
                    AND a.ano = b.ano(+)
                    AND a.semestre = b.semestre(+)
                    AND a.tipo_nota <> 'Y'
                    AND a.tipo_nota <> 'H'
                    AND ((b.definitiva IS NULL
                        AND a.tipo_nota <> 'V'
                        AND b.tipo_nota IS NULL)
                    OR (a.tipo_nota = 'D'
                        AND b.tipo_nota = 'H'))
                UNION ALL
                SELECT
                    a.COD_CARRERA,
                    a.COD_ALUMNO,
                    a.SEMESTRE,
                    a.ANO,
                    a.COD_MATERIA,
                    a.TIPO_NOTA,
                    a.DEFINITIVA,
                    a.COD_CAR_MAT,
                    a.COD_MAT_MAT,
                    a.RESOLUCION,
                    a.CREDITOS,
                    b.definitiva HABILITACION,
                    M.NOMBRE
                FROM
                    materia m,
                    nota a,
                    (
                    SELECT
                        *
                    FROM
                        nota
                    WHERE
                        tipo_nota = 'Y') b
                WHERE
                    m.cod_carrera = a.cod_carrera
                    AND m.cod_materia = a.cod_materia
                    AND a.cod_carrera = b.cod_carrera(+)
                    AND a.cod_materia = b.cod_materia(+)
                    AND a.cod_alumno = b.cod_alumno(+)
                    AND a.ano = b.ano(+)
                    AND a.semestre = b.semestre(+)
                    AND a.tipo_nota <> 'Y'
                    AND a.tipo_nota <> 'H'
                    AND ((b.definitiva IS NULL
                        AND a.tipo_nota <> 'D'
                        AND b.tipo_nota IS NOT NULL)
                    OR (a.tipo_nota = 'V'
                        AND b.tipo_nota = 'Y'))
                ), notas_impresion_grads AS (
                SELECT
                    a.COD_CARRERA,
                    a.COD_ALUMNO,
                    a.SEMESTRE,
                    a.ANO,
                    a.COD_MATERIA,
                    a.TIPO_NOTA,
                    a.DEFINITIVA,
                    a.COD_CAR_MAT,
                    a.COD_MAT_MAT,
                    a.RESOLUCION,
                    a.CREDITOS,
                    b.definitiva HABILITACION,
                    M.NOMBRE,
                    a.DOCUMENTO
                FROM
                    materia m,
                    (
                    SELECT
                        *
                    FROM
                        notas_exa
                    WHERE
                        tipo_nota <> 'V') a,
                    (
                    SELECT
                        *
                    FROM
                        notas_exa
                    WHERE
                        tipo_nota = 'H') b
                WHERE
                    m.cod_carrera = a.cod_carrera
                    AND m.cod_materia = a.cod_materia
                    AND
                a.cod_carrera = b.cod_carrera(+)
                    AND
                a.cod_materia = b.cod_materia(+)
                    AND
                a.cod_alumno = b.cod_alumno(+)
                    AND
                a.ano = b.ano(+)
                    AND
                a.semestre = b.semestre(+)
                    AND
                a.tipo_nota <> 'Y'
                    AND a.tipo_nota <> 'H'
                    AND
                ((b.definitiva IS NULL
                        AND a.tipo_nota <> 'V'
                        AND b.tipo_nota IS NULL)
                    OR (a.tipo_nota = 'D'
                        AND b.tipo_nota = 'H'))
                UNION ALL
                SELECT
                    a.COD_CARRERA,
                    a.COD_ALUMNO,
                    a.SEMESTRE,
                    a.ANO,
                    a.COD_MATERIA,
                    a.TIPO_NOTA,
                    a.DEFINITIVA,
                    a.COD_CAR_MAT,
                    a.COD_MAT_MAT,
                    a.RESOLUCION,
                    a.CREDITOS,
                    b.definitiva HABILITACION,
                    M.NOMBRE,
                    a.DOCUMENTO
                FROM
                    materia m,
                    notas_exa a,
                    (
                    SELECT
                        *
                    FROM
                        notas_exa
                    WHERE
                        tipo_nota = 'Y') b
                WHERE
                    m.cod_carrera = a.cod_carrera
                    AND m.cod_materia = a.cod_materia
                    AND a.cod_carrera = b.cod_carrera(+)
                    AND a.cod_materia = b.cod_materia(+)
                    AND a.cod_alumno = b.cod_alumno(+)
                    AND a.ano = b.ano(+)
                    AND a.semestre = b.semestre(+)
                    AND a.tipo_nota <> 'Y'
                    AND a.tipo_nota <> 'H'
                    AND ((b.definitiva IS NULL
                        AND a.tipo_nota <> 'D'
                        AND b.tipo_nota IS NOT NULL)
                    OR (a.tipo_nota = 'V'
                        AND b.tipo_nota = 'Y'))
                ),
        cancelaciones_ordinarias AS (
                    SELECT
	rs.COD_CARRERA,
	rs.COD_MATERIA,
	EXTRACT(YEAR FROM rs.FECHA) AS anio,
	CASE
		WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN '1'
		ELSE '2'
	END AS SEMESTRE,
	COUNT(*) AS cancelaciones_ordinarias
FROM
	REGISTRO_SEMESTRE rs
JOIN MATERIA m ON
	m.COD_CARRERA = rs.COD_CARRERA
	AND m.COD_MATERIA = rs.COD_MATERIA
WHERE
	rs.VALOR < 0
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
                        AND rs.COD_CARRERA in $carrera
                    GROUP BY rs.COD_CARRERA, rs.COD_MATERIA , EXTRACT(YEAR FROM rs.FECHA),
	CASE
		WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN '1'
		ELSE '2'
	END
                ),
                cancelaciones_extraordinarias AS (
                    SELECT 
                        c.COD_CARRERA,
                        cd.COD_MATERIA,
                        c.ANO AS anio,
                        c.SEMESTRE AS semestre,
                        COUNT(*) AS cancelaciones_extraordinarias
                    FROM CANCELACIONE_DETALLE cd
                    JOIN CANCELACIONE c ON c.CONSECUTIVO = cd.CONSECUTIVO
                    WHERE 
                        c.ESTADO = 'R' 
                        AND c.ANO = $anio
                        AND c.SEMESTRE = $semestre
                        AND c.COD_CARRERA in $carrera
                    GROUP BY c.COD_CARRERA, cd.COD_MATERIA, c.ANO, c.SEMESTRE
                ),
                notas_info AS (
                    SELECT 
                        n.COD_CARRERA,
                        n.COD_MATERIA,
                        n.ANO AS anio,
                        n.SEMESTRE AS semestre,
                        COUNT(*) AS total_notas,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) = 0 THEN 1 END) AS zero,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) < 3 THEN 1 END) AS reprobado,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) >= 3 THEN 1 END) AS aprobado,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) >= 3 AND NVL(n.HABILITACION, n.DEFINITIVA) < 4 THEN 1 END) AS entre_3_y_3_9,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) >= 4 THEN 1 END) AS entre_4_y_5
                    FROM impresion_act  n
                    WHERE 
                        n.ANO = $anio AND n.SEMESTRE = $semestre AND n.COD_CARRERA in $carrera
                    GROUP BY n.COD_CARRERA, n.COD_MATERIA, n.ANO, n.SEMESTRE
                ),
                notas_grad AS (
                    SELECT
                        ne.COD_CARRERA,
                        ne.COD_MATERIA,
                        ne.ANO AS ANO,
                        ne.SEMESTRE AS SEMESTRE,
                        COUNT(*) AS total_notas,
                        COUNT(CASE WHEN  NVL(ne.HABILITACION, ne.DEFINITIVA) = 0 THEN 1 END) AS zero,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) < 3 THEN 1 END) AS reprobado,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) >= 3 THEN 1 END) AS aprobado,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) >= 3 AND NVL(ne.HABILITACION, ne.DEFINITIVA) < 4 THEN 1 END) AS entre_3_y_3_9,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) >= 4 THEN 1 END) AS entre_4_y_5
                    FROM
                        notas_impresion_grads  ne
                    WHERE
                        ne.ANO = $anio
                        AND ne.SEMESTRE = $semestre
                        AND ne.COD_CARRERA in $carrera
                    GROUP BY
                        ne.COD_CARRERA,
                        ne.COD_MATERIA,
                        ne.ANO,
                        ne.SEMESTRE
                    )
                    SELECT 
                        COALESCE(n.COD_CARRERA, g.COD_CARRERA) as COD_CARRERA,
                        COALESCE(n.COD_MATERIA, g.COD_MATERIA) as COD_MATERIA,
                        m.NOMBRE AS NOMBRE_MATERIA,
                        m.CREDITOS,
                        CASE
                            WHEN TRIM(m.SEMESTRE) IN ('0', '00') THEN 0
                            ELSE TO_NUMBER(TRIM(LEADING '0' FROM m.SEMESTRE))
                        END AS semestre_materia,
                        COALESCE(o.cancelaciones_ordinarias, 0) AS cancelaciones_ordinarias,
                        COALESCE(c.cancelaciones_extraordinarias, 0) AS cancelaciones_extraordinarias,
                        COALESCE(n.total_notas, 0) + COALESCE(g.total_notas, 0) AS total_notas,
                        COALESCE(n.zero, 0) + COALESCE(g.zero, 0) AS zero,
                        COALESCE(n.reprobado , 0)+ COALESCE(g.reprobado , 0) AS reprobado,
                        COALESCE(n.aprobado , 0) + COALESCE(g.aprobado , 0) AS aprobado,
                        COALESCE(n.entre_3_y_3_9, 0)+ COALESCE(g.entre_3_y_3_9, 0) AS entre_3_y_3_9,
                        COALESCE(n.entre_4_y_5, 0) + COALESCE(g.entre_4_y_5, 0) AS entre_4_y_5
                    FROM
                        notas_info n
                    FULL OUTER JOIN notas_grad g
                                        ON
                        g.COD_CARRERA = n.COD_CARRERA
                        AND g.COD_MATERIA = n.COD_MATERIA
                    LEFT JOIN cancelaciones_ordinarias o 
                                        ON
                        o.COD_CARRERA = n.COD_CARRERA
                        AND o.COD_MATERIA = n.COD_MATERIA
                    LEFT JOIN cancelaciones_extraordinarias c 
                                        ON
                        c.COD_CARRERA = n.COD_CARRERA
                        AND c.COD_MATERIA = n.COD_MATERIA
                    JOIN MATERIA m 
                                        ON
                        n.COD_CARRERA = m.COD_CARRERA
                        AND n.COD_MATERIA = m.COD_MATERIA
                    ORDER BY
                        semestre_materia DESC,
                        total_notas DESC,
                        m.COD_MATERIA";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

}