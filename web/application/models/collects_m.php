<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Collects_m extends CI_Model
{
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
    
    function saveCollect( $param_arr ) {
        
        $this->company_db->trans_start();
        
        $query = 'INSERT INTO collections(datetime, fk_contract_id, notes) VALUES(\''.$param_arr['datetime'].'\', '.$param_arr['contractID'].', \''.$this->company_db->escape_str($param_arr['notes']).'\')';
        $this->company_db->query($query);
        
        $query = 'SELECT last_insert_id() as collectID';
        $collectID = $this->company_db->query($query)->row()->collectID;
        
        foreach($param_arr['items'] as $i) {
            
            $query = 'INSERT INTO collections_items(fk_collection_id, fk_contract_item_id, qty) VALUES('.$collectID.', '.$i['contractItemID'].', '.$i['collectQty'].')';
            $this->company_db->query($query);
            
            $query = 'UPDATE contract_items SET qty_supplied = ifnull(qty_supplied,0) + '.$i['collectQty'].' WHERE pk_id = '.$i['contractItemID'];
            $this->company_db->query($query);
        }
            
        return $this->company_db->trans_complete();
        
    }
    
    
    function selectCollectDetails ( $collectID ) {
        
        $query = 'SELECT pk_id, datetime, fk_contract_id, notes FROM collections WHERE pk_id = '.$collectID;
        return $this->company_db->query($query)->row();
    }
    
    function selectNotesItems ( $collectID ) {
        
        $query = 'SELECT coli.pk_id, coli.fk_contract_item_id, coli.notes, coli.qty,
                        ci.description
                    FROM collections_items as coli
                        INNER JOIN contract_items as ci ON ci.pk_id = coli.fk_contract_item_id
                    WHERE fk_collection_id = '.$collectID;
        return $this->company_db->query($query)->result();
    }
    
    function selectPastNotes ( $contractID ) {
        
        $query = 'SELECT pk_id, datetime, notes FROM collections WHERE fk_contract_id = '.$contractID;
        return $this->company_db->query($query)->result();
    }
}
