<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_loan')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_loan'));

// Agregar script
$document->addScript('../assets/itsolution24/angular/modals/LoanPayModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanViewModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanCreateModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanEditModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanDeleteModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/LoanController.js');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper" ng-controller="LoanController">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo trans('text_loan_title'); ?>
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
        <?php if (isset($request->get['box_state']) && $request->get['box_state']=='open'): ?>
          <a href="loan.php"><?php echo trans('text_loan_title'); ?></a>  
        <?php else: ?>
          <?php echo trans('text_loan_title'); ?>  
        <?php endif; ?>
      </li>
      <?php if (isset($request->get['box_state']) && $request->get['box_state']=='open'): ?>
        <li class="active">
          <?php echo trans('text_add'); ?> 
        </li>
      <?php endif; ?>
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
    
    <?php if (user_group_id() == 1 || has_permission('access', 'take_loan')) : ?>
      <div class="box box-info<?php echo create_box_state(); ?>">
        <div class="box-header with-border">
          <h3 class="box-title">
            <span class="fa fa-fw fa-plus"></span> <?php echo trans('text_take_loan_title'); ?>
          </h3>
          <button type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
            <i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-plus'; ?>"></i>
          </button>
        </div>
      
        <?php if (isset($error_message)): ?>
          <div class="alert alert-danger">
            <p>
              <span class="fa fa-warning"></span> 
              <?php echo $error_message ; ?>
            </p>
          </div>
        <?php elseif (isset($success_message)): ?>
          <div class="alert alert-success">
            <p>
              <span class="fa fa-check"></span> 
              <?php echo $success_message ; ?>
            </p>
          </div>
        <?php endif; ?>

        <!--Agregar Expend, crear formulario-->
        <?php include('../_inc/template/loan_take_form.php'); ?>
        
      </div>
    <?php endif; ?>

    <div class="row">

      <!--Inicio de la sección Lista de gastos-->
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo trans('text_loan_list_title'); ?>
            </h3>
            <div class="box-tools pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                  <span class="fa fa-filter"></span> 
                  <?php if (isset($request->get['type']) && $request->get['type'] == 'paid') : ?>
                    <?php echo trans('text_paid'); ?>
                  <?php elseif (isset($request->get['type']) && $request->get['type'] == 'due') : ?>
                    <?php echo trans('text_due'); ?>
                  <?php else : ?>
                    <?php echo trans('text_all'); ?>
                  <?php endif; ?>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="loan.php">
                        <?php echo trans('button_all'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="loan.php?type=paid">
                        <?php echo trans('button_paid'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="loan.php?type=due">
                        <?php echo trans('button_due'); ?>
                      </a>
                    </li>
                 </ul>
              </div>
            </div>
          </div>
          <div class='box-body'>     
            <?php
              $hide_colums = "";
              if (user_group_id() != 1) {
                if (! has_permission('access', 'loan_pay')) {
                  $hide_colums .= "8,";
                }
                if (! has_permission('access', 'view_loan')) {
                  $hide_colums .= "9,";
                }
                if (! has_permission('access', 'update_loan')) {
                  $hide_colums .= "10,";
                }
                if (! has_permission('access', 'delete_loan')) {
                  $hide_colums .= "11,";
                }
              }
            ?> 
            <div class="table-responsive">                     
              <table id="loan-loan-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo trans('label_serial_no'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo trans('label_date'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_loan_from'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_basic_amount'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_interest'); ?> (%)
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_payable'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_paid'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_due'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_pay'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_view'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_edit'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_delete'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo trans('label_serial_no'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo trans('label_date'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo trans('label_loan_from'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_basic_amount'); ?>
                    </th>
                    <th class="w-10">&nbsp;</th>
                    <th class="w-10">
                      <?php echo trans('label_payable'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_paid'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_due'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_pay'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_view'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_edit'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo trans('label_delete'); ?>
                    </th>
                  </tr>
                </tfoot>
              </table>    
            </div>
          </div>
        </div>
      </div>
       <!--Fin de sección Lista de gastos-->
    </div>
  </section>
  <!--Fin del contenido-->
</div>
<!--Fin del contenedor de contenido-->

<?php include ("footer.php"); ?>