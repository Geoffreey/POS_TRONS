<?php 
ob_start();
session_start();
include ("../_init.php");

// Comprobar si el usuario inició sesión o no
if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  exit();
}

$store_id = store_id();
$user_id = user_id();

/**
 *===================
 * INICIO DE TABLA DE DATOS
 *===================
 */

// Ahora hacemos el WHERE usando el store_id
$where_query = "selling_info.store_id = '$store_id'";

// Si hay filtro de fecha
$from = from();
$to = to();
if ($from && $to) {
  $where_query .= " AND selling_info.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
}

// Definimos la tabla, usando JOIN entre selling_price y selling_info
$table = "(SELECT 
            selling_price.price_id, 
            selling_price.invoice_id, 
            selling_info.created_at, 
            SUM(selling_price.payable_amount) AS amount
          FROM selling_price
          JOIN selling_info ON selling_price.invoice_id = selling_info.invoice_id
          WHERE $where_query
          GROUP BY selling_price.invoice_id) as selling_summary";

// Llave primaria
$primaryKey = 'price_id';

// Columnas para el DataTable
$columns = array(
    array(
        'db' => 'price_id', // 👈 usar price_id
        'dt' => 'serial_no',
        'formatter' => function($d, $row) {
            static $count = 1;
            return $count++;
        }
    ),
    array( 'db' => 'invoice_id', 'dt' => 'invoice_id' ),
    array( 'db' => 'created_at', 'dt' => 'created_at' ),
    array( 
        'db' => 'invoice_id', 
        'dt' => 'title',
        'formatter' => function($d, $row) {
            return 'VENTA';
        }
    ),
    array( 
        'db' => 'amount',
        'dt' => 'this_month',
        'formatter' => function($d, $row) {
            return currency_format($d);
        }
    ),
    array( 
        'db' => 'amount',
        'dt' => 'this_year',
        'formatter' => function($d, $row) {
            return currency_format($d);
        }
    ),
    array( 
        'db' => 'amount',
        'dt' => 'till_now',
        'formatter' => function($d, $row) {
            return currency_format($d);
        }
    ),
);

// Ejecutamos el procesamiento normal de SSP
echo json_encode(
    SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);
