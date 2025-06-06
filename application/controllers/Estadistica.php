<?php

class Estadistica extends CMS_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!isset($this->usuario)) {
            redirect();
        }

        $this->load->model('Carrera_model');
        $datos_carreras = $this->session->userdata('datos_carrera');
        if (!$datos_carreras) {
            $datos_carreras = $this->Carrera_model->get_carreras_jefe($this->usuario->CODIGO);
            $this->session->set_userdata('datos_carrera', $datos_carreras);
        }

        $this->load->library('breadcrumb');
        $template = [
            'tag_open' => '<ol class="breadcrumb">',
            'crumb_open' => '<li class="breadcrumb-item">',
            'crumb_active' => '<li class="breadcrumb-item active" aria-current="page">'
        ];
        $this->breadcrumb->set_template($template);

        $this->load->model('Alumno_model');
        $this->load->model('Estadistica_model');

        //$this->template->add_js('plugins/chartjs/Chart.min');

        $this->template->add_css('css/custom_table_size');
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

    public function materia_cancel()
    {

        $carrera =  $this->carreras_array();

        if ($this->input->method() === 'post') {

            // búsqueda por semestre único
            $this->session->set_userdata('cance_anio', $this->input->post('anio_unico'));
            $this->session->set_userdata('cance_semestre', $this->input->post('semestre_unico'));
            $this->session->set_userdata('cance_estado', $this->input->post('estado_cancelacion'));
            // redirige para evitar reenvío de formulario
            redirect('estadistica/materia_cancel');
        } else {
            $this->load->model('Cancelacione_model');

            $anio = $this->session->userdata('cance_anio');
            $semestre = $this->session->userdata('cance_semestre');
            $estado = $this->session->userdata('cance_estado');

            $smestres_factor = $this->Cancelacione_model->semestre_factores($carrera);
            $ult_sem = end($smestres_factor);

             if (!$anio) {
                if ($ult_sem && is_object($ult_sem) && isset($ult_sem->ANO)) {
                    $anio = $ult_sem->ANO;
                } else {
                    $anio = date('Y'); 
                }
            }

            if (!$semestre) {
                if ($ult_sem && is_object($ult_sem) && isset($ult_sem->SEMESTRE)) {
                    $semestre = $ult_sem->SEMESTRE;
                } else {
                    $semestre = 1; 
                }
            }
            if (!$estado) $estado = 'P';

            $datos = $this->Cancelacione_model->materia_conteo($carrera, $anio, $semestre, $estado);

            $breadcrumb_items = [

                'Motivos por materia' => 'estadistica/materia_cancel'
            ];
            $this->breadcrumb->add_item($breadcrumb_items);

            $bread = $this->breadcrumb->generate();
            $this->template->set('bread', $bread);
            $this->template->set('sem_activos', $smestres_factor);
            $this->template->set('anio', $anio);
            $this->template->set('semestre', $semestre);
            $this->template->set('estado', $estado);
            $this->template->set('datos', $datos);
            $this->template->add_js('js/estadistica/materia_cancel');
            $this->template->set('item_sidebar_active', 'ver_est_materia_cancel');
            $this->template->set('content_header', 'Motivos de cancelacion de por materias');
            $this->template->set('content_sub_header', 'Listado de materias con motivos de cancelacion');
            $this->template->render('estadistica/materia_cancelacion');
        }
    }

    public function cancelaciones_materia($anio, $semestre, $estado, $carrera_mat, $materia)
    {
        $carrera =  $this->carreras_array();

        $this->load->model('Cancelacione_model');


        $list_fact = $this->Cancelacione_model->factores_conteo_materia($carrera_mat, $anio, $semestre, $estado, $materia);

        $list_caract = $this->Cancelacione_model->caracteristica_conteo_materia($carrera_mat, $anio, $semestre, $estado, $materia);

        $breadcrumb_items = [

            'Motivos por materia' => 'estadistica/materia_cancel',
            "motivos de materia $carrera_mat$materia" => 'estadistica/cancelaciones_materia'

        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);
        $this->template->set('list_fact', $list_fact);
        $this->template->set('list_caract', $list_caract);
        $this->template->add_js('js/estadistica/factores_cancelacion_m');
        $this->template->set('item_sidebar_active', 'ver_est_materia_cancel');
        $this->template->set('content_header', "$carrera_mat$materia Motivos de cancelacion");
        $this->template->set('content_sub_header', 'Graficas de motivos de cancelacion de la materia');
        $this->template->render('estadistica/factores_cancelacion_materia');
    }

    public function cancelaciones()
    {
        $carrera =  $this->carreras_array();

        if ($this->input->method() === 'post') {

            // búsqueda por semestre único
            $this->session->set_userdata('cance_anio', $this->input->post('anio_unico'));
            $this->session->set_userdata('cance_semestre', $this->input->post('semestre_unico'));
            $this->session->set_userdata('cance_estado', $this->input->post('estado_cancelacion'));
            // redirige para evitar reenvío de formulario
            redirect('estadistica/cancelaciones');
        } else {
            $this->load->model('Cancelacione_model');

            $anio = $this->session->userdata('cance_anio');
            $semestre = $this->session->userdata('cance_semestre');
            $estado = $this->session->userdata('cance_estado');

            $smestres_factor = $this->Cancelacione_model->semestre_factores($carrera);
            $ult_sem = end($smestres_factor);

        
            if (!$anio) {
                if ($ult_sem && is_object($ult_sem) && isset($ult_sem->ANO)) {
                    $anio = $ult_sem->ANO;
                } else {
                    $anio = date('Y'); 
                }
            }

            if (!$semestre) {
                if ($ult_sem && is_object($ult_sem) && isset($ult_sem->SEMESTRE)) {
                    $semestre = $ult_sem->SEMESTRE;
                } else {
                    $semestre = 1; 
                }
            }
            if (!$estado) $estado = 'P';

            $list_fact = $this->Cancelacione_model->factores_conteo($carrera, $anio, $semestre, $estado);

            $list_caract = $this->Cancelacione_model->caracteristica_conteo_general($carrera, $anio, $semestre, $estado);

            $breadcrumb_items = [

                'Factores de cancelacion' => 'estadistica/cancelaciones'
            ];
            $this->breadcrumb->add_item($breadcrumb_items);

            $bread = $this->breadcrumb->generate();
            $this->template->set('bread', $bread);
            $this->template->set('sem_activos', $smestres_factor);
            $this->template->set('anio', $anio);
            $this->template->set('semestre', $semestre);
            $this->template->set('estado', $estado);
            $this->template->set('list_fact', $list_fact);
            $this->template->set('list_caract', $list_caract);
            $this->template->add_js('js/estadistica/factores_cancelacion');

            $this->template->set('item_sidebar_active', 'ver_est_cancelaciones');
            $this->template->set('content_header', 'Motivos de cancelacion');
            $this->template->set('content_sub_header', 'Muestra los motivos de cancelacion de un semestre');
            $this->template->render('estadistica/factores_cancelacion');
        }
    }


    public function buscar_inactivos_edad($anio, $semestre, $categoria)
    {


        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');
        $carrera = $this->carreras_array();


        $datos = $this->Alumno_model->inactivos_semestre($carrera, $anio . '-' . $semestre);

        $datos_filtrados = array_filter($datos, function ($alumno) use ($categoria) {
            if (!isset($alumno->EDAD_SEMESTRE)) return false;

            $edad = (int)$alumno->EDAD_SEMESTRE;

            switch ($categoria) {
                case 'MENOR_18':

                    return $edad < 18;
                case 'ENTRE_18_24':
                    return $edad >= 18 && $edad <= 24;
                case 'ENTRE_25_29':
                    return $edad >= 25 && $edad <= 29;
                case 'MAYOR_30':
                    return $edad > 30;
                default:
                    return false; // categoría no válida
            }
        });

        // Reindexar si lo necesitas
        $datos_filtrados = array_values($datos_filtrados);



        $this->template->add_js('js/alumno/no_matriculado2');

        $breadcrumb_items = [


            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Inactivos' => 'alumno/buscar_inactivos_tabla_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos_filtrados);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');

        $this->template->render('estadistica/lista_inactivos');
    }



    public function buscar_activos_edad($anio, $semestre, $categoria)
    {

        //$anio = $this->input->get('anio');
        //$semestre = $this->input->get('semestre');
        $carrera = $this->carreras_array();

        $datos = $this->Alumno_model->activos_semestre($carrera, $anio . '-' . $semestre);

        $datos_filtrados = array_filter($datos, function ($alumno) use ($categoria) {
            if (!isset($alumno->EDAD_SEMESTRE)) return false;

            $edad = (int)$alumno->EDAD_SEMESTRE;

            switch ($categoria) {
                case 'MENOR_18':
                    return $edad < 18;
                case 'ENTRE_18_24':
                    return $edad >= 18 && $edad <= 24;
                case 'ENTRE_25_29':
                    return $edad >= 25 && $edad <= 29;
                case 'MAYOR_30':
                    return $edad > 30;
                default:
                    return false; // categoría no válida
            }
        });

        // Reindexar si lo necesitas
        $datos_filtrados = array_values($datos_filtrados);

        $breadcrumb_items = [

            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Activos' => 'alumno/buscar_activos_tabla_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->set('datos', $datos_filtrados);

        $this->template->add_js('js/alumno/activos');
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        $this->template->render('estadistica/lista_activos');
    }


    public function buscar_inactivos_sexo($anio, $semestre, $sexo)
    {


        $carrera = $this->carreras_array();


        $datos = $this->Alumno_model->inactivos_semestre($carrera, $anio . '-' . $semestre);

        $datos_filtrados = array_filter($datos, function ($alumno) use ($sexo) {
            return isset($alumno->SEXO) && $alumno->SEXO === $sexo;
        });

        $datos_filtrados = array_values($datos_filtrados);

        $this->template->add_js('js/alumno/no_matriculado2');

        $breadcrumb_items = [

            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Inactivos' => 'alumno/buscar_inactivos_tabla_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos_filtrados);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');

        $this->template->render('estadistica/lista_inactivos');
    }



    public function buscar_activos_sexo($anio, $semestre, $sexo)
    {


        $carrera = $this->carreras_array();

        $datos = $this->Alumno_model->activos_semestre($carrera, $anio . '-' . $semestre);

        $datos_filtrados = array_filter($datos, function ($alumno) use ($sexo) {
            return isset($alumno->SEXO) && $alumno->SEXO === $sexo;
        });

        // Reindexar si es necesario
        $datos_filtrados = array_values($datos_filtrados);

        $breadcrumb_items = [

            'Desercion por semestre' => 'estadistica/desercion_semestre',
            'Activos' => 'alumno/buscar_activos_tabla_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);


        $this->template->set('datos', $datos_filtrados);

        $this->template->add_js('js/alumno/activos');
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        $this->template->render('estadistica/lista_activos');
    }

    public function desercion_semestre()
    {
        $carrera = $this->carreras_array();

        $datos = $this->Estadistica_model->desercion_semestre($carrera);


        $breadcrumb_items = [
            'Desercion por semestre' => 'estadistica/desercion_semestre'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_js('js/estadistica/estadistica_desercion');
        $this->template->set('resumen', $datos);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre');
        $this->template->set('content_header', 'Ver por semestre');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        $this->template->render('estadistica/desercion');
    }


    public function desercion_semestre_ingreso()
    {


        $carrera = $this->carreras_array();

        $datos = $this->Estadistica_model->desercion_semestre_ingreso($carrera);

        //$this->template->add_message(array("error" => "ocurrio un error"));
        //$this->template->add_message(array("warning" => "ocurrio una alerta"));
        //$this->template->add_message(array("success" => "ocurrio algo sin problemas"));

        //estas alertas se mostrarán en la siquiente página que acceda el usuario (se utilizan antes de un redirect())
        //$this->template->set_flash_message(array("mensaje de información cargado en la página anterior por el controlador item2"), "info");

        $breadcrumb_items = [

            'Desercion por semestre de ingreso' => 'estadistica/desercion_semestre_ingreso'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_js('js/estadistica/estadistica_desercion_ingreso');
        $this->template->set('resumen', $datos);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_semestre_ingreso');
        $this->template->set('content_header', 'Ver por semestre de ingreso');
        $this->template->set('content_sub_header', 'Estado de alumnos por semestre');
        $this->template->render('estadistica/desercion_ingreso');
    }

    public function desercion_sexo()
    {
        $carrera = $this->carreras_array();

        $datos = $this->Estadistica_model->get_inactivos_conteo_semestre_sexo($carrera);


        $breadcrumb_items = [

            'Desercion por sexo' => 'estadistica/desercion_sexo'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_js('js/estadistica/estadistica_sexo');
        $this->template->set('datos', $datos);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_sexo');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');
        $this->template->render('estadistica/desercion_sexo');
    }

    public function desercion_edad()
    {
        $carrera = $this->carreras_array();


        $inact = $this->Estadistica_model->estadistica_edad_inactivos2($carrera);
        $act = $this->Estadistica_model->estadistica_edad_activos2($carrera);
        $cancel = $this->Estadistica_model->estadistica_edad_cancel2($carrera);
        $grads = $this->Estadistica_model->estadistica_edad_grados2($carrera);
        $actual_act = $this->Estadistica_model->estadistica_edad_activos_sem_actual($carrera);

        $inacti = $this->edad_to_rangos($inact);
        $cancel_semestre = $this->edad_to_rangos($cancel);

        $activos = $this->edad_to_rangos($act);
        $activos_actual = $this->edad_to_rangos($actual_act);

        $graduados = $this->edad_to_rangos($grads);

        $activos_update = $this->sumar_por_semestre($activos, $activos_actual);
        $inactivos = $this->sumar_por_semestre($inacti, $cancel_semestre);

        $data = $this->combinar_por_semestre($activos_update, $inactivos, $graduados);

        //echo var_dump($data);

        $breadcrumb_items = [

            'Desercion por edad' => 'estadistica/desercion_edad'
        ];
        $this->breadcrumb->add_item($breadcrumb_items);

        $bread = $this->breadcrumb->generate();
        $this->template->set('bread', $bread);

        $this->template->add_js('js/estadistica/estadistica_edad');
        $this->template->set_flash_message(array("Se muestran las edades que tenian los estudiantes en el semestre"), "info");

        //$this->template->set('anio_actual', $anio);
        //$this->template->set('anio_inicio', $semestre);
        $this->template->set('datos', $data);
        $this->template->set('item_sidebar_active', 'ver_est_desercion_edad');
        $this->template->set('content_header', 'Ver alumnos');
        $this->template->set('content_sub_header', 'Listado de alumnos');

        $this->template->render('estadistica/desercion_edad');
    }

    public function combinar_por_semestre($activos, $inactivos, $graduados)
    {
        $semestres = [];

        // Recolectar todos los semestres únicos
        foreach ([$activos, $inactivos, $graduados] as $grupo) {
            foreach ($grupo as $item) {
                $semestres[$item['SEMESTRE']] = true;
            }
        }

        $result = [];


        foreach (array_keys($semestres) as $sem) {
            $a = $this->buscar_semestre($activos, $sem);
            $i = $this->buscar_semestre($inactivos, $sem);
            $g = $this->buscar_semestre($graduados, $sem);

            $result[] = [
                'SEMESTRE' => $sem,

                // Activos
                'ACTIVOS_TOTAL' => $a['TOTAL'],
                'ACTIVOS_MENOR_18' => $a['MENOR_18'],
                'ACTIVOS_ENTRE_18_24' => $a['ENTRE_18_24'],
                'ACTIVOS_ENTRE_25_29' => $a['ENTRE_25_29'],
                'ACTIVOS_MAYOR_30' => $a['MAYOR_30'],

                // Inactivos
                'INACTIVOS_TOTAL' => $i['TOTAL'],
                'INACTIVOS_MENOR_18' => $i['MENOR_18'],
                'INACTIVOS_ENTRE_18_24' => $i['ENTRE_18_24'],
                'INACTIVOS_ENTRE_25_29' => $i['ENTRE_25_29'],
                'INACTIVOS_MAYOR_30' => $i['MAYOR_30'],

                // Graduados
                'GRADUADOS_TOTAL' => $g['TOTAL'],
                'GRADUADOS_MENOR_18' => $g['MENOR_18'],
                'GRADUADOS_ENTRE_18_24' => $g['ENTRE_18_24'],
                'GRADUADOS_ENTRE_25_29' => $g['ENTRE_25_29'],
                'GRADUADOS_MAYOR_30' => $g['MAYOR_30'],

                // Total general
                'TOTAL' => $a['TOTAL'] + $i['TOTAL'] + $g['TOTAL']
            ];
        }

        return $result;
    }


    private function buscar_semestre($datos, $sem)
    {
        foreach ($datos as $item) {
            if ($item['SEMESTRE'] == $sem) {
                return $item;
            }
        }

        // Si no se encuentra el semestre, se devuelven ceros
        return [
            'SEMESTRE' => $sem,
            'TOTAL' => 0,
            'MENOR_18' => 0,
            'ENTRE_18_24' => 0,
            'ENTRE_25_29' => 0,
            'MAYOR_30' => 0,
        ];
    }

    public function sumar_por_semestre($datos1, $datos2)
    {
        $result = [];

        foreach ([$datos1, $datos2] as $grupo) {
            foreach ($grupo as $item) {
                $sem = $item['SEMESTRE'];

                if (!isset($result[$sem])) {
                    $result[$sem] = [
                        'SEMESTRE' => $sem,
                        'TOTAL' => 0,
                        'MENOR_18' => 0,
                        'ENTRE_18_24' => 0,
                        'ENTRE_25_29' => 0,
                        'MAYOR_30' => 0,
                    ];
                }

                $result[$sem]['TOTAL'] += $item['TOTAL'];
                $result[$sem]['MENOR_18'] += $item['MENOR_18'];
                $result[$sem]['ENTRE_18_24'] += $item['ENTRE_18_24'];
                $result[$sem]['ENTRE_25_29'] += $item['ENTRE_25_29'];
                $result[$sem]['MAYOR_30'] += $item['MAYOR_30'];
            }
        }

        return array_values($result);
    }

    public function edad_to_rangos($datos)
    {
        $result = [];

        foreach ($datos as $item) {
            $sem = $item->SEMESTRE;
            $edad = $item->EDAD;
            $count = $item->TOTAL;

            if (!isset($result[$sem])) {
                $result[$sem] = [
                    'SEMESTRE' => $sem,
                    'TOTAL' => 0,
                    'MENOR_18' => 0,
                    'ENTRE_18_24' => 0,
                    'ENTRE_25_29' => 0,
                    'MAYOR_30' => 0,
                ];
            }

            $result[$sem]['TOTAL'] += $count;

            if ($edad < 18) {
                $result[$sem]['MENOR_18'] += $count;
            } elseif ($edad >= 18 && $edad <= 24) {
                $result[$sem]['ENTRE_18_24'] += $count;
            } elseif ($edad >= 25 && $edad <= 29) {
                $result[$sem]['ENTRE_25_29'] += $count;
            } else {
                $result[$sem]['MAYOR_30'] += $count;
            }
        }

        return $result;
    }
}
