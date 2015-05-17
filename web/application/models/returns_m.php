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
    
    function saveHiredReturn( $param_arr ) {
        
        $this->company_db->trans_start();
        
        $q = 'INSERT INTO returns(datetime, fk_contract_id, notes) VALUES(\''.$param_arr['datetime'].'\', '.$param_arr['contractID'].', \''.$this->company_db->escape_str($param_arr['notes']).'\')';
        $this->company_db->query($q);
        
        $q = 'SELECT last_insert_id() as returnID';
        $returnID = $this->company_db->query($q)->row()->returnID;
        
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
        
    }
}
