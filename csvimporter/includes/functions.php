<?php

//añadir nuevo mayorista
function add_dealer($dealer)
{

    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'csv_dealers',
        array(
            'name' => $dealer,

        ),
        array(
            '%s',
        )
    );

    echo '<p>¡Mayorista agregado correctamente! </p>';

}

//añadir nuevo archivo a la db
function add_file($file,$dealer,$date)
{

    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'csv_files',
        array(
            'file' => $file,
            'dealer' => $dealer,
            'date' => $date,

        ),
        array(
            '%s',
        )
    );

    echo '<p>¡Nuevo file añadido a la base de datos! </p>';

}

//Eliminar Mayorista
function delete_dealer($dealer)
{

    global $wpdb;
    $wpdb->delete(
        $wpdb->prefix . 'csv_dealers',      // table name with dynamic prefix
        ['id' => $dealer],                       // which id need to delete
        ['%d'],                             // make sure the id format
    );

    $wpdb->delete(
        $wpdb->prefix . 'csv_import_products',      // table name with dynamic prefix
        ['dealer' => $dealer],                       // which id need to delete
        ['%d'],                             // make sure the id format
    );

    echo '<p>¡Mayorista borrado correctamente! </p>';
}

//Limpiar tabla de productos
function delete_all_products()
{

    global $table_prefix, $wpdb;
    $customerTable = $wpdb->prefix . 'csv_import_products';
    $sql = "DROP TABLE IF EXISTS ".$customerTable."";
    $wpdb->query($sql);

    // Customer Table
    $customerTable = $table_prefix . 'csv_import_products';
    // Create Customer Table if not exist
    if( $wpdb->get_var( "show tables like '$customerTable'" ) != $customerTable ) {

        // Query - Create Table
        $sql = "CREATE TABLE `$customerTable` (";
        $sql .= " `id` int(11) NOT NULL auto_increment, ";
        $sql .= " `title` varchar(500), ";
        $sql .= " `sku` varchar(500), ";
        $sql .= " `codigo` varchar(500), ";
        $sql .= " `marca` varchar(500), ";
        $sql .= " `categoria` varchar(255), ";
        $sql .= " `stock` varchar(255), ";
        $sql .= " `impuesto` varchar(255), ";
        $sql .= " `precio` varchar(255), ";
        $sql .= " `canon` varchar(255), ";
        $sql .= " `costo` varchar(255), ";
        $sql .= " `short_desc` longtext, ";
        $sql .= " `long_desc` longtext, ";
        $sql .= " `img_default` varchar(255), ";
        $sql .= " `img_gallery` text, ";
        $sql .= " `pvp` varchar(255), ";
        $sql .= " `pvo` varchar(255), ";
        $sql .= " `web` varchar(255), ";
        $sql .= " `fecha` datetime, ";
        $sql .= " `if_update` int(11), ";
        $sql .= " `id_web` varchar(60), ";
        $sql .= " `dealer` int(11), ";
        $sql .= " PRIMARY KEY `KEY` (`id`), ";
        $sql .= " UNIQUE `UNIQUE` (`title`,`sku`, `dealer`)";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        // Include Upgrade Script
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

        // Create Table
        dbDelta( $sql );
    }

    echo '<p>¡Todos los productos borrados! </p>';
}

//añadir campo personalizado a la ficha de producto
$config_costo = get_option('config_costo');
$config_beneficio = get_option('config_beneficio');
$config_dealer = get_option('config_dealer');
if ($config_costo=='yes'){
    add_action('woocommerce_product_options_general_product_data', 'add_costo_field');
    add_action('woocommerce_process_product_meta', 'add_costo_save');

}
if ($config_dealer=='yes'){
    add_action('woocommerce_product_options_general_product_data', 'add_dealer_field');
    add_action('woocommerce_process_product_meta', 'add_dealer_save');


}
if ($config_beneficio=='yes'){
    add_action('woocommerce_product_options_general_product_data', 'add_beneficio_field');
    add_action('woocommerce_process_product_meta', 'add_beneficio_save');

}


function add_dealer_field(){
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_dealer_csv',
            'placeholder' => '',
            'label' => __('Mayorista', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';


}

function add_costo_field(){
    global $woocommerce, $post;

    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_costo_csv',
            'placeholder' => '',
            'label' => __('Costo', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';

}

function add_beneficio_field(){
    global $woocommerce, $post;

    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_beneficio',
            'placeholder' => '',
            'label' => __('Beneficio', 'woocommerce'),
            'value' => 0,
            'desc_tip' => 'true'
        )
    );

    echo '</div>';
//funciones javascript para cambiar el valor del beneficio en función del costo y pvp
    echo'<script type="text/javascript">
            jQuery(document).ready(function(){
                calcularBeneficio();         
            });
                document.getElementById(\'_costo_csv\').addEventListener("change", function(){
                    calcularBeneficio();
                    }, false);
                document.getElementById(\'_regular_price\').addEventListener("change", function(){
                    calcularBeneficio();
                    }, false);  
              function calcularBeneficio(){   
            var venta = document.getElementById(\'_regular_price\').value;
                var compra = document.getElementById(\'_costo_csv\').value;
                var beneficio = parseFloat(venta) - parseFloat(compra);
                document.getElementById(\'_beneficio\').value = beneficio.toFixed(2);
              }
            </script>';
}

function add_costo_save($post_id)
{

    $woocommerce_custom_product_text_field_costo = $_POST['_costo_csv'];
    if(isset($woocommerce_custom_product_text_field_costo)){
        update_post_meta($post_id, '_costo_csv', esc_attr($woocommerce_custom_product_text_field_costo));
    }

}

function add_dealer_save($post_id)
{

    $woocommerce_custom_product_text_field = $_POST['_dealer_csv'];
    if(isset($woocommerce_custom_product_text_field)){
        update_post_meta($post_id, '_dealer_csv', esc_attr($woocommerce_custom_product_text_field));
    }

}
function add_beneficio_save($post_id)
{

    $woocommerce_custom_product_text_field_beneficio = $_POST['_beneficio'];
    if(isset($woocommerce_custom_product_text_field_beneficio)){

        update_post_meta($post_id, '_beneficio', esc_attr($woocommerce_custom_product_text_field_beneficio));
    }

}