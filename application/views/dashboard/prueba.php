<div class="box box-primary">
    <div class="box-header">
        <h2 class="box-title"> caja</h2>
    </div>
    <div class="box-body">
        <p> texto texto </p>

    
        <a href="<?php echo site_url('/test'); ?>">probar db</a>



        <form id="miFormulario" data-url="<?php echo site_url('alumno/lista'); ?>">

            <input type="text" id="num1" >
            <input type="text" id="num2" >

            <input type="button" id="enviarBtn" class="btn btn-lg btn-danger" value="Enviar">

        </form>

    

        <div id="resultado">


        </div>


        <?php

$data = array( 'alumno'=> array(),
    'datos_personales' =>array(),
    'datos_per' => array(),
    'nota' => array(),
    'carrera' => array(),
    'facultad' => array(),
    'matriculado' => array(),
    'tipo_carrera' => array()
);


foreach($listado as $fil){
    switch($fil->TABLE_NAME){
        case "ALUMNO":
            $data['alumno'][]=$fil;
            break;
        case "DATOS_PERSONALES":
            $data['datos_personales'][]=$fil;
            break;
        case "DATOS_PER":
            $data['datos_per'][]=$fil;
            break;
        case "NOTA":
            $data['nota'][]=$fil;
            break;
        case "CARRERA":
            $data['carrera'][]=$fil;
            break;
        case "facultad":
            $data['facultad'][]=$fil;
            break;
        case "MATRICULADO":
            $data['matriculado'][]=$fil;
            break;
        case "TIPO_CARRERA":
            $data['tipo_carrera'][]=$fil;
            break;
        
    }
}


echo "<pre>"; // Use <pre> for better readability in HTML
//var_dump($data);
echo "</pre>";
// Sample array (replace this with the array you get from your function)


foreach($data as $table => $columns){
    echo $table;
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<thead>';
    echo '<tr>';
    //echo '<th>Table Name</th>';
    echo '<th>Column Name</th>';
    echo '<th>Data Type</th>';
    echo '<th>Data Length</th>';
    echo '<th>Nullable</th>';
    echo '<th>Primary Key</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach($columns as $row){
        echo '<tr>';
        //echo '<td>' . htmlspecialchars($row->TABLE_NAME) . '</td>';
        echo '<td>' . htmlspecialchars($row->COLUMN_NAME) . '</td>';
        echo '<td>' . htmlspecialchars($row->DATA_TYPE) . '</td>';
        echo '<td>' . htmlspecialchars($row->DATA_LENGTH) . '</td>';
        echo '<td>' . htmlspecialchars($row->NULLABLE) . '</td>';
        echo '<td>' . htmlspecialchars($row->IS_PRIMARY_KEY) . '</td>';
        echo '</tr>';

    }

    echo '</tbody>';
    echo '</table>';

}

?>


    </div>
</div>