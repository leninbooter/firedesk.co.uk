<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers_m extends CI_Model
{	
	public function get_customers()
	{
		$this->load->database();
		$query = $this->db->query("select pk_id, name from customers order by 2");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_names_address()
	{	
		$this->load->database();
		$query = $this->db->query( "SELECT * FROM get_customers_addr;" );
		return !empty($query->result()) ? $query->result() : false;
	}
	
	public function get_names_like( $name )
	{			
		$this->load->database();
		$query = $this->db->query( "CALL get_customers_like(?);", array($name) );
		if( $query->num_rows() > 0 )
		{
			$customers = $query->result_array();
			mysqli_next_result( $this->db->conn_id );
			return $customers;
		}else
		{
			return false;
		}
	}
	
	public function get_customers_addresses( $pk_id )
	{
		$this->load->database();
		$query = $this->db->query( "CALL get_customers_address(?);", array($pk_id) );
		if( $query->num_rows() > 0 )
		{
			$addresses = $query->result_array();
			mysqli_next_result( $this->db->conn_id );
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
	
	public function save_customer( $vars_array )		
	{		
		$this->load->database();
		array_walk($vars_array, "self::clean_vars");
		$query = "CALL ins_customer(".$this->db->escape_str($vars_array["order_number"]).",
									'".$this->db->escape_str($vars_array["name"])."', 
									'".$this->db->escape_str($vars_array["address"])."', 
									'".$this->db->escape_str($vars_array["telephone"])."', 
									'".$this->db->escape_str($vars_array["fax"])."', 
									'".$this->db->escape_str($vars_array["email"])."', 
									'".$this->db->escape_str($vars_array["contact_name"])."', 
									'".$this->db->escape_str($vars_array["representative"])."',
									".$this->db->escape_str($vars_array["vat"]).", 
									'".date('Y-m-d H:i:s')."',
									".$this->db->escape_str($vars_array["type"]).", 
									".$this->db->escape_str($vars_array["invoicing"]).", 
									'".$this->db->escape_str($vars_array["account_reference"])."',
									".$this->db->escape_str($vars_array["days_week"]).", 
									".$this->db->escape_str($vars_array["holiday_credit"]).", 
									".$this->db->escape_str($vars_array["prices_type"]).", 
									3,
									".$this->db->escape_str($vars_array["credit_limit"]).", 
									'".$this->db->escape_str($vars_array["statement_address"])."', 
									".$this->db->escape_str($vars_array["parent_account_id"]).",
									'".$this->db->escape_str($vars_array["address1"])."', 
									'".$this->db->escape_str($vars_array["address2"])."', 
									'".$this->db->escape_str($vars_array["address3"])."', 
									'".$this->db->escape_str($vars_array["address4"])."', 
									'".$this->db->escape_str($vars_array["address5"])."', 
									'".$this->db->escape_str($vars_array["post_code"])."', 
									'".$this->db->escape_str($vars_array["mobile"])."', 
									'".$this->db->escape_str($vars_array["disc_perc"])."',
									'".$this->db->escape_str($vars_array["account_department"])."',
									'".$this->db->escape_str($vars_array["account_dept_number"])."'
									)";
		$query = str_replace("'NULL'", "NULL", $query);
		$query = $this->db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return is_numeric($result->result) ? $result->result : false;
		}else
		{
			return false;
		}			
	}	
}