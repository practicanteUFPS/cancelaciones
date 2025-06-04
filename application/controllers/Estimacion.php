<?php

class Estimacion extends CMS_Controller
{

    function __construct()
    {
        parent::__construct();


        if (!isset($this->usuario)) {
            redirect();
        }

        $this->load->library('breadcrumb');
        $this->load->model('Carrera_model');
        $datos_carreras = $this->session->userdata('datos_carrera');
        if (!$datos_carreras) {
            $datos_carreras = $this->Carrera_model->get_carreras_jefe($this->usuario->CODIGO);
            $this->session->set_userdata('datos_carrera', $datos_carreras);
        }


        $this->load->model('Prediccion_model');


        // Custom style
        $template = [
            'tag_open' => '<ol class="breadcrumb">',
            'crumb_open' => '<li class="breadcrumb-item">',
            'crumb_active' => '<li class="breadcrumb-item active" aria-current="page">'
        ];
        $this->breadcrumb->set_template($template);

      
        //$this->template->add_js('js/onnxruntime-web/ort.min');
        $this->template->add_js('js/chartjs/chartjs-plugin-datalabels@2');
        $this->template->add_js('js/chartjs/chart');
        $this->template->add_js('plugins/jspdf/jspdf.plugin.autotable.min');
        $this->template->add_js('plugins/jspdf/jspdf.umd.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/jszip/jszip.min');
        $this->template->add_js('plugins/datatablesbuttons/js/buttons.html5.min');
        $this->template->add_js('plugins/datatablesbuttons/js/dataTables.buttons.min');
        $this->template->add_css('plugins/datatablesbuttons/css/buttons.dataTables.min');
        $this->template->add_js('js/select2/select2.min');
        $this->template->add_js('plugins/datatables/js/dataTables.bootstrap.min');
        $this->template->add_js('plugins/datatables/js/jquery.dataTables.min');
        $this->template->add_css('plugins/datatables/css/dataTables.bootstrap.min');

        $this->template->set_template('default_template/default_template');
        $this->template->add_css('css/adminlte/skins/skin-red-light.min');
        $this->template->add_css('css/adminlte/skins/_all-skins.min');
    }


 function carreras_array()
    {

        $datos_carreras = $this->session->userdata('datos_carrera');

        $codigos = array_map(function ($obj) {
            return "'" . addslashes($obj->COD_CARRERA) . "'";
        }, $datos_carreras);

        return '(' . implode(', ', $codigos) . ')';
    }

    public function mostrar()
    {

        $breadcrumb_items = [
           
            'Estimaciones' => 'estimacion/mostrar'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        // Generate breadcrumb
        $bread = $this->breadcrumb->generate();

        $carrera =  $this->carreras_array();
        $listado = $this->Prediccion_model->get_activos_prediccion($carrera);

        //echo var_dump($listado[0]);

        $datos = $this->normalizar_datos($listado);

        //$this->template->set('carrera', $carrera);
        $this->template->set('bread', $bread);
        $this->template->set('datos', $datos);

        $this->template->add_js('js/estimacion/estimacion');
        $this->template->set('item_sidebar_active', 'estimacion');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de asignaturas');
        $this->template->render('prediccion/prediccion_lista');
    }

    function normalizar_datos($datos)
    {
        $agrupados = [];

        // Codificación de SEXO
        $sexo_map = ['F' => 0, 'M' => 1];

        foreach ($datos as $registro) {
            $codigo = $registro->CODIGO_ALUMNO;

            if (!isset($agrupados[$codigo])) {
                $agrupados[$codigo] = [
                    "EDAD" => [],
                    "SEXO" => [],
                    "JORNADA" => [],
                    "CARRERA" => [],
                    "PROM_POND" => [],
                    "CRED_TOTAL" => 0,
                    "CRED_APROB" => 0,
                    "CRED_REPROB" => 0,
                    "CRED_CANCEL_EXT" => 0,
                    "CANC_EXT" => 0,
                    "SEMESTRE" => 0,
                    "DESERCION" => 0,

                    // Nuevos campos agregados
                    "DOCUMENTO" => $registro->DOCUMENTO,
                    "NOMBRES" => $registro->NOMBRES,
                    "NOMBRE_CARRERA" => $registro->NOMBRE_CARRERA,
                    "ULT_ANO_MATRICULADO" => $registro->ULT_ANO_MATRICULADO,
                    "ULT_SEM_MATRICULADO" => $registro->ULT_SEM_MATRICULADO,
                    "MATRICULA_ESTADO" => $registro->MATRICULA_ESTADO,
                    "MATRICULA_DESCRIPCION" => $registro->MATRICULA_DESCRIPCION,
                    "APROBADOS" => $registro->APROBADOS,
                    "CURSADOS" => $registro->CURSADOS,
                    "NUM_SEM_MAT" => $registro->NUM_SEM_MAT,
                    "SEXO_C" => $registro->SEXO,
                    "EDAD_ACTUAL" => $registro->EDAD_ACTUAL
                ];
            }

            $agrupados[$codigo]["EDAD"][] = floatval($registro->EDAD);
            $agrupados[$codigo]["SEXO"][] = $sexo_map[$registro->SEXO];
            $agrupados[$codigo]["JORNADA"][] = $registro->JORNADA;
            $agrupados[$codigo]["CARRERA"][] = $registro->CARRERA;
            $agrupados[$codigo]["PROM_POND"][] = floatval($registro->PROM_POND);
            $agrupados[$codigo]["CRED_TOTAL"] += floatval($registro->CRED_TOTAL);
            $agrupados[$codigo]["CRED_APROB"] += floatval($registro->CRED_APROB);
            $agrupados[$codigo]["CRED_REPROB"] += floatval($registro->CRED_REPROB);
            $agrupados[$codigo]["CRED_CANCEL_EXT"] += floatval($registro->CRED_CANCEL_EXT);
            $agrupados[$codigo]["CANC_EXT"] += floatval($registro->CANC_EXT);
            $agrupados[$codigo]["SEMESTRE"] += 1;
            $agrupados[$codigo]["DESERCION"] = max($agrupados[$codigo]["DESERCION"], intval($registro->DESERCION));
        }

        $resultados = [];

        foreach ($agrupados as $codigo => $a) {
            // Codificación de JORNADA y CARRERA como valores únicos por orden
            $jornada = array_count_values($a["JORNADA"]);
            $carrera = array_count_values($a["CARRERA"]);
            $jornada_encoded = array_search(max($jornada), $jornada); // el modo
            $carrera_encoded = array_search(max($carrera), $carrera); // el modo

            // Puedes luego mapear estas a números como en Python con LabelEncoder
            static $jornada_map = [];
            static $carrera_map = [];
            if (!isset($jornada_map[$jornada_encoded])) $jornada_map[$jornada_encoded] = count($jornada_map);
            if (!isset($carrera_map[$carrera_encoded])) $carrera_map[$carrera_encoded] = count($carrera_map);

            $edad_promedio = array_sum($a["EDAD"]) / count($a["EDAD"]);
            if ($edad_promedio < 18) {
                $edad_categoria = 0;
            } elseif ($edad_promedio <= 24) {
                $edad_categoria = 1;
            } elseif ($edad_promedio <= 29) {
                $edad_categoria = 2;
            } else {
                $edad_categoria = 3;
            }

            $res = [
                // Datos identificativos y para mostrar
                "CODIGO_ALUMNO" => $codigo,
                "DOCUMENTO" => $a["DOCUMENTO"],
                "NOMBRES" => $a["NOMBRES"],
                "NOMBRE_CARRERA" => $a["NOMBRE_CARRERA"],
                "ULT_ANO_MATRICULADO" => $a["ULT_ANO_MATRICULADO"],
                "ULT_SEM_MATRICULADO" => $a["ULT_SEM_MATRICULADO"],
                "MATRICULA_ESTADO" => $a["MATRICULA_ESTADO"],
                "MATRICULA_DESCRIPCION" => $a["MATRICULA_DESCRIPCION"],
                "APROBADOS" => $a["APROBADOS"],
                "CURSADOS" => $a["CURSADOS"],
                "NUM_SEM_MAT" => $a["NUM_SEM_MAT"],
                "SEXO_C" => $a["SEXO_C"],
                "EDAD_ACTUAL" => $a["EDAD_ACTUAL"],



                "f0" => $edad_categoria,
                "f1" => array_sum($a["SEXO"]) / count($a["SEXO"]),
                "f2" => $jornada_map[$jornada_encoded],
                "f3" => $carrera_map[$carrera_encoded],
                "f4" => array_sum($a["PROM_POND"]) / count($a["PROM_POND"]),
                "f5" => $a["CRED_TOTAL"],
                "f6" => $a["CRED_APROB"],
                "f7" => $a["CRED_REPROB"],
                "f8" => $a["CRED_CANCEL_EXT"],
                "f9" => $a["CANC_EXT"],
                "f10" => $a["SEMESTRE"],
                "f11" => $a["SEMESTRE"] > 0 ? $a["CANC_EXT"] / $a["SEMESTRE"] : 0,
                "f12" => $a["CRED_TOTAL"] > 0 ? $a["CRED_REPROB"] / $a["CRED_TOTAL"] : 0,
                "f13" => $a["CRED_TOTAL"] > 0 ? $a["CRED_APROB"] / $a["CRED_TOTAL"] : 0,
                "DESERCION" => $a["DESERCION"]
            ];

            $resultados[] = $res;
        }

        return $resultados;
    }
}
