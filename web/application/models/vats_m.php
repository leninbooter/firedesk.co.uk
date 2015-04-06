<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vats_m extends CI_Model
{		
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function get_all_vats()
	{
		
		$query =  $this->company_db->query( "select * from vats;");
		return !empty($query->result()) ? $query->result() : array();
	}	

	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}	
	
	function save_vat( $vars_array )		
	{		
		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "call ins_stock_item(
									'". $this->company_db->escape_str($vars_array["stock_number"])."',
									'". $this->company_db->escape_str($vars_array["location"])."',
									'". $this->company_db->escape_str($vars_array["description"])."',
									 ". $this->company_db->escape_str($vars_array["quantity_rec_level"]).",
									 ". $this->company_db->escape_str($vars_array["standard_price"]).",
									 ". $this->company_db->escape_str($vars_array["special_price"]).",
									 ". $this->company_db->escape_str($vars_array["units_of_for_special"]).",
									 ". $this->company_db->escape_str($vars_array["cost_price_a"]).",
									 ". $this->company_db->escape_str($vars_array["cost_price_b"]).",
									 ". $this->company_db->escape_str($vars_array["cost_price_c"]).",
									 ". $this->company_db->escape_str($vars_array["fk_vat_code"]).",
									 ". $this->company_db->escape_str($vars_array["fk_family_group"]).",
									 ". $this->company_db->escape_str($vars_array["fk_discount_group"]).",
									 ". $this->company_db->escape_str($vars_array["fk_supplier_a"]).",
									 ". $this->company_db->escape_str($vars_array["fk_supplier_b"]).",
									 ". $this->company_db->escape_str($vars_array["fk_supplier_c"])."
									);";
		$query = str_replace("'NULL'", "NULL", $query);
		$query =  $this->company_db->query($query);
						
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			if($result->type == "inserted" )
			{	
				$return['type'] = $result->type;
				$return['pk_id'] = $result->pk_id;
				return $return;
			}elseif($result->type == "updated")
			{
				$return['type'] = $result->type;
				$return['pk_id'] = $result->pk_id;
				return $return;
			}
		}
		return false;
	}
	
	function update_vat($vars_array)
	{
		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_stock(
									'". $this->company_db->escape_str($vars_array["date"])."',
									". $this->company_db->escape_str($vars_array["fk_item_id"]).",
									'". $this->company_db->escape_str($vars_array["description"])."',
									". $this->company_db->escape_str($vars_array["qty"]).",
									". $this->company_db->escape_str($vars_array["cost"])."
									);";
		$query = str_replace("'NULL'", "NULL", $query);
		$query =  $this->company_db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result->result == "ok" ? $result->result : false;
		}
		return false;
	}
}