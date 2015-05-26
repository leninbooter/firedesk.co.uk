<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_m extends CI_Model
{		
	var $company_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		
		$this->company_db = $this->load->database(company_db_string_connection(), true);
	}
	
	function sel_user_data($global_user_id)
	{
		$query = "select pk_id, name, fk_profile_id, email from users where fk_global_user_id = $global_user_id";
        log_message('debug', $query);
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->row() : array();
	}
	
	function select_users_all()
	{
		$query = "select pk_id, name as label from users
					union all
					select '-1' as pk_id, 'Everybody' as label;";
		$query =  $this->company_db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
}