<?php
$codigos_seleccionados = array();
foreach ($carrera_select as $carrera) {
    if (isset($carrera->COD_CARRERA)) {
        $codigos_seleccionados[] = $carrera->COD_CARRERA;
    }
}
?>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Seleccione carreras a mostrar</h3>
      
    </div>
    <div class="box-body">
        <table class="table table-hover table-bordered table-striped table-condensed text-center no-margin">
            <thead>
                <tr>
                    <th>COD_CARRERA</th>
                    <th>NOMCORTO</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrera_todas as $carrera): ?>
                    <tr>
                        <td><?php echo $carrera->COD_CARRERA; ?></td>
                        <td><?php echo $carrera->NOMCORTO; ?></td>
                        <td>
                            <input type="checkbox" class="carrera-check"
                                value="<?php echo $carrera->COD_CARRERA; ?>"
                                <?php echo in_array($carrera->COD_CARRERA, $codigos_seleccionados) ? 'checked' : ''; ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>




<script>
    const url = '<?= base_url("carrera/vista") ?>';

    document.querySelectorAll('.carrera-check').forEach(function(checkbox) {
        checkbox.addEventListener('change', function(event) {
            const checkedBoxes = document.querySelectorAll('.carrera-check:checked');

            // Si el usuario intenta desmarcar el último checkbox marcado, revertimos la acción
            if (checkedBoxes.length === 0) {
                alert('Debe seleccionar al menos una carrera.');
                event.target.checked = true; // Vuelve a marcar el checkbox que se intentó desmarcar
                return; // No sigue con el fetch
            }

            // Si pasa la validación, enviamos las seleccionadas
            const seleccionadas = Array.from(checkedBoxes)
                .map(cb => cb.value)
                .join('-');

            const formData = new FormData();
            formData.append('carreras', seleccionadas);

            fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta del servidor:', data);
                })
                .catch(error => {
                    console.error('Error al enviar:', error);
                });
        });
    });
</script>