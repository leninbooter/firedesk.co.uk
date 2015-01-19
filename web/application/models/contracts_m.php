<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts_m extends CI_Model
{	
	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}
	
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
	
	public function get_contract_list( )
	{
		$this->load->database();
		$query = "call get_all_contracts()";
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{			
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
		{
			return false;
		}
	}
	
	public function get_live_contracts ( $customer_id = 0 )
	{
		$query = "select
						customers.name,
						contracts.pk_id,
						contracts.creation_date,
						contracts.date,
						contracts.dueback
					from
						contracts join customers on customers.pk_id = contracts.fk_customer_id
								  join contract_status on contract_status.pk_id = contracts.fk_contract_status_id 
					where contracts.fk_contract_status_id = 3";
		if( $customer_id > 0 )
			$query = $query." and fk_customer_id = ".$customer_id;
			
		$this->load->database();
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
			return array();
	}
	
	public function get_outstanding_contracts_orderBy ( $order = 0 )
	{
		$query = "SELECT
					customers.name,
					contracts.pk_id,
					contracts.creation_date,
					contracts.date,
					contracts.dueback
				FROM
					contracts
						JOIN
					customers ON customers.pk_id = contracts.fk_customer_id
						JOIN
					contract_status ON contract_status.pk_id = contracts.fk_contract_status_id
						JOIN
					contract_items ON contracts.pk_id = contract_items.fk_contract_id
				WHERE
					contract_items.qty > contract_items.qty_supplied and contracts.fk_contract_status_id = 3
				GROUP BY 2";
		if( $order > 0 and $order < 6 )
			$query = $query." ORDER BY ".$order;
		else return array();
			
		$this->load->database();
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
			return array();
	}
	
	public function get_outstanding_items ( $contract_id )
	{
		$this->load->database();
		$query = "SELECT
					contract_items.pk_id,
					contract_items.item_no,
					contract_items.description,
					contract_items.rate,
					contract_items.qty,
					contract_items.qty_supplied
				FROM
					contract_items
				WHERE
					fk_contract_id = ".$this->db->escape_str($contract_id);
		
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
			return array();
	}
	
	public function get_contract_status( $contract_id )
	{
		$this->load->database();
		$query = "select fk_contract_status_id as result from contracts where pk_id = ".$contract_id;
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return is_numeric($result->result) ? $result->result : false;
		}else
		{
			return false;
		}
	}
	public function get_contract_details( $contract_id )
	{
		$this->load->database();
		$query = "call get_contract_details(".$contract_id.");";
		$query = $this->db->query($query);
		$result = $query->result(); 
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
		{
			return false;
		}
	}
	
	public function get_contract_items( $contract_id )
	{
		$this->load->database();
		$query = "call get_contract_items(".$contract_id.");";
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
		{
			return array();
		}
	}

	public function get_all_items_not_invoiced( $contract_id )
	{
		$this->load->database();
		$query = "select item_no, qty_supplied, description, entries_no, rate, regularity, discount_perc, value, item_type from contract_items where invoiced = 0 and fk_contract_id = ".$this->db->escape_str($contract_id).";";				
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result( $this->db->conn_id );
			return $result;
		}else
			return array();
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
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			log_message('debug', $result->result);
			return is_numeric($result->result) ? $result->result : false;
		}else
		{
			return false;
		}			
	}
	
	public function save_contract_item( $vars_array )		
	{		
		$this->load->database();		
		array_walk($vars_array, "self::clean_vars");
		$query = "CALL ins_contract_item("
									.$this->db->escape_str($vars_array["item_no"]).","
									.$this->db->escape_str($vars_array["qty"]).","				
									."'".$this->db->escape_str($vars_array["description"])."',"			
									.$this->db->escape_str($vars_array["entry"]).","
									.$this->db->escape_str($vars_array["rate_per"]).","
									.$this->db->escape_str($vars_array["regularity"]).","
									.$this->db->escape_str($vars_array["disc"]).","
									.$this->db->escape_str($vars_array["value"]).","
									.$this->db->escape_str($vars_array["contract_id"]).","
									.$this->db->escape_str($vars_array["item_type"])
									.")";		
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return $result->result == "true" ? true : false;
		}else
		{
			return false;
		}			
	}
	
	public function save_item_supplied( $vars_array )
	{
		$this->load->database();
		log_message('debug', "database loaded");
		array_walk($vars_array, "self::clean_vars");
		log_message('debug', "vars_array cleaned");
		$query = "CALL save_item_supplied(
		".$this->db->escape_str($vars_array['contract_id']).",
		".$this->db->escape_str($vars_array['item_id']).",
		".$this->db->escape_str($vars_array['now'])."
		)";
		log_message('debug', "query formed");
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result))
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id);
			return $result->result == "true" ? true : false;
		}else
			return false;
	}
	
	public function set_contract_abandoned( $contract_id )
	{
		$this->load->database();
		$query = "call set_contract_abandoned(".$contract_id.")";
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return $result->result == 'true' ? true : false;
		}else
		{
			return false;
		}
	}
	
	public function set_contract_active( $contract_id )
	{
		$this->load->database();
		$query = "call set_contract_active(".$contract_id.")";
		$query = $this->db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return $result->result == 'true' ? true : false;
		}else
		{
			return false;
		}
	}
	
	
}