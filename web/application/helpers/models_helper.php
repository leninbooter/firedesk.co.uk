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

if( ! function_exists('company_db_string_connection'))
{
	function company_db_string_connection()
	{
		$ci = get_instance();
		
		$local_db['hostname'] = $ci->nativesession->get('user')['db_host'];
		$local_db['username'] = $ci->nativesession->get('user')['db_user'];
		$local_db['password'] = $ci->nativesession->get('user')['db_pwd'];
		$local_db['database'] = $ci->nativesession->get('user')['db'];
		$local_db['dbdriver'] = 'mysqli';
		$local_db['dbprefix'] = '';
		$local_db['pconnect'] = TRUE;
		$local_db['db_debug'] = TRUE;
		$local_db['cache_on'] = FALSE;
		$local_db['cachedir'] = '';
		$local_db['char_set'] = 'utf8';
		$local_db['dbcollat'] = 'utf8_general_ci';
		$local_db['swap_pre'] = '';
		$local_db['autoinit'] = TRUE;
		$local_db['stricton'] = FALSE;
			foreach($local_db as $key=>$value)
				log_message('debug', $key."=".$value);
		
		return $local_db;
	}
}