<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts_m extends CI_Model
{	
	public function get_accounts_like( $text_search )
	{			
		$this->load->database();
		$query = $this->db->query( "CALL get_accounts_like(?);", array($text_search) );
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

	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}	
	
	public function save_contract( $vars_array )		
	{		
		$this->load->database();		
		if( $vars_array["saved_addresses"] == "" )
		{
				if( $vars_array["new_address"] != "" )
				{
					$vars_array["saved_addresses"] = $vars_array["new_address"];
				}
		}
		array_walk($vars_array, "self::clean_vars");
		$query = "CALL ins_contract("
									.$this->db->escape_str($vars_array["account_reference_id"]).","
									.$this->db->escape_str($vars_array["contract_type"]).","				
									.$this->db->escape_str($vars_array["identification_type"]).","	
									."'".$this->db->escape_str($vars_array["identification"])."',"			
									.$this->db->escape_str($vars_array["payment_method"]).","
									.$this->db->escape_str($vars_array["payment_ammount"]).","
									."'".$this->db->escape_str($vars_array["payment_notes"])."',"
									."'".$this->db->escape_str($vars_array["saved_addresses"])."',"
									.$this->db->escape_str($vars_array["delivery_charge"]).","
									."'".$this->db->escape_str($vars_array["notes"])."',".
									"'".date('Y-m-d H:i:s')."',"
									."'".$this->db->escape_str($vars_array["time"])."',"
									."'".$this->db->escape_str(str_replace("/","-",$vars_array["date"]))."',"
									."'".$this->db->escape_str(str_replace("/","-",$vars_array["due_back"]))."'"
									.")";
		log_message('debug', $query);
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