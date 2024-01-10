<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_tax_overview_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_tax_overview_report'));

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportTaxOverviewController.js');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper" ng-controller="ReportTaxOverviewController">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <h1>
      <?php echo trans('text_tax_overview_report_title'); ?>
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
        <?php echo trans('text_tax_overview_report_title'); ?>
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
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs store-m15">
            <li class="active">
                <a href="#sell_tax" data-toggle="tab" aria-expanded="false">
                <?php echo trans('text_sell_tax'); ?>
              </a>
            </li>
            <li class="">
                <a href="#purchase_tax" data-toggle="tab" aria-expanded="false">
                <?php echo trans('text_purchase_tax'); ?>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="sell_tax">
              <div class="table-responsive">  
                <?php
                    $hide_colums = "";
                  ?>  
                <table id="tax-tax-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
                  <thead>
                    <tr class="bg-gray">
                      <th class="w-20" >
                        <?php echo trans('label_tax_percent'); ?>
                      </th>
                      <th class="w-20" >
                        <?php echo trans('label_count'); ?>
                      </th>
                      <th class="w-20">
                        <?php echo trans('label_subtotal'); ?>
                      </th>
                      <th class="w-20">
                        <?php echo trans('label_tax_amount'); ?>
                      </th>
                      <th class="w-20">
                        <?php echo trans('label_total'); ?>
                      </th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr class="bg-gray">
                      <th class="w-20" >
                        <?php echo trans('label_tax_percent'); ?>
                      </th>
                      <th class="w-20" >
                        <?php echo trans('label_count'); ?>
                      </th>
                      <th class="w-20">
                        <?php echo trans('label_subtotal'); ?>
                      </th>
                      <th class="w-20">
                        <?php echo trans('label_tax_amount'); ?>
                      </th>
                      <th class="w-20">
                        <?php echo trans('label_total'); ?>
                      </th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="purchase_tax">
              <p>Unavailable Now...</p>
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