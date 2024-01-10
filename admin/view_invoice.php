<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_sell_invoice')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

if (!isset($request->get['invoice_id'])) { 
  redirect('invoice.php');
}
$invoice_id = $request->get['invoice_id'];

// MODELO DE FACTURA
$invoice_model = registry()->get('loader')->model('invoice');
$invoice_info = $invoice_model->getInvoiceInfo($invoice_id);
if (!$invoice_info) {
  redirect('invoice.php');
}

$document->setTitle(trans('text_invoice') . ' - ' . $invoice_id);

// CONTRAER LA BARRA LATERAL
$document->setBodyClass('sidebar-collapse');
$document->setBodyClass('invoice-page');

// Agregar clase de cuerpo
$document->setBodyClass('sidebar-collapse');

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/InvoiceViewController.js'); 

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php"); 
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <h1>
      <?php echo trans('text_invoice_title'); ?> &larr; <?php echo $invoice_id ; ?>
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
      <li>
        <a href="invoice.php">
          <?php echo trans('text_invoice'); ?>
        </a>
      </li>
      <li class="active">
        <?php echo $invoice_id ; ?>
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
        <div class="box box-info">
        	<div class='box-body'>  

            <div id="invoice" class="row" ng-controller="InvoiceViewController">
              <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">  
                <?php include('../_inc/template/partials/invoice_view.php'); ?>
              </div>
            </div>

    		  </div> 
        </div>
      </div>
    </div>
  </section>
  <!-- Fin del contenido-->

</div>
<!--Fin del contenedor de contenido-->

<?php include ("footer.php"); ?>