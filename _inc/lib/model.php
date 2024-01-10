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
abstract class Model 
{
	protected $registry;
	protected $hooks;

	public function __construct($registry) 
	{
		$this->registry = $registry;
		$this->hooks = registry()->get('hooks');
	}

	public function __get($key) 
	{
		return $this->registry->get($key);
	}

	public function __set($key, $value) 
	{
		$this->registry->set($key, $value);
	}
}