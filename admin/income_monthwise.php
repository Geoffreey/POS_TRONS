<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_income_monthwise')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_income_monthwise'));

// Agregar script
$document->addScript('../assets/itsolution24/angular/controllers/IncomeMonthwiseController.js');

// Agregar clase de cuerpo
$document->setBodyClass('sidebar-collapse');

// Incluir encabezado y pie de página
include("header.php"); 
include ("left_sidebar.php");
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper">

  <!-- Inicio del encabezado de contenido -->
  <section class="content-header" ng-controller="IncomeMonthwiseController">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo trans('text_income_monthwise_title'); ?>
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
        <?php echo trans('text_income_monthwise_title'); ?>
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
    <?php 
      $year = from() ? date('Y', strtotime(from())) : year();
      $month = from() ? date('m', strtotime(from())) : month();
      if(isset($_REQUEST['month'])){
        $month=$_REQUEST['month'];
      }
      $days_in_month = get_total_day_in_month();
      ?>
    <div class="row" id="income-monthwise-report">
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <select id='selectMonthChange'>
              <option value='01' <?= ($month=='01') ? 'selected' : null; ?>>Enero</option>
              <option value='02' <?= ($month=='02') ? 'selected' : null; ?>>Febrero</option>
              <option value='03' <?= ($month=='03') ? 'selected' : null; ?>>Marzo</option>
              <option value='04' <?= ($month=='04') ? 'selected' : null; ?>>Abril</option>
              <option value='05' <?= ($month=='05') ? 'selected' : null; ?>>Mayo</option>
              <option value='06' <?= ($month=='06') ? 'selected' : null; ?>>Junio</option>
              <option value='07' <?= ($month=='07') ? 'selected' : null; ?>>Julio</option>
              <option value='08' <?= ($month=='08') ? 'selected' : null; ?>>Agosto</option>
              <option value='09' <?= ($month=='09') ? 'selected' : null; ?>>Septiembre</option>
              <option value='10' <?= ($month=='10') ? 'selected' : null; ?>>Octubre</option>
              <option value='11' <?= ($month=='11') ? 'selected' : null; ?>>Noviembre</option>
              <option value='12' <?= ($month=='12') ? 'selected' : null; ?>>Diciembre</option>
            </select>
            <h3 class="box-title">
              <?php 
              echo date("F", mktime(0, 0, 0, $month, 10)).', '.$year;
              if (to()) {
                echo '<i> <small>to</small> </i>';
                $year = date('Y', strtotime(to()));
                $month = date('m', strtotime(to()));
                echo date("F", mktime(0, 0, 0, $month, 10)).', '.$year;
              }
              ?>
            </h3>
            <a class="pull-right pointer no-print" onClick="window.printContent('income-monthwise-report', {title:'<?php echo trans('title_income_monthwise');?>', 'headline':'<?php echo trans('title_income_monthwise');?>', screenSize:'fullScreen'});">
              <i class="fa fa-print"></i> <?php echo trans('text_print');?>
            </a>
          </div>
          <div class="box-body pt-0">
            <div class="table-responsive">                     
              <table id="expense-expense-list" class="table table-bordered table-striped table-hovered">
                <thead>
                  <tr class="bg-success">
                      <th class="w-5 text-center bg-black"><?php echo trans('label_serial_no'); ?></th>
                      <th class="w-5 text-center"><?php echo trans('label_capital'); ?></th>
                      <th class="w-5 text-center"><?php echo trans('label_profit'); ?></th>
                    <?php foreach (get_income_sources(array('filter_type'=>'credit','filter_show_in_income'=>'yes')) as $source):?>
                      <th class="w-5 text-center">
                        <?php echo $source['source_name'];?>
                      </th>
                    <?php endforeach; ?>
                      <th class="w-10 text-center bg-red">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $filter_date = isset($request->get['date']) ? $request->get['date'] : '';
                    $inc = 1;
                    for ($i=0; $i < $days_in_month; $i++) : 
                      $from = date('Y-m-d',strtotime($year.'-'.$month.'-'.$inc));
                      $profit = get_profit_amount($from,$from);
                      $capital = get_capital_amount($from,$from);
                      $is_highlisht_row = false;
                      $is_highlisht_row = strtotime($from) == strtotime($filter_date) ? true : false;
                      ?>
                      <tr class="<?php echo $is_highlisht_row ? 'bg-yellow' : 'bg-gray';?>">
                          <td class="w-5 text-center bg-black"><?php echo $inc;?></td>
                          <td class="w-5 text-center"><?php echo $capital ? currency_format($capital) : '-';?></td>
                          <td class="w-5 text-center"><?php echo $profit ? currency_format($profit) : '-';?></td>
                        <?php foreach (get_income_sources(array('filter_type'=>'credit','filter_show_in_income'=>'yes')) as $source):?>
                          <td class="w-5 text-center">
                            <?php echo get_total_substract_source_income($source['source_id'], $from, $from) ? currency_format(get_total_substract_source_income($source['source_id'], $from, $from)) : '-';?>
                          </td>
                        <?php endforeach;?>
                        <td class="w-10 text-center bg-green">
                          <?php echo get_total_substract_income($from, $from) ? currency_format(get_total_substract_income($from, $from)) : '-';?>
                        </td>
                      </tr>
                  <?php $inc++;endfor;?>
                </tbody>
                <tfoot>
                  <tr class="bg-success">
                      <?php
                        $from = date('Y-m-d',strtotime($year.'-'.$month.'-1'));
                        $to = date('Y-m-d',strtotime($year.'-'.$month.'-'.$days_in_month));
                        $profit = get_profit_amount($from,$to);
                        $capital = get_capital_amount($from,$to);
                      ?>
                      <th class="w-5 text-center bg-red"><?php echo trans('label_total'); ?></th>
                      <th class="w-5 text-center"><?php echo currency_format($capital);?></th>
                      <th class="w-5 text-center"><?php echo currency_format($profit);?></th>
                    <?php foreach (get_income_sources(array('filter_type'=>'credit','filter_show_in_income'=>'yes')) as $source) :?>
                      <th class="w-5 text-center">
                        <?php echo get_total_substract_source_income($source['source_id'], $from, $to) ? currency_format(get_total_substract_source_income($source['source_id'], $from, $to)) : '-';?>
                      </th>
                    <?php endforeach; ?>
                      <th class="w-10 text-center bg-primary"><?php echo get_total_substract_income($from, $to) ? currency_format(get_total_substract_income($from, $to)) : '-';?></th>
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

<?php include ("footer.php"); ?>