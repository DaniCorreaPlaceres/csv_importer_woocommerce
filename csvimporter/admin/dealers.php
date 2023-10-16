<?php

function get_dealers(){
    global $wpdb;
    //Si recibimos por post un nuevo mayorista ejecutamos la funcion para agregarlo
    if (isset($_POST['dealer'])) {
        add_dealer($_POST['dealer']);
    }

    //borrar mayorista
    if (isset($_POST['delete_dealer'])){
        delete_dealer($_POST['dealer_id']);
    }
    //Obtenemos todos los mayoristas guardados
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}csv_dealers ", ARRAY_A  );



    ?>
    <h1>Listado de Mayoristas</h1>

    <?php
    //Imprimimos el listado de mayoristas
    foreach($results as $key => $value): ?>

        <form method="post" enctype="multipart/form-data">

            <p> <?php echo $value['id'].' '.$value['name']; ?> </p>
            <input type="hidden" id="dealer_id" name="dealer_id" value= "<?php echo $value['id']; ?>"/>
            <input type="hidden" id="delete_dealer" name="delete_dealer" value="0" />
            <?php submit_button('Borrar Mayorista '.$value['name'].'') ?>
        </form>

    <?php
    endforeach;
    //var_dump($results);
    ?>
    <h2>Añadir nuevo mayorista</h2>
    <form  method="post" enctype="multipart/form-data">
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="dealer">Mayorista:</label>
                </th>
                <td>

                    <input type="text" id="dealer" name="dealer" />

                </td>
            </tr>

            </tbody>
        </table>
        <?php submit_button('Añadir nuevo Mayorista') ?>
    </form>



<?php



}


