<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Branches_m extends CI_Model
{
	var $fd_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->fd_db = $this->load->database('default', true);	
	}
    
    function retrieveBranchDetails ( $branchID ) {
        
        $q = 'SELECT 
                db,
                db_user,
                db_pwd,
                db_host
            FROM branches 
            WHERE pk_id = '.$branchID;
        
        return $this->fd_db->query( $q )->row();
    }
    
    function retrieveBranches( $companyID ) {
        
        $q = 'SELECT 
                pk_id, 
                branch_name
            FROM branches
            WHERE
                fk_company_id = '.$companyID;
                
        return $this->fd_db->query($q)->result();
    }
}