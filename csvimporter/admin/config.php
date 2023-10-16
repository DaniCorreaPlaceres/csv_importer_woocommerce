<?php
//Activar o desactivar configuraciones del plugin
function get_config(){
    global $wpdb;

    if (isset($_POST['config_plugin'])){
        echo 'Configuración actualizada';
        update_option('config_costo', $_POST['config_costo'], '', 'yes');
        update_option('config_beneficio', $_POST['config_beneficio'], '', 'yes');
        update_option('config_dealer', $_POST['config_dealer'], '', 'yes');

    }

    $config_costo = get_option('config_costo');
    $config_beneficio = get_option('config_beneficio');
    $config_dealer = get_option('config_dealer');


    ?>
    <h1>Configuración del Plugin CSV Importer</h1>


    <h2>Configuración</h2>
    <form  method="post" enctype="multipart/form-data">
        <input type="hidden" id="config_plugin" name="config_plugin" value="yes">
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label><input type="checkbox" name="config_dealer" id="config_dealer" value="yes" <?php echo ($config_dealer=='yes' ? 'checked' : ''); ?>> Activar ver mayorista en ficha de producto</label>
                </th>
            </tr>
            <tr>
                <th>
                    <label><input type="checkbox" name="config_costo" id="config_costo" value="yes" <?php echo ($config_costo=='yes' ? 'checked' : ''); ?>> Activar campo costo en ficha de producto</label>
                </th>
            </tr>

            <tr>
                <th>
                    <label><input type="checkbox" name="config_beneficio" id="config_beneficio" value="yes"<?php echo ($config_beneficio=='yes' ? 'checked' : ''); ?>> Activar campo beneficio en ficha de producto</label>
                </th>
            </tr>

            </tbody>
        </table>
        <?php submit_button('Actualizar') ?>
    </form>



    <?php



}