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
    function deleteCrossHiredItem($param_arr) {
        
        $this->company_db->trans_start();
        
        $query = "SELECT cchi.fk_cross_hire_order_item_id, cchi.qty FROM contract_cross_hire_items as cchi
                     WHERE cchi.pk_id = {$param_arr['crossHiredItemRowID']}";        
        $contractItemRow = $this->company_db->query($query)->row();
        
   
        
        $query = "DELETE FROM contract_cross_hire_items WHERE pk_id = {$param_arr['crossHiredItemRowID']} AND fk_contract_id = {$param_arr['contractID']}";
        $this->company_db->query($query);
        
        
        $query = "UPDATE cross_hire_orders_items 
                    SET qty_used = qty_used - {$contractItemRow->qty}, qty_idle = qty_idle + {$contractItemRow->qty}
                    WHERE pk_id = {$contractItemRow->fk_cross_hire_order_item_id}";
        $this->company_db->query($query);   

        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
        
    }
    
    function deleteHiredItem($param_arr ) {
        
        $this->company_db->trans_start();
        
        $query              = "SELECT item_no, qty, item_type, non_hire_fleet_trans FROM contract_items WHERE pk_id = {$param_arr['hireItemRowID']}";
        $contractItemRow    = $this->company_db->query($query);
        
        $childrenItems  = $this->company_db->query("SELECT  ci.pk_id, 
                                                            ci.item_no, 
                                                            ci.item_type, 
                                                            ci.qty,
                                                            hi.fk_type,
                                                            ci.non_hire_fleet_trans
                                                    FROM 
                                                        contract_items as ci 
                                                        INNER JOIN hire_items as hi ON hi.pk_id = ci.item_no
                                                    WHERE parent_item = {$contractItemRow->row()->item_no}
                                                        AND fk_contract_id = {$param_arr['contractID']}")->result();
        foreach( $childrenItems as $item ) {
            
            if ($item->item_type == "1") { // sold item
                
                $query = "UPDATE sales_stock SET quantity_balance = quantity_balance + {$item->qty} WHERE pk_id = {$item->item_no}";
                $this->company_db->query($query);
                
                $query = "INSERT INTO stock_movements (date, fk_item_id,  description, qty, cost)
                            VALUES ('{$param_arr['date']}',
                                     {$item->item_no}, 
                                    ' Contract no. {$param_arr['contractID']} Item removed', 
                                    {$item->qty}, 
                                    0);";
                $this->company_db->query($query);
                
            }elseif ($item->item_type == "2") { // hired item
                
                if ($item->non_hire_fleet_trans == null) {
                    
                    $query = "UPDATE hire_items SET allocated_on_contract = NULL WHERE pk_id = {$contractItemRow->row()->item_no}";
                    $this->company_db->query($query);
                    
                    $query = "UPDATE hire_items SET qty = qty + {$item->qty} WHERE pk_id =  {$item->item_no} AND fk_type IN (2); ";
                    $this->company_db->query($query);
                }                               
                
            }
            
            $query = "DELETE FROM contract_items WHERE pk_id = {$item->pk_id}";
            $this->company_db->query($query);
        }
        
        if ($contractItemRow->row()->non_hire_fleet_trans == null) {
                    
            $query = "UPDATE hire_items SET allocated_on_contract = NULL WHERE pk_id =  {$contractItemRow->row()->item_no}";
            $this->company_db->query($query);
            
            $query = "UPDATE hire_items SET qty = qty + {$contractItemRow->row()->qty} WHERE pk_id =  {$contractItemRow->row()->item_no} AND fk_type IN (2); ";
            $this->company_db->query($query);
        }
        
        $query = "DELETE FROM contract_items WHERE pk_id = {$param_arr['hireItemRowID']}";
        $this->company_db->query($query);
            
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
    }
	
    function deleteSoldItem( $param_arr ) {
        
        $this->company_db->trans_start();

        $query              = "SELECT item_no, qty FROM contract_items WHERE pk_id = {$param_arr['saleItemRowID']}";
        $contractItemRow    = $this->company_db->query($query);

        $query              = "UPDATE sales_stock SET quantity_balance = quantity_balance + {$contractItemRow->row()->qty} WHERE pk_id = {$contractItemRow->row()->item_no}";
        $this->company_db->query($query);
        
        $query              = "INSERT INTO stock_movements (date, fk_item_id,  description, qty, cost)
                                VALUES ('{$param_arr['date']}',
                                         {$contractItemRow->row()->item_no}, 
                                        ' Contract no. {$param_arr['contractID']} Item removed', 
                                        {$contractItemRow->row()->qty}, 
                                        0);";
        $this->company_db->query($query);
        
        $query              = "DELETE FROM contract_items WHERE pk_id = {$param_arr['saleItemRowID']}";
        $this->company_db->query($query);
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
        
    }
    
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

    /**
    *
    * Retrieve the details of the contract where an item from the hire fleet is allocated on
    *
    * @param    $itemID Pk ID of the item to be queried.
    * @return   Array object with the details of the contract that has the item hired. 
    *           False if it is not hired on any contract
    *
    */
    function isHireFleetItemAllocated( $itemID ) {
        
        $possesingContractquery = "SELECT allocated_on_contract FROM hire_items WHERE pk_id = {$itemID}";
        $possesingContractquery = $this->company_db->query($possesingContractquery)->row_array();
        
        if ( $possesingContractquery['allocated_on_contract'] == NULL) {
            
            return false;
        }else {
            
            return true;
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
    
    /**
    *
    * Retrieve information about the hiring contract of a determined hire fleet
    * item
    *
    * @param Int ID of the hire fleet item that is allocated in a contract
    * @return   Array object of data about the hiring contract of the item ID 
    *           queried.
    *
    */
    
    function getHiringContractDetailsOf( $hireFleetItemID ) {
        
        $query = "SELECT 
                    hi.fleet_number,
                    hi.description,                    
                    c.delivery_address,                     
                    c.creation_date,                     
                    ch.date_time,                    
                    cus.name, 
                    cus.address                                          
                  FROM hire_items as hi
                    INNER JOIN contracts as c ON c.pk_id = hi.allocated_on_contract
                    LEFT JOIN contracts_history as ch ON ch.fk_contract_id = c.pk_id AND fk_change_to = 3
                    INNER JOIN customers AS cus ON cus.pk_id = c.fk_customer_id
                  WHERE hi.pk_id = {$contractID}";
                  
                  
        $query = $this->company_db->query($query);
        return !empty($query->result()) ? $query->row_array() : array();
    }
    
	function getContractDetails( $contractID )
	{
		$query = "SELECT 
                    c.pk_id,
                    c.fk_customer_id, 
                    c.fk_contract_type_id, 
                    c.fk_identification_type_id, 
                    c.identification, 
                    c.fk_payment_type_id, 
                    c.payment_amount, 
                    c.payment_notes, 
                    c.delivery_address, 
                    c.delivery_charge, 
                    c.notes, 
                    c.creation_date, 
                    c.time, 
                    c.date, 
                    c.dueback, 
                    c.fk_contract_status_id,
                    cs.name as status_name,
                    ch.date_time,
                    cus.order_number, 
                    cus.name, 
                    cus.address, 
                    cus.telephone, 
                    cus.fax, 
                    cus.email, 
                    cus.contact_name, 
                    cus.representative, 
                    cus.vat_exempt, 
                    cus.creation_date, 
                    cus.type, 
                    cus.invoicing, 
                    cus.account_reference, 
                    cus.days_week, 
                    cus.holiday_credit, 
                    cus.prices, 
                    cus.status, 
                    cus.credit_limit, 
                    cus.statement_address, 
                    cus.subaccount                       
                  FROM contracts as c
                    LEFT JOIN contracts_history as ch ON ch.fk_contract_id = c.pk_id AND fk_change_to = 3
                    INNER JOIN customers AS cus ON cus.pk_id = c.fk_customer_id
                    INNER JOIN contract_status as cs ON cs.pk_id = c.fk_contract_status_id
                  WHERE c.pk_id = {$contractID}";
                                   
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
		
		$query = "SELECT 
                    pk_id, item_no, qty_supplied, description, entries_no, rate, 
                    regularity, discount_perc, value, item_type 
                FROM contract_items 
                WHERE invoiced = 0 and qty_supplied > 0 
                    AND fk_contract_id = ". $this->company_db->escape_str($contract_id).";";				
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
		
		$query = "select pk_id, item_no, qty_supplied, description, entries_no, 
                    rate, regularity, discount_perc, value, item_type 
                from contract_items where invoiced = 0 
                    and fk_contract_id = ". $this->company_db->escape_str($contract_id).";";				
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
    
    function insCrossHireItem( $param_arr ) {
        
        array_walk($param_arr, "clean_vars");
        
        $this->company_db->trans_start();
        
        $query = "INSERT INTO contract_cross_hire_items (
                    fk_contract_id, 
                    fk_cross_hire_order_item_id, 
                    rate, 
                    day1, 
                    day2, 
                    day3, 
                    week, 
                    wend,
                    qty
                    )
                VALUES (
                    {$param_arr['contractID']}, 
                    {$param_arr['crossHireOrderItemID']}, 
                    {$param_arr['rate']}, 
                    {$param_arr['day1']}, 
                    {$param_arr['day2']}, 
                    {$param_arr['day3']}, 
                    {$param_arr['week']}, 
                    {$param_arr['wend']},
                    {$param_arr['qty']}
                   ) ;";
        $this->company_db->query($query);
        
        $query = "UPDATE cross_hire_orders_items 
                    SET qty_used = qty_used + {$param_arr['qty']}, qty_idle = qty_idle - {$param_arr['qty']}
                    WHERE pk_id = {$param_arr['crossHireOrderItemID']}";
        $this->company_db->query($query);        
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
    }
    
    function insHireItem( $param_arr ) {
        
        array_walk($param_arr, "clean_vars");
        
        $this->company_db->trans_start();
        
        if ( $param_arr['allocated'] != "yes" ) {
        
            if ( $this->isHireFleetItemAllocated($param_arr['hireItemID']) ) {
            
                return false;
            }
            
        }else {
            
            $q = "SELECT allocated_on_contract FROM hire_items WHERE pk_id = {$param_arr['hireItemID']}";            
            $possesingContractID = $this->company_db->query($q)->row_array()['allocated_on_contract'];
            
            $query = "UPDATE contract_items SET non_hire_fleet_trans = 1
                        WHERE item_no = {$param_arr['hireItemID']}
                            AND item_type = 2
                            AND fk_contract_id = {$possesingContractID}";
            log_message('debug', $query);
            $this->company_db->query($query);
            
            /*$query = "UPDATE hire_items SET allocated_on_contract = NULL
                        WHERE pk_id = {$param_arr['hireItemID']}";
            $this->company_db->query($query);*/
        }
        
        if ($param_arr['hireItemType'] != "Multiple") {
            
            $query = "UPDATE hire_items SET allocated_on_contract = {$param_arr['contractID']}
                        WHERE pk_id = {$param_arr['hireItemID']}";
            $this->company_db->query($query);
            
        }else {
            
            $query = "UPDATE hire_items SET qty = qty - {$param_arr['hireItemQty']}
                        WHERE pk_id = {$param_arr['hireItemID']}";
            $this->company_db->query($query);
        }
        
        
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
                    {$param_arr['hireItemQty']},
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

        array_walk($param_array, "clean_vars");
        
        $this->company_db->trans_start();
        
        $query = "UPDATE contracts SET fk_contract_status_id = 2 WHERE pk_id = {$param_array['contractID']} and fk_contract_status_id = 1;";

        $this->company_db->query($query);
        
        if ($param_array['itemType'] == 1) {
            
            $query              = "UPDATE sales_stock SET quantity_balance = quantity_balance - {$param_array["qty"]} WHERE pk_id =  {$param_array["item_no"]}";
            $this->company_db->query($query);
            
            $query = "INSERT INTO stock_movements (date, fk_item_id,  description, qty, cost)
                                VALUES ('{$param_array['date']}',
                                         {$param_array["item_no"]}, 
                                        ' Contract no. {$param_array['contractID']} Item added', 
                                        -{$param_array["qty"]}, 
                                        0);";

            $query = str_replace("'NULL'", "NULL", $query);            
            
            if ( !$this->company_db->query($query) ) {
                return false;
            }
            
        }elseif ($param_array['itemType'] == 2) {
            $query = "UPDATE hire_items SET allocated_on_contract = {$param_array['contractID']} WHERE pk_id = {$param_array['item_no']}  AND fk_type IN (1,3,4);";
            $this->company_db->query($query);                    
            
            $query = "UPDATE hire_items SET qty = qty - {$param_array['qty']} WHERE pk_id = {$param_array['item_no']}  AND fk_type IN (2);";
            $this->company_db->query($query); 
        }
        
        $query = "INSERT INTO contract_items (
                item_no, 
                qty, 
                description, 
                discount_perc,
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
                {$param_array['disc']},
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
	
    /**
    *
    * Add the components and its parent of a hire item to a contract
    *
    * *Missing working with bundles
    *
    * @param $param_array 
    * @return Bool true in case of succesful transaction. False otherwise
    *
    */
    function insMultipartItemComponent( $param_array ) {
        
        array_walk($param_array, "clean_vars");
        
        $this->company_db->trans_start();
        
        $query = "UPDATE contracts SET fk_contract_status_id = 2
                    WHERE pk_id = {$param_array['contractID']} 
                        AND fk_contract_status_id = 1;";
             
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
                VALUES (
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
        
        $query = "UPDATE hire_items 
                    SET 
                        allocated_on_contract = {$param_array['contractID']}
                    WHERE pk_id = {$param_array['item_no']}
                        AND fk_type IN (1,3,4)";
        $this->company_db->query($query);

        $query = "UPDATE hire_items 
                    SET 
                        qty = qty - {$param_array['qty']}
                WHERE pk_id = {$param_array['item_no']}
                    AND fk_type IN (2)";
        $this->company_db->query($query);                        
        
        $this->company_db->trans_complete();
        return $this->company_db->trans_status();
    }
    
    function insSaleItem( $param_arr ) {
        
        array_walk($param_arr, "clean_vars");

        $this->company_db->trans_start();
        
        $query = "UPDATE contracts SET fk_contract_status_id = 2 WHERE pk_id = {$param_arr['contractID']} and fk_contract_status_id = 1;";             
        $this->company_db->query($query);   

        $param_arr["date"]              = $this->company_db->escape_str($param_arr["date"]);
        $param_arr["saleStockItemID"]   = $this->company_db->escape_str($param_arr["saleStockItemID"]);
        $param_arr["qty"]               = $this->company_db->escape_str($param_arr["qty"]);
        $query                          = "INSERT INTO stock_movements (date, fk_item_id,  description, qty, cost)
                                            VALUES ('{$param_arr['date']}',
                                                    {$param_arr['saleStockItemID']}, 
                                                    'Contract sale. Contract no. {$param_arr['contractID']}', 
                                                    -{$param_arr['qty']}, 
                                                    0);";
        $query                          = str_replace("'NULL'", "NULL", $query);
        $this->company_db->query($query);        
        
        $query = "UPDATE sales_stock 
                    SET quantity_balance = quantity_balance - {$param_arr['qty']},
                        last_movement = '{$param_arr['date']}'
                    WHERE pk_id = {$param_arr['saleStockItemID']};";
        $query = str_replace("'NULL'", "NULL", $query);
        $this->company_db->query($query);
                
                
        $query = "INSERT INTO contract_items (
                item_no, 
                qty, 
                description, 
                rate, 
                discount_perc, 
                value, 
                fk_contract_id, 
                item_type
            )
            VALUES(
                {$param_arr['saleStockItemID']},
                {$param_arr['qty']},
                '{$param_arr['description']}',
                {$param_arr['price']},	
                {$param_arr['discount']}, 			
                {$param_arr['total']}, 			
                {$param_arr['contractID']},
                1	
            );";               
        
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
    
    function selectChargingBandsUsed( $contractID ) {
        
        $query = 'SELECT name, _4hr, _8hr, _1day, _2day, _3day, _4day, _5day,
                        _6day, week, weekend, subsequent_days, days_per_week,
                        thereafter, min_days
                  FROM hire_items_charging_bands
                  WHERE pk_id in (
                                    SELECT DISTINCT hicb.pk_id
									FROM contract_items as ci
										INNER JOIN hire_items as hi ON ci.item_no = hi.pk_id AND ci.item_type = 2
										INNER JOIN hire_items_family_groups as hifg ON hifg.pk_id = hi.fk_family_group
										INNER JOIN hire_items_charging_bands as hicb ON hicb.pk_id = hifg.fk_charging_band    
									WHERE ci.fk_contract_id = '.$contractID.'
									AND ( (ci.item_type = 1 AND ci.parent_item is not null )
											OR (ci.item_type = 2) )
                                )';
                                
        $query = $this->company_db->query($query);
        return !empty($query->result()) ? $query->result() : array();                        
    }
    
    function selectHiredItems( $contractID ) {
        
         $query = 'SELECT 
                    ci.pk_id,
                    ifnull(ss.stock_number, hi.fleet_number) as productID,
                    ci.parent_item,
                    ci.item_no, 
                    ci.qty, 
                    ci.description, 
                    ci.rate, 
                    ci.discount_perc, 
                    value,
                    ci.non_hire_fleet_trans,
                    hicb.name as charging_band
                FROM  
                    contract_items as ci
                    LEFT JOIN sales_stock as ss ON ss.pk_id = ci.item_no AND ci.item_type = 1
                    LEFT JOIN hire_items as hi ON hi.pk_id = ci.item_no AND ci.item_type = 2
                    LEFT JOIN hire_items_family_groups as hifg ON hifg.pk_id = hi.fk_family_group
                    LEFT JOIN hire_items_charging_bands as hicb ON hicb.pk_id = hifg.fk_charging_band
                WHERE 
                    ci.fk_contract_id = '.$contractID.'
                    AND ( (ci.item_type = 1 AND ci.parent_item is not null)
                    OR (ci.item_type = 2) )';
        $query = $this->company_db->query($query);
        return !empty($query->result()) ? $query->result() : array();
        
    }
    
    function selectCrossedHiredItems($contractID) {
        
        $query = "SELECT
                    cchi.pk_id,
                    ss.stock_number,
                    choi.description,
                    cchi.qty,
                    cchi.rate,
                    concat(cchi.day1,'%') as day1,
                    concat(cchi.day2,'%') as day2,
                    concat(cchi.day3,'%') as day3,
                    concat(cchi.week,'%') as week,
                    concat(cchi.wend,'%') as wend,
                    0 as discount
                FROM contract_cross_hire_items as cchi
                    INNER JOIN cross_hire_orders_items as choi ON choi.pk_id = cchi.fk_cross_hire_order_item_id
                    INNER JOIN sales_stock as ss ON ss.pk_id = choi.fk_item_id
                WHERE cchi.fk_contract_id = {$contractID}";
        $query = $this->company_db->query($query);
        return !empty($query->result()) ? $query->result() : array();
    }
    
    function selectSalesItems( $contractID ) {
        
        $query = "SELECT 
                    ci.pk_id,
                    stock_number, 
                    item_no, 
                    qty, 
                    ci.description, 
                    rate, 
                    discount_perc, 
                    value
                FROM  
                    contract_items as ci
                    INNER JOIN sales_stock as ss ON ss.pk_id = item_no
                WHERE 
                    fk_contract_id = {$contractID}
                    AND item_type = 1
                    AND parent_item is null";
        $query = $this->company_db->query($query);
        return !empty($query->result()) ? $query->result() : array();
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
	
	function set_contract_active( $param_arr )
	{
		
		$query = 'update contracts set fk_contract_status_id = 3 where pk_id = '.$param_arr['contractID'];
        $this->company_db->trans_start();
        $this->company_db->query($query);
        
		$query = 'INSERT INTO contracts_history(fk_contract_id, date_time, fk_change_to)
                  VALUES('.$param_arr['contractID'].', \''.$param_arr['datetime'].'\' , 3)';
		$this->company_db->query($query);
        
        return $this->company_db->trans_complete();
	}
	
	
}