<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('name_valid'))
{
    function name_valid( $valor )
	{
		if( preg_match('/^[A-Za-zñÑ0-9\-\_\.\,\s]{0,200}$/', $valor) == 1 )
		{
			return true;
		}else
		{
			$this->form_validation->set_message('name_valid', 'The %s field can only contain letters, numbers, dashes, underscores, dots and commas.');
			return false;
		}
	}  
}

if ( ! function_exists('shorttext_valid')){
	
		function shorttext_valid( $valor )
	{
		if( (strlen($valor) > 0 && preg_match('/^[A-Za-zñÑ0-9\/\\\-\_\.\,\#\s]{2,200}$/', $valor) == 1 ) || strlen($valor) == 0)
		{
			return true;
		}else
		{
			$this->form_validation->set_message('shorttext_valid', "Invalid characteres");
			return false;
		}
	}
}

if ( ! function_exists('telephone_valid')){
	
	function telephone_valid( $valor )
	{
		if( (strlen($valor) > 0 && preg_match( '/^(?:\d|\+){1}[0-9]{1,19}$/', $valor )  == 1) || $valor == "" )
		{
			return true;
		}else
		{
			$this->form_validation->set_message('telephone_valid', $this->lang->line('error_bad_phone_account'));
			return false;
		}
	}	
}