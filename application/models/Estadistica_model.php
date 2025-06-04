<?php
class Estadistica_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }




    public function desercion_semestre($carrera)
    {
        $sql = "WITH ultimo_sem AS (
            (
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
                            PARTITION BY COD_ALUMNO,
                        COD_CARRERA
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
            )
            ),
            inactivos AS (
            SELECT
                CASE
                    WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                    WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                END AS semestre,
                count(*) AS total,
                COUNT(CASE 
                                        WHEN a.MATRICULADO IN ('X', 'Y', 'L', 'A') THEN 1 
                                    END) AS cantidad_x
            FROM
                ALUMNO a
            LEFT JOIN EXALUMNO e ON
                a.DOCUMENTO = e.DOCUMENTO
                AND a.COD_CARRERA = e.COD_CARRERA
                AND a.COD_ALUMNO = e.COD_ALUMNO
                AND e.TIPO_EXALUMNO = 'G'
            JOIN  ultimo_sem ma ON
                ma.COD_ALUMNO = a.COD_ALUMNO
                AND ma.COD_CARRERA = a.COD_CARRERA
            WHERE
                NOT (a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                    AND e.COD_ALUMNO IS NOT NULL)
                AND a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                AND a.COD_CARRERA in $carrera
            GROUP BY
                CASE
                    WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                    WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                END
            ORDER BY
                semestre DESC
            ),
            activos AS  (
            SELECT
                ma.ANO || '-' || ma.SEMESTRE AS semestre,
                count(*) AS total,
                COUNT(CASE 
                                        WHEN ma.MATRICULADO IN ('I', 'S', 'R', 'P', 'M') THEN 1 
                                    END) AS activos,
                COUNT(CASE 
                                        WHEN ma.MATRICULADO IN ('C') THEN 1 
                                    END) AS cantidad_c
            FROM
                MATRICULA_ALUMNO ma
            WHERE
                ma.COD_CARRERA in $carrera
            GROUP BY
                ma.ANO || '-' || ma.SEMESTRE
            ORDER BY
                semestre DESC
            ),
            graduados AS (
            SELECT
                SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, 5, 1) AS semestre,
                count(*) AS total,
                COUNT(CASE 
                                        WHEN e.TIPO_EXALUMNO IN ('G') THEN 1 
                                    END) AS cantidad_g
            FROM
                EXALUMNO e
            WHERE 
                e.COD_CARRERA in $carrera
                AND LENGTH(e.PER_ACA_RETIRO) >= 5
            GROUP BY
                SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, 5, 1)
            ORDER BY
                semestre DESC
            ),
            actual AS (
            SELECT
                TO_CHAR(SYSDATE, 'YYYY') || '-' ||
                CASE
                    WHEN EXTRACT(MONTH FROM SYSDATE) < 8 THEN '1'
                    ELSE '2'
                END AS semestre,
                COUNT(*) AS total
            FROM
                alumno a
            WHERE
                a.MATRICULADO IN ('I', 'S', 'R', 'P', 'M')
                    AND a.COD_CARRERA in $carrera
            )
            SELECT
                COALESCE(a.semestre, i.semestre, g.semestre, aa.semestre) AS semestre,
                COALESCE(i.total, 0)+ COALESCE(a.total, 0)+ COALESCE(g.total, 0) + COALESCE(aa.total, 0) AS total,
                COALESCE(i.cantidad_x, 0)+ COALESCE(a.cantidad_c, 0) AS inactivos,
                COALESCE(a.activos, 0) + COALESCE(aa.total, 0) AS activos,
                COALESCE(g.cantidad_g, 0) AS graduados,
                ROUND(
                ((COALESCE(a.activos, 0) + COALESCE(aa.total, 0) ) * 100.0) / 
                NULLIF(COALESCE(i.total, 0) + COALESCE(a.total, 0) + COALESCE(g.total, 0) + COALESCE(aa.total, 0), 0), 2
            ) AS porcentaje_activos,
                ROUND(
                ((COALESCE(i.cantidad_x, 0) + COALESCE(a.cantidad_c, 0)) * 100.0) /
                NULLIF(COALESCE(i.total, 0) + COALESCE(a.total, 0) + COALESCE(g.total, 0) + COALESCE(aa.total, 0), 0), 2
            ) AS porcentaje_inactivos,
                ROUND(
                (COALESCE(g.cantidad_g, 0) * 100.0) /
                NULLIF(COALESCE(i.total, 0) + COALESCE(a.total, 0) + COALESCE(g.total, 0) + COALESCE(aa.total, 0), 0), 2
            ) AS porcentaje_graduados
            FROM
                activos a
            FULL OUTER JOIN inactivos i ON
                a.semestre = i.semestre
            FULL OUTER JOIN graduados g ON
                COALESCE(a.semestre, i.semestre) = g.semestre
            FULL OUTER JOIN actual aa ON
                aa.semestre = COALESCE(a.semestre, i.semestre, g.semestre)
                order by semestre ASC";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


    public function desercion_semestre_ingreso($carrera)
    {

        $sql = "WITH graduados AS (
                    SELECT
                        EXTRACT(YEAR FROM e.FEC_INGRESO)|| '-' || CASE
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (11, 12, 1, 2, 3, 4) THEN 1
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (6, 7, 8, 9, 10) THEN 2
                            ELSE NULL
                        END AS semestre,
                        COUNT(*) AS cantidad_graduados
                    FROM
                        EXALUMNO e
                    WHERE
                        e.TIPO_EXALUMNO = 'G'
                        AND e.COD_CARRERA in $carrera
                    GROUP BY
                        EXTRACT(YEAR FROM e.FEC_INGRESO)|| '-' || CASE
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (11, 12, 1, 2, 3, 4) THEN 1
                            WHEN EXTRACT(MONTH FROM e.FEC_INGRESO) IN (6, 7, 8, 9, 10) THEN 2
                            ELSE NULL
                        END,
                        e.COD_CARRERA
                                    ),
                    alumnos AS (
                    SELECT
                        EXTRACT(YEAR FROM a.FEC_INGRESO)|| '-' || a.SEM_INGRESO AS semestre,
                        COUNT(*) AS total,
                        COUNT(CASE 
                                                WHEN a.MATRICULADO IN ('X', 'Y', 'L', 'A') THEN 1 
                                            END) AS cantidad_x,
                        COUNT(CASE 
                                                WHEN a.MATRICULADO IN ('I', 'S', 'P', 'M','R') THEN 1 
                                            END) AS activo
                    FROM
                        ALUMNO a
                    LEFT JOIN EXALUMNO e ON
                        a.DOCUMENTO = e.DOCUMENTO
                        AND a.COD_CARRERA = e.COD_CARRERA
                        AND a.COD_ALUMNO = e.COD_ALUMNO
                        AND e.TIPO_EXALUMNO = 'G'
                    WHERE
                        a.COD_CARRERA in $carrera
                        AND NOT (a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                            AND e.COD_ALUMNO IS NOT NULL)
                    GROUP BY
                        EXTRACT(YEAR FROM a.FEC_INGRESO)|| '-' || a.SEM_INGRESO
                    )
                    SELECT
                        COALESCE(a.semestre, g.semestre) AS semestre,
                        COALESCE(a.total, 0)+ COALESCE(g.cantidad_graduados, 0) AS total,
                        COALESCE(a.cantidad_x, 0 ) AS inactivos,
                        COALESCE(a.activo, 0 ) AS activos,
                        COALESCE(g.cantidad_graduados, 0) AS graduados,
                            ROUND(
                            CASE 
                                WHEN (COALESCE(a.total, 0) + COALESCE(g.cantidad_graduados, 0)) = 0 THEN 0
                                ELSE (COALESCE(a.activo, 0) * 100.0) / (COALESCE(a.total, 0) + COALESCE(g.cantidad_graduados, 0))
                            END, 
                            2
                        ) AS porcentaje_activos,
                        ROUND(
                            CASE 
                                WHEN (COALESCE(a.total, 0) + COALESCE(g.cantidad_graduados, 0)) = 0 THEN 0
                                ELSE (COALESCE(a.cantidad_x, 0) * 100.0) / (COALESCE(a.total, 0) + COALESCE(g.cantidad_graduados, 0))
                            END, 
                            2
                        ) AS porcentaje_inactivos,
                        ROUND(
                            CASE 
                                WHEN (COALESCE(a.total, 0) + COALESCE(g.cantidad_graduados, 0)) = 0 THEN 0
                                ELSE (COALESCE(g.cantidad_graduados, 0) * 100.0) / (COALESCE(a.total, 0) + COALESCE(g.cantidad_graduados, 0))
                            END, 
                            2
                        ) AS porcentaje_graduados
                    FROM
                        alumnos a
                    FULL OUTER JOIN graduados g ON
                        g.semestre = a.semestre
                    ORDER BY
                        semestre ASC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_inactivos_conteo_semestre_sexo($carrera)
    {

        $sql =  "WITH ultimo_sem AS (
                    (
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
                                    PARTITION BY COD_ALUMNO,
                                COD_CARRERA
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
                    )
                    ),
                    inactivos AS (
                    SELECT
                        CASE
                            WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                            WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                        END AS semestre,
                        count(*) AS total,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'F' THEN 1 
                                            END) AS cantidad_F,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'M' THEN 1 
                                            END) AS cantidad_M
                    FROM
                        ALUMNO a
                    JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
                    LEFT JOIN EXALUMNO e ON
                        a.DOCUMENTO = e.DOCUMENTO
                        AND a.COD_CARRERA = e.COD_CARRERA
                        AND a.COD_ALUMNO = e.COD_ALUMNO
                        AND e.TIPO_EXALUMNO = 'G'
                    JOIN ultimo_sem ma ON
                        ma.COD_ALUMNO = a.COD_ALUMNO
                        AND ma.COD_CARRERA = a.COD_CARRERA
                    WHERE
                        NOT (a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                            AND e.COD_ALUMNO IS NOT NULL)
                        AND a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                            AND a.COD_CARRERA in $carrera
                        GROUP BY
                            CASE
                                WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                                WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                            END
                        ORDER BY
                            semestre DESC
                    ),
                    activos AS (
                    SELECT
                        ma.ANO || '-' || ma.SEMESTRE AS semestre,
                        count(*) AS total,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'F' THEN 1 
                                            END) AS cantidad_F,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'M' THEN 1 
                                            END) AS cantidad_M
                    FROM
                        MATRICULA_ALUMNO ma
                    JOIN ALUMNO a ON
                        a.COD_ALUMNO = ma.COD_ALUMNO
                        AND a.COD_CARRERA = ma.COD_CARRERA
                    JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
                    WHERE
                        ma.MATRICULADO IN ('I', 'S', 'R', 'P', 'M')
                            AND
                    ma.COD_CARRERA in $carrera
                        GROUP BY
                            ma.ANO || '-' || ma.SEMESTRE
                        ORDER BY
                            semestre DESC
                    ),
                    cance_semestre AS (
                    SELECT
                        ma.ANO || '-' || ma.SEMESTRE AS semestre,
                        count(*) AS total,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'F' THEN 1 
                                            END) AS cantidad_F,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'M' THEN 1 
                                            END) AS cantidad_M
                    FROM
                        MATRICULA_ALUMNO ma
                    JOIN ALUMNO a ON
                        a.COD_ALUMNO = ma.COD_ALUMNO
                        AND a.COD_CARRERA = ma.COD_CARRERA
                    JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
                    WHERE
                        ma.MATRICULADO IN ('C')
                            AND ma.COD_CARRERA in $carrera
                        GROUP BY
                            ma.ANO || '-' || ma.SEMESTRE
                        ORDER BY
                            semestre DESC
                    ),
                    graduados AS (
                    SELECT
                        SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, 5, 1) AS semestre,
                        count(*) AS total,
                        COUNT(CASE 
                                                WHEN de.SEXO = 'F' THEN 1 
                                            END) AS cantidad_F,
                        COUNT(CASE 
                                                WHEN de.SEXO = 'M' THEN 1 
                                            END) AS cantidad_M
                    FROM
                        EXALUMNO e
                    JOIN DATOS_EXA de ON
                        de.DOCUMENTO = e.DOCUMENTO
                    WHERE 
                        e.COD_CARRERA in $carrera
                        AND LENGTH(e.PER_ACA_RETIRO) >= 5
                    GROUP BY
                        SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, 5, 1)
                    ORDER BY
                        semestre DESC
                    ),
                    actual AS (
                    SELECT
                        TO_CHAR(SYSDATE, 'YYYY') || '-' ||
                        CASE
                            WHEN EXTRACT(MONTH FROM SYSDATE) < 8 THEN '1'
                            ELSE '2'
                        END AS semestre,
                            count(*) AS total,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'F' THEN 1 
                                            END) AS cantidad_F,
                        COUNT(CASE 
                                                WHEN dp.SEXO = 'M' THEN 1 
                                            END) AS cantidad_M
                    FROM
                        alumno a
                    JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
                    WHERE
                        a.MATRICULADO IN ('I', 'S', 'R', 'P', 'M')
                        AND a.COD_CARRERA in $carrera
                    ), 
                    resumen_base AS (
                    SELECT
                        COALESCE(a.semestre, i.semestre, g.semestre, cs.semestre, aa.semestre) AS semestre,
                        COALESCE(i.total, 0) + COALESCE(a.total, 0) + COALESCE(g.total, 0) + COALESCE(cs.total, 0) + COALESCE(aa.total, 0) AS total,
                        COALESCE(i.cantidad_f, 0) + COALESCE(cs.cantidad_f, 0) AS inactivos_f,
                        COALESCE(i.cantidad_m, 0) + COALESCE(cs.cantidad_m, 0) AS inactivos_m,
                        COALESCE(a.cantidad_f, 0) + COALESCE(aa.cantidad_f, 0) AS activos_f,
                        COALESCE(a.cantidad_m, 0) + COALESCE(aa.cantidad_m, 0) AS activos_m,
                        COALESCE(g.cantidad_f, 0) AS graduados_f,
                        COALESCE(g.cantidad_m, 0) AS graduados_m
                    FROM
                        activos a
                        FULL OUTER JOIN inactivos i ON i.semestre = a.semestre
                        FULL OUTER JOIN graduados g ON g.semestre = COALESCE(a.semestre, i.semestre)
                        FULL OUTER JOIN cance_semestre cs ON cs.semestre = COALESCE(a.semestre, i.semestre)
                        FULL OUTER JOIN actual aa ON aa.semestre = COALESCE(a.semestre, i.semestre, g.semestre)
                    )SELECT
                    rb.*,
                    ROUND(rb.inactivos_f * 100.0 / NULLIF(rb.total, 0), 2) AS pct_inactivos_f,
                    ROUND(rb.inactivos_m * 100.0 / NULLIF(rb.total, 0), 2) AS pct_inactivos_m,
                    ROUND(rb.activos_f * 100.0 / NULLIF(rb.total, 0), 2) AS pct_activos_f,
                    ROUND(rb.activos_m * 100.0 / NULLIF(rb.total, 0), 2) AS pct_activos_m,
                    ROUND(rb.graduados_f * 100.0 / NULLIF(rb.total, 0), 2) AS pct_graduados_f,
                    ROUND(rb.graduados_m * 100.0 / NULLIF(rb.total, 0), 2) AS pct_graduados_m
                    FROM resumen_base rb
                    ORDER BY rb.semestre asc";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function estadistica_edad_inactivos2($carrera)
    {
        $sql = "WITH ultimo_sem AS (
                    SELECT
                            ma.COD_ALUMNO, ma.COD_CARRERA,ma.ANO, ma.SEMESTRE
                    FROM
                            (
                        SELECT
                                COD_ALUMNO, COD_CARRERA, ANO,  SEMESTRE,
                                ROW_NUMBER() OVER (
                                    PARTITION BY COD_ALUMNO,
                                COD_CARRERA
                        ORDER BY
                                ANO DESC,SEMESTRE DESC,ROWID
                                ) AS rn
                        FROM
                                MATRICULA_ALUMNO
                        ) ma
                    WHERE
                            ma.rn = 1
                    
                    )
                    SELECT
                        CASE
                            WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                            WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                        END AS semestre,
                        TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD') 
                        WHEN ma.SEMESTRE = 2 THEN TO_DATE((ma.ANO + 1) || '-01-01', 'YYYY-MM-DD') 
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12) AS edad,
                    count(*) AS total
                    FROM
                        ALUMNO a
                    JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
                    LEFT JOIN EXALUMNO e ON
                        a.DOCUMENTO = e.DOCUMENTO
                        AND a.COD_CARRERA = e.COD_CARRERA
                        AND a.COD_ALUMNO = e.COD_ALUMNO
                        AND e.TIPO_EXALUMNO = 'G'
                    JOIN ultimo_sem ma ON
                        ma.COD_ALUMNO = a.COD_ALUMNO
                        AND ma.COD_CARRERA = a.COD_CARRERA
                    WHERE 
                            NOT (a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                            AND e.COD_ALUMNO IS NOT NULL)
                        AND a.MATRICULADO IN ('X', 'Y', 'L', 'A')
                            AND a.COD_CARRERA in $carrera
                            GROUP BY CASE
                            WHEN ma.SEMESTRE = 1 THEN ma.ANO || '-2'
                            WHEN ma.SEMESTRE = 2 THEN (ma.ANO + 1) || '-1'
                        END,
                         TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD') 
                        WHEN ma.SEMESTRE = 2 THEN TO_DATE((ma.ANO + 1) || '-01-01', 'YYYY-MM-DD') 
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12)
                    ORDER BY semestre DESC
                   ";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function estadistica_edad_activos_sem_actual($carrera)
    {

        $sql = " SELECT
                TO_CHAR(SYSDATE, 'YYYY') || '-' ||
                CASE
                    WHEN EXTRACT(MONTH FROM SYSDATE) < 8 THEN '1'
                    ELSE '2'
                END AS semestre,
                TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad,
                COUNT(*) AS total
            FROM
                alumno a
                JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
            WHERE
                a.MATRICULADO IN ('I', 'S', 'R', 'P', 'M')
                    AND a.COD_CARRERA in $carrera
            GROUP BY TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12)";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function estadistica_edad_activos2($carrera)
    {
        $sql = "SELECT
                        ma.ANO || '-' || ma.SEMESTRE AS semestre,
                        TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                        WHEN ma.SEMESTRE = 2 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12) AS edad,
                    count(*) as total
                    FROM
                        MATRICULA_ALUMNO ma
                    JOIN ALUMNO a ON
                        a.COD_ALUMNO = ma.COD_ALUMNO
                        AND a.COD_CARRERA = ma.COD_CARRERA
                    JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
                    WHERE
                        ma.MATRICULADO IN ('I', 'S', 'R', 'P', 'M')
                            AND
                    ma.COD_CARRERA in $carrera
                    GROUP BY  ma.ANO || '-' || ma.SEMESTRE,
                    TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                        WHEN ma.SEMESTRE = 2 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12)
                    ORDER BY semestre";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


    public function estadistica_edad_cancel2($carrera)
    {

        $sql = "SELECT
                        ma.ANO || '-' || ma.SEMESTRE AS semestre,
                        TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                        WHEN ma.SEMESTRE = 2 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12) AS edad,
                    count(*) as total
                    FROM
                        MATRICULA_ALUMNO ma
                    JOIN ALUMNO a ON
                        a.COD_ALUMNO = ma.COD_ALUMNO
                        AND a.COD_CARRERA = ma.COD_CARRERA
                    JOIN DATOS_PER dp ON
                        a.DOCUMENTO = dp.DOCUMENTO
                    WHERE
                        ma.MATRICULADO IN ('C')
                            AND ma.COD_CARRERA in $carrera
                    GROUP BY  ma.ANO || '-' || ma.SEMESTRE,
                    TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN ma.SEMESTRE = 1 THEN TO_DATE(ma.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                        WHEN ma.SEMESTRE = 2 THEN TO_DATE(ma.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12)
                    ORDER BY semestre DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }




    public function estadistica_edad_grados2($carrera)
    {
        $sql = "SELECT
	SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, 5, 1) AS semestre,
	TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '1' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-01-01', 'YYYY-MM-DD')
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '2' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-07-01', 'YYYY-MM-DD')
                        END,
                        de.FECHA_NACIMIENTO
                    ) / 12) AS edad, 
                    count(*) AS total
                        FROM
                            EXALUMNO e
                        JOIN DATOS_EXA de ON
                            de.DOCUMENTO = e.DOCUMENTO
                        WHERE
                            e.COD_CARRERA in $carrera
                            AND LENGTH(e.PER_ACA_RETIRO) >= 5
                        GROUP BY
                            SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, 5, 1),
                            TRUNC(MONTHS_BETWEEN(
                        CASE 
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '1' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-01-01', 'YYYY-MM-DD')
                        WHEN SUBSTR(e.PER_ACA_RETIRO, 5, 1) = '2' 
                            THEN TO_DATE(SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-07-01', 'YYYY-MM-DD')
                        END,
                                de.FECHA_NACIMIENTO
                            ) / 12)
                 ORDER BY semestre DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }
}
