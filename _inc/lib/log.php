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
class Log 
{
	private $handle;
	
	public function __construct($filename) {
		if (LOG) {
			$this->handle = fopen(DIR_LOG . $filename, 'a');
		}
	}
	
	public function write($message) {
		fwrite($this->handle, date('Y-m-d G:i:s') . ' - ' . print_r($message, true) . "\n");
	}

	public function simplyWrite($message) {
		fwrite($this->handle, print_r($message, true) . "\n");
	}
	
	public function __destruct() {
		if (LOG) {
			fclose($this->handle);
		}
	}
}