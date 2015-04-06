<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilities_m extends CI_Model
{	
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function save_holiday($month, $day, $isholiday)
	{
		
		$query = "UPDATE holidays SET isholiday = ".$isholiday." WHERE month = ".$month." AND monthday = ".$day;
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		return true;
	}
	
	function get_holidays($month)
	{
		
		$query = "select monthday from holidays where isholiday = 1 and month = ".$month;
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			return $result;
		}else
		{
			return array();
		}
	}
}