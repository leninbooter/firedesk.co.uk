<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('clean_vars'))
{
	function clean_vars(&$value, $key)
	{
		if($value == "" && $value != "0")
		{
			$value = "NULL";
		}
	}
}