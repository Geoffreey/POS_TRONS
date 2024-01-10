<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_profit_and_loss_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_profit_and_loss'));
$document->setBodyClass('sidebar-collapse');

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportLossController.js');
$document->addScript('../assets/itsolution24/angular/controllers/ReportProfitController.js');

$from = from();
$to = to();
if (!$from) {
  $from = date('Y-m-d');
  $to = date('Y-m-d');
}

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<style type="text/css">
.loss-profit-row:after {
  content: "";
  position: absolute;
  left: 50%;
  top: 0;
  width: 2px;
  height: 100%;
  background-color: #ECF0F5;
}
.select2-container {
  width: 50px;
}
</style>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo trans('text_profit_and_loss_title'); ?>
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
        <?php echo trans('text_profit_and_loss_title'); ?>
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
    
    <div class="box box-default">
      <div class="box-header bg-info">
        <h3 class="box-title">
          <?php echo trans('text_profit_and_loss_details_title'); ?> 
          <?php if (from()) : ?>
            (<?php echo date("j M Y", strtotime(from()));?>)
          <?php else: ?>
            (<?php echo date("j M Y", time());?>)
          <?php endif; ?>
        </h3>
      </div>
      <div class="loss-profit-row">
        <div class="row">
          <div class="col-md-6 loss-col" ng-controller="ReportLossController">
            <div class="box-header">
              <h3 class="box-title">
                <?php echo trans('text_loss_title'); ?>
              </h3>
            </div>
            <div class='box-body'>
              <?php include('../_inc/template/partials/report_loss.php'); ?>
            </div>
          </div>

          <div class="col-md-6" ng-controller="ReportProfitController">
            <div class="box-header">
              <h3 class="box-title">
                <?php echo trans('text_profit_title'); ?>
              </h3>
            </div>
            <div class='box-body'>     
              <?php include('../_inc/template/partials/report_profit.php'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="box box-default">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <div class="table-responsive">
            <table class="table table-bordered table-striped mt-20">
              <tbody>
                <tr>
                  <td class="w-50 bg-gray text-right"><?php echo trans('label_total_profit'); ?></td>
                  <td class="w-50 text-left"><?php echo currency_format(get_total_profit($from, $to));?></td>
                </tr>
                <tr>
                  <td class="w-50 bg-gray text-right"><?php echo trans('label_total_loss'); ?></td>
                  <td class="w-50 text-left"><?php echo currency_format(get_total_loss($from, $to));?></td>
                </tr>
                <tr>
                  <td class="w-50 bg-gray text-right"><?php echo trans('label_net_profit'); ?></td>
                  <td class="w-50 text-left"><?php echo currency_format(get_total_profit($from, $to) - get_total_loss($from, $to));?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!--Fin del contenido-->
</div>
<!--Fin del contenedor de contenido-->

<?php include ("footer.php"); ?>