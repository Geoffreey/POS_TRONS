<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_expense')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_expense'));

// Agregar script
$document->addScript('../assets/itsolution24/angular/modals/ExpenseViewModal.js');
$document->addScript('../assets/itsolution24/angular/modals/ExpenseEditModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/ExpenseController.js');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header" ng-controller="ExpenseController">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo trans('text_expense_title'); ?>
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
          <a href="expense.php"><?php echo trans('text_expense_title'); ?></a>  
        <?php else: ?>
          <?php echo trans('text_expense_title'); ?>  
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
    
    <?php if (user_group_id() == 1 || has_permission('access', 'create_expense')) : ?>
      <div class="box box-info<?php echo create_box_state(); ?>">
        <div class="box-header with-border">
          <h3 class="box-title">
            <span class="fa fa-fw fa-plus"></span> <?php echo trans('text_new_expense_title'); ?>
          </h3>
          <button type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
            <i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-plus'; ?>"></i>
          </button>
        </div>

        <!--Agregar Expend, crear formulario-->
        <?php include('../_inc/template/expense_create_form.php'); ?>
        
      </div>
    <?php endif; ?>

    <div class="row">

      <!--Inicio de la sección Lista de gastos-->
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo trans('text_expense_list_title'); ?>
            </h3>
          </div>
          <div class='box-body'>     
            <?php
              $hide_colums = "";
              if (user_group_id() != 1) {
                if (! has_permission('access', 'read_expense')) {
                  $hide_colums .= "5,";
                }
                if (! has_permission('access', 'update_expense')) {
                  $hide_colums .= "6,";
                }
                if (! has_permission('access', 'delete_expense')) {
                  $hide_colums .= "7,";
                }
              }
            ?> 
            <div class="table-responsive">                     
              <table id="expense-expense-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo trans('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_title'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_category_name'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_amount'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo trans('label_fecha_gasto'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo trans('label_created_at'); ?>
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
                    <th class="w-20">
                      <?php echo trans('label_title'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo trans('label_category_name'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo trans('label_amount'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo trans('label_fecha_gasto'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo trans('label_created_at'); ?>
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