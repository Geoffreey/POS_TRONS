<?php
ob_start();
session_start();
include ("../_init.php");
include ("../const.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_sell_list')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_invoice'));

// Agregar script
$document->addScript('../assets/itsolution24/angular/modals/InstallmentPaymentModal.js');
$document->addScript('../assets/itsolution24/angular/modals/InstallmentViewModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/InvoiceController.js');

// CONTRAER LA BARRA LATERAL
$document->setBodyClass('sidebar-collapse');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php");
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper" ng-controller="InvoiceController">
	
	<!-- Inicio del encabezado de contenido -->
	<section class="content-header">
		<?php include ("../_inc/template/partials/apply_filter.php"); ?>
		<h1>
		    <?php echo trans('text_sell_list_title'); ?>
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
		    	<?php echo trans('text_sell_list_title'); ?>
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
		      		<div class="box-header">
				        <h3 class="box-title">
				        	<?php echo trans('text_invoices'); ?>
				        </h3>
				        <div class="box-tools pull-right">
				        	<div class="btn-group" style="max-width:700px;">
				                <div class="input-group">
				                  <div class="input-group-addon no-print" style="padding: 2px 8px; border-right: 0;">
				                    <i class="fa fa-users" id="addIcon" style="font-size: 1.2em;"></i>
				                  </div>
				                  <select id="customer_id" class="form-control" name="customer_id" >
				                    <option value="null"><?php echo trans('text_select'); ?></option>
				                    <?php foreach (get_customers() as $the_customer) : ?>
				                      <option value="<?php echo $the_customer['customer_id'];?>">
				                      <?php echo $the_customer['customer_name'].' - '.$the_customer['customer_mobile'];?>
				                    </option>
				                  <?php endforeach;?>
				                  </select>
								  <div class="input-group-addon no-print" style="padding: 2px 8px; border-right: 0;">
				                    <i class="fa fa-truck" id="addIcon" style="font-size: 1.2em;"></i>
				                  </div>
								  <select id="currier" class="form-control" name="currier" >
				                    <option value="null"><?php echo trans('text_select'); ?></option>
				                    <?php foreach (Currier as $k=>$v)  : ?>
				                    <option value="<?= $k; ?>">
				                      <?= $v; ?>
				                    </option>
				                  	<?php endforeach;?>
				                  </select>
								  <div class="input-group-addon no-print" style="padding: 2px 8px; border-right: 0;">
				                    <i class="fa fa-truck" id="addIcon" style="font-size: 1.2em;"></i>
				                  </div>
								  <select id="estadoEnvio" class="form-control" name="estadoEnvio" >
				                    <option value="null"><?php echo trans('text_select'); ?></option>
				                    <?php foreach (EstadoEnvio as $k=>$v)  : ?>
				                    <option value="<?= $k; ?>">
				                      <?= $v; ?>
				                    </option>
				                  	<?php endforeach;?>
				                  </select>
								  <div class="input-group-addon no-print" style="padding: 2px 8px; border-right: 0;">
				                    <i class="fa fa-thumbs-up" id="addIcon" style="font-size: 1.2em;"></i>
				                  </div>
								  <select id="social" class="form-control" name="social" >
				                    <option value="null"><?php echo trans('text_select'); ?></option>
				                    <?php foreach (Social as $k=>$v)  : ?>
				                    <option value="<?= $k; ?>">
				                      <?= $v; ?>
				                    </option>
				                  	<?php endforeach;?>
				                  </select>
				                  <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
				                    <i class="fa fa-search" id="addIcon" style="font-size: 1.2em;"></i>
				                  </div>
				                </div>
				            </div>
			                <div class="btn-group">
				                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
				                	<span class="fa fa-fw fa-filter"></span> 
				                  	<?php if(isset($request->get['type'])) : ?>
				                  		<?php echo trans('text_'.$request->get['type']); ?>
					                <?php else : ?>
					                	<?php echo trans('button_filter'); ?>
					                <?php endif; ?>
				                    &nbsp;<span class="caret"></span>
				                </button>
								<button id="btn-fix-invoices" class="btn btn-sm btn-warning" style="margin-left:10px;">
    								<i class="fa fa-wrench"></i> Reparar facturas pagadas mal marcadas
								</button>
				                <ul class="dropdown-menu" role="menu">
				                	<li>
				                    	<a href="invoice.php<?php echo $query_string ? $query_string.'&' : '?';?>">
				                    		<?php echo trans('button_today_invoice'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="invoice.php<?php echo $query_string ? $query_string.'&' : '?';?>type=all_invoice">
				                    		<?php echo trans('button_all_invoice'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="invoice.php<?php echo $query_string ? $query_string.'&' : '?';?>type=due">
				                    		<?php echo trans('button_due_invoice'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="invoice.php<?php echo $query_string ? $query_string.'&' : '?';?>type=all_due">
				                    		<?php echo trans('button_all_due_invoice'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="invoice.php<?php echo $query_string ? $query_string.'&' : '?';?>type=paid">
				                    		<?php echo trans('button_paid_invoice'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="invoice.php<?php echo $query_string ? $query_string.'&' : '?';?>type=inactive">
				                    		<?php echo trans('button_inactive_invoice'); ?>
				                    	</a>
				                    </li>
				                 </ul>
			                </div>
			            </div>
				     </div>
			      	<div class='box-body'>  
						<div class="table-responsive"> 
						<?php
				            $hide_colums = "";
				            if (user_group_id() != 1) {
				            	if (! has_permission('access', 'sell_payment')) {
				                $hide_colums .= "4,";
				              }
				              if (! has_permission('access', 'create_sell_return')) {
				                $hide_colums .= "5,";
				              }
				               if (! has_permission('access', 'read_sell_invoice')) {
				                $hide_colums .= "6,";
				              }
				              if (! has_permission('access', 'update_sell_invoice_info')) {
				                $hide_colums .= "7,";
				              }
				              if (! has_permission('access', 'delete_sell_invoice')) {
				                $hide_colums .= "8,";
				              }
				            }
				          ?>  

						  <table id="invoice-invoice-list"  class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
						    <thead>
						      	<tr class="bg-gray">
							        <th class="w-20">
							        	<?php echo trans('label_invoice_id'); ?>
							        </th>
							        <th class="w-20">
							        	<?php echo trans('label_datetime'); ?>
							        </th>
							        <th class="w-20">
							        	<?php echo trans('label_customer_name'); ?>
							        </th>
									<th class="w-7">
							        	<?php //echo trans('label_status'); ?>
										TIPO DE ENVIO
							        </th>
									<th class="w-7">
										ESTADO ENVIO
							        </th>
									<th class="w-7">
							        	<?php //echo trans('label_status'); ?>
										RED SOCIAL
							        </th>
									<th class="w-7">
							        	Monto
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_status'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_pay'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_return'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_view'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_edit'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_delete'); ?>
							        </th>
						      	</tr>
						    </thead>
						     <tfoot>
			               		<tr class="bg-gray">
							        <th class="w-20">
							        	<?php echo trans('label_invoice_id'); ?>
							        </th>
							        <th class="w-20">
							        	<?php echo trans('label_datetime'); ?>
							        </th>
							        <th class="w-20">
							        	<?php echo trans('label_customer_name'); ?>
							        </th>
									<th class="w-7">
							        	<?php //echo trans('label_status'); ?>
										TIPO DE ENVIO
							        </th>
									<th class="w-7">
										ESTADO ENVIO
							        </th>
									<th class="w-7">
							        	<?php //echo trans('label_status'); ?>
										RED SOCIAL
							        </th>
									<th class="w-7">
							        	Monto
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_status'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_pay'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_return'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_view'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_edit'); ?>
							        </th>
							        <th class="w-7">
							        	<?php echo trans('label_delete'); ?>
							        </th>
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
<!--FUNCION PARA REPARAR FACTURAS MAL PAGADAS-->
<script>
$(document).ready(function () {
  $('#btn-fix-invoices').on('click', function () {
    if (!confirm('¿Estás seguro que deseas corregir las facturas pagadas mal marcadas como "unpaid"?')) return;

    $.ajax({
      url: '../_inc/invoice.php',
      method: 'POST',
      dataType: 'json',
      data: {
        action_type: 'FIX_PAID_STATUS'
      },
      beforeSend: function () {
        $('#btn-fix-invoices').prop('disabled', true).html('<i class="fa fa-cog fa-spin"></i> Corrigiendo...');
      },
      success: function (res) {
        alert(res.msg || 'Facturas corregidas exitosamente');
        $('#invoice-invoice-list').DataTable().ajax.reload(); // Refresca la tabla
      },
      error: function (xhr) {
        alert(xhr.responseJSON?.error || 'Ocurrió un error');
      },
      complete: function () {
        $('#btn-fix-invoices').prop('disabled', false).html('<i class="fa fa-wrench"></i> Reparar facturas pagadas mal marcadas');
      }
    });
  });
});
</script>

<?php include ("footer.php"); ?>