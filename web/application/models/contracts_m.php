<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts_m extends CI_Model
{
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	/*function clean_vars(&$value, $key)
	{
		if($value == "")
		{
			$value = "NULL";
		}
	}*/
	
	function get_accounts_like( $text_search )
	{			
		
		$query =  $this->company_db->query( "CALL get_accounts_like(?);", array($text_search) );
		if( $query->num_rows() > 0 )
		{
			$customers = $query->result_array();
			mysqli_next_result(  $this->company_db->conn_id );
			return $customers;
		}else
		{
			return false;
		}
	}	
	
	function get_contract_list( )
	{
		
		$query = "call get_all_contracts()";
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{			
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
		{
			return false;
		}
	}
	
	function get_live_contracts ( $customer_id = 0 )
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
			
		
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
			return array();
	}
	
	function get_outstanding_contracts_orderBy ( $order = 0 )
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
			
		
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
			return array();
	}
	
	function get_outstanding_items ( $contract_id )
	{
		
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
					fk_contract_id = ". $this->company_db->escape_str($contract_id);
		
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
			return array();
	}
	
	function get_contract_status( $contract_id )
	{
		
		$query = "select fk_contract_status_id as result from contracts where pk_id = ".$contract_id;
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return is_numeric($result->result) ? $result->result : false;
		}else
		{
			return false;
		}
	}
	function get_contract_details( $contract_id )
	{
		
		$query = "call get_contract_details(".$contract_id.");";
		$query =  $this->company_db->query($query);
		$result = $query->result(); 
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
		{
			return false;
		}
	}
	
	function get_contract_items( $contract_id )
	{
		
		$query = "call get_contract_items(".$contract_id.");";
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
		{
			return array();
		}
	}

	function get_all_items_not_invoiced( $contract_id )
	{
		
		$query = "select pk_id, item_no, qty_supplied, description, entries_no, rate, regularity, discount_perc, value, item_type from contract_items where invoiced = 0 and qty_supplied > 0 and fk_contract_id = ". $this->company_db->escape_str($contract_id).";";				
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
			return array();
	}
	
	function get_all_items_not_invoiced_not_supplied( $contract_id )
	{
		
		$query = "select pk_id, item_no, qty_supplied, description, entries_no, rate, regularity, discount_perc, value, item_type from contract_items where invoiced = 0 and fk_contract_id = ". $this->company_db->escape_str($contract_id).";";				
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->result();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result;
		}else
			return array();
	}
    
    function insHireItem( $param_arr ) {
        
        array_walk($param_arr, "clean_vars");
        
        $this->company_db->trans_start();
        
        $query = "INSERT INTO contract_items (
                    item_no, 
                    qty, 
                    description, 
                    rate, 
                    value, 
                    fk_contract_id, 
                    item_type
                )
                VALUES(
                    {$param_arr['hireItemID']},
                    1,
                    '{$param_arr['hireItemDescription']}',
                    {$param_arr['hireItemPrice']},	
                    {$param_arr['hireItemPrice']}, 			
                    {$param_arr['contractID']},
                    2		
                );";
                
        $this->company_db->query($query);
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();         
    }
    
    function insHireItemAccesory( $param_array ) {
        log_message('debug', 'insHireItemAccesory');
        array_walk($param_array, "clean_vars");
        
        $this->company_db->trans_start();
        
        $query = "UPDATE contracts SET fk_contract_status_id = 2 WHERE pk_id = {$param_array['contractID']} and fk_contract_status_id = 1;";
        log_message('debug', $query);
        $this->company_db->query($query);
        
        if ($param_array['itemType'] == 1) {
            
            $query = "call update_stock(
                                        '". $this->company_db->escape_str($param_array["date"])."',
                                        ". $this->company_db->escape_str($param_array["item_no"]).",
                                        'Contract sale. Contract no. {$param_array['contractID']}',
                                        ". $this->company_db->escape_str($param_array["qty"]).",
                                        0
									);";
            log_message('debug', $query);
            $query = str_replace("'NULL'", "NULL", $query);
            $query =  $this->company_db->query($query);
            
            if ( !empty($query->result()) ) {
                $result = $query->row();
                mysqli_next_result(  $this->company_db->conn_id );
                if ($result->result != "ok") {
                    return false;
                }
            }else {
                return false;
            }
            log_message('debug', '1 insHireItemAccesory');
        }elseif ($param_array['itemType'] == 2) {
            $query = "UPDATE hire_items SET allocated_on_contract = {$param_array['contractID']} WHERE pk_id = {$param_array['item_no']}";
            log_message('debug', $query);
            $this->company_db->query($query);                    
        }
        
        log_message('debug', '2 insHireItemAccesory');
        $query = "INSERT INTO contract_items (
                item_no, 
                qty, 
                description, 
                rate, 
                value, 
                fk_contract_id, 
                item_type,
                parent_item
            )
            VALUES(
                {$param_array['item_no']},
                {$param_array['qty']},
                '{$param_array['description']}',
                {$param_array['rate']},	
                {$param_array['value']}, 			
                {$param_array['contractID']},
                {$param_array['itemType']},		
                {$param_array['hireItemID']}		
            );";               
        log_message('debug', $query);
        $this->company_db->query($query);
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
    }
	
    function insMultipartItemComponent( $param_array ) {
        
        array_walk($param_array, "clean_vars");
        
        $this->company_db->trans_start();
        
        $query = "UPDATE contracts SET fk_contract_status_id = 2 WHERE pk_id = {$param_array['contractID']} and fk_contract_status_id = 1;";
             
        $this->company_db->query($query);                
        
        $query = "INSERT INTO contract_items (
                    item_no, 
                    qty, 
                    description, 
                    rate, 
                    value, 
                    fk_contract_id, 
                    item_type,
                    parent_item
                )
                VALUES(
                    {$param_array['item_no']},
                    {$param_array['qty']},
                   '{$param_array['description']}',
                    {$param_array['rate']},	
                    {$param_array['value']}, 			
                    {$param_array['contractID']},
                    {$param_array['itemType']},		
                    {$param_array['hireItemID']}		
                );";   
                
        $this->company_db->query($query);
        
        $query = "UPDATE hire_items SET allocated_on_contract = {$param_array['contractID']} WHERE pk_id = {$param_array['item_no']}";
        log_message('debug', $query);
        $this->company_db->query($query);        
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
    }
    
	function save_contract( $vars_array )		
	{		
				
		if( $vars_array["saved_addresses"] == "" )
		{
				if( $vars_array["new_address"] != "" )
				{
					$vars_array["saved_addresses"] = $vars_array["new_address"];
				}
		}
		array_walk($vars_array, "clean_vars");
		$query = "CALL ins_contract("
									. $this->company_db->escape_str($vars_array["account_reference_id"]).","
									. $this->company_db->escape_str($vars_array["contract_type"]).","
									."NULL,"	
									."NULL,"			
									."NULL,"
									."NULL,"
									."NULL,"
									."'". $this->company_db->escape_str($vars_array["saved_addresses"])."',"
									. $this->company_db->escape_str($vars_array["delivery_charge"]).","
									."'". $this->company_db->escape_str($vars_array["notes"])."',".
									"'".date('Y-m-d H:i:s')."',"
									."'". $this->company_db->escape_str($vars_array["time"])."',"
									."'". $this->company_db->escape_str($vars_array["date"])."',"
									."NULL,"
									. $this->company_db->escape_str($vars_array["type"])."
								)";		
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			log_message('debug', $result->result);
			return is_numeric($result->result) ? $result->result : false;
		}else
		{
			return false;
		}			
	}
	
	function save_contract_item( $vars_array )		
	{		
				
		array_walk($vars_array, "clean_vars");
		$query = "CALL ins_contract_item("
									. $this->company_db->escape_str($vars_array["item_no"]).","
									. $this->company_db->escape_str($vars_array["qty"]).","				
									."'". $this->company_db->escape_str($vars_array["description"])."',"			
									. $this->company_db->escape_str($vars_array["entry"]).","
									. $this->company_db->escape_str($vars_array["rate_per"]).","
									. $this->company_db->escape_str($vars_array["regularity"]).","
									. $this->company_db->escape_str($vars_array["disc"]).","
									. $this->company_db->escape_str($vars_array["value"]).","
									. $this->company_db->escape_str($vars_array["contract_id"]).","
									. $this->company_db->escape_str($vars_array["item_type"])
									.")";		
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result->result == "true" ? true : false;
		}else
		{
			return false;
		}			
	}
	
	function save_item_supplied( $vars_array )
	{
		
		log_message('debug', "database loaded");
		array_walk($vars_array, "clean_vars");
		log_message('debug', "vars_array cleaned");
		$query = "CALL save_item_supplied(
		". $this->company_db->escape_str($vars_array['contract_id']).",
		". $this->company_db->escape_str($vars_array['item_id']).",
		". $this->company_db->escape_str($vars_array['now'])."
		)";
		log_message('debug', "query formed");
		$query = str_replace("'NULL'", "NULL", $query);
		log_message('debug', $query);
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result))
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id);
			return $result->result == "true" ? true : false;
		}else
			return false;
	}
	
	function set_contract_abandoned( $contract_id )
	{
		
		$query = "call set_contract_abandoned(".$contract_id.")";
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result->result == 'true' ? true : false;
		}else
		{
			return false;
		}
	}
	
	function set_contract_active( $contract_id )
	{
		
		$query = "call set_contract_active(".$contract_id.")";
		$query =  $this->company_db->query($query);
		$result = $query->result();
		if( !empty($result) )
		{
			$result = $query->row();
			mysqli_next_result(  $this->company_db->conn_id );
			return $result->result == 'true' ? true : false;
		}else
		{
			return false;
		}
	}
	
	
}