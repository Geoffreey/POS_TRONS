<?php
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_collection_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_collection_report'));
$document->setBodyClass('sidebar-collapse');

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportCollectionController.js');
$document->addScript('../assets/itsolution24/angular/modals/UserInvoiceDetailsModal.js');
$document->addScript('../assets/itsolution24/angular/modals/DueCollectionDetailsModal.js');

// Agregar clase de cuerpo
$document->setBodyClass('sidebar-collapse');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php");
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper">

  <!--  Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo trans('text_collection_report_title'); ?>
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
        <?php echo trans('text_collection_report_title'); ?>
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
      <?php if (user_group_id() == 1 || has_permission('access', 'read_collection_report')) : ?>
          <!-- Collection Report Start -->
          <div id="collection-report" class="col-md-12">
            <div class="box box-info">
              <div class="box-header with-border" style="padding: 12px 10px;">
                <h3 class="box-title">
                  <?php echo trans('text_collection_report'); ?>
                </h3>
                <div class="box-tools pull-right">
                  <div class="btn-group" style="max-width:280px;">
                      <div class="input-group">
                        <div class="input-group-addon no-print" style="padding: 2px 8px; border-right: 0;">
                          <i class="fa fa-users" id="addIcon" style="font-size: 1.2em;"></i>
                        </div>
                        <select id="user_id" class="form-control" name="user_id" >
                          <option value=""><?php echo trans('text_select'); ?></option>
                          <?php foreach (get_users() as $the_user) : ?>
                            <option value="<?php echo $the_user['id'];?>">
                            <?php echo $the_user['username'];?>
                          </option>
                        <?php endforeach;?>
                        </select>
                        <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                          <i class="fa fa-search" id="addIcon" style="font-size: 1.2em;"></i>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
              <div class="dashboard-widget box-body">
                <?php include('../_inc/template/partials/report_collection.php'); ?>
              </div>
            </div>
          </div>
          <!--Collection Report End -->
          <?php endif; ?>

      </div>
  </section>
  <!--Fin del contenido-->

</div>
<!--Fin del contenedor de contenido-->

<?php include ("footer.php"); ?>