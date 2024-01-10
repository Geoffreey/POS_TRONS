<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'send_sms')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_send_sms'));

// Agregar script
$document->addScript('../assets/underscore/underscore.min.js');
$document->addScript('../assets/itsolution24/angular/controllers/SMSController.js');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper" ng-controller="SMSController">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header">
    <h1>
      <?php echo trans('text_sms_title'); ?>
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
        <?php echo trans('text_send_sms'); ?>
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
    
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">
          <span class="fa fa-fw fa-comment-o"></span> <?php echo trans('text_send_sms_title'); ?>
        </h3>
      </div>
      <div class="box-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs store-m15">
            <!-- <li class="active">
                <a href="#event_sms" data-toggle="tab" aria-expanded="false">
                <?php //echo trans('text_event_sms'); ?>
              </a>
            </li> -->
            <li class="active">
                <a href="#single" data-toggle="tab" aria-expanded="false">
                <?php echo trans('text_single'); ?>
              </a>
            </li>
            <li>
                <a href="#group" data-toggle="tab" aria-expanded="false">
                <?php echo trans('text_group'); ?>
              </a>
            </li>
          </ul>
          <div class="tab-content">

            <!-- Event SMS Start -->
            <!-- <div class="tab-pane active" id="event_sms">
              <?php //include('../_inc/template/sms_send_event_form.php'); ?>
            </div>  -->
            <!-- Event SMS End -->

            <!-- Single SMS Start -->
            <div class="tab-pane active" id="single">
              <?php include('../_inc/template/sms_send_form.php'); ?>
            </div> 
            <!-- Single SMS End -->

            <!-- Group SMS Start -->
            <div class="tab-pane" id="group">
              <?php include('../_inc/template/sms_send_group_form.php'); ?>
            </div> 
            <!-- Group SMS End -->

          </div>
        </div>
      </div>
      <div class="box-footer">
        <p class="text-blue"><i>*SMS sending is a time consuming task. It may take few seconds to few minutes event several minutes to acconplished. So, It is highly recommended to configure Cronjob in Linux or Schedule a task in Windows to smooth this task.</i></p>
      </div>
    </div>

  </section>
  <!--Fin del contenido-->
  
</div>
<!--Fin del contenedor de contenido-->

<?php include ("footer.php"); ?>