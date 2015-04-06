<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Family_groups_m extends CI_model
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
		
		$query =  $this->company_db->query("select pk_id, name, members from family_groups order by 2");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function insert_group( $name )
	{
		
		$query =  $this->company_db->query("select pk_id from family_groups where name = '". $this->company_db->escape_str($name)."';");
		$result = $query->result();
		if( !empty($result) )
		{
			return "exists";
		}
		else{
			$query =  $this->company_db->query("insert into family_groups (name) values('". $this->company_db->escape_str($name)."')");
			$query = "select last_insert_id() as 'result'";
			$query =  $this->company_db->query($query);
			$result = $query->result();
			if( !empty($result) )
			{
				$result = $query->row();
				mysqli_next_result(  $this->company_db->conn_id );
				return is_numeric($result->result) ? $result->result : false;
			}
		}
		return false;
	}
}