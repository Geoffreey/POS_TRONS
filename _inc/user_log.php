<?php 
ob_start();
session_start();
include ("../_init.php");

// Comprobar si el usuario inició sesión o no
// If user is not logged in then return an alert message
if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_login')));
  exit();
}

// Comprobar, si el usuario tiene permiso de lectura o no
// If user have not reading permission return an alert message
if (user_group_id() != 1 && !has_permission('access', 'read_user_log')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_read_permission')));
  exit();
}

/**
 *===================
 * INICIO DE TABLA DE DATOS
 *===================
 */

$Hooks->do_action('Before_Showing_User_Log');

$user_id = $request->get['user_id'];
$where_query = "user_id = '{$user_id}'";

// tabla de base de datos a utilizar
$table = "(SELECT login_logs.* FROM login_logs WHERE $where_query) as login_logs";
 
// Llave principal de la tabla
$primaryKey = 'id';

$columns = array(
    array(
        'db' => 'id',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            return 'row_'.$d;
        }
    ),
    array( 'db' => 'id', 'dt' => 'serial' ),
    array( 'db' => 'username', 'dt' => 'username' ),
    array( 'db' => 'ip', 'dt' => 'ip' ),
    array( 
      'db' => 'created_at',   
      'dt' => 'time' ,
      'formatter' => function($d, $row) {
          return format_date($row['created_at']);
      }
    ),
);
 
echo json_encode(
  SSP::simple( $request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_User_Log');

/**
 *===================
 * FIN TABLA DE DATOS
 *===================
 */