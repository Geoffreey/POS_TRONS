<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_purchase_payment_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_purchase_payment_report'));

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportPurchasePaymentController.js');

// Agregar clase de cuerpo
$document->setBodyClass('sidebar-collapse');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper" ng-controller="ReportPurchasePaymentController">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo trans('text_purchase_payment_report_title'); ?>
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
        <?php echo trans('text_purchase_payment_report_title'); ?>
      </li>
    </ol>
  </section>
  <!-- Fin del encabezado de contenido -->

  <!-- Content Start Start -->
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
          <div class="box-body">
            <div class="table-responsive">  
              <table id="report-report-list" class="table table-bordered table-striped table-hover">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-10">
                      <?php echo trans('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_date'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_type'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_invoice_id'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo trans('label_pmethod_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_note'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_amount'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
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