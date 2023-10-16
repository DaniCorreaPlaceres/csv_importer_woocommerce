jQuery(document).ready(function(){
    jQuery('#example thead tr').clone(true).appendTo( '#example thead' );
    jQuery('#example thead tr:eq(1) th').each( function (i) {
        var title = jQuery(this).text();
        jQuery(this).html( '<input type="search" name="search_'+title+'" placeholder="Search '+title+'" />' );

        jQuery( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );


    var table = jQuery('#example').DataTable({

        orderCellsTop: true,
        fixedHeader: true,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            url:'admin-ajax.php?action=client_json',
           /* dataSrc:""*/
           // data: {action:'client_json'},
           // type: 'POST'
        },
        'columns': [
            { data: 'id' },
            { data: 'select' },
            { data: 'foto' },
            { data: 'title' },
            { data: 'sku' },
            { data: 'marca' },
            { data: 'categoria' },
            { data: 'stock' },
            { data: 'costo' },
            { data: 'pvp' },
            { data: 'pvo' },
            { data: 'dealer' },
            { data: 'publicar' },

        ]

    });

});