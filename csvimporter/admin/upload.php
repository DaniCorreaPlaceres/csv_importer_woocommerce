<?php
// Include Upgrade Script
require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

function my_custom()
{
    //echo WP_PLUGIN_URL ;

    upload();
    $bytes = apply_filters('import_upload_size_limit', wp_max_upload_size());
    $size = size_format($bytes);
    $upload_dir = wp_upload_dir();
    global $wpdb;
    $dealers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}csv_dealers ", ARRAY_A);
    if (!isset($_GET['id']) && !isset($_POST['p2']) && !isset($_POST['update_list'])) {
        ?>
        <h1>Subir un nuevo CSV</h1>
        <!-- Form to handle the upload - The enctype value here is very important -->
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                <tr>
                    <th>
                        <label for="upload">Selecciona el CSV:</label>
                    </th>
                    <td>
                        <input type="file" id="csv_products" name="csv_products" size="25"/>
                        <input type="hidden" name="action" value="save"/>
                        <input type="hidden" name="p2" value="1"/>
                        <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>"/>
                        <small><?php printf(__('Maximum size: %s'), $size); ?></small>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="dealer">Mayorista:</label>
                    </th>
                    <td>

                        <select name="dealer">
                            <option value="">-----------------</option>
                            <?php
                            //formamos el select de los mayoristas
                            foreach ($dealers as $key => $value):
                                echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                            endforeach;
                            ?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <th><label><input type="checkbox" name="delete_old" id="delete_old" value="yes"> ¿Borrar anteriores productos de este mayorista?</label></th>
                </tr>


                <tr>
                    <th><label>% de impuesto (IVA o IGIC) *Se calculará al precio de cada producto (Ejemplo:
                            21): </label><br/></th>
                    <td><input type="text" name="iva" id="iva" value="" placeholder="" size="5"/></td>
                </tr>
                <tr>
                    <th><label>Delimitado por: </label><br/></th>
                    <td><input type="text" name="delimiter" id="delimiter" value=";" placeholder="," size="2"/></td>
                </tr>

                </tbody>
            </table>
            <?php submit_button('Subir') ?>
        </form>
        <?php
    }
}

function upload()
{
    // First check if the file appears on the _FILES array
    if (isset($_FILES['csv_products'])) {
        $pdf = $_FILES['csv_products'];

        // Use the wordpress function to upload
        // test_upload_pdf corresponds to the position in the $_FILES array
        // 0 means the content is not associated with any other posts
        $uploaded = media_handle_upload('csv_products', 0);

        // Error checking using WP functions
        if (is_wp_error($uploaded)) {
            echo "Error uploading file: " . $uploaded->get_error_message();
            //echo "<br>".$_POST['delimiter'];
        } else {
            echo "File upload successful!";
            $date = date("Y-m-d H:i:s");
            // echo "<br>".$_POST['delimiter'];
            $file = get_attached_file($uploaded);
            update_option('csv_upload', $file, '', 'yes');
            update_option('dealer', $_POST['dealer'], '', 'yes');
            if (isset($_POST['delete_old'])){
                update_option('delete_old', $_POST['delete_old'], '', 'yes');

            }else{
                update_option('delete_old', 'no', '', 'yes');

            }
            update_option('delimeter', $_POST['delimiter'], '', 'yes');
            update_option('iva', $_POST['iva'], '', 'yes');
            $csv_file = get_option('csv_upload');
            $dealer = get_option('dealer');

            add_file($csv_file, $dealer, $date);
            // The nested array to hold all the arrays
            $the_big_array = [];
            // $i=0;

            $counter = 0;
            // Open the file for reading
            if (($h = fopen("{$csv_file}", "r")) !== FALSE) {

                while (!feof($h)) {
                    if ($counter === 2)
                        break;

                    $data = fgetcsv($h, 0, "{$_POST['delimiter']}");
                    $data = array_map("utf8_encode", $data);
                    $the_big_array[] = $data;
                    ++$counter;
                }

                fclose($h);
            }


            //$csv_file= get_option('csv_upload');
            // var_dump($the_big_array);
            if ($_POST['p2']) {
                ?>
                <style>
                    .wp-core-ui select {
                        width: 100px;
                    }
                </style>
                <div class="col-12">
                    <p>Ejemplo de los datos incluidos en el CSV (deberás elegir que dato corresponde con los campos
                        requeridos)</p>
                    <table id="table_id" class="display compact"
                           style="font-size: 11px; text-align: center; width:100%">
                        <thead>
                        <tr>
                            <?php
                            foreach ($the_big_array[0] as $key => $value):
                                echo '<th>' . $value . '</th>'; //close your tags!!
                            endforeach;
                            ?>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <?php
                            foreach ($the_big_array[1] as $key => $value):
                                echo '<td>' . substr($value, 0, 50) . '</td>'; //close your tags!!
                            endforeach;
                            ?>

                        </tr>
                        </tbody>


                    </table>

                </div>


                <div class="col-12">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="update_list" value="1"/>
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <th>
                                    <label for="title">TITULO</label>
                                </th>
                                <th>
                                    <label for="sku">SKU</label>
                                </th>
                                <th>
                                    <label for="codigo">CÓDIGO</label>
                                </th>
                                <th>
                                    <label for="categoria">CATEGORÍA</label>
                                </th>
                                <th>
                                    <label for="marca">MARCA</label>
                                </th>
                                <th>
                                    <label for="stock">STOCK</label>
                                </th>
                                <th>
                                    <label for="price">PRECIO</label>
                                </th>
                                <th>
                                    <label for="canon">CANON</label>
                                </th>
                                <th>
                                    <label for="short_desc">DESCRIPCIÓN CORTA</label>
                                </th>
                                <th>
                                    <label for="long_desc">DESCRIOCIÓN LARGA</label>
                                </th>
                                <th>
                                    <label for="img_default">IMAGEN PRINCIPAL</label>
                                </th>
                                <th>
                                    <label for="img_gallery">GALERIA DE IMÁGENES</label>
                                </th>


                            </tr>
                            <tr>
                                <td>
                                    <select name="title">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="sku">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>

                                <td>
                                    <select name="codigo">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="categoria">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="marca">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="stock">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="price">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="canon">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="short_desc">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="long_desc">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="img_default">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="img_gallery">
                                        <option value="clear">-----------------</option>
                                        <?php
                                        foreach ($the_big_array[0] as $key => $value):
                                            echo '<option value="' . $key . '">' . $value . '</option>'; //close your tags!!
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                            </tr>


                            </tbody>
                        </table>
                        <?php submit_button('Actualizar listado') ?>
                    </form>
                </div>
                <script>
                    jQuery(document).ready(function () {
                        jQuery('#table_id').DataTable({
                            searching: false,
                            paging: false,
                            scrollX: true,
                            info: false
                        });
                    });
                </script>
                <?php
            }

            //test_convert($uploaded);
        }
    }

    if (isset($_POST['update_list'])) {
        ?>
        <?php
        global $table_prefix, $wpdb;
        $fecha = date("Y-m-d H:i:s");

        //Aqui recorremos el csv para actualizar el listado con los datos enviados
        //Tenemos que mapear los datos del csv con los campos seleccionados en el punto anterior
        $camps_map = array(
            'title' => $_POST['title'],
            'sku' => $_POST['sku'],
            'codigo' => $_POST['codigo'],
            'categoria' => $_POST['categoria'],
            'marca' => $_POST['marca'],
            'stock' => $_POST['stock'],
            'price' => $_POST['price'],
            'canon' => $_POST['canon'],
            'short_desc' => $_POST['short_desc'],
            'long_desc' => $_POST['long_desc'],
            'img_default' => $_POST['img_default'],
            'img_gallery' => $_POST['img_gallery'],

        );

        //var_dump($camps_map);

        $csv_file = get_option('csv_upload');
        $dealer = get_option('dealer');
        $delimeter = get_option('delimeter');
        $delete_old = get_option('delete_old');

        if ($delete_old=="yes"){
            $sql_delete="DELETE FROM `wp_csv_import_products` WHERE `dealer`='".$dealer."'";
            $wpdb->query($sql_delete);
        }

        //Abrimos el csv y lo recorremos entero para guardar los datos en un array
        // The nested array to hold all the arrays
        $the_big_array = [];


        // Open the file for reading
        if (($h = fopen("{$csv_file}", "r")) !== FALSE) {

            while (($data = fgetcsv($h, 0, "{$delimeter}")) !== FALSE) {
                // Each individual array is being pushed into the nested array
                //$data = array_map("utf8_encode", $data); //added
                $the_big_array[] = $data;
            }

            fclose($h);
        }

        //Actualizar el listado
        //Utilizar utf8_encode en cada campo para solucionar problemas de las tildes.

        $actualizados = 0;
        $nuevos = 0;
        //$query_update_insert="";
        $existentes = "";
        $impuesto = get_option('iva');
        //$wpdb->show_errors();
        $customerTable = $table_prefix . 'csv_import_products';
        //recorremos el array desde el 1 para saltar la linea 0 que corresponde con los titulos
        for ($i = 1; $i < sizeof($the_big_array); $i++) {
            //set_time_limit(120);
            $query_update_insert = "";
            //extraemos los datos del excel uno a uno
            //controlar cuando un campo mapeado viene vacio el valor tiene que ser vacio
            if ($camps_map['title'] != "clear") {
                $title = addslashes(utf8_encode($the_big_array[$i][$camps_map['title']]));
            } else {
                $title = "";
            }
            if ($camps_map['sku'] != "clear") {
                $sku = $the_big_array[$i][$camps_map['sku']];
            } else {
                $sku = "";
            }
            if ($camps_map['codigo'] != "clear") {
                $codigo = $the_big_array[$i][$camps_map['codigo']];
            } else {
                $codigo = "";
            }
            if ($camps_map['categoria'] != "clear") {
                $categoria = utf8_encode($the_big_array[$i][$camps_map['categoria']]);
            } else {
                $categoria = "";
            }
            if ($camps_map['marca'] != "clear") {
                $marca = addslashes(utf8_encode($the_big_array[$i][$camps_map['marca']]));
            } else {
                $marca = "";
            }
            if ($camps_map['stock'] != "clear") {
                $stock_total = $the_big_array[$i][$camps_map['stock']];
            } else {
                $stock_total = "";
            }
            if ($camps_map['price'] != "clear") {
                $precio = $the_big_array[$i][$camps_map['price']];
            } else {
                $precio = "";
            }
            if ($camps_map['canon'] != "clear") {
                $canon = $the_big_array[$i][$camps_map['canon']];
            } else {
                $canon = "";
            }
            if ($camps_map['short_desc'] != "clear") {
                $short_desc = addslashes(utf8_encode($the_big_array[$i][$camps_map['short_desc']]));
            } else {
                $short_desc = "";
            }
            if ($camps_map['long_desc'] != "clear") {
                $long_desc = addslashes(utf8_encode($the_big_array[$i][$camps_map['long_desc']]));
            } else {
                $long_desc = "";
            }
            if ($camps_map['img_default'] != "clear") {
                $foto_principal = addslashes($the_big_array[$i][$camps_map['img_default']]);
            } else {
                $foto_principal = "";
            }
            if ($camps_map['img_gallery'] != "clear") {
                $fotos_galeria = addslashes($the_big_array[$i][$camps_map['img_gallery']]);
            } else {
                $fotos_galeria = "";
            }

            if ($sku==""){$sku="sin_sku_".$i;}
            if ($title==""){$title="sin_nombre_".$i;}
            if ($codigo==""){$codigo="sin_codigo_".$i;}
            if ($categoria==""){$categoria="sin_categoria_".$i;}
            if ($marca==""){$marca="sin_marca_".$i;}


            //$precio=str_replace(",",".", $the_big_array[$i][$camps_map['price']]);

            //$canon=str_replace(",",".", $the_big_array[$i][$camps_map['canon']]);

            $precio = number_format(floatval($precio), 2, '.', '');
            $canon=floatval($canon);

            //Añadimos el campo de % de impuesto para calcular el precio de costo con el impuesto incluido

            if ($impuesto == "" || $impuesto == "0") {
                $impuesto = 100;
            }
            $precio_igic = (floatval($precio) * floatval($impuesto) / 100) + floatval($precio);
            //el precio con impuesto lo aplicamos al costo
            $costo = number_format($precio_igic, 2, '.', '');
            $liquidacion = 0;
            $fecha_promo = 0;
            $prox_llegada = 0;



            $existe = wc_get_product_id_by_sku($sku);

            //comprobamos si el producto existe en la web y sacamos su id en la web
            if ($existe != 0 || $existe != NULL) {

                $web = "SI";
                $product = wc_get_product($existe);
                $precio_venta = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                if ($sale_price == $precio_venta) {
                    $sale_price = "";
                }
                $short_desc = $product->get_short_description();
                $long_desc = $product->get_description();
                $id_web = $existe;
            } else {
                $web = "NO";
                $precio_venta = "";
                $sale_price = "";
                $id_web = 0;
            }



            $query_update_insert = "insert into " . $customerTable . " (title,sku,codigo,marca,categoria,stock,impuesto,precio,canon,costo,short_desc,long_desc,img_default,img_gallery,web,`fecha`,dealer,id_web) values 
   ('" . utf8_encode($title) . "','" . $sku . "','" . $codigo . "','" . $marca . "','" . $categoria . "','" . $stock_total . "','" . $impuesto . "','" . $precio . "','" . $canon . "','" . $costo . "','" . $short_desc . "','" . $long_desc . "','" . $foto_principal . "','" . $fotos_galeria . "','" . $web . "','" . $fecha . "','" . $dealer . "','" . $id_web . "') 
   on duplicate key update title= '".$title."', stock='" . $stock_total . "',impuesto='".$impuesto."', precio='" . $precio . "',canon='" . $canon . "', costo='" . $costo . "', web='" . $web . "', id_web= '" . $id_web . "',`fecha`= '" . $fecha . "', `if_update`= '1';";



            $wpdb->query($query_update_insert);

            $actualizados++;





        }

        echo "Productos procesados:" . $actualizados;


    }


}


?>