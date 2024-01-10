<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_income_and_expense_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_income_and_expense'));
$document->setBodyClass('sidebar-collapse');

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportIncomeController.js');
$document->addScript('../assets/itsolution24/angular/controllers/ReportExpenseController.js');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<style type="text/css">
.income-expense-row:after {
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
      <?php echo trans('text_income_and_expense_title'); ?>
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
        <?php echo trans('text_income_and_expense_title'); ?>
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
    
    <div class="box box-default" id="income-expense-report">
      <div class="box-header bg-info">
        <h3 class="box-title">
          <?php if (from()) : ?>
            <?php echo trans('text_date'); ?>: <?php echo date("j M Y", strtotime(from()));?>
          <?php else: ?>
            <?php echo trans('text_date'); ?>: <?php echo date("j M Y", time());?>
          <?php endif; ?>
        </h3>
        <a class="pull-right pointer no-print" onClick="window.printContent('income-expense-report', {title:'<?php echo trans('title_income_and_expense');?>', 'headline':'<?php echo trans('title_income_and_expense');?>', screenSize:'fullScreen'});">
          <i class="fa fa-print"></i> <?php echo trans('text_print');?>
        </a>
      </div>
      <div class="income-expense-row">
        <div class="row">
          <div class="col-md-6" ng-controller="ReportIncomeController">
            <div class="box-header">
              <h3 class="box-title">
                <?php echo trans('title_income'); ?>
              </h3>
            </div>
            <div class='box-body'>
              <?php include('../_inc/template/partials/report_income.php'); ?>
            </div>
          </div>
          <div class="col-md-6 expense-col" ng-controller="ReportExpenseController">
            <div class="box-header">
              <h3 class="box-title">
                <?php echo trans('title_expense'); ?>
              </h3>
            </div>
            <div class='box-body'>     
              <?php include('../_inc/template/partials/report_expense.php'); ?>
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