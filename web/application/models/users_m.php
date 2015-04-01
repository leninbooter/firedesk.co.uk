<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_m extends CI_Model
{		
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->helper('models');
    }
	
	public function select_users_all()
	{
		$this->load->database();
		
		$query = "select pk_id, name as label from users
					union all
					select '-1' as pk_id, 'Everybody' as label;";
		$query = $this->db->query($query);
		return !empty($query->result()) ? $query->result() : array();
	}
	
}