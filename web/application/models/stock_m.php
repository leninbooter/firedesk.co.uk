<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_m extends CI_Model
{			
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function clean_vars(&$value, $key)
	{
		if($value == "" && $value != "0")
		{
			$value = "NULL";
		}
	}
	
	function get_items_all( )
	{
		
		$query =  $this->company_db->query( "SELECT pk_id, stock_number as 'number', description as 'label', 2 as 'origin' FROM sales_stock;");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_items_soled_by( $supplier_id )
	{
		
		$query =  $this->company_db->query( "call get_items_obtained_from(".$supplier_id.");");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_item_details( $pk_id )
	{
		
		$query =  $this->company_db->query( "SELECT * FROM sales_stock where pk_id = ".$pk_id);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_items_from_family_from_item ( $pk_id )
	{
		
		$query =  $this->company_db->query( "CALL get_items_from_family_of(".$pk_id.");");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_item_prices( $pk_id )
	{
		
		$query = "SELECT fk_customer_id, price_type, min_qty, max_qty, price FROM stock_item_prices WHERE fk_stock_item_id = ".$pk_id;
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_items_stock_levels( )
	{
		
		$query =  $this->company_db->query( "SELECT pk_id, stock_number, description, location, quantity_rec_level, quantity_balance FROM sales_stock;");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function save_item( $vars_array )		
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
	
	function ins_items_receipts( $vars_array )
	{
				
		array_walk($vars_array, "self::clean_vars");
		
		$query = "CALL ins_items_receipts(
											". $this->company_db->escape_str($vars_array["order_id"]).",
											". $this->company_db->escape_str($vars_array["purchase_order_item_id"]).",
											". $this->company_db->escape_str($vars_array["qty"]).",
											". $this->company_db->escape_str($vars_array["cost"]).",
											'". $this->company_db->escape_str($vars_array["date"])."',
											0
											);";
		log_message('debug', $query);
		$query = str_replace("'NULL'", "NULL", $query);		
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result->result == "ok" ? true : false;
		}
		return false;
	}
	
	function ins_up_item_price($item)
	{
		
		array_walk($item, "self::clean_vars");
		
		$query = "call save_price(". $this->company_db->escape_str($item['customer_id']).",". $this->company_db->escape_str($item["stock_item_id"]).",". $this->company_db->escape_str($item["price"]).",". $this->company_db->escape_str($item["price_type"]).",". $this->company_db->escape_str($item["min"]).",". $this->company_db->escape_str($item["max"]).");";		
		
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			log_message('debug', $result->result);
			mysqli_next_result(  $this->company_db->conn_id );					
			if($result->result == "ok" || $result->result == "notUpdated")
				return true;
		}
		return false;
	}
	
	function upd_balances_massive( $vars_array )
	{
		
		array_walk($vars_array, "self::clean_vars");
		log_message('debug', $vars_array["apply_to"]);
		if($vars_array["apply_to"] == "Family group")
		{
			$query = "call update_balances_massive(
									". $this->company_db->escape_str($vars_array["family_group_id"]).",
									". $this->company_db->escape_str($vars_array["negative_balances"]).",
									'". $this->company_db->escape_str($vars_array["date"])."'
									);";
			
		}elseif( $vars_array["apply_to"] == "Entire stock")
		{
			$query = "call update_balances_massive(
									NULL,
									". $this->company_db->escape_str($vars_array["negative_balances"]).",
									'". $this->company_db->escape_str($vars_array["date"])."'
									);";
		}
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			log_message('debug', $result->result);
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	function upd_locations_massive( $vars_array )
	{
		

		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_locations_massive(
								". $this->company_db->escape_str($vars_array["family_group_id"]).",
								'". $this->company_db->escape_str($vars_array["location"])."',
								'". $this->company_db->escape_str($vars_array["date"])."'
								);";
			
		
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	function upd_prices_massive( $vars_array )
	{
		
		log_message('debug',  $this->company_db->escape_str($vars_array["special"]));
		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_prices_massive(
								". $this->company_db->escape_str($vars_array["family_group_id"]).",
								". $this->company_db->escape_str($vars_array["by_percentage"]).",
								". $this->company_db->escape_str($vars_array["standard"]).",
								". $this->company_db->escape_str($vars_array["special"]).",
								". $this->company_db->escape_str($vars_array["cost_price_a"]).",
								". $this->company_db->escape_str($vars_array["cost_price_b"]).",
								". $this->company_db->escape_str($vars_array["cost_price_c"]).",
								'". $this->company_db->escape_str($vars_array["date"])."'
								);";
			
		
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	function upd_vats_massive( $vars_array )
	{
		

		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_vats_massive(
								". $this->company_db->escape_str($vars_array["family_group_id"]).",
								". $this->company_db->escape_str($vars_array["vat_id"]).",
								'". $this->company_db->escape_str($vars_array["date"])."'
								);";
			
		
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	function update_qty($vars_array)
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