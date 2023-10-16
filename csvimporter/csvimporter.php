<?php
defined('ABSPATH') or die();
//Constante con la ruta completa del plugin
define('CSVIMPORTER_PATH',plugin_dir_path(__FILE__));
include(CSVIMPORTER_PATH . 'includes/options.php');
include(CSVIMPORTER_PATH . 'includes/functions.php');
include(CSVIMPORTER_PATH . 'admin/upload.php');
include(CSVIMPORTER_PATH . 'admin/dealers.php');
include(CSVIMPORTER_PATH . 'admin/list.php');
include(CSVIMPORTER_PATH . 'admin/config.php');
include(CSVIMPORTER_PATH . 'admin/product.php');
//include CSVIMPORTER_PATH. '/datatables/client.php';


/*
Plugin Name: CsvImporter
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Import products to your woocommerce from a csv.
Version: 0.1
Author: Daniel Correa Placeres
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2
*/


function activate()
{
    //A partir de aquí escribe todas las tareas que quieres realizar en la activación

    init_db_myplugin();
    update_option('config_costo', 'yes', '', 'yes');
    update_option('config_beneficio', 'yes', '', 'yes');
    update_option('config_dealer', 'yes', '', 'yes');

}
register_activation_hook(__FILE__,'activate');

function desactivate()
{
    //A partir de aqui escribe todas las tareas que quieres realizar en la desactivación
}
//register_activation_hook(__FILE__,'desactivate');


function init_db_myplugin() {

    // Funcion para generar las tablas necesarias del plugin
    // WP Globals
    global $table_prefix, $wpdb;
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
    $dealersTable = $table_prefix . 'csv_dealers';
    if( $wpdb->get_var( "show tables like '$dealersTable'" ) != $dealersTable ) {

        // Query - Create Table
        $sql = "CREATE TABLE `$dealersTable` (";
        $sql .= " `id` int(11) NOT NULL auto_increment, ";
        $sql .= " `name` varchar(500) NOT NULL, ";

        $sql .= " PRIMARY KEY `dealers_id` (`id`) ";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        // Include Upgrade Script
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

        // Create Table
        dbDelta( $sql );
    }

    $filesTable = $table_prefix . 'csv_files';
    if( $wpdb->get_var( "show tables like '$filesTable'" ) != $filesTable ) {

        // Query - Create Table
        $sql = "CREATE TABLE `$filesTable` (";
        $sql .= " `id` int(11) NOT NULL auto_increment, ";
        $sql .= " `file` varchar(500) NOT NULL, ";
        $sql .= " `dealer` int(11) NOT NULL, ";
        $sql .= " `date` datetime NOT NULL, ";

        $sql .= " PRIMARY KEY `csv_files_id` (`id`) ";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        // Include Upgrade Script
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

        // Create Table
        dbDelta( $sql );
    }
}

