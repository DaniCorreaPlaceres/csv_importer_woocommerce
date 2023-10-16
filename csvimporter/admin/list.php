<?php

function show_product_list(){
    if (isset($_POST['delete_all_products'])){
        delete_all_products();
    }
    global $table_prefix, $wpdb;


    $customerTable = $table_prefix . 'csv_import_products';
    $count=0;

    //Obtenemos las categorias de productos creadas en la web para crear el select de categorias para la publicacion por lote
    //Obtener Categorías del WC
    /*categorias*/
    $taxonomy     = 'product_cat';
    $orderby      = 'name';
    $show_count   = 0;      // 1 for yes, 0 for no
    $pad_counts   = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no
    $title1        = '';
    $empty        = 0;

    $args = array(
        'taxonomy'     => $taxonomy,
        'orderby'      => $orderby,
        'show_count'   => $show_count,
        'pad_counts'   => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li'     => $title1,
        'hide_empty'   => $empty
    );
    $all_categories = get_categories( $args );
    $html_select='<select style="width: 300px;"  id="categoria" name="categoria[]" multiple="multiple">';
    foreach ($all_categories as $cat) {
        if($cat->category_parent == 0) {
            $category_id = $cat->term_id;
            $html_select.='<option value="'. $cat->term_id . '">' . $cat->name .'</option>';


            // echo '<br /><a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->term_id . '-' . $cat->name .'</a>';

            $args2 = array(
                'taxonomy'     => $taxonomy,
                'child_of'     => 0,
                'parent'       => $category_id,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title1,
                'hide_empty'   => $empty
            );
            $sub_cats = get_categories( $args2 );
            if($sub_cats) {
                foreach($sub_cats as $sub_category) {
                    $html_select.='<option value="'. $sub_category->term_id . '">-' . $sub_category->name .'</option>';
                    //echo  $sub_category->name ;
                    $args3 = array(
                        'taxonomy'     => $taxonomy,
                        'child_of'     => 0,
                        'parent'       => $sub_category->term_id,
                        'orderby'      => $orderby,
                        'show_count'   => $show_count,
                        'pad_counts'   => $pad_counts,
                        'hierarchical' => $hierarchical,
                        'title_li'     => $title1,
                        'hide_empty'   => $empty
                    );
                    $sub_cats2 = get_categories( $args3 );
                    if($sub_cats2) {
                        foreach($sub_cats2 as $sub_category2) {
                            $html_select.='<option value="'. $sub_category2->term_id . '">--' . $sub_category2->name .'</option>';
                            //echo  $sub_category->name ;
                        }
                    }
                }
            }




        }
    }
    $html_select.='</select>';
    $categorias= $html_select;



//Recibimos los productos seleccionados para la actualización/publicación en lote
    if (isset($_POST['product'])){

        foreach ($_POST['product'] as $i):
            /*Actualizamos la base de datos del listado*/
            $query_update_insert = "UPDATE " . $customerTable . " SET  
            pvp= '".$_POST['pvp_'.$i]."', pvo='" . $_POST['pvo_'.$i]. "' WHERE id='".$i."'";
            $wpdb->query($query_update_insert);

            //Publicamos el producto en la web
            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}csv_import_products WHERE `id`=".$i."", ARRAY_A  );
            $camps = array(
                'id' => $results[0]['id'],
                'title' => $results[0]['title'],
                'sku' => $results[0]['sku'],
                'codigo' => $results[0]['codigo'],
                'categoria' => $_POST['categoria'],
                ///'marca' => $_POST['marca'],
                'stock' => $results[0]['stock'],
                'price' => $results[0]['costo'],
                //'canon' => $_POST['canon'],
                'short_desc' => $results[0]['short_desc'],
                'long_desc' => $results[0]['long_desc'],
                'img_default' => $results[0]['img_default'],
                'img_gallery' => $results[0]['img_gallery'],
                'pvo' => $results[0]['pvo'],
                'pvp' => $results[0]['pvp'],
                'web' => "SI",

            );


            $galeria=explode(",", $camps['img_gallery']);
            $post = array(
                'post_author' => get_current_user_id(),
                'post_content' =>  $camps['long_desc'],
                'post_excerpt' =>  $camps['short_desc'],
                'post_status' => "publish",
                'post_title' =>  $camps['title'],
                'post_parent' => '',
                'post_type' => "product",
            );

            //Create post
            if ( $camps['img_default']=="" ||  $camps['img_default']==NULL) {
                $camps['img_default']='https://www.gamingcanarias.com/actualizarstock/images/no-foto.jpg';
            }
            //$_POST['foto_principal']='https://cluster.binarycanarias.com/img/cp-10100087.jpg';
            $title_image=str_replace(' ', '-',  $camps['title']);
            //$_POST['foto_principal']=str_replace('https', 'http', $_POST['foto_principal']);

            $post_id = wp_insert_post( $post, $wp_error );
            if($post_id){
                array_push($upPrp, $post_id);
                if(isset( $camps['img_default'])){
                    $image_url=    $camps['img_default'];
                    $image_name       = $image_url;
                    $upload_dir       = wp_upload_dir(); // Set upload folder
                    $image_data       = file_get_contents($image_url); // Get image data
                    $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
                    $filename         = basename( $unique_file_name ); // Create image file name

                    // Check folder permission and define file location
                    if( wp_mkdir_p( $upload_dir['path'] ) ) {
                        $file = $upload_dir['path'] . '/' . $filename;
                    } else {
                        $file = $upload_dir['basedir'] . '/' . $filename;
                    }

                    // Create the image  file on the server
                    file_put_contents( $file, $image_data );

                    // Check image file type
                    $wp_filetype = wp_check_filetype( $filename, null );

                    // Set attachment data
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title'     => sanitize_file_name( $filename ),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );

                    // Create the attachment
                    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );

                    // Include image.php
                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    // Define attachment metadata
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

                    // Assign metadata to attachment
                    wp_update_attachment_metadata( $attach_id, $attach_data );

                }
                set_post_thumbnail( $post_id, $attach_id );
                // add_post_meta($post_id, '_thumbnail_id', $attach_id);
            }

            //echo $_POST['category'];

            if(isset( $camps['categoria'])){

                $cat= $camps['categoria'];

            }
            else{

                $cat="";
            }

            if ( $camps['pvo']!="") {
                $p_price= $camps['pvo'];
            }else{
                $p_price= $camps['pvp'];
            }

            wp_set_object_terms($post_id, 'simple', 'product_type');
            $cat = array_map ('intval', $cat);
            $cat = array_unique ($cat);
            wp_set_object_terms( $post_id, $cat, 'product_cat');

            update_post_meta( $post_id, '_visibility', 'visible' );
            update_post_meta( $post_id, '_stock_status', 'instock');
            update_post_meta( $post_id, 'total_sales', '0');
            update_post_meta( $post_id, '_downloadable', 'no');
            update_post_meta( $post_id, '_virtual', 'no');
            update_post_meta( $post_id, '_regular_price',  $camps['pvp'] );
            update_post_meta( $post_id, '_sale_price',  $camps['pvo'] );
            update_post_meta( $post_id, '_purchase_note', "" );
            update_post_meta( $post_id, '_featured', "no" );
            update_post_meta( $post_id, '_weight', "" );
            update_post_meta( $post_id, '_length', "" );
            update_post_meta( $post_id, '_width', "" );
            update_post_meta( $post_id, '_height', "" );
            update_post_meta($post_id, '_sku', trim( $camps['sku']));
            update_post_meta( $post_id, '_product_attributes', array());
            update_post_meta( $post_id, '_sale_price_dates_from', "" );
            update_post_meta( $post_id, '_sale_price_dates_to', "" );
            update_post_meta( $post_id, '_price', $p_price );
            update_post_meta( $post_id, '_sold_individually', "" );
            update_post_meta( $post_id, '_manage_stock', "yes" );
            update_post_meta( $post_id, '_backorders', "no" );
            update_post_meta( $post_id, '_stock',  $camps['stock'] );
            update_post_meta( $post_id, '_download_limit', '');
            update_post_meta( $post_id, '_download_expiry', '');
            update_post_meta( $post_id, '_download_type', '');
            //update_field('coste', $_POST['costo'], $post_id);

            if(isset( $camps['img_gallery']) &&  $camps['img_gallery']!=""){
                $extimagesize= sizeof($galeria);
                $imgids= array();
                for($loop =0;$loop <= $extimagesize;$loop++){
                    //$galeria[$loop]=str_replace('https', 'http', $galeria[$loop]);
                    $image_url=   $galeria[$loop];
                    $image_name       = $image_url;
                    $upload_dir       = wp_upload_dir(); // Set upload folder
                    $image_data       = file_get_contents($image_url); // Get image data
                    $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
                    $filename         = basename( $unique_file_name ); // Create image file name

                    // Check folder permission and define file location
                    if( wp_mkdir_p( $upload_dir['path'] ) ) {
                        $file = $upload_dir['path'] . '/' . $filename;
                    } else {
                        $file = $upload_dir['basedir'] . '/' . $filename;
                    }

                    // Create the image  file on the server
                    file_put_contents( $file, $image_data );

                    // Check image file type
                    $wp_filetype = wp_check_filetype( $filename, null );

                    // Set attachment data
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title'     => sanitize_file_name( $filename ),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );

                    // Create the attachment
                    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );

                    // Include image.php
                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    // Define attachment metadata
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

                    // Assign metadata to attachment
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    array_push($imgids, $attach_id);
                }

                $join_string=implode(", ", $imgids);
                update_post_meta( $post_id, '_product_image_gallery', $join_string);
            }
            else{
                update_post_meta( $post_id, '_product_image_gallery', '');
            }





            $count++;

        endforeach;
        echo 'Productos actualizados: '. $count;
    }



    ?>
    <style>
        thead{
            font-size: 12px;
            text-transform: uppercase;
            text-align: center;
            background-color: #7b2465;
            color: #fff;
        }
        thead input {
            width: 100%;
        }
        input[name="search_id"] {
            display: none;
        }

        tbody{
            font-size: 12px;
        }
    </style>
    <form method="post" id="delete_all">
        <input type="hidden" id="delete_all_products" name="delete_all_products" value="1">
        <?php submit_button('Borrar todos los Productos') ?>

    </form>
    <form id="form_listado" name="form_listado" method="post" >
    <table id="example" class="table table-striped table-bordered compact" style="font-size: 11px; text-align: center; width:100%">
        <thead>
        <tr>
            <th>id</th>
            <th>select</th>

            <th>foto</th>
            <th>title</th>
            <th>sku</th>
            <th>marca</th>
            <th>categoria</th>
            <th>stock</th>
            <th>costo</th>
            <th>pvp</th>
            <th>pvo</th>
            <th>dealer</th>
            <th>publicar</th>
        </tr>
        </thead>

    </table>
        <p>PUBLICACION EN LOTE (PRODUCTOS SELECCIONADOS)</p>
        <P>Seleccione las categorías de la publicación en lote</P>
        <?php echo $categorias; ?>
        <?php submit_button('Publicar Productos') ?>
    </form>


    <?php

}

function client_json() {
    global $wpdb;

    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $search_subfamilia=$_POST['columns'][5]['search']['value'];
    $search_category=$_POST['columns'][6]['search']['value'];
    $search_sku=$_POST['columns'][4]['search']['value'];
    $search_descripcion=$_POST['columns'][3]['search']['value'];
    $searchQuery = " WHERE dealer != '0' ";


    $x=1;
    if ($search_subfamilia!='' OR $search_category!='' OR $search_sku!='' OR $search_descripcion!='') {
    }

    if($search_subfamilia != ''){
        $searchQuery .= "AND marca like'%".$search_subfamilia."%' ";
        $x--;
        if ($x>1) {
        }
    }

    if($search_category != ''){
        $searchQuery .= "AND categoria like'%".$search_category."%' ";
        $x--;
        if ($x>1) {
        }
    }



    if($search_sku != ''){
        $searchQuery .= "AND sku like'%".$search_sku."%' ";
        $x--;
        if ($x>1) {
        }
    }


    if($search_descripcion != ''){
        $searchQuery .= "AND title like'%".$search_descripcion."%' ";
        $x--;
        if ($x>1) {
        }
    }

    //total de resultados sin filtros
    $records = $wpdb->get_results( "SELECT count(*) as allcount FROM {$wpdb->prefix}csv_import_products ", ARRAY_A  );
    $totalRecords = $records[0]['allcount'];


    //total resultados con filtros
    $records = $wpdb->get_results( "SELECT count(*) as allcount FROM {$wpdb->prefix}csv_import_products {$searchQuery}", ARRAY_A  );

    $totalRecordwithFilter = $records[0]['allcount'];

    $empQuery = "select * from ".$wpdb->prefix."csv_import_products ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
    $empRecords = $wpdb->get_results($empQuery, ARRAY_A );
    $data = array();
    $url= admin_url('admin.php?page=csvimporter/admin/product.php');
    foreach($empRecords as $value => $row) {

        $id=$row['id'];
        $title=$row['title'];
        $sku=$row['sku'];
        $marca=$row['marca'];
        $categoria=$row['categoria'];
        $stock=$row['stock'];
        $costo=$row['costo'];
        $dealer=$row['dealer'];
        $query= "SELECT name FROM ".$wpdb->prefix."csv_dealers WHERE id=".$dealer."";
        $dealers = $wpdb->get_results($query, ARRAY_A);
        $select='<label><input type="checkbox" name="product[]" id="product" value="'.$row['id'].'"></label>';
        $img_default='<img src='.$row['img_default'].' alt="" class="" width="75" height="75">';
        $pvo='<label><input type="text" name="pvo_'.$id.'" id="pvo_'.$id.'" value="'.$row['pvo'].'"></label>';
        $pvp='<label><input type="text" name="pvp_'.$id.'" id="pvp_'.$id.'" value="'.$row['pvp'].'"></label>';
        $publicar='<a href="'.$url.'&id=' . $row['id'].'">Editar</a>';


        $data[] = array(
            "id"=>$id,
            "select"=>$select,
            "foto"=>$img_default,
            "title"=>$title,
            "sku"=>$sku,
            "marca"=>$marca,
            "categoria"=>$categoria,
            "stock"=>$stock,
            "costo"=>$costo,
            "pvp"=>$pvp,
            "pvo"=>$pvo,
            "dealer"=>$dealers[0]['name'],
            "publicar"=>$publicar
        );

    }




    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);
    // Kills WordPress execution
    wp_die();
}
// wp_ajax is a authenticated Ajax
add_action('wp_ajax_client_json', 'client_json' );
//wp_ajax_nopriv is a non-authenticated Ajax
add_action('wp_ajax_nopriv_client_json', 'client_json' );

