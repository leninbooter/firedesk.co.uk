<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers_m extends CI_Model
{	
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function get_customers()
	{
		
		$query = $this->company_db->query("select pk_id, name from customers order by 2");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_customer_details( $pk_id )
	{
		
		$query = $this->company_db->query( "" );
		return !empty($query->result()) ? $query->row() : false;
	}
	
	function get_names_address()
	{	
		
		$query = $this->company_db->query( "select name, address from customers" );
		return !empty($query->result()) ? $query->result() : false;
	}
	
	function get_names_like( $name )
	{			
		
		$query = $this->company_db->query( "CALL get_customers_like(?);", array($name) );
		if( $query->num_rows() > 0 )
		{
			$customers = $query->result_array();
			mysqli_next_result( $this->company_db->conn_id );
			return $customers;
		}else
		{
			return false;
		}
	}
	
	function get_customers_addresses( $pk_id )
	{
		
		$query = $this->company_db->query( "CALL get_customers_address(?);", array($pk_id) );
		if( $query->num_rows() > 0 )
		{
			$addresses = $query->result_array();
			mysqli_next_result( $this->company_db->conn_id );
			return $addresses;
		}else
		{
			return false;
		}
	}

	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}	
	
	function save_customer( $vars_array )		
	{		
		
		array_walk($vars_array, "self::clean_vars");
		$query = "CALL ins_customer(". $this->company_db->escape_str($vars_array["order_number"]).",
									'". $this->company_db->escape_str($vars_array["name"])."', 
									'". $this->company_db->escape_str($vars_array["address"])."', 
									'". $this->company_db->escape_str($vars_array["telephone"])."', 
									'". $this->company_db->escape_str($vars_array["fax"])."', 
									'". $this->company_db->escape_str($vars_array["email"])."', 
									'". $this->company_db->escape_str($vars_array["contact_name"])."', 
									'". $this->company_db->escape_str($vars_array["representative"])."',
									". $this->company_db->escape_str($vars_array["vat"]).", 
									'".date('Y-m-d H:i:s')."',
									". $this->company_db->escape_str($vars_array["type"]).", 
									". $this->company_db->escape_str($vars_array["invoicing"]).", 
									'". $this->company_db->escape_str($vars_array["account_reference"])."',
									". $this->company_db->escape_str($vars_array["days_week"]).", 
									". $this->company_db->escape_str($vars_array["holiday_credit"]).", 
									". $this->company_db->escape_str($vars_array["prices_type"]).", 
									3,
									". $this->company_db->escape_str($vars_array["credit_limit"]).", 
									'". $this->company_db->escape_str($vars_array["statement_address"])."', 
									". $this->company_db->escape_str($vars_array["parent_account_id"]).",
									'". $this->company_db->escape_str($vars_array["address1"])."', 
									'". $this->company_db->escape_str($vars_array["address2"])."', 
									'". $this->company_db->escape_str($vars_array["address3"])."', 
									'". $this->company_db->escape_str($vars_array["address4"])."', 
									'". $this->company_db->escape_str($vars_array["address5"])."', 
									'". $this->company_db->escape_str($vars_array["post_code"])."', 
									'". $this->company_db->escape_str($vars_array["mobile"])."', 
									'". $this->company_db->escape_str($vars_array["disc_perc"])."',
									'". $this->company_db->escape_str($vars_array["account_department"])."',
									'". $this->company_db->escape_str($vars_array["account_dept_number"])."'
									)";
		$query = str_replace("'NULL'", "NULL", $query);
		$query = $this->company_db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->company_db->conn_id );
			return is_numeric($result->result) ? $result->result : false;
		}else
		{
			return false;
		}			
	}	
}