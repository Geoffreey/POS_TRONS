<?php 
ob_start();
session_start();
include ("../_init.php");

if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  exit();
}

$store_id = store_id();
$where_query = "s.store_id = '$store_id'";

// Filtro por fecha
$from = from();
$to = to();
if ($from && $to) {
  $where_query .= " AND s.created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
}

$table = <<<EOT
(
  SELECT 
    s.invoice_id,
    s.created_at,
    s.store_id,
    SUM((si.item_price * si.item_quantity) - (IFNULL(ri.return_qty,0) * si.item_price)) AS total_neto
  FROM selling_info s
  LEFT JOIN selling_item si ON s.invoice_id = si.invoice_id
  LEFT JOIN (
    SELECT 
      invoice_id, 
      item_id, 
      SUM(item_quantity) AS return_qty
    FROM return_items
    GROUP BY invoice_id, item_id
  ) ri ON s.invoice_id = ri.invoice_id AND si.item_id = ri.item_id
  WHERE $where_query
    AND NOT EXISTS (
      SELECT 1 FROM deleted_invoices_log dlog 
      WHERE dlog.invoice_id = s.invoice_id
    )
    AND ((si.item_quantity - IFNULL(ri.return_qty,0)) > 0)
  GROUP BY s.invoice_id
) AS venta_real
EOT;

$primaryKey = 'invoice_id';

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
    'db' => 'total_neto',
    'dt' => 'this_month',
    'formatter' => function($d, $row) {
      return currency_format($d);
    }
  ),
  array( 
    'db' => 'total_neto',
    'dt' => 'this_year',
    'formatter' => function($d, $row) {
      return currency_format($d);
    }
  ),
  array( 
    'db' => 'total_neto',
    'dt' => 'till_now',
    'formatter' => function($d, $row) {
      return currency_format($d);
    }
  ),
);

echo json_encode(
  SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);
