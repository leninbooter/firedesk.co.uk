<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cross_hire_m extends CI_Model
{	
	function abandon_order( $pk_id )
	{
		$this->load->database();
		$query = "call set_cross_hire_order_abandon(".$pk_id.");";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->row() : false;
		
	}

	function activate_order( $pk_id )
	{
		$this->load->database();
		$query = "call set_cross_hire_order_complete(".$pk_id.");";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->row() : false;
	}

	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}
	
	function get_outstanding_orders()
	{
		$this->load->database();
		$query = "SELECT
					DISTINCT(
					po.pk_id),
					po.creation_date,
					po.fk_status,
					po.total_amount,
					s.name
					FROM purchases_orders as po
					INNER JOIN purchases_orders_items as poi ON poi.fk_purchase_order_id = po.pk_id and poi.qty_received < poi.qty and po.fk_status = 2
					INNER JOIN suppliers as s ON s.pk_id = po.fk_supplier_id;";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : false;
	}
	
	public function get_order_details( $pk_id )
	{
		$this->load->database();
		$query = "select fk_supplier_id, delivery_address, contact_name, contact_telephone, contact_email, creation_date, operator, fk_status, ifnull(total_amount,0.00) as 'total_amount',
		s.name,
		s.address1,
		s.telephone1,
		s.telephone2
from cross_hire_orders as po
inner join suppliers as s on po.fk_supplier_id = s.pk_id
where po.pk_id = ".$pk_id;		
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->row() : false;
	}
	
	public function get_order_items( $pk_id )
	{
		$this->load->database();
		$query = "select pk_id, fk_cross_hire_order_id, fk_item_id, description, qty, suppliers_code, rate, perc_discount, total, min_hire_days_charged, due_delivery_date, qty_received, qty_used, qty_idle, qty_rtns, qty_offhire
					from cross_hire_orders_items where fk_cross_hire_order_id = ".$pk_id;
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_hired_items()
	{
		$this->load->database();
		$query ="select
					 s.name as 'hired_from',
					 chois.fk_cross_hire_order_id as 'cross_hire_order_id',
					 chois.cost as 'cost',
					 chois.fk_cross_hire_order_item_id,
					 count(chois.fk_cross_hire_order_item_id) as 'max',
					 choi.description as 'description',
					 choi.fk_item_id as 'stock_id'
				from cross_hire_items_stock as chois
					 INNER JOIN cross_hire_orders_items as choi on choi.pk_id = chois.fk_cross_hire_order_item_id
					 INNER JOIN cross_hire_orders as cho on cho.pk_id = chois.fk_cross_hire_order_id
					 INNER JOIN suppliers as s on s.pk_id = cho.fk_supplier_id
				group by chois.fk_cross_hire_order_item_id;";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	public function get_all_purchase_orders()
	{
		$this->load->database();
		$query = "select
			cho.pk_id, 
			cho.fk_supplier_id, 
			cho.creation_date, 
			cho.fk_status, 
			cho.total_amount,
			s.name,
			sts.description as 'status',
            choi.description
			from cross_hire_orders as cho
				inner join cross_hire_orders_items as choi on choi.fk_cross_hire_order_id = cho.pk_id
				inner join suppliers as s on cho.fk_supplier_id = s.pk_id 
                inner join purchases_orders_status as sts on sts.pk_id = cho.fk_status;";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
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
		
		$query = "CALL generate_cross_hire_ord(
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
		
		$query = "CALL ins_cross_hire_ord_item(
											".$this->db->escape_str($vars_array["order_id"]).",
											".$this->db->escape_str($vars_array["item_id"]).",
											'".$this->db->escape_str($vars_array["description"])."',
											".$this->db->escape_str($vars_array["qty"]).",											
											'".$this->db->escape_str($vars_array["suppliers_code"])."',
											".$this->db->escape_str($vars_array["rate"]).",											
											".$this->db->escape_str($vars_array["disc"]).",	
											".$this->db->escape_str($vars_array["total"])."
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