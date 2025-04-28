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
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

// 🚀 Agregas SOLO ESTA función, porque get_total_sell() no existe aún:
function get_total_sell($from, $to) {
  global $db;
  $store_id = store_id();

  $query = "SELECT 
              SUM((selling_item.item_price - selling_item.item_purchase_price)) AS utilidad_total
            FROM selling_item
            JOIN selling_info ON selling_item.invoice_id = selling_info.invoice_id
            WHERE selling_info.store_id = '$store_id'
              AND selling_info.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
              AND (selling_item.item_quantity - selling_item.return_quantity) > 0";

  $statement = $db->prepare($query);
  $statement->execute();
  $row = $statement->fetch(PDO::FETCH_ASSOC);

  return $row && isset($row['utilidad_total']) ? $row['utilidad_total'] : 0;
}

function get_total_gastos($from, $to) {
  global $db;
  $store_id = store_id();

  $query = "SELECT 
              SUM(amount) AS total_gastos
            FROM expenses
            WHERE store_id = '$store_id'
              AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'";

  $statement = $db->prepare($query);
  $statement->execute();
  $row = $statement->fetch(PDO::FETCH_ASSOC);

  return $row && isset($row['total_gastos']) ? $row['total_gastos'] : 0;
}
// 🚀 Ahora calculas todo:

$total_venta = get_total_sell($from, $to);
$total_gasto = get_total_gastos($from, $to);
$utilidad_bruta = $total_venta - $total_gasto;

// Establecer título del documento
$document->setTitle(trans('title_profit_and_loss'));
$document->setBodyClass('sidebar-collapse');

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/ReportGastosController.js');
$document->addScript('../assets/itsolution24/angular/controllers/ReportVentasController.js');

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
      <?php echo trans('Estado de resultados'); ?>
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
        <?php echo trans('Estado de resultados'); ?>
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
    
    <div class="box box-default" id="profit-loss-report">
      <div class="box-header bg-info">
        <h3 class="box-title">
          <?php echo trans('Detalle estado de resultados'); ?> 
          <?php if (from()) : ?>
            (<?php echo date("j M Y", strtotime(from()));?>)
          <?php else: ?>
            (<?php echo date("j M Y", time());?>)
          <?php endif; ?>
        </h3>
        <a class="pull-right pointer no-print" onClick="window.printContent('profit-loss-report', {title:'<?php echo trans('title_profit_and_loss');?>', 'headline':'<?php echo trans('title_profit_and_loss');?>', screenSize:'fullScreen'});">
          <i class="fa fa-print"></i> <?php echo trans('text_print');?>
        </a>
      </div>
      <div class="loss-profit-row">
        <div class="row">
          <!--Reporte de perdida-->
          <div class="col-md-6 loss-col" ng-controller="ReportGastosController">
            <div class="box-header">
              <h3 class="box-title">
                <?php echo trans('Gastos'); ?>
              </h3>
            </div>
            <div class='box-body'>
              <?php include('../_inc/template/partials/reporte_gastos.php'); ?>
            </div>
          </div>
          <!--Reporta ganancia-->
          <div class="col-md-6" ng-controller="ReportVentasController">
            <div class="box-header">
              <h3 class="box-title">
                <?php echo trans('Ventas'); ?>
              </h3>
            </div>
            <div class='box-body'>     
              <?php include('../_inc/template/partials/reporte_venta.php'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--Filtro por fecha-->
    <div class="box box-default">
      <div class="box-header with-border">
        <form method="get" action="">
          <div class="row">
            <div class="col-md-4">
              <label>Desde:</label>
              <input type="date" name="from" class="form-control" value="<?php echo isset($_GET['from']) ? $_GET['from'] : date('Y-m-01'); ?>" required>
            </div>
            <div class="col-md-4">
              <label>Hasta:</label>
              <input type="date" name="to" class="form-control" value="<?php echo isset($_GET['to']) ? $_GET['to'] : date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-4">
              <label>&nbsp;</label><br>
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-filter"></i> Filtrar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  <!--tabla de resultados-->
    <div class="box box-default">
      <div class="box-header text-center">
        <h4 class="title"><?php echo trans('title_profit');?>-<?php echo format_date($from) . ' a ' . format_date($to); ?></h4>
      </div>
      <div class="xbox-body">
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <div class="table-responsive">
            <table class="table table-bordered table-striped mt-0">
              <tbody>
                <tr>
                  <td class="w-50 bg-gray text-right bg-green"><?php echo trans('utilidad_bruta'); ?></td>
                  <td class="w-50 text-left bg-success"><?php echo currency_format($total_venta); ?></td>
                </tr>
                <tr>
                  <td class="w-50 bg-gray text-right bg-red"><?php echo trans('total_gastos'); ?></td>
                  <td class="w-50 text-left bg-danger"><?php echo currency_format($total_gasto); ?></td>
                </tr>
                <tr>
                <td class="w-50 bg-gray text-right bg-blue"><?php echo trans('utilidad_neta'); ?></td>
                <td class="w-50 text-left bg-info"><?php echo currency_format($utilidad_bruta); ?></td>
                </tr>
              </tbody>
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