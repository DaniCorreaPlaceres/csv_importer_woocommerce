<?php
function public_product()
{
    global $table_prefix, $wpdb;

    if (isset($_GET['id'])){
        $id=$_GET['id'];
        $siguiente=$id+1;
        $anterior=$id-1;
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}csv_import_products WHERE `id`=".$id."", ARRAY_A  );
        $title = $results[0]['title'];
        $sku = $results[0]['sku'];
        $stock = $results[0]['stock'];




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


        //obtener imagenes de la galería
        $fotos_galeria='';
        $array_galeria=explode(",", $results[0]['img_gallery']);
        for($a=0; $a<sizeof($array_galeria);$a++)
        {
            $fotos_galeria.='<div class="col">';
            $fotos_galeria.='<img style="width: 90px;" class="img-fluid" src="'.$array_galeria[$a].'">';
            $fotos_galeria.='</div>';
        }

        ?>
        <div class="row">
            <div class="col" style="text-align: right;">
                <form style="display: inline-flex;" method="post" >
                    <input type="hidden" id="id" name="id" value="<?php echo $anterior; ?>">
                    <button type="submit" class="btn btn-secondary my-2 my-sm-0">PRODUCTO ANTERIOR</button>
                </form>
                <form style="display: inline-flex;" method="post" >
                    <input type="hidden" id="id" name="id" value="<?php echo $siguiente; ?>">
                    <button type="submit" class="btn btn-info my-2 my-sm-0">SIGUIENTE PRODUCTO</button>
                </form>
            </div>
        </div>
        <div class="row">
        <form id="form_producto" name="form_producto" method="post" >
            <div class="row">
                <div class="col">
                    <label for="title">Nombre producto:</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="" value="<?php echo $title; ?>">
                    <label for="title">SKU:</label>
                    <input type="text" id="sku" name="sku" class="form-control" placeholder="" value="<?php echo $sku; ?>" readonly>
                    <label for="title">Código:</label>
                    <input type="text" id="codigo" name="codigo" class="form-control" placeholder="" value="<?php echo $results[0]['codigo']; ?>" readonly>
                    <div class="row">
                        <div class="col">
                            <label for="title">Costo:</label>
                            <input type="text" id="costo" name="costo" class="form-control" placeholder="" value="<?php echo $results[0]['costo']; ?>">
                        </div>
                        <div class="col">
                            <label for="title">P.V.P: </label>
                            <input type="text" id="pvp" name="pvp" class="form-control" placeholder="" value="<?php echo $results[0]['pvp']; ?>">
                        </div>
                        <div class="col">
                            <label for="title">P.V.P OFERTA:</label>
                            <input type="text" id="pvo" name="pvo" class="form-control" placeholder="" value="<?php echo $results[0]['pvo']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="title">Stock:</label>
                            <input type="text" id="stock" name="stock" class="form-control" placeholder="" value="<?php echo $results[0]['stock']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <?php if ($results[0]['web']!='SI') {
                            # code...
                            ?>
                            <div class="col">
                                <label for="title">Categorías en la web:</label>
                                <?php echo $categorias; ?>
                            </div>
                        <?php }?>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="title">Descripción corta:</label>
                            <textarea class="form-control" id="short_desc" name="short_desc" rows="3"><?php echo utf8_decode($results[0]['short_desc']); ?></textarea>

                        </div>
                    </div>

                </div>

                <div class="col">
                    <label for="title">Imagen principal:</label>
                    <input type="text" id="img_default" name="img_default" class="form-control" placeholder="" value="<?php echo $results[0]['img_default']; ?>">
                    <div class="row">


                        <img class="img-fluid" style="width: 200px;" src=<?php echo $results[0]['img_default']; ?>>
                    </div>
                    <label for="title">Imagenes de la galería (url separadas por coma):</label>
                    <textarea class="form-control" id="img_gallery" name="img_gallery" rows="3"><?php echo $results[0]['img_gallery']; ?></textarea>

                    <div class="row">


                        <?php echo $fotos_galeria; ?>
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col">
                    <label for="title">Descripción larga:</label>
                    <textarea class="form-control" id="long_desc" name="long_desc" rows="3"><?php echo utf8_decode($results[0]['long_desc']); ?></textarea>
                </div>
            </div>


            <input type="hidden" id="id_publicar" name="id_publicar" value="<?php echo $results[0]['id'];?>">
            <input type="hidden" id="web" name="web" value="<?php echo $results[0]['web'];?>">

            <?php submit_button('Publicar Producto') ?>
        </form>
        </div>

<?php
if (isset($_POST['id_publicar']))
{
    $camps = array(
        'id' => $_POST['id_publicar'],
        'title' => $_POST['title'],
        'sku' => $_POST['sku'],
        'codigo' => $_POST['codigo'],
        'categoria' => $_POST['categoria'],
        ///'marca' => $_POST['marca'],
        'stock' => $_POST['stock'],
        'price' => $_POST['costo'],
        //'canon' => $_POST['canon'],
        'short_desc' => $_POST['short_desc'],
        'long_desc' => $_POST['long_desc'],
        'img_default' => $_POST['img_default'],
        'img_gallery' => $_POST['img_gallery'],
        'pvo' => $_POST['pvo'],
        'pvp' => $_POST['pvp'],
        'web' => "SI",

    );

    $customerTable = $table_prefix . 'csv_import_products';

    $query_update_insert = "UPDATE " . $customerTable . " SET  title= '".$camps['title']."', stock='" . $camps['stock'] . "', precio='" . $camps['price'] . "', costo='" . $camps['price'] . "', web='" . $camps['web'] . "', pvp='" . $camps['pvp'] . "', pvo='" . $camps['pvo'] . "' WHERE id='".$camps['id']."'";


     $wpdb->query($query_update_insert);

    echo "Publicado:". $camps['title'];

    $galeria=explode(",", $_POST['img_gallery']);
    $post = array(
        'post_author' => get_current_user_id(),
        'post_content' => $_POST['long_desc'],
        'post_excerpt' => $_POST['short_desc'],
        'post_status' => "publish",
        'post_title' => $_POST['title'],
        'post_parent' => '',
        'post_type' => "product",
    );

    //Create post
    if ($_POST['img_default']=="" || $_POST['img_default']==NULL) {
        $_POST['img_default']='https://www.gamingcanarias.com/actualizarstock/images/no-foto.jpg';
    }
    //$_POST['foto_principal']='https://cluster.binarycanarias.com/img/cp-10100087.jpg';
    $title_image=str_replace(' ', '-', $_POST['title']);
    //$_POST['foto_principal']=str_replace('https', 'http', $_POST['foto_principal']);

    $post_id = wp_insert_post( $post, $wp_error );
    if($post_id){
        array_push($upPrp, $post_id);
        if(isset($_POST['img_default'])){
            $image_url=   $_POST['img_default'];
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

    if(isset($_POST['categoria'])){

        $cat=$_POST['categoria'];

    }
    else{

        $cat="";
    }

    if ($_POST['pvo']!="") {
        $p_price=$_POST['pvo'];
    }else{
        $p_price=$_POST['pvp'];
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
    update_post_meta( $post_id, '_regular_price', $_POST['pvp'] );
    update_post_meta( $post_id, '_sale_price', $_POST['pvo'] );
    update_post_meta( $post_id, '_purchase_note', "" );
    update_post_meta( $post_id, '_featured', "no" );
    update_post_meta( $post_id, '_weight', "" );
    update_post_meta( $post_id, '_length', "" );
    update_post_meta( $post_id, '_width', "" );
    update_post_meta( $post_id, '_height', "" );
    update_post_meta($post_id, '_sku', trim($_POST['sku']));
    update_post_meta( $post_id, '_product_attributes', array());
    update_post_meta( $post_id, '_sale_price_dates_from', "" );
    update_post_meta( $post_id, '_sale_price_dates_to', "" );
    update_post_meta( $post_id, '_price', $p_price );
    update_post_meta( $post_id, '_sold_individually', "" );
    update_post_meta( $post_id, '_manage_stock', "yes" );
    update_post_meta( $post_id, '_backorders', "no" );
    update_post_meta( $post_id, '_stock', $_POST['stock'] );
    update_post_meta( $post_id, '_download_limit', '');
    update_post_meta( $post_id, '_download_expiry', '');
    update_post_meta( $post_id, '_download_type', '');
    //update_field('coste', $_POST['costo'], $post_id);

    if(isset($_POST['img_gallery']) && $_POST['img_gallery']!=""){
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

    //$_POST['title']='"'.$_POST['title'].'"';
    // $_POST['foto_principal']='"'.$_POST['foto_principal'].'"';
    //$_POST['fotos_galeria']='"'.$_POST['fotos_galeria'].'"';
    //$insert_long=$_POST['long_desc'];
   // $insert_short=$_POST['short_desc'];

    echo "Producto Añadido y Actualizado";



}




    }else{
        echo 'Debe seleccionar un producto del listado.';
    }


}