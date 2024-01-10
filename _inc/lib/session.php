<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	MODERN POS
| -----------------------------------------------------
| AUTHOR:			web.ferrocasa.pw
| -----------------------------------------------------
| EMAIL:			info@web.ferrocasa.pw
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY web.ferrocasa.pw
| -----------------------------------------------------
| WEBSITE:			http://web.ferrocasa.pw
| -----------------------------------------------------
*/
class Session 
{
	public $data = array();

	public function __construct() 
	{
		if (!session_id()) {
			ini_set('session.use_only_cookies', 'On');
			ini_set('session.use_trans_sid', 'On');
			ini_set('session.cookie_httponly', 'On');
			ini_set('session.cookie_lifetime', 'On');
			ini_set('session.cookie_domain', 'On');

			session_set_cookie_params(60*60*24*14, '/');
			
			session_start();
		}

		$this->data =& $_SESSION;
	}

	public function getId() 
	{
		return session_id();
	}

	public function destroy() 
	{
		return session_destroy();
	}
}