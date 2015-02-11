<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vats_m extends CI_Model
{		
	public function get_all_vats()
	{
		$this->load->database();
		$query = $this->db->query( "select * from vats;");
		return !empty($query->result()) ? $query->result() : array();
	}	

	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}	
	
	public function save_vat( $vars_array )		
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
	
	public function update_vat($vars_array)
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