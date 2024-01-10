<?php 
ob_start();
session_start();
include ("../_init.php");

// Comprobar si el usuario inició sesión o no
// Si el usuario no ha iniciado sesión, aparece un mensaje de alerta
if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_login')));
  exit();
}

// Comprobar, si el usuario tiene permiso de lectura o no
// Si el usuario no tiene permiso de lectura, aparece un mensaje de alerta
if (user_group_id() != 1 AND !has_permission('access', 'read_customer')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_read_permission')));
  exit();
}

//MODELO DE CLIENTE DE CARGA
$customer_model = registry()->get('loader')->model('customer');
$store_id = store_id();
$user_id = user_id();

// Validar datos de publicación
function validate_request_data($request) 
{
  // Validar nombre del cliente
  if (!validateString($request->post['customer_name'])) {
    throw new Exception(trans('error_customer_name'));
  }

  // Validar fecha de nacimiento del cliente
 if ($request->post['dob']) {
    if (!isItValidDate($request->post['dob'])) {
        throw new Exception(trans('error_date_of_birth'));
    }
  }

  // Validar correo electrónico y móvil del cliente
  if (!validateEmail($request->post['customer_email']) 
    AND (empty($request->post['customer_mobile']) 
      || !valdateMobilePhone($request->post['customer_mobile']))) {

    throw new Exception(trans('error_customer_email_or_mobile'));
  }

  // Validar sexo del cliente
  if (!validateInteger($request->post['customer_sex'])) {
    throw new Exception(trans('error_customer_sex'));
  }

  // Validar estado del cliente
  if (get_preference('invoice_view') == 'indian_gst') {
    if (!validateString($request->post['customer_state'])) {
      throw new Exception(trans('error_customer_state'));
    }
  }

  // Validación de la tienda
  if (!isset($request->post['customer_store']) || empty($request->post['customer_store'])) {
    throw new Exception(trans('error_store'));
  }

  // Validar estado
  if (!is_numeric($request->post['status'])) {
    throw new Exception(trans('error_status'));
  }

  // Validar orden de clasificación
  if (!is_numeric($request->post['sort_order'])) {
    throw new Exception(trans('error_sort_order'));
  }
}

// Verifica la existencia del cliente por id
function validate_existance($request, $id = 0)
{
  
  // Comprobar la dirección de correo electrónico, ¿si existe o no?
  if (!empty($request->post['customer_email'])) {
    $statement = db()->prepare("SELECT * FROM `customers` WHERE `customer_email` = ? AND `customer_id` != ?");
    $statement->execute(array($request->post['customer_email'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception(trans('error_email_exist'));
    }
  }

  // Comprobar teléfono móvil, ¿existe?
  if (!empty($request->post['customer_mobile'])) {
    $statement = db()->prepare("SELECT * FROM `customers` WHERE `customer_mobile` = ? AND `customer_id` != ?");
    $statement->execute(array($request->post['customer_mobile'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception(trans('error_mobile_exist'));
    }
  }
}

function add_customer_balance($customer_id, $amount, $pmethod_id,  $notes='')
{
  $balance = get_customer_balance($customer_id);
  $reference_no = generate_customer_transacton_ref_no('add_balance');
  $statement = db()->prepare("INSERT INTO `customer_transactions` SET `type` = ?, `reference_no` = ?, `customer_id` = ?, `store_id` = ?, `pmethod_id` = ?, `notes` = ?, `amount` = ?, `balance` = ?, `created_by` = ?, `created_at` = ?");
  $statement->execute(array('add_balance', $reference_no, $customer_id, store_id(), $pmethod_id, $notes, $amount, $balance+$amount, user_id(), date_time()));

  $statement = db()->prepare("UPDATE `customer_to_store` SET `balance` = `balance` + {$amount} WHERE `store_id` = ? AND `customer_id` = ?");
  $statement->execute(array(store_id(), $customer_id));
}

// crear cliente
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'CREATE')
{
  try {

    // Crear verificación de permisos
    if (user_group_id() != 1 AND !has_permission('access', 'create_customer')) {
      throw new Exception(trans('error_create_permission'));
    }

    // Validar datos de publicación
    validate_request_data($request);
    
    // validar existencia
    validate_existance($request);

    $Hooks->do_action('Before_Create_Customer', $request);

    // Insertar nuevo cliente en la base de datos
    $customer_id = $customer_model->addCustomer($request->post);

    // Obtener información del cliente
    $customer = $customer_model->getCustomer($customer_id);
    $contact = $customer['customer_mobile'] ? $customer['customer_mobile'] : $customer['customer_email'];

    $amount = $request->post['credit_balance'];
    if ($amount > 0) {
      $pmethod_id = 1;
      add_customer_balance($customer_id, $amount, $pmethod_id);
    }

    $Hooks->do_action('After_Create_Customer', $customer);

    header('Content-Type: application/json');
    $due_amount = $customer['balance'] < 0 ? currency_format($customer['balance']) : 0;
    echo json_encode(array('msg' => trans('text_success'), 'id' => $customer_id, 'customer_name' => $customer['customer_name'], 'customer_contact' => $contact, 'due_amount' => $due_amount));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Actualizar cliente
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'UPDATE')
{
  try {

    // Comprobar permiso de actualización
    if (user_group_id() != 1 AND !has_permission('access', 'update_customer')) {
      throw new Exception(trans('error_update_permission'));
    }

    // Validar identificación del producto
    if (empty($request->post['customer_id'])) {
      throw new Exception(trans('error_customer_id'));
    }

    $id = $request->post['customer_id'];

    // if ($id == 1) {
    //   throw new Exception(trans('error_update_permission'));
    // }

    // Validar datos de publicación
    validate_request_data($request);

    // validar existencia
    validate_existance($request, $id);

    $Hooks->do_action('Before_Update_Customer', $request);
    
    // Editar cliente
    $customer_id = $customer_model->editCustomer($id, $request->post);
    $customer = $customer_model->getCustomer($customer_id);

    $Hooks->do_action('After_Update_Customer', $customer_id);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => trans('text_update_success'), 'id' => $customer_id, 'customer' => $customer));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// eliminar cliente
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'DELETE') 
{
  try {

    // Comprobar permiso de eliminación
    if (user_group_id() != 1 AND !has_permission('access', 'delete_customer')) {
      throw new Exception(trans('error_delete_permission'));
    }

    // Validar identificación del cliente
    if (empty($request->post['customer_id'])) {
      throw new Exception(trans('error_customer_id'));
    }

    $id = $request->post['customer_id'];

    if ($id == 1) {
      throw new Exception(trans('error_delete_permission'));
    }

    $the_customer = $customer_model->getCustomer($id);

    if (!$the_customer) {
      throw new Exception(trans('error_customer_not_found'));
    }

    $new_customer_id = $request->post['new_customer_id'];

    // el cliente ambulante no se puede eliminar
    if ($request->post['customer_id'] == 1) {
      throw new Exception(trans('error_unable_to_delete'));
    }

    // validar la acción de eliminación
    if (empty($request->post['delete_action'])) {
      throw new Exception(trans('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' AND empty($new_customer_id)) {
      throw new Exception(trans('error_new_customer_name'));
    }

    $Hooks->do_action('Before_Delete_Customer', $request);

    // reemplazar cliente con nuevo
    if ($request->post['delete_action'] == 'insert_to') {
      $customer_model->replaceWith($new_customer_id, $id);
    }

    $statement = $db->prepare("UPDATE `customer_transactions` SET `customer_id` = ? WHERE `customer_id` = ?");
    $statement->execute(array($new_customer_id, $id));

    // eliminar cliente
    $customer = $customer_model->deleteCustomer($id);

    $Hooks->do_action('After_Delete_Customer', $customer);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => trans('text_delete_success'), 'id' => $id));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Restar saldo
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'SUBSTRACTBALANCE')
{
  try {

    // Comprobar permiso de actualización
    if (user_group_id() != 1 AND !has_permission('access', 'substract_customer_balance')) {
      throw new Exception(trans('error_update_permission'));
    }

    // Validar identificación del cliente
    $customer_id = $request->post['customer_id'];
    if (!validateInteger($customer_id)) {
      throw new Exception(trans('error_customer_id'));
    }

    // Validar importe
    $amount = $request->post['amount'];
    if (!is_numeric($amount)) {
      throw new Exception(trans('error_amount'));
    }

    $notes = $request->post['note'];

    $balance = get_customer_balance($customer_id);
    if ($balance < $amount) {
      throw new Exception(trans('error_amount_exceed'));
    }

    $reference_no = generate_customer_transacton_ref_no('substract_balance');
    $statement = db()->prepare("INSERT INTO `customer_transactions` SET `type` = ?, `reference_no` = ?, `customer_id` = ?, `store_id` = ?, `notes` = ?, `amount` = ?, `balance` = ?, `created_by` = ?, `created_at` = ?");
    $statement->execute(array('substract_balance', $reference_no, $customer_id, $store_id, $notes, $amount, $balance-$amount, $user_id, date_time()));

    $statement = db()->prepare("UPDATE `customer_to_store` SET `balance` = `balance` - {$amount} WHERE `store_id` = ? AND `customer_id` = ?");
    $statement->execute(array($store_id, $customer_id));

    header('Content-Type: application/json');
    echo json_encode(array('msg' => trans('text_balance_substracted'), 'id' => $customer_id, 'amount' => $amount, 'balance' => currency_format(get_customer_balance($customer_id))));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Agregar saldo
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'ADDBALANCE')
{
  try {

    // Comprobar permiso de actualización
    if (user_group_id() != 1 AND !has_permission('access', 'add_customer_balance')) {
      throw new Exception(trans('error_update_permission'));
    }

    // Validar identificación del cliente
    $customer_id = $request->post['customer_id'];
    if (!validateInteger($customer_id)) {
      throw new Exception(trans('error_customer_id'));
    }

    // Validar ID del método p
    $pmethod_id = $request->post['pmethod_id'];
    if (!validateInteger($pmethod_id)) {
      throw new Exception(trans('error_pmethod_id'));
    }

    // Validar importe
    $amount = $request->post['amount'];
    if (!is_numeric($amount)) {
      throw new Exception(trans('error_amount'));
    }

    $notes = $request->post['note'];

    add_customer_balance($customer_id, $amount, $pmethod_id,  $notes);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => trans('text_balance_added'), 'id' => $customer_id, 'amount' => $amount, 'balance' => currency_format(get_customer_balance($customer_id))));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Formulario de creación de cliente
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'CREATE') 
{
  include 'template/customer_create_form.php';
  exit();
}

// Formulario de edición del cliente
if (isset($request->get['customer_id']) AND isset($request->get['action_type']) AND $request->get['action_type'] == 'EDIT') {
  $customer = $customer_model->getCustomer($request->get['customer_id']);
  include 'template/customer_form.php';
  exit();
}

// Formulario de eliminación de clientes
if (isset($request->get['customer_id']) AND isset($request->get['action_type']) AND $request->get['action_type'] == 'DELETE') {
  $customer = $customer_model->getCustomer($request->get['customer_id']);
  include 'template/customer_del_form.php';
  exit();
}

//Reiniciar formulario de saldo
if (isset($request->get['customer_id']) AND isset($request->get['action_type']) AND $request->get['action_type'] == 'SUBSTRACTBALANCE') {
  $customer = $customer_model->getCustomer($request->get['customer_id']);
  include 'template/customer_substract_balance_form.php';
  exit();
}

// Agregar formulario de saldo
if (isset($request->get['customer_id']) AND isset($request->get['action_type']) AND $request->get['action_type'] == 'ADDBALANCE') {
  $customer = $customer_model->getCustomer($request->get['customer_id']);
  include 'template/customer_add_balance_form.php';
  exit();
}

/**
 *===================
 * INICIO DE TABLA DE DATOS
 *===================
 */
$Hooks->do_action('Before_Showing_Customer_List');

$where_query = "c2s.store_id = {$store_id}";
 
// tabla de base de datos a utilizar
$table = "(SELECT customers.*, c2s.balance, c2s.status, c2s.sort_order FROM customers 
  LEFT JOIN customer_to_store c2s ON (customers.customer_id = c2s.customer_id) 
  WHERE $where_query) as customers";
 
// Llave principal de la tabla
$primaryKey = 'customer_id';

$columns = array(
  array(
      'db' => 'customer_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'customer_id', 'dt' => 'customer_id' ),
  array( 
    'db' => 'customer_name',   
    'dt' => 'customer_name' ,
    'formatter' => function($d, $row) {
        return $row['customer_name'];
    }
  ),
  array( 'db' => 'customer_email',  'dt' => 'customer_email' ),
  array( 'db' => 'customer_mobile',  'dt' => 'customer_mobile' ),
  array( 'db' => 'customer_nit',  'dt' => 'customer_nit' ),
  array(
      'db'        => 'customer_sex',
      'dt'        => 'customer_sex',
      'formatter' => function( $d, $row ) {
        $sex = trans('label_others');
        if ($d == 1) {
          $sex = trans('label_male');
        } else if ($d == 2) {
          $sex = trans('label_female');
        }
        return $sex;
      }
  ),
  array( 
    'db' => 'customer_address',   
    'dt' => 'customer_address' ,
    'formatter' => function($d, $row) {
        return limit_char($row['customer_address'], 30);
    }
  ),
  array( 
    'db' => 'dob',   
    'dt' => 'dob' ,
    'formatter' => function($d, $row) {
      if ($row['dob']) {
        return date("j M Y", strtotime($row['dob']));
      }
      return '-';
    }
  ),
  array( 
    'db' => 'balance',   
    'dt' => 'balance' ,
    'formatter' => function($d, $row) {
      return currency_format($row['balance']);
    }
  ),
  array(
      'db'        => 'customer_id',
      'dt'        => 'btn_pos',
      'formatter' => function( $d, $row ) {

        if (!$row['status']) {
          return '<a href="#" class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-shopping-cart"></i></a>';
        }
        
        return '<a href="pos.php?customer_id='.$row['customer_id'].'" id="sell-product" class="btn btn-sm btn-block btn-success" type="button" title="'.trans('button_sell').'"><i class="fa fa-shopping-cart"></i></a>';
      }
  ),
  array(
      'db'        => 'customer_id',
      'dt'        => 'btn_profile',
      'formatter' => function( $d, $row ) {
        return '<a href="customer_profile.php?customer_id='.$row['customer_id'].'&type=all_invoice" id="sell-product" class="btn btn-sm btn-block btn-info" type="button" title="'.trans('button_view_profile').'"><i class="fa fa-user"></i></a>';
      }
  ),
  array( 
    'db' => 'status',   
    'dt' => 'status',
    'formatter' => function($d, $row) {
      return $row['status'] 
        ? '<span class="label label-success">'.trans('text_active').'</span>' 
        : '<span class="label label-warning">' .trans('text_inactive').'</span>';
    }
  ),
  array(
      'db'        => 'customer_id',
      'dt'        => 'btn_edit',
      'formatter' => function( $d, $row ) {
        // if ($row['customer_id'] == 1) {          
        //   return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
        // }
        return '<button id="edit-customer" class="btn btn-sm btn-block btn-primary" type="button" title="'.trans('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
      }
  ),
  array(
      'db'        => 'customer_id',
      'dt'        => 'btn_delete',
      'formatter' => function( $d, $row ) {
        if ($row['customer_id'] == 1) {
          return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash"></i></button>';
        }
        return '<button id="delete-customer" class="btn btn-sm btn-block btn-danger" type="button" title="'.trans('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
      }
  )
); 

echo json_encode(
    SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Customer_List');

/**
 *===================
 * FIN TABLA DE DATOS
 *===================
 */