<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchases_orders_m extends CI_Model
{	
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function abandon_order( $pk_id )
	{
		
		$query = "call set_purchase_order_abandon(".$pk_id.");";
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->row() : false;
		
	}

	function activate_order( $pk_id )
	{
		
		$query = "call set_purchase_order_complete(".$pk_id.");";
		$query =  $this->company_db->query($query);
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
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : false;
	}
	
	function get_order_details( $pk_id )
	{
		
		$query = "select fk_supplier_id, delivery_address, contact_name, contact_telephone, contact_email, creation_date, operator, fk_status, ifnull(total_amount,0.00) as 'total_amount',
		s.name,
		s.address1,
		s.telephone1,
		s.telephone2
from purchases_orders as po
inner join suppliers as s on po.fk_supplier_id = s.pk_id
where po.pk_id = ".$pk_id;		
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->row() : false;
	}
	
	function get_order_items( $pk_id )
	{
		
		$query = "select pk_id, fk_purchase_order_id, fk_item_id, description, qty, suppliers_code, cost, perc_discount, total, `for`, due_delivery_date, qty_received from purchases_orders_items where fk_purchase_order_id = ".$pk_id;
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_all_purchase_orders()
	{
		
		$query = "select
			po.pk_id, 
			po.fk_supplier_id, 
			po.delivery_address, 
			po.contact_name, 
			po.contact_telephone, 
			po.contact_email, 
			po.creation_date, 
			po.operator, 
			po.fk_status, 
			po.total_amount,
			s.name,
			sts.description
			from purchases_orders as po inner join suppliers as s on po.fk_supplier_id = s.pk_id inner join purchases_orders_status as sts on sts.pk_id = po.fk_status ;";
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
	function get_suppliers()
	{
		
		$query =  $this->company_db->query( "select pk_id, name, contact, address1, telephone1, fax from suppliers order by 2;");
		return !empty($query->result()) ? $query->result() : false;
	}
	
	function get_supplier_details( $pk_id )
	{
		
		$query =  $this->company_db->query( "select name, email, telephone1, telephone2, fax, address1, address2, address3, address4, address5, address6, zipcode, contact, banck_name, account_number, swift_code, account_type, bank_acc_addr_1, bank_acc_addr_2, bank_acc_addr_3, bank_telephone, account_credit, account_payment_credit, account_payment_credit_from, account_credit_setlement from suppliers where pk_id = ".$pk_id);
		return !empty($query->result()) ? $query->result() : false;
	}

	
	
	function generate_order( $vars_array )		
	{		
		
		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "CALL generate_purch_ord(
											 ". $this->company_db->escape_str($vars_array["supplier_id"]).",
											'". $this->company_db->escape_str($vars_array["delivery_address"])."',
											'". $this->company_db->escape_str($vars_array["contact_name"])."',
											'". $this->company_db->escape_str($vars_array["contact_phone"])."',
											'". $this->company_db->escape_str($vars_array["contact_email"])."',
											'". $this->company_db->escape_str($vars_array["creation_date"])."',
											NULL
										);";
		$query = str_replace("'NULL'", "NULL", $query);
		
		$query =  $this->company_db->query($query);
		
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return is_numeric($result->result) ? $result->result : false;
		}
		return false;
	}
	
	function save_item( $vars_array )
	{
		
		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "CALL ins_purch_ord_item(
											". $this->company_db->escape_str($vars_array["order_id"]).",
											". $this->company_db->escape_str($vars_array["item_id"]).",
											'". $this->company_db->escape_str($vars_array["description"])."',
											". $this->company_db->escape_str($vars_array["qty"]).",											
											'". $this->company_db->escape_str($vars_array["suppliers_code"])."',
											". $this->company_db->escape_str($vars_array["cost"]).",											
											NULL,
											". $this->company_db->escape_str($vars_array["total"]).",
											'". $this->company_db->escape_str($vars_array["for"])."',
											NULL
										);";
				
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
}