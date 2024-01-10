<?php
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_stock_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_stock_report'));
$document->setBodyClass('sidebar-collapse');

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportStockController.js');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php");
?>
<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper">

	<!-- Inicio del encabezado de contenido -->
	<section class="content-header">
		<?php include ("../_inc/template/partials/apply_filter.php"); ?>
	  <h1>
	    <?php echo trans('text_stock_report_title'); ?>
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
	    	<?php echo trans('text_stock_report_title'); ?>
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
			<?php if (user_group_id() == 1 || has_permission('access', 'read_stock_report')) : ?>
		      	<div id="reprot_stock_parent_wrapper" class="col-md-12">
			        <div class="box box-info">
			          <div class="box-header with-border">
			            <h3 class="box-title">
			              <?php echo trans('text_stock_report'); ?>
			            </h3>
			            <!--Box Tools End-->
			            <div class="box-tools pull-right">
							<select id="sup_id" class="select2" name="sup_id">
								<option value="">
									--- <?php echo sprintf(trans('text_view_all'), 'Stock Report'); ?> ---
								</option>
								<?php foreach (get_suppliers() as $supplier): ?>
								<option value="<?php echo $supplier['sup_id'];?>">
							    	<?php echo $supplier['sup_name']; ?>
							    </option>
							<?php endforeach; ?>
							</select>
			            </div>
			          </div>
			          <div class="box-body">
			            <?php include('../_inc/template/partials/report_stock.php'); ?>
			          </div>
			        </div>
		    	</div>
		    <?php endif; ?>
	    </div>
	</section>
	<!--Fin del contenido-->

</div>
<!--Fin del contenedor de contenido-->

<?php include ("footer.php"); ?>