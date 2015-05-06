<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sessions_m extends CI_Model
{		
	var $fd_db;

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('models');
		$this->fd_db = $this->load->database('default', true);
	}
	
	function sel_user_data($username)
	{
		$query = "select 
				cr.pk_id, 
				cr.username,
				cr.password,
				p.pk_id as profile_id,
				p.name as profile_name,
				co.pk_id as company_id,
				co.name as company_name,
				co.db,
				co.db_user,
				co.db_pwd,
				co.db_host,
                co.logo
				from credentials as cr
				inner join profiles as p on p.pk_id = cr.fk_profile_id
				left join companies as co on co.pk_id = cr.fk_company_id
				where cr.username =  '$username'";
				log_message('debug', $query);
		$query =$this->fd_db->query($query);
		return !empty($query->result()) ? $query->row() : array();
	}
}