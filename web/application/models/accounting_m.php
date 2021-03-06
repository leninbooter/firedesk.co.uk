<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounting_m extends CI_Model
{
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');

		$this->company_db = $this->load->database(company_db_string_connection(), true);        
	}
    
    function accountHasChildren( $accountCode ) {
        
        $q = 'SELECT count(1) count FROM acc_coa WHERE code like \''.$accountCode.'%\' LIMIT 2';
        return $this->company_db->query($q)->row()->count > 1 ? true : false;
    }
    
    function canBeRelated( $accountCode ) {
        
        $q = 'SELECT 1 as parent FROM acc_coa WHERE code = \''.$accountCode.'\'';
        return isset($this->company_db->query($q)->row()->parent) &&
               $this->company_db->query($q)->row()->parent == "1" ? true : false;
    }
    
    function delAccount( $accountCode ) {
        
        $q = 'DELETE FROM acc_coa WHERE code = \''.$accountCode.'\'';
        return $this->company_db->simple_query($q);
    }
    
    function getAccounts() {
        
        $q = 'SELECT * FROM acc_coa';
        return $this->company_db->query($q)->result();
    }
    
    function getBankAccounts() {
        
        $q = 'SELECT code, name 
                FROM acc_coa
                INNER JOIN acc_default_accounts as ada ON ada.fk_account_id = code 
                    AND pk_id BETWEEN 16 AND 24';
        return $this->company_db->query($q)->result();
    }
    
    function getBankAccountDtls( $accCode ) {
    
        $q = 'SELECT
                pk_id,
                acb.name,
                cleared,
                held,
                pending,
                balance,
                today,
                ac.name as bankAccountName
                FROM acc_cash_books as acb
                    INNER JOIN acc_coa as ac ON ac.code = fk_bank_account
                WHERE fk_bank_account = \''.$accCode.'\'';
        $r = $this->company_db->query($q)->row();        
        
        if( empty($r) ) {
            
            if ( $this->insCashBook($accCode) ) {
                
                return $this->company_db->query($q)->row();        
            }else {
                
                return false;
            }
        }else {
            
            return $r;
        }
        
    }
    
    function getAccMvmnts( $cashBook ) {
        
        $q = 'SELECT *
                FROM acc_cash_book_details
                WHERE fk_cash_book = '.$cashBook;
        return $this->company_db->query($q)->result();
    }
    
    function getDefaultAccounts() {
        
        $q = 'SELECT * FROM acc_default_accounts';
        return $this->company_db->query($q)->result();
    }
    
    
    function insAccount( $codeAccount, $accountName ) {
        
        $q = 'SELECT \'yes\' as found FROM acc_coa WHERE code = \''. $codeAccount .'\' OR name = \'' . $accountName . '\'';
        if ( !empty($this->company_db->query($q)->row()) && $this->company_db->query($q)->row()->found == 'yes' ){
            
            // The code or the name of the account exist in the chart. Return 1
            return 1;
        }
        
        $q = 'INSERT INTO acc_coa ( code, name) VALUES ( \'' . $codeAccount . '\', \'' . $accountName .'\')';
        return $this->company_db->query($q);
    }
    
    function insCashBook( $accCode ) {
        
        $q = 'INSERT INTO acc_cash_books(fk_bank_account, cleared, held, pending, balance, today)
                VALUES('.$accCode.', 0,0,0,0,0)';
        return $this->company_db->query($q);
    }
    
    function updDefAccount ( $defAccountID, $accountCode ) {
        
        $q = 'UPDATE acc_default_accounts SET fk_account_id = '.$accountCode.'  WHERE pk_id = '.$defAccountID;
        return $this->company_db->simple_query($q);
    }
}