<?php 
ob_start();
session_start();
include ("../_init.php");

// VENTANA MODAL DEL ADMINISTRADOR DE ARCHIVOS PARA LLAMADAS AJAX
if(isset($request->get['ajax'])) 
{
  if (!is_loggedin()) {
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => trans('error_login')));
    exit();
  }
  
  if (DEMO) {
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => trans('text_disable_in_demo')));
    exit();
  }

  // Comprobar, si el usuario tiene permiso de lectura o no
  // Si el usuario no tiene permiso de lectura, devuelva el error
  if (user_group_id() != 1 && !has_permission('access', 'read_filemanager')) {
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => trans('error_read_permission')));
    exit();
  }

	include('../_inc/template/partials/filemanager_ajax.php');
	exit();
}

if (DEMO) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Redirigir, si el usuario no ha iniciado sesión
if (!is_loggedin()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}  

// Redirigir, si el usuario no tiene permiso de lectura
if (user_group_id() != 1 && !has_permission('access', 'read_filemanager')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Establecer título del documento
$document->setTitle(trans('title_filemanager'));

// Agregar clase de cuerpo
$document->setBodyClass('sidebar-collapse');

// Incluir encabezado y pie de página
include ("header.php");
include ("left_sidebar.php");
?>

<!-- Inicio del contenedor de contenido -->
<div class="content-wrapper">

  <!--Inicio de contenido-->
  <section class="content">

    <?php if(DEMO) : ?>
    <div class="box">
      <div class="box-body">
        <div class="alert alert-info mb-0">
          <p><span class="fa fa-fw fa-info-circle"></span> <?php echo $demo_text; ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
  	<div class="filemanger-width">
  		<?php
        include('../_inc/template/partials/filemanager.php');
      ?>
  	</div>
  </section>
  <!--Fin del contenido-->
</div>
<!--Fin del contenedor de contenido-->
    
<?php include ("footer.php"); ?>