<?php
ob_start();
session_start();
include ("../_init.php");
include ("../const.php");

// Comprobar si el usuario inició sesión o no
// If user is not logged in then return an alert message
if (!is_loggedin()) {
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => trans('error_login')));
    exit();
  }
  $store_id = store_id();
  $user_id = user_id();
// Update invoice info
if($request->server['REQUEST_METHOD'] == 'GET' && $request->get['action_type'] == 'UPDATEINVOICEINFO')
{
    try {
        
        // Check permission
        if (user_group_id() != 1 && !has_permission('access', 'update_sell_invoice_info')) {
          throw new Exception(trans('error_update_permission'));
        }

        // Validate invoice id
        if (empty($request->get['invoice_id'])) {
            throw new Exception(trans('error_invoice_id'));
        }

        $invoice_id = $request->get['invoice_id'];

        if (!is_numeric($request->get['estadoEnvio'])) {
            throw new Exception(trans('error_status'));
        }

        $estadoEnvio = $request->get['estadoEnvio'];

        $Hooks->do_action('Before_Update_Invoice_Info', $invoice_id);

        $statement = db()->prepare("UPDATE `selling_info` SET `estadoEnvio` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($estadoEnvio, $store_id, $invoice_id));

        $Hooks->do_action('After_Update_Invoice_Info', $invoice_id);

        header('Content-Type: application/json');
        echo json_encode(array('msg' => trans('text_sell_update_success'), 'invoice_id' => $invoice_id));
        exit();

    } catch(Exception $e) {

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
  }
}
?>