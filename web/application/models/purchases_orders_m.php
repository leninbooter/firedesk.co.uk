<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchases_orders_m extends CI_Model
{	
	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}
	
	public function get_order_details( $pk_id )
	{
		$this->load->database();
		$query = "select fk_supplier_id, delivery_address, contact_name, contact_telephone, contact_email, creation_date, operator, fk_status, total_amount,
		s.name,
		s.address1,
		s.telephone1,
		s.telephone2
from purchases_orders as po
inner join suppliers as s on po.fk_supplier_id = s.pk_id
where po.pk_id = ".$pk_id;		
		$query = $this->db->query($query);
		log_message('debug', $query->row()->name);
		return !empty($query->result()) ? $query->row() : false;
	}
	
	public function get_suppliers()
	{
		$this->load->database();
		$query = $this->db->query( "select pk_id, name, contact, address1, telephone1, fax from suppliers order by 2;");
		return !empty($query->result()) ? $query->result() : false;
	}
	
	public function get_supplier_details( $pk_id )
	{
		$this->load->database();
		$query = $this->db->query( "select name, email, telephone1, telephone2, fax, address1, address2, address3, address4, address5, address6, zipcode, contact, banck_name, account_number, swift_code, account_type, bank_acc_addr_1, bank_acc_addr_2, bank_acc_addr_3, bank_telephone, account_credit, account_payment_credit, account_payment_credit_from, account_credit_setlement from suppliers where pk_id = ".$pk_id);
		return !empty($query->result()) ? $query->result() : false;
	}

	
	
	public function generate_order( $vars_array )		
	{		
		$this->load->database();
		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "CALL generate_purch_ord(
											 ".$this->db->escape_str($vars_array["supplier_id"]).",
											'".$this->db->escape_str($vars_array["delivery_address"])."',
											'".$this->db->escape_str($vars_array["contact_name"])."',
											'".$this->db->escape_str($vars_array["contact_phone"])."',
											'".$this->db->escape_str($vars_array["contact_email"])."',
											'".$this->db->escape_str($vars_array["creation_date"])."',
											NULL
										);";
		$query = str_replace("'NULL'", "NULL", $query);
		
		$query = $this->db->query($query);
		
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result( $this->db->conn_id );
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}

	public function save_item( $vars_array )
	{
		$this->load->database();
		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "CALL ins_purch_ord_item(
											".$this->db->escape_str($vars_array["order_id"]).",
											".$this->db->escape_str($vars_array["item_id"]).",
											'".$this->db->escape_str($vars_array["description"])."',
											".$this->db->escape_str($vars_array["qty"]).",											
											'".$this->db->escape_str($vars_array["suppliers_code"])."',
											".$this->db->escape_str($vars_array["cost"]).",											
											NULL,
											".$this->db->escape_str($vars_array["total"]).",
											'".$this->db->escape_str($vars_array["for"])."',
											NULL
										);";
				
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
}