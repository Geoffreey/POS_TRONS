<?php 
ob_start();
session_start();
include ("../_init.php");

// Comprobar si el usuario inició sesión o no
// If user is not logged in then return error
if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_login')));
  exit();
}

// Comprobar, si el usuario tiene permiso de lectura o no
// Si el usuario no tiene permiso de lectura, devuelva el error
if (user_group_id() != 1 && !has_permission('access', 'read_pos_receipt_template')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_read_permission')));
  exit();
}

$store_id = store_id();
$user_id = user_id();

// Template edit form
if (isset($request->get['template_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') 
{
  require(DIR_INCLUDE.'template/receipt_template/classic.php');
  exit();
}