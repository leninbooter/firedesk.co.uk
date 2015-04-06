<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suppliers_m extends CI_Model
{	
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
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
	
	function get_suppliers_addresses()
	{	
		
		$query =  $this->company_db->query( "SELECT pk_id, name, address1, address2, address3, address4, address5, address6  FROM suppliers;" );
		return !empty($query->result()) ? $query->result() : array();
	}

	function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}	
	
	function save_supplier( $vars_array )		
	{		
		
		array_walk($vars_array, "self::clean_vars");
		
		$query = "select pk_id from suppliers where name = '". $this->company_db->escape_str($vars_array["name"])."'";
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			$pk_id = $result->pk_id;
			$query = "UPDATE suppliers SET 	email = '". $this->company_db->escape_str($vars_array["email"])."',
											telephone1 = '". $this->company_db->escape_str($vars_array["telephone1"])."',
											telephone2 = '". $this->company_db->escape_str($vars_array["telephone2"])."',
											fax = '". $this->company_db->escape_str($vars_array["fax"])."',
											address1 = '". $this->company_db->escape_str($vars_array["address1"])."',
											address2 = '". $this->company_db->escape_str($vars_array["address2"])."',
											address3 = '". $this->company_db->escape_str($vars_array["address3"])."',
											address4 = '". $this->company_db->escape_str($vars_array["address4"])."',
											address5 = '". $this->company_db->escape_str($vars_array["address5"])."',
											address6 = '". $this->company_db->escape_str($vars_array["address6"])."',
											zipcode = '". $this->company_db->escape_str($vars_array["zipcode"])."',
											contact = '". $this->company_db->escape_str($vars_array["contact"])."',
											banck_name = '". $this->company_db->escape_str($vars_array["banck_name"])."',
											account_number = '". $this->company_db->escape_str($vars_array["account_number"])."',
											swift_code = '". $this->company_db->escape_str($vars_array["swift_code"])."',
											account_type = ". $this->company_db->escape_str($vars_array["account_type"]).", 
											bank_acc_addr_1 = '". $this->company_db->escape_str($vars_array["bank_acc_addr_1"])."',
											bank_acc_addr_2 = '". $this->company_db->escape_str($vars_array["bank_acc_addr_2"])."',
											bank_acc_addr_3 = '". $this->company_db->escape_str($vars_array["bank_acc_addr_3"])."',
											bank_telephone = '". $this->company_db->escape_str($vars_array["bank_telephone"])."',
											account_credit = ". $this->company_db->escape_str($vars_array["account_credit"]).",
											account_payment_credit = ". $this->company_db->escape_str($vars_array["account_payment_credit"]).",
											account_payment_credit_from = '". $this->company_db->escape_str($vars_array["account_payment_credit_from"])."',
											account_credit_setlement = ". $this->company_db->escape_str($vars_array["account_credit_setlement"])."
										WHERE pk_id = ".$pk_id;
		}else
		{
			$query = "INSERT INTO suppliers (
											name, email, telephone1, telephone2, fax, address1, address2, address3, address4, address5, address6, zipcode, contact, banck_name, account_number, swift_code, account_type, bank_acc_addr_1, bank_acc_addr_2, bank_acc_addr_3, bank_telephone, account_credit, account_payment_credit, account_payment_credit_from, account_credit_setlement
										) 
								VALUES(
									'". $this->company_db->escape_str($vars_array["name"])."',
									'". $this->company_db->escape_str($vars_array["email"])."',
									'". $this->company_db->escape_str($vars_array["telephone1"])."',
									'". $this->company_db->escape_str($vars_array["telephone2"])."',
									'". $this->company_db->escape_str($vars_array["fax"])."',
									'". $this->company_db->escape_str($vars_array["address1"])."',
									'". $this->company_db->escape_str($vars_array["address2"])."',
									'". $this->company_db->escape_str($vars_array["address3"])."',
									'". $this->company_db->escape_str($vars_array["address4"])."',
									'". $this->company_db->escape_str($vars_array["address5"])."',
									'". $this->company_db->escape_str($vars_array["address6"])."',
									'". $this->company_db->escape_str($vars_array["zipcode"])."',
									'". $this->company_db->escape_str($vars_array["contact"])."',
									'". $this->company_db->escape_str($vars_array["banck_name"])."',
									'". $this->company_db->escape_str($vars_array["account_number"])."',
									'". $this->company_db->escape_str($vars_array["swift_code"])."',
									". $this->company_db->escape_str($vars_array["account_type"]).",
									'". $this->company_db->escape_str($vars_array["bank_acc_addr_1"])."',
									'". $this->company_db->escape_str($vars_array["bank_acc_addr_2"])."',
									'". $this->company_db->escape_str($vars_array["bank_acc_addr_3"])."',
									'". $this->company_db->escape_str($vars_array["bank_telephone"])."',
									". $this->company_db->escape_str($vars_array["account_credit"]).",
									". $this->company_db->escape_str($vars_array["account_payment_credit"]).",
									'". $this->company_db->escape_str($vars_array["account_payment_credit_from"])."',
									". $this->company_db->escape_str($vars_array["account_credit_setlement"])."
								)";
		}
		$query = str_replace("'NULL'", "NULL", $query);
		$query =  $this->company_db->query($query);
		$return = array();
		if(!isset($pk_id)) 
		{
			$query = "select last_insert_id() as 'result'";
			$query =  $this->company_db->query($query);			
			$return['type'] = 'insert';
		}else{
			$query = "select row_count() as 'result'";
			$query =  $this->company_db->query($query);
			$return['type'] = 'update';
		}
		$result = $query->result();
		if( !empty($query->result()) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			if(is_numeric($result->result) && ($result->result > 0 || $return['type'] == "update") )
			{	
				$return['result'] = $result->result;
				return $return;
			}
		}
		return false;
	}	
}