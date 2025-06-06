<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_sell_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_sell_report'));

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportSellItemWiseControllerMonth.js');

// Agregar clase de cuerpo
$document->setBodyClass('sidebar-collapse');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper" ng-controller="ReportSellItemWiseController">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <?php 
      include ("../_inc/template/partials/apply_filter.php"); 
    ?>
    <h1>
      REPORTE DE VENTA POR ARTICULO MENSUAL
      <small>
        <?php echo store('name'); ?>
      </small>
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="dashboard.php">
          <i class="fa fa-dashboard"></i>
           <?php echo trans('text_dashboard'); ?>
         </a>
       </li>
      <li class="active">
        REPORTE DE VENTA POR ARTICULO MENSUAL
      </li>
    </ol>
  </section>
  <!-- Fin del encabezado de contenido -->

  <!--Inicio de contenido-->
  <section class="content">

    <?php if(DEMO) : ?>
    <div class="box">
      <div class="box-body">
        <div class="alert alert-info mb-0">
          <p><span class="fa fa-fw fa-info-circle"></span> <?php echo $demo_text; ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php 
    $year = from() ? date('Y', strtotime(from())) : year();
    $month = from() ? date('m', strtotime(from())) : month();
    if(isset($_REQUEST['month'])){
      $month=$_REQUEST['month'];
    }
    $days_in_month = get_total_day_in_month();
    ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              VENTA POR ARTICULO MENSUAL
            </h3>
            <select id='selectMonthChange'>
              <option value='01' <?= ($month=='01') ? 'selected' : null; ?>>Enero</option>
              <option value='02' <?= ($month=='02') ? 'selected' : null; ?>>Febrero</option>
              <option value='03' <?= ($month=='03') ? 'selected' : null; ?>>Marzo</option>
              <option value='04' <?= ($month=='04') ? 'selected' : null; ?>>Abril</option>
              <option value='05' <?= ($month=='05') ? 'selected' : null; ?>>Mayo</option>
              <option value='06' <?= ($month=='06') ? 'selected' : null; ?>>Junio</option>
              <option value='07' <?= ($month=='07') ? 'selected' : null; ?>>Julio</option>
              <option value='08' <?= ($month=='08') ? 'selected' : null; ?>>Agosto</option>
              <option value='09' <?= ($month=='09') ? 'selected' : null; ?>>Septiembre</option>
              <option value='10' <?= ($month=='10') ? 'selected' : null; ?>>Octubre</option>
              <option value='11' <?= ($month=='11') ? 'selected' : null; ?>>Noviembre</option>
              <option value='12' <?= ($month=='12') ? 'selected' : null; ?>>Diciembre</option>
            </select>
            
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                  $print_columns = '0,1,2,3,4,5,6';
                  if (user_group_id() != 1) {
                    if (! has_permission('access', 'show_purchase_price')) {
                      $print_columns = str_replace('4,', '', $print_columns);
                    }
                  }
                  $hide_colums = "1,";//"4,";
                  if (user_group_id() != 1) {
                    if (!has_permission('access', 'show_purchase_price')) {
                      $hide_colums .= "4,";
                    }
                  }
                ?>
              <table id="report-report-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>" data-print-columns="<?php echo $print_columns;?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo trans('label_serial_no'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_created_at'); ?>
                    </th>
                    <th class="w-30">
                      <?php echo trans('label_product_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_quantity'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_purchase_price'); ?>
                    </th>
                    <th class="w-20">
                      Descuentos
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_selling_price'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo trans('label_serial_no'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_created_at'); ?>
                    </th>
                    <th class="w-30">
                      <?php echo trans('label_product_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_quantity'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_purchase_price'); ?>
                    </th>
                    <th class="w-20">
                      Descuentos
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_selling_price'); ?>
                    </th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--Fin del contenido-->

</div>
<!--Fin del contenedor de contenido-->

<?php include ("footer.php"); ?>