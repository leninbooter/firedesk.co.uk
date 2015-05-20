<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Returns_m extends CI_Model
{
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
    
    function insReturnNote ( $datetime, $contractID, $notes ) {        
        
        $q = 'INSERT INTO returns(datetime, fk_contract_id, notes) VALUES(\''.$datetime.'\', '.$contractID.', \''.$this->company_db->escape_str($notes).'\')';
        $this->company_db->query($q);
        
        $q = 'SELECT last_insert_id() as returnID';
        
        return $this->company_db->query($q)->row()->returnID;
    }
    
    function saveHiredReturn( $param_arr ) {
        
        $this->company_db->trans_start();
        
        $returnID = $this->insReturnNote($param_arr['datetime'], $param_arr['contractID'], $param_arr['notes']);
        
        foreach($param_arr['items'] as $i) {           
            
            $q = 'INSERT INTO returns_items(fk_return_id, fk_collections_item_id, qty) 
                        VALUES('.$returnID.', '.$i['collectItemId'].', '.$i['returnedQty'].')';
            $this->company_db->query($q);            
            
            $q = 'UPDATE collections_items 
                            SET qty_returned = ifnull(qty_returned,0) + '.$i['returnedQty'].' 
                            WHERE pk_id = '.$i['collectItemId'];
            $this->company_db->query($q);
            
            // Return to the stock if it was a sale
            $q = 'SELECT item_no, cost, '.$i['returnedQty'].' as qty , item_type
                    FROM contract_items
                    WHERE pk_id = (SELECT fk_contract_item_id FROM collections_items 
                                    WHERE pk_id = '.$i['collectItemId'].')';
            $contractItemRow = $this->company_db->query($q)->row();
            
            if ( $contractItemRow->item_type == 1 ) {
                
                $stock_cost_total = $contractItemRow->qty * $contractItemRow->cost;
                
                $q = 'UPDATE sales_stock 
                        SET quantity_balance = quantity_balance + '.$contractItemRow->qty .'
                        WHERE pk_id = '.$contractItemRow->item_no;
                $this->company_db->query($q);
                
                 $q = 'update sales_stock 
                        set 
                            summed_cost = ifnull(summed_cost, 0) + '. $this->company_db->escape_str($contractItemRow->cost).',
                            movements_count = ifnull(movements_count, 0) + 1
                        where pk_id = '.$contractItemRow->item_no;
                $this->company_db->query($q);
                
                $q = 'update sales_stock 
                        set stock_cost_average = summed_cost/movements_count, 
                            stock_cost_total = stock_cost_total + '.$stock_cost_total.', 
                            sales_cost = '. $this->company_db->escape_str($contractItemRow->cost).'
                        where pk_id = '.$contractItemRow->item_no;
                $this->company_db->query($q);
            }
            
            
        }
            
         $this->company_db->trans_complete();
        return $this->company_db->trans_status();
        
    }
    
    /*function saveSoldReturn( $param_arr ) {
        
        $this->company_db->trans_start();
        
        $returnID = $this->insReturnNote($param_arr['datetime'], $param_arr['contractID'], $param_arr['notes']);
        
        foreach($param_arr['items'] as $i) {
            
            $q = 'INSERT INTO returns_items(fk_return_id, fk_collections_item_id, qty) 
                        VALUES('.$returnID.', '.$i['collectItemId'].', '.$i['returnedQty'].')';
            $this->company_db->query($q);            
            
            $q = 'UPDATE collections_items 
                            SET qty_returned = ifnull(qty_returned,0) + '.$i['returnedQty'].' 
                            WHERE pk_id = '.$i['collectItemId'];
            
            $this->company_db->query($q);
        }
            
        return $this->company_db->trans_complete();
    }*/
}
