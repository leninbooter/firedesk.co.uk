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
    
    function getAccounts() {
        
        $q = 'SELECT * FROM acc_coa';
        return $this->company_db->query($q)->result();
    }
}