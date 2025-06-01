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
$where_query = "si.store_id = '$store_id'";

// Si hay filtro de fecha
$from = from();
$to = to();
$from = from();
$to = to();

if (!$from || !$to) {
  $from = date('Y-m-01');
  $to = date('Y-m-d');
}


// Definimos la tabla, usando JOIN entre selling_price y selling_info
    $table = <<<EOT
(
  SELECT 
    s.invoice_id,
    s.created_at,
    SUM(
      (si.item_price - (si.item_price * si.item_discount / 100)) 
      * (si.item_quantity - IFNULL(ri.return_qty, 0))
    ) AS amount
  FROM selling_item si
  JOIN selling_info s ON si.invoice_id = s.invoice_id
  JOIN selling_price sp ON s.invoice_id = sp.invoice_id
  LEFT JOIN (
    SELECT invoice_id, item_id, SUM(item_quantity) AS return_qty
    FROM return_items
    GROUP BY invoice_id, item_id
  ) ri ON si.invoice_id = ri.invoice_id AND si.item_id = ri.item_id
  WHERE s.store_id = $store_id
    AND s.status = 1
    AND s.payment_status = 'paid'
    AND sp.payable_amount > 0
    AND NOT EXISTS (
      SELECT 1 FROM deleted_invoices_log dlog 
      WHERE dlog.invoice_id = s.invoice_id
    )
    AND s.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'
  GROUP BY s.invoice_id
) AS selling_summary
EOT;



// Llave primaria
$primaryKey = 'invoice_id';

// Columnas para el DataTable
$columns = array(
    array(
    'db' => 'invoice_id',
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
