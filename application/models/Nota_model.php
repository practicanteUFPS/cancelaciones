<?php
class Nota_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_nota($codigo, $carrera)
    {
        $sql = "SELECT n.COD_CARRERA, n.COD_MATERIA, n.DEFINITIVA, n.CREDITOS, n.COD_CAR_MAT, 
        n.COD_MAT_MAT, n.ANO, n.SEMESTRE , m.NOMBRE FROM NOTA n 
        JOIN MATERIA m ON m.COD_MATERIA = n.COD_MATERIA AND m.COD_CARRERA = n.COD_CARRERA
        WHERE  n.COD_ALUMNO = '$codigo' AND n.COD_CARRERA = '$carrera'
        ORDER BY n.ANO DESC, n.SEMESTRE DESC";


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
                ) SELECT n.COD_CARRERA, n.COD_MATERIA,  NVL(n.HABILITACION, n.DEFINITIVA) AS DEFINITIVA, n.CREDITOS, n.COD_CAR_MAT, 
        n.COD_MAT_MAT, n.ANO, n.SEMESTRE , m.NOMBRE FROM impresion_act n 
        JOIN MATERIA m ON m.COD_MATERIA = n.COD_MATERIA AND m.COD_CARRERA = n.COD_CARRERA
        WHERE  n.COD_ALUMNO = '$codigo' AND n.COD_CARRERA = '$carrera'
        ORDER BY n.ANO DESC, n.SEMESTRE DESC";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_nota_graduado($codigo, $carrera)
    {
        $sql = "SELECT ne.COD_CARRERA, ne.COD_MATERIA, ne.DEFINITIVA, ne.CREDITOS, ne.COD_CAR_MAT, 
        ne.COD_MAT_MAT, ne.ANO, ne.SEMESTRE , m.NOMBRE FROM NOTAS_EXA ne 
        JOIN MATERIA m ON m.COD_MATERIA = ne.COD_MATERIA AND m.COD_CARRERA = ne.COD_CARRERA
        WHERE  ne.COD_ALUMNO = '$codigo' AND ne.COD_CARRERA = '$carrera'
        ORDER BY ne.ANO DESC, ne.SEMESTRE DESC";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_estudiante_nota_tipo($codigo, $carrera, $semestre, $ano, $operador, $nota)
    {
        /*
        $sql = "SELECT
                    dp.NOMBRES ,
                    n.DEFINITIVA,
                    tn.NOMBRE AS TIPO_NOTA ,
                    a.DOCUMENTO ,
                    a.COD_ALUMNO ,
                    a.COD_CARRERA ,
                    NULL AS PER_ACA_RETIRO
                FROM
                    NOTA n
                JOIN ALUMNO a ON
                    n.COD_ALUMNO = a.COD_ALUMNO
                    AND n.COD_CARRERA = a.COD_CARRERA
                JOIN DATOS_PER dp ON
                    dp.DOCUMENTO = a.DOCUMENTO
                JOIN TIPO_NOTA tn ON
                    n.TIPO_NOTA = tn.TIPO_NOTA
                LEFT JOIN EXALUMNO e ON
                    a.DOCUMENTO = e.DOCUMENTO
                    AND a.COD_CARRERA = e.COD_CARRERA
                    AND a.COD_ALUMNO = e.COD_ALUMNO
                    AND e.TIPO_EXALUMNO = 'G'
                WHERE
                    NOT (a.MATRICULADO IN('X', 'G')
                        AND e.COD_ALUMNO IS NOT NULL)
                    AND n.COD_CARRERA = '$carrera' 
                    AND n.COD_MATERIA  = '$codigo'
                    AND n.SEMESTRE='$semestre' AND n.ANO = $ano
                    AND n.DEFINITIVA $operador'$nota'";
*/

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
                )
                SELECT
                    dp.NOMBRES ,
                    NVL(ia.HABILITACION, ia.DEFINITIVA) AS DEFINITIVA,
                    tn.NOMBRE AS TIPO_NOTA ,
                    a.DOCUMENTO ,
                    a.COD_ALUMNO ,
                    a.COD_CARRERA ,
                    NULL AS PER_ACA_RETIRO
                FROM
                    impresion_act ia
                JOIN ALUMNO a ON
                    ia.COD_ALUMNO = a.COD_ALUMNO
                    AND ia.COD_CARRERA = a.COD_CARRERA
                JOIN DATOS_PER dp ON
                    dp.DOCUMENTO = a.DOCUMENTO
                JOIN TIPO_NOTA tn ON
                    ia.TIPO_NOTA = tn.TIPO_NOTA
                LEFT JOIN EXALUMNO e ON
                    a.DOCUMENTO = e.DOCUMENTO
                    AND a.COD_CARRERA = e.COD_CARRERA
                    AND a.COD_ALUMNO = e.COD_ALUMNO
                    AND e.TIPO_EXALUMNO = 'G'
                WHERE
                    NOT (a.MATRICULADO IN('X', 'G')
                        AND e.COD_ALUMNO IS NOT NULL)
                    AND ia.COD_CARRERA = '$carrera' 
                    AND ia.COD_MATERIA  = '$codigo'
                    AND ia.SEMESTRE='$semestre' AND ia.ANO = $ano
                    AND NVL(ia.HABILITACION, ia.DEFINITIVA) $operador'$nota'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_estudiante_graduado_nota_tipo($codigo, $carrera, $semestre, $ano, $operador, $nota)
    {

        /*
        $sql = "SELECT
                    de.NOMBRES ,
                    ne.DEFINITIVA ,
                    tn.NOMBRE AS TIPO_NOTA ,
                    ne.DOCUMENTO ,
                    e.COD_ALUMNO,
                    e.COD_CARRERA ,
                    SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, -1) AS PER_ACA_RETIRO
                FROM
                    NOTAS_EXA ne
                JOIN EXALUMNO e ON
                    e.COD_ALUMNO = ne.COD_ALUMNO
                    AND e.COD_CARRERA = ne.COD_CARRERA
                    AND e.DOCUMENTO = ne.DOCUMENTO
                JOIN DATOS_EXA de ON
                    de.DOCUMENTO = e.DOCUMENTO
                 JOIN TIPO_NOTA tn ON
                    ne.TIPO_NOTA = tn.TIPO_NOTA
                WHERE
                    ne.COD_CARRERA = '$carrera'
                    AND ne.COD_MATERIA = '$codigo'
                    AND ne.SEMESTRE = '$semestre'
                    AND ne.ANO = $ano
                    AND ne.DEFINITIVA $operador '$nota'";
*/

        $sql = "WITH notas_impresion_grads AS (
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
                )
                SELECT
                    de.NOMBRES ,
                    NVL(ne.HABILITACION, ne.DEFINITIVA) AS DEFINITIVA ,
                    tn.NOMBRE AS TIPO_NOTA ,
                    ne.DOCUMENTO ,
                    e.COD_ALUMNO,
                    e.COD_CARRERA ,
                    SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, -1) AS PER_ACA_RETIRO
                FROM
                    notas_impresion_grads ne
                JOIN EXALUMNO e ON
                    e.COD_ALUMNO = ne.COD_ALUMNO
                    AND e.COD_CARRERA = ne.COD_CARRERA
                    AND e.DOCUMENTO = ne.DOCUMENTO
                JOIN DATOS_EXA de ON
                    de.DOCUMENTO = e.DOCUMENTO
                JOIN TIPO_NOTA tn ON
                    ne.TIPO_NOTA = tn.TIPO_NOTA
                WHERE
                    ne.COD_CARRERA = '$carrera'
                    AND ne.COD_MATERIA = '$codigo'
                    AND ne.SEMESTRE = '$semestre'
                    AND ne.ANO = $ano
                    AND NVL(ne.HABILITACION, ne.DEFINITIVA) $operador '$nota'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_estudiante_nota_rango($codigo, $carrera, $semestre, $ano, $nota_menor, $nota_mayor)
    {
        /*
        $sql = "SELECT
                    dp.NOMBRES ,
                    n.DEFINITIVA,
                    n.TIPO_NOTA ,
                    a.DOCUMENTO ,
                    a.COD_ALUMNO ,
                    a.COD_CARRERA ,
                    NULL AS PER_ACA_RETIRO
                FROM
                    NOTA n
                JOIN ALUMNO a ON
                    n.COD_ALUMNO = a.COD_ALUMNO
                    AND n.COD_CARRERA = a.COD_CARRERA
                JOIN DATOS_PER dp ON
                    dp.DOCUMENTO = a.DOCUMENTO
                LEFT JOIN EXALUMNO e ON
                    a.DOCUMENTO = e.DOCUMENTO
                    AND a.COD_CARRERA = e.COD_CARRERA
                    AND a.COD_ALUMNO = e.COD_ALUMNO
                    AND e.TIPO_EXALUMNO = 'G'
                WHERE
                    NOT (a.MATRICULADO IN('X', 'G')
                        AND e.COD_ALUMNO IS NOT NULL)
                    AND n.COD_CARRERA = '$carrera' 
                    AND n.COD_MATERIA  = '$codigo'
                    AND n.SEMESTRE='$semestre' 
                    AND n.ANO = $ano
                    AND n.DEFINITIVA >='$nota_menor' 
                    AND n.DEFINITIVA < '$nota_mayor'";

*/
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
                )
                SELECT
                    dp.NOMBRES ,
                    NVL(ia.HABILITACION, ia.DEFINITIVA) AS DEFINITIVA,
                    tn.NOMBRE AS TIPO_NOTA ,
                    a.DOCUMENTO ,
                    a.COD_ALUMNO ,
                    a.COD_CARRERA ,
                    NULL AS PER_ACA_RETIRO
                FROM
                    impresion_act ia
                JOIN ALUMNO a ON
                    ia.COD_ALUMNO = a.COD_ALUMNO
                    AND ia.COD_CARRERA = a.COD_CARRERA
                JOIN DATOS_PER dp ON
                    dp.DOCUMENTO = a.DOCUMENTO
                JOIN TIPO_NOTA tn ON
                    ia.TIPO_NOTA = tn.TIPO_NOTA
                LEFT JOIN EXALUMNO e ON
                    a.DOCUMENTO = e.DOCUMENTO
                    AND a.COD_CARRERA = e.COD_CARRERA
                    AND a.COD_ALUMNO = e.COD_ALUMNO
                    AND e.TIPO_EXALUMNO = 'G'
                WHERE
                    NOT (a.MATRICULADO IN('X', 'G')
                        AND e.COD_ALUMNO IS NOT NULL)
                    AND ia.COD_CARRERA = '$carrera' 
                    AND ia.COD_MATERIA  = '$codigo'
                    AND ia.SEMESTRE='$semestre' 
                    AND ia.ANO = $ano
                    AND ia.DEFINITIVA >='$nota_menor' 
                    AND ia.DEFINITIVA < '$nota_mayor'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_estudiante_graduado_nota_rango($codigo, $carrera, $semestre, $ano, $nota_menor, $nota_mayor)
    {
        /*
        $sql = "SELECT
                    de.NOMBRES ,
                    ne.DEFINITIVA ,
                    ne.TIPO_NOTA ,
                    ne.DOCUMENTO ,
                    e.COD_ALUMNO,
                    e.COD_CARRERA ,
                    e.PER_ACA_RETIRO
                FROM
                    NOTAS_EXA ne
                JOIN EXALUMNO e ON
                    e.COD_ALUMNO = ne.COD_ALUMNO
                    AND e.COD_CARRERA = ne.COD_CARRERA
                    AND e.DOCUMENTO = ne.DOCUMENTO
                JOIN DATOS_EXA de ON
                    de.DOCUMENTO = e.DOCUMENTO
                WHERE
                    ne.COD_CARRERA = '$carrera' 
                    AND ne.COD_MATERIA  = '$codigo'
                    AND ne.SEMESTRE = '$semestre' 
                    AND ne.ANO = $ano
                    AND ne.DEFINITIVA >='$nota_menor' 
                    AND ne.DEFINITIVA < '$nota_mayor'";
*/
        $sql = "WITH notas_impresion_grads AS (
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
                )
                SELECT
                    de.NOMBRES ,
                    NVL(ne.HABILITACION, ne.DEFINITIVA) AS DEFINITIVA ,
                    tn.NOMBRE AS TIPO_NOTA ,
                    ne.DOCUMENTO ,
                    e.COD_ALUMNO,
                    e.COD_CARRERA ,
                    SUBSTR(e.PER_ACA_RETIRO, 1, 4) || '-' || SUBSTR(e.PER_ACA_RETIRO, -1) AS PER_ACA_RETIRO
                FROM
                    notas_impresion_grads ne
                JOIN EXALUMNO e ON
                    e.COD_ALUMNO = ne.COD_ALUMNO
                    AND e.COD_CARRERA = ne.COD_CARRERA
                    AND e.DOCUMENTO = ne.DOCUMENTO
                JOIN DATOS_EXA de ON
                    de.DOCUMENTO = e.DOCUMENTO
                JOIN TIPO_NOTA tn ON
                    ne.TIPO_NOTA = tn.TIPO_NOTA
                WHERE
                    ne.COD_CARRERA = '$carrera' 
                    AND ne.COD_MATERIA  = '$codigo'
                    AND ne.SEMESTRE = '$semestre' 
                    AND ne.ANO = $ano
                    AND ne.DEFINITIVA >='$nota_menor' 
                    AND ne.DEFINITIVA < '$nota_mayor'";

        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }

    public function get_conteo_cancelaciones($codigo, $carrera)
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
                ),  registro_con_semestre AS (
                SELECT
	rs.COD_CARRERA,
	rs.COD_MATERIA,
	EXTRACT(YEAR FROM rs.FECHA) AS anio,
	CASE
		WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN 1
		ELSE 2
	END AS SEMESTRE,
	COUNT(*) AS cancelaciones_ordinarias
FROM
	REGISTRO_SEMESTRE rs
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
                    AND rs.COD_CARRERA = $carrera
                    AND rs.COD_MATERIA = $codigo
                GROUP BY
                    rs.COD_CARRERA,
                    rs.COD_MATERIA,
                    EXTRACT(YEAR FROM rs.FECHA),
	CASE
		WHEN TO_CHAR(rs.FECHA, 'MMDD') < '0801' THEN 1
		ELSE 2
	END                                                                                           
                ),
                cancelaciones AS (
                SELECT
                    TO_NUMBER(c.ANO) AS ANO,
                    TO_NUMBER(c.SEMESTRE) AS SEMESTRE,
                    COUNT(*) AS cancelaciones
                FROM
                    CANCELACIONE_DETALLE cd
                JOIN CANCELACIONE c ON
                    c.CONSECUTIVO = cd.CONSECUTIVO
                JOIN MATERIA m ON
                    m.COD_CARRERA = c.COD_CARRERA
                    AND m.COD_MATERIA = cd.COD_MATERIA
                WHERE
                    c.ESTADO = 'R'
                    AND m.COD_CARRERA = '$carrera'
                    AND m.COD_MATERIA = '$codigo'
                GROUP BY
                    TO_NUMBER(c.ANO),
                    TO_NUMBER(c.SEMESTRE)
                ),
                notas AS (
                SELECT
                    TO_NUMBER(n.ANO) AS ANO,
                    TO_NUMBER(n.SEMESTRE) AS SEMESTRE,
                    COUNT(*) AS total_notas,
                    COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) = 0 THEN 1 END) AS zero,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) < 3 THEN 1 END) AS reprobado,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) >= 3 THEN 1 END) AS aprobado,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) >= 3 AND NVL(n.HABILITACION, n.DEFINITIVA) < 4 THEN 1 END) AS entre_3_y_3_9,
                        COUNT(CASE WHEN NVL(n.HABILITACION, n.DEFINITIVA) >= 4 THEN 1 END) AS entre_4_y_5
                FROM
                    MATERIA m
                JOIN impresion_act n ON
                    n.COD_CARRERA = m.COD_CARRERA
                    AND n.COD_MATERIA = m.COD_MATERIA
                WHERE
                    m.COD_MATERIA = '$codigo'
                    AND m.COD_CARRERA = '$carrera'
                GROUP BY
                    TO_NUMBER(n.ANO),
                    TO_NUMBER(n.SEMESTRE)
                ), 
                notas_grad AS (
                SELECT
                    TO_NUMBER(ne.ANO) AS ANO,
                    TO_NUMBER(ne.SEMESTRE) AS SEMESTRE,
                    COUNT(*) AS total_notas,
                   COUNT(CASE WHEN  NVL(ne.HABILITACION, ne.DEFINITIVA) = 0 THEN 1 END) AS zero,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) < 3 THEN 1 END) AS reprobado,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) >= 3 THEN 1 END) AS aprobado,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) >= 3 AND NVL(ne.HABILITACION, ne.DEFINITIVA) < 4 THEN 1 END) AS entre_3_y_3_9,
                        COUNT(CASE WHEN NVL(ne.HABILITACION, ne.DEFINITIVA) >= 4 THEN 1 END) AS entre_4_y_5
                FROM
                    notas_impresion_grads ne
                WHERE
                    ne.COD_MATERIA = '$codigo'
                    AND ne.COD_CARRERA = '$carrera'
                GROUP BY
                    TO_NUMBER(ne.ANO),
                    TO_NUMBER(ne.SEMESTRE)
                )
                SELECT
                    COALESCE(c.ANO, n.ANO, o.anio, g.ANO) AS ANO,
                    COALESCE(c.SEMESTRE, n.SEMESTRE, o.semestre, g.SEMESTRE) AS SEMESTRE,
                    COALESCE(c.cancelaciones, 0) AS cancelaciones,
                    COALESCE(o.cancelaciones_ordinarias, 0) AS cancelaciones_ordinarias,
                    COALESCE(n.total_notas, 0) + COALESCE(g.total_notas, 0) AS total_notas,
                    COALESCE(n.zero, 0) + COALESCE(g.zero, 0) AS zero,
                    COALESCE(n.reprobado , 0)+ COALESCE(g.reprobado , 0) AS reprobado,
                    COALESCE(n.aprobado , 0) + COALESCE(g.aprobado , 0) AS aprobado,
                    COALESCE(n.entre_3_y_3_9, 0)+ COALESCE(g.entre_3_y_3_9, 0) AS entre_3_y_3_9,
                    COALESCE(n.entre_4_y_5, 0) + COALESCE(g.entre_4_y_5, 0) AS entre_4_y_5
                FROM
                    cancelaciones c
                FULL OUTER JOIN notas n ON
                    c.ANO = n.ANO
                    AND c.SEMESTRE = n.SEMESTRE
                FULL OUTER JOIN notas_grad g ON
                    g.ANO =  coalesce(c.ANO, n.ANO)
                    AND g.SEMESTRE = COALESCE(c.SEMESTRE, n.SEMESTRE)
                FULL OUTER JOIN registro_con_semestre o ON
                    o.anio = COALESCE(c.ANO, n.ANO , g.ANO)
                    AND o.semestre = COALESCE(c.SEMESTRE, n.SEMESTRE, g.SEMESTRE)
                ORDER BY
                    ANO DESC,
                    SEMESTRE DESC";
        $arr = array();
        $this->database2->get_obj_array($sql, $arr);
        return $arr;
    }
}
