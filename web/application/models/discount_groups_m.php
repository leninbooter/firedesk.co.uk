<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Discount_groups_m extends CI_model
{
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}

	function get_groups()
	{
		
		$query =  $this->company_db->query("select pk_id, description, discount_percentage from discount_groups");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function update_group( $pk_id, $description, $discount_percentage )
	{
		
		if($description == "") $description = "NULL";
		$query = "update discount_groups set description = '". $this->company_db->escape_str($description)."', discount_percentage = ".$discount_percentage." where pk_id = ".$pk_id;		
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		$query =  $this->company_db->query("select row_count() as 'result'");
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			$value = is_numeric($result->result) ? $result->result : false;
			return is_numeric($result->result) ? true : false;
		}else
			return false;
	}
}