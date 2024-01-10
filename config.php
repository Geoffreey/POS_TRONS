<?php 
define('INSTALLED', true);

	$sql_details = array(
'host' => 'localhost',
'db' => 'posmoderno',
'user' => 'root',
'pass' => 'root',
'port' => '3306'
	);


	/*
	 * --------------------------------------------------------------------
	 * CONSTANTES GLOBALES
	 * --------------------------------------------------------------------
	 */
	define('FILE_READ_MODE', 0644);
	define('FILE_WRITE_MODE', 0666);
	define('DIR_READ_MODE', 0755);
	define('DIR_WRITE_MODE', 0755);
	
	define('ROOT', __DIR__);
	define('ADMINDIRNAME', 'admin');
	define('DIR_INCLUDE', ROOT.'/_inc/');
	define('DIR_LIBRARY', DIR_INCLUDE.'/lib/');
	define('DIR_MODEL', DIR_INCLUDE.'/model/');
	define('DIR_VENDOR', DIR_INCLUDE.'/vendor/');
	define('DIR_ADMIN', ROOT.'/'.ADMINDIRNAME.'/');
	define('DIR_HELPER', ROOT.'/_inc/helper/');
	define('DIR_LANGUAGE', ROOT.'/language/');
	define('DIR_STORAGE', ROOT.'/storage/');
	define('DIR_ASSET', ROOT.'/assets/');
	define('DIR_EMAIL_TEMPLATE', DIR_INCLUDE.'template/email/');
	define('DIR_BACKUP', DIR_STORAGE.'backups/');
	define('DIR_LOG', DIR_STORAGE.'logs/');


	/*
	 * --------------------------------------------------------------------
	  *SINCRONIZACIÓN OFFLINE-ONLINE
	 * --------------------------------------------------------------------
	 *
	  * Alternar la sincronización en línea sin conexión
	 *
	 */
	define('SYNCHRONIZATION', false);
	define('SYNCSERVERURL', '');


	/*
	 * --------------------------------------------------------------------
	  * SUBDIRECTORIO
	 * --------------------------------------------------------------------
	 *
	  *Esto es útil cuando alojará la aplicación dentro de un subdirectorio de root
	 */
	define('SUBDIRECTORY', '');


	/*
	 * --------------------------------------------------------------------
	 * RUTA DEL ADMINISTRADOR DE ARCHIVOS
	 * --------------------------------------------------------------------
	 *
	 * Si usa FTP para filemanager, simplemente déjelo en blanco
	 */
	define('FILEMANAGERPATH', ROOT.'');


	/*
	 * --------------------------------------------------------------------
	 * URL DEL ADMINISTRADOR DE ARCHIVOS
	 * --------------------------------------------------------------------
	 *
	 * Si usa FTP para filemanager, simplemente déjelo en blanco
	 * Example: http://modernpos/storage directory
	 */
	define('FILEMANAGERURL', '');


	/*
	 * --------------------------------------------------------------------
	 *ACTIVAR/DESACTIVAR EL SISTEMA DE ENGANCHE
	 * --------------------------------------------------------------------
	 */
	define('HOOK', 1);


	/*
	 * --------------------------------------------------------------------
	 * ACTIVAR/DESACTIVAR EL SISTEMA DE REGISTRO
	 * 
	 * to work properly set HOOK as 1
	 * --------------------------------------------------------------------
	 */
	define('LOG', 1);


	/*
	 * ----------------------------------------------------------------------------
	 * SUSPENDER LA CUENTA DE USUARIO DURANTE UN PERÍODO ESPECÍFICO, SI SE PRODUJERON ERRORES EN LOS INTENTOS DE INICIO DE SESIÓN
	 *-----------------------------------------------------------------------------
	 */
	define('TOTAL_LOGIN_TRY', 10);

	/*
	 * --------------------------------------------------------------------
	 * SI LA CUENTA ESTÁ BLOQUEADA, DESBLOQUEE DESPUÉS DEL TIEMPO ESPECIFICADO (MINUTOS)
	 *---------------------------------------------------------------------
	 */
	define('UNLOCK_ACCOUNT_AFTER', 10);

	/*
	 * --------------------------------------------------------------------
	 * VENTA A PLAZOS
	 * --------------------------------------------------------------------
	 *
	 */
	define('INSTALLMENT', true);


	/*
	 * --------------------------------------------------------------------
	 * SOLO SE PERMITEN ESTAS IP, SI ESTÁN VACÍAS, SE PERMITEN TODAS LAS IP
	 * --------------------------------------------------------------------
	 */
	define('DENIED_IPS', array());


	/*
	 * --------------------------------------------------------------------
	 * DENEGADO ESTAS IP PARA ACCEDER AL SISTEMA
	 * --------------------------------------------------------------------
	 */
	define('ALLOWED_ONLY_IPS', array());


	/*
	 * --------------------------------------------------------------------
	 * PREFIJOS DE FACTURA
	 * --------------------------------------------------------------------
	 */
	$invoice_init_prefix = array(
		'purchase' => 'B',
		'due_paid' => 'F',
		'expense' => 'E',
		'withdraw' => 'W',
		'deposit' => 'E',
	);

	/*
	 * --------------------------------------------------------------------
	 * ACTIVAR/DESACTIVAR DEMO
	 * --------------------------------------------------------------------
	 *
	 * DEMO siempre debe establecerse en false para producción
	 * Para restringir la instilación como demo establezca DEMO en true
	 */
	define('DEMO', false);


	/*
	 * --------------------------------------------------------------------
	 * USAR ACTIVOS COMPILADOS
	 * --------------------------------------------------------------------
	 *
	 * Si es true, entonces el sistema usará activos compilados, es decir, js, css e imágenes minificados / combinados
	 */
	define('USECOMPILEDASSET', true);

	/*
	 * --------------------------------------------------------------------
	 * Alternancia de derecha a izquierda (RTL)
	 * --------------------------------------------------------------------
	 *
	 * Si es true, entonces el sistema usará activos compilados, es decir, js, css e imágenes minificados / combinados
	 */
	define('RTL', 0);