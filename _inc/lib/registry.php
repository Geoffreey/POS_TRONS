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
final class Registry 
{
	private $data = array();

	public function get($key) 
	{
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	public function set($key, $value) 
	{
		$this->data[$key] = $value;
	}

	public function has($key) 
	{
		return isset($this->data[$key]);
	}
}