<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_m extends CI_Model
{		
	function clean_vars(&$value, $key)
	{
		if($value == "" && $value != "0")
		{
			$value = "NULL";
		}
	}
	
	public function get_items_soled_by( $supplier_id )
	{
		$this->load->database();
		$query = $this->db->query( "call get_items_obtained_from(".$supplier_id.");");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_item_details( $pk_id )
	{
		$this->load->database();
		$query = $this->db->query( "SELECT * FROM sales_stock where pk_id = ".$pk_id);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_items_from_family_from_item ( $pk_id )
	{
		$this->load->database();
		$query = $this->db->query( "CALL get_items_from_family_of(".$pk_id.");");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_item_prices( $pk_id )
	{
		$this->load->database();
		$query = "SELECT fk_customer_id, price_type, min_qty, max_qty, price FROM stock_item_prices WHERE fk_stock_item_id = ".$pk_id;
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_items_stock_levels( )
	{
		$this->load->database();
		$query = $this->db->query( "SELECT pk_id, stock_number, description, location, quantity_rec_level, quantity_balance FROM sales_stock;");
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function save_item( $vars_array )		
	{		
		$this->load->database();
		array_walk($vars_array, "self::clean_vars");
		
		$query = "call ins_stock_item(
									'".$this->db->escape_str($vars_array["stock_number"])."',
									'".$this->db->escape_str($vars_array["location"])."',
									'".$this->db->escape_str($vars_array["description"])."',
									 ".$this->db->escape_str($vars_array["quantity_rec_level"]).",
									 ".$this->db->escape_str($vars_array["standard_price"]).",
									 ".$this->db->escape_str($vars_array["special_price"]).",
									 ".$this->db->escape_str($vars_array["units_of_for_special"]).",
									 ".$this->db->escape_str($vars_array["cost_price_a"]).",
									 ".$this->db->escape_str($vars_array["cost_price_b"]).",
									 ".$this->db->escape_str($vars_array["cost_price_c"]).",
									 ".$this->db->escape_str($vars_array["fk_vat_code"]).",
									 ".$this->db->escape_str($vars_array["fk_family_group"]).",
									 ".$this->db->escape_str($vars_array["fk_discount_group"]).",
									 ".$this->db->escape_str($vars_array["fk_supplier_a"]).",
									 ".$this->db->escape_str($vars_array["fk_supplier_b"]).",
									 ".$this->db->escape_str($vars_array["fk_supplier_c"])."
									);";
		$query = str_replace("'NULL'", "NULL", $query);
		$query = $this->db->query($query);
						
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
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
	
	public function ins_items_receipts( $vars_array )
	{
		$this->load->database();		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "CALL ins_items_receipts(
											".$this->db->escape_str($vars_array["order_id"]).",
											".$this->db->escape_str($vars_array["purchase_order_item_id"]).",
											".$this->db->escape_str($vars_array["qty"]).",
											".$this->db->escape_str($vars_array["cost"]).",
											'".$this->db->escape_str($vars_array["date"])."',
											0
											);";
		log_message('debug', $query);
		$query = str_replace("'NULL'", "NULL", $query);		
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return $result->result == "ok" ? true : false;
		}
		return false;
	}
	
	public function ins_up_item_price($item)
	{
		$this->load->database();
		array_walk($item, "self::clean_vars");
		
		$query = "call save_price(".$this->db->escape_str($item['customer_id']).",".$this->db->escape_str($item["stock_item_id"]).",".$this->db->escape_str($item["price"]).",".$this->db->escape_str($item["price_type"]).",".$this->db->escape_str($item["min"]).",".$this->db->escape_str($item["max"]).");";		
		
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			log_message('debug', $result->result);
			mysqli_next_result( $this->db->conn_id );					
			if($result->result == "ok" || $result->result == "notUpdated")
				return true;
		}
		return false;
	}
	
	public function upd_balances_massive( $vars_array )
	{
		$this->load->database();
		array_walk($vars_array, "self::clean_vars");
		log_message('debug', $vars_array["apply_to"]);
		if($vars_array["apply_to"] == "Family group")
		{
			$query = "call update_balances_massive(
									".$this->db->escape_str($vars_array["family_group_id"]).",
									".$this->db->escape_str($vars_array["negative_balances"]).",
									'".$this->db->escape_str($vars_array["date"])."'
									);";
			
		}elseif( $vars_array["apply_to"] == "Entire stock")
		{
			$query = "call update_balances_massive(
									NULL,
									".$this->db->escape_str($vars_array["negative_balances"]).",
									'".$this->db->escape_str($vars_array["date"])."'
									);";
		}
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			log_message('debug', $result->result);
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	public function upd_locations_massive( $vars_array )
	{
		$this->load->database();

		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_locations_massive(
								".$this->db->escape_str($vars_array["family_group_id"]).",
								'".$this->db->escape_str($vars_array["location"])."',
								'".$this->db->escape_str($vars_array["date"])."'
								);";
			
		
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	public function upd_prices_massive( $vars_array )
	{
		$this->load->database();
		log_message('debug', $this->db->escape_str($vars_array["special"]));
		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_prices_massive(
								".$this->db->escape_str($vars_array["family_group_id"]).",
								".$this->db->escape_str($vars_array["by_percentage"]).",
								".$this->db->escape_str($vars_array["standard"]).",
								".$this->db->escape_str($vars_array["special"]).",
								".$this->db->escape_str($vars_array["cost_price_a"]).",
								".$this->db->escape_str($vars_array["cost_price_b"]).",
								".$this->db->escape_str($vars_array["cost_price_c"]).",
								'".$this->db->escape_str($vars_array["date"])."'
								);";
			
		
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	public function upd_vats_massive( $vars_array )
	{
		$this->load->database();

		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_vats_massive(
								".$this->db->escape_str($vars_array["family_group_id"]).",
								".$this->db->escape_str($vars_array["vat_id"]).",
								'".$this->db->escape_str($vars_array["date"])."'
								);";
			
		
										
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	public function update_qty($vars_array)
	{
		$this->load->database();
		array_walk($vars_array, "self::clean_vars");
		
		$query = "call update_stock(
									'".$this->db->escape_str($vars_array["date"])."',
									".$this->db->escape_str($vars_array["fk_item_id"]).",
									'".$this->db->escape_str($vars_array["description"])."',
									".$this->db->escape_str($vars_array["qty"]).",
									".$this->db->escape_str($vars_array["cost"])."
									);";
		$query = str_replace("'NULL'", "NULL", $query);
		$query = $this->db->query($query);
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return $result->result == "ok" ? $result->result : false;
		}
		return false;
	}
}