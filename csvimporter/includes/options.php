<?php
defined('ABSPATH') or die();

add_action('admin_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js');

    wp_register_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css' );
    wp_enqueue_style( 'bootstrap-css' );
    wp_register_style( 'datatable-css', '//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css' );
    wp_enqueue_style( 'datatable-css' );
    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js' );
    wp_enqueue_script( 'datatable-js', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js' );


    /*ajax datatable*/
    wp_register_script('dcms_miscript',plugin_dir_url(__FILE__). '/js/data_list.js' );
    wp_localize_script('dcms_miscript', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

    wp_enqueue_script('jquery');
    wp_enqueue_script('dcms_miscript');
}







// Top level menu del plugin
function admin_menu()
{
    add_menu_page( 'CSV IMPORTER', 'CSV IMPORTER', 'manage_options', CSVIMPORTER_PATH . '/admin/upload.php', 'my_custom');
    add_submenu_page(CSVIMPORTER_PATH . '/admin/upload.php','Configuración','Configuración','manage_options',CSVIMPORTER_PATH . '/admin/config.php', 'get_config');
    add_submenu_page(CSVIMPORTER_PATH . '/admin/upload.php','Upload CSV','Upload CSV','manage_options',CSVIMPORTER_PATH . '/admin/upload.php', 'my_custom');
    add_submenu_page(CSVIMPORTER_PATH . '/admin/upload.php','Mayoristas','Mayoristas','manage_options',CSVIMPORTER_PATH . '/admin/dealers.php', 'get_dealers');
    add_submenu_page(CSVIMPORTER_PATH . '/admin/upload.php','Listado de Productos','Listado de Productos','manage_options',CSVIMPORTER_PATH . '/admin/list.php', 'show_product_list');
    add_submenu_page(CSVIMPORTER_PATH . '/admin/upload.php','Producto','Producto','manage_options',CSVIMPORTER_PATH . '/admin/product.php', 'public_product');

}
// El hook admin_menu ejecuta la funcion admin_menu
add_action( 'admin_menu',  'admin_menu');




