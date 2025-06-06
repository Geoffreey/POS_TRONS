<?php 
ob_start();
session_start();
include ("_init.php");

// Redirigir, si el usuario no ha iniciado sesión
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}?>
<!DOCTYPE html>
<html lang="<?php echo $document->langTag($active_lang);?>">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Select Store<?php echo store('name') ? ' | ' . store('name') : null; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!--Establecer Favicon-->
  <?php if ($store->get('favicon')): ?>
      <link rel="shortcut icon" href="assets/itsolution24/img/logo-favicons/<?php echo $store->get('favicon'); ?>">
  <?php else: ?>
      <link rel="shortcut icon" href="assets/itsolution24/img/logo-favicons/nofavicon.png">
  <?php endif; ?>

  <!-- Todo CSS -->

  <?php if (DEMO || USECOMPILEDASSET) : ?>

    <!-- INICIO DE SESIÓN CSS COMBINADO -->
    <link type="text/css" href="assets/itsolution24/cssmin/login.css" rel="stylesheet">

  <?php else : ?>

    <!-- Bootstrap CSS -->
    <link type="text/css" href="assets/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Perfect Scroll CSS -->
    <link type="text/css" href="assets/perfectScroll/css/perfect-scrollbar.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link type="text/css" href="assets/toastr/toastr.min.css" rel="stylesheet">

    <!-- Tema CSS -->
    <link type="text/css" href="assets/itsolution24/css/theme.css" rel="stylesheet">

    <!-- Inicio sesión CSS -->
    <link type="text/css" href="assets/itsolution24/css/login.css" rel="stylesheet">

  <?php endif; ?>

  <!-- All JS -->

  <script type="text/javascript">
    var baseUrl = "<?php echo root_url(); ?>";
    var adminDir = "<?php echo ADMINDIRNAME; ?>";
    var refUrl = "<?php echo isset($session->data['ref_url']) ? $session->data['ref_url'] : ''?>";
  </script>

  <?php if (DEMO || USECOMPILEDASSET) : ?>

    <!-- Login Combined JS -->
    <script src="assets/itsolution24/jsmin/login.js"></script>

  <?php else : ?>

    <!-- jQuery JS  -->
    <script src="assets/jquery/jquery.min.js" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    <!-- Perfect Scroll JS -->
    <script src="assets/perfectScroll/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>

    <!-- Toastr JS -->
    <script src="assets/toastr/toastr.min.js" type="text/javascript"></script>

    <!-- Common JS -->
    <script src="assets/itsolution24/js/common.js"></script>

    <!-- Login JS -->
    <script src="assets/itsolution24/js/login.js"></script>

  <?php endif; ?>

</head>
<body class="login-page">
<div class="hidden"><?php include('assets/itsolution24/img/iconmin/membership/membership.svg');?></div>

  <section class="login-box">
    <div class="login-logo">
      <div class="text">
        <p>
          <strong>
            <?php echo trans('text_select_store'); ?>
          </strong>
        </p>
      </div>
    </div>
    <?php if (isset($error_message)) { ?>
      <div class="alert alert-danger">
          <p class=""><span class="fa fa-fw fa-warning"></span> <?php echo $error_message ; ?></p>
      </div>
      <br>
    <?php } ?>
    <div id="store-launcher" class="login-box-body" ng-controller="StoreController">
      <ul class="list-unstyled list-group store-list">
        <?php foreach (get_stores() as $the_store): ?>
          <li class="list-group-item">
            <a class="activate-store" href="<?php echo root_url();?>/<?php echo ADMINDIRNAME;?>/store.php?active_store_id=<?php echo $the_store['store_id']; ?>">
              <div class="store-icon">
                <svg class="svg-icon"><use href="#icon-store"></svg>
              </div>
              <div class="store-name">
                <?php echo $the_store['name']; ?>
                <span class="pull-right">&rarr;</span>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="copyright text-center">
      <p>&copy; <a href="https://ventas.programacionparacompartir.com/">MAS SISTEMAS CON CODIGO FUENTE AQUI</a></p>
    </div>
  </section>

<script type="text/javascript">
$(document).ready(function() {
  $(".store-list").perfectScrollbar();
});
</script>

<noscript>You need to have javascript enabled in order to use <strong><?php echo store('name');?></strong>.</noscript>
</body>
</html>