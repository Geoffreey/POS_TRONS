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
$document->addScript('../assets/itsolution24/angular/controllers/ReportSellCategoryWiseController.js');

// Agregar clase de cuerpo
$document->setBodyClass('sidebar-collapse');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper" ng-controller="ReportSellCategoryWiseController">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo trans('text_selling_report_title'); ?>
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
        <?php echo trans('text_selling_report_title'); ?>
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
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo trans('text_selling_report_sub_title'); ?>
            </h3>
            <div class="box-tools pull-right">
              
                <div class="btn-group">
                  <button type="button" class="btn btn-info">
                    <span class="fa fa-filter"></span> 
                    <?php if (current_nav() == 'report_sell_itemwise') : ?>
                      <?php echo trans('button_itemwise'); ?>
                    <?php elseif (current_nav() == 'report_sell_categorywise') : ?>
                      <?php echo trans('button_categorywise'); ?>
                    <?php elseif (current_nav() == 'report_sell_supplierwise') : ?>
                      <?php echo trans('button_supplierwise'); ?>
                    <?php else: ?>
                      <?php echo trans('button_filter'); ?>
                    <?php endif; ?>
                  </button>
                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                      <li>
                        <a href="report_sell_itemwise.php">
                          <?php echo trans('button_itemwise'); ?>
                        </a>
                      </li>
                      <li>
                        <a href="report_sell_categorywise.php">
                          <?php echo trans('button_categorywise'); ?>
                        </a>
                      </li>
                      <li>
                        <a href="report_sell_supplierwise.php">
                          <?php echo trans('button_supplierwise'); ?>
                        </a>
                      </li>
                   </ul>
                </div>

            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                  $print_columns = '0,1,2,3,4,5';
                  if (user_group_id() != 1) {
                    if (! has_permission('access', 'show_purchase_price')) {
                      $print_columns = str_replace('4,', '', $print_columns);
                    }
                  }
                  $hide_colums = "";
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
                      <?php echo trans('label_category_name');?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_quantity'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_purchase_price'); ?>
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
                      <?php echo trans('label_category_name');?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_quantity'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_purchase_price'); ?>
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