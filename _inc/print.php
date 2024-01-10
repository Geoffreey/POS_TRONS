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
if (user_group_id() != 1 && !has_permission('access', 'pos_print')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_print_permission')));
  exit();
}

// LOAD ESCPOS LIBRARY
$escpos = new Escpos();

// PRINT DATA
$data = json_decode($_GET['data']);
dd($data);
$escpos->load($data->printer);
$escpos->print_receipt($data);