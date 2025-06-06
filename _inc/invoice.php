<?php 
ob_start();
session_start();
include ("../_init.php");
include ("../const.php");

if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_login')));
  exit();
}

// Comprobar, si el usuario tiene permiso de lectura o no
// If user have not reading permission return an alert message
if (user_group_id() != 1 && !has_permission('access', 'read_sell_list')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_read_permission')));
  exit();
}

$store_id = store_id();
$user_id = user_id();

// LOAD INVOICE MODEL
$invoice_model = registry()->get('loader')->model('invoice');

// Reparar facturas mal marcadas como "due" pero ya pagadas
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] === 'FIX_PAID_STATUS') {
    try {
        if (!is_loggedin()) {
            throw new Exception('No autorizado');
        }

        if (user_group_id() != 1 && !has_permission('access', 'update_sell_invoice_info')) {
            throw new Exception('No tienes permiso para ejecutar esta acci?n.');
        }

        $statement = db()->prepare("
            UPDATE selling_info si
            JOIN (
                SELECT 
                    si.invoice_id,
                    ROUND(SUM(COALESCE(p.amount, 0)), 2) AS total_paid,
                    ROUND(sp.payable_amount, 2) AS payable_amount
                FROM selling_info si
                JOIN selling_price sp ON si.invoice_id = sp.invoice_id
                LEFT JOIN payments p ON si.invoice_id = p.invoice_id AND p.type != 'discount'
                WHERE si.store_id = ?
                  AND si.payment_status = 'due'
                GROUP BY si.invoice_id, sp.payable_amount
                HAVING ABS(total_paid - payable_amount) <= 0.01 AND payable_amount > 0
            ) pagos ON si.invoice_id = pagos.invoice_id
            SET si.payment_status = 'paid'
        ");
        $statement->execute([store_id()]);

        echo json_encode(['msg' => 'Facturas corregidas exitosamente']);
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}

// Delete invoice
if($request->server['REQUEST_METHOD'] == 'POST' && $request->post['action_type'] == 'DELETE')
{
    try {

        error_log("Intentando eliminar invoice: " . $request->post['invoice_id']);
        
        // Check permission
        if (user_group_id() != 1 && !has_permission('access', 'delete_sell_invoice')) {
          throw new Exception(trans('error_delete_permission'));
        }

        // Validate invoice id
        if (empty($request->post['invoice_id'])) {
            throw new Exception(trans('error_invoice_id'));
        }

        $invoice_id = $request->post['invoice_id'];

        // Check, if invoice exist or not
        $selling_info = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$selling_info) {
            throw new Exception(trans('error_invoice_not_found'));
        }
        $due = $selling_info['due'];
        
        // Obtener la informaci��n de la factura antes de eliminarla
$statement = db()->prepare("
    SELECT si.*, sp.paid_amount 
    FROM selling_info si
    LEFT JOIN selling_price sp ON si.invoice_id = sp.invoice_id
    WHERE si.store_id = ? AND si.invoice_id = ?
");
$statement->execute(array($store_id, $invoice_id));
$selling_info = $statement->fetch(PDO::FETCH_ASSOC);

if (!$selling_info) {
    throw new Exception(trans('error_invoice_not_found'));
}

// Guardar la factura en el log de auditor��a antes de eliminarla
$invoice_data = json_encode($selling_info);
$deleted_by = $user_id; // ID del usuario que elimina la factura

$statement = db()->prepare("INSERT INTO deleted_invoices_log (invoice_id, customer_id, total_amount, deleted_by, invoice_data) VALUES (?, ?, ?, ?, ?)");
$statement->execute(array(
    $invoice_id,
    $selling_info['customer_id'],
    $selling_info['paid_amount'],  //Ahora se obtiene de selling_price
    $deleted_by,
    $invoice_data
));

        // Check invoice delete duration
        $selling_date_time = strtotime($selling_info['created_at']);
        if (invoice_delete_lifespan() > $selling_date_time) {
          throw new Exception(trans('error_delete_duration_expired'));
        }

        // Fetch selling invoice item
        $statement = db()->prepare("SELECT * FROM `selling_item` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));
        $selling_items = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Check, if invoice item exist or not
        if (!$statement->rowCount()) {
            throw new Exception(trans('error_invoice_item'));
        }

        $Hooks->do_action('Before_Delete_Invoice', $request);

        // Quantity adjustment start
        foreach ($selling_items as $item) {
            $item_id = $item['item_id'];
            $item_quantity = $item['item_quantity']-$item['return_quantity'];
            db()->prepare("UPDATE `purchase_item` SET `status` = ?, `total_sell` = `total_sell` - {$item_quantity} WHERE `invoice_id` = ? AND `item_id` = ?");
            db()->execute(array('active', $item['purchase_invoice_id'], $item_id));

            db()->prepare("UPDATE `product_to_store` SET `quantity_in_stock` = `quantity_in_stock` + {$item_quantity} WHERE `store_id` = ? AND `product_id` = ?");
            db()->execute(array($store_id, $item_id));
        }
        // Quantity adjustment end

        // Delete payments
        $statement = db()->prepare("DELETE FROM  `payments` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete returns
        $statement = db()->prepare("DELETE FROM  `returns` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete return items
        $statement = db()->prepare("DELETE FROM  `return_items` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice info
        $statement = db()->prepare("DELETE FROM  `selling_info` WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice items
        $statement = db()->prepare("DELETE FROM  `selling_item` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice price info
        $statement = db()->prepare("DELETE FROM  `selling_price` WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($store_id, $invoice_id));

        if ($due > 0) {
            $statement = db()->prepare("UPDATE `customer_to_store` SET `due` = `due`-$due  WHERE `store_id` = ? AND `customer_id` = ?");
            $statement->execute(array($store_id, $selling_info['customer_id']));
        }

        // Substract bank transaction
        $withdraw_amount = $selling_info['paid_amount'] - $selling_info['return_amount'];
        if (($account_id = store('deposit_account_id')) && $withdraw_amount > 0) {
          $ref_no = unique_transaction_ref_no('withdraw');
          $statement = db()->prepare("SELECT `category_id` FROM `expense_categorys` WHERE `sell_delete` = ?");
          $statement->execute(array(1));
          $category = $statement->fetch(PDO::FETCH_ASSOC);
          $exp_category_id = $category['category_id'];
          $statement = db()->prepare("SELECT `source_id` FROM `income_sources` WHERE `for_sell` = ?");
          $statement->execute(array(1));
          $source = $statement->fetch(PDO::FETCH_ASSOC);
          $source_id = $source['source_id'];
          $title = 'Debit while deleting sell invoice';
          $details = 'Customer name: ' . get_the_customer($selling_info['customer_id'], 'customer_name');
          $image = 'NULL';
          $transaction_type = 'withdraw';

          $statement = db()->prepare("INSERT INTO `bank_transaction_info` (store_id, is_substract, account_id, source_id, exp_category_id, ref_no, invoice_id, transaction_type, title, details, image, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
          $statement->execute(array($store_id, 1, $account_id, $source_id, $exp_category_id, $ref_no, $invoice_id, $transaction_type, $title, $details, $image, $user_id, date_time()));
		  $info_id = db()->lastInsertId();

          $statement = db()->prepare("INSERT INTO `bank_transaction_price` (store_id, info_id, ref_no, amount) VALUES (?, ?, ?, ?)");
          $statement->execute(array($store_id, $info_id, $ref_no, $withdraw_amount));

          $statement = db()->prepare("UPDATE `bank_account_to_store` SET `withdraw` = `withdraw` + $withdraw_amount WHERE `store_id` = ? AND `account_id` = ?");
          $statement->execute(array($store_id, $account_id));

          $statement = db()->prepare("UPDATE `bank_accounts` SET `total_deposit` = `total_deposit` + $withdraw_amount WHERE `id` = ?");
          $statement->execute(array($account_id));
        }

        $Hooks->do_action('After_Delete_Invoice', $request);
        // ? RESPUESTA JSON CORRECTA
        ob_clean();
        flush();
        header('Content-Type: application/json');
        echo json_encode(array('msg' => trans('text_delete_success')));
        exit();

} catch(Exception $e) { 
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
}
}

// Update invoice info
if($request->server['REQUEST_METHOD'] == 'POST' && $request->post['action_type'] == 'UPDATEINVOICEINFO')
{
    try {
        
        // Check permission
        if (user_group_id() != 1 && !has_permission('access', 'update_sell_invoice_info')) {
          throw new Exception(trans('error_update_permission'));
        }

        // Validate invoice id
        if (empty($request->post['invoice_id'])) {
            throw new Exception(trans('error_invoice_id'));
        }

        $invoice_id = $request->post['invoice_id'];

        // Check, if invoice exist or not
        $invoice_info = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice_info) {
            throw new Exception(trans('error_invoice_id'));
        }

        if (!is_numeric($request->post['status'])) {
            throw new Exception(trans('error_status'));
        }

        // Check invoice edit duration
        $selling_date_time = strtotime($invoice_info['created_at']);
        if (invoice_edit_lifespan() > $selling_date_time) {
          throw new Exception(trans('error_edit_duration_expired'));
        }

        $customer_mobile = $request->post['customer_mobile'];
        $invoice_note = $request->post['invoice_note'];
        $status = $request->post['status'];
        $subtotal = $invoice_info['subtotal'];
        $payable_amount = $invoice_info['payable_amount'];
        $discount_amount = $request->post['discount_amount'];

        $Hooks->do_action('Before_Update_Invoice_Info', $invoice_id);

        $payable_amount = $subtotal - ($subtotal*($discount_amount/100));
        $paid_amount = $invoice_info['paid_amount'];
        $due_paid = $invoice_info['due_paid'];
        $due = 0;
        $balance = 0;
        if ($due_paid > $payable_amount) {
            $due_paid = $payable_amount;
        }
        if ($payable_amount > $paid_amount) {
            $due = $payable_amount - $paid_amount;
        }
        if ($paid_amount > $payable_amount) {
            $balance = $paid_amount - $payable_amount;
        }

        if ($balance > 0) {
            $paid_amount = $paid_amount - $balance;
            $statement = db()->prepare("INSERT INTO `payments` SET `type` = ?, `store_id` = ?, `invoice_id` = ?, `pos_balance` = ?, `created_by` = ?");
            $statement->execute(array('change', $store_id, $invoice_id, $balance, $user_id));
        } else {
            $statement = db()->prepare("DELETE FROM `payments` WHERE `store_id` = ? AND `invoice_id` = ? AND `type` = ?");
            $statement->execute(array($store_id, $invoice_id, 'change'));
        }

        $statement = db()->prepare("UPDATE `selling_price` SET `discount_amount` = ?, `payable_amount` = ?, `paid_amount` = ?, `due_paid` = ?, `due` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($discount_amount, $payable_amount, $paid_amount, $due_paid, $due, $store_id, $invoice_id));

        if ($due > 0) {
            $payment_status = 'due';
        } else {
            $payment_status = 'paid';
        }

        $statement = db()->prepare("DELETE FROM `payments` WHERE `store_id` = ? AND `invoice_id` = ? AND `type` = ? AND `note` = ?");
        $statement->execute(array($store_id, $invoice_id, 'discount', 'discount_while_invoice_edit'));
        if ($discount_amount > 0) {
            $statement = db()->prepare("INSERT INTO `payments` (type, store_id, invoice_id, amount, note, total_paid, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $statement->execute(array('discount', $store_id, $invoice_id, $discount_amount, 'discount_while_invoice_edit', $discount_amount, $user_id, date_time()));
        }

        $statement = db()->prepare("UPDATE `selling_info` SET `payment_status` = ?, `checkout_status` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($payment_status, 1, $store_id, $invoice_id));

        // Update invoice info
        $statement = db()->prepare("UPDATE `selling_info` SET `customer_mobile` = ?, `invoice_note` = ?, `status` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($customer_mobile, $invoice_note, $status, $store_id, $invoice_id));

        $Hooks->do_action('After_Update_Invoice_Info', $invoice_id);

        header('Content-Type: application/json');
        echo json_encode(array('msg' => trans('text_sell_update_success'), 'invoice_id' => $invoice_id, 'id' => $invoice_info['info_id']));
        exit();

    } catch(Exception $e) {

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
  }
}

// Invoice Info Edit Form
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEINFOEDIT') 
{
    try {
        $invoice_id = isset($request->get['invoice_id']) ? $request->get['invoice_id'] : null;
        $invoice = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice) {
            throw new Exception(trans('error_invoice_not_found'));
        }
        include('template/invoice_info_edit_form.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// Invoice View
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEVIEW') 
{
    try {

        if (user_group_id() != 1 && !has_permission('access', 'read_sell_invoice')) {
          throw new Exception(trans('error_read_permission'));
        }

        $invoice_id = isset($request->get['invoice_id']) ? $request->get['invoice_id'] : null;
        $invoice_info = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice_info) {
            throw new Exception(trans('error_invoice_not_found'));
        }
        include('../_inc/template/partials/invoice_view_js.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// Fetch Invoice
if ($request->server['REQUEST_METHOD'] == 'GET' && isset($request->get['invoice_id']))
{
    try {

        // Validate invoice id
        $invoice_id = $request->get['invoice_id'];
        $invoice = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice) {
            throw new Exception(trans('error_invoice_id'));
        }        

        // Fetch invoice info
        $statement = db()->prepare("SELECT selling_info.*, selling_price.*, customers.customer_name FROM `selling_info` 
            LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
            LEFT JOIN `customers` ON (`selling_info`.`customer_id` = `customers`.`customer_id`) 
            WHERE `selling_info`.`invoice_id` = ?");
        $statement->execute(array($invoice_id));
        $invoice = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($invoice)) {
            throw new Exception(trans('error_selling_not_found'));
        }

        if (isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
            $selling_date_time = strtotime($invoice['created_at']);
            if (invoice_edit_lifespan() > $selling_date_time) {
                throw new Exception(trans('error_duration_expired'));
            }
        }
        
        // Fetch invoice item
        $statement = db()->prepare("SELECT * FROM `selling_item` WHERE invoice_id = ?");
        $statement->execute(array($invoice_id));
        $selling_items = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($selling_items)) {
            throw new Exception(trans('error_selling_item'));
        }

        $invoice['items'] = $selling_items;

        header('Content-Type: application/json');
        echo json_encode(array('msg' => trans('text_success'), 'invoice' => $invoice));
        exit();

    } catch(Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// View invoice details
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEDETAILS') {

    try {

        $user_id = isset($request->get['user_id']) ? $request->get['user_id'] : null;
        $where_query = "`selling_info`.`inv_type` = 'sell' AND `created_by` = ? AND `status` = ?";
        $from = from() ? from() : date('Y-m-d');
        $to = to() ? to() : date('Y-m-d');
        $where_query .= date_range_filter($from, $to);
        $statement = db()->prepare("SELECT * FROM `selling_info` 
            LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`)
            WHERE $where_query");
        $statement->execute(array($user_id, 1));
        $invoices = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!$statement->rowCount() > 0) {
            throw new Exception(trans('error_not_found'));
        }

        include('template/user_invoice_details.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// View invoice due details
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEDUEDETAILS') {

    try {

        $user_id = isset($request->get['user_id']) ? $request->get['user_id'] : null;
        $where_query = "`selling_info`.`inv_type` = 'sell' AND `created_by` = ? AND `status` = ? AND `selling_price`.`due` > 0";
        $from = from() ? from() : date('Y-m-d');
        $to = to() ? to() : date('Y-m-d');
        $where_query .= date_range_filter($from, $to);

        $statement = db()->prepare("SELECT * FROM `selling_info` 
            LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`)
            WHERE $where_query");
        $statement->execute(array($user_id, 1));
        $invoices = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!$statement->rowCount() > 0) {
            throw new Exception(trans('error_not_found'));
        }

        include('template/user_invoice_due_details.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

/**
 *===================
 * INICIO DE TABLA DE DATOS
 *===================
 */

$Hooks->do_action('Before_Showing_Invoice_List');

$where_query = "selling_info.store_id = '$store_id'";
$where_query .= " AND EXISTS (
    SELECT 1 FROM selling_item 
    WHERE selling_item.invoice_id = selling_info.invoice_id 
    AND (selling_item.item_quantity - selling_item.return_quantity) > 0
)";
if (isset($request->get['type']) && ($request->get['type'] != 'undefined') && $request->get['type'] != '') {
    switch ($request->get['type']) {
        case 'due':
        case 'all_due':
            $where_query .= " AND selling_info.payment_status = 'due'";
            break;
        case 'paid':
            $where_query .= " AND selling_info.payment_status = 'paid'";
            break;
        case 'inactive':
            $where_query .= " AND selling_info.status = 0";
            break;
        default:
            $where_query .= " AND selling_info.status = 1";
            break;
    }
};
if (from()) {
    $from = from();
    $to = to();
    $where_query .= date_range_filter($from, $to);
}

if (isset($request->get['customer_id']) && ($request->get['customer_id'] != 'undefined') && $request->get['customer_id'] != '' && $request->get['customer_id'] != 'null') {
    $customer_id = $request->get['customer_id'];
    $where_query .= " AND selling_info.customer_id = {$customer_id}";
}

if (isset($request->get['currier']) && ($request->get['currier'] != 'undefined') && $request->get['currier'] != '' && $request->get['currier'] != 'null') {
    $currier = $request->get['currier'];
    $where_query .= " AND selling_info.currier = '{$currier}'";
}

if (isset($request->get['estadoEnvio']) && ($request->get['estadoEnvio'] != 'undefined') && $request->get['estadoEnvio'] != '' && $request->get['estadoEnvio'] != 'null') {
    $estadoEnvio = $request->get['estadoEnvio'];
    $where_query .= " AND selling_info.estadoEnvio = '{$estadoEnvio}'";
}

if (isset($request->get['social']) && ($request->get['social'] != 'undefined') && $request->get['social'] != '' && $request->get['social'] != 'null') {
    $social = $request->get['social'];
    $where_query .= " AND selling_info.social = '{$social}'";
}

// tabla de base de datos a utilizar
//(select sum((item_price*(item_quantity-return_quantity))-(((item_price*(item_quantity-return_quantity))*item_discount)/100)) from selling_item  where invoice_id=selling_info.invoice_id) as amount
//$table = "(SELECT selling_info.*, (select sum(payable_amount) from selling_price where invoice_id=selling_info.invoice_id) as amount, (select name from pmethods where pmethod_id=selling_info.pmethod_id) as pmethod FROM `selling_info` WHERE {$where_query}) as selling_info";
$table = "(SELECT selling_info.*, 
  (
    ROUND(
      (SELECT 
        SUM((item_price * (item_quantity - return_quantity)) 
          - ((item_price * (item_quantity - return_quantity)) * item_discount / 100) 
          + item_tax)
       FROM selling_item
       WHERE invoice_id = selling_info.invoice_id), 2
    )
    +
    ROUND(
      (SELECT shipping_amount 
       FROM selling_price 
       WHERE invoice_id = selling_info.invoice_id), 2
    )
  ) AS amount,
  (SELECT name FROM pmethods WHERE pmethod_id = selling_info.pmethod_id) AS pmethod 
  FROM `selling_info` 
  WHERE {$where_query}
    AND NOT EXISTS (
      SELECT 1 FROM deleted_invoices_log dlog 
      WHERE dlog.invoice_id = selling_info.invoice_id
    )
) AS selling_info";


// Llave principal de la tabla
$primaryKey = 'info_id';

$columns = array(
    array(
      'db' => 'info_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
    ),
    array( 'db' => 'edit_counter', 'dt' => 'edit_counter' ),
    array( 'db' => 'invoice_id', 'dt' => 'id' ),
    array(
        'db' => 'invoice_id',
        'dt' => 'invoice_id',
        'formatter' => function( $d, $row) {
            $o = $row['invoice_id'];   
            if ($row['edit_counter'] > 0) {
                $o .= ' <span class="fa fa-edit" title="'.$row['edit_counter'].' time(s) edited"></span>';
            }         
            return $o;
        }
    ),
    array( 
      'db' => 'created_at',   
      'dt' => 'created_at' ,
      'formatter' => function($d, $row) {
        $date = new DateTimeImmutable($row['created_at']);
        return $date->format('d-m-Y H:i:s');
      }
    ),
    array(
        'db' => 'customer_id',
        'dt' => 'customer_name',
        'formatter' => function( $d, $row) {
            $customer = get_the_customer($row['customer_id']);
			if (isset($customer['customer_id'])) {
				return '<a href="customer_profile.php?customer_id=' . $customer['customer_id'] . '">' . $customer['customer_name'] . ' - ' . $customer['customer_mobile'] . '</a>';
			}
			return '';
        }
    ),
    array(
        'db' => 'created_by',
        'dt' => 'created_by',
        'formatter' => function( $d, $row) {
            $the_user = get_the_user($row['created_by']);
            if (isset($the_user['id'])) {
                return '<a href="user.php?user_id=' . $the_user['id'] . '&username='.$the_user['username'].'">' . $the_user['username'] . '</a>';
            }

            return;
        }
    ),
    array( 
      'db' => 'amount',   
      'dt' => 'amount',
      'formatter' => function($d, $row) {
        return currency_format($row['amount']);
      }
    ),
    array( 
      'db' => 'pmethod',   
      'dt' => 'pmethod',
      'formatter' => function($d, $row) {
        return $row['pmethod'];
      }
    ),
    array( 'db' => 'payment_status', 'dt' => 'payment_status' ),
    array( 'db' => 'is_installment', 'dt' => 'is_installment' ),
    array( 
        'db' => 'currier',   
        'dt' => 'currier' ,
        'formatter' => function($d, $row) {
            if($row['currier']!='null' and strlen($row['currier'])>0){
                return Currier[$row['currier']];
            } else {
                return $row['currier'];
            }
                
        }
    ),
    array( 
        'db' => 'estadoEnvio',   
        'dt' => 'estadoEnvio' ,
        'formatter' => function($d, $row) {
            if($row['estadoEnvio']!='null' and strlen($row['estadoEnvio'])>0){
                $selectEE='<select id="changeEE" name="changeEE" >';
                foreach (EstadoEnvio as $key => $value) {
                    $selectEE.='<option value="'.$key.'" ';
                    if($row['estadoEnvio']==$key){
                        $selectEE.=' selected ';
                    }
                    $selectEE.='>'.$value.'</option>';
                }
                $selectEE.='</select>';
                return $selectEE;
            } else {
                return $row['estadoEnvio'];
            }
                
        }
    ),
    array( 
        'db' => 'social',   
        'dt' => 'social' ,
        'formatter' => function($d, $row) {
            if($row['social']!='null' and strlen($row['social'])>0){
                return Social[$row['social']];
            } else {
                return $row['social'];
            }
                
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'status',
        'formatter' => function($d, $row)  {
            if ($row['payment_status'] == 'due') {
                return '<span class="label label-danger">'.trans('text_unpaid').'</span>';
            } else {
                return '<span class="label label-success">'.trans('text_paid').'</span>';
            }
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_pay',
        'formatter' => function($d, $row) {
            if ($row['is_installment']) {
                return '<span class="label label-warning">Installment</span>';
            }
            if ($row['payment_status'] != 'paid') {
                return '<button id="pay_now" class="btn btn-sm btn-block btn-success" title="'.trans('button_view_receipt').'" data-loading-text="..."><i class="fa fa-money"></i></button>';
            }
            return '-';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_return',
        'formatter' => function($d, $row) {
            if ($row['is_installment']) {
                return;
            }
            return '<button id="return_item" class="btn btn-sm btn-block btn-warning" title="'.trans('button_return').'" data-loading-text="..."><i class="fa fa-minus"></i></button>';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_view',
        'formatter' => function($d, $row) {
            if ($row['is_installment']) {
                return '<button id="view-installment-btn" class="btn btn-sm btn-block btn-info" title="'.trans('button_view_details').'" data-loading-text="..."><i class="fa fa-eye"></i></button>';
            }
            return '<a class="btn btn-sm btn-block btn-info" href="view_invoice.php?invoice_id='.$row['invoice_id'].'" title="'.trans('button_view_receipt').'" data-loading-text="..."><i class="fa fa-eye"></i></a>';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_edit',
        'formatter' => function($d, $row){
            if ($row['is_installment']) {
                return;
            }
            $selling_date_time = strtotime($row['created_at']);
            if (invoice_edit_lifespan() > $selling_date_time) {
                return '<a class="btn btn-sm btn-block btn-default" href="#" disabled><span class="fa fa-pencil"></span></a>';
            }
            return '<button id="edit-invoice-info" class="btn btn-sm btn-block btn-primary" title="'.trans('button_edit').'" data-loading-text="..."><span class="fa fa-pencil"></span></button>'; 
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_delete',
        'formatter' => function($d, $row){
            if ($row['is_installment']) {
                return;
            }
            $selling_date_time = strtotime($row['created_at']);
            if (invoice_delete_lifespan() > $selling_date_time) {
                return '<a class="btn btn-sm btn-block btn-default" href="#" disabled><span class="fa fa-trash"></span></a>';
            }
            return '<button class="btn btn-sm btn-block btn-danger" id="delete-invoice" title="'.trans('button_delete').'" data-loading-text="..."><i class="fa fa-trash"  data-loading-text="..."></i></button>';
        }
    )
);

echo json_encode(
    SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Invoice_List');

/**
 *===================
 * FIN TABLA DE DATOS
 *===================
 */