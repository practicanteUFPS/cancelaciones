<?php
class Prediccion_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_activos_prediccion($carrera)
    {
        $sql = "WITH cancelaciones AS (
SELECT
	c.COD_CARRERA,
	c.COD_ALUMNO,
	c.ANO,
	c.SEMESTRE,
	SUM(m.CREDITOS) AS cred_cancel_ext,
	COUNT(*) AS canc_ext
FROM
	CANCELACIONE_DETALLE cd
JOIN CANCELACIONE c ON
	c.CONSECUTIVO = cd.CONSECUTIVO
JOIN MATERIA m ON
	m.COD_MATERIA = cd.COD_MATERIA
	AND m.COD_CARRERA = c.COD_CARRERA
WHERE
	c.ESTADO = 'R'
GROUP BY
	c.COD_CARRERA,
	c.COD_ALUMNO,
	c.ANO,
	c.SEMESTRE
	),
cancelaciones_ordinarias AS (
SELECT
	rs.COD_CARRERA,
	rs.COD_ALUMNO,
	EXTRACT(YEAR FROM rs.FECHA) AS ANO,
	CASE
		WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN '1'
		ELSE '2'
	END AS SEMESTRE,
	COUNT(*) AS canc_ord,
	SUM(m.CREDITOS) AS cred_cancel_ord
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
GROUP BY
	rs.COD_CARRERA,
	rs.COD_ALUMNO,
	EXTRACT(YEAR FROM rs.FECHA),
	CASE
		WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN '1'
		ELSE '2'
	END
),
impresion_act AS (
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
),
notas_act AS(
SELECT
	a.COD_CARRERA || a.COD_ALUMNO AS CODIGO_ALUMNO,
	n.ano || '-' || n.semestre AS semestre,
	 a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
                       a.ULT_SEM_MATRICULADO, a.MATRICULADO AS MATRICULA_ESTADO, 
                       a.CRE_APROBADOS as aprobados, a.CRE_CURSADOS as cursados, a.NUM_SEM_MAT,
                       car.NOMBRE AS NOMBRE_CARRERA, 
                       dp.NOMBRES, m.DESCRIPCION AS MATRICULA_DESCRIPCION,
					   TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12) AS edad_actual,
	car.COD_CARRERA AS carrera,
	car.JORNADA,
	dp.SEXO,
	TRUNC(MONTHS_BETWEEN(
                        CASE
                        WHEN n.SEMESTRE = 1 THEN TO_DATE(n.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                        WHEN n.SEMESTRE = 2 THEN TO_DATE(n.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12) AS EDAD,
	a.ESTRATO,
	ROUND(SUM(NVL(n.HABILITACION, n.DEFINITIVA) * m.CREDITOS) / SUM(m.CREDITOS), 2) AS prom_pond,
	SUM(m.CREDITOS) AS cred_total,
		MAX(NVL(ca.cred_cancel_ext, 0)) AS cred_cancel_ext,
	MAX(NVL(ca.canc_ext, 0)) AS canc_ext,
	MAX(NVL(co.cred_cancel_ord, 0)) AS cred_cancel_ord,
	MAX(NVL(co.canc_ord, 0)) AS canc_ord,
	MAX(NVL(ca.cred_cancel_ext, 0)) + MAX(NVL(co.cred_cancel_ord, 0)) AS cred_cancel_total,
	SUM(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) >= 3 THEN m.CREDITOS ELSE 0 END) AS cred_aprob,
	SUM(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) < 3 THEN m.CREDITOS ELSE 0 END) AS cred_reprob,
	CASE
		WHEN a.MATRICULADO IN ('X', 'Y', 'L' , 'A') THEN 1
		WHEN a.MATRICULADO = 'X'
			AND e.TIPO_EXALUMNO = 'G' THEN 0
			WHEN a.MATRICULADO IN ('I', 'S', 'R', 'P', 'M') THEN 0
			ELSE 0
		END AS desercion
	FROM
		impresion_act n
	JOIN MATERIA m ON
		n.COD_MATERIA = m.COD_MATERIA
		AND n.COD_CARRERA = m.COD_CARRERA
	JOIN CARRERA car ON
		car.COD_CARRERA = n.COD_CARRERA
	JOIN ALUMNO a ON
		n.COD_ALUMNO = a.COD_ALUMNO
		AND n.COD_CARRERA = a.COD_CARRERA
	JOIN MATRICULADO m ON a.MATRICULADO = m.ESTADO
	JOIN DATOS_PER dp ON
		dp.DOCUMENTO = a.DOCUMENTO
	LEFT JOIN cancelaciones ca ON
		ca.COD_CARRERA = a.COD_CARRERA
		AND ca.COD_ALUMNO = a.COD_ALUMNO
		AND ca.ANO = n.ANO
		AND ca.SEMESTRE = n.SEMESTRE
	LEFT JOIN cancelaciones_ordinarias co ON
		co.COD_CARRERA = a.COD_CARRERA
		AND co.COD_ALUMNO = a.COD_ALUMNO
		AND co.ANO = n.ANO
		AND co.SEMESTRE = n.SEMESTRE
	LEFT JOIN EXALUMNO e ON
		a.DOCUMENTO = e.DOCUMENTO
		AND a.COD_CARRERA = e.COD_CARRERA
		AND a.COD_ALUMNO = e.COD_ALUMNO
		AND e.TIPO_EXALUMNO = 'G'
	WHERE
		NOT (a.MATRICULADO = 'X'
			AND e.COD_ALUMNO IS NOT NULL)
        AND a.COD_CARRERA in $carrera
        --AND a.MATRICULADO  IN ('I','S','R','P','M')
		AND a.MATRICULADO  IN ('I','S','P','M')
	GROUP BY
		a.COD_CARRERA || a.COD_ALUMNO,
		n.ano || '-' || n.semestre,
		a.DOCUMENTO, a.ULT_ANO_MATRICULADO, 
		a.ULT_SEM_MATRICULADO, a.MATRICULADO,
                       a.CRE_APROBADOS, a.CRE_CURSADOS, a.NUM_SEM_MAT,
                       car.NOMBRE,
                       dp.NOMBRES, m.DESCRIPCION,
					   TRUNC(MONTHS_BETWEEN(SYSDATE, dp.FECHA_NACIMIENTO) / 12),
		car.COD_CARRERA,
		car.JORNADA,
		dp.SEXO,
		TRUNC(MONTHS_BETWEEN(
                        CASE
                        WHEN n.SEMESTRE = 1 THEN TO_DATE(n.ANO || '-01-01', 'YYYY-MM-DD')-- Primer semestre: enero
                        WHEN n.SEMESTRE = 2 THEN TO_DATE(n.ANO || '-07-01', 'YYYY-MM-DD')-- Segundo semestre: julio
                        END,
                        dp.FECHA_NACIMIENTO
                    ) / 12) ,
		a.ESTRATO,
		a.MATRICULADO,
		e.TIPO_EXALUMNO
	ORDER BY
		a.COD_CARRERA || a.COD_ALUMNO,
		semestre ASC
)
SELECT
	ag.codigo_alumno,
	ag.semestre,
	 ag.DOCUMENTO, ag.ULT_ANO_MATRICULADO, 
                       ag.ULT_SEM_MATRICULADO, ag.MATRICULA_ESTADO, 
                       ag.aprobados, ag.cursados, ag.NUM_SEM_MAT,
                       ag.NOMBRE_CARRERA, 
                       ag.NOMBRES, ag.MATRICULA_DESCRIPCION, ag.edad_actual,
	ag.carrera,
	ag.jornada,
	ag.sexo ,
	ag.edad ,
	--ag.estrato ,
	ag.prom_pond ,
	ag.cred_total ,
	ag.cred_cancel_ext,
	ag.canc_ext ,
	--ag.cred_cancel_ord ,
	--ag.canc_ord,
	--ag.cred_cancel_total ,
	ag.cred_aprob ,
	ag.cred_reprob,
	ag.desercion
FROM
	notas_act ag";

//echo $sql;
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }


}