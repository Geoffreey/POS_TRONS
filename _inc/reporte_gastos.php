<?php 
ob_start();
session_start();
include ("../_init.php");

// Comprobar si el usuario inició sesión o no
if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_login')));
  exit();
}

$store_id = store_id();
$user_id = user_id();

/**
 *===================
 * INICIO DE TABLA DE DATOS
 *===================
 */

$Hooks->do_action('Before_Showing_Loss_List');

$where_query = "returnable='no' AND status=1";

$from = from();
$to = to();

if ($from && $to) {
  $where_query .= " AND DATE(fecha_gasto) BETWEEN '$from' AND '$to'";
}

// tabla de base de datos a utilizar
$table = "(SELECT * FROM expenses 
  WHERE $where_query GROUP BY category_id
  ) as expenses";

// Llave principal de la tabla
$primaryKey = 'id';

$columns = array(
  array(
      'db' => 'id',
      'dt' => 'DT_RowId',
      'formatter' => function($d, $row) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'id', 'dt' => 'serial_no' ),
  array( 
    'db' => 'category_id',   
    'dt' => 'title',
    'formatter' => function($d, $row) {
        $parent = '';
        $category = get_the_expense_category($row['category_id']);
        if ($category['parent_id']) {
            $parent_cat = get_the_expense_category($category['parent_id']);
            $parent = $parent_cat['category_name'] . ' > ';
        }
        return $parent . $category['category_name'];
    }
  ),
  array(
    'db' => 'fecha_gasto',
    'dt' => 'fecha_gasto',
    'formatter' => function($d, $row) {
        return date('d-m-Y', strtotime($d));
    }
  ),
  array( 
    'db' => 'amount',   
    'dt' => 'this_month',
    'formatter' => function($d, $row) use($from, $to) {
      $total = get_expenses_total_by_category($row['category_id'], $from, $to, store_id());
      return currency_format($total);
    }
  ),
  array( 
    'db' => 'amount',   
    'dt' => 'this_year',
    'formatter' => function($d, $row) use($from) {
      $year = $from ? date('Y', strtotime($from)) : date('Y');
      $from_year = "$year-01-01";
      $to_year = "$year-12-31";
      $total = get_total_category_expense($row['category_id'], $from_year, $to_year, store_id(), 'no');
      return currency_format($total);
    }
  ),
  array( 
    'db' => 'amount',   
    'dt' => 'till_now',
    'formatter' => function($d, $row) {
      $total = get_total_category_expense($row['category_id'], null, null, store_id(), 'no');
      return currency_format($total);
    }
  ),
); 

echo json_encode(
    SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Loss_List');

/**
 *===================
 * FIN TABLA DE DATOS
 *===================
 */
