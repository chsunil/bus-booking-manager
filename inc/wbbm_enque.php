<?php 
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

// Enqueue Scripts for admin dashboard
add_action('admin_enqueue_scripts', 'wbbm_bus_admin_scripts');
function wbbm_bus_admin_scripts() {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-core');   
    wp_enqueue_style('wbbm-clocklet-style',plugin_dir_url( __DIR__ ).'css/clocklet.css',array());
    wp_enqueue_style('mep-admin-style',plugin_dir_url( __DIR__ ).'css/admin_style.css',array());
    wp_enqueue_style('mep-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());
    wp_enqueue_style('font-awesome-css-cdn', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.2.0/css/all.min.css", null, 1);
    wp_enqueue_style('datatable-css-cdn', "https://cdn.datatables.net/1.11.0/css/jquery.dataTables.min.css", null, 1);


 wp_enqueue_script('wbbm-select2-lib',plugin_dir_url( __DIR__ ).'js/select2.full.min.js',array('jquery','jquery-ui-core'),1,true); 

 wp_register_script('multidatepicker-wbbm', 'https://cdn.rawgit.com/dubrox/Multiple-Dates-Picker-for-jQuery-UI/master/jquery-ui.multidatespicker.js', array('jquery'), 1, true);
 wp_enqueue_script('multidatepicker-wbbm');

 wp_enqueue_script('wbbm-clocklet-lib',plugin_dir_url( __DIR__ ).'js/clocklet.js',array('jquery','jquery-ui-core'),1,true);
wp_enqueue_script('gmap-scripts',plugin_dir_url( __DIR__ ).'js/mkb-admin.js',array('jquery','jquery-ui-core'),1,true);

    // datatable scripts
// wp_register_script('datatable-wbbm', 'https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js', array('jquery'), 1, true);
// wp_enqueue_script('datatable-wbbm');
// wp_register_script('datatable-btn-wbbm', 'https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js', array('jquery'), 1, true);   
// wp_enqueue_script('datatable-btn-wbbm');
wp_enqueue_script('datatable', 'https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js', array('jquery'));
wp_enqueue_script('dt_buttons', 'https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js', array('jquery', 'datatable'));

wp_enqueue_script('dt_zip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js', array('jquery', 'datatable'));
wp_enqueue_script('dt_pdf', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js', array('jquery', 'datatable'));
wp_enqueue_script('dt__vsf', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js', array('jquery', 'datatable'));
wp_enqueue_script('dt_btn', 'https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js', array('jquery', 'datatable'));
wp_enqueue_script('dt__print', 'https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js', array('jquery', 'datatable'));

}

function wbbm_add_admin_scripts( $hook ) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'wbbm_bus' === $post->post_type ) { 
             wp_enqueue_style('mep-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());
        
        }
    }
}
add_action( 'admin_enqueue_scripts', 'wbbm_add_admin_scripts', 10, 1 );




// Datepicker code for admin dashboard load in footer section
add_action('admin_footer','wbbm_admin_footer_script',10,99);
add_action('wp_footer','wbbm_admin_footer_script',10,99);
function wbbm_admin_footer_script(){
  ?>
<script type="text/javascript">
jQuery(document).ready(function($){
  
      jQuery('#myTable').DataTable({
        // "scrollY": 500,
        // "scrollX": true,
         order: [[ 0, "desc" ]],
          scrollX: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "footerCallback": function ( row, data, start, end, display ) {
          
          this.api().columns([0, 1, 2, 3, 4, 5, 7]).every( function () {
            var column = this;
                var select = $('<select><option value=""></option></select>')
                .appendTo( $(column.footer()).empty() )
                .on( 'change', function () {
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );

                    column
                        .search( val ? '^'+val+'$' : '', true, false )
                        .draw();
                } );
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            
        } )
          
          var api = this.api(), data;
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column(6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            jQuery( api.column( 6 ).footer() ).html(
                ''+pageTotal +' ('+ total +' Total)'
            );
    
      
    }    }, );
    
      
      jQuery( "#j_date" ).datepicker({
        dateFormat: "<?php echo wbbm_convert_datepicker_dateformat(); ?>",
        minDate:0
      });  
      
      jQuery( "#r_date" ).datepicker({
        dateFormat: "<?php echo wbbm_convert_datepicker_dateformat(); ?>",
        minDate:0
      });

      jQuery( "#ja_date" ).datepicker({
        dateFormat: "yy-mm-dd"
      });
    });
</script>
  <?php
}





// Select2 code for admin dashboard load in footer section
add_action('wp_footer','wbbm_admin_footer_select_2_script',10,99);
function wbbm_admin_footer_select_2_script(){
  ?>
<script type="text/javascript">
jQuery(document).ready(function($){
      jQuery(".select2, #boarding_point, #drp_point").select2();
    });
</script>
  <?php
}







// Enqueue Scripts for frontend
add_action('wp_enqueue_scripts', 'wbbm_bus_enqueue_scripts');
function wbbm_bus_enqueue_scripts() {
   wp_enqueue_script('jquery');
   wp_enqueue_script('jquery-ui-datepicker');
   wp_enqueue_script('jquery-ui-core');   
   wp_enqueue_script('jquery-ui-accordion');
   wp_enqueue_style('wbbm-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());
    wp_enqueue_script('wbbm-select2-lib',plugin_dir_url( __DIR__ ).'js/select2.full.min.js',array('jquery','jquery-ui-core'),1,false);    

   wp_enqueue_style('wbbm-bus-style',plugin_dir_url( __DIR__ ).'css/style.css',array());


   wp_enqueue_style ('font-awesome-css-cdn',"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css",null,1);

   wp_enqueue_style ('wbbm-select2-style-cdn',"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css",null,1);

    wp_enqueue_script('mage_style',plugin_dir_url( __DIR__ ).'js/mage_style.js',array('jquery'),1,true);
    wp_enqueue_style('mage_css',plugin_dir_url( __DIR__ ).'css/mage_css.css',array());
}

// Ajax Issue
add_action('wp_head','wbbm_ajax_url',5);
add_action('admin_head','wbbm_ajax_url',5);
function wbbm_ajax_url() {
    ?>
    <script type="text/javascript">
        var wbtm_ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}